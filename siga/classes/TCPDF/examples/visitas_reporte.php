<?php
session_start();
date_default_timezone_set('America/Lima');

// Include the main TCPDF library
require_once('tcpdf_include.php');
require_once '../../Db.class.php';

class MYPDF extends TCPDF {
    public function Header() {}
    public function Footer() {}
}

// Crear documento PDF — A4 horizontal
$pdf = new MYPDF("L", PDF_UNIT, "A4", true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Márgenes compactos para maximizar espacio
$margin_left   = 8;
$margin_right  = 8;
$margin_top    = 8;
$margin_bottom = 6;
$pdf->SetMargins($margin_left, $margin_top, $margin_right);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(5);
$pdf->SetAutoPageBreak(FALSE, $margin_bottom);
$pdf->setImageScale(1.53);

if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------
$Db = new Db();

// Nombre de la local
$nomb_loca = '';
$result = $Db->query("SELECT nom1_loca FROM mp_admi_loca WHERE codi_loca='".$_POST['iden_loca']."'");
foreach ($result as $rows)
    $nomb_loca = $rows['nom1_loca'];

// Tipos de visita
$arra_options_tvis = [];
$result = $Db->select('mp_maes_visi_tipo', '', '', '', ['x_nombre' => 'ASC']);
foreach ($result as $rows)
    $arra_options_tvis[$rows['n_codigo']] = $rows['x_nombre'];

// Siglas y nombres de dependencias
$arra_options_depe_sigl = [];
$arra_options_depe_nomb = [];
$result = $Db->query("SELECT codi_depe, sigl_depe, nomb_depe FROM mp_admi_depe WHERE codi_loca='".$_POST['iden_loca']."' AND esta_depe=1 ORDER BY nomb_depe");
foreach ($result as $rows) {
    $arra_options_depe_sigl[$rows['codi_depe']] = $rows['sigl_depe'];
    $arra_options_depe_nomb[$rows['codi_depe']] = $rows['nomb_depe'];
}

// Personal (para campo AUTORIZA)
$arra_options_pers = [];
$result = $Db->query("SELECT iden_pers, appa_pers, apma_pers, nomb_pers FROM mp_admi_pers");
foreach ($result as $rows)
    $arra_options_pers[$rows['iden_pers']] = strtoupper($rows['appa_pers'].' '.$rows['apma_pers'].', '.$rows['nomb_pers']);

$busc_pala_sql = "";
if(isset($_POST['busq_pala']) && trim($_POST['busq_pala']) != '') {
    $pala = trim($_POST['busq_pala']);
    $busc_pala_sql = " AND (ndoc_visi LIKE '%$pala%' OR nomb_visi LIKE '%$pala%' OR appa_visi LIKE '%$pala%' OR apma_visi LIKE '%$pala%')";
}

// Total de registros
$result = $Db->query("SELECT COUNT(*) canti FROM mp_visi_registro WHERE iden_loca='".$_POST['iden_loca']."' AND esta_visi>0 AND fech_visi>='".$_POST['fech_desd']."' AND fech_visi<='".$_POST['fech_hast']."'".$busc_pala_sql);
foreach ($result as $rows)
    $tota_resu = $rows['canti'];

// Registros por página (fila 4.5mm, espacio usable permite más de 35 filas)
$regs_por_pagi = 37;
$pagi_total = ceil($tota_resu / $regs_por_pagi);
if ($pagi_total < 1) $pagi_total = 1;

// Fechas formateadas
$fecha_desd_fmt = substr($_POST['fech_desd'],8,2).'/'.substr($_POST['fech_desd'],5,2).'/'.substr($_POST['fech_desd'],0,4);
$fecha_hast_fmt = substr($_POST['fech_hast'],8,2).'/'.substr($_POST['fech_hast'],5,2).'/'.substr($_POST['fech_hast'],0,4);

// Ancho total útil = 297 - 8 - 8 = 281mm
// Columnas: N°(7) + DOC(25) + NOMBRE(58) + TIPO(20) + FECHA(16) + INGR(14) + SAL(14) + DESTINO(62) + AUTORIZA(65) = 281
$col = [
    'num'     =>  7,
    'doc'     => 25,
    'nombre'  => 58,
    'tipo'    => 20,
    'fecha'   => 16,
    'ingreso' => 14,
    'salida'  => 14,
    'destino' => 62,
    'autoriza'=> 65,
];
$ancho_total = array_sum($col); // 281

// ---------------------------------------------------------
// Función encabezado
function poner_cabecera($pdf, $nomb_loca, $fecha_desd_fmt, $fecha_hast_fmt, $pagi_actu, $pagi_tota, $col, $ancho_total)
{
    // ── LOGO alargado horizontalmente ──────────────────────
    $imag_fron = 'images/cabecera_mpartes.jpg';
    $pdf->Image($imag_fron, 8, 5, 115, 20, 'JPEG', '', 'M', false, 300, '', false, false, 0, false, false, false);

    // Fecha, hora y página — esquina superior derecha (texto más grande y más abajo)
    $pdf->SetFont('arialn', '', 8);
    $pdf->SetXY(245, 7);
    $pdf->Cell(35, 4, "FECHA : ".date("d/m/Y"), 0, 1, 'R');
    $pdf->SetXY(245, 11);
    $pdf->Cell(35, 4, "HORA  : ".date("H:i:s"), 0, 1, 'R');
    $pdf->SetXY(245, 15);
    $pdf->Cell(35, 4, "PÁG.  : $pagi_actu de $pagi_tota", 0, 1, 'R');

    // Títulos centrados usando todo el ancho de la hoja A4 (297mm)
    $pdf->SetFont('arialn', '', 8);
    $pdf->SetXY(0, 8);
    $pdf->Cell(297, 4, "SISTEMA INTEGRADO DE GESTIÓN ADMINISTRATIVA - SIGA", 0, 1, 'C');

    $pdf->SetFont('arialn', 'B', 11);
    $pdf->SetXY(0, 13);
    $pdf->Cell(297, 5, "REPORTE DE REGISTRO DE VISITAS", 0, 1, 'C');

    $pdf->SetFont('arialn', '', 8);
    $pdf->SetXY(0, 25);
    $pdf->Cell(297, 3, strtoupper($nomb_loca)."   (Desde: $fecha_desd_fmt  hasta: $fecha_hast_fmt)", 0, 1, 'C');

    // Cabecera de tabla
    $pdf->SetFillColor(55, 99, 150);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('arialn', 'B', 7);
    $pdf->SetXY(8, 29);

    $pdf->Cell($col['num'],      6, "N°",              1, 0, 'C', 1);
    $pdf->Cell($col['doc'],      6, "N° DOCUMENTO",    1, 0, 'C', 1);
    $pdf->Cell($col['nombre'],   6, "APELLIDOS Y NOMBRES", 1, 0, 'C', 1);
    $pdf->Cell($col['tipo'],     6, "TIPO",            1, 0, 'C', 1);
    $pdf->Cell($col['fecha'],    6, "FECHA",           1, 0, 'C', 1);
    $pdf->Cell($col['ingreso'],  6, "INGRESO",         1, 0, 'C', 1);
    $pdf->Cell($col['salida'],   6, "SALIDA",          1, 0, 'C', 1);
    $pdf->Cell($col['destino'],  6, "DESTINO",         1, 0, 'C', 1);
    $pdf->Cell($col['autoriza'], 6, "AUTORIZA",        1, 1, 'C', 1);

    // Restaurar colores
    $pdf->SetTextColor(30, 30, 30);
    $pdf->SetFillColor(255, 255, 255);
}

// ---------------------------------------------------------
// Primera página
$pdf->AddPage();
poner_cabecera($pdf, $nomb_loca, $fecha_desd_fmt, $fecha_hast_fmt, 1, $pagi_total, $col, $ancho_total);

$pdf->SetFont('arialn', '', 6.5);
$pdf->SetXY(8, 35); // justo después de la cabecera de tabla

$alto      = 4.5; // alto de fila compacto (4.5mm → ~35 filas por página)
$cont      = 0;
$cont_pagi = 0;
$pagi_actu = 1;
$fill      = false;

$result = $Db->query("SELECT * FROM mp_visi_registro WHERE iden_loca='".$_POST['iden_loca']."' AND esta_visi>0 AND fech_visi>='".$_POST['fech_desd']."' AND fech_visi<='".$_POST['fech_hast']."'".$busc_pala_sql." ORDER BY fdig_visi ASC");

foreach ($result as $rows) {
    $cont++;
    $cont_pagi++;

    if ($cont_pagi > $regs_por_pagi) {
        $pdf->AddPage();
        $pagi_actu++;
        poner_cabecera($pdf, $nomb_loca, $fecha_desd_fmt, $fecha_hast_fmt, $pagi_actu, $pagi_total, $col, $ancho_total);
        $pdf->SetXY(8, 35);
        $cont_pagi = 1;
        $fill = false;
    }

    // Color de fila alternado
    if ($fill) $pdf->SetFillColor(235, 242, 250);
    else        $pdf->SetFillColor(255, 255, 255);

    // Fecha formateada
    $fech_fmt = str_replace('-', '/', $rows['fech_visi']);

    // Hora ingreso/salida
    $ingr = $rows['ingr_visi'] ? $rows['ingr_visi'] : '--:--';
    $sali = ($rows['sali_visi'] && $rows['sali_visi'] != '00:00:00') ? $rows['sali_visi'] : '--:--';

    // Nombre completo
    $nombre = strtoupper(trim($rows['appa_visi'].' '.$rows['apma_visi'].', '.$rows['nomb_visi']));

    // Destino
    $destino = isset($arra_options_depe_nomb[$rows['iden_depe']]) ? $arra_options_depe_nomb[$rows['iden_depe']] : '';
    if (!$destino && isset($arra_options_depe_sigl[$rows['iden_depe']]))
        $destino = $arra_options_depe_sigl[$rows['iden_depe']];

    // Tipo visita
    $tipo_txt = isset($arra_options_tvis[$rows['tipo_visi']]) ? $arra_options_tvis[$rows['tipo_visi']] : '';

    // Autoriza (personal)
    $autoriza = isset($arra_options_pers[$rows['iden_pers']]) ? $arra_options_pers[$rows['iden_pers']] : '';

    $pdf->SetFont('arialn', '', 6.5);
    $pdf->MultiCell($col['num'],      $alto, $cont,     'B', 'C', $fill, 0, '', '', true, 0, false, false, $alto, 'M');
    $pdf->MultiCell($col['doc'],      $alto, $rows['ndoc_visi'], 'B', 'C', $fill, 0, '', '', true, 0, false, false, $alto, 'M');
    $pdf->MultiCell($col['nombre'],   $alto, $nombre,   'B', 'L', $fill, 0, '', '', true, 0, false, false, $alto, 'M');
    $pdf->MultiCell($col['tipo'],     $alto, $tipo_txt, 'B', 'C', $fill, 0, '', '', true, 0, false, false, $alto, 'M');
    $pdf->MultiCell($col['fecha'],    $alto, $fech_fmt, 'B', 'C', $fill, 0, '', '', true, 0, false, false, $alto, 'M');
    $pdf->MultiCell($col['ingreso'],  $alto, $ingr,     'B', 'C', $fill, 0, '', '', true, 0, false, false, $alto, 'M');
    $pdf->MultiCell($col['salida'],   $alto, $sali,     'B', 'C', $fill, 0, '', '', true, 0, false, false, $alto, 'M');
    $pdf->MultiCell($col['destino'],  $alto, $destino,  'B', 'L', $fill, 0, '', '', true, 0, false, false, $alto, 'M');
    $pdf->MultiCell($col['autoriza'], $alto, $autoriza, 'B', 'L', $fill, 1, '', '', true, 0, false, false, $alto, 'M');

    $fill = !$fill;
    $ultimo_digi_visi = $rows['digi_visi'];
}

// Total al final
$pdf->SetFont('arialn', 'B', 8);
$pdf->SetFillColor(220, 220, 220);
$pdf->SetXY(8, $pdf->GetY() + 2);
$pdf->Cell($ancho_total, 5, "TOTAL DE REGISTROS: $tota_resu", 1, 1, 'R', 1);

// Espacio para la firma al final del reporte
if ($pdf->GetY() > 175) {
    $pdf->AddPage();
    $pagi_actu++;
    poner_cabecera($pdf, $nomb_loca, $fecha_desd_fmt, $fecha_hast_fmt, $pagi_actu, $pagi_actu > $pagi_total ? $pagi_actu : $pagi_total, $col, $ancho_total);
    $pdf->SetXY(8, 40);
} else {
    $pdf->Ln(20);
}

$nomb_operador = 'FIRMA DEL OPERADOR DE TURNO';
if (!empty($ultimo_digi_visi)) {
    $res_op = $Db->query("SELECT nomb_oper, appa_oper, apma_oper FROM mp_admi_oper WHERE iden_oper='$ultimo_digi_visi'");
    if (isset($res_op[0]['nomb_oper'])) {
        $nomb_operador = trim(strtoupper($res_op[0]['nomb_oper'].' '.$res_op[0]['appa_oper'].' '.$res_op[0]['apma_oper']));
    }
}

$pdf->SetFont('arialn', '', 8);
$pdf->Cell($ancho_total, 4, "_________________________________________", 0, 1, 'C');
$pdf->Cell($ancho_total, 4, $nomb_operador, 0, 1, 'C');
$pdf->SetFont('arialn', 'B', 8);
$pdf->Cell($ancho_total, 4, "AGENTE DE SEGURIDAD", 0, 1, 'C');

// Generar PDF
$pdf->Output('reporte_visitas.pdf', 'I');
?>
