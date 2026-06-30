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
$margin_left=10;
$margin_right=10;
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

    $result=$Db->query("select * from mp_notif_guia_cabecera where iden_guia='".$_POST['iden_guia']."' ");
    foreach($result as $rows)
    {
        $nume_guia=$rows['nume_guia'];
        $anno_guia=$rows['anno_guia'];
        $fgen_guia=$rows['fgen_guia'];
        $iden_mens=$rows['iden_mens'];
        $iden_zona=$rows['iden_zona'];
        $codi_guia=str_pad($nume_guia, 4, "0", STR_PAD_LEFT).'-'.$anno_guia;
    }

    $result=$Db->query("select * from mp_maes_personal where iden_pers='$iden_mens'");
    foreach($result as $rows)
        $nomb_mens=$rows['appa_pers'].' '.$rows['apma_pers'].', '.$rows['nomb_pers'];
    
    $result=$Db->query("select * from mp_notif_zonas where iden_zona='$iden_zona'");
    foreach($result as $rows)
        $nomb_zona=$rows['nomb_zona'];
    
    $result=$Db->select('mp_maes_notif_tdocumento', '', '', '','');
    foreach($result as $rows)
            $arra_options_tipo[$rows['n_codigo']]=substr($rows['x_nombre'],0,10);
    
    $result=$Db->query("select * from mp_maes_personal where codi_carg in (17,18,19,20)");
	foreach($result as $rows)
		$arra_options_remi[$rows['iden_pers']]=ucwords(strtolower($rows['appa_pers'].' '.$rows['apma_pers'].', '.$rows['nomb_pers']));
	
	$result=$Db->query("select * from mp_notif_destinatario_frecuente");
	foreach($result as $rows)
		$arra_options_dest[$rows['iden_dest']]=ucwords(strtolower($rows['nomb_dest'].' - '.$rows['dire_dest']));
	
	$result=$Db->query("select * from mp_admi_depe");
	foreach($result as $rows)
		$arra_options_depe[$rows['codi_depe']]=ucwords(strtolower($rows['nomb_depe']));
            
/*    
    $result=$Db->query("select distinct b.codi_depe,b.nomb_depe from mp_mpar_carpetas a, mp_admi_depe b where a.codi_depe=b.codi_depe AND a.esta_mpar=1 AND a.codi_depe<>0 AND a.codi_pers<>0 AND a.depe_mpar='".$_POST['sesi_codi_depe']."'");
    foreach($result as $rows)
            $arra_nomb_depe[$rows['codi_depe']]=$rows['nomb_depe'];
    
    $result=$Db->query("select distinct b.iden_pers,b.nomb_pers,b.appa_pers from mp_mpar_carpetas a, mp_maes_personal b where a.codi_pers=b.iden_pers AND a.esta_mpar=1 AND a.codi_depe<>0 AND a.codi_pers<>0 AND a.depe_mpar='".$_POST['sesi_codi_depe']."'");
    foreach($result as $rows)
    {
        $posi=strpos($rows['nomb_pers'],' ');
        if($posi==0)    $posi=100;
            $arra_nomb_fisc[$rows['iden_pers']]=substr($rows['nomb_pers'],0,$posi)."\n".$rows['appa_pers'];
    }
*/

	$pdf->SetTextColor($colA,$colB,$colC);

	$font_llen='helvetica';
	$tama_llen=12;
	
	$result=$Db->query("select count(*) canti from mp_notif_guia_detalle where esta_deta=1 AND iden_guia='".$_POST['iden_guia']."'"); 
	foreach($result as $rows)
        $tota_resu=$rows['canti'];
    $pagi_total=ceil($tota_resu/20);
	
	$pdf->AddPage();
	poner_cabecera($pdf,$codi_guia,$fgen_guia,$tota_resu,$nomb_mens,$nomb_zona,1,$pagi_total);
	
function poner_cabecera($pdf,$codi_guia,$fgen_guia,$tota_resu,$nomb_mens,$nomb_zona,$pagi_actu=1,$pagi_tota=1)
{
    $imag_fron='images/cabecera_mpartes.jpg';
    
	$pdf->Image($imag_fron, 10, 10, 150, '', 'JPEG', '', 'M', false, 300, '', false, false, 0, false, false, false);
	
	
	
	// define barcode style
	$style = array(
		'position' => '',
		'align' => 'C',
		'stretch' => false,
		'fitwidth' => true,
		'cellfitalign' => '',
		'border' => false,
		'hpadding' => 'auto',
		'vpadding' => 'auto',
		'fgcolor' => array(0,0,0),
		'bgcolor' => false, //array(255,255,255),
		'text' => true,
		'font' => 'helvetica',
		'fontsize' => 8,
		'stretchtext' => 4
	);

	// PRINT VARIOUS 1D BARCODES

	// CODE 128 AUTO
	$pdf->write1DBarcode('A'.$codi_guia, 'C128', '140', '10', '', 17, 0.4, $style, 'N');
	
	
	

    $pdf->SetFont('arialn', '', 8);
    $pdf->SetXY(175,28);
    $pdf->Cell(10, 4, "FECHA :", 0, 1, 'R', 0, '', 0, false, 'T', 'M');
    $pdf->SetXY(184,28);
    $pdf->Cell(30, 4, date("d/m/Y"), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
    $pdf->SetXY(175,31);
    $pdf->Cell(10, 4, "HORA :", 0, 1, 'R', 0, '', 0, false, 'T', 'M');
    $pdf->SetXY(184,31);
    $pdf->Cell(30, 4, date("H:i:s"), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
    $pdf->SetXY(175,34);
    $pdf->Cell(10, 4, "PAGINA :", 0, 1, 'R', 0, '', 0, false, 'T', 'M');
    $pdf->SetXY(184,34);
    $pdf->Cell(30, 4, "$pagi_actu de $pagi_tota", 0, 1, 'L', 0, '', 0, false, 'T', 'M');

	$pdf->SetFont('arialn', '', 8);
    $pdf->SetXY(20,28);
	$pdf->Cell(170, 4, "SISTEMA DE GESTIÓN ADMINISTRATIVA - SIGA DF AREQUIPA", 0, 1, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', 'B', 14);
	$pdf->SetXY(20,32);
	$pdf->Cell(170, 4, "GUÍA DE ASIGNACIÓN N° $codi_guia", 0, 1, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', 'B', 9);
	$pdf->SetXY(10,40);
	$pdf->Cell(20, 4,"FECHA GUÍA", 0, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(3, 4,":", 0, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '', 9);
	$pdf->Cell(70, 4,"$fgen_guia", 0, 0, 'L', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetFont('arialn', 'B', 9);
	$pdf->Cell(35, 4,"CANTIDAD DE REGISTROS", 0, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(3, 4,":", 0, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '', 9);
	$pdf->Cell(30, 4,"$tota_resu", 0, 0, 'L', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetFont('arialn', 'B', 9);
	$pdf->SetXY(10,45);
	$pdf->Cell(20, 4,"NOTIFICADOR", 0, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(3, 4,":", 0, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '', 9);
	$pdf->Cell(70, 4,"$nomb_mens", 0, 0, 'L', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetFont('arialn', 'B', 9);
	$pdf->Cell(35, 4,"ZONA DESTINO", 0, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(3, 4,":", 0, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '', 9);
	$pdf->Cell(30, 4,"$nomb_zona", 0, 0, 'L', 0, '', 0, false, 'T', 'M');
	
	$pdf->setCellHeightRatio(1);
	$pdf->SetFillColor(224,224,224);
	//$pdf->SetTextColor(77, 77, 77);
	
	//MultiCell(w, h, txt, border = 0, align = 'J', fill = 0, ln = 1, x = '', y = '', reseth = true, stretch = 0, ishtml = false, autopadding = true, maxh = 0)
	
	$pdf->SetXY(10,51);
	$pdf->SetFont('arialn', '', 7);
	$pdf->Cell(6, 6, "N.", 'TB', 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(15, 6, "DOCUMENTO", 'TB', 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(20, 6, "CÓDIGO", 'TB', 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(14, 6, "TIPO", 'TB', 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(15, 6, "FECHA", 'TB', 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(50, 6, "FISCAL REMITENTE Y DESTINATARIO", 'TB', 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(45, 6, "DEPENDENCIA", 'TB', 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(25, 6, "DESPACHO", 'TB', 0, 'C', 1, '', 0, false, 'T', 'M');
}
	
	$pdf->SetFont('arialn', '',8);
	$pdf->SetXY(10,57);
	$alto=11;
	$cont=0;
	$cont_pagi=0;
	$pagi_actu=1;
	
	$result=$Db->query("select * from mp_notif_guia_detalle=a, mp_notif_documentos=b where a.iden_docu=b.iden_docu AND a.esta_deta=1 AND a.iden_guia='".$_POST['iden_guia']."' order by orde_deta");
    
	foreach($result as $rows)
	{
	    $cont++;
	    $cont_pagi++;
	    if($cont_pagi>20)
	    {
	        $pdf->AddPage();
	        $pagi_actu++;
	        poner_cabecera($pdf,$codi_guia,$fgen_guia,$tota_resu,$nomb_mens,$nomb_zona,$pagi_actu,$pagi_total);
	        $pdf->SetXY(20,59);
	        $cont_pagi=1;
	    }
	    $rows['fdig_mpar']=substr($rows['fdig_mpar'],6,2).'/'.substr($rows['fdig_mpar'],4,2).'/'.substr($rows['fdig_mpar'],0,4)."\n".substr($rows['fdig_mpar'],8,2).':'.substr($rows['fdig_mpar'],10,2)." hrs.";
		$rows['fech_asig']=substr($rows['fech_asig'],6,2).'/'.substr($rows['fech_asig'],4,2).'/'.substr($rows['fech_asig'],0,4)."\n".substr($rows['fech_asig'],8,2).':'.substr($rows['fech_asig'],10,2)." hrs.";
    	
    	$pdf->MultiCell(6, $alto,$cont, 'B', 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(15, $alto,$rows['nume_docu'], 'B', 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(20, $alto,$rows['cbar_docu'], 'B', 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(14, $alto,$arra_options_tipo[$rows['iden_tipo']], 'B', 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(15, $alto,$rows['freg_docu'], 'B', 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(50, $alto,"(F) ".$arra_options_remi[$rows['iden_remi']]."\n(D) ".$arra_options_dest[$rows['iden_dest']], 'B', 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(45, $alto,$arra_options_depe[$rows['depe_remi']], 'B', 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(25, $alto,$arra_nomb_fisc[$rows['codi_pers']], 'B', 'C', 0, 1, '', '', true, 0, false, false, $alto, 'M');
		
	}

//Close and output PDF document
$pdf->Output('example_027.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
