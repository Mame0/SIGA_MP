<?php
// No incluir cabecera.php para control total del HTML
require_once 'classes/Html.class.php';
require_once 'classes/Db.class.php';

// Inicializar la conexión a la base de datos
try {
    $db = new Db();
} catch (Exception $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}

$page_title = 'Actualizar Firmas desde CSV';

$success_message = '';
$error_message = '';
$info_message = '';
$error_details = [];
$duplicate_details = [];

/**
 * Parsea una cadena de fecha en varios formatos comunes.
 * @param string $date_string La fecha en formato de cadena.
 * @return string|null La fecha en formato Y-m-d H:i:s o null si no se puede parsear.
 */
function parse_date_flexible($date_string) {
    if (empty($date_string)) {
        return null;
    }
    
    // Normaliza separadores y elimina espacios extra
    $date_string = trim(str_replace('/', '-', $date_string));

    // Formatos a intentar, incluyendo aquellos con microsegundos.
    $formats_to_try = [
        'd-m-Y H:i:s.u', 'd-m-Y H:i:s', 'd-m-Y H:i', 'd-m-Y',
        'Y-m-d H:i:s.u', 'Y-m-d H:i:s', 'Y-m-d H:i', 'Y-m-d'
    ];

    foreach ($formats_to_try as $format) {
        $date_obj = DateTime::createFromFormat($format, $date_string);
        // Si el formato es con 'u' pero no hay microsegundos, createFromFormat puede devolver la fecha actual.
        // Verificamos que no haya errores y que el año sea razonable.
        if ($date_obj !== false && $date_obj->format('Y') > 1900) {
            return $date_obj->format('Y-m-d H:i:s');
        }
    }
    
    // Fallback para formatos más genéricos que strtotime puede entender
    $timestamp = strtotime($date_string);
    if ($timestamp !== false) {
        return date('Y-m-d H:i:s', $timestamp);
    }

    return null;
}


/**
 * Procesa un archivo CSV para insertar registros de firmas en la base de datos.
 *
 * @param string $csv_file Ruta al archivo CSV.
 * @param Db $db Instancia de la clase Db.
 * @return array Un array con el conteo de registros y los detalles de errores.
 */
function procesarArchivoFirmasCSV($csv_file, $db) {
    $response = [
        'counts' => ['inserted' => 0, 'duplicates' => 0, 'errors' => 0],
        'error_details' => [],
        'duplicate_details' => []
    ];
    $handle = fopen($csv_file, "r");
    $line_number = 0;

    if ($handle === FALSE) {
        return false;
    }

    $db->beginTransaction();

    try {
        while (($data = fgetcsv($handle, 2000, ";")) !== FALSE) {
            $line_number++;
            
            $data = array_map(function($d) { return mb_convert_encoding($d, 'UTF-8', 'ISO-8859-1'); }, $data);

            $f_programada_raw = isset($data[0]) ? trim($data[0]) : '';
            $f_firma_raw = isset($data[1]) ? trim($data[1]) : '';
            $x_observacion = isset($data[2]) ? trim($data[2]) : '';
            $x_ape_paterno = isset($data[3]) ? trim($data[3]) : '';
            $x_ape_materno = isset($data[4]) ? trim($data[4]) : '';
            $x_nombres = isset($data[5]) ? trim($data[5]) : '';
            $c_usuario = isset($data[6]) ? trim($data[6]) : '';
            $x_nom_instancia = isset($data[7]) ? trim($data[7]) : '';
            $c_estado = isset($data[8]) ? trim($data[8]) : '';
            $c_persona = isset($data[9]) ? trim($data[9]) : '';
            $tx_formato = isset($data[10]) ? trim($data[10]) : '';
            $tx_doc_id = isset($data[11]) ? trim($data[11]) : null;

            $f_firma_mysql = parse_date_flexible($f_firma_raw);
            $f_programada_mysql = parse_date_flexible($f_programada_raw);

            if (empty($tx_formato) || empty($f_firma_mysql)) {
                $reason = 'Datos clave incompletos: ';
                if (empty($tx_formato)) $reason .= '[Columna 11: tx_formato está vacía]. ';
                if (empty($f_firma_mysql)) $reason .= "[Columna 2: f_firma ('{$f_firma_raw}') no es una fecha válida]. ";
                $response['error_details'][] = ['line' => $line_number, 'reason' => $reason, 'data' => implode('; ', $data)];
                $response['counts']['errors']++;
                continue;
            }

            $validation_error_reason = '';
            if (mb_strlen($x_ape_paterno, 'UTF-8') > 255) { $validation_error_reason .= '[Col 4: Apellido Paterno demasiado largo]. '; }
            if (mb_strlen($x_ape_materno, 'UTF-8') > 255) { $validation_error_reason .= '[Col 5: Apellido Materno demasiado largo]. '; }
            if (mb_strlen($x_nombres, 'UTF-8') > 255) { $validation_error_reason .= '[Col 6: Nombres demasiado largo]. '; }
            if (mb_strlen($c_usuario, 'UTF-8') > 100) { $validation_error_reason .= '[Col 7: Usuario demasiado largo]. '; }
            if (mb_strlen($x_nom_instancia, 'UTF-8') > 255) { $validation_error_reason .= '[Col 8: Instancia demasiado larga]. '; }
            if (mb_strlen($c_estado, 'UTF-8') > 50) { $validation_error_reason .= '[Col 9: Estado demasiado largo]. '; }
            if (mb_strlen($c_persona, 'UTF-8') > 100) { $validation_error_reason .= '[Col 10: Persona demasiado larga]. '; }
            if (mb_strlen($tx_formato, 'UTF-8') > 255) { $validation_error_reason .= '[Col 11: Formato demasiado largo]. '; }
            if ($tx_doc_id !== null && mb_strlen($tx_doc_id, 'UTF-8') > 100) { $validation_error_reason .= '[Col 12: Doc ID demasiado largo]. '; }

            if (!empty($validation_error_reason)) {
                $response['error_details'][] = ['line' => $line_number, 'reason' => $validation_error_reason, 'data' => implode('; ', $data)];
                $response['counts']['errors']++;
                continue;
            }

            $conditions = [
                'tx_formato' => $tx_formato,
                'f_firma' => $f_firma_mysql
            ];
            if ($tx_doc_id === null) {
                $conditions[] = 'tx_doc_id IS NULL';
            } else {
                $conditions['tx_doc_id'] = $tx_doc_id;
            }
            
            $existing_record = $db->select('mp_cons_firm', $conditions, 1);

            if ($existing_record) {
                $response['counts']['duplicates']++;
                $response['duplicate_details'][] = ['line' => $line_number, 'data' => implode('; ', $data)];
            } else {
                $data_to_insert = [
                    'f_programada' => $f_programada_mysql,
                    'f_firma' => $f_firma_mysql,
                    'x_observacion' => $x_observacion,
                    'x_ape_paterno' => $x_ape_paterno,
                    'x_ape_materno' => $x_ape_materno,
                    'x_nombres' => $x_nombres,
                    'c_usuario' => $c_usuario,
                    'x_nom_instancia' => $x_nom_instancia,
                    'c_estado' => $c_estado,
                    'c_persona' => $c_persona,
                    'tx_formato' => $tx_formato,
                    'tx_doc_id' => $tx_doc_id
                ];

                $result = $db->insert('mp_cons_firm', $data_to_insert);

                if ($result) {
                    $response['counts']['inserted']++;
                } else {
                    $response['error_details'][] = ['line' => $line_number, 'reason' => 'Error de BD al insertar: ' . $db->getLastError(), 'data' => implode('; ', $data)];
                    $response['counts']['errors']++;
                }
            }
        }
        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    } finally {
        fclose($handle);
    }

    return $response;
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['archivo_csv'])) {
    if ($_FILES['archivo_csv']['error'] == UPLOAD_ERR_OK) {
        $csv_file = $_FILES['archivo_csv']['tmp_name'];
        
        try {
            $resultado = procesarArchivoFirmasCSV($csv_file, $db);
            if ($resultado !== false) {
                            $counts = $resultado['counts'];
                            $error_details = $resultado['error_details'];
                            $duplicate_details = $resultado['duplicate_details'];
                
                                        $nuevos = $counts['inserted'];
                
                                        $duplicados = $counts['duplicates'];
                
                                        $errores = $counts['errors'];
                
                                        $total = $nuevos + $duplicados + $errores;
                
                            
                
                                                                $info_message = '<h5 class="card-title mb-3">Resumen de Importación</h5>';
                
                            
                
                                                                $info_message .= '<div class="fs-4">';
                
                            
                
                                                                $info_message .= '<p class="mb-1">Total de filas procesadas: <span class="badge bg-secondary">' . $total . '</span></p>';
                
                            
                
                                                                $info_message .= '<p class="mb-1">Registros nuevos insertados: <span class="badge bg-success">' . $nuevos . '</span></p>';
                
                            
                
                                                                $info_message .= '<p class="mb-1">Registros duplicados omitidos: <span class="badge bg-warning text-dark">' . $duplicados . '</span></p>';
                
                            
                
                                                                $info_message .= '<p class="mb-1">Filas con errores o datos clave incompletos: <span class="badge bg-danger">' . $errores . '</span></p>';
                
                            
                
                                                                $info_message .= '</div>';            } else {
                $error_message = "No se pudo abrir el archivo CSV para procesarlo.";
            }
        } catch (Exception $e) {
            $error_message = "Ocurrió un error durante la importación: " . $e->getMessage();
        }

    } else {
        $error_message = "Error al subir el archivo. Código de error: " . $_FILES['archivo_csv']['error'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .card-header { background-color: #343a40; color: white; }
        .error-details, .duplicate-details { max-height: 400px; overflow-y: auto; font-size: 0.8rem; }
        .error-details pre, .duplicate-details pre { white-space: pre-wrap; word-break: break-all; }
    </style>
</head>
<body>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h5 class="card-title"><i class="bi bi-cloud-upload"></i> <?php echo $page_title; ?></h5></div>
                <div class="card-body">

                    <?php if ($success_message): ?><div class="alert alert-success"><?php echo $success_message; ?></div><?php endif; ?>
                    <?php if ($info_message): ?><div class="card shadow-sm mb-4"><div class="card-body bg-light"><?php echo $info_message; ?></div></div><?php endif; ?>
                    <?php if ($error_message): ?><div class="alert alert-danger"><?php echo $error_message; ?></div><?php endif; ?>

                    <?php if (!empty($error_details)): ?>
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-danger"><i class="bi bi-exclamation-triangle"></i> Detalle de Errores (Primeros 10 Ejemplos)</div>
                        <div class="card-body error-details">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th># Línea</th>
                                        <th>Razón del Error</th>
                                        <th>Datos de la Fila</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach (array_slice($error_details, 0, 10) as $error): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($error['line']); ?></td>
                                        <td><?php echo htmlspecialchars($error['reason']); ?></td>
                                        <td><small><?php echo htmlspecialchars($error['data']); ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($duplicate_details)): ?>
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-warning text-dark"><i class="bi bi-files"></i> Detalle de Duplicados Omitidos (Primeros 10 Ejemplos)</div>
                        <div class="card-body duplicate-details">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th># Línea</th>
                                        <th>Datos de la Fila</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach (array_slice($duplicate_details, 0, 10) as $duplicate): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($duplicate['line']); ?></td>
                                        <td><small><?php echo htmlspecialchars($duplicate['data']); ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="card shadow-sm mb-4">
                        <div class="card-header"><i class="bi bi-file-earmark-ruled"></i> Instrucciones</div>
                        <div class="card-body">
                            <p>1. El archivo debe estar en formato <strong>CSV (delimitado por punto y coma ';')</strong>.</p>
                            <p>2. El archivo <strong>NO debe contener una fila de cabecera</strong>.</p>
                            <p>3. El sistema omitirá registros duplicados basándose en <strong>Documento ID, Formato y Fecha de Firma</strong>.</p>
                            <p>4. Las columnas de fecha pueden estar en formatos como <code>d/m/Y H:i:s</code>, <code>d/m/Y</code>, o <code>Y-m-d H:i:s</code>.</p>
                        </div>
                    </div>

                    <form action="consulta_firma_actualizar.php" method="post" enctype="multipart/form-data">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header"><i class="bi bi-file-earmark-arrow-up"></i> Seleccionar Archivo para Importar</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="archivo_csv" class="form-label">Archivo CSV</label>
                                    <input class="form-control" type="file" id="archivo_csv" name="archivo_csv" accept=".csv" required>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-upload"></i> Iniciar Importación</button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
