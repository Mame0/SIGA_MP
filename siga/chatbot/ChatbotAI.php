<?php
/**
 * Clase ChatbotAI
 * Maneja la comunicación con múltiples proveedores de IA con fallback automático.
 *
 * Cadena de fallback:
 *   1. Gemini (principal)
 *   2. Groq  - Llama 3.3 70B   (~1,000 req/día  | Velocidad)
 *   3. Cerebras - Llama 3.1 8B (1M tokens/día   | Alto volumen)
 *   4. Mistral Small            (4M tokens/mes   | Calidad)
 *
 * Si un proveedor devuelve HTTP 429 (cuota), 401 (sin auth) o 503,
 * el sistema salta automáticamente al siguiente proveedor.
 */

class ChatbotAI {

    /** @var object Conexión a la base de datos */
    private $db;

    /** @var string Proveedor configurado por defecto */
    private $proveedor;

    /** @var string Proveedor que realmente respondió (puede ser diferente por fallback) */
    private $proveedorUsado = '';

    /** @var array Registro de intentos de fallback (para debug/log) */
    private $intentosFallback = [];

    public function __construct($db, $proveedor = PROVEEDOR_IA_ACTIVO) {
        $this->db       = $db;
        $this->proveedor = $proveedor;
    }

    /**
     * Devuelve el proveedor que efectivamente respondió
     */
    public function getProveedorUsado() {
        return $this->proveedorUsado ?: $this->proveedor;
    }

    /**
     * Devuelve el log de intentos de fallback (útil para debug)
     */
    public function getIntentosFallback() {
        return $this->intentosFallback;
    }

    // =========================================================
    //  MÉTODO PRINCIPAL — con lógica de fallback automático
    // =========================================================

    /**
     * Envía un mensaje a la IA y obtiene la respuesta.
     * Si el proveedor principal falla por cuota (429/401/503),
     * intenta automáticamente el siguiente en la cadena.
     *
     * @param string $mensajeUsuario
     * @param array  $historial
     * @return array ['exito' => bool, 'respuesta' => string, 'error' => string]
     */
    public function obtenerRespuesta($mensajeUsuario, $historial = []) {
        // 1. Buscar en el directorio de despachos primero
        $infoDirectorio = $this->buscarEnDirectorio($mensajeUsuario);

        // 2. Definir la cadena de proveedores a intentar
        $cadena = $this->construirCadenaFallback();

        // 3. Iterar la cadena hasta obtener una respuesta exitosa
        $ultimoError = '';
        foreach ($cadena as $prov) {
            $resultado = $this->consultarProveedor($prov, $mensajeUsuario, $historial, $infoDirectorio);

            if ($resultado['exito']) {
                $this->proveedorUsado = $prov;

                // Registrar en log si hubo fallback activo
                if ($prov !== $cadena[0]) {
                    error_log("[Chatbot Fallback] Respondió: $prov (fallback activo)");
                }
                return $resultado;
            }

            // Guardar intento fallido
            $httpCode = $resultado['httpCode'] ?? 0;
            $this->intentosFallback[] = [
                'proveedor' => $prov,
                'httpCode'  => $httpCode,
                'error'     => $resultado['error']
            ];
            $ultimoError = $resultado['error'];

            // Solo continuar fallback si el error es por cuota / auth / servicio caído
            $codigosFallback = defined('CHATBOT_FALLBACK_CODIGOS')
                ? CHATBOT_FALLBACK_CODIGOS
                : [429, 401, 503];

            if (!in_array($httpCode, $codigosFallback)) {
                // Error diferente (400, 500, etc.) — no tiene sentido intentar otro proveedor
                if (CHATBOT_DEBUG_MODE) {
                    error_log("[Chatbot] $prov falló con HTTP $httpCode (sin fallback): " . $resultado['error']);
                }
                break;
            }

            if (CHATBOT_DEBUG_MODE) {
                error_log("[Chatbot Fallback] $prov falló con HTTP $httpCode, intentando siguiente proveedor...");
            }
        }

        // Todos los proveedores fallaron
        return [
            'exito'     => false,
            'respuesta' => '',
            'error'     => 'Todos los proveedores de IA fallaron. Último error: ' . $ultimoError,
            'httpCode'  => 0
        ];
    }

    // =========================================================
    //  CONSTRUCCIÓN DE LA CADENA DE FALLBACK
    // =========================================================

    /**
     * Devuelve la lista ordenada de proveedores a intentar.
     * Siempre empieza con el proveedor configurado como activo.
     */
    private function construirCadenaFallback() {
        // Cadena completa de fallback en orden
        $todosCadena = ['gemini', 'groq', 'cerebras', 'mistral'];

        if (!defined('CHATBOT_FALLBACK_HABILITADO') || !CHATBOT_FALLBACK_HABILITADO) {
            // Fallback desactivado: solo intentar el proveedor activo
            return [$this->proveedor];
        }

        // Reordenar: el proveedor activo va primero, los demás en orden
        $cadena = [$this->proveedor];
        foreach ($todosCadena as $p) {
            if ($p !== $this->proveedor) {
                $cadena[] = $p;
            }
        }

        return $cadena;
    }

    // =========================================================
    //  DISPATCHER DE PROVEEDORES
    // =========================================================

    /**
     * Enruta la consulta al método correcto según el proveedor.
     */
    private function consultarProveedor($proveedor, $mensajeUsuario, $historial, $infoDirectorio) {
        switch ($proveedor) {
            case 'gemini':
                return $this->consultarGemini($mensajeUsuario, $historial, $infoDirectorio);

            case 'groq':
                return $this->consultarProveedorOpenAICompatible(
                    GROQ_API_URL,
                    GROQ_API_KEY,
                    GROQ_MODEL,
                    $mensajeUsuario,
                    $historial,
                    $infoDirectorio,
                    'groq'
                );

            case 'cerebras':
                return $this->consultarProveedorOpenAICompatible(
                    CEREBRAS_API_URL,
                    CEREBRAS_API_KEY,
                    CEREBRAS_MODEL,
                    $mensajeUsuario,
                    $historial,
                    $infoDirectorio,
                    'cerebras'
                );

            case 'mistral':
                return $this->consultarProveedorOpenAICompatible(
                    MISTRAL_API_URL,
                    MISTRAL_API_KEY,
                    MISTRAL_MODEL,
                    $mensajeUsuario,
                    $historial,
                    $infoDirectorio,
                    'mistral'
                );

            case 'openai':
                return $this->consultarOpenAI($mensajeUsuario, $historial, $infoDirectorio);

            default:
                return [
                    'exito'     => false,
                    'respuesta' => '',
                    'error'     => "Proveedor '$proveedor' no reconocido",
                    'httpCode'  => 0
                ];
        }
    }

    // =========================================================
    //  CONSULTA A GOOGLE GEMINI
    // =========================================================

    private function consultarGemini($mensajeUsuario, $historial, $infoDirectorio = '') {
        // Construir el prompt completo
        $promptCompleto = CHATBOT_PROMPT_SISTEMA . "\n\n";

        if (!empty($infoDirectorio)) {
            $promptCompleto .= $infoDirectorio . "\n\n";
        }

        if (!empty($historial)) {
            $promptCompleto .= "Historial de conversación:\n";
            foreach ($historial as $msg) {
                $promptCompleto .= "Usuario: " . $msg['usuario'] . "\n";
                $promptCompleto .= "Asistente: " . $msg['bot'] . "\n";
            }
            $promptCompleto .= "\n";
        }

        $promptCompleto .= "Usuario: " . $mensajeUsuario . "\nAsistente:";

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $promptCompleto]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature'     => 0.7,
                'maxOutputTokens' => 500,
            ]
        ];

        $url = GEMINI_API_URL . '?key=' . GEMINI_API_KEY;
        return $this->enviarSolicitudHTTP($url, $payload, 'gemini');
    }

    // =========================================================
    //  CONSULTA GENÉRICA PARA PROVEEDORES COMPATIBLES CON OPENAI
    //  (Groq, Cerebras, Mistral usan exactamente el mismo formato)
    // =========================================================

    private function consultarProveedorOpenAICompatible(
        $url,
        $apiKey,
        $modelo,
        $mensajeUsuario,
        $historial,
        $infoDirectorio = '',
        $nombreProveedor = 'openai'
    ) {
        // Verificar que la API key no sea un placeholder
        if (empty($apiKey) || strpos($apiKey, 'TU_API_KEY') !== false) {
            return [
                'exito'     => false,
                'respuesta' => '',
                'error'     => "API Key de '$nombreProveedor' no configurada",
                'httpCode'  => 401
            ];
        }

        // Construir mensajes en formato OpenAI
        $promptSistema = CHATBOT_PROMPT_SISTEMA;
        if (!empty($infoDirectorio)) {
            $promptSistema .= "\n\n" . $infoDirectorio;
        }

        $mensajes = [
            ['role' => 'system', 'content' => $promptSistema]
        ];

        // Agregar historial
        if (!empty($historial)) {
            foreach ($historial as $msg) {
                $mensajes[] = ['role' => 'user',      'content' => $msg['usuario']];
                $mensajes[] = ['role' => 'assistant', 'content' => $msg['bot']];
            }
        }

        // Mensaje actual
        $mensajes[] = ['role' => 'user', 'content' => $mensajeUsuario];

        $payload = [
            'model'       => $modelo,
            'messages'    => $mensajes,
            'temperature' => 0.7,
            'max_tokens'  => 500
        ];

        return $this->enviarSolicitudHTTP($url, $payload, 'openai', $apiKey);
    }

    // =========================================================
    //  CONSULTA A OPENAI (mantener por compatibilidad)
    // =========================================================

    private function consultarOpenAI($mensajeUsuario, $historial, $infoDirectorio = '') {
        return $this->consultarProveedorOpenAICompatible(
            OPENAI_API_URL,
            OPENAI_API_KEY,
            OPENAI_MODEL,
            $mensajeUsuario,
            $historial,
            $infoDirectorio,
            'openai'
        );
    }

    // =========================================================
    //  ENVÍO HTTP GENÉRICO
    // =========================================================

    /**
     * Envía solicitud HTTP a la API.
     * Devuelve siempre el httpCode junto con el resultado.
     *
     * @param string      $url
     * @param array       $payload
     * @param string      $tipo     'gemini' | 'openai'
     * @param string|null $apiKey   Para proveedores con Bearer token
     * @return array ['exito', 'respuesta', 'error', 'httpCode']
     */
    private function enviarSolicitudHTTP($url, $payload, $tipo, $apiKey = null) {
        $ch = curl_init($url);

        $headers = ['Content-Type: application/json'];

        if ($tipo === 'openai' && $apiKey !== null) {
            $headers[] = 'Authorization: Bearer ' . $apiKey;
        } elseif ($tipo === 'openai' && defined('OPENAI_API_KEY')) {
            $headers[] = 'Authorization: Bearer ' . OPENAI_API_KEY;
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Para entornos XAMPP locales

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return [
                'exito'     => false,
                'respuesta' => '',
                'error'     => 'Error de conexión cURL: ' . $curlError,
                'httpCode'  => 0
            ];
        }

        if ($httpCode !== 200) {
            $errorMsg = "Error HTTP $httpCode";
            if (CHATBOT_DEBUG_MODE) {
                $errorMsg .= ": " . mb_substr($response, 0, 300);
            }
            return [
                'exito'     => false,
                'respuesta' => '',
                'error'     => $errorMsg,
                'httpCode'  => $httpCode
            ];
        }

        $data = json_decode($response, true);

        // Extraer respuesta según el formato del proveedor
        if ($tipo === 'gemini') {
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                return [
                    'exito'     => true,
                    'respuesta' => trim($data['candidates'][0]['content']['parts'][0]['text']),
                    'error'     => '',
                    'httpCode'  => 200
                ];
            }
        } elseif ($tipo === 'openai') {
            // Groq, Cerebras, Mistral y OpenAI usan este mismo formato
            if (isset($data['choices'][0]['message']['content'])) {
                return [
                    'exito'     => true,
                    'respuesta' => trim($data['choices'][0]['message']['content']),
                    'error'     => '',
                    'httpCode'  => 200
                ];
            }
        }

        // Respuesta 200 pero con formato inesperado
        return [
            'exito'     => false,
            'respuesta' => '',
            'error'     => 'Formato de respuesta inesperado. Respuesta: ' . mb_substr($response, 0, 200),
            'httpCode'  => 200
        ];
    }

    // =========================================================
    //  HISTORIAL Y PERSISTENCIA
    // =========================================================

    /**
     * Guarda la conversación en la base de datos.
     * Registra el proveedor que REALMENTE respondió.
     */
    public function guardarConversacion($sesionId, $mensajeUsuario, $respuestaBot, $ipUsuario = null) {
        try {
            $datos = [
                'sesion_id'      => $sesionId,
                'usuario_mensaje' => $mensajeUsuario,
                'bot_respuesta'  => $respuestaBot,
                'proveedor_ia'   => $this->getProveedorUsado(), // Proveedor real (no el configurado)
                'ip_usuario'     => $ipUsuario
            ];

            return $this->db->insert('mp_chatbot_historial', $datos);
        } catch (Exception $e) {
            if (CHATBOT_DEBUG_MODE) {
                error_log("Error al guardar conversación: " . $e->getMessage());
            }
            return false;
        }
    }

    /**
     * Obtiene el historial de conversación de una sesión
     */
    public function obtenerHistorial($sesionId, $limite = CHATBOT_MAX_HISTORIAL) {
        try {
            $sql = "SELECT usuario_mensaje as usuario, bot_respuesta as bot 
                    FROM mp_chatbot_historial 
                    WHERE sesion_id = :sesion 
                    ORDER BY fecha_creacion DESC 
                    LIMIT :limite";

            $resultado = $this->db->query($sql, [':sesion' => $sesionId, ':limite' => $limite]);

            // Invertir el orden para tener cronológico
            return array_reverse($resultado);
        } catch (Exception $e) {
            if (CHATBOT_DEBUG_MODE) {
                error_log("Error al obtener historial: " . $e->getMessage());
            }
            return [];
        }
    }

    // =========================================================
    //  BÚSQUEDA EN DIRECTORIO DE DESPACHOS
    // =========================================================

    /**
     * Busca información en el directorio de despachos
     * @param string $consulta - Texto de búsqueda del usuario
     * @return string - Información encontrada o vacío
     */
    public function buscarEnDirectorio($consulta) {
        try {
            // Detectar si pregunta por un despacho, correo o teléfono
            $palabrasClave = [
                'despacho', 'fiscalía', 'fiscalia', 'correo', 'email', 'e-mail',
                'teléfono', 'telefono', 'contacto', 'número', 'numero',
                'dirección', 'direccion', 'ubicación', 'ubicacion',
                'cita', 'consulta', 'denuncia',
                'presidencia', 'autoridad', 'mesa', 'partes',
                'superior', 'provincial', 'especializada',
                'familia', 'violencia', 'corrupción', 'anticorrupción',
                'paucarpata', 'mariano melgar', 'jacobo hunter', 'camaná', 'camana',
                'castilla', 'chivay', 'pedregal', 'islay', 'unión', 'union',
                'anexo', 'horario', 'atención', 'atencion',
                // Fiscalías de turno (24h)
                'turno', 'urgencia', 'emergencia', 'guardia', 'nocturno', 'noche',
                'fin de semana', 'feriado', 'domingo', 'sabado', 'sábado',
                'penal', 'prevención', 'prevencion',
                // Tarifas y copias TUPA
                'copia', 'copias', 'tarifa', 'tarifas', 'costo', 'precio', 'pago',
                'constancia', 'constancias', 'tupa', 'reporte', 'certificada',
                'cuánto cuesta', 'cuanto cuesta', 'cuánto vale', 'cuanto vale',
                '2403', '2405', '2407', 'tesorería', 'tesoreria'
            ];

            // Contar cuántas palabras clave contiene
            $coincidencias = 0;
            foreach ($palabrasClave as $palabra) {
                if (stripos($consulta, $palabra) !== false) {
                    $coincidencias++;
                }
            }

            if ($coincidencias === 0) {
                return '';
            }

            // Extraer palabras importantes (eliminar palabras comunes)
            $palabrasComunes = ['el', 'la', 'los', 'las', 'de', 'del', 'un', 'una', 'es', 'cual', 'cuál', 'como', 'cómo', 'donde', 'dónde', 'que', 'qué', 'para', 'por', 'con', 'sin'];
            $palabras = explode(' ', strtolower($consulta));
            $palabrasImportantes = array_filter($palabras, function($palabra) use ($palabrasComunes) {
                return strlen($palabra) > 2 && !in_array($palabra, $palabrasComunes);
            });

            if (empty($palabrasImportantes)) {
                $palabrasImportantes = [$consulta];
            }

            // Construir condiciones OR para cada palabra importante
            $condiciones = [];
            $parametros  = [];
            $i = 0;
            foreach ($palabrasImportantes as $palabra) {
                $param = ':palabra' . $i;
                $condiciones[] = "(nombre LIKE $param OR observaciones LIKE $param OR correo LIKE $param OR tipo LIKE $param)";
                $parametros[$param] = '%' . $palabra . '%';
                $i++;
            }

            $sql = "SELECT nombre, correo, telefono, anexo, horario, observaciones 
                    FROM mp_chatbot_directorio 
                    WHERE activo = 1 
                    AND (" . implode(' OR ', $condiciones) . ")
                    LIMIT 5";

            $resultados = $this->db->query($sql, $parametros);

            if (count($resultados) > 0) {
                $info = "\n\n📋 INFORMACIÓN DEL DIRECTORIO OFICIAL:\n\n";

                foreach ($resultados as $row) {
                    $info .= "• " . $row['nombre'] . "\n";
                    if ($row['correo']) {
                        $info .= "  📧 Correo: " . $row['correo'] . "\n";
                    }
                    if ($row['telefono']) {
                        $info .= "  📞 Teléfono: " . $row['telefono'];
                        if ($row['anexo']) {
                            $info .= " (Anexo: " . $row['anexo'] . ")";
                        }
                        $info .= "\n";
                    }
                    if ($row['horario']) {
                        $info .= "  🕐 Horario: " . $row['horario'] . "\n";
                    }
                    if ($row['observaciones']) {
                        $info .= "  ℹ️ " . $row['observaciones'] . "\n";
                    }
                    $info .= "\n";
                }

                return $info;
            }

            return '';

        } catch (Exception $e) {
            if (CHATBOT_DEBUG_MODE) {
                error_log("Error al buscar en directorio: " . $e->getMessage());
            }
            return '';
        }
    }
}
