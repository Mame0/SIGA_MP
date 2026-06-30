<?php

//$_SESSION['iden_oper']=1;
date_default_timezone_set('America/Lima');

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');
require_once '../../Db.class.php';
class MYPDF extends TCPDF {

        //Page header
        public function Header() {
        }

        // Page footer
        public function Footer() {
        }
}

// create new PDF document
$pdf = new MYPDF("P", PDF_UNIT, "A4", true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$margin_left=20;
$margin_right=20;
$margin_top=20;
$margin_bottom=10;
$margin_header=20;
$margin_footer=10;
$pdf->SetMargins($margin_left, $margin_top, $margin_right);
$pdf->SetHeaderMargin($margin_header);
$pdf->SetFooterMargin($margin_footer);

// set auto page breaks
$pdf->SetAutoPageBreak(FALSE, $margin_bottom);

// set image scale factor
//$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setImageScale(1.53);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

$Db = new Db();

    $result=$Db->query("select * from mp_admi_depe where codi_depe='".$_POST['sesi_codi_depe']."' ");
    foreach($result as $rows)
        $nomb_depe=$rows['abre_depe'];
    
    $result=$Db->query("select distinct b.codi_depe,b.nomb_depe from mp_mpar_carpetas a, mp_admi_depe b where a.codi_depe=b.codi_depe AND a.esta_mpar=1 AND a.codi_depe<>0 AND a.codi_pers<>0 AND a.depe_mpar='".$_POST['sesi_codi_depe']."'");
    foreach($result as $rows)
            $arra_nomb_depe[$rows['codi_depe']]=$rows['nomb_depe'];
    
    $result=$Db->select('mp_maes_mpar_tdoc', '', '', '', ['n_codigo'=>'ASC']);
    foreach($result as $rows)
            $arra_options_tdoc[$rows['n_codigo']]=$rows['x_nombre'];
    
    $result=$Db->query("select distinct b.iden_pers,b.nomb_pers,b.appa_pers from mp_mpar_carpetas a, mp_maes_personal b where a.codi_pers=b.iden_pers AND a.esta_mpar=1 AND a.codi_depe<>0 AND a.codi_pers<>0 AND a.depe_mpar='".$_POST['sesi_codi_depe']."'");
    foreach($result as $rows)
    {
        $posi=strpos($rows['nomb_pers'],' ');
        if($posi==0)    $posi=100;
            $arra_nomb_fisc[$rows['iden_pers']]=substr($rows['nomb_pers'],0,$posi)."\n".$rows['appa_pers'];
    }

	$pdf->SetTextColor($colA,$colB,$colC);

	$font_llen='helvetica';
	$tama_llen=12;
	
	$items_por_pagina=18;
	
	//$result=$Db->query("select count(*) canti from mp_patr_inve_regi as a, mp_patr_siga as b where a.codi_patr=b.codigo_patrimonial AND a.codi_inve='".$_POST['codi_inve']."' AND a.usua_inve='".$_POST['usua_inve']."' AND a.esta_regi='1'");
	$result=$Db->query("select count(*) canti from mp_patr_siga where docum_identidad='".$_POST['usua_inve']."'");
	foreach($result as $rows)
        $tota_resu=$rows['canti'];
    $pagi_total=ceil($tota_resu/$items_por_pagina);
	
	$subtitulo="[".$_POST['fech_inve']."] ".strtoupper($_POST['nomb_inve']);
	$subsubtitulo="USUARIO: ".substr($_POST['usua_dato'],4);
	
	$pdf->AddPage();
	poner_cabecera($pdf,$subtitulo,$subsubtitulo,1,$pagi_total);
	
function poner_cabecera($pdf,$subtitulo,$subsubtitulo,$pagi_actu=1,$pagi_tota=1)
{
    $imag_fron='images/cabecera_mpartes.jpg';
    
	$pdf->Image($imag_fron, 20, 10, 170, '', 'JPEG', '', 'M', false, 300, '', false, false, 0, false, false, false);

    $pdf->SetFont('arialn', '', 8);
    $pdf->SetXY(165,14);
    $pdf->Cell(10, 4, "FECHA :", 0, 1, 'R', 0, '', 0, false, 'T', 'M');
    $pdf->SetXY(174,14);
    $pdf->Cell(30, 4, date("d/m/Y"), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
    $pdf->SetXY(165,17);
    $pdf->Cell(10, 4, "HORA :", 0, 1, 'R', 0, '', 0, false, 'T', 'M');
    $pdf->SetXY(174,17);
    $pdf->Cell(30, 4, date("H:i:s"), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
    $pdf->SetXY(165,20);
    $pdf->Cell(10, 4, "PAGINA :", 0, 1, 'R', 0, '', 0, false, 'T', 'M');
    $pdf->SetXY(174,20);
    $pdf->Cell(30, 4, "$pagi_actu de $pagi_tota", 0, 1, 'L', 0, '', 0, false, 'T', 'M');

	$pdf->SetFont('arialn', '', 11);
    $pdf->SetXY(20,28);
	$pdf->Cell(170, 4, "SISTEMA INTEGRADO DE GESTIÓN ADMINISTRATIVA - SIGA INVENTARIO", 0, 1, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', 'B', 14);
	$pdf->SetXY(20,33.5);
	$pdf->Cell(170, 4, "REPORTE DE INVENTARIO POR USUARIO", 0, 1, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '', 11);
	$pdf->SetXY(20,40);
	$pdf->Cell(170, 4,$subtitulo, 0, 1, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetXY(20,45);
	$pdf->Cell(170, 4, $subsubtitulo, 0, 1, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->setCellHeightRatio(1);
	$pdf->SetFillColor(224,224,224);
	//$pdf->SetTextColor(77, 77, 77);

	//MultiCell(w, h, txt, border = 0, align = 'J', fill = 0, ln = 1, x = '', y = '', reseth = true, stretch = 0, ishtml = false, autopadding = true, maxh = 0)
	
	$pdf->SetXY(20,51);
	$pdf->SetFont('arialn', '', 9);
	$pdf->Cell(8, 8, "N.", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(22, 8, "COD.PATR.", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(52, 8, "DESCRIPCIÓN", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(20, 8, "MARCA", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(27, 8, "MODELO/COLOR", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(23, 8, "SERIE", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(19, 8, "INVENTARIO", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(20,259);
	$pdf->MultiCell(85.5, 20," ", 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(85.5, 20," ", 1, 'C', 0, 1, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(85.5, 5,"TRABAJADOR RESPONSABLE DE LOS BIENES", 1, 'C', 0, 0, '', '', true, 0, false, false, 5, 'M');
    $pdf->MultiCell(85.5, 5,"PERSONAL INVENTARIADOR", 1, 'C', 0, 1, '', '', true, 0, false, false, 5, 'M');
}
	
	$pdf->SetFont('arialn', '',9);
	$pdf->SetXY(20,59);
	$alto=11;
	$cont=0;
	$cont_pagi=0;
	$pagi_actu=1;
	
	if($_POST['loca_inve'])
	    $result=$Db->query("select * from mp_patr_inve_regi as a, mp_patr_siga as b where a.codi_patr=b.codigo_patrimonial AND a.codi_inve='".$_POST['codi_inve']."' AND a.usua_inve='".$_POST['usua_inve']."' AND a.codi_loca='".$_POST['loca_inve']."' AND a.esta_regi='1' order by b.descripcion asc");
	else
	    $result=$Db->query("select * from mp_patr_inve_regi as a, mp_patr_siga as b where a.codi_patr=b.codigo_patrimonial AND a.codi_inve='".$_POST['codi_inve']."' AND a.usua_inve='".$_POST['usua_inve']."' AND a.esta_regi='1' order by b.descripcion asc");
	foreach($result as $rows)
	{
	    $cont++;
	    $cont_pagi++;
	    if($cont_pagi>$items_por_pagina)
	    {
	        $pdf->AddPage();
	        $pagi_actu++;
	        poner_cabecera($pdf,$subtitulo,$subsubtitulo,$pagi_actu,$pagi_total);
	        $pdf->SetXY(20,59);
	        $cont_pagi=1;
	    }
		
		$rows['fdig_regi']=substr($rows['fdig_regi'],6,2).'/'.substr($rows['fdig_regi'],4,2).'/'.substr($rows['fdig_regi'],0,4)."\n".substr($rows['fdig_regi'],8,2).':'.substr($rows['fdig_regi'],10,2)." hrs.";
    	
    	$pdf->MultiCell(8, $alto,$cont, 'B', 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(22, $alto,$rows['codigo_patrimonial']." C.B.:".$rows['codigo_barra'], 'B', 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(52, $alto,$rows['descripcion'], 'B', 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(20, $alto,$rows['marca'], 'B', 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(27, $alto,$rows['modelo']."\n".$rows['color'], 'B', 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(23, $alto,$rows['nro_serie'], 'B', 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(19, $alto,$rows['fdig_regi'], 'B', 'C', 0, 1, '', '', true, 0, false, false, $alto, 'M');
			
		$arra_impr[$rows['codigo_patrimonial']]=$rows['codigo_patrimonial'];
	}
	
	
	//AGREGAMOS LOS BIENES DEL SIGA QUE NO HAN SIDO UBICADOS
	//$result=$Db->query("select * from mp_patr_inve_regi as a, mp_patr_siga as b where a.codi_patr=b.codigo_patrimonial AND a.codi_inve='".$_POST['codi_inve']."' AND a.usua_inve='".$_POST['usua_inve']."' AND a.esta_regi='1' order by b.descripcion asc");
	$result=$Db->query("select * from mp_patr_siga where docum_identidad='".$_POST['usua_inve']."' order by descripcion asc");
	foreach($result as $rows)
	{
	  if(!$arra_impr[$rows['codigo_patrimonial']])
	  {
	    $cont++;
	    $cont_pagi++;
	    if($cont_pagi>$items_por_pagina)
	    {
	        $pdf->AddPage();
	        $pagi_actu++;
	        poner_cabecera($pdf,$subtitulo,$subsubtitulo,$pagi_actu,$pagi_total);
	        $pdf->SetXY(20,59);
	        $cont_pagi=1;
	    }
		
		//$rows['fech_asig']=substr($rows['fech_asig'],6,2).'/'.substr($rows['fech_asig'],4,2).'/'.substr($rows['fech_asig'],0,4)."\n".substr($rows['fech_asig'],8,2).':'.substr($rows['fech_asig'],10,2)." hrs.";
    	
    	$pdf->MultiCell(8, $alto,$cont, 'B', 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(22, $alto,$rows['codigo_patrimonial']." C.B.:".$rows['codigo_barra'], 'B', 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(52, $alto,$rows['descripcion'], 'B', 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(20, $alto,$rows['marca'], 'B', 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(27, $alto,$rows['modelo']."\n".$rows['color'], 'B', 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(23, $alto,$rows['nro_serie'], 'B', 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(19, $alto,"PENDIENTE DE UBICAR", 'B', 'C', 0, 1, '', '', true, 0, false, false, $alto, 'M');
	  }
	}
	
	
//Close and output PDF document
$pdf->Output('example_027.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
