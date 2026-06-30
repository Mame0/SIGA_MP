<?php
/**
 * Clase ChatbotAI
 * Maneja la comunicación con diferentes proveedores de IA (Gemini y OpenAI)
 */

class ChatbotAI {
    private $proveedor;
    private $db;
    
    public function __construct($db, $proveedor = PROVEEDOR_IA_ACTIVO) {
        $this->db = $db;
        $this->proveedor = $proveedor;
    }
    
    /**
     * Envía un mensaje a la IA y obtiene la respuesta
     * @param string $mensajeUsuario - Mensaje del usuario
     * @param array $historial - Historial de conversación (opcional)
     * @return array - ['exito' => bool, 'respuesta' => string, 'error' => string]
     */
    public function obtenerRespuesta($mensajeUsuario, $historial = []) {
        // NUEVO: Buscar en el directorio primero
        $infoDirectorio = $this->buscarEnDirectorio($mensajeUsuario);
        
        if ($this->proveedor === 'gemini') {
            return $this->consultarGemini($mensajeUsuario, $historial, $infoDirectorio);
        } elseif ($this->proveedor === 'openai') {
            return $this->consultarOpenAI($mensajeUsuario, $historial, $infoDirectorio);
        } else {
            return [
                'exito' => false,
                'respuesta' => '',
                'error' => 'Proveedor de IA no válido'
            ];
        }
    }
    
    /**
     * Consulta a Google Gemini API
     */
    private function consultarGemini($mensajeUsuario, $historial, $infoDirectorio = '') {
        // Construir el prompt completo
        $promptCompleto = CHATBOT_PROMPT_SISTEMA . "\n\n";
        
        // NUEVO: Agregar información del directorio si existe
        if (!empty($infoDirectorio)) {
            $promptCompleto .= $infoDirectorio . "\n\n";
        }
        
        // Agregar historial si existe
        if (!empty($historial)) {
            $promptCompleto .= "Historial de conversación:\n";
            foreach ($historial as $msg) {
                $promptCompleto .= "Usuario: " . $msg['usuario'] . "\n";
                $promptCompleto .= "Asistente: " . $msg['bot'] . "\n";
            }
            $promptCompleto .= "\n";
        }
        
        $promptCompleto .= "Usuario: " . $mensajeUsuario . "\nAsistente:";
        
        // Preparar el payload para Gemini
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $promptCompleto]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 500,
            ]
        ];
        
        $url = GEMINI_API_URL . '?key=' . GEMINI_API_KEY;
        
        return $this->enviarSolicitudHTTP($url, $payload, 'gemini');
    }
    
    /**
     * Consulta a OpenAI API
     */
    private function consultarOpenAI($mensajeUsuario, $historial, $infoDirectorio = '') {
        // Construir mensajes para OpenAI
        $mensajes = [
            ['role' => 'system', 'content' => CHATBOT_PROMPT_SISTEMA]
        ];
        
        // Agregar historial
        if (!empty($historial)) {
            foreach ($historial as $msg) {
                $mensajes[] = ['role' => 'user', 'content' => $msg['usuario']];
                $mensajes[] = ['role' => 'assistant', 'content' => $msg['bot']];
            }
        }
        
        // Agregar mensaje actual
        $mensajes[] = ['role' => 'user', 'content' => $mensajeUsuario];
        
        // Preparar el payload para OpenAI
        $payload = [
            'model' => OPENAI_MODEL,
            'messages' => $mensajes,
            'temperature' => 0.7,
            'max_tokens' => 500
        ];
        
        return $this->enviarSolicitudHTTP(OPENAI_API_URL, $payload, 'openai');
    }
    
    /**
     * Envía solicitud HTTP a la API
     */
    private function enviarSolicitudHTTP($url, $payload, $tipo) {
        $ch = curl_init($url);
        
        $headers = ['Content-Type: application/json'];
        
        if ($tipo === 'openai') {
            $headers[] = 'Authorization: Bearer ' . OPENAI_API_KEY;
        }
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return [
                'exito' => false,
                'respuesta' => '',
                'error' => 'Error de conexión: ' . $error
            ];
        }
        
        if ($httpCode !== 200) {
            $errorMsg = "Error HTTP $httpCode";
            if (CHATBOT_DEBUG_MODE) {
                $errorMsg .= ": " . $response;
            }
            return [
                'exito' => false,
                'respuesta' => '',
                'error' => $errorMsg
            ];
        }
        
        $data = json_decode($response, true);
        
        // Extraer respuesta según el proveedor
        if ($tipo === 'gemini') {
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                return [
                    'exito' => true,
                    'respuesta' => trim($data['candidates'][0]['content']['parts'][0]['text']),
                    'error' => ''
                ];
            }
        } elseif ($tipo === 'openai') {
            if (isset($data['choices'][0]['message']['content'])) {
                return [
                    'exito' => true,
                    'respuesta' => trim($data['choices'][0]['message']['content']),
                    'error' => ''
                ];
            }
        }
        
        return [
            'exito' => false,
            'respuesta' => '',
            'error' => 'Formato de respuesta inesperado de la IA'
        ];
    }
    
    /**
     * Guarda la conversación en la base de datos
     */
    public function guardarConversacion($sesionId, $mensajeUsuario, $respuestaBot, $ipUsuario = null) {
        try {
            $datos = [
                'sesion_id' => $sesionId,
                'usuario_mensaje' => $mensajeUsuario,
                'bot_respuesta' => $respuestaBot,
                'proveedor_ia' => $this->proveedor,
                'ip_usuario' => $ipUsuario
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
                'anexo', 'horario', 'atención', 'atencion'
            ];
            
            // Contar cuántas palabras clave contiene
            $coincidencias = 0;
            foreach ($palabrasClave as $palabra) {
                if (stripos($consulta, $palabra) !== false) {
                    $coincidencias++;
                }
            }
            
            // Si tiene al menos 1 palabra clave, buscar
            if ($coincidencias === 0) {
                return '';
            }
            
            // Extraer palabras importantes de la consulta (eliminar palabras comunes)
            $palabrasComunes = ['el', 'la', 'los', 'las', 'de', 'del', 'un', 'una', 'es', 'cual', 'cuál', 'como', 'cómo', 'donde', 'dónde', 'que', 'qué', 'para', 'por', 'con', 'sin'];
            $palabras = explode(' ', strtolower($consulta));
            $palabrasImportantes = array_filter($palabras, function($palabra) use ($palabrasComunes) {
                return strlen($palabra) > 2 && !in_array($palabra, $palabrasComunes);
            });
            
            // Si no hay palabras importantes, usar la consulta completa
            if (empty($palabrasImportantes)) {
                $palabrasImportantes = [$consulta];
            }
            
            // Construir condiciones OR para cada palabra importante
            $condiciones = [];
            $parametros = [];
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
    
    /**
     * Obtiene el historial de conversación de una sesión
     */
    public function obtenerHistorial($sesionId, $limite = CHATBOT_MAX_HISTORIAL) {
        try {
            $limite = (int)$limite;
            $sql = "SELECT usuario_mensaje as usuario, bot_respuesta as bot 
                    FROM mp_chatbot_historial 
                    WHERE sesion_id = :sesion 
                    ORDER BY fecha_creacion DESC";
            
            $resultado = $this->db->query($sql, [':sesion' => $sesionId]);
            
            // Limitar en PHP para evitar errores de sintaxis PDO con LIMIT en MariaDB
            if (count($resultado) > $limite) {
                $resultado = array_slice($resultado, 0, $limite);
            }
            
            // Invertir el orden para tener cronológico
            return array_reverse($resultado);
        } catch (Exception $e) {
            if (CHATBOT_DEBUG_MODE) {
                error_log("Error al obtener historial: " . $e->getMessage());
            }
            return [];
        }
    }
}
