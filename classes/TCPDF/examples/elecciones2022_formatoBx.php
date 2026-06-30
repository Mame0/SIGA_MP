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
	$pdf->MultiCell(26.2, 13,"25/09/2022", 1, 'C', 0, 0, '', '', true, 0, false, false, 13, 'M');
	$pdf->SetFont('arialn', '', 9);
	$pdf->Cell(16.2, 13, "Hora", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(26.2, 13,"07:08 hrs.", 1, 'C', 0, 0, '', '', true, 0, false, false, 13, 'M');
	$pdf->SetFont('arialn', '', 9);
	$pdf->Cell(85.2, 4, "Tipo", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(104.8,75);
	$pdf->Cell(21.2, 9, "Alerta", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(21.2, 9,"X", 1, 'C', 0, 0, '', '', true, 0, false, false, 9, 'M');
	$pdf->SetFont('arialn', '', 9);
	$pdf->Cell(21.2, 9, "Ocurrencia", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(21.2, 9,"X", 1, 'C', 0, 0, '', '', true, 0, false, false, 9, 'M');
	
	$pdf->SetXY(20,87);
	$pdf->SetFont('arialn', '',12);
	$pdf->Cell(9, 54, "1", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '',8);
	$pdf->Cell(53, 4, "Distrito Fiscal", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(54, 4, "Provincia", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(54, 4, "Distrito", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(29,90.3);
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(53, 9.5,"07:08 hrs.", 1, 'C', 0, 0, '', '', true, 0, false, false, 9.5, 'M');
	$pdf->MultiCell(54, 9.5,"07:08 hrs.", 1, 'C', 0, 0, '', '', true, 0, false, false, 9.5, 'M');
	$pdf->MultiCell(54, 9.5,"07:08 hrs.", 1, 'C', 0, 0, '', '', true, 0, false, false, 9.5, 'M');
	
	$pdf->SetXY(29,99.8);
	$pdf->SetFont('arialn', '',8);
	$pdf->Cell(161, 4, "Lugar", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(29,103.8);
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(161, 9.5,"07:08 hrs.", 1, 'C', 0, 0, '', '', true, 0, false, false, 9.5, 'M');
	
	$pdf->SetXY(29,113.3);
	$pdf->SetFont('arialn', '',8);
	$pdf->Cell(80.5, 4, html_entity_decode("Fiscal que reporta"), 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(80.5, 4, html_entity_decode("Fiscal&iacute;a"), 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(29,117.3);
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(80.5, 9.5,"07:08 hrs.", 1, 'C', 0, 0, '', '', true, 0, false, false, 9.5, 'M');
	$pdf->MultiCell(80.5, 9.5,"07:08 hrs.", 1, 'C', 0, 0, '', '', true, 0, false, false, 9.5, 'M');
	
	$pdf->SetXY(29,126.8);
	$pdf->SetFont('arialn', '',8);
	$pdf->Cell(80.5, 4, html_entity_decode("Tel&eacute;fono"), 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(80.5, 4, html_entity_decode("Correo Electr&oacute;nico"), 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(29,130.8);
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->MultiCell(80.5, 10.2,"07:08 hrs.", 1, 'C', 0, 0, '', '', true, 0, false, false, 10.2, 'M');
	$pdf->MultiCell(80.5, 10.2,"07:08 hrs.", 1, 'C', 0, 0, '', '', true, 0, false, false, 10.2, 'M');
	
	$pdf->SetXY(20,141);
	$pdf->SetFont('arialn', '',12);
	$pdf->Cell(9, 69, "2", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '',8);
	$pdf->MultiCell(18, 69,"Tipo de alerta u ocurrencia", 1, 'C', 1, 0, '', '', true, 0, false, false, 69, 'M');
	$pdf->Cell(120, 4, "Tipo", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(23, 4, "Marcar con \"X\"", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,145);
	$pdf->SetFont('arialn', '',9);
	$pdf->Cell(120, 5, html_entity_decode("1. Destrucci&oacute;n de material electoral (&aacute;nforas, actas o c&eacute;dulas de votaci&oacute;n, etc.)"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->Cell(23, 5, "X", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,150);
	$pdf->SetFont('arialn', '',9);
	$pdf->Cell(120, 5, html_entity_decode("2. Disturbios debido a problemas en la instalaci&oacute;n de mesas"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->Cell(23, 5, "X", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,155);
	$pdf->SetFont('arialn', '',9);
	$pdf->Cell(120, 5, html_entity_decode("3. Difusi&oacute;n de propaganda pol&iacute;tica durante el mismo d&iacute;a de las elecciones"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->Cell(23, 5, "X", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,160);
	$pdf->SetFont('arialn', '',9);
	$pdf->Cell(120, 5, html_entity_decode("4. Enfrentamientos entre agrupaciones pol&iacute;ticas"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->Cell(23, 5, "X", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,165);
	$pdf->SetFont('arialn', '',9);
	$pdf->Cell(120, 5, html_entity_decode("5. Presencia de \"votos golondrinos\""), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->Cell(23, 5, "X", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,170);
	$pdf->SetFont('arialn', '',9);
	$pdf->Cell(120, 5, html_entity_decode("6. Toma de centros de votaci&oacute;n con actos violentos"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->Cell(23, 5, "X", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,175);
	$pdf->SetFont('arialn', '',9);
	$pdf->Cell(120, 5, html_entity_decode("7. Impedimento que el personal electoral se lleve las actas o las &aacute;nforas de los centros de votaci&oacute;n"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->Cell(23, 5, "X", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,180);
	$pdf->SetFont('arialn', '',9);
	$pdf->Cell(120, 5, html_entity_decode("8. Suplantaci&oacute;n de identidad"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->Cell(23, 5, "X", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,185);
	$pdf->SetFont('arialn', '',9);
	$pdf->Cell(120, 5, html_entity_decode("9. Incautaci&oacute;n del material electoral presuntamente falso o adulterado"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->Cell(23, 5, "X", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,190);
	$pdf->SetFont('arialn', '',9);
	$pdf->Cell(120, 5, html_entity_decode("10. Intervenciones a personas portando documentos presuntamente falsos"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->Cell(23, 5, "X", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,195);
	$pdf->SetFont('arialn', '',9);
	$pdf->Cell(120, 5, html_entity_decode("11. Discriminaci&oacute;n e incitaci&oacute;n a la discriminaci&oacute;n"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->Cell(23, 5, "X", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,200);
	$pdf->SetFont('arialn', '',9);
	$pdf->Cell(120, 5, html_entity_decode("12. Violaci&oacute;n de medidas sanitarias"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->Cell(23, 5, "X", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,205);
	$pdf->SetFont('arialn', '',9);
	$pdf->Cell(120, 5, html_entity_decode("13. Otros"), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->Cell(23, 5, "X", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(20,210);
	$pdf->SetFont('arialn', '',12);
	$pdf->Cell(9, 67, "3", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '',8);
	$pdf->MultiCell(18, 69,"Detalle de los hechos", 1, 'C', 1, 0, '', '', true, 0, false, false, 69, 'M');
	$pdf->MultiCell(143, 69,"\nDetalle de los hechos", 1, 'L', 0, 0, '', '', true, 0, false, false, 69, 'T');
	
	
	
	
	
	$pdf->SetXY(20,276.5);
	$pdf->SetFont('arialn', '',10);
	$pdf->Cell(170, 8, "1", 0, 0, 'R', 0, '', 0, false, 'T', 'M');
	
	$pdf->AddPage();
	
	$pdf->SetXY(20,20);
	$pdf->SetFont('arialn', '',12);
	$pdf->Cell(9, 53, "4", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '',8);
	$pdf->MultiCell(15, 53,"Accionar fiscal", 1, 'C', 1, 0, '', '', true, 0, false, false, 53, 'M');
	$pdf->MultiCell(146, 53,"\nDetalle", 1, 'L', 0, 0, '', '', true, 0, false, false, 53, 'T');
	
	$pdf->SetXY(20,73);
	$pdf->SetFont('arialn', '',12);
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
	
for($x=0;$x<20;$x++)
{
	$pdf->SetXY(44,81+$alto*$x);
	$pdf->MultiCell(14, $alto,"X", 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(14, $alto,"X", 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(8, $alto,"2", 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(59, $alto,"Ana Fabiola Maria Barrientos Espezua", 1, 'L', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(27, $alto,"29709217", 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(12, $alto,"M", 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
	$pdf->MultiCell(12, $alto,"23", 1, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
}	
	
	//$pdf->MultiCell(28, 4,html_entity_decode("Condici&oacute;n"), 1, 'C', 1, 0, '', '', true, 0, false, false, 28, 'M');
	//$pdf->MultiCell(8, 8,html_entity_decode("N&deg;"), 1, 'C', 1, 0, '', '', true, 0, false, false, 8, 'M');
	//$pdf->MultiCell(40, 8,html_entity_decode("Nombres y apellidos"), 1, 'C', 1, 0, '', '', true, 0, false, false, 8, 'M');
	
	$pdf->SetXY(20,268);
	$pdf->SetFont('arialn', 'B',9);
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
