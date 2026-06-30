<?php
require_once(dirname(__FILE__).'/tcpdf.php');
class TCPDF_SIOJ extends TCPDF {
	function poner_firma_digital()
	{
		$posx=135;
		$posy=7;
		$anch=58;
		$alto=16;

		$certificate = 'file://cert/'.$_SESSION['logi_oper'].'.crt';
		// set additional information
		$info = array(
			'Name' => 'CSJAR',
			'Location' => 'CSJAR',
			'Reason' => 'SIOJ Alimentos',
			'ContactInfo' => 'http://csjarequipa.pj.gob.pe',
		);
		// set document signature
		$this->setSignature($certificate, $certificate, 'SIOJAlimentos', '', 2, $info);
		// define active area for signature appearance
		$this->setSignatureAppearance($posx,$posy,$anch,$alto);

		$this->Image('img/firma_csjar.jpg', $posx, $posy, $anch, $alto, 'JPG');
		$this->SetFont('courier', '', 7);
		$this->TextFont('Firmado digitalmente por:',$posx+24,$posy+1);
		$this->TextFont($_SESSION['appa_oper'].' '.$_SESSION['apma_oper'],$posx+24,$posy+3.5);
		$this->TextFont($_SESSION['nomb_oper'],$posx+24,$posy+6);
		$this->TextFont('DNIx: '.$_SESSION['logi_oper'],$posx+24,$posy+8.5);
		$this->TextFont('Fecha: '.date("d/m/Y H:i:s"),$posx+24,$posy+11);
		$this->SetFont('helvetica','',$this->size);

	}
	function poner_firma($firm,$nomb,$depe,$inst)
	{
		if($nomb AND $firm)
		{
			//para centrar la firma
			list($width, $height) = getimagesize($firm);
			$scale=$width/$height;
			$anch_celd=200;
			$alto_firm=70;
			$cent_firm=($anch_celd-($scale*$alto_firm))/2;
			//para convertirlo a mm
			$cent_firm=$cent_firm/3;

			$this->contador_fila++;
			$this->arra_dato[1][$this->contador_fila]=array(
				1=>array('anch'=>$anch_celd,'alto'=>'7',  'nfot'=>$firm,'hfot'=>$alto_firm/3,'alin'=>'C','cent_firm'=>$cent_firm,'tama'=>$this->tamano_letra+3,'bord'=>"BTLR"),
				2=>array('anch'=>$anch_celc,'alto'=>$alto_firm/3,  'nomb'=>"",'alin'=>'C','tama'=>$tama+3,'bord'=>""),
			);
			$this->contador_fila++;
			$this->arra_dato[1][$this->contador_fila]=array(
				1=>array('anch'=>'70','alto'=>'1',  'nomb'=>"",'alin'=>'C','tama'=>1,'bord'=>"T",'mult'=>"",'form'=>""),
			);
			$this->contador_fila++;
			$this->arra_dato[1][$this->contador_fila]=array(
				1=>array('anch'=>'70','alto'=>'4',  'nomb'=>$nomb,'alin'=>'C','tama'=>$this->tamano_letra+2,'bord'=>"",'mult'=>"",'form'=>"B"),
			);
			if($depe)
			{
				$this->contador_fila++;
				$this->arra_dato[1][$this->contador_fila]=array(
					1=>array('anch'=>'70','alto'=>'4',  'nomb'=>$depe,'alin'=>'C','tama'=>$this->tamano_letra,'bord'=>"",'mult'=>"",'form'=>""),
				);
			}
			if($inst)
			{
				$this->contador_fila++;
				$this->arra_dato[1][$this->contador_fila]=array(
					1=>array('anch'=>'70','alto'=>'4',  'nomb'=>$inst,'alin'=>'C','tama'=>$this->tamano_letra,'bord'=>"",'mult'=>"",'form'=>""),
				);
			}
		}
	}
	function TextFont($txt,$x,$y,$fsize="",$fstyle="",$ftype="")
	{
		// Cambio en el tipo y/o estilo de la fuente
		if ($ftype!=""|| $fstyle!="")
		{
			$ffamil_actual = $this->FontFamily;
			$fstyle_actual = $this->FontStyle;
			$this->SetFont($ftype,$fstyle);
		}
		// Cambio en el tamanho de la fuente
		if ( is_int($fsize)|| is_float($fsize) )
		{
			$fsize_actual = $this->FontSizePt;
			$this->SetFontSize($fsize);
		}
		$this->Text($x,$y,$txt);
		$this->SetFont($ffamil_actual,$fstyle_actual,$fsize_actual);
	}
	public function poner_datos()
	{
		$anch=$this->getPageWidth()-($this->lMargin+$this->rMargin);
		for(reset($this->arra_dato);$p=key($this->arra_dato);next($this->arra_dato))
		{
			$this->addPage();
			for(reset($this->arra_dato[$p]);$f=key($this->arra_dato[$p]);next($this->arra_dato[$p]))
			{
				for(reset($this->arra_dato[$p][$f]);$c=key($this->arra_dato[$p][$f]);next($this->arra_dato[$p][$f]))
				{
					if(strstr($this->arra_dato[$p][$f][$c]['anch'],'%'))
						$this->arra_dato[$p][$f][$c]['anch']=($anch*$this->arra_dato[$p][$f][$c]['anch'])/100;

					if($this->arra_dato[$p][$f][$c]['tama'])
						$this->SetFontSize($this->arra_dato[$p][$f][$c]['tama']);
					else
						$this->SetFontSize(10);

					$this->SetFont('',"{$this->arra_dato[$p][$f][$c]['form']}");
					if($this->arra_dato[$p][$f][$c]['llen'] AND $this->arra_dato[$p][$f][$c]['gris'])
						$this->SetFillColor($this->arra_dato[$p][$f][$c]['gris']);
					$xx=$this->arra_dato[$p][$f][$c]['mult'];

					if($this->arra_dato[$p][$f][$c]['sety'])        //addPage()
						$this->SetY($this->tMargin+$this->arra_dato[$p][$f][$c]['sety']);
					if($this->arra_dato[$p][$f][$c]['addp'])        //addPage()
						$this->addPage();
					else
					{
						if($this->arra_dato[$p][$f][$c]['nfot'] OR $this->arra_dato[$p][$f][$c]['huel'])
						{
							if($this->arra_dato[$p][$f][$c]['nfot'])
							{
								$this->Image($this->arra_dato[$p][$f][$c]['nfot'],$this->GetX()+$this->arra_dato[$p][$f][$c]['cent_firm'],$this->GetY(),$this->arra_dato[$p][$f][$c]['wfot'],$this->arra_dato[$p][$f][$c]['hfot']);
								//$this->Image($this->arra_dato[$p][$f][$c]['nfot'],$this->arra_dato[$p][$f][$c]['xfot'],$this->arra_dato[$p][$f][$c]['yfot'],$this->arra_dato[$p][$f][$c]['wfot'],$this->arra_dato[$p][$f][$c]['hfot']);
							}
							else
							{
								//$this->Image($this->arra_dato[$p][$f][$c]['huel'],530,200,40,'');
								$xxxx=530;
								$yyyy=300;
								$this->Image($this->arra_dato[$p][$f][$c]['huel'],$xxxx,$yyyy-100,'50','');
								$this->Rotate(90,$xxxx,$yyyy);
								$this->SetY($yyyy+48);
								$this->SetX($xxxx);
								$this->SetFontSize(6);
								$this->Cell(150,9,$this->arra_dato[$p][$f][$c]['nomb'],"T",0,'C',0);
								$this->SetY($yyyy+55);
								$this->SetX($xxxx);
								$this->Cell(150,8,"DNI Nro. ".$this->arra_dato[$p][$f][$c]['ndoc'],"",0,'C',0);
								$this->Rotate(0);
							}
						}
						else
						{
							if($this->arra_dato[$p][$f][$c]['mult'])
							{
								$this->MultiCell($this->arra_dato[$p][$f][$c]['anch'],$this->arra_dato[$p][$f][$c]['alto'],$this->arra_dato[$p][$f][$c]['nomb'],$this->arra_dato[$p][$f][$c]['bord'],$this->arra_dato[$p][$f][$c]['alin'],$this->arra_dato[$p][$f][$c]['llen']);
							}
							else
								$this->Cell($this->arra_dato[$p][$f][$c]['anch'],$this->arra_dato[$p][$f][$c]['alto'],$this->arra_dato[$p][$f][$c]['nomb'],$this->arra_dato[$p][$f][$c]['bord'],$this->arra_dato[$p][$f][$c]['calt'],$this->arra_dato[$p][$f][$c]['alin'],$this->arra_dato[$p][$f][$c]['llen']);
						}
					}
				}
				$this->Ln();
			}
		}
	}
} // END OF CLASS
