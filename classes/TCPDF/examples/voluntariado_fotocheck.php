<?php

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');
//require_once '../../Db.class.php';
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

// set font

$Db = new Db();
	
    switch($_POST['busq_tipo'])
    {       
    	case 1:	$parametro="nomb_volu like :m_busq"; break;
	    case 2:	$parametro="docu_volu=:m_busq"; break;
    }

$result=$Db->query("select * from mp_voluntariado where $parametro AND anno_volu=:m_anno order by nomb_volu asc",[':m_busq'=>$_POST['busq_dato'],':m_anno'=>$_POST['anno_volu']]);

$cont=0;
$arra_fila[1]=10;
$arra_colu[1]=10;
$arra_fila[2]=105.5;
$arra_colu[2]=10;
$arra_fila[3]=10;
$arra_colu[3]=70;
$arra_fila[4]=105.5;
$arra_colu[4]=70;
$arra_fila[5]=10;
$arra_colu[5]=130;
$arra_fila[6]=105.5;
$arra_colu[6]=130;
$arra_fila[7]=10;
$arra_colu[7]=190;
$arra_fila[8]=105.5;
$arra_colu[8]=190;

$pdf->AddPage(); 

foreach($result as $rows)
{
   $veri="chek_pers_".$rows['codi_volu'];
   //if(file_exists("fotos_voluntariado/".$rows['docu_volu'].".jpg") AND $rows['habi_impr']==1 AND $rows['esta_impr']==0 AND ($_POST['todo_chek']==1 OR ($_POST['todo_chek']==2 AND $_POST[$veri])) OR 5==5)
   if(file_exists("fotos_voluntariado/".$rows['docu_volu'].".jpg") AND $rows['habi_impr']==1 AND $rows['esta_impr']==0 AND ($_POST['todo_chek']==1 OR ($_POST['todo_chek']==2 AND $_POST[$veri])))
   {
    $cont++;
    if($cont>8)
    {
        $cont=1;
        $pdf->AddPage();
    }
	$pdf->SetFont('helvetica', '', 11);
	
	//Para marcar los que se impriman
	//$result_impr=$Db->update($nomb_tabl,['esta_impr'=>'1'],['codi_pers'=>$rows['codi_pers']]);

	// add a page
	
	
	$fila=$arra_fila[$cont];
	$colu=$arra_colu[$cont];

	$pdf->Image("images/voluntariado_front.jpg", $fila, $colu, 95, 60, 'JPEG', '', 'M', false, 300, '', false, false, 0, false, false, false);
	
	$file = "fotos_voluntariado/".$rows['docu_volu'].".jpg"; // Dirección de la imagen.
	$imagen = getimagesize($file); //Sacamos la información.
	$ancho = $imagen[0]; //Ancho.
	$alto = $imagen[1]; //Alto.
	$parametro=$alto/$ancho;
	$ancho_nuevo=15.5;
	$alto_nuevo=$ancho_nuevo*$parametro;
	$aumento=21-$alto_nuevo;
	$pdf->Image($file, 11+$fila, 9+$colu+$aumento, 19.5, '', 'JPEG', '', 'T', false, 300, '', false, false, 0, false, false, false);
	
	    $pdf->Image("images/front_foto5.png", 10+$fila, 8+$colu, 21.5, 28.5, '', '', '', false, 300);

//echo"<HR>$ancho - $alto - $parametro - $alto_nuevo<HR>";
//die();
//$rows['pers_nombres']="MMMMMMMMMMM MMMMMMMMMMMMMMMMMMMMMMM";

	//$pdf->SetTextColor(45, 53, 89);
	$rows['nomb_volu']=utf8_encode(utf8_decode(substr($rows['nomb_volu'],0,50)));
	if(strlen($rows['nomb_volu'])<20)
		$pdf->SetFont('helvetica', '', 11);
	else
	{
		if(strlen($rows['nomb_volu'])<22)
			$pdf->SetFont('helvetica', '', 10);
		else
		{
			if(strlen($rows['nomb_volu'])<24)
				$pdf->SetFont('helvetica', '', 9);
			else
				$pdf->SetFont('helvetica', '', 8);
		}
	}

	$pdf->SetXY(35+$fila,10+$colu);
	$pdf->Cell(50, 7, strtoupper($rows['nomb_pers']), 0, false, 'L', 0, '', 0, false, 'T', 'M');

	$apellidos=utf8_encode(utf8_decode(substr($rows['nomb_volu'],0,50)));
	
	//if(strlen($apellidos)>25)
	//    $apellidos=substr($apellidos,0,strrpos($apellidos," "));
	    
	if(strlen($apellidos)<20)
		$pdf->SetFont('helvetica', 'B', 10);
	else
	{
		if(strlen($apellidos)<22)
			$pdf->SetFont('helvetica', 'B', 10);
		else
		{
			if(strlen($apellidos)<24)
				$pdf->SetFont('helvetica', 'B', 9);
			else
				$pdf->SetFont('helvetica', 'B', 8);
		}
	}
	
	
		
	$pdf->SetXY(33+$fila,15+$colu);
	//$pdf->SetTextColor(45, 53, 89);
	$pdf->Cell(61, 7, $apellidos, 0, false, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(43+$fila,22.5+$colu);
	$pdf->SetTextColor(255, 255, 255);
	$pdf->SetFont('helvetica', '', 9);
	$pdf->Cell(50, 7, $rows['docu_volu'], 0, false, 'L', 0, '', 0, false, 'T', 'M');
	$pdf->SetTextColor(0,0,0);
	

	// print a message
	//$txt = "You can also export 1D barcodes in other formats (PNG, SVG, HTML). Check the examples inside the barcodes directory.\n";
	$pdf->SetY(43);

	// -----------------------------------------------------------------------------

	$pdf->SetFont('helvetica', '', 10);
	//$pdf->SetTextColor(45, 53, 89);

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
		'text' => false,
		'font' => 'helvetica',
		'fontsize' => 8,
		'stretchtext' => 4
	);

	// PRINT VARIOUS 1D BARCODES

	// CODE 128 AUTO
	$pdf->write1DBarcode($rows['docu_volu'], 'C128',1+$fila,53+$colu, '', 8, 0.4, $style, 'N');
	    
	    
	    
	    
	    


   }
}

//Close and output PDF document
$pdf->Output('example_027.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
