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
$pdf = new MYPDF("L", PDF_UNIT, "FCPJ", true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$margin_left=5;
$margin_right=5;
$margin_top=5;
$margin_bottom=5;
$margin_header=5;
$margin_footer=5;
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

// set font

$Db = new Db();
	
    $buscar_depe="";
        if($_POST['codi_depe'])
                $buscar_depe=" AND codi_depe='".$_POST['codi_depe']."'";
    switch($_POST['busq_tipo'])
    {       
    	case 1:	$parametro="CONCAT(appe_pers,' ',nomb_pers) like :m_busq $buscar_depe"; break;
	    case 2:	$parametro="ndni_pers=:m_busq $buscar_depe"; break;
    }

    switch($_POST['codi_form'])
	{
	    case 1: $nomb_tabl="mp_fotocheck_personal";  
	            $parametro.=" AND codi_carg NOT IN (16,17,18,19,20)";
	            break;
	    case 2: $nomb_tabl="mp_fotocheck_personal"; 
	            $parametro.=" AND codi_carg IN (16,17,18,19,20)";
	            break;
	    case 3: $nomb_tabl="mp_fotocheck_secigra";  
	            break;
	}


$result=$Db->query("select * from mp_maes_fotocheck_cargo");
foreach($result as $rows)
	//echo $rows['x_nombre']."<HR>";
	//die();
	$arra_carg[$rows['n_codigo']]=$rows['x_nombre'];
	//$arra_carg[22]="Jefe";
$result=$Db->query("select * from mp_maes_fotocheck_dependencia");
foreach($result as $rows)
	$arra_depe[$rows['n_codigo']]=$rows['x_nombre'];
$result=$Db->query("select * from mp_maes_fotocheck_rlaboral");
foreach($result as $rows)
	$arra_moda[$rows['n_codigo']]=$rows['x_nombre'];
	
$result=$Db->query("select * from $nomb_tabl where $parametro AND esta_pers=1 order by appe_pers,nomb_pers asc",[':m_busq'=>$_POST['busq_dato']]);
        
foreach($result as $rows)
{
   $veri="chek_pers_".$rows['codi_pers'];
   if(file_exists("fotos/".$rows['ndni_pers'].".jpg") AND $rows['habi_impr']==1 AND $rows['esta_impr']==0 AND ($_POST['todo_chek']==1 OR ($_POST['todo_chek']==2 AND $_POST[$veri])))
   {
	$pdf->SetFont('helvetica', '', 11);
	
	//Para marcar los que se impriman
	//$result_impr=$Db->update($nomb_tabl,['esta_impr'=>'1'],['codi_pers'=>$rows['codi_pers']]);

	// add a page
	$pdf->AddPage();

    switch($_POST['codi_form'])
    {
        case 1: $imag_fron="images/front.jpg";
                break;
        case 2: $imag_fron="images/front2.jpg";
                break;
        case 3: $imag_fron="images/front3.jpg";
                break;
        default:$imag_fron="images/front.jpg";
    }
	$pdf->Image($imag_fron, 2, 2, 81, 50, 'JPEG', '', 'M', false, 300, '', false, false, 0, false, false, false);
	
	$file = "fotos/".$rows['ndni_pers'].".jpg"; // Dirección de la imagen.
	$imagen = getimagesize($file); //Sacamos la información.
	$ancho = $imagen[0]; //Ancho.
	$alto = $imagen[1]; //Alto.
	$parametro=$alto/$ancho;
	$ancho_nuevo=15.5;
	$alto_nuevo=$ancho_nuevo*$parametro;
	$aumento=21-$alto_nuevo;
	$pdf->Image($file, 7, 23+$aumento, 15.5, '', 'JPEG', '', 'T', false, 300, '', false, false, 0, false, false, false);
	if($_POST['codi_form']==2)  //si es fiscal
	    $pdf->Image("images/front_foto6.png", 6, 22, 17.5, 23, '', '', '', false, 300);
	else
	    $pdf->Image("images/front_foto5.png", 6, 22, 17.5, 23, '', '', '', false, 300);

//echo"<HR>$ancho - $alto - $parametro - $alto_nuevo<HR>";
//die();
//$rows['pers_nombres']="MMMMMMMMMMM MMMMMMMMMMMMMMMMMMMMMMM";

	$pdf->SetTextColor(45, 53, 89);
	$rows['nomb_pers']=utf8_encode(utf8_decode(substr($rows['nomb_pers'],0,27)));
	if(strlen($rows['nomb_pers'])<20)
		$pdf->SetFont('helvetica', '', 11);
	else
	{
		if(strlen($rows['nomb_pers'])<22)
			$pdf->SetFont('helvetica', '', 10);
		else
		{
			if(strlen($rows['nomb_pers'])<24)
				$pdf->SetFont('helvetica', '', 9);
			else
				$pdf->SetFont('helvetica', '', 8);
		}
	}

	$pdf->SetXY(25,21);
	$pdf->Cell(50, 7, strtoupper($rows['nomb_pers']), 0, false, 'L', 0, '', 0, false, 'T', 'M');

	$apellidos=utf8_encode(utf8_decode(substr(strtoupper($rows['appe_pers']),0,27)));
	if(strlen($apellidos)<20)
		$pdf->SetFont('helvetica', 'B', 11);
	else
	{
		if(strlen($apellidos)<22)
			$pdf->SetFont('helvetica', '', 10);
		else
		{
			if(strlen($apellidos)<24)
				$pdf->SetFont('helvetica', '', 9);
			else
				$pdf->SetFont('helvetica', '', 8);
		}
	}
		
	$pdf->SetXY(25,25.5);
	$pdf->SetTextColor(45, 53, 89);
	$pdf->Cell(50, 7, strtoupper($apellidos), 0, false, 'L', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(25,29.5);
	$pdf->SetTextColor(77, 77, 77);
	$pdf->SetFont('helvetica', '', 8);
	$pdf->Cell(50, 7, "DNI: ".$rows['ndni_pers'], 0, false, 'L', 0, '', 0, false, 'T', 'M');

//$rows['codi_carg']=1;
//$arra_carg[22]="Asistente en Función Fiscal";

//print_r($arra_carg);
//echo"<HR>".$arra_carg[$rows['codi_carg']]."<HR>";

	$pdf->SetFont('helvetica', 'B', 11);
	//$pdf->SetTextColor(0, 0, 0, 100);
	$pdf->SetTextColor(45, 53, 89);
	$pdf->SetXY(25,35.7);
	if($_POST['codi_form']==3)  //si es Secigra
	    $pdf->Cell(50, 7,"Secigrista", 0, false, 'L', 0, '', 0, false, 'T', 'M');
	else
	    $pdf->Cell(50, 7,utf8_encode(utf8_decode($arra_carg[$rows['codi_carg']])), 0, false, 'L', 0, '', 0, false, 'T', 'M');
	
//$rows['codi_depe']=1;
//$arra_depe[22]="Mesa de Partes de la Fiscalia Prov. Corp. Espec. en Delitos de Corrupcion de Funcionarios de Arequipa";
	$pdf->SetFont('helvetica', '', 8);
	$pdf->SetTextColor(77, 77, 77);
	$pdf->SetXY(25,41.5);
	$pdf->setCellHeightRatio(1);
	if($_POST['codi_form']==3)  //si es Secigra
	    $pdf->MultiCell(57, 7,html_entity_decode("C&oacute;digo: ".$rows['codi_adic']), 1, 'L', 1, 0, '', '', true);
	else
	    $pdf->MultiCell(57, 7,utf8_encode(utf8_decode($arra_depe[$rows['codi_depe']])), 1, 'L', 1, 0, '', '', true);

	$pdf->AddPage();
	
	$imag_back="images/back.jpg";
	switch($_POST['codi_form'])
    {
        case 1: $imag_back="images/back.jpg";
                break;
        case 2: $imag_back="images/back2.jpg";
                break;
        case 3: $imag_back="images/back3.jpg";
                break;
        default:$imag_back="images/back.jpg";
    }
	
	$pdf->Image($imag_back, 2, 2, 81, 50, 'JPEG', '', 'M', false, 300, '', false, false, 0, false, false, false);

	$pdf->SetFont('helvetica', 'B', 9);
	//$pdf->SetTextColor(0, 0, 0, 100);
	$pdf->SetTextColor(45, 53, 89);
	$pdf->SetXY(17.5,9.5);
	if($_POST['codi_form']==3)  //si es Secigra
	    $pdf->Cell(50, 7,"Programa Secigra ".date(Y), 0, false, 'C', 0, '', 0, false, 'T', 'M');
	else
	    $pdf->Cell(50, 7,"Reg. Laboral: ".utf8_encode($arra_moda[$rows['codi_regi']]), 0, false, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetFont('helvetica', '', 7.5);
	$pdf->SetTextColor(77,77,77);
	$pdf->SetXY(17.5,13.5);
	$pdf->Cell(50, 7,utf8_encode("Este documento es personal e intransferible, en caso"), 0, false, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetXY(17.5,16.5);
	$pdf->Cell(50, 7,utf8_encode(utf8_decode("de hallazgo, comunicarse al teléfono: (054) 212311")), 0, false, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetXY(17.5,19.5);
	$pdf->Cell(50, 7,utf8_encode("Anexo 2211"), 0, false, 'C', 0, '', 0, false, 'T', 'M');

	$pdf->SetFont('helvetica', '', 7);

	$pdf->SetXY(28,34);
	$pdf->Cell(15, 3, $rows['esca_pers'], 0, false, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetXY(6.5,40.3);
	$pdf->Cell(15, 3,$rows['marc_pers'], 0, false, 'L', 0, '', 0, false, 'T', 'M');

	// print a message
	//$txt = "You can also export 1D barcodes in other formats (PNG, SVG, HTML). Check the examples inside the barcodes directory.\n";
	$pdf->SetY(43);

	// -----------------------------------------------------------------------------

	$pdf->SetFont('helvetica', '', 10);
	$pdf->SetTextColor(45, 53, 89);

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
		'fgcolor' => array(45,53,89),
		'bgcolor' => false, //array(255,255,255),
		'text' => true,
		'font' => 'helvetica',
		'fontsize' => 8,
		'stretchtext' => 4
	);

	// PRINT VARIOUS 1D BARCODES

	// CODE 128 AUTO
	$pdf->write1DBarcode($rows['ndni_pers'], 'C128', '23', '24', '', 17, 0.4, $style, 'N');
	
	switch($_POST['codi_form'])
	{
	    case 1: break;
	    case 2: //fiscal
                $pdf->SetFont('helvetica', 'B', 8);
	            //$pdf->SetTextColor(0, 0, 0, 100);
	            $pdf->SetTextColor(45, 53, 89);
	            $pdf->SetXY(17.5,45.5);
	            $pdf->Cell(50, 7,html_entity_decode("Uso de Car&aacute;cter Oficial"), 0, false, 'C', 0, '', 0, false, 'T', 'M');
	            break;
	    case 3: //secigrista
	            $pdf->SetFont('helvetica', '', 8);
	            //$pdf->SetTextColor(0, 0, 0, 100);
	            $pdf->SetTextColor(0, 0, 0);
	            $pdf->SetXY(3,45);
	            $pdf->Cell(50, 7,html_entity_decode("Caduca: Nov - ".date("Y")), 0, false, 'L', 0, '', 0, false, 'T', 'M');
	            break;
        default: break;
	}


	// ---------------------------------------------------------
   }
}

//Close and output PDF document
$pdf->Output('example_027.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
