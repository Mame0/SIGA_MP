<?php
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
$margin_bottom=20;
$margin_header=20;
$margin_footer=20;
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

//$_POST['codi_aler']=1;

    $result=$Db->query("select * from mp_elec_alertas where codi_aler='".$_POST['codi_aler']."'");
    $_POST['codi_elec']=$result[0]['codi_elec'];
    $_POST['codi_usua']=$result[0]['codi_usua'];
    $_POST['aler_ocur']=$result[0]['aler_ocur'];
    $_POST['codi_tale']=$result[0]['codi_tale'];
    $_POST['hora_aler']=substr($result[0]['fech_aler'],8,2).':'.substr($result[0]['fech_aler'],10,2).' hrs.';
    $_POST['fech_aler']=substr($result[0]['fech_aler'],6,2).'/'.substr($result[0]['fech_aler'],4,2).'/'.substr($result[0]['fech_aler'],0,4);
    $_POST['ubig_aler']=$result[0]['ubig_aler'];
    $_POST['luga_aler']=$result[0]['luga_aler'];
    $_POST['deta_aler']=$result[0]['deta_aler'];
    $_POST['acci_aler']=$result[0]['acci_aler'];
    $_POST['digi_aler']=$result[0]['digi_aler'];
    
    $result=$Db->query("select * from mp_admi_oper where iden_oper='".$_POST['digi_aler']."'");
    $_POST['nomb_fisc']=$result[0]['nomb_oper'].' '.$result[0]['appa_oper'].' '.$result[0]['apma_oper'];
    $_POST['nomb_depe']=$result[0]['depe_oper'];
    $_POST['tele_fisc']=$result[0]['celu_oper'];
    $_POST['mail_fisc']=$result[0]['mail_oper'];
    
    $arra_aler_ocur[$_POST['aler_ocur']]='X';
    $arra_codi_tale[$_POST['codi_tale']]='X';
    
    $result=$Db->query("select prov,dist from ubig_reni WHERE CONCAT(cdep,cpro,cdis)='".$_POST['ubig_aler']."'");
	$nomb_prov=$result[0]['prov'].' ';
	$nomb_dist=$result[0]['dist'].' ';
	
	//Colores del formato
	$colA=0;
	$colB=0;
	$colC=0;
	
	//Colores de texto llenado
	$colD=0;
	$colE=63;
	$colF=127;
	
	$pdf->SetTextColor($colA,$colB,$colC);
    
	$font_llen='helvetica';
	$tama_llen=12;
	
	$pdf->AddPage();
    $imag_fron='images/cabecera_elecciones2022.jpg';
    
	$pdf->Image($imag_fron, 20, 18, 170, '', 'JPEG', '', 'M', false, 300, '', false, false, 0, false, false, false);

    //$pdf->SetLineWidth(.01);
    $pdf->SetLineStyle(array('width' => 0.05, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0));

	$pdf->SetFont('arialn', 'B', 14);
    $pdf->SetXY(20,46);
	$pdf->Cell(170, 4, "FORMATO B", 0, 1, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetXY(20,51.5);
	$pdf->Cell(170, 4, "ALERTAS Y OCURRENCIAS", 0, 1, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '', 11);
	$pdf->Cell(170, 4, html_entity_decode("ANTES, DURANTE Y DESPU&Eacute;S DE LAS ELECCIONES REGIONALES Y MUNICIPALES 2022"), 0, 1, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(170, 4, html_entity_decode("Ministerio P&uacute;blico - Fiscal&iacute;a de la Naci&oacute;n"), 0, 1, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->setCellHeightRatio(1);
	$pdf->SetFillColor(224,224,224);
	//$pdf->SetTextColor(77, 77, 77);
	
	//MultiCell(w, h, txt, border = 0, align = 'J', fill = 0, ln = 1, x = '', y = '', reseth = true, stretch = 0, ishtml = false, autopadding = true, maxh = 0)
	
	$pdf->SetXY(20,71);
	$pdf->SetFont('arialn', '', 9);
	$pdf->Cell(16.2, 13, "Fecha", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->MultiCell(26.2, 13,$_POST['fech_aler'], 1, 'C', 0, 0, '', '', true, 0, false, false, 13, 'M');
	$pdf->SetFont('arialn', '', 9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(16.2, 13, "Hora", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->MultiCell(26.2, 13,$_POST['hora_aler'], 1, 'C', 0, 0, '', '', true, 0, false, false, 13, 'M');
	$pdf->SetFont('arialn', '', 9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(85.2, 4, "Tipo", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(104.8,75);
	$pdf->Cell(21.2, 9, "Alerta", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->MultiCell(21.2, 9,$arra_aler_ocur['1'], 1, 'C', 0, 0, '', '', true, 0, false, false, 9, 'M');
	$pdf->SetFont('arialn', '', 9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(21.2, 9, "Ocurrencia", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->MultiCell(21.2, 9,$arra_aler_ocur['2'], 1, 'C', 0, 0, '', '', true, 0, false, false, 9, 'M');
	
	$pdf->SetXY(20,87);
	$pdf->SetFont('arialn', '',12);
	$pdf->Cell(9, 54, "1", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '',8);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(53, 4, "Distrito Fiscal", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(54, 4, "Provincia", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(54, 4, "Distrito", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(29,90.3);
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->MultiCell(53, 9.5,"Arequipa", 1, 'C', 0, 0, '', '', true, 0, false, false, 9.5, 'M');
	$pdf->MultiCell(54, 9.5,$nomb_prov, 1, 'C', 0, 0, '', '', true, 0, false, false, 9.5, 'M');
	$pdf->MultiCell(54, 9.5,$nomb_dist, 1, 'C', 0, 0, '', '', true, 0, false, false, 9.5, 'M');
	
	$pdf->SetXY(29,99.8);
	$pdf->SetFont('arialn', '',8);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(161, 4, "Lugar", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(29,103.8);
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->MultiCell(161, 9.5,$_POST['luga_aler'], 1, 'C', 0, 0, '', '', true, 0, false, false, 9.5, 'M');
	
	$pdf->SetXY(29,113.3);
	$pdf->SetFont('arialn', '',8);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(80.5, 4, html_entity_decode("Fiscal que reporta"), 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(80.5, 4, html_entity_decode("Fiscal&iacute;a"), 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(29,117.3);
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->MultiCell(80.5, 9.5,$_POST['nomb_fisc'], 1, 'C', 0, 0, '', '', true, 0, false, false, 9.5, 'M');
	$pdf->MultiCell(80.5, 9.5,$_POST['nomb_depe'], 1, 'C', 0, 0, '', '', true, 0, false, false, 9.5, 'M');
	
	$pdf->SetXY(29,126.8);
	$pdf->SetFont('arialn', '',8);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(80.5, 4, html_entity_decode("Tel&eacute;fono"), 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(80.5, 4, html_entity_decode("Correo Electr&oacute;nico"), 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(29,130.8);
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->MultiCell(80.5, 10.2,$_POST['tele_fisc'], 1, 'C', 0, 0, '', '', true, 0, false, false, 10.2, 'M');
	$pdf->MultiCell(80.5, 10.2,$_POST['mail_fisc'], 1, 'C', 0, 0, '', '', true, 0, false, false, 10.2, 'M');
	
	$pdf->SetXY(20,141);
	$pdf->SetFont('arialn', '',12);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(9, 69, "2", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '',8);
	$pdf->MultiCell(18, 69,"Tipo de alerta u ocurrencia", 1, 'C', 1, 0, '', '', true, 0, false, false, 69, 'M');
	$pdf->Cell(120, 4, "Tipo", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(23, 4, "Marcar con \"X\"", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,145);
	$pdf->SetFont('arialn', '',9);
	$pdf->Cell(120, 5, html_entity_decode("1. Destrucci&oacute;n de material electoral (&aacute;nforas, actas o c&eacute;dulas de votaci&oacute;n, etc.)"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(23, 5, $arra_codi_tale[1], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,150);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(120, 5, html_entity_decode("2. Disturbios debido a problemas en la instalaci&oacute;n de mesas"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(23, 5, $arra_codi_tale[2], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,155);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(120, 5, html_entity_decode("3. Difusi&oacute;n de propaganda pol&iacute;tica durante el mismo d&iacute;a de las elecciones"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(23, 5, $arra_codi_tale[3], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,160);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(120, 5, html_entity_decode("4. Enfrentamientos entre agrupaciones pol&iacute;ticas"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(23, 5, $arra_codi_tale[4], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,165);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(120, 5, html_entity_decode("5. Presencia de \"votos golondrinos\""), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(23, 5, $arra_codi_tale[5], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,170);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(120, 5, html_entity_decode("6. Toma de centros de votaci&oacute;n con actos violentos"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(23, 5, $arra_codi_tale[6], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,175);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(120, 5, html_entity_decode("7. Impedimento que el personal electoral se lleve las actas o las &aacute;nforas de los centros de votaci&oacute;n"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(23, 5, $arra_codi_tale[7], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,180);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(120, 5, html_entity_decode("8. Suplantaci&oacute;n de identidad"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(23, 5, $arra_codi_tale[8], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,185);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(120, 5, html_entity_decode("9. Incautaci&oacute;n del material electoral presuntamente falso o adulterado"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(23, 5, $arra_codi_tale[9], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,190);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(120, 5, html_entity_decode("10. Intervenciones a personas portando documentos presuntamente falsos"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(23, 5, $arra_codi_tale[10], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,195);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(120, 5, html_entity_decode("11. Discriminaci&oacute;n e incitaci&oacute;n a la discriminaci&oacute;n"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(23, 5, $arra_codi_tale[11], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,200);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(120, 5, html_entity_decode("12. Violaci&oacute;n de medidas sanitarias"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(23, 5, $arra_codi_tale[12], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,205);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(120, 5, html_entity_decode("13. Otros"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(23, 5, $arra_codi_tale[13], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(20,210);
	$pdf->SetFont('arialn', '',12);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(9, 67, "3", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '',8);
	$pdf->MultiCell(18, 69,"Detalle de los hechos", 1, 'C', 1, 0, '', '', true, 0, false, false, 69, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen-1);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->MultiCell(143, 69,"\n".$_POST['deta_aler'], 1, 'L', 0, 0, '', '', true, 0, false, false, 69, 'T');
	
	
	
	
	
	$pdf->SetXY(20,276.5);
	$pdf->SetFont('arialn', '',10);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(170, 8, "1", 0, 0, 'R', 0, '', 0, false, 'T', 'M');
	
	$pdf->AddPage();
	
	$pdf->SetXY(20,20);
	$pdf->SetFont('arialn', '',12);
	$pdf->Cell(9, 53, "4", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '',8);
	$pdf->MultiCell(15, 53,"Accionar fiscal", 1, 'C', 1, 0, '', '', true, 0, false, false, 53, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen-1);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->MultiCell(146, 53,"\n".$_POST['acci_aler'], 1, 'L', 0, 0, '', '', true, 0, false, false, 53, 'T');
	
	$pdf->SetXY(20,73);
	$pdf->SetFont('arialn', '',12);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(9, 195, "5", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '',8);
	$pdf->MultiCell(15, 195,"Lesionados y fallecidos", 1, 'C', 1, 0, '', '', true, 0, false, false, 195, 'M');
	$pdf->Cell(28, 4, html_entity_decode("Condici&oacute;n") , 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(8, 8, html_entity_decode("N&deg;") , 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(59, 8, html_entity_decode("Nombres y Apellidos") , 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(27, 8, html_entity_decode("DNI") , 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(12, 8, html_entity_decode("Sexo") , 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(12, 8, html_entity_decode("Edad") , 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(44,77);
	$pdf->SetFont('arialn', '',8);
	$pdf->Cell(14, 4, html_entity_decode("Lesionado") , 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(14, 4, html_entity_decode("Fallecido") , 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$alto=9.35;
	$pdf->SetFont($font_llen, '', $tama_llen-2);
	
	$result=$Db->query("select * from mp_elec_alertas_lesionados WHERE codi_aler='".$_POST['codi_aler']."' AND esta_lesi='1' order by codi_lesi");
	$cont=0;
	foreach($result as $rows)
	{
	    $cont++;
	    $arra_lesi[$cont]['codi_lesi']=$rows['codi_lesi'];
	    $arra_lesi[$cont]['lesi_fall']=$rows['lesi_fall'];
	    $arra_lesi[$cont]['nomb_lesi']=$rows['nomb_lesi'];
	    $arra_lesi[$cont]['ndni_lesi']=$rows['ndni_lesi'];
	    $arra_lesi[$cont]['sexo_lesi']=$rows['sexo_lesi'];
	    $arra_lesi[$cont]['edad_lesi']=$rows['edad_lesi'];
	    $arra_lesi[$cont]['nume_lesi']=$cont;
	    $arra_lesi_fall[$cont][$rows['lesi_fall']]='X';
	}
	$arra_sexo[1]='M';
	$arra_sexo[2]='F';
//Textos completos	codi_lesi	codi_aler	lesi_fall	nomb_lesi	ndni_lesi	sexo_lesi	edad_lesi	digi_lesi	fdig_lesi	esta_lesi
	
for($x=0;$x<20;$x++)
{
	$pdf->SetXY(44,81+$alto*$x);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->MultiCell(14, $alto,$arra_lesi_fall[$x+1][1], 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(14, $alto,$arra_lesi_fall[$x+1][2], 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(8, $alto,$arra_lesi[$x+1]['nume_lesi'], 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(59, $alto,$arra_lesi[$x+1]['nomb_lesi'], 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(27, $alto,$arra_lesi[$x+1]['ndni_lesi'], 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(12, $alto,$arra_sexo[$arra_lesi[$x+1]['sexo_lesi']], 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(12, $alto,$arra_lesi[$x+1]['edad_lesi'], 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
}	
	
	//$pdf->MultiCell(28, 4,html_entity_decode("Condici&oacute;n"), 1, 'C', 1, 0, '', '', true, 0, false, false, 28, 'M');
	//$pdf->MultiCell(8, 8,html_entity_decode("N&deg;"), 1, 'C', 1, 0, '', '', true, 0, false, false, 8, 'M');
	//$pdf->MultiCell(40, 8,html_entity_decode("Nombres y apellidos"), 1, 'C', 1, 0, '', '', true, 0, false, false, 8, 'M');
	
	$pdf->SetXY(20,268);
	$pdf->SetFont('arialn', 'B',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(11, 9, "Anexar:", 0, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '',10);
	$pdf->MultiCell(155, 9,"Copia simple de documento fiscal", 0, 'L', 0, 0, '', '', true, 0, false, false, 9, 'M');
	
	$pdf->SetXY(20,276.5);
	$pdf->SetFont('arialn', '',9);
	$pdf->Cell(170, 8, "2", 0, 0, 'R', 0, '', 0, false, 'T', 'M');

	// ---------------------------------------------------------
   //}
//}

//Close and output PDF document
$pdf->Output('example_027.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
