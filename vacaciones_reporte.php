<?php
/* =====================================================================
 *  vacaciones_reporte.php — Reporte de vacaciones (formato del Excel origen)
 *  Fase 5. Una fila por tramo ACTIVO. Exporta a Excel (?export=xls).
 *  Ver DOC_MODULO_VACACIONES.md §8
 * ===================================================================== */
ob_start(); // permite enviar cabeceras de descarga aunque cabecera.php emita salida
require_once 'include/cabecera.php';

define('VACA_CARGO_LABEL', 'ASISTENTE ADMINISTRATIVO (CONDUCTOR)');

// --- Filtros ---
$f_periodo = isset($_GET['f_periodo']) ? trim($_GET['f_periodo']) : '';
$f_regimen = isset($_GET['f_regimen']) ? trim($_GET['f_regimen']) : '';
$export    = isset($_GET['export']) ? $_GET['export'] : '';

// --- Consulta: una fila por tramo ACTIVO (§8) ---
$where  = ["t.estado = 'ACTIVO'"];
$params = [];
if ($f_periodo !== '') { $where[] = 'p.etiqueta = :etq';  $params[':etq'] = $f_periodo; }
if ($f_regimen !== '') { $where[] = 'c.regimen = :reg';   $params[':reg'] = $f_regimen; }
$sqlWhere = implode(' AND ', $where);

$filas = $Db->query(
    "SELECT CONCAT(c.appat,' ',c.apmat,', ',c.nombres) AS nombre_completo,
            c.regimen, p.etiqueta, t.fecha_inicio, t.fecha_fin, t.dias
     FROM mp_vaca_tramo t
     JOIN mp_vaca_periodo   p ON p.id_periodo   = t.id_periodo
     JOIN mp_vaca_conductor c ON c.id_conductor = t.id_conductor
     WHERE $sqlWhere
     ORDER BY c.appat, c.apmat, c.nombres, p.etiqueta, t.fecha_inicio",
    $params
);
$filas = is_array($filas) ? $filas : [];
$totalDias = array_sum(array_map(fn($r) => (int)$r['dias'], $filas));

// Opciones de los filtros
$periodos  = $Db->query("SELECT etiqueta FROM mp_vaca_periodo_cat WHERE estado = 1 ORDER BY orden");
$regimenes = $Db->query("SELECT DISTINCT regimen FROM mp_vaca_conductor WHERE estado = 1 AND regimen <> '' ORDER BY regimen");

function vaca_fecha($f) {
    if (empty($f) || $f === '0000-00-00') return '-';
    $d = DateTime::createFromFormat('Y-m-d', $f);
    return $d ? $d->format('d/m/Y') : htmlspecialchars($f);
}

/* ---------------------------------------------------------------------
 *  Exportación a Excel (HTML con MIME .xls; sin dependencias externas)
 * ------------------------------------------------------------------- */
if ($export === 'xls') {
    if (ob_get_length() !== false) { ob_end_clean(); } // descarta salida previa del bootstrap
    $nombre = 'reporte_vacaciones_' . date('Ymd_His') . '.xls';
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $nombre . '"');
    header('Cache-Control: max-age=0');
    echo "\xEF\xBB\xBF"; // BOM UTF-8 para conservar tildes en Excel
    echo '<meta charset="utf-8">';
    echo '<table border="1">';
    echo '<thead><tr style="background:#073A6B;color:#fff;font-weight:bold;">'
       . '<th>APELLIDOS Y NOMBRES</th><th>CARGO</th><th>REGIMEN</th><th>PERIODO</th>'
       . '<th>FECHA DE INICIO</th><th>FECHA DE FIN</th><th>TOTAL VAC.</th></tr></thead><tbody>';
    foreach ($filas as $r) {
        echo '<tr>'
           . '<td>' . htmlspecialchars($r['nombre_completo']) . '</td>'
           . '<td>' . VACA_CARGO_LABEL . '</td>'
           . '<td>' . htmlspecialchars($r['regimen']) . '</td>'
           . '<td>' . htmlspecialchars($r['etiqueta']) . '</td>'
           . '<td>' . vaca_fecha($r['fecha_inicio']) . '</td>'
           . '<td>' . vaca_fecha($r['fecha_fin']) . '</td>'
           . '<td>' . (int)$r['dias'] . '</td>'
           . '</tr>';
    }
    echo '<tr style="font-weight:bold;background:#e9ecef;">'
       . '<td colspan="6" align="right">TOTAL DÍAS</td><td>' . $totalDias . '</td></tr>';
    echo '</tbody></table>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Vacaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .header-vaca { background-color: #073A6B; }
        .card-header { background-color: #073A6B; color: #fff; font-weight: bold; }
        .btn-primary { background-color: #073A6B; border-color: #073A6B; }
        .btn-primary:hover { background-color: #052849; border-color: #052849; }
        .table th { background-color: #e9ecef; }
        .text-primary { color: #073A6B !important; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>
    <header class="header-vaca text-white p-3 mb-3 no-print">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="bi bi-file-earmark-spreadsheet fs-2 me-2"></i>
                <h1 class="h4 mb-0">Reporte de Vacaciones</h1>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-light btn-sm fw-bold" onclick="window.print()">
                    <i class="bi bi-printer"></i> Imprimir
                </button>
                <a id="btnExcel" class="btn btn-success btn-sm fw-bold">
                    <i class="bi bi-file-earmark-excel"></i> Exportar Excel
                </a>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <!-- Filtros -->
        <form class="card shadow-sm mb-3 no-print" method="get" id="filtros">
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small mb-0">Periodo</label>
                        <select class="form-select form-select-sm" name="f_periodo">
                            <option value="">— Todos —</option>
                            <?php foreach ($periodos as $p): ?>
                                <option value="<?= htmlspecialchars($p['etiqueta']) ?>" <?= $f_periodo === $p['etiqueta'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['etiqueta']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-0">Régimen</label>
                        <select class="form-select form-select-sm" name="f_regimen">
                            <option value="">— Todos —</option>
                            <?php foreach ($regimenes as $r): ?>
                                <option value="<?= htmlspecialchars($r['regimen']) ?>" <?= $f_regimen === $r['regimen'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($r['regimen']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-funnel"></i> Filtrar</button>
                        <a class="btn btn-outline-secondary btn-sm" href="vacaciones_reporte.php"><i class="bi bi-x-circle"></i> Limpiar</a>
                    </div>
                </div>
            </div>
        </form>

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-table"></i> Programación de vacaciones (una fila por tramo)</span>
                <span class="badge bg-light text-dark">Filas: <?= count($filas) ?> · Total días: <?= $totalDias ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>APELLIDOS Y NOMBRES</th>
                                <th>CARGO</th>
                                <th class="text-center">RÉGIMEN</th>
                                <th class="text-center">PERIODO</th>
                                <th class="text-center">FECHA DE INICIO</th>
                                <th class="text-center">FECHA DE FIN</th>
                                <th class="text-center">TOTAL VAC.</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (count($filas) === 0): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">
                                No hay tramos de vacaciones que coincidan con el filtro.
                            </td></tr>
                        <?php else: foreach ($filas as $r): ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($r['nombre_completo']) ?></td>
                                <td><?= VACA_CARGO_LABEL ?></td>
                                <td class="text-center"><?= htmlspecialchars($r['regimen']) ?: '-' ?></td>
                                <td class="text-center"><?= htmlspecialchars($r['etiqueta']) ?></td>
                                <td class="text-center"><?= vaca_fecha($r['fecha_inicio']) ?></td>
                                <td class="text-center"><?= vaca_fecha($r['fecha_fin']) ?></td>
                                <td class="text-center fw-bold"><?= (int)$r['dias'] ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                        <?php if (count($filas) > 0): ?>
                        <tfoot>
                            <tr class="table-secondary fw-bold">
                                <td colspan="6" class="text-end">TOTAL DÍAS</td>
                                <td class="text-center"><?= $totalDias ?></td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // El botón Excel reusa los filtros actuales del formulario.
        document.getElementById('btnExcel').addEventListener('click', function () {
            const f = document.getElementById('filtros');
            const params = new URLSearchParams(new FormData(f));
            params.set('export', 'xls');
            window.location = 'vacaciones_reporte.php?' + params.toString();
        });
    </script>
</body>
</html>
