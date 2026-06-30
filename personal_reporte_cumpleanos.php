<?php
ob_start();

require_once 'include/cabecera.php';
require_once 'classes/Html.class.php';
require_once 'classes/Db.class.php';

// CARGAR PHPSPREADSHEET AL INICIO
require_once 'spreadsheets/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

$html = new htmlclass();
$Db = new Db();

// Meses
$meses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];

$mes_actual = (int)date('m');

// Parámetros de filtros
$filtros = [
    'codi_loca' => $_POST['codi_loca'] ?? [],
    'codi_depe' => $_POST['codi_depe'] ?? [],
    'mes' => $_POST['mes'] ?? $mes_actual,
    'tipo_export' => $_POST['tipo_export'] ?? ''
];

$es_busqueda = true; 

// Arrays de opciones
$arra_options_loca = [];
$result = $Db->query("SELECT codi_loca, nom1_loca FROM mp_admi_loca WHERE esta_loca = 1 ORDER BY nom1_loca", []);
foreach ($result as $rows) $arra_options_loca[$rows['codi_loca']] = $rows['nom1_loca'];

$arra_options_depe = [];
$result = $Db->query("SELECT codi_depe, nomb_depe, codi_loca FROM mp_admi_depe WHERE esta_depe = 1 ORDER BY nomb_depe", []);
foreach ($result as $rows) {
    $arra_options_depe[$rows['codi_depe']] = [
        'nombre' => $rows['nomb_depe'],
        'codi_loca' => $rows['codi_loca']
    ];
}

$datos = [];
$busc_tota_item = 0;

if ($es_busqueda) {
    $sql = "SELECT 
        p.iden_pers,
        p.ndoc_pers,
        CONCAT(IFNULL(p.appa_pers,''), ' ', IFNULL(p.apma_pers,''), ' ', IFNULL(p.nomb_pers,'')) AS nombres_completos,
        p.fnac_pers,
        DAY(STR_TO_DATE(p.fnac_pers, '%Y%m%d')) as dia_cumple,
        YEAR(CURDATE()) - YEAR(STR_TO_DATE(p.fnac_pers, '%Y%m%d')) AS edad,
        IFNULL(d.nomb_depe,'') AS dependencia,
        IFNULL(l.nom1_loca,'') AS sede,
        IFNULL(c.x_nombre,'') AS cargo
    FROM mp_admi_pers p
    LEFT JOIN mp_maes_cargo c ON p.iden_carg = c.n_codigo
    LEFT JOIN mp_admi_depe d ON p.iden_depe = d.codi_depe
    LEFT JOIN mp_admi_loca l ON d.codi_loca = l.codi_loca
    WHERE p.acti_pers = 1 
    AND MONTH(STR_TO_DATE(p.fnac_pers, '%Y%m%d')) = :mes";

    $params = [':mes' => $filtros['mes']];

    if (!empty($filtros['codi_loca'])) {
        $in_loca = implode(',', array_map('intval', $filtros['codi_loca']));
        $sql .= " AND d.codi_loca IN ($in_loca)";
    }
    if (!empty($filtros['codi_depe'])) {
        $in_depe = implode(',', array_map('intval', $filtros['codi_depe']));
        $sql .= " AND p.iden_depe IN ($in_depe)";
    }

    $sql .= " ORDER BY dia_cumple ASC, nombres_completos ASC";

    $datos = $Db->query($sql, $params);
    $busc_tota_item = count($datos);
}

// ==================== EXPORTAR A EXCEL XLSX ====================
if ($filtros['tipo_export'] == 'excel' && $busc_tota_item > 0) {
    ob_end_clean();
    
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Cumpleaños ' . $meses[$filtros['mes']]);
    
    // TÍTULO PRINCIPAL
    $sheet->mergeCells('A1:I1');
    $sheet->setCellValue('A1', 'MINISTERIO PÚBLICO - DISTRITO FISCAL DE AREQUIPA');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setARGB('FFFFFF');
    $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('073A6B');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getRowDimension(1)->setRowHeight(25);
    
    // SUBTÍTULO
    $sheet->mergeCells('A2:I2');
    $sheet->setCellValue('A2', 'REPORTE DE CUMPLEAÑOS - MES DE ' . strtoupper($meses[$filtros['mes']]));
    $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12)->getColor()->setARGB('FFFFFF');
    $sheet->getStyle('A2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('4A90E2');
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getRowDimension(2)->setRowHeight(20);
    
    // ENCABEZADOS
    $headers = ['N°', 'DNI', 'APELLIDOS Y NOMBRES', 'F. NACIM.', 'DÍA', 'EDAD', 'CARGO', 'DEPENDENCIA', 'SEDE'];
    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col . '4', $header);
        $col++;
    }
    
    $sheet->getStyle('A4:I4')->getFont()->setBold(true)->getColor()->setARGB('FFFFFF');
    $sheet->getStyle('A4:I4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('4A90E2');
    $sheet->getStyle('A4:I4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A4:I4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    
    // DATOS
    $row = 5;
    $cont = 1;
    foreach ($datos as $data) {
        $sheet->setCellValue('A' . $row, $cont++);
        $sheet->setCellValue('B' . $row, $data['ndoc_pers']);
        $sheet->setCellValue('C' . $row, strtoupper($data['nombres_completos']));
        $sheet->setCellValue('D' . $row, !empty($data['fnac_pers']) ? date('d/m/Y', strtotime($data['fnac_pers'])) : '-');
        $sheet->setCellValue('E' . $row, $data['dia_cumple']);
        $sheet->setCellValue('F' . $row, $data['edad']);
        $sheet->setCellValue('G' . $row, $data['cargo']);
        $sheet->setCellValue('H' . $row, $data['dependencia']);
        $sheet->setCellValue('I' . $row, $data['sede']);
        
        if ($cont % 2 == 0) {
            $sheet->getStyle('A' . $row . ':I' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F9F9F9');
        }
        
        $row++;
    }
    
    $sheet->getStyle('A4:I' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    
    // Ajustar columnas
    $sheet->getColumnDimension('A')->setWidth(5);
    $sheet->getColumnDimension('B')->setWidth(12);
    $sheet->getColumnDimension('C')->setWidth(40);
    $sheet->getColumnDimension('D')->setWidth(12);
    $sheet->getColumnDimension('E')->setWidth(8);
    $sheet->getColumnDimension('F')->setWidth(8);
    $sheet->getColumnDimension('G')->setWidth(35);
    $sheet->getColumnDimension('H')->setWidth(40);
    $sheet->getColumnDimension('I')->setWidth(25);
    
    $filename = 'reporte_cumpleanos_' . $meses[$filtros['mes']] . '_' . date('Ymd_His') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

// ==================== EXPORTAR A PDF ====================
if ($filtros['tipo_export'] == 'pdf' && $busc_tota_item > 0) {
    ob_end_clean();
    
    if (file_exists('classes/TCPDF/tcpdf.php')) {
        require_once 'classes/TCPDF/tcpdf.php';
        
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('Ministerio Público');
        $pdf->SetTitle('Reporte de Cumpleaños');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->AddPage();
        
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetFillColor(7, 58, 107);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 10, 'MINISTERIO PÚBLICO - DISTRITO FISCAL DE AREQUIPA', 0, 1, 'C', 1);
        
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetFillColor(74, 144, 226);
        $pdf->Cell(0, 8, 'REPORTE DE CUMPLEAÑOS - ' . strtoupper($meses[$filtros['mes']]), 0, 1, 'C', 1);
        
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetFillColor(227, 242, 253);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 6, 'Fecha: ' . date('d/m/Y H:i:s') . ' | Total: ' . $busc_tota_item, 0, 1, 'C', 1);
        $pdf->Ln(3);
        
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(74, 144, 226);
        $pdf->SetTextColor(255, 255, 255);
        
        $pdf->Cell(10, 7, 'N°', 1, 0, 'C', 1);
        $pdf->Cell(20, 7, 'DNI', 1, 0, 'C', 1);
        $pdf->Cell(65, 7, 'APELLIDOS Y NOMBRES', 1, 0, 'C', 1);
        $pdf->Cell(20, 7, 'F. NACIM.', 1, 0, 'C', 1);
        $pdf->Cell(10, 7, 'DÍA', 1, 0, 'C', 1);
        $pdf->Cell(10, 7, 'EDAD', 1, 0, 'C', 1);
        $pdf->Cell(50, 7, 'CARGO', 1, 0, 'C', 1);
        $pdf->Cell(50, 7, 'DEPENDENCIA', 1, 0, 'C', 1);
        $pdf->Cell(40, 7, 'SEDE', 1, 1, 'C', 1);
        
        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetTextColor(0, 0, 0);
        
        $cont = 1;
        foreach ($datos as $row) {
            $pdf->SetFillColor($cont % 2 == 0 ? 249 : 255, $cont % 2 == 0 ? 249 : 255, $cont % 2 == 0 ? 249 : 255);
            
            $pdf->Cell(10, 5, $cont++, 1, 0, 'C', 1);
            $pdf->Cell(20, 5, $row['ndoc_pers'], 1, 0, 'C', 1);
            $pdf->Cell(65, 5, substr(strtoupper($row['nombres_completos']), 0, 45), 1, 0, 'L', 1);
            $pdf->Cell(20, 5, !empty($row['fnac_pers']) ? date('d/m/Y', strtotime($row['fnac_pers'])) : '-', 1, 0, 'C', 1);
            $pdf->Cell(10, 5, $row['dia_cumple'], 1, 0, 'C', 1);
            $pdf->Cell(10, 5, $row['edad'], 1, 0, 'C', 1);
            $pdf->Cell(50, 5, substr($row['cargo'], 0, 40), 1, 0, 'L', 1);
            $pdf->Cell(50, 5, substr($row['dependencia'], 0, 40), 1, 0, 'L', 1);
            $pdf->Cell(40, 5, substr($row['sede'], 0, 30), 1, 1, 'L', 1);
        }
        
        $pdf->Output('reporte_cumpleanos_' . $meses[$filtros['mes']] . '_' . date('Ymd_His') . '.pdf', 'D');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Cumpleaños</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/modern_styles.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card-header {
            background-color: #073A6B;
            color: white;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #073A6B;
            border-color: #073A6B;
        }
        .btn-primary:hover {
            background-color: #052a4e;
            border-color: #052a4e;
        }
        .btn-success {
            background-color: #198754;
            border-color: #198754;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .table-header {
            background-color: #4A90E2;
            color: white;
        }
    </style>
    <script>
        function exportar(tipo) {
            document.getElementById('tipo_export').value = tipo;
            document.getElementById('formReporte').submit();
        }
    </script>
</head>
<body>

<div class="container-fluid pt-4">
    <h3 class="text-center mb-4" style="color:#073A6B;"><b>REPORTE DE CUMPLEAÑOS</b></h3>

    <form id="formReporte" method="post">
        <input type="hidden" id="tipo_export" name="tipo_export" value="">

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-filter me-2"></i>Criterios de Búsqueda
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="mes" class="form-label">Mes</label>
                        <select id="mes" name="mes" class="form-select">
                            <?php foreach($meses as $k => $v) echo "<option value=\"$k\" ".($filtros['mes'] == $k ? 'selected' : '').">$v</option>"; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="codi_depe" class="form-label">Dependencia</label>
                        <select id="codi_depe" name="codi_depe[]" class="form-select select2" multiple>
                            <?php foreach($arra_options_depe as $k => $v) echo "<option value=\"$k\" data-codi-loca=\"{$v['codi_loca']}\" ".(in_array($k, $filtros['codi_depe']) ? 'selected' : '').">{$v['nombre']}</option>"; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="codi_loca" class="form-label">Local</label>
                        <select id="codi_loca" name="codi_loca[]" class="form-select select2" multiple>
                            <?php foreach($arra_options_loca as $k => $v) echo "<option value=\"$k\" ".(in_array($k, $filtros['codi_loca']) ? 'selected' : '').">$v</option>"; ?>
                        </select>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-search me-2"></i>BUSCAR</button>
                    <button type="button" class="btn btn-secondary btn-lg" onclick="window.location.href=window.location.pathname;"><i class="fas fa-sync-alt me-2"></i>LIMPIAR</button>
                </div>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-birthday-cake me-2"></i>Resultados - <?= $meses[$filtros['mes']] ?> <span class="badge bg-light text-dark ms-2"><?= $busc_tota_item ?> encontrados</span>
        </div>
        <div class="card-body">
            <?php if ($busc_tota_item > 0): ?>
                <div class="text-center mb-4">
                    <button type="button" class="btn btn-success" onclick="exportar('excel')"><i class="fas fa-file-excel me-2"></i>Exportar a Excel</button>
                    <button type="button" class="btn btn-danger" onclick="exportar('pdf')"><i class="fas fa-file-pdf me-2"></i>Exportar a PDF</button>
                    <button type="button" class="btn btn-info text-white" onclick="window.print()"><i class="fas fa-print me-2"></i>Imprimir</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-header">
                            <tr>
                                <th>N°</th>
                                <th>DNI</th>
                                <th>APELLIDOS Y NOMBRES</th>
                                <th>F. NACIMIENTO</th>
                                <th>DÍA</th>
                                <th>EDAD</th>
                                <th>CARGO</th>
                                <th>DEPENDENCIA</th>
                                <th>SEDE</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $cont = 1; foreach ($datos as $row): ?>
                            <tr>
                                <td><?= $cont++ ?></td>
                                <td><?= htmlspecialchars($row['ndoc_pers']) ?></td>
                                <td><?= htmlspecialchars(strtoupper($row['nombres_completos'])) ?></td>
                                <td><?= !empty($row['fnac_pers']) ? date('d/m/Y', strtotime($row['fnac_pers'])) : '-' ?></td>
                                <td class="text-center fw-bold"><?= htmlspecialchars($row['dia_cumple']) ?></td>
                                <td><?= htmlspecialchars($row['edad']) ?></td>
                                <td><?= htmlspecialchars($row['cargo']) ?></td>
                                <td><?= htmlspecialchars($row['dependencia']) ?></td>
                                <td><?= htmlspecialchars($row['sede']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>No se encontraron cumpleañeros para el mes seleccionado con los filtros actuales.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            placeholder: 'Seleccione...',
            allowClear: true
        });

        $('#codi_depe').on('change', function() {
            var selectedDeps = $(this).val();
            var $localSelect = $('#codi_loca');
            
            if (selectedDeps && selectedDeps.length > 0) {
                var allowedLocals = [];
                
                // Collect allowed locals from selected dependencies
                $('#codi_depe option:selected').each(function() {
                    var loca = $(this).data('codi-loca');
                    if (loca) allowedLocals.push(String(loca));
                });
                
                // Filter Local options
                $localSelect.find('option').each(function() {
                    var val = $(this).val();
                    if (allowedLocals.includes(val)) {
                        $(this).prop('disabled', false);
                    } else {
                        $(this).prop('disabled', true);
                        $(this).prop('selected', false); // Deselect if hidden
                    }
                });
            } else {
                // Enable all if no dependency selected
                $localSelect.find('option').prop('disabled', false);
            }
            
            // Refresh Select2 to show changes
            $localSelect.trigger('change.select2');
        });
        
        // Trigger on load to apply initial state
        $('#codi_depe').trigger('change');
    });
</script>
</body>
</html>
