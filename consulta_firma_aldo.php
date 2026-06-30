<?php
require_once 'db_config.php'; 

define('RECORDS_PER_PAGE', 50); // Registros por página

$results = [];
$errors = [];
$searchTermOccurred = false;


$search_ape_paterno = isset($_GET['ape_paterno']) ? trim($_GET['ape_paterno']) : '';
$search_ape_materno = isset($_GET['ape_materno']) ? trim($_GET['ape_materno']) : '';
$search_nombres = isset($_GET['nombres']) ? trim($_GET['nombres']) : '';
$search_dni = isset($_GET['dni']) ? trim($_GET['dni']) : '';
$search_fecha_inicio = isset($_GET['fecha_firma_inicio']) ? $_GET['fecha_firma_inicio'] : '';
$search_fecha_fin = isset($_GET['fecha_firma_fin']) ? $_GET['fecha_firma_fin'] : '';

$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) {
    $current_page = 1;
}
$total_records = 0;
$total_pages = 0;

$display_columns = [
    'id_registro' => 'ID Reg.',
    'f_programada' => 'F. Programada',
    'f_firma' => 'Fecha Firma',
    'x_nombres' => 'Nombres',
    'x_ape_paterno' => 'Ap. Paterno',
    'x_ape_materno' => 'Ap. Materno',
    'tx_doc_id' => 'DNI',
    'x_observacion' => 'Observación',
    'x_nom_instancia' => 'Instancia/Sede',
    'tx_formato' => 'Formato/Exp.',
    'c_usuario' => 'Usuario Sys.',
    'c_estado' => 'Estado Reg.',
    'c_persona' => 'Cód. Persona',
    'fecha_carga' => 'Fecha Carga Arch.',
    'numero_archivo_carga' => '# Archivo Carga',
];

$sql_select_fields = "id_registro, fecha_carga, numero_archivo_carga, f_programada, f_firma, x_observacion, x_ape_paterno, x_ape_materno, x_nombres, c_usuario, x_nom_instancia, c_estado, c_persona, tx_formato, tx_doc_id";
$sql_from_table = "FROM mp_cons_firm";
$sql_base_where = " WHERE 1=1";

$conditions = [];
$params_where = []; 
$types_where = "";  

if ($_SERVER["REQUEST_METHOD"] == "GET" && count($_GET) > 0 ) { 
    if (!empty($search_ape_paterno)) {
        $conditions[] = "x_ape_paterno LIKE ?";
        $params_where[] = "%" . $search_ape_paterno . "%";
        $types_where .= "s";
    }
    if (!empty($search_ape_materno)) {
        $conditions[] = "x_ape_materno LIKE ?";
        $params_where[] = "%" . $search_ape_materno . "%";
        $types_where .= "s";
    }
    if (!empty($search_nombres)) {
        $conditions[] = "x_nombres LIKE ?";
        $params_where[] = "%" . $search_nombres . "%";
        $types_where .= "s";
    }
    if (!empty($search_dni)) { 
        $conditions[] = "tx_doc_id LIKE ?";
        $params_where[] = "%" . $search_dni . "%";
        $types_where .= "s";
    }
    if (!empty($search_fecha_inicio)) {
        $conditions[] = "DATE(f_firma) >= ?";
        $params_where[] = $search_fecha_inicio;
        $types_where .= "s";
    }
    if (!empty($search_fecha_fin)) {
        $conditions[] = "DATE(f_firma) <= ?";
        $params_where[] = $search_fecha_fin;
        $types_where .= "s";
    }
     
    if (!empty($search_ape_paterno) || !empty($search_ape_materno) || !empty($search_nombres) || !empty($search_dni) || !empty($search_fecha_inicio) || !empty($search_fecha_fin) || isset($_GET['buscar'])) {
        $searchTermOccurred = true;
    }
    
    $sql_where_clause = $sql_base_where;
    if (!empty($conditions)) {
        $sql_where_clause .= " AND " . implode(" AND ", $conditions);
    }
 
    $sql_count = "SELECT COUNT(*) " . $sql_from_table . $sql_where_clause;
    $stmt_count = $conn->prepare($sql_count);
    if (!$stmt_count) {
        $errors[] = "Error al preparar la consulta de conteo: " . $conn->error;
    } else {
        if (!empty($types_where)) {
            $stmt_count->bind_param($types_where, ...$params_where);
        }
        if ($stmt_count->execute()) {
            $stmt_count->bind_result($total_records);
            $stmt_count->fetch();
        } else {
            $errors[] = "Error al ejecutar la consulta de conteo: " . $stmt_count->error;
        }
        $stmt_count->close();
    }

    if ($total_records > 0) {
        $total_pages = ceil($total_records / RECORDS_PER_PAGE);
        if ($current_page > $total_pages) { 
            $current_page = $total_pages;
        }
    } else {
        $total_pages = 0; 
    }
    
    $offset = ($current_page - 1) * RECORDS_PER_PAGE;

    // Consulta para obtener los datos de la página actual
    $sql_data_query = "SELECT " . $sql_select_fields . " " . $sql_from_table . $sql_where_clause;
    $sql_data_query .= " ORDER BY f_firma DESC, id_registro DESC LIMIT ? OFFSET ?"; // Añadir orden y paginación

    $stmt_data = $conn->prepare($sql_data_query);
    if (!$stmt_data) {
        $errors[] = "Error al preparar la consulta de datos: " . $conn->error;
    } else {
        
        $params_data = $params_where; 
        $params_data[] = RECORDS_PER_PAGE;
        $params_data[] = $offset;

        $types_data = $types_where . "ii"; 

        if (!empty($types_data)) { 
            $stmt_data->bind_param($types_data, ...$params_data);
        }
        
        if ($stmt_data->execute()) {
            $result_set = $stmt_data->get_result();
            while ($row = $result_set->fetch_assoc()) {
                $results[] = $row;
            }
        } else {
            $errors[] = "Error al ejecutar la consulta de datos: " . $stmt_data->error;
        }
        $stmt_data->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Firmas</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background-color: #f0f2f5; color: #333; }
        .header { background-color: #073a6b; color: white; padding: 15px 30px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header h1 { margin: 0; font-size: 1.8em; }
        .container { max-width: 95%; margin: 30px auto; padding: 25px; background-color: #fff; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        
        form { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px 20px; margin-bottom: 25px; padding: 20px; background-color: #f9f9f9; border-radius: 6px; border: 1px solid #e0e0e0;}
        form div { display: flex; flex-direction: column; }
        label { font-weight: 600; margin-bottom: 6px; color: #333; font-size: 0.90em; }
        input[type="text"], input[type="date"] { padding: 9px 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 0.95em; transition: border-color 0.3s; }
        input[type="text"]:focus, input[type="date"]:focus { border-color: #073a6b; outline: none; box-shadow: 0 0 0 0.1rem rgba(0,123,255,.25); }
        
        .form-actions { grid-column: 1 / -1; display: flex; justify-content: flex-start; gap: 10px; margin-top:10px;} /* Alineado a la izquierda */
        input[type="submit"], .clear-button { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; font-weight: 500; transition: background-color 0.3s; }
        input[type="submit"] { background-color: #073a6b; color: white; }
        input[type="submit"]:hover { background-color: #0056b3; }
        .clear-button { background-color: #6c757d; color: white; text-decoration: none; display: inline-block; text-align:center; }
        .clear-button:hover { background-color: #545b62; }

        .results-info { margin-bottom: 15px; padding: 12px; background-color: #e9ecef; border-radius: 4px; font-size: 0.95em; border: 1px solid #ced4da; }
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 0px; } /* Reducido margen superior de la tabla */
        th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; font-size: 0.85em; white-space: nowrap; } /* Reducido padding y nowrap */
        th { background-color: #073a6b; color: white; font-weight: 600; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f1f1f1; }
        .no-results { text-align: center; color: #777; margin-top: 25px; padding: 15px; background-color: #fff3cd; border: 1px solid #ffeeba; border-radius: 4px; }
        .error-message { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 12px; border-radius: 4px; margin-bottom: 20px; }
        .initial-message { text-align: center; padding: 20px; background-color: #e2f3ff; border: 1px solid #b8dCff; color: #004085; border-radius: 4px;}
        .pagination { text-align: center; margin-top: 20px; padding-bottom: 10px; }
        .pagination a, .pagination strong, .pagination span { display: inline-block; padding: 6px 10px; margin: 0 3px; border: 1px solid #ddd; text-decoration: none; color: #007bff; border-radius:4px; font-size:0.9em; }
        .pagination strong { background-color: #073a6b; color: white; border-color: #073a6b;}
        .pagination a:hover { background-color: #eee; }
        .pagination span { color: #aaa; border: 1px solid transparent;}
    </style>
</head>
<body>
    <header class="header">
        <h1>Consulta de Firmas</h1>
    </header>

    <div class="container">
        <form method="GET" action="consulta_firma.php">
            <div>
                <label for="ape_paterno">Apellido Paterno:</label>
                <input type="text" id="ape_paterno" name="ape_paterno" value="<?php echo htmlspecialchars($search_ape_paterno); ?>">
            </div>
            <div>
                <label for="ape_materno">Apellido Materno:</label>
                <input type="text" id="ape_materno" name="ape_materno" value="<?php echo htmlspecialchars($search_ape_materno); ?>">
            </div>
            <div>
                <label for="nombres">Nombres:</label>
                <input type="text" id="nombres" name="nombres" value="<?php echo htmlspecialchars($search_nombres); ?>">
            </div>
            <div>
                <label for="dni">DNI:</label>
                <input type="text" id="dni" name="dni" value="<?php echo htmlspecialchars($search_dni); ?>">
            </div>
            <div>
                <label for="fecha_firma_inicio">Fecha Firma Desde:</label>
                <input type="date" id="fecha_firma_inicio" name="fecha_firma_inicio" value="<?php echo htmlspecialchars($search_fecha_inicio); ?>">
            </div>
            <div>
                <label for="fecha_firma_fin">Fecha Firma Hasta:</label>
                <input type="date" id="fecha_firma_fin" name="fecha_firma_fin" value="<?php echo htmlspecialchars($search_fecha_fin); ?>">
            </div>
            <div class="form-actions">
                <input type="submit" name="buscar" value="Buscar"> <a href="consulta_firma.php" class="clear-button">Limpiar Filtros</a>
            </div>
        </form>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($searchTermOccurred || $current_page > 1 || count($_GET) > 0 && !isset($_GET['buscar']) && !empty(array_filter($_GET, function($k){ return $k !== 'page';}, ARRAY_FILTER_USE_KEY )) ): // Mostrar si se buscó, se está en otra página, o hay filtros activos (excepto solo 'page') ?>
            <?php if (!empty($results)): ?>
                <div class="results-info">
                    Página <strong><?php echo $current_page; ?></strong> de <strong><?php echo $total_pages > 0 ? $total_pages : 1; ?></strong>.
                    Total de registros encontrados: <strong><?php echo $total_records; ?></strong>.
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <?php foreach ($display_columns as $col_header): ?>
                                    <th><?php echo htmlspecialchars($col_header); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $row): ?>
                                <tr>
                                    <?php foreach ($display_columns as $col_key => $col_header): ?>
                                        <td>
                                            <?php
                                            $cell_value = isset($row[$col_key]) ? $row[$col_key] : '';
                                            // Formatear fechas si la clave contiene 'fecha' o 'f_'
                                            if (($col_key == 'fecha_carga' || $col_key == 'f_programada' || $col_key == 'f_firma') && !empty($cell_value)) {
                                                try {
                                                    $date = new DateTime($cell_value);
                                                    echo htmlspecialchars($date->format('d/m/Y H:i:s'));
                                                } catch (Exception $e) {
                                                    echo htmlspecialchars($cell_value); // Mostrar como está si no es fecha válida
                                                }
                                            } elseif ($col_key == 'x_observacion') {
                                                echo nl2br(htmlspecialchars($cell_value));
                                            } else {
                                                echo htmlspecialchars($cell_value);
                                            }
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <?php
                    if ($total_pages > 1):
                        // Construir query string base para paginación, manteniendo filtros
                        $query_params_pagination = [];
                        if (!empty($search_ape_paterno)) $query_params_pagination['ape_paterno'] = $search_ape_paterno;
                        if (!empty($search_ape_materno)) $query_params_pagination['ape_materno'] = $search_ape_materno;
                        if (!empty($search_nombres)) $query_params_pagination['nombres'] = $search_nombres;
                        if (!empty($search_dni)) $query_params_pagination['dni'] = $search_dni;
                        if (!empty($search_fecha_inicio)) $query_params_pagination['fecha_firma_inicio'] = $search_fecha_inicio;
                        if (!empty($search_fecha_fin)) $query_params_pagination['fecha_firma_fin'] = $search_fecha_fin;
                        if(isset($_GET['buscar'])) $query_params_pagination['buscar'] = $_GET['buscar'];


                        $base_url = "consulta_firma.php?" . http_build_query($query_params_pagination) . "&";

                        if ($current_page > 1): ?>
                            <a href="<?php echo $base_url; ?>page=1">Primera</a>
                            <a href="<?php echo $base_url; ?>page=<?php echo $current_page - 1; ?>">Anterior</a>
                        <?php endif; ?>

                        <?php
                        $range = 2; // Cuántas páginas mostrar antes y después de la actual
                        $start_loop = max(1, $current_page - $range);
                        $end_loop = min($total_pages, $current_page + $range);

                        if ($start_loop > 1) {
                            echo "<a href='{$base_url}page=1'>1</a>";
                            if ($start_loop > 2) {
                                echo "<span>...</span>";
                            }
                        }

                        for ($i = $start_loop; $i <= $end_loop; $i++): ?>
                            <?php if ($i == $current_page): ?>
                                <strong><?php echo $i; ?></strong>
                            <?php else: ?>
                                <a href="<?php echo $base_url; ?>page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php
                        if ($end_loop < $total_pages) {
                            if ($end_loop < $total_pages - 1) {
                                echo "<span>...</span>";
                            }
                            echo "<a href='{$base_url}page={$total_pages}'>{$total_pages}</a>";
                        }
                        ?>

                        <?php if ($current_page < $total_pages): ?>
                            <a href="<?php echo $base_url; ?>page=<?php echo $current_page + 1; ?>">Siguiente</a>
                            <a href="<?php echo $base_url; ?>page=<?php echo $total_pages; ?>">Última</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

            <?php elseif ($searchTermOccurred && empty($errors)): // Si se buscó y no hay errores, pero no hay resultados ?>
                <p class="no-results">No se encontraron registros que coincidan con los criterios de búsqueda.</p>
            <?php endif; ?>
        <?php elseif (empty($errors) && !isset($_GET['buscar']) && empty(array_filter($_GET))): // Mensaje inicial si no hay errores ni búsqueda activa ?>
            <div class="initial-message">
                <p>Por favor, ingrese uno o más criterios en los campos de arriba y presione "Buscar" para ver los resultados.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>