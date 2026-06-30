<?php
/**
 * Test para listar modelos disponibles de Gemini
 */

require_once 'config.php';

echo "<h1>Listando Modelos Disponibles de Gemini</h1>";
echo "<style>body{font-family:sans-serif;padding:20px;} pre{background:#f5f5f5;padding:10px;overflow:auto;}</style>";

$url = 'https://generativelanguage.googleapis.com/v1/models?key=' . GEMINI_API_KEY;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    
    echo "<h2>Modelos que soportan generateContent:</h2>";
    echo "<ul>";
    
    if (isset($data['models'])) {
        foreach ($data['models'] as $model) {
            $modelName = $model['name'];
            $supportedMethods = isset($model['supportedGenerationMethods']) ? $model['supportedGenerationMethods'] : [];
            
            if (in_array('generateContent', $supportedMethods)) {
                // Extraer solo el nombre del modelo (sin el prefijo "models/")
                $shortName = str_replace('models/', '', $modelName);
                echo "<li><strong>" . $shortName . "</strong></li>";
            }
        }
    }
    echo "</ul>";
    
    echo "<h2>Respuesta completa:</h2>";
    echo "<pre>" . htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT)) . "</pre>";
} else {
    echo "<p style='color:red;'>Error HTTP: " . $httpCode . "</p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
}
