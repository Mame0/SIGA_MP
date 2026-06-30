<?php
/**
 * Test directo de api.php
 */

// Simular una petición POST
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['mensaje'] = 'Hola';

// Capturar la salida
ob_start();

// Simular el input JSON
$GLOBALS['HTTP_RAW_POST_DATA'] = json_encode(['mensaje' => 'Hola']);

// Incluir el API
include 'api.php';

$output = ob_get_clean();

echo "<h1>Salida de api.php:</h1>";
echo "<pre>" . htmlspecialchars($output) . "</pre>";
