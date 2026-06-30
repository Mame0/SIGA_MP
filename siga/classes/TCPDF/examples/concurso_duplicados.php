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
	
	$result=$Db->query("select * from mp_maes_concurso_regimen");
	foreach($result as $rows)
        $arra_regi[$rows['n_codigo']]=$rows['x_nombre'];
	
	$result=$Db->query("select * from mp_concurso_plazas");
	foreach($result as $rows)
        $arra_plaz[$rows['codi_plaz']]=$rows['nomb_plaz'];
	
	$result=$Db->query("select * from mp_concurso_proceso");
	foreach($result as $rows)
	{
        $arra_proc[$rows['codi_proc']]=$rows['nume_proc'].'-'.$rows['anno_proc'];
		$arra_proc_regi[$rows['codi_proc']]=$rows['regi_proc'];
	}
	
	$result=$Db->query("select * from mp_concurso_examen");
	foreach($result as $rows)
        $arra_exam[$rows['codi_exam']]=$rows['fech_exam'];
	
	//$result=$Db->query("select count(*) canti from mp_concurso_postulantes where codi_plaz='".$_POST['codi_plaz']."'");
	//foreach($result as $rows)
    //    $tota_resu=$rows['canti'];
    //$pagi_total=ceil($tota_resu/40);
    
    $result=$Db->query("select c.docu_post docu,count(*) cant from mp_concurso_proceso=a,mp_concurso_plazas=b,mp_concurso_postulantes=c where a.codi_proc=b.codi_proc AND b.codi_plaz=c.codi_plaz AND a.codi_exam='".$_POST['codi_exam']."' AND a.esta_proc='1' AND b.esta_plaz='1' AND c.esta_post='1' group by c.docu_post having cant > 1");
	$list_post='';
	foreach($result as $rows)
	{       
		$list_post.=",".$rows['docu'];
		$tota_resu++;
	}
	$list_post=substr($list_post,1);
	$pagi_total=ceil(($tota_resu*2)/40);
	
	$subtitulo="EXAMEN: ".$arra_exam[$_POST['codi_exam']];
	//$subsubtitulo="PLAZA: ".$arra_plaz[$_POST['codi_plaz']];
	
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
	$pdf->Cell(170, 4, "SISTEMA INTEGRADO DE GESTIÓN ADMINISTRATIVA - SIGA CONCURSOS", 0, 1, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->SetFont('arialn', 'B', 14);
	$pdf->SetXY(20,33.5);
	$pdf->Cell(170, 4, "REPORTE DE DUPLICADOS", 0, 1, 'C', 0, '', 0, false, 'T', 'M');
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
	$pdf->Cell(18, 8, "DOCUM.", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(30, 8, "AP. PATERNO", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(30, 8, "AP. MATERNO", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(50, 8, "NOMBRES", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(18, 8, "PLAZA", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
	$pdf->Cell(17, 8, "ASISTENCIA", 1, 0, 'C', 1, '', 0, false, 'T', 'M');
}
	
	$pdf->SetFont('arialn', '',9);
	$pdf->SetXY(20,59);
	$alto=5.5;
	$cont=0;
	$cont_pagi=0;
	$pagi_actu=1;
	
	//$result=$Db->query("select * from mp_concurso_postulantes where codi_plaz='".$_POST['codi_plaz']."' AND esta_post='1' order by appa_post,apma_post,nomb_post");
	$result=$Db->query("select c.codi_post codi_post,c.docu_post,c.appa_post,c.apma_post,c.nomb_post,b.codi_plaz,c.regi_asis,c.fdig_asis from mp_concurso_proceso=a,mp_concurso_plazas=b,mp_concurso_postulantes=c where a.codi_proc=b.codi_proc AND b.codi_plaz=c.codi_plaz AND a.codi_exam='".$_POST['codi_exam']."' AND a.esta_proc='1' AND b.esta_plaz='1' AND c.esta_post='1' and c.docu_post in ($list_post) order by c.appa_post,c.apma_post,c.nomb_post");
    
    $camb=$borde='';
	foreach($result as $rows)
	{
	    
	        if($camb==$rows['docu_post'])
		    {
		        $rows['docu_post']=" ";
		        $contador='';
		        $borde='B';
		    }
		    else
		    {
		        $camb=$rows['docu_post'];
			    $cont++;
			    $contador=$cont;
			    $borde='0';
		    }
	    
	    $cont_pagi++;
	    if($cont_pagi>40)
	    {
	        $pdf->AddPage();
	        $pagi_actu++;
	        poner_cabecera($pdf,$subtitulo,$subsubtitulo,$pagi_actu,$pagi_total);
	        $pdf->SetXY(20,59);
	        $cont_pagi=1;
	    }
		
		$asis='';
		if($rows['regi_asis'])
			$asis='Presente';
	    //$rows['fdig_mpar']=substr($rows['fdig_mpar'],6,2).'/'.substr($rows['fdig_mpar'],4,2).'/'.substr($rows['fdig_mpar'],0,4)."\n".substr($rows['fdig_mpar'],8,2).':'.substr($rows['fdig_mpar'],10,2)." hrs.";
		//$rows['fech_asig']=substr($rows['fech_asig'],6,2).'/'.substr($rows['fech_asig'],4,2).'/'.substr($rows['fech_asig'],0,4)."\n".substr($rows['fech_asig'],8,2).':'.substr($rows['fech_asig'],10,2)." hrs.";
    	
    	$pdf->MultiCell(8, $alto,$contador, $borde, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(18, $alto,$rows['docu_post'], $borde, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(30, $alto,$rows['appa_post'], $borde, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(30, $alto,$rows['apma_post'], $borde, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(50, $alto,$rows['nomb_post'], $borde, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(18, $alto,$arra_plaz[$rows['codi_plaz']], $borde, 'C', 0, 0, '', '', true, 0, false, false, $alto, 'M');
    	$pdf->MultiCell(17, $alto,$asis, $borde, 'C', 0, 1, '', '', true, 0, false, false, $alto, 'M');
			
			
	
	}

//Close and output PDF document
$pdf->Output('example_027.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
