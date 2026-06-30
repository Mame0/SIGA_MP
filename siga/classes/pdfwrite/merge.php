<?php
require_once('fpdf.php');
require_once('fpdi.php');

class ConcatPdf extends FPDI
{
    public $files = array();

    public function setFiles($files)
    {
        $this->files = $files;
    }

    public function concat()
    {
        foreach($this->files AS $file) {
            $pageCount = $this->setSourceFile($file);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $tplIdx = $this->ImportPage($pageNo);
                $s = $this->getTemplatesize($tplIdx);
                $this->AddPage($s['w'] > $s['h'] ? 'L' : 'P', array($s['w'], $s['h']));
                $this->useTemplate($tplIdx);
            }
        }
    }
}

/*
if($_GET["notifica"]==1){

	$dir1="../../".$_GET["dir1"];
	$dir2="../../".$_GET["dir2"];
	echo "directorio 1: ".$dir1."<hr/>";
	echo "directorio 2: ".$dir2."<hr/>";
	$archivo_pdf=$dir1;

	$pdf = new ConcatPdf();
	$pdf->setFiles(array($dir1, $dir2));
	$pdf->concat();

	$pdf->Output("$archivo_pdf", 'F');
	echo"<iframe src=\"$archivo_pdf\" width=580 height=520 marginheight=0 marginwidth=0 noresize scrolling=\"No\" frameborder=0></iframe>";
}
else{




$subir_partes=0;
$id=$_GET["id"];

// modificar con rutas estos archivos no son los firmados
//$num=17;

//$pdf_1="../../archivos/documentos_firmados/docu_".str_pad($id, 9, "0", STR_PAD_LEFT)."_".str_pad($num,4 , "0", STR_PAD_LEFT)."_".str_pad($subir_partes, 9, "0", STR_PAD_LEFT).".pdf";
//$num++;
//$pdf_2="../../archivos/documentos_firmados/docu_".str_pad($id, 9, "0", STR_PAD_LEFT)."_".str_pad($num,4 , "0", STR_PAD_LEFT)."_".str_pad($subir_partes, 9, "0", STR_PAD_LEFT).".pdf";

$pdf_1="../../archivos/cem/informe_psciologico-".str_pad($id, 6, "0", STR_PAD_LEFT).".pdf";
$pdf_2="../../archivos/cem/informe_social-".str_pad($id, 6, "0", STR_PAD_LEFT).".pdf";
$pdf_3="../../archivos/cem/denuncia_legal-".str_pad($id, 6, "0", STR_PAD_LEFT).".pdf";
$archivo_pdf="../../archivos/documentos_firmados/docu_".str_pad($id, 9, "0", STR_PAD_LEFT)."_".str_pad(4,4 , "0", STR_PAD_LEFT)."_".str_pad($subir_partes, 9, "0", STR_PAD_LEFT).".pdf";

//echo $pdf_1."<hr/>";
//echo $pdf_2."<hr/>";
//echo $pdf_3."<hr/>";

if (file_exists($pdf_1)) {
	if (file_exists($pdf_2)) {
		if (file_exists($pdf_3)) {
			$pdf = new ConcatPdf();
			$pdf->setFiles(array($pdf_3,$pdf_1, $pdf_2));
			$pdf->concat();

			$pdf->Output("$archivo_pdf", 'D');
			echo"<iframe src=\"$archivo_pdf\" width=580 height=520 marginheight=0 marginwidth=0 noresize scrolling=\"No\" frameborder=0></iframe>";			
		} else {
			echo "no se escribio la denuncia<hr/>";
		}
	} else {
		echo "El informe del(a) asistente social  firmado digitalmente no existe<hr/>";
	}
} else {
    echo "El informe psicologico firmado digitalmente no existe<hr/>";
}


}
*/
