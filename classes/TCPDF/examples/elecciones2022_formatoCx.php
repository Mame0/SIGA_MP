<?php

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
//$result=$Db->query("select * from mp_maes_fotocheck_cargo");
//foreach($result as $rows)
//	$arra_carg[$rows['n_codigo']]=$rows['x_nombre'];

	$font_llen='helvetica';
	$tama_llen=12;
	
	$pdf->AddPage();
    $imag_fron='images/cabecera_elecciones2022.jpg';
    
	$pdf->Image($imag_fron, 20, 18, 170, '', 'JPEG', '', 'M', false, 300, '', false, false, 0, false, false, false);

    

	$pdf->SetFont('arialn', 'B', 14);
    $pdf->SetXY(20,46);
	$pdf->Cell(170, 4, "FORMATO C", 0, 1, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetXY(20,51.5);
	$pdf->Cell(170, 4, "DETENCIONES E INTERVENCIONES", 0, 1, 'C', 0, '', 0, false, 'T', 'M');
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
	$pdf->MultiCell(26.2, 13,"25/09/2022", 1, 'C', 0, 0, '', '', true, 0, false, false, 13, 'M');
	$pdf->SetFont('arialn', '', 9);
	$pdf->Cell(16.2, 13, "Hora", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(26.2, 13,"07:08 hrs.", 1, 'C', 0, 0, '', '', true, 0, false, false, 13, 'M');
	$pdf->SetFont('arialn', '', 9);
	$pdf->Cell(85.2, 4, "Tipo", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(104.8,75);
	$pdf->Cell(21.2, 9, "Detenido", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(21.2, 9,"X", 1, 'C', 0, 0, '', '', true, 0, false, false, 9, 'M');
	$pdf->SetFont('arialn', '', 9);
	$pdf->Cell(21.2, 9, "Intervenido", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(21.2, 9,"X", 1, 'C', 0, 0, '', '', true, 0, false, false, 9, 'M');
	
	$pdf->SetXY(20,87);
	$pdf->SetFont('arialn', '',12);
	$pdf->Cell(9, 40.5, "1", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '',8);
	$pdf->Cell(53, 4, "Distrito Fiscal", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(54, 4, "Provincia", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(54, 4, "Distrito", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(29,90.3);
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(53, 9.5,"07:08 hrs.", 1, 'C', 0, 0, '', '', true, 0, false, false, 9.5, 'M');
	$pdf->MultiCell(54, 9.5,"07:08 hrs.", 1, 'C', 0, 0, '', '', true, 0, false, false, 9.5, 'M');
	$pdf->MultiCell(54, 9.5,"07:08 hrs.", 1, 'C', 0, 0, '', '', true, 0, false, false, 9.5, 'M');
	/*
	$pdf->SetXY(29,99.8);
	$pdf->SetFont('arialn', '',8);
	$pdf->Cell(161, 4, "Lugar", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(29,103.8);
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(161, 9.5,"07:08 hrs.", 1, 'C', 0, 0, '', '', true, 0, false, false, 9.5, 'M');
	*/
	$pdf->SetXY(29,99.8);
	$pdf->SetFont('arialn', '',8);
	$pdf->Cell(80.5, 4, html_entity_decode("Fiscal que reporta"), 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(80.5, 4, html_entity_decode("Fiscal&iacute;a"), 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(29,103.8);
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(80.5, 9.5,"07:08 hrs.", 1, 'C', 0, 0, '', '', true, 0, false, false, 9.5, 'M');
	$pdf->MultiCell(80.5, 9.5,"07:08 hrs.", 1, 'C', 0, 0, '', '', true, 0, false, false, 9.5, 'M');
	
	$pdf->SetXY(29,113.3);
	$pdf->SetFont('arialn', '',8);
	$pdf->Cell(80.5, 4, html_entity_decode("Tel&eacute;fono"), 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(80.5, 4, html_entity_decode("Correo Electr&oacute;nico"), 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(29,117.3);
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(80.5, 10.2,"07:08 hrs.", 1, 'C', 0, 0, '', '', true, 0, false, false, 10.2, 'M');
	$pdf->MultiCell(80.5, 10.2,"07:08 hrs.", 1, 'C', 0, 0, '', '', true, 0, false, false, 10.2, 'M');
	
	$pdf->SetXY(20,127.5);
	$pdf->SetFont('arialn', '',12);
	$pdf->Cell(9, 148, "2", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '',8);
	$pdf->MultiCell(18, 10.2,"Apellidos y nombres", 1, 'C', 1, 0, '', '', true, 0, false, false, 10.2, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(143, 10.2,"  Jesus Manuel Barreda Mayhua", 1, 'L', 0, 0, '', '', true, 0, false, false, 10.2, 'M');
	
	$pdf->SetXY(29,137.7);
	$pdf->SetFont('arialn', '',8);
	$pdf->MultiCell(18, 10.2,"DNI", 1, 'C', 1, 0, '', '', true, 0, false, false, 10.2, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(50, 10.2,"  29709217", 1, 'L', 0, 0, '', '', true, 0, false, false, 10.2, 'M');
	$pdf->SetFont('arialn', '',8);
	$pdf->MultiCell(12, 10.2,"Edad", 1, 'C', 1, 0, '', '', true, 0, false, false, 10.2, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(25, 10.2,"88", 1, 'C', 0, 0, '', '', true, 0, false, false, 10.2, 'M');
	$pdf->SetFont('arialn', '',8);
	$pdf->MultiCell(12, 10.2,"Sexo", 1, 'C', 1, 0, '', '', true, 0, false, false, 10.2, 'M');
	$pdf->MultiCell(22, 4,"Hombre", 1, 'C', 1, 0, '', '', true, 0, false, false, 4, 'M');
	$pdf->MultiCell(22, 4,"Mujer", 1, 'C', 1, 0, '', '', true, 0, false, false, 4, 'M');
	
	$pdf->SetXY(146,141.7);
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(22, 6.2,"X", 1, 'C', 0, 0, '', '', true, 0, false, false, 6.2, 'M');
	$pdf->MultiCell(22, 6.2,"X", 1, 'C', 0, 0, '', '', true, 0, false, false, 6.2, 'M');
	
	$pdf->SetXY(29,147.9);
	$pdf->SetFont('arialn', '',8);
	$pdf->MultiCell(18, 127.6,html_entity_decode("Motivo de intervenci&oacute;n o detenci&oacute;n"), 1, 'C', 1, 0, '', '', true, 0, false, false, 127.6, 'M');
	$pdf->Cell(62, 4, html_entity_decode("Motivo de intervenci&oacute;n"), 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(25, 4, html_entity_decode("Hora"), 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(56, 4, html_entity_decode("Detalle"), 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$alto=10.3;
	
	$pdf->SetXY(47,151.9);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(62, $alto,html_entity_decode("1. Distribuir propaganda electoral en horas restringidas"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen-1);
	$pdf->MultiCell(25, $alto,html_entity_decode("12:25 hrs."), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(56, $alto,html_entity_decode("dfgsdfg dsfg sdfg sdfg sdf gsdf g"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	
	$pdf->SetXY(47,151.9+$alto*1);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(62, $alto,html_entity_decode("2. Suplantar identidad"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen-1);
	$pdf->MultiCell(25, $alto,html_entity_decode("12:25 hrs."), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(56, $alto,html_entity_decode("dfgsdfg dsfg sdfg sdfg sdf gsdf g"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	
	$pdf->SetXY(47,151.9+$alto*2);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(62, $alto,html_entity_decode("3. Destruir materialelectoral (&aacute;nforas, actas c&eacute;dulas, etc.)"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen-1);
	$pdf->MultiCell(25, $alto,html_entity_decode("12:25 hrs."), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(56, $alto,html_entity_decode("dfgsdfg dsfg sdfg sdfg sdf gsdf g"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	
	$pdf->SetXY(47,151.9+$alto*3);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(62, $alto,html_entity_decode("4. Portar material electoral presuntamente falso o adulterado"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen-1);
	$pdf->MultiCell(25, $alto,html_entity_decode("12:25 hrs."), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(56, $alto,html_entity_decode("dfgsdfg dsfg sdfg sdfg sdf gsdf g"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	
	$pdf->SetXY(47,151.9+$alto*4);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(62, $alto,html_entity_decode("5. Incupmplir la \"ley seca\""), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen-1);
	$pdf->MultiCell(25, $alto,html_entity_decode("12:25 hrs."), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(56, $alto,html_entity_decode("dfgsdfg dsfg sdfg sdfg sdf gsdf g"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	
	$pdf->SetXY(47,151.9+$alto*5);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(62, $alto,html_entity_decode("6. Ingresar a un centro de votaci&oacute;n portando un arma de fuego"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen-1);
	$pdf->MultiCell(25, $alto,html_entity_decode("12:25 hrs."), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(56, $alto,html_entity_decode("dfgsdfg dsfg sdfg sdfg sdf gsdf g"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	
	$pdf->SetXY(47,151.9+$alto*6);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(62, $alto,html_entity_decode("7. Enfrentamiento entre agrupaciones pol&iacute;ticas"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen-1);
	$pdf->MultiCell(25, $alto,html_entity_decode("12:25 hrs."), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(56, $alto,html_entity_decode("dfgsdfg dsfg sdfg sdfg sdf gsdf g"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	
	$pdf->SetXY(47,151.9+$alto*7);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(62, $alto,html_entity_decode("8. Agredir a un efectivo policial u otra autoridad"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen-1);
	$pdf->MultiCell(25, $alto,html_entity_decode("12:25 hrs."), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(56, $alto,html_entity_decode("dfgsdfg dsfg sdfg sdfg sdf gsdf g"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	
	$pdf->SetXY(47,151.9+$alto*8);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(62, $alto,html_entity_decode("9. Presunta comisi&oacute;n del delito contra la fe p&uacute;blica (uso de documento p&uacute;blico falsificado) en agravio del Estado"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen-1);
	$pdf->MultiCell(25, $alto,html_entity_decode("12:25 hrs."), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(56, $alto,html_entity_decode("dfgsdfg dsfg sdfg sdfg sdf gsdf g"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	
	$pdf->SetXY(47,151.9+$alto*9);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(62, $alto,html_entity_decode("10. Discriminaci&oacute;n e incitaci&oacute;n a la discriminaci&oacute;n"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen-1);
	$pdf->MultiCell(25, $alto,html_entity_decode("12:25 hrs."), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(56, $alto,html_entity_decode("dfgsdfg dsfg sdfg sdfg sdf gsdf g"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	
	$pdf->SetXY(47,151.9+$alto*10);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(62, $alto,html_entity_decode("11. Violaci&oacute;n de medidas sanitarias"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen-1);
	$pdf->MultiCell(25, $alto,html_entity_decode("12:25 hrs."), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(56, $alto,html_entity_decode("dfgsdfg dsfg sdfg sdfg sdf gsdf g"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	
	$pdf->SetXY(47,151.9+$alto*11);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(62, $alto,html_entity_decode("12. Otros"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen-1);
	$pdf->MultiCell(25, $alto,html_entity_decode("12:25 hrs."), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(56, $alto,html_entity_decode("dfgsdfg dsfg sdfg sdfg sdf gsdf g"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
		
	$pdf->SetXY(20,276.5);
	$pdf->SetFont('arialn', '',10);
	$pdf->Cell(170, 8, "1", 0, 0, 'R', 0, '', 0, false, 'T', 'M');
	
	$pdf->AddPage();
	
	$pdf->SetXY(20,20);
	$pdf->SetFont('arialn', '',12);
	$pdf->Cell(9, 18, " ", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '',8);
	$pdf->MultiCell(18, 18,html_entity_decode("Lugar de intervenci&oacute;n o detenci&oacute;n"), 1, 'C', 1, 0, '', '', true, 0, false, false, 18, 'M');
	$pdf->MultiCell(143, 18,"\nDetalle", 1, 'L', 0, 0, '', '', true, 0, false, false, 18, 'T');
	
	$pdf->SetXY(20,38);
	$pdf->SetFont('arialn', '',12);
	$pdf->Cell(9, 65, "3", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', 'B',9);
	$pdf->MultiCell(161,7 ,html_entity_decode("Accionar del Ministerio P&uacute;blico"), 1, 'C', 1, 0, '', '', true, 0, false, false, 7, 'M');
	
	$pdf->SetXY(29,45);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(161,16 ,html_entity_decode("Situaci&oacute;n jur&iacute;dica de los intervenidos o detenidos: cuando la polic&iacute;a interviene o detiene a una persona por los hechos descritos anteriormente, su situaci&oacute;n es detenci&oacute;n preliminar. Posteriormente, despu&eacute;s de que los intervenidos o detenidos rinden sus manifestaciones el fiscal puede disponer su libertad en calidad de citados (libertad con citaci&oacute;n) o puede formalizar denuncia penal por uno o m&aacute;s delitos tipificados en el C&iacute;digo Penal.\n"), 1, 'J', 0, 0, '', '', true, 0, false, false, 16, 'M');
	
	$pdf->SetXY(29,61);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(20.125,18 ,html_entity_decode("Detenci&oacute;n preliminar"), 1, 'C', 1, 0, '', '', true, 0, false, false, 18, 'M');
	$pdf->MultiCell(20.125,18 ,html_entity_decode("Libertad con citaci&oacute;n"), 1, 'C', 1, 0, '', '', true, 0, false, false, 18, 'M');
	$pdf->MultiCell(20.125,18 ,html_entity_decode("Proceso inmediato"), 1, 'C', 1, 0, '', '', true, 0, false, false, 18, 'M');
	$pdf->MultiCell(20.125,18 ,html_entity_decode("Formalizaci&oacute;n de la investigaci&oacute;n"), 1, 'C', 1, 0, '', '', true, 0, false, false, 18, 'M');
	$pdf->MultiCell(60.375,4 ,html_entity_decode("Salidas alternativas"), 1, 'C', 1, 0, '', '', true, 0, false, false, 4, 'M');
	$pdf->MultiCell(20.125,18 ,html_entity_decode("Archivo"), 1, 'C', 1, 0, '', '', true, 0, false, false, 18, 'M');
	
	$pdf->SetXY(109.5,65);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(20.125,14 ,html_entity_decode("Principio de oportunidad"), 1, 'C', 1, 0, '', '', true, 0, false, false, 14, 'M');
	$pdf->MultiCell(20.125,14 ,html_entity_decode("Terminaci&oacute;n anticipada"), 1, 'C', 1, 0, '', '', true, 0, false, false, 14, 'M');
	$pdf->MultiCell(20.125,14 ,html_entity_decode("Acuerdos reparatorios"), 1, 'C', 1, 0, '', '', true, 0, false, false, 14, 'M');
	
	$pdf->SetXY(29,79);
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(20.125,24 ,html_entity_decode("X"), 1, 'C', 0, 0, '', '', true, 0, false, false, 24, 'M');
	$pdf->MultiCell(20.125,24 ,html_entity_decode("X"), 1, 'C', 0, 0, '', '', true, 0, false, false, 24, 'M');
	$pdf->MultiCell(20.125,24 ,html_entity_decode("X"), 1, 'C', 0, 0, '', '', true, 0, false, false, 24, 'M');
	$pdf->MultiCell(20.125,24 ,html_entity_decode("X"), 1, 'C', 0, 0, '', '', true, 0, false, false, 24, 'M');
	$pdf->MultiCell(20.125,24 ,html_entity_decode("X"), 1, 'C', 0, 0, '', '', true, 0, false, false, 24, 'M');
	$pdf->MultiCell(20.125,24 ,html_entity_decode("X"), 1, 'C', 0, 0, '', '', true, 0, false, false, 24, 'M');
	$pdf->MultiCell(20.125,24 ,html_entity_decode("X"), 1, 'C', 0, 0, '', '', true, 0, false, false, 24, 'M');
	$pdf->MultiCell(20.125,24 ,html_entity_decode("X"), 1, 'C', 0, 0, '', '', true, 0, false, false, 24, 'M');
	
	$pdf->SetXY(20,103);
	$pdf->SetFont('arialn', '',12);
	$pdf->Cell(9, 133, "4", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(27, 133,html_entity_decode("Tipo de delito o infracci&oacute;n"), 1, 'C', 1, 0, '', '', true, 0, false, false, 133, 'M');
	$pdf->MultiCell(113.9,7 ,html_entity_decode("Presuntos delitos"), 1, 'C', 1, 0, '', '', true, 0, false, false, 7, 'M');
	$pdf->MultiCell(20.125,7 ,html_entity_decode("Marcar con \"X\""), 1, 'C', 1, 0, '', '', true, 0, false, false, 7, 'M');
	
	$alto=9.3;
	
	$pdf->SetXY(56,110);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(113.9, $alto,html_entity_decode("1. Perturbaci&oacute;n o impedimento del proceso electoral (art&iacute;culo 354&deg; del Código Penal)"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(20.125, $alto,html_entity_decode("X"), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	
	$pdf->SetXY(56,110+$alto*1);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(113.9, $alto,html_entity_decode("2. Impedimento del derecho de sufragio (art&iacute;culo 355º del C&oacute;digo Penal)"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(20.125, $alto,html_entity_decode("X"), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	
	$pdf->SetXY(56,110+$alto*2);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(113.9, $alto,html_entity_decode("3. Inducci&oacute;n al no votar o hacerlo en sentido determinado (art&iacute;culo 356º del C&oacute;digo Penal)"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(20.125, $alto,html_entity_decode("X"), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	
	$pdf->SetXY(56,110+$alto*3);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(113.9, $alto,html_entity_decode("4. Suplantaci&oacute;n de votante (art&iacute;culo 357º del C&oacute;digo Penal)"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(20.125, $alto,html_entity_decode("X"), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	
	$pdf->SetXY(56,110+$alto*4);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(113.9, $alto,html_entity_decode("5. Publicidad ilegal del sentido del voto (art&iacute;culo 358º del C&oacute;digo Penal)"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(20.125, $alto,html_entity_decode("X"), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	
	$pdf->SetXY(56,110+$alto*5);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(113.9, $alto,html_entity_decode("6. Atentados contra el derecho de sufragio (art&iacute;culo 359º del C&oacute;digo Penal)"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(20.125, $alto,html_entity_decode("X"), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	
	$pdf->SetXY(56,110+$alto*6);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(113.9, $alto,html_entity_decode("7. Violencia contra la autoridad para impedir el ejercicio de sus funciones (art&iacute;culo 366° del C&oacute;digo Penal)"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(20.125, $alto,html_entity_decode("X"), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	
	$pdf->SetXY(56,110+$alto*7);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(113.9, $alto,html_entity_decode("8. Voto fraudulento (art&iacute;culo 386º de la Ley Org&aacute;nica de Elecciones)"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(20.125, $alto,html_entity_decode("X"), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	
	$pdf->SetXY(56,110+$alto*8);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(113.9, $alto,html_entity_decode("9. Realizar propaganda electoral en horas en que &eacute;sta estaba suspendida (art&iacute;culo 389º de la Ley Org&aacute;nica de Elecciones)"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(20.125, $alto,html_entity_decode("X"), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	
	$pdf->SetXY(56,110+$alto*9);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(113.9, $alto,html_entity_decode("10. Incumplir la “ley seca” (art&iacute;culo 351° y 390º inciso a) de la Ley Org&aacute;nica de Elecciones)"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(20.125, $alto,html_entity_decode("X"), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	
	$pdf->SetXY(56,110+$alto*10);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(113.9, $alto,html_entity_decode("11. Discriminaci&oacute;n e incitaci&oacute;n a la discriminaci&oacute;n (art&iacute;culo 323º del C&oacute;digo Penal)"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(20.125, $alto,html_entity_decode("X"), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	
	$pdf->SetXY(56,110+$alto*11);
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(113.9, $alto,html_entity_decode("12. Violaci&oacute;n de medidas sanitarias (art&iacute;culo 292° del C&oacute;digo Penal)"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(20.125, $alto,html_entity_decode("X"), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
		
	$pdf->SetXY(56,110+$alto*12);
	$pdf->SetFont('arialn', '',9);
	$alto+=5.1;
	$pdf->MultiCell(113.9, $alto,html_entity_decode("13. Otros delitos (detallar)"), 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(20.125, $alto,html_entity_decode("X"), 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	
	$pdf->SetXY(20,236);
	$pdf->SetFont('arialn', '',12);
	$pdf->Cell(9, 33, "5", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '',9);
	$pdf->MultiCell(27, 33,html_entity_decode("Detalle de los\nhechos"), 1, 'C', 1, 0, '', '', true, 0, false, false, 33, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen-2);
	$pdf->MultiCell(134.025,33 ,html_entity_decode("Presuntos delitos"), 1, 'L', 0, 0, '', '', true, 0, false, false, 33, 'M');
	//$pdf->MultiCell(20.125,7 ,html_entity_decode("Marcar con \"X\""), 1, 'C', 1, 0, '', '', true, 0, false, false, 7, 'M');
	
	
	
	$pdf->SetXY(20,268);
	$pdf->SetFont('arialn', 'B',9);
	$pdf->Cell(11, 9, "Anexar:", 0, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '',10);
	$pdf->MultiCell(155, 9,"Copia simple de la denuncia y/o parte policial", 0, 'L', 0, 0, '', '', true, 0, false, false, 9, 'M');
	
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
