<?php
/**
 * Test de diagnóstico del Chatbot
 * Este archivo verifica cada componente del sistema
 */

echo "<h1>Diagnóstico del Chatbot</h1>";
echo "<style>body{font-family:sans-serif;padding:20px;} .ok{color:green;} .error{color:red;} pre{background:#f5f5f5;padding:10px;}</style>";

// Test 1: Verificar que config.php se carga
echo "<h2>1. Verificando configuración...</h2>";
try {
    require_once 'config.php';
    echo "<p class='ok'>✓ Archivo config.php cargado correctamente</p>";
    echo "<p>API Key Gemini: " . substr(GEMINI_API_KEY, 0, 20) . "...</p>";
    echo "<p>Proveedor activo: " . PROVEEDOR_IA_ACTIVO . "</p>";
    echo "<p>Debug mode: " . (CHATBOT_DEBUG_MODE ? 'Activado' : 'Desactivado') . "</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ Error al cargar config.php: " . $e->getMessage() . "</p>";
    exit;
}

// Test 2: Verificar conexión a la base de datos
echo "<h2>2. Verificando conexión a base de datos...</h2>";
try {
    require_once CHATBOT_DB_CLASS_PATH;
    $db = new Db();
    echo "<p class='ok'>✓ Conexión a base de datos exitosa</p>";
    
    // Verificar que las tablas existen
    $result = $db->query("SHOW TABLES LIKE 'mp_chatbot_historial'");
    if (count($result) > 0) {
        echo "<p class='ok'>✓ Tabla mp_chatbot_historial existe</p>";
    } else {
        echo "<p class='error'>✗ Tabla mp_chatbot_historial NO existe. Ejecuta setup.sql</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error de base de datos: " . $e->getMessage() . "</p>";
}

// Test 3: Verificar que cURL está habilitado
echo "<h2>3. Verificando extensión cURL...</h2>";
if (function_exists('curl_version')) {
    $curl_info = curl_version();
    echo "<p class='ok'>✓ cURL está habilitado</p>";
    echo "<p>Versión: " . $curl_info['version'] . "</p>";
} else {
    echo "<p class='error'>✗ cURL NO está habilitado. Necesitas habilitarlo en php.ini</p>";
}

// Test 4: Probar conexión con Gemini API
echo "<h2>4. Probando conexión con Gemini API...</h2>";

$testPrompt = "Hola, responde solo con 'OK'";
$payload = [
    'contents' => [
        [
            'parts' => [
                ['text' => $testPrompt]
            ]
        ]
    ]
];

$url = GEMINI_API_URL . '?key=' . GEMINI_API_KEY;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "<p>Código HTTP: " . $httpCode . "</p>";

if ($curlError) {
    echo "<p class='error'>✗ Error de cURL: " . $curlError . "</p>";
} elseif ($httpCode === 200) {
    echo "<p class='ok'>✓ Conexión exitosa con Gemini API</p>";
    $data = json_decode($response, true);
    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
        echo "<p class='ok'>✓ Respuesta recibida: " . $data['candidates'][0]['content']['parts'][0]['text'] . "</p>";
    } else {
        echo "<p class='error'>✗ Respuesta inesperada de Gemini</p>";
        echo "<pre>" . htmlspecialchars(print_r($data, true)) . "</pre>";
    }
} else {
    echo "<p class='error'>✗ Error HTTP " . $httpCode . "</p>";
    echo "<p>Respuesta del servidor:</p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    // Verificar si es problema de API Key
    if ($httpCode === 400) {
        $errorData = json_decode($response, true);
        if (isset($errorData['error']['message'])) {
            echo "<p class='error'><strong>Mensaje de error:</strong> " . $errorData['error']['message'] . "</p>";
            if (strpos($errorData['error']['message'], 'API key') !== false) {
                echo "<p class='error'><strong>PROBLEMA DETECTADO:</strong> La API Key no es válida. Verifica que:</p>";
                echo "<ul>";
                echo "<li>La API Key empiece con 'AIza' (no 'TAIza')</li>";
                echo "<li>La hayas copiado completa desde Google AI Studio</li>";
                echo "<li>No tenga espacios al inicio o final</li>";
                echo "</ul>";
            }
        }
    }
}

// Test 5: Verificar que api.php es accesible
echo "<h2>5. Verificando api.php...</h2>";
if (file_exists('api.php')) {
    echo "<p class='ok'>✓ Archivo api.php existe</p>";
} else {
    echo "<p class='error'>✗ Archivo api.php NO existe</p>";
}

echo "<hr>";
echo "<h2>Resumen</h2>";
echo "<p>Si todos los tests anteriores pasaron, el chatbot debería funcionar.</p>";
echo "<p>Si hay errores, corrígelos y vuelve a ejecutar este test.</p>";
echo "<p><a href='index.php'>Ir al Chatbot</a> | <a href='test_diagnostico.php'>Recargar Test</a></p>";
