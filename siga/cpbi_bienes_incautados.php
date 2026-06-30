<?php
// cpbi_bienes_incautados.php

require_once 'include/cabecera.php';
require_once 'classes/Html.class.php';
require_once 'classes/Db.class.php';
require_once dirname(__FILE__) . '/spreadsheets/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Inicializar conexión a la base de datos
$Db = new Db();

// --- Lógica para Exportar a Excel ---
if (isset($_GET['expxls'])) {
    // Sanitizar entradas
    $text_busc = isset($_GET['text_busc']) ? trim($_GET['text_busc']) : '';
    $codi_deli = isset($_GET['codi_deli']) ? intval($_GET['codi_deli']) : 0;
    $id_tipo = isset($_GET['id_tipo']) ? intval($_GET['id_tipo']) : 0;

    $condadd_export = "WHERE 1=1";
    if ($text_busc !== '') {
        // ADVERTENCIA: Esto es vulnerable a Inyección SQL.
        // La clase Db debería ser actualizada para soportar sentencias preparadas.
        $condadd_export .= " AND desc_bien LIKE '%" . $text_busc . "%' ";
    }
    if ($codi_deli > 0) {
        $condadd_export .= " AND mp_cpbi_bienes.codi_deli = " . $codi_deli;
    }
    if ($id_tipo > 0) {
        $condadd_export .= " AND mp_cpbi_bienes.id_tipo_bien = " . $id_tipo;
    }

    $sql_export = "SELECT mp_cpbi_bienes.*, mp_maes_delito.x_nombre as desc_delito, 
            mp_maes_cpbi_estado.x_nombre as desc_esta, mp_maes_cpbi_estado_proceso.x_nombre as esta_proceso, mp_maes_personal.appa_pers,
            mp_maes_personal.apma_pers, mp_maes_personal.nomb_pers, tipos.x_nombre as tipo_bien
            FROM mp_cpbi_bienes
            LEFT JOIN mp_maes_cpbi_tipos as tipos ON mp_cpbi_bienes.id_tipo_bien = tipos.n_codigo
            LEFT JOIN mp_maes_cpbi_estado ON mp_cpbi_bienes.codi_esta = mp_maes_cpbi_estado.n_codigo
            LEFT JOIN mp_maes_delito ON mp_cpbi_bienes.codi_deli = mp_maes_delito.n_codigo
            LEFT JOIN mp_maes_personal ON mp_cpbi_bienes.codi_fisc = mp_maes_personal.iden_pers
            LEFT JOIN mp_maes_cpbi_estado_proceso ON mp_cpbi_bienes.codi_epro = mp_maes_cpbi_estado_proceso.n_codigo
            $condadd_export ORDER BY desc_bien ASC";

    $result_pagi = $Db->query($sql_export);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Bienes Incautados");

    $sheet->setCellValue('A1', 'MINISTERIO PÚBLICO');
    $sheet->setCellValue('A2', 'BIENES INCAUTADOS');
    $sheet->getStyle('A1:A2')->getFont()->setBold(true);

    $headers = [
        "FECHA INTERNAMIENTO", "N° REGISTRO", "DESCRIPCIÓN", "TIPO DE BIEN", "MARCA", "SERIE", "ESTADO",
        "CARPETA", "FISCAL", "DELITO", "AUTOR DEL DELITO", "PERJUDICADO DEL DELITO",
        "DÍAS", "SITUACIÓN PROCESO"
    ];
    $sheet->fromArray($headers, NULL, 'A6');
    $sheet->getStyle('A6:N6')->getFont()->setBold(true);

    $solofecha = date('Y-m-d');
    $row_num = 7;
    if($result_pagi){
        foreach ($result_pagi as $row) {
            $fecha1 = new DateTime($row['fech_inte']);
            $fecha2 = new DateTime($solofecha);
            $diff = $fecha1->diff($fecha2);
            $cantdias = $diff->days;

            $rowData = [
                $row['fech_inte'],
                $row['nume_regi'],
                $row['desc_bien'],
                $row['tipo_bien'],
                $row['marc_bien'],
                $row['seri_bien'],
                $row['desc_esta'],
                $row['nume_carp'],
                trim($row['appa_pers'] . " " . $row['apma_pers'] . " " . $row['nomb_pers']),
                $row['desc_delito'],
                $row['agraviante'],
                $row['agraviado'],
                $cantdias,
                $row['esta_proceso']
            ];
            $sheet->fromArray($rowData, NULL, 'A' . $row_num);
            $row_num++;
        }
    }


    foreach (range('A', 'N') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    $nomarhi = "bienes_incautados.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $nomarhi . '"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    ob_clean(); // Limpiar el búfer de salida antes de enviar las cabeceras
    $writer->save('php://output');
    exit();
}

// --- Lógica Principal de la Página ---
$is_search = isset($_POST['form_submitted']);

// Obtener y sanitizar parámetros de búsqueda
$text_busc = isset($_POST['text_busc']) ? trim($_POST['text_busc']) : '';
$nume_regi = isset($_POST['nume_regi']) ? trim($_POST['nume_regi']) : '';
$codi_deli = isset($_POST['codi_deli']) ? intval($_POST['codi_deli']) : 0;
$id_tipo = isset($_POST['id_tipo']) ? intval($_POST['id_tipo']) : 0;
$date_filter = isset($_POST['date_filter']) ? $_POST['date_filter'] : '';
$current_page = isset($_POST['busc_pagi_actu']) ? intval($_POST['busc_pagi_actu']) : 1;
if($current_page < 1) $current_page = 1;

// Preparar datos para los menús desplegables
$delitos = $Db->select('mp_maes_delito', '', '', '', ['x_nombre' => 'ASC']);
$tipos = $Db->select('mp_maes_cpbi_tipos', 'n_estado=1', '', '', ['x_nombre' => 'ASC']);

$busc_tota_item = 0;
$result_pagi = [];
$busc_tota_pagi = 0;

// Realizar búsqueda si el formulario fue enviado
if ($is_search) {
    $params = [];
    $condadd = "WHERE 1=1";

    if (!empty($text_busc)) {
        $condadd .= " AND desc_bien LIKE :text_busc";
        $params[':text_busc'] = '%' . $text_busc . '%';
    }
    if (!empty($nume_regi)) {
        $condadd .= " AND nume_regi = :nume_regi";
        $params[':nume_regi'] = $nume_regi;
    }
    if (!empty($codi_deli)) {
        $condadd .= " AND mp_cpbi_bienes.codi_deli = :codi_deli";
        $params[':codi_deli'] = $codi_deli;
    }
    if (!empty($id_tipo)) {
        $condadd .= " AND mp_cpbi_bienes.id_tipo_bien = :id_tipo";
        $params[':id_tipo'] = $id_tipo;
    }
    if (!empty($date_filter)) {
        switch ($date_filter) {
            case 'today': // hoy
                $condadd .= " AND fech_inte >= CURDATE() AND fech_inte < DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
                break;
            case 'yesterday': // ayer
                $condadd .= " AND fech_inte >= DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND fech_inte < CURDATE()";
                break;
            case 'last_week': // última semana
                $condadd .= " AND fech_inte >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
                break;
            case 'last_month': // último mes
                $condadd .= " AND fech_inte >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
                break;
            case 'this_year': // este año
                $condadd .= " AND YEAR(fech_inte) = YEAR(CURDATE())";
                break;
        }
    }

    $count_sql = "SELECT COUNT(*) as total FROM mp_cpbi_bienes " . $condadd;
    $total_result = $Db->query($count_sql, $params);
    $busc_tota_item = isset($total_result[0]['total']) ? $total_result[0]['total'] : 0;

    $busc_item_pagi = 20; // Items per page
    $busc_tota_pagi = ceil($busc_tota_item / $busc_item_pagi);
    $busc_limi_pagi = ($current_page - 1) * $busc_item_pagi;

    $query = "SELECT mp_cpbi_bienes.*, mp_maes_delito.x_nombre as desc_delito,
              mp_maes_cpbi_estado.x_nombre as desc_esta, mp_maes_cpbi_estado_proceso.x_nombre as esta_proceso,
              mp_maes_personal.appa_pers, mp_maes_personal.apma_pers, mp_maes_personal.nomb_pers,
              tipos.x_nombre as tipo_bien
              FROM mp_cpbi_bienes
              LEFT JOIN mp_maes_cpbi_tipos as tipos ON mp_cpbi_bienes.id_tipo_bien = tipos.n_codigo
              LEFT JOIN mp_maes_cpbi_estado ON mp_cpbi_bienes.codi_esta = mp_maes_cpbi_estado.n_codigo
              LEFT JOIN mp_maes_delito ON mp_cpbi_bienes.codi_deli = mp_maes_delito.n_codigo
              LEFT JOIN mp_maes_personal ON mp_cpbi_bienes.codi_fisc = mp_maes_personal.iden_pers
              LEFT JOIN mp_maes_cpbi_estado_proceso ON mp_cpbi_bienes.codi_epro = mp_maes_cpbi_estado_proceso.n_codigo
              $condadd ORDER BY mp_cpbi_bienes.codi_bien DESC LIMIT $busc_limi_pagi, $busc_item_pagi";
    
    $result_pagi = $Db->query($query, $params);
    $solofecha = date('Y-m-d');
}
?>
<?php
$page_title = 'Bienes Incautados';
require_once 'include/page_header.php';
?>
    <div class="container-fluid mt-4">
        <h2 class="text-center mb-4 text-primary">BIENES INCAUTADOS</h2>

        <form name="form" method="post" action="cpbi_bienes_incautados.php">
            <input type="hidden" name="codi_bien">
            <input type="hidden" name="busc_pagi_actu" value="<?= htmlspecialchars($current_page) ?>">
            <input type="hidden" name="form_submitted" value="1">

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-search"></i> Formulario de Búsqueda
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="text_busc" class="form-label">Descripción del Bien</label>
                            <input type="text" name="text_busc" id="text_busc" class="form-control" placeholder="Ingrese texto a buscar" value="<?= htmlspecialchars($text_busc) ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="nume_regi" class="form-label">Número de Registro</label>
                            <input type="text" name="nume_regi" id="nume_regi" class="form-control" placeholder="Ingrese número" value="<?= htmlspecialchars($nume_regi) ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="codi_deli" class="form-label">Delito</label>
                            <select name="codi_deli" id="codi_deli" class="form-select">
                                <option value="0">&lt;- Todos -&gt;</option>
                                <?php if($delitos) foreach ($delitos as $delito): ?>
                                    <option value="<?= $delito['n_codigo'] ?>" <?= ($codi_deli == $delito['n_codigo']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($delito['x_nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="id_tipo" class="form-label">Tipo de Bien</label>
                            <select name="id_tipo" id="id_tipo" class="form-select">
                                <option value="0">&lt;- Todos -&gt;</option>
                                <?php if($tipos) foreach ($tipos as $tipo): ?>
                                    <option value="<?= $tipo['n_codigo'] ?>" <?= ($id_tipo == $tipo['n_codigo']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tipo['x_nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date_filter" class="form-label">Fecha de Internamiento</label>
                            <select name="date_filter" id="date_filter" class="form-select">
                                <option value="">-- Todas --</option>
                                <option value="today" <?= ($date_filter == 'today') ? 'selected' : '' ?>>Hoy</option>
                                <option value="yesterday" <?= ($date_filter == 'yesterday') ? 'selected' : '' ?>>Ayer</option>
                                <option value="last_week" <?= ($date_filter == 'last_week') ? 'selected' : '' ?>>Última Semana</option>
                                <option value="last_month" <?= ($date_filter == 'last_month') ? 'selected' : '' ?>>Último Mes</option>
                                <option value="this_year" <?= ($date_filter == 'this_year') ? 'selected' : '' ?>>Este Año</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Buscar Bienes</button>
                            <button type="button" class="btn btn-success" onclick="f_nuevo()"><i class="bi bi-plus-circle"></i> Agregar Nuevo</button>
                            <button type="button" class="btn btn-info" onclick="f_exportar(event)"><i class="bi bi-file-earmark-excel"></i> Exportar a Excel</button>
                            <button type="button" class="btn btn-secondary" onclick="f_limpiar()"><i class="bi bi-eraser"></i> Limpiar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <?php if ($is_search): ?>
        <div class="card shadow-sm">
            <div class="card-header">
                <i class="bi bi-list-ul"></i> Resultados de Búsqueda: <?= $busc_tota_item ?> encontrados
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Editar</th>
                                <th>Movimientos</th>
                                <th>Archivo Digital</th>
                                <th>Fecha internamiento</th>
                                <th>Nº Registro</th>
                                <th>Descripción</th>
                                <th>Tipo de Bien</th>
                                <th>Marca</th>
                                <th>Serie</th>
                                <th>Estado</th>
                                <th>Carpeta</th>
                                <th>Fiscal</th>
                                <th>Delito</th>
                                <th>Autor delito</th>
                                <th>Perjudicado</th>
                                <th>Días</th>
                                <th>Est. Proceso</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($busc_tota_item > 0 && $result_pagi): ?>
                                <?php foreach ($result_pagi as $rows):
                                    $fecha1 = new DateTime($rows['fech_inte']);
                                    $fecha2 = new DateTime($solofecha);
                                    $diff = $fecha1->diff($fecha2);
                                    $cantdias = $diff->days;
                                ?>
                                    <tr>
                                        <td class="text-center"><a href="#" onclick="f_editar('<?= $rows['codi_bien'] ?>'); return false;"><i class="bi bi-pencil-square text-primary"></i></a></td>
                                        <td class="text-center"><a href="#" onclick="f_movimientos('<?= $rows['codi_bien'] ?>'); return false;"><i class="bi bi-arrows-move text-primary"></i></a></td>
                                        <td class="text-center">
                                            <?php if (!empty($rows['ruta_archivo_digital'])) : ?>
                                                <a href="<?= htmlspecialchars($rows['ruta_archivo_digital']) ?>" target="_blank"><i class="bi bi-file-earmark-arrow-down-fill text-success"></i></a>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($rows['fech_inte']) ?></td>
                                        <td><?= htmlspecialchars($rows['nume_regi']) ?></td>
                                        <td><?= htmlspecialchars($rows['desc_bien']) ?></td>
                                        <td><?= htmlspecialchars($rows['tipo_bien']) ?></td>
                                        <td><?= htmlspecialchars($rows['marc_bien']) ?></td>
                                        <td><?= htmlspecialchars($rows['seri_bien']) ?></td>
                                        <td><?= htmlspecialchars($rows['desc_esta']) ?></td>
                                        <td><?= htmlspecialchars($rows['nume_carp']) ?></td>
                                        <td><?= htmlspecialchars(trim($rows['appa_pers'] . " " . $rows['apma_pers'] . " " . $rows['nomb_pers'])) ?></td>
                                        <td><?= htmlspecialchars($rows['desc_delito']) ?></td>
                                        <td><?= htmlspecialchars($rows['agraviante']) ?></td>
                                        <td><?= htmlspecialchars($rows['agraviado']) ?></td>
                                        <td><?= $cantdias ?></td>
                                        <td><?= htmlspecialchars($rows['esta_proceso']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="17" class="text-center">No se encontraron bienes con los criterios de búsqueda.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($busc_tota_pagi > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $busc_tota_pagi; $i++): ?>
                                <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                    <a class="page-link" href="#" onclick="change_page(<?= $i ?>)"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

<?php require_once 'include/page_footer.php'; ?>

<script>
function f_nuevo() {
    window.location.href = 'cpbi_bienes_registro.php';
}

function f_editar(codi_bien) {
    window.location.href = `cpbi_bienes_registro.php?codi_bien=${codi_bien}`;
}

function f_movimientos(codi_bien) {
    window.location.href = `cpbi_bienes_movs.php?codi_bien=${codi_bien}`;
}

function f_limpiar() {
    window.location.href = 'cpbi_bienes_incautados.php';
}

function f_exportar(event) {
    event.preventDefault();
    const form = document.forms['form'];
    const text_busc = form.text_busc.value;
    const codi_deli = form.codi_deli.value;
    const id_tipo = form.id_tipo.value;

    let url = 'cpbi_bienes_incautados.php?expxls=1';
    if (text_busc) url += `&text_busc=${encodeURIComponent(text_busc)}`;
    if (codi_deli) url += `&codi_deli=${codi_deli}`;
    if (id_tipo) url += `&id_tipo=${id_tipo}`;

    window.location.href = url;
}

function change_page(page) {
    const form = document.forms['form'];
    form.busc_pagi_actu.value = page;
    form.submit();
}
</script>