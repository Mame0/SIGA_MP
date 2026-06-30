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

    $result=$Db->query("select codi_elec,nomb_elec from mp_elec_config WHERE habi_elec='1'");
	foreach($result as $rows)
	{
	    $_POST['nomb_elec']=$rows['nomb_elec'];
	    $_POST['codi_elec']=$rows['codi_elec'];
	}
    
    $result=$Db->query("select count(*) as cant_coor from mp_elec_coordinaciones where codi_elec='".$_POST['codi_elec']."' AND esta_coor='1'");
    $cant_coor=$result[0]['cant_coor'];
    
    $result=$Db->query("SELECT codi_tdif,count(*) as cant_difu FROM `mp_elec_difusion` where codi_elec='".$_POST['codi_elec']."' AND esta_difu='1' group by codi_tdif");
    foreach($result as $rows)
        $arra_cant_difu[$rows['codi_tdif']]=$rows['cant_difu'];
    
    $result=$Db->query("SELECT codi_tpre,count(*) as cant_prev FROM `mp_elec_prevencion` where codi_elec='".$_POST['codi_elec']."' AND esta_prev='1' group by codi_tpre");
    foreach($result as $rows)
        $arra_cant_prev[$rows['codi_tpre']]=$rows['cant_prev'];

    $result=$Db->query("SELECT codi_tale,aler_ocur,count(*) as cant_aler FROM `mp_elec_alertas` where codi_elec='".$_POST['codi_elec']."' AND esta_aler='1' group by codi_tale,aler_ocur");
    foreach($result as $rows)
        $arra_cant_aler[$rows['codi_tale']][$rows['aler_ocur']]=$rows['cant_aler'];
    
    $result=$Db->query("SELECT b.lesi_fall,b.sexo_lesi,count(*) as cant_lesi FROM mp_elec_alertas as a,mp_elec_alertas_lesionados as b where a.codi_aler=b.codi_aler AND a.codi_elec='".$_POST['codi_elec']."' AND a.esta_aler='1' AND b.esta_lesi group by b.lesi_fall,b.sexo_lesi");
    foreach($result as $rows)
        $arra_cant_lesi[$rows['lesi_fall']][$rows['sexo_lesi']]=$rows['cant_lesi'];

    $result=$Db->query("SELECT sexo_dete,count(*) as cant_dete FROM `mp_elec_detenciones` where codi_elec='".$_POST['codi_elec']."' AND esta_dete='1' AND dete_inte='1' AND edad_dete>17 group by sexo_dete");
    foreach($result as $rows)
        $arra_dete_mayo[$rows['sexo_dete']]=$rows['cant_dete'];
    
    $result=$Db->query("SELECT sexo_dete,count(*) as cant_dete FROM `mp_elec_detenciones` where codi_elec='".$_POST['codi_elec']."' AND esta_dete='1' AND dete_inte='2' AND edad_dete<18 group by sexo_dete");
    foreach($result as $rows)
        $arra_inte_meno[$rows['sexo_dete']]=$rows['cant_dete'];

//mp_elec_detenciones: codi_dete    codi_elec   codi_usua   dete_inte   fech_dete   ubig_dete   nomb_dete   ndni_dete   edad_dete   sexo_dete   codi_inte   hora_moti   deta_moti   codi_acci   codi_deli   luga_inte   deta_inte   digi_dete   fdig_dete   esta_dete
//mp_elec_alertas:             codi_aler	codi_elec	codi_usua	aler_ocur	codi_tale	fech_aler	ubig_aler	luga_aler	deta_aler	acci_aler	digi_aler	fdig_aler	esta_aler
//mp_elec_alertas_lesionados:  codi_lesi	codi_aler	lesi_fall	nomb_lesi	ndni_lesi	sexo_lesi	edad_lesi	digi_lesi	fdig_lesi	esta_lesi

    $result=$Db->query("select * from mp_admi_oper where iden_oper='".$_POST['iden_oper']."'");
    $_POST['nomb_fisc']=$result[0]['nomb_oper'].' '.$result[0]['appa_oper'].' '.$result[0]['apma_oper'];
    $_POST['nomb_depe']=$result[0]['depe_oper'];
    $_POST['tele_fisc']=$result[0]['celu_oper'];
    $_POST['mail_fisc']=$result[0]['mail_oper'];

    $fech_form=date("d/m/Y");
    $hora_form=date("H:i")." hrs.";

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

    

	$pdf->SetFont('arialn', 'B', 14);
    $pdf->SetXY(20,46);
	$pdf->Cell(170, 4, "FORMATO A", 0, 1, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetXY(20,51.5);
	$pdf->Cell(170, 4, "ACCIONAR FISCAL", 0, 1, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '', 11);
	$pdf->Cell(170, 4, html_entity_decode("ANTES, DURANTE Y DESPU&Eacute;S DE LAS ELECCIONES REGIONALES Y MUNICIPALES 2022"), 0, 1, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(170, 4, html_entity_decode("Ministerio P&uacute;blico - Fiscal&iacute;a de la Naci&oacute;n"), 0, 1, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->setCellHeightRatio(1);
	$pdf->SetFillColor(224,224,224);
	//$pdf->SetTextColor(77, 77, 77);
	
	//MultiCell(w, h, txt, border = 0, align = 'J', fill = 0, ln = 1, x = '', y = '', reseth = true, stretch = 0, ishtml = false, autopadding = true, maxh = 0)
	
	$pdf->SetXY(20,71);
	$pdf->SetFont('arialn', '', 9);
	$pdf->Cell(42.5, 11, "Fecha", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->MultiCell(42.5, 11,$fech_form, 1, 'C', 0, 0, '', '', true, 0, false, false, 11, 'M');
	$pdf->SetFont('arialn', '', 9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(42.5, 11, "Hora", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->MultiCell(42.5, 11,$hora_form, 1, 'C', 0, 0, '', '', true, 0, false, false, 11, 'M');
	
	$pdf->SetXY(20,85);
	$pdf->SetFont('arialn', '',12);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(9, 49, "1", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '',9);
	$pdf->Cell(80.5, 5.3, "Distrito Fiscal", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(80.5, 5.3, "Provincia", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(29,90.3);
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->MultiCell(80.5, 11,"Arequipa", 1, 'C', 0, 0, '', '', true, 0, false, false, 11, 'M');
	$pdf->MultiCell(80.5, 11,"Arequipa", 1, 'C', 0, 0, '', '', true, 0, false, false, 11, 'M');
	
	$pdf->SetXY(29,101.3);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(80.5, 5.3, "Fiscal que reporta", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(80.5, 5.3, html_entity_decode("Fiscal&iacute;a"), 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(29,106.6);
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->MultiCell(80.5, 11,$_POST['nomb_fisc'], 1, 'C', 0, 0, '', '', true, 0, false, false, 11, 'M');
	$pdf->MultiCell(80.5, 11,$_POST['nomb_depe'], 1, 'C', 0, 0, '', '', true, 0, false, false, 11, 'M');
	
	$pdf->SetXY(29,117.6);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(80.5, 5.3, html_entity_decode("Tel&eacute;fono"), 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(80.5, 5.3, html_entity_decode("Correo Electr&oacute;nico"), 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(29,123);
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->MultiCell(80.5, 11,$_POST['tele_fisc'], 1, 'C', 0, 0, '', '', true, 0, false, false, 11, 'M');
	$pdf->MultiCell(80.5, 11,$_POST['mail_fisc'], 1, 'C', 0, 0, '', '', true, 0, false, false, 11, 'M');
	
	$pdf->SetXY(20,134);
	$pdf->SetFont('arialn', '',12);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(9, 143, "2", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '',8);
	$pdf->MultiCell(18, 143,"Actuaciones Preventivas", 1, 'C', 1, 0, '', '', true, 0, false, false, 143, 'M');
	$pdf->Cell(35, 5, "Tipo", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(93, 5, "Detalle", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(15, 5, "Cantidad", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,139);
	$pdf->MultiCell(128,9.2,html_entity_decode("A. Coordinaci&oacute;n Interinstitucional con el JNE, ONPE, PNP, Fuerzas Armadas, etc."), 1, 'L', 0, 0, '', '', true, 0, false, false, 9.2, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(15, 9.2, number_format($cant_coor,0) , 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,148.2);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(35,46,html_entity_decode("B. Acci&oacute;n de difusi&oacute;n sensibilizaci&oacute;n y educaci&oacute;n"), 1, 'L', 0, 0, '', '', true, 0, false, false, 46, 'M');
	$pdf->MultiCell(93,9.2,html_entity_decode("1. Empleando material impreso (afiches, tr&iacute;pticos, d&iacute;pticos, etc.)"), 1, 'L', 0, 0, '', '', true, 0, false, false, 9.2, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(15, 9.2, number_format($arra_cant_difu[1],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(82,157.4);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(93,9.2,html_entity_decode("2. Empleando mensajes preventivo en diarios, boletines, revistas, etc."), 1, 'L', 0, 0, '', '', true, 0, false, false, 9.2, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(15, 9.2, number_format($arra_cant_difu[2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(82,166.6);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(93,9.2,html_entity_decode("3. Empleando material audiovisual (videos, audios, cu&ntilde;as radiales, etc.)"), 1, 'L', 0, 0, '', '', true, 0, false, false, 9.2, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(15, 9.2, number_format($arra_cant_difu[3],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(82,175.8);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(93,9.2,html_entity_decode("4. Realizando entrevistas en medios de comunicaci&oacute;n local con fines de prevenci&oacute;n (radio, televisi&oacute;n, etc.)"), 1, 'L', 0, 0, '', '', true, 0, false, false, 9.2, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(15, 9.2, number_format($arra_cant_difu[4],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(82,185);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(93,9.2,html_entity_decode("5. Otra"), 1, 'L', 0, 0, '', '', true, 0, false, false, 9.2, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(15, 9.2, number_format($arra_cant_difu[5],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,194.2);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(128,9.2,html_entity_decode("C. Constataci&oacute;n preventiva en el lugar de los hechos ante posibles alertas, que son de conocimiento del fiscal y presuntamente constituyen delito, dejando constancia escrita de los hechos."), 1, 'L', 0, 0, '', '', true, 0, false, false, 9.2, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(15, 9.2, number_format($arra_cant_prev[1],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,203.4);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(35,64.4,html_entity_decode("D. Operativos preventivos"), 1, 'L', 0, 0, '', '', true, 0, false, false, 64.4, 'M');
	$pdf->MultiCell(93,9.2,html_entity_decode("1. Sobre el cumplimiento de la ley seca (visitas a discotecas, bares y cantinas)"), 1, 'L', 0, 0, '', '', true, 0, false, false, 9.2, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(15, 9.2, number_format($arra_cant_prev[2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(82,212.6);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(93,9.2,html_entity_decode("2. En los centros de votaci&oacute;n para verificar que no exista propaganda al interior o en los alrededores"), 1, 'L', 0, 0, '', '', true, 0, false, false, 9.2, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(15, 9.2, number_format($arra_cant_prev[3],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(82,221.8);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(93,9.2,html_entity_decode("3. En los centros de votaci&oacute;n para verificar la distribuci&oacute;n de material electoral"), 1, 'L', 0, 0, '', '', true, 0, false, false, 9.2, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(15, 9.2, number_format($arra_cant_prev[4],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(82,231);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(93,9.2,html_entity_decode("4. Sobre el nivel de seguridad externa de los centros de votaci&oacute;n (miembros de las Fuerzas Armadas y la PNP)"), 1, 'L', 0, 0, '', '', true, 0, false, false, 9.2, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(15, 9.2, number_format($arra_cant_prev[5],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(82,240.2);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(93,9.2,html_entity_decode("5. Para el control de identidad (identificaci&oacute;n de personas requisitoriadas)"), 1, 'L', 0, 0, '', '', true, 0, false, false, 9.2, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(15, 9.2, number_format($arra_cant_prev[6],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(82,249.4);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(93,9.2,html_entity_decode("6. Sobre el cumplimiento de medidas sanitarias ante la pandemia por la COVID-19"), 1, 'L', 0, 0, '', '', true, 0, false, false, 9.2, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(15, 9.2, number_format($arra_cant_prev[7],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(82,258.6);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(93,9.2,"7. Otros", 1, 'L', 0, 0, '', '', true, 0, false, false, 9.2, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(15, 9.2, number_format($arra_cant_prev[8],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,267.8);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(35,9.2,"E. Otras actuaciones", 1, 'L', 0, 0, '', '', true, 0, false, false, 9.2, 'M');
	$pdf->MultiCell(93,9.2," ", 1, 'L', 0, 0, '', '', true, 0, false, false, 9.2, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(15, 9.2, number_format($arra_cant_prev[9],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(20,276.5);
	$pdf->SetFont('arialn', '',10);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(170, 8, "1", 0, 0, 'R', 0, '', 0, false, 'T', 'M');
	
	$pdf->AddPage();
	
	$pdf->SetXY(20,20);
	$pdf->SetFont('arialn', '',12);
	$pdf->Cell(9, 191, "3", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '',8);
	$pdf->MultiCell(18, 191,"Alertas u ocurrencias (Formato B)", 1, 'C', 1, 0, '', '', true, 0, false, false, 143, 'M');
	$pdf->Cell(89, 8, "Tipo", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(36, 4, "Detallar", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(18, 8, "Cantidad", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(136,24);
	$pdf->Cell(18, 4, "Alerta", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(18, 4, "Ocurrencia", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$alto=12.5;
	
	$pdf->SetXY(47,28);
	$pdf->MultiCell(89, $alto,html_entity_decode("1. Destrucci&oacute;n de material electoral (&aacute;nforas, actas o c&eacute;dulas de votaci&oacute;n, etc.)"), 1, 'L', 0, 0, '', '', true, 0, false, false, 10, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$c=1;
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1]+$arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,28+$alto);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(89, $alto,html_entity_decode("2. Disturbios debido a problemas en la instalaci&oacute;n de mesas"), 1, 'L', 0, 0, '', '', true, 0, false, false, 10, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$c=2;
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1]+$arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,28+$alto*2);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(89, $alto,html_entity_decode("3. Difusi&oacute;n de propaganda pol&iacute;tica durante el mismo d&iacute;a de las elecciones"), 1, 'L', 0, 0, '', '', true, 0, false, false, 10, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$c=3;
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1]+$arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,28+$alto*3);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(89, $alto,html_entity_decode("4. Enfrentamientos entre agrupaciones pol&iacute;ticas"), 1, 'L', 0, 0, '', '', true, 0, false, false, 10, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$c=4;
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1]+$arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,28+$alto*4);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(89, $alto,"5. Presencia de \"votos golondrinos\"", 1, 'L', 0, 0, '', '', true, 0, false, false, 10, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$c=5;
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1]+$arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,28+$alto*5);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(89, $alto,html_entity_decode("6. Toma de centros de votaci&oacute;n con actos violentos"), 1, 'L', 0, 0, '', '', true, 0, false, false, 10, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$c=6;
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1]+$arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,28+$alto*6);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(89, $alto,html_entity_decode("7. Impedimento que el personal electoral se lleve las actas o las &aacute;nforas de los centros de votaci&oacute;n"), 1, 'L', 0, 0, '', '', true, 0, false, false, 10, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$c=7;
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1]+$arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,28+$alto*7);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(89, $alto,html_entity_decode("8. Suplantaci&oacute;n de identidad"), 1, 'L', 0, 0, '', '', true, 0, false, false, 10, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$c=8;
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1]+$arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,28+$alto*8);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(89, $alto,html_entity_decode("9. Incautaci&oacute;n del material electoral presuntamente falso o adulterado"), 1, 'L', 0, 0, '', '', true, 0, false, false, 10, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$c=9;
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1]+$arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,28+$alto*9);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(89, $alto,html_entity_decode("10. Intervenciones a personas portando documentos presuntamente falsos"), 1, 'L', 0, 0, '', '', true, 0, false, false, 10, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$c=10;
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1]+$arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,28+$alto*10);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(89, $alto,html_entity_decode("11. Discriminaci&oacute;n e incitaci&oacute;n a la discriminaci&oacute;n"), 1, 'L', 0, 0, '', '', true, 0, false, false, 10, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$c=11;
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1]+$arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,28+$alto*11);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(89, $alto,html_entity_decode("12. Violaci&oacute;n de medidas sanitarias"), 1, 'L', 0, 0, '', '', true, 0, false, false, 10, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$c=12;
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1]+$arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,28+$alto*12);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->MultiCell(89, $alto,html_entity_decode("13. Otros"), 1, 'L', 0, 0, '', '', true, 0, false, false, 10, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$c=13;
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(18, $alto, number_format($arra_cant_aler[$c][1]+$arra_cant_aler[$c][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,28+$alto*13);
	$pdf->SetFont('arialn', '',9);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(71.5, 4, "Lesionados", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(71.5, 4, "Fallecidos", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,32+$alto*13);
	$pdf->Cell(35.75, 4, "Hombre", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(35.75, 4, "Mujer", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(35.75, 4, "Hombre", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(35.75, 4, "Mujer", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,36+$alto*13);
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(35.75, $alto, number_format($arra_cant_lesi[1][1],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(35.75, $alto, number_format($arra_cant_lesi[1][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(35.75, $alto, number_format($arra_cant_lesi[2][1],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(35.75, $alto, number_format($arra_cant_lesi[2][2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(20,211);
	$pdf->SetFont('arialn', '',12);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(9, 20.5, "4", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '',8);
	$pdf->MultiCell(18, 20.5,"Detenciones e intervenciones (Formato C)", 1, 'C', 1, 0, '', '', true, 0, false, false, 20.5, 'M');
	
	$pdf->SetXY(47,211);
	$pdf->SetFont('arialn', '',9);
	$pdf->Cell(71.5, 4, "Detenidos (mayores de edad)", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(71.5, 4, "Intervenidos (menores de edad)", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,215);
	$pdf->Cell(35.75, 4, "Hombre", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(35.75, 4, "Mujer", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(35.75, 4, "Hombre", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(35.75, 4, "Mujer", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(47,219);
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(35.75, $alto, number_format($arra_dete_mayo[1],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(35.75, $alto, number_format($arra_dete_mayo[2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(35.75, $alto, number_format($arra_inte_meno[1],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->Cell(35.75, $alto, number_format($arra_inte_meno[2],0), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(20,231.5);
	$pdf->SetFont('arialn', '',12);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->Cell(9, 45, "5", 1, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', '',8);
	$pdf->MultiCell(18, 45,"Observaciones", 1, 'C', 1, 0, '', '', true, 0, false, false, 45, 'M');
	$pdf->SetFont($font_llen, '', $tama_llen);
	$pdf->SetTextColor($colD,$colE,$colF);
	$pdf->Cell(143, 45, "", 1, 0, 'L', 0, '', 0, false, 'T', 'M');
	
	$pdf->SetXY(20,276.5);
	$pdf->SetTextColor($colA,$colB,$colC);
	$pdf->SetFont('arialn', '',10);
	$pdf->Cell(170, 8, "2", 0, 0, 'R', 0, '', 0, false, 'T', 'M');

	// ---------------------------------------------------------
   //}
//}

//Close and output PDF document
$pdf->Output('example_027.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
