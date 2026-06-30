<?php
use setasign\Fpdi;
require_once('classes/TCPDF/tcpdf.php');
require_once('classes/FPDI/src/autoload.php');

class PDF_CONCAT extends Fpdi\TCPDF\Fpdi
{
	protected $tplId;
	function Header()
	{
	}
	function Footer()
	{
	}

	var $files = array();
	function setFiles($files)
	{
		$this->files = $files;
	}
	function concat()
	{
		foreach($this->files AS $file)
		{
			$pagecount = $this->setSourceFile($file);
			for ($i = 1; $i <= $pagecount; $i++)
			{
				$tplidx = $this->ImportPage($i);
				$s = $this->getTemplatesize($tplidx);
				$this->AddPage('P', array($s['w'], $s['h']));
				$this->useTemplate($tplidx);
			}
		}
	}
	function poner_firma_digital()
	{
		$posx=135;
		$posy=7;
		$anch=58;
		$alto=16;
		$certificate = 'file://cert/'.$_SESSION['logi_oper'].'.crt';
echo"<HR>".$_SESSION['logi_oper']."<HR>";
die();
		$this->setSignature($certificate, $certificate, 'SIOJAlimentos', '', 2, $info);
		$this->setSignatureAppearance($posx,$posy,$anch,$alto);

		$this->Image('img/firma_csjar.jpg', $posx, $posy, $anch, $alto, 'JPG');
		$this->SetFont('courier', '', 7);
		$this->Text($posx+24,$posy+1,'Firmado digitalmente por:');
		$this->Text($posx+24,$posy+3.5,$_SESSION['appa_oper'].' '.$_SESSION['apma_oper']);
		$this->Text($posx+24,$posy+6,$_SESSION['nomb_oper']);
		$this->Text($posx+24,$posy+8.5,'DNIy: '.$_SESSION['logi_oper']);
		$this->Text($posx+24,$posy+11,'Fecha: '.date("d/m/Y H:i:s"));
	}
}
?>
