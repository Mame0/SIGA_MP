<?php
/**
 * Diagnóstico de errores de la API de Gemini
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';
require_once CHATBOT_DB_CLASS_PATH;
require_once 'ChatbotAI.php';

echo "<h1>Diagnóstico de Errores de Gemini</h1>";
echo "<style>body{font-family:sans-serif;padding:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} pre{background:#f5f5f5;padding:10px;overflow:auto;}</style>";

try {
    $db = new Db();
    $chatbot = new ChatbotAI($db);
    
    echo "<h2>1. Verificando API Key</h2>";
    echo "<p>API Key: " . substr(GEMINI_API_KEY, 0, 10) . "..." . substr(GEMINI_API_KEY, -5) . "</p>";
    echo "<p>Modelo: " . GEMINI_MODEL . "</p>";
    
    echo "<h2>2. Probando consulta simple (sin directorio)</h2>";
    $respuesta1 = $chatbot->obtenerRespuesta('Hola', []);
    
    if ($respuesta1['exito']) {
        echo "<p class='success'>✓ Consulta simple funciona</p>";
        echo "<pre>" . htmlspecialchars($respuesta1['respuesta']) . "</pre>";
    } else {
        echo "<p class='error'>✗ Error en consulta simple</p>";
        echo "<pre>" . htmlspecialchars($respuesta1['error']) . "</pre>";
    }
    
    echo "<h2>3. Probando consulta que activa el directorio</h2>";
    $respuesta2 = $chatbot->obtenerRespuesta('¿Cuál es el correo de la Presidencia?', []);
    
    if ($respuesta2['exito']) {
        echo "<p class='success'>✓ Consulta con directorio funciona</p>";
        echo "<pre>" . htmlspecialchars($respuesta2['respuesta']) . "</pre>";
    } else {
        echo "<p class='error'>✗ Error en consulta con directorio</p>";
        echo "<pre>" . htmlspecialchars($respuesta2['error']) . "</pre>";
    }
    
    echo "<h2>4. Verificando tamaño del prompt</h2>";
    $infoDirectorio = $chatbot->buscarEnDirectorio('presidencia');
    $promptCompleto = CHATBOT_PROMPT_SISTEMA . "\n\n" . $infoDirectorio . "\n\nUsuario: ¿Cuál es el correo de la Presidencia?\nAsistente:";
    
    $caracteres = strlen($promptCompleto);
    $tokens_aprox = $caracteres / 4; // Aproximación
    
    echo "<p>Caracteres totales: <strong>$caracteres</strong></p>";
    echo "<p>Tokens aproximados: <strong>" . round($tokens_aprox) . "</strong></p>";
    
    if ($tokens_aprox > 30000) {
        echo "<p class='error'>⚠️ El prompt es MUY LARGO. Gemini puede rechazarlo.</p>";
        echo "<p>Solución: Reduce el CHATBOT_PROMPT_SISTEMA en config.php</p>";
    } elseif ($tokens_aprox > 20000) {
        echo "<p class='warning'>⚠️ El prompt es largo. Puede causar problemas ocasionales.</p>";
    } else {
        echo "<p class='success'>✓ El tamaño del prompt es aceptable</p>";
    }
    
    echo "<h2>5. Verificando límites de API</h2>";
    echo "<p>Gemini Free Tier límites:</p>";
    echo "<ul>";
    echo "<li>15 solicitudes por minuto</li>";
    echo "<li>1,500 solicitudes por día</li>";
    echo "<li>1 millón de tokens por minuto</li>";
    echo "</ul>";
    
    echo "<p class='warning'>Si estás probando mucho el chatbot, puedes haber alcanzado el límite de 15 solicitudes/minuto.</p>";
    echo "<p><strong>Solución:</strong> Espera 1 minuto y vuelve a probar.</p>";
    
    echo "<h2>6. Muestra del prompt completo</h2>";
    echo "<p>Primeros 1000 caracteres del prompt que se envía a Gemini:</p>";
    echo "<pre>" . htmlspecialchars(substr($promptCompleto, 0, 1000)) . "...</pre>";
    
    echo "<h2>7. Recomendaciones</h2>";
    echo "<ul>";
    echo "<li>Si el error es 'Error HTTP 429': Has excedido el límite de solicitudes. Espera 1 minuto.</li>";
    echo "<li>Si el error es 'Error HTTP 400': El prompt es inválido o demasiado largo.</li>";
    echo "<li>Si el error es 'Error HTTP 500': Error temporal de Gemini. Intenta de nuevo.</li>";
    echo "<li>Si no hay error pero dice 'dificultades técnicas': Gemini bloqueó el contenido por seguridad.</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><a href='index.php'>Volver al Chatbot</a> | <a href='test_busqueda_bd.php'>Test de Búsqueda</a></p>";
