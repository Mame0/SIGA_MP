<?php
/**
 * API Backend del Chatbot
 * Recibe mensajes del frontend y devuelve respuestas de la IA
 */

// Cargar configuración PRIMERO (antes de usar cualquier constante)
require_once 'config.php';
require_once CHATBOT_DB_CLASS_PATH;
require_once 'ChatbotAI.php';

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', CHATBOT_DEBUG_MODE ? 1 : 0);

// Headers CORS
if (CHATBOT_ALLOW_CORS) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
}

header('Content-Type: application/json; charset=utf-8');

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Función para responder JSON
function responderJSON($data, $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Leer el cuerpo de la solicitud
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        responderJSON(['error' => 'Datos inválidos'], 400);
    }
    
    // Validar mensaje
    $mensaje = isset($data['mensaje']) ? trim($data['mensaje']) : '';
    
    if (empty($mensaje)) {
        responderJSON(['error' => 'El mensaje no puede estar vacío'], 400);
    }
    
    if (strlen($mensaje) > CHATBOT_MAX_LENGTH) {
        responderJSON(['error' => 'El mensaje es demasiado largo'], 400);
    }
    
    // Obtener o crear sesión
    session_start();
    if (!isset($_SESSION['chatbot_sesion_id'])) {
        $_SESSION['chatbot_sesion_id'] = uniqid('chat_', true);
    }
    $sesionId = $_SESSION['chatbot_sesion_id'];
    
    // Rate limiting simple (opcional)
    if (!isset($_SESSION['chatbot_contador'])) {
        $_SESSION['chatbot_contador'] = 0;
        $_SESSION['chatbot_hora_inicio'] = time();
    }
    
    // Resetear contador cada hora
    if (time() - $_SESSION['chatbot_hora_inicio'] > 3600) {
        $_SESSION['chatbot_contador'] = 0;
        $_SESSION['chatbot_hora_inicio'] = time();
    }
    
    $_SESSION['chatbot_contador']++;
    
    if ($_SESSION['chatbot_contador'] > CHATBOT_RATE_LIMIT) {
        responderJSON([
            'error' => 'Has excedido el límite de mensajes por hora. Por favor, intenta más tarde.'
        ], 429);
    }
    
    // Conectar a la base de datos
    $db = new Db();
    
    // Crear instancia del chatbot
    $chatbot = new ChatbotAI($db);
    
    // Obtener historial de conversación
    $historial = $chatbot->obtenerHistorial($sesionId, 5); // Últimos 5 mensajes
    
    // Obtener respuesta de la IA
    $tiempoInicio = microtime(true);
    $resultado = $chatbot->obtenerRespuesta($mensaje, $historial);
    $tiempoRespuesta = round((microtime(true) - $tiempoInicio) * 1000); // En milisegundos
    
    if (!$resultado['exito']) {
        // Si TODOS los proveedores fallaron, dar una respuesta de respaldo
        $respuestaRespaldo = "Lo siento, estoy experimentando dificultades técnicas en este momento. " .
                            "Por favor, intenta nuevamente en unos momentos o contacta directamente con nuestra oficina.";

        responderJSON([
            'respuesta'        => $respuestaRespaldo,
            'tiempo_respuesta' => $tiempoRespuesta,
            'modo_respaldo'    => true,
            'error_tecnico'    => CHATBOT_DEBUG_MODE ? $resultado['error'] : null,
            'intentos_fallback' => CHATBOT_DEBUG_MODE ? $chatbot->getIntentosFallback() : null
        ]);
    }
    
    $respuestaBot = $resultado['respuesta'];
    
    // Guardar en la base de datos
    $ipUsuario = $_SERVER['REMOTE_ADDR'] ?? null;
    $chatbot->guardarConversacion($sesionId, $mensaje, $respuestaBot, $ipUsuario);
    
    // Responder al frontend
    responderJSON([
        'respuesta'       => $respuestaBot,
        'tiempo_respuesta' => $tiempoRespuesta,
        'sesion_id'       => CHATBOT_DEBUG_MODE ? $sesionId : null,
        'proveedor'       => CHATBOT_DEBUG_MODE ? $chatbot->getProveedorUsado() : null,
        'fallback_activo' => CHATBOT_DEBUG_MODE ? (count($chatbot->getIntentosFallback()) > 0) : null,
        'intentos_fallback' => CHATBOT_DEBUG_MODE ? $chatbot->getIntentosFallback() : null
    ]);
    
} catch (Exception $e) {
    if (CHATBOT_DEBUG_MODE) {
        responderJSON([
            'error' => 'Error del servidor',
            'detalle' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    } else {
        responderJSON([
            'error' => 'Error del servidor. Por favor, intenta nuevamente.'
        ], 500);
    }
}
