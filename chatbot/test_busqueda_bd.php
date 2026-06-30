<?php
/**
 * Test de búsqueda en el directorio
 * Verifica si ChatbotAI.php está buscando correctamente en la BD
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';
require_once CHATBOT_DB_CLASS_PATH;
require_once 'ChatbotAI.php';

echo "<h1>Test de Búsqueda en Directorio</h1>";
echo "<style>body{font-family:sans-serif;padding:20px;} .success{color:green;} .error{color:red;} pre{background:#f5f5f5;padding:10px;}</style>";

try {
    $db = new Db();
    $chatbot = new ChatbotAI($db);
    
    echo "<h2>1. Verificando tabla mp_chatbot_directorio</h2>";
    $count = $db->query("SELECT COUNT(*) as total FROM mp_chatbot_directorio");
    echo "<p class='success'>✓ Tabla existe. Total de registros: <strong>" . $count[0]['total'] . "</strong></p>";
    
    if ($count[0]['total'] == 0) {
        echo "<p class='error'>⚠️ La tabla está vacía. Ejecuta directorio_completo.sql en phpMyAdmin</p>";
    }
    
    echo "<h2>2. Probando búsqueda directa en BD</h2>";
    $test = $db->query("SELECT nombre, correo, telefono FROM mp_chatbot_directorio WHERE nombre LIKE '%Presidencia%' LIMIT 1");
    if (count($test) > 0) {
        echo "<p class='success'>✓ Búsqueda SQL funciona</p>";
        echo "<pre>" . print_r($test[0], true) . "</pre>";
    } else {
        echo "<p class='error'>✗ No se encontraron resultados</p>";
    }
    
    echo "<h2>3. Probando método buscarEnDirectorio()</h2>";
    
    $consultas = [
        'presidencia',
        'correo de la presidencia',
        'teléfono',
        'fiscalía',
        'mesa de partes',
        'hola' // Esta NO debe buscar
    ];
    
    foreach ($consultas as $consulta) {
        echo "<h3>Consulta: \"$consulta\"</h3>";
        $resultado = $chatbot->buscarEnDirectorio($consulta);
        
        if (!empty($resultado)) {
            echo "<p class='success'>✓ Encontró información</p>";
            echo "<pre>" . htmlspecialchars($resultado) . "</pre>";
        } else {
            echo "<p>○ No encontró información (puede ser normal si no contiene palabras clave)</p>";
        }
        echo "<hr>";
    }
    
    echo "<h2>4. Probando respuesta completa del chatbot</h2>";
    echo "<p>Pregunta: '¿Cuál es el correo de la Presidencia?'</p>";
    
    $respuesta = $chatbot->obtenerRespuesta('¿Cuál es el correo de la Presidencia?', []);
    
    if ($respuesta['exito']) {
        echo "<p class='success'>✓ Chatbot respondió correctamente</p>";
        echo "<pre>" . htmlspecialchars($respuesta['respuesta']) . "</pre>";
    } else {
        echo "<p class='error'>✗ Error: " . $respuesta['error'] . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><a href='index.php'>Volver al Chatbot</a></p>";
