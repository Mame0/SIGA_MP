<?php
ob_start();

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

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

// Parámetros de filtros
$filtros = [
    'codi_loca' => $_POST['codi_loca'] ?? [],
    'codi_depe' => $_POST['codi_depe'] ?? [],
    'codi_regi' => $_POST['codi_regi'] ?? [],
    'codi_carg' => $_POST['codi_carg'] ?? [],
    'codi_sexo' => $_POST['codi_sexo'] ?? '',
    'codi_hijo' => $_POST['codi_hijo'] ?? '',
    'edad_desd' => $_POST['edad_desd'] ?? '',
    'edad_hast' => $_POST['edad_hast'] ?? '',
    'codi_sind' => $_POST['codi_sind'] ?? '',
    'codi_moda' => $_POST['codi_moda'] ?? [],
    'codi_presu' => $_POST['codi_presu'] ?? '',
    'codi_conad' => $_POST['codi_conad'] ?? '',
    'tipo_export' => $_POST['tipo_export'] ?? '',
    'reporte_tipo' => $_POST['reporte_tipo'] ?? 'ninguno'
];

$es_busqueda = $_SERVER['REQUEST_METHOD'] === 'POST' && empty($filtros['tipo_export']);

// Arrays de opciones
$arra_options_hijo = ['' => '-- Ambos --', '1' => 'Si tiene hijos', '2' => 'No tiene hijos'];
$arra_options_sindi = ['' => '-- Ambos --', '1' => 'Si es Sindicalizado', '2' => 'No es Sindicalizado'];
$arra_options_conad = ['' => '-- Ambos --', '1' => 'Si tiene Conadis', '2' => 'No tiene Conadis'];
$arra_options_presu = ['' => '-- Ambos --', '1' => 'Arequipa', '2' => 'Lima'];
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

$arra_options_regi = [];
$result = $Db->query("SELECT n_codigo, x_nombre FROM mp_maes_regimen_laboral WHERE n_estado = 1", []);
foreach ($result as $rows) $arra_options_regi[$rows['n_codigo']] = $rows['x_nombre'];

$arra_options_carg = [];
$result = $Db->query("SELECT n_codigo, x_nombre FROM mp_maes_cargo WHERE n_estado = 1 ORDER BY x_nombre", []);
foreach ($result as $rows) $arra_options_carg[$rows['n_codigo']] = $rows['x_nombre'];

$arra_options_sexo = ['' => '-- Ambos --'];
$result = $Db->query("SELECT n_codigo, x_nombre FROM mp_maes_sexo WHERE n_estado = 1", []);
foreach ($result as $rows) $arra_options_sexo[$rows['n_codigo']] = $rows['x_nombre'];

$arra_options_moda = [];
$result = $Db->query("SELECT n_codigo, x_nombre FROM mp_maes_modalidad_trabajo WHERE n_estado = 1", []);
foreach ($result as $rows) $arra_options_moda[$rows['n_codigo']] = $rows['x_nombre'];

$datos = [];
$busc_tota_item = 0;

if ($es_busqueda || !empty($filtros['tipo_export'])) {
    $sql = "SELECT 
        p.iden_pers,
        p.ndoc_pers,
        CONCAT(IFNULL(p.appa_pers,''), ' ', IFNULL(p.apma_pers,'')) AS apellidos,
        IFNULL(p.nomb_pers,'') AS nombres,
        IFNULL(s.x_nombre,'') AS sexo,
        p.fnac_pers,
        YEAR(CURDATE()) - YEAR(STR_TO_DATE(p.fnac_pers, '%Y%m%d')) AS edad,
        IFNULL(ec.x_nombre,'') AS estado_civil,
        (SELECT COUNT(*) FROM mp_admi_pers_fami pf 
          WHERE pf.iden_pers = p.iden_pers 
          AND pf.esta_fami = 1 
          AND pf.iden_tipo = 3) AS num_hijos,
        IFNULL(d.nomb_depe,'') AS dependencia,
        IFNULL(l.nom1_loca,'') AS sede,
        IFNULL(r.x_nombre,'') AS regimen,
        IFNULL(c.x_nombre,'') AS cargo,
        p.fing_pers,
        p.esta_pers,
        IF(p.iden_sind = 1, 'Sí', 'No') AS sindicalizado,
        IFNULL(moda.x_nombre, '') AS modalidad_trabajo,
        CASE p.iden_pres WHEN 1 THEN 'Arequipa' WHEN 2 THEN 'Lima' ELSE '' END AS presupuesto
    FROM mp_admi_pers p
    LEFT JOIN mp_maes_sexo s ON p.iden_sexo = s.n_codigo
    LEFT JOIN mp_maes_cargo c ON p.iden_carg = c.n_codigo
    LEFT JOIN mp_maes_regimen_laboral r ON p.iden_rlab = r.n_codigo
    LEFT JOIN mp_admi_depe d ON p.iden_depe = d.codi_depe
    LEFT JOIN mp_admi_loca l ON d.codi_loca = l.codi_loca
    LEFT JOIN mp_maes_estado_civil ec ON p.iden_eciv = ec.n_codigo
    LEFT JOIN mp_maes_modalidad_trabajo moda ON p.iden_modtrab = moda.n_codigo
    WHERE p.acti_pers = 1";

    $params = [];

    if (!empty($filtros['codi_loca'])) {
        $in_loca = implode(',', array_map('intval', $filtros['codi_loca']));
        $sql .= " AND d.codi_loca IN ($in_loca)";
    }
    if (!empty($filtros['codi_depe'])) {
        $in_depe = implode(',', array_map('intval', $filtros['codi_depe']));
        $sql .= " AND p.iden_depe IN ($in_depe)";
    }
    if (!empty($filtros['codi_regi'])) {
        $in_regi = implode(',', array_map('intval', $filtros['codi_regi']));
        $sql .= " AND p.iden_rlab IN ($in_regi)";
    }
    if (!empty($filtros['codi_carg'])) {
        $in_carg = implode(',', array_map('intval', $filtros['codi_carg']));
        $sql .= " AND p.iden_carg IN ($in_carg)";
    }
    if (!empty($filtros['codi_sexo'])) {
        $sql .= " AND p.iden_sexo = :codi_sexo";
        $params[':codi_sexo'] = $filtros['codi_sexo'];
    }
    if (!empty($filtros['edad_desd']) && is_numeric($filtros['edad_desd'])) {
        $sql .= " AND YEAR(CURDATE()) - YEAR(STR_TO_DATE(p.fnac_pers, '%Y%m%d')) >= :edad_desd";
        $params[':edad_desd'] = $filtros['edad_desd'];
    }
    if (!empty($filtros['edad_hast']) && is_numeric($filtros['edad_hast'])) {
        $sql .= " AND YEAR(CURDATE()) - YEAR(STR_TO_DATE(p.fnac_pers, '%Y%m%d')) <= :edad_hast";
        $params[':edad_hast'] = $filtros['edad_hast'];
    }
    if ($filtros['codi_sind'] == '1') {
        $sql .= " AND p.iden_sind = 1";
    } elseif ($filtros['codi_sind'] == '2') {
        $sql .= " AND (p.iden_sind = 0 OR p.iden_sind IS NULL)";
    }
    if ($filtros['codi_conad'] == '1') {
        $sql .= " AND p.cona_pers = 1";
    } elseif ($filtros['codi_conad'] == '2') {
        $sql .= " AND (p.cona_pers = 0 OR p.cona_pers IS NULL)";
    }
    if (!empty($filtros['codi_presu']) && is_numeric($filtros['codi_presu'])) {
        $sql .= " AND p.iden_pres = :codi_presu";
        $params[':codi_presu'] = $filtros['codi_presu'];
    }
    if (!empty($filtros['codi_moda'])) {
        $in_moda = implode(',', array_map('intval', $filtros['codi_moda']));
        $sql .= " AND p.iden_modtrab IN ($in_moda)";
    }

    // FILTRO DE HIJOS
    if ($filtros['codi_hijo'] == '1') {
        $sql .= " HAVING num_hijos > 0";
    } elseif ($filtros['codi_hijo'] == '2') {
        $sql .= " HAVING num_hijos = 0";
    }

    $sql .= " ORDER BY p.appa_pers, p.apma_pers, p.nomb_pers LIMIT 2000";

    $datos = $Db->query($sql, $params);
    $busc_tota_item = count($datos);
}

// ==================== EXPORTAR A EXCEL XLSX ====================
if ($filtros['tipo_export'] == 'excel' && $busc_tota_item > 0) {
    ob_end_clean();
    
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Reporte Personal');
    
    // TÍTULO PRINCIPAL
    $sheet->mergeCells('A1:M1');
    $sheet->setCellValue('A1', 'MINISTERIO PÚBLICO - DISTRITO FISCAL DE AREQUIPA');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setARGB('FFFFFF');
    $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('073A6B');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getRowDimension(1)->setRowHeight(25);
    
    // SUBTÍTULO
    $sheet->mergeCells('A2:M2');
    $sheet->setCellValue('A2', 'REPORTE GENERAL DE PERSONAL');
    $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12)->getColor()->setARGB('FFFFFF');
    $sheet->getStyle('A2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('4A90E2');
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getRowDimension(2)->setRowHeight(20);
    
    // INFORMACIÓN DEL REPORTE
    $sheet->mergeCells('A3:M3');
    $sheet->setCellValue('A3', 'Fecha de generación: ' . date('d/m/Y H:i:s') . ' | Total de registros: ' . $busc_tota_item);
    $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(10);
    $sheet->getStyle('A3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('E3F2FD');
    $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
    // ENCABEZADOS
    $headers = ['N°', 'DNI', 'APELLIDOS', 'NOMBRES', 'SEXO', 'F. NACIM.', 'EDAD', 'CARGO', 'DEPENDENCIA', 'SEDE', 'RÉGIMEN', 'EST. CIVIL', 'F. INGRESO'];
    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col . '4', $header);
        $col++;
    }
    
    $sheet->getStyle('A4:M4')->getFont()->setBold(true)->getColor()->setARGB('FFFFFF');
    $sheet->getStyle('A4:M4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('4A90E2');
    $sheet->getStyle('A4:M4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A4:M4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    
    // DATOS
    $row = 5;
    $cont = 1;
    foreach ($datos as $data) {
        $sheet->setCellValue('A' . $row, $cont++);
        $sheet->setCellValue('B' . $row, $data['ndoc_pers']);
        $sheet->setCellValue('C' . $row, strtoupper($data['apellidos']));
        $sheet->setCellValue('D' . $row, strtoupper($data['nombres']));
        $sheet->setCellValue('E' . $row, $data['sexo']);
        $sheet->setCellValue('F' . $row, !empty($data['fnac_pers']) ? date('d/m/Y', strtotime($data['fnac_pers'])) : '-');
        $sheet->setCellValue('G' . $row, $data['edad']);
        $sheet->setCellValue('H' . $row, $data['cargo']);
        $sheet->setCellValue('I' . $row, $data['dependencia']);
        $sheet->setCellValue('J' . $row, $data['sede']);
        $sheet->setCellValue('K' . $row, $data['regimen']);
        $sheet->setCellValue('L' . $row, $data['estado_civil']);
        $sheet->setCellValue('M' . $row, !empty($data['fing_pers']) ? date('d/m/Y', strtotime($data['fing_pers'])) : '-');
        
        if ($cont % 2 == 0) {
            $sheet->getStyle('A' . $row . ':M' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F9F9F9');
        }
        
        $row++;
    }
    
    $sheet->getStyle('A4:M' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    
    // Ajustar columnas
    $sheet->getColumnDimension('A')->setWidth(5);
    $sheet->getColumnDimension('B')->setWidth(12);
    $sheet->getColumnDimension('C')->setWidth(25);
    $sheet->getColumnDimension('D')->setWidth(25);
    $sheet->getColumnDimension('E')->setWidth(10);
    $sheet->getColumnDimension('F')->setWidth(12);
    $sheet->getColumnDimension('G')->setWidth(8);
    $sheet->getColumnDimension('H')->setWidth(35);
    $sheet->getColumnDimension('I')->setWidth(40);
    $sheet->getColumnDimension('J')->setWidth(25);
    $sheet->getColumnDimension('K')->setWidth(25);
    $sheet->getColumnDimension('L')->setWidth(15);
    $sheet->getColumnDimension('M')->setWidth(12);
    
    $filename = 'reporte_personal_' . date('Ymd_His') . '.xlsx';
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

        // --- INICIO: GENERAR Y OBTENER GRÁFICO ---
        $chart_image_data = null;
        if(!empty($datos) && $filtros['reporte_tipo'] != 'ninguno'){
            $filtros_grafico = $filtros;
            $filtros_grafico['reporte_tipo'] = $_POST['reporte_tipo'] ?? 'ninguno';
            $chart_params = http_build_query($filtros_grafico);
            $chart_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/personal_reporte_grafico.php?' . $chart_params;
            $chart_image_data = @file_get_contents($chart_url);
        }
        // --- FIN: GENERAR Y OBTENER GRÁFICO ---
        
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('Ministerio Público');
        $pdf->SetTitle('Reporte General de Personal');
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
        $pdf->Cell(0, 8, 'REPORTE GENERAL DE PERSONAL', 0, 1, 'C', 1);
        
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetFillColor(227, 242, 253);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 6, 'Fecha: ' . date('d/m/Y H:i:s') . ' | Total: ' . $busc_tota_item, 0, 1, 'C', 1);
        $pdf->Ln(3);

        // --- INICIO: EMBEBER GRÁFICO EN PDF ---
        if ($chart_image_data) {
            // Centrar la imagen en la página
            $pdf->Image('@' . $chart_image_data, 60, 50, 180, 87, '', '', '', true, 150, 'C', false, false, 0, false, false, false);
        }
        // --- FIN: EMBEBER GRÁFICO EN PDF ---

        // Iniciar la tabla en una nueva página
        $pdf->AddPage();
        
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->SetFillColor(74, 144, 226);
        $pdf->SetTextColor(255, 255, 255);
        
        $pdf->Cell(8, 7, 'N°', 1, 0, 'C', 1);
        $pdf->Cell(20, 7, 'DNI', 1, 0, 'C', 1);
        $pdf->Cell(40, 7, 'APELLIDOS', 1, 0, 'C', 1);
        $pdf->Cell(35, 7, 'NOMBRES', 1, 0, 'C', 1);
        $pdf->Cell(12, 7, 'SEXO', 1, 0, 'C', 1);
        $pdf->Cell(18, 7, 'F. NACIM.', 1, 0, 'C', 1);
        $pdf->Cell(10, 7, 'EDAD', 1, 0, 'C', 1);
        $pdf->Cell(45, 7, 'CARGO', 1, 0, 'C', 1);
        $pdf->Cell(50, 7, 'DEPENDENCIA', 1, 0, 'C', 1);
        $pdf->Cell(30, 7, 'RÉGIMEN', 1, 1, 'C', 1);
        
        $pdf->SetFont('helvetica', '', 6);
        $pdf->SetTextColor(0, 0, 0);
        
        $cont = 1;
        foreach ($datos as $row) {
            $pdf->SetFillColor($cont % 2 == 0 ? 249 : 255, $cont % 2 == 0 ? 249 : 255, $cont % 2 == 0 ? 249 : 255);
            
            $pdf->Cell(8, 5, $cont++, 1, 0, 'C', 1);
            $pdf->Cell(20, 5, $row['ndoc_pers'], 1, 0, 'C', 1);
            $pdf->Cell(40, 5, substr(strtoupper($row['apellidos']), 0, 30), 1, 0, 'L', 1);
            $pdf->Cell(35, 5, substr(strtoupper($row['nombres']), 0, 25), 1, 0, 'L', 1);
            $pdf->Cell(12, 5, $row['sexo'], 1, 0, 'C', 1);
            $pdf->Cell(18, 5, !empty($row['fnac_pers']) ? date('d/m/Y', strtotime($row['fnac_pers'])) : '-', 1, 0, 'C', 1);
            $pdf->Cell(10, 5, $row['edad'], 1, 0, 'C', 1);
            $pdf->Cell(45, 5, substr($row['cargo'], 0, 35), 1, 0, 'L', 1);
            $pdf->Cell(50, 5, substr($row['dependencia'], 0, 38), 1, 0, 'L', 1);
            $pdf->Cell(30, 5, substr($row['regimen'], 0, 22), 1, 1, 'L', 1);
        }
        
        $pdf->Output('reporte_personal_' . date('Ymd_His') . '.pdf', 'D');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte General de Personal</title>
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
    <h3 class="text-center mb-4" style="color:#073A6B;"><b>REPORTE GENERAL DE PERSONAL</b></h3>

    <form id="formReporte" method="post">
        <input type="hidden" id="tipo_export" name="tipo_export" value="">

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-filter me-2"></i>Criterios de Búsqueda
            </div>
            <div class="card-body">
                <div class="row g-3">
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
                    <div class="col-md-4">
                        <label for="codi_regi" class="form-label">Régimen</label>
                        <select id="codi_regi" name="codi_regi[]" class="form-select select2" multiple>
                            <?php foreach($arra_options_regi as $k => $v) echo "<option value=\"$k\" ".(in_array($k, $filtros['codi_regi']) ? 'selected' : '').">$v</option>"; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="codi_carg" class="form-label">Cargo</label>
                        <select id="codi_carg" name="codi_carg[]" class="form-select select2" multiple>
                            <?php foreach($arra_options_carg as $k => $v) echo "<option value=\"$k\" ".(in_array($k, $filtros['codi_carg']) ? 'selected' : '').">$v</option>"; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="codi_sexo" class="form-label">Sexo</label>
                        <select id="codi_sexo" name="codi_sexo" class="form-select">
                            <?php foreach($arra_options_sexo as $k => $v) echo "<option value=\"$k\" ".($filtros['codi_sexo'] == $k ? 'selected' : '').">$v</option>"; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="codi_hijo" class="form-label">Tiene Hijos</label>
                        <select id="codi_hijo" name="codi_hijo" class="form-select">
                            <?php foreach($arra_options_hijo as $k => $v) echo "<option value=\"$k\" ".($filtros['codi_hijo'] == $k ? 'selected' : '').">$v</option>"; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="edad_desd" class="form-label">Edad (Desde)</label>
                        <input type="number" id="edad_desd" name="edad_desd" class="form-control" value="<?= $filtros['edad_desd'] ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="edad_hast" class="form-label">Edad (Hasta)</label>
                        <input type="number" id="edad_hast" name="edad_hast" class="form-control" value="<?= $filtros['edad_hast'] ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="codi_sind" class="form-label">Sindicalizado</label>
                        <select id="codi_sind" name="codi_sind" class="form-select">
                            <?php foreach($arra_options_sindi as $k => $v) echo "<option value=\"$k\" ".($filtros['codi_sind'] == $k ? 'selected' : '').">$v</option>"; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="codi_moda" class="form-label">Modalidad Trabajo</label>
                        <select id="codi_moda" name="codi_moda[]" class="form-select select2" multiple>
                            <?php foreach($arra_options_moda as $k => $v) echo "<option value=\"$k\" ".(in_array($k, $filtros['codi_moda']) ? 'selected' : '').">$v</option>"; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="codi_presu" class="form-label">Presupuesto</label>
                        <select id="codi_presu" name="codi_presu" class="form-select">
                            <?php foreach($arra_options_presu as $k => $v) echo "<option value=\"$k\" ".($filtros['codi_presu'] == $k ? 'selected' : '').">$v</option>"; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="codi_conad" class="form-label">Conadis</label>
                        <select id="codi_conad" name="codi_conad" class="form-select">
                            <?php foreach($arra_options_conad as $k => $v) echo "<option value=\"$k\" ".($filtros['codi_conad'] == $k ? 'selected' : '').">$v</option>"; ?>
                        </select>
                    </div>
                     <div class="col-md-4">
                        <label for="reporte_tipo" class="form-label">Tipo de Gráfico</label>
                        <select id="reporte_tipo" name="reporte_tipo" class="form-select">
                             <?php
                                $arra_options_grafico = [
                                    'sexo' => 'Distribución por Sexo', 
                                    'regimen' => 'Distribución por Régimen Laboral',
                                    'modalidad' => 'Distribución por Modalidad de Trabajo',
                                    'cargo' => 'Distribución por Cargo',
                                    'ninguno' => 'Ninguno'
                                ];
                                $reporte_tipo_seleccionado = $_POST['reporte_tipo'] ?? 'ninguno';
                                foreach($arra_options_grafico as $k => $v) echo "<option value=\"$k\" ".($reporte_tipo_seleccionado == $k ? 'selected' : '').">$v</option>";
                            ?>
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

    <?php if ($es_busqueda || !empty($filtros['tipo_export'])): ?>
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-bar me-2"></i>Resultados de la Búsqueda <span class="badge bg-light text-dark ms-2"><?= $busc_tota_item ?> encontrados</span>
            </div>
            <div class="card-body">
                <?php if ($busc_tota_item > 0): ?>
                    <div class="text-center mb-4">
                        <button type="button" class="btn btn-success" onclick="exportar('excel')"><i class="fas fa-file-excel me-2"></i>Exportar a Excel</button>
                        <button type="button" class="btn btn-danger" onclick="exportar('pdf')"><i class="fas fa-file-pdf me-2"></i>Exportar a PDF</button>
                        <button type="button" class="btn btn-info text-white" onclick="window.print()"><i class="fas fa-print me-2"></i>Imprimir</button>
                    </div>

                    <div class="text-center my-4">
                        <?php
                        $reporte_tipo = $_POST['reporte_tipo'] ?? 'ninguno';
                        if ($reporte_tipo != 'ninguno'):
                            $titulo_grafico = match($reporte_tipo) {
                                'regimen' => 'Distribución por Régimen Laboral',
                                'modalidad' => 'Distribución por Modalidad de Trabajo',
                                'cargo' => 'Distribución por Cargo',
                                default => 'Distribución por Sexo'
                            };
                            $filtros_grafico = $filtros;
                            $filtros_grafico['reporte_tipo'] = $reporte_tipo;
                            $chart_params = http_build_query($filtros_grafico);
                        ?>
                        <h4 class="mb-3" style="color:#073A6B;">Gráfico de <?= $titulo_grafico ?></h4>
                        <img src="personal_reporte_grafico.php?<?= $chart_params ?>" class="img-fluid border rounded" alt="Gráfico de <?= $titulo_grafico ?>">
                        <?php endif; ?>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-header">
                                <tr>
                                    <th>N°</th><th>DNI</th><th>APELLIDOS</th><th>NOMBRES</th><th>SEXO</th><th>F. NACIMIENTO</th><th>EDAD</th><th>N° HIJOS</th><th>CARGO</th><th>DEPENDENCIA</th><th>SEDE</th><th>RÉGIMEN</th><th>EST.CIVIL</th><th>SINDICALIZADO</th><th>MOD. TRABAJO</th><th>PRESUPUESTO</th><th>F.INGRESO</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $cont = 1; foreach ($datos as $row): ?>
                                <tr>
                                    <td><?= $cont++ ?></td>
                                    <td><?= htmlspecialchars($row['ndoc_pers']) ?></td>
                                    <td><?= htmlspecialchars(strtoupper($row['apellidos'])) ?></td>
                                    <td><?= htmlspecialchars(strtoupper($row['nombres'])) ?></td>
                                    <td><?= htmlspecialchars($row['sexo']) ?></td>
                                    <td><?= !empty($row['fnac_pers']) ? date('d/m/Y', strtotime($row['fnac_pers'])) : '-' ?></td>
                                    <td><?= htmlspecialchars($row['edad']) ?></td>
                                    <td><?= htmlspecialchars($row['num_hijos']) ?></td>
                                    <td><?= htmlspecialchars($row['cargo']) ?></td>
                                    <td><?= htmlspecialchars($row['dependencia']) ?></td>
                                    <td><?= htmlspecialchars($row['sede']) ?></td>
                                    <td><?= htmlspecialchars($row['regimen']) ?></td>
                                    <td><?= htmlspecialchars($row['estado_civil']) ?></td>
                                    <td><?= htmlspecialchars($row['sindicalizado']) ?></td>
                                    <td><?= htmlspecialchars($row['modalidad_trabajo']) ?></td>
                                    <td><?= htmlspecialchars($row['presupuesto']) ?></td>
                                    <td><?= !empty($row['fing_pers']) ? date('d/m/Y', strtotime($row['fing_pers'])) : '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>No se encontraron registros con los criterios de búsqueda seleccionados.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
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