<?php
require_once 'classes/Db.class.php';

header('Content-Type: application/json; charset=utf-8');

$Db = new Db();

// Se obtiene el término de búsqueda enviado por Select2 (parámetro 'q').
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

$results = [];

// Solo se ejecuta la búsqueda si el término no está vacío.
if ($searchTerm !== '') {
    
    // La consulta SQL usa alias 'id' y 'text' para ser compatible directamente con Select2.
    $sql = "SELECT n_codigo as id, x_nombre as text 
            FROM mp_maes_grado_especialidades 
            WHERE x_nombre LIKE ? AND n_estado = 1 
            ORDER BY x_nombre 
            LIMIT 30";
    
    // Se prepara el array de parámetros para la consulta.
    $params = ['%' . $searchTerm . '%'];
    
    // Se utiliza el método 'query' de tu clase Db, que es la forma correcta.
    $rows = $Db->query($sql, $params);
    
    // Si la consulta devuelve filas, se asignan directamente al resultado.
    // El formato (id, text) ya es el que Select2 necesita.
    if ($rows) {
        $results = $rows;
    }
}

// Se imprime el resultado en formato JSON, que será leído por el script de Select2.
echo json_encode($results);
?>