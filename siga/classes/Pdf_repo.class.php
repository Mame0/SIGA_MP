<?
require_once("pdfwrite/fpdi.php");
class documento_pdf extends fpdi
{
	function documento_pdf($orientation='P',$unit='pt',$format='A4',$plantilla="")
	{
		parent::fpdi($orientation,$unit,$format);
		$this->SetFont('Arial','',$this->size);
		//$this->addPage();
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
function Header()
{
	if($this->ComenzarCabecera==1)
	{
		if($this->logo)
			$this->Image($this->logo,$this->lMargin,$this->tMargin,'',35);
		else
		{
			$this->Image("img/escudo.jpg",$this->lMargin,$this->tMargin,'',31);
			$this->SetFontSize(12);
			$this->TextFont("PIOJ",$this->lMargin+33,$this->tMargin+8,8,'B');
			$this->Line($this->lMargin+33,$this->tMargin+12,$this->lMargin+127,$this->tMargin+12);
			$this->SetFontSize(8);
			$this->TextFont("PROYECTO DE INTEROPERABILIDAD",$this->lMargin+33,$this->tMargin+18,5);
			$this->TextFont("DE OPERADORES DE JUSTICIA",$this->lMargin+33,$this->tMargin+24,5);
		}
		if(!$this->SinDatosCabecera)
		{
			$this->TextFont("FECHA IMPRESION",$this->wPt-$this->rMargin-110,$this->tMargin+8,7);	$this->TextFont(": ".date("Y/m/d"),$this->wPt-$this->rMargin-40,$this->tMargin+8,7);
			$this->TextFont("HORA IMPRESION",$this->wPt-$this->rMargin-110,$this->tMargin+16,7);	$this->TextFont(": ".date("H:i:s"),$this->wPt-$this->rMargin-40,$this->tMargin+16,7);
			$this->TextFont(utf8_decode("PAGINA"),$this->wPt-$this->rMargin-110,$this->tMargin+24,7);	$this->TextFont(": ".$this->PageNo()." de {nb}",$this->wPt-$this->rMargin-40,$this->tMargin+24,7);
		}

		//HUELLA
		$huel="archivos/huellas/pers_".str_pad($this->HuellaSioj,9,'0',STR_PAD_LEFT).".jpg";
		$tipo_imag=exif_imagetype($huel);	//2 JPG
		if(file_exists($huel))
		{
			if($tipo_imag==2)
			{
				$xxxx=530;
				$yyyy=300;
				$this->Image("$huel",$xxxx,$yyyy-100,'50','');
				$this->Rotate(90,$xxxx,$yyyy);
				$this->SetY($yyyy+48);
				$this->SetX($xxxx);
				$this->SetFontSize(6);
				$this->Cell(150,9,$this->Nombres,"T",0,'C',0);
				$this->SetY($yyyy+55);
				$this->SetX($xxxx);
				$this->Cell(150,8,"DNI Nro. ".$this->Documento,"",0,'C',0);
				$this->Rotate(0);
			}
			else
				echo"Tipo de Imagen de Huella no soportada";
		}
		//FIN HUELLA

		//FIRMA SIOJ
		$firm="archivos/firmas/oper_".str_pad($this->FirmaSioj,6,'0',STR_PAD_LEFT).".jpg";
		if(file_exists($firm))
		{
			$xxxx=530;
			$yyyy=695;
			$this->Rotate(90,$xxxx,$yyyy);
			$this->Image("$firm",$xxxx,$yyyy,'',60);
			$this->SetY($yyyy+48);
			$this->SetX($xxxx);
			$this->SetFontSize(5.9);
			$this->Cell(150,9,$this->NombreOperador,"T",0,'C',0);
			$this->SetY($yyyy+55);
			$this->SetX($xxxx);
			if($this->DocumentoOperador)
				$this->Cell(150,8,"DNI: ".$this->DocumentoOperador,"",0,'C',0);
			$sell="archivos/sellos/inst_".str_pad($this->Instancia,6,'0',STR_PAD_LEFT).".jpg";
			if(file_exists($sell))
				$this->Image("$sell",$xxxx-60,$yyyy+5,'',60);
			$this->Rotate(0);
		}
		//FIN FIRMA SIOJ
		

		if($this->Acta)
		{
			$seri=$this->Actaseri;
			$nume=$this->Actanum;
			$anno=$this->Actaanno;
			$nume=str_pad($nume,3,'0',STR_PAD_LEFT);
			$this->TextFont(utf8_decode("ACTA Nro."),$this->wPt-$this->rMargin-70,$this->tMargin+50,12,'B');
			if($seri)
			{
				$seri=str_pad($seri,2,'0',STR_PAD_LEFT);
				$this->TextFont("$seri-$nume-$anno",$this->wPt-$this->rMargin-90,$this->tMargin+70,15,'B');
			}
			else
				$this->TextFont("$nume-$anno",$this->wPt-$this->rMargin-70,$this->tMargin+70,15,'B');
		}

		if($this->OtraCabecera)
		{
			$this->TextFont($this->OtraCabecera,$this->wPt-$this->rMargin-136,$this->tMargin+20,20,'B');
		}
		$this->SetFontSize(9);
		$temp_y=36;
		//$this->TextFont($this->tit1,($this->ancho_pagina-$this->GetStringWidth($this->tit1))/2,56,9,'B');
		$forz=0;
		if($this->tit0 AND $this->tit1)
		{
			$forz=1;
			$temp_y-=10;
			$cent=$this->lMargin+10+(($this->ancho_pagina-($this->lMargin+$this->rMargin+$this->GetStringWidth($this->tit0)))/2);
			$this->TextFont(utf8_decode($this->tit0),$cent,$this->tMargin+$temp_y,9,'B');
			$temp_y+=10;
		}
		elseif($this->tit0 AND !$this->tit1)
		{
			$this->tit1=$this->tit0;
			$this->tit0='';
		}
		$this->SetFontSize(6);
		$cent=$this->lMargin+(($this->ancho_pagina-($this->lMargin+$this->rMargin+$this->GetStringWidth($this->tit1)))/2);
		$this->TextFont($this->tit1,$cent,$this->tMargin+$temp_y,9,'B');

		$ta=10;
                if(strlen($this->tit2)>75)
                        $ta=8;
                $this->SetFontSize($ta);

		$cent=$this->lMargin+(($this->ancho_pagina-($this->lMargin+$this->rMargin+$this->GetStringWidth($this->tit2)))/2);
		if($this->tit1)
			$tmar=50;
		else
			$tmar=35;
		$this->TextFont($this->tit2,$cent,$this->tMargin+$tmar,$ta,'B');

		$this->SetY($this->tMargin+$tmar+8);	//antes estaba en 5
		if($this->Acta)
			$this->SetFontSize(8);
		else
			$this->SetFontSize(9);
		if(!$this->arra_deta_limi)
			$this->arra_deta_limi=1000;
		for(reset($this->arra_deta);$d=key($this->arra_deta);next($this->arra_deta))
		{
			if($d<=$this->arra_deta_limi OR $this->PageNo()==1)
			{
				if(!$forz OR ($forz AND $this->arra_deta[$d]['forz']==1))
				{
					$alto=13;
					if($this->arra_deta[$d]['des1']=='' AND $this->arra_deta[$d]['des2']=='')
						$alto=5;
					$bor1=$this->arra_deta[$d]['bor1'];
					$this->SetFont('','B');
					$this->Cell($this->arra_deta[$d]['anc1'],$alto,$this->arra_deta[$d]['tit1'],$bor1,0,'L',0);
					if($this->arra_deta[$d]['tit1'])
						$this->Cell(10,$alto,":",$bor1,0,'C',0);
					$this->SetFont('','');
					$this->Cell(250,$alto,$this->arra_deta[$d]['des1'],$bor1,0,'L',0);

					$bor2=$this->arra_deta[$d]['bor2'];
					$this->SetFont('','B');
					$this->Cell($this->arra_deta[$d]['anc2'],$alto,$this->arra_deta[$d]['tit2'],$bor1,0,'L',0);
					if($this->arra_deta[$d]['tit2'])
						$this->Cell(10,$alto,":",$bor1,0,'C',0);
					$this->SetFont('','');
					$this->Cell(0,$alto,$this->arra_deta[$d]['des2'],$bor1,0,'L',0);

					$this->Ln();
				}
			}
		}
		$this->Ln(5);
		$this->SetFont('','B');

		if(!$this->arra_form['alto_cabe'])
			$this->arra_form['alto_cabe']=10;
		if($this->arra_form['tama_letr'])
			$this->SetFontSize($this->arra_form['tama_letr']);
		else
			$this->SetFontSize(8);
		if($this->arra_form['anch_line'])
			$this->SetLineWidth($this->arra_form['anch_line']);
		else
			$this->SetLineWidth(.1);
		if($this->arra_form['colo_llen'])
			$this->SetFillColor($this->arra_form['colo_llen']);
		else
			$this->SetFillColor();

		$anch=$this->wPt-($this->lMargin+$this->rMargin);
		$cabe=0;
		for(reset($this->arra_cabe);$c=key($this->arra_cabe);next($this->arra_cabe))
		{
			$cabe=1;
			if(strstr($this->arra_cabe[$c]['anch'],'%'))
				$this->arra_cabe[$c]['anch']=($anch*$this->arra_cabe[$c]['anch'])/100;
			if($this->arra_cabe[$c]['llen'] AND $this->arra_cabe[$c]['gris'])
					$this->SetFillColor($this->arra_cabe[$c]['gris']);
			$this->Cell($this->arra_cabe[$c]['anch'],$this->arra_form['alto_cabe'],$this->arra_cabe[$c]['nomb'],$this->arra_cabe[$c]['bord'],$this->arra_cabe[$c]['calt'],$this->arra_cabe[$c]['alin'],$this->arra_cabe[$c]['llen']);
		}
		if($cabe)
			$this->Ln();
		$cabe=0;
		for(reset($this->arra_cab2);$c=key($this->arra_cab2);next($this->arra_cab2))
		{
			$cabe=1;
			if(strstr($this->arra_cab2[$c]['anch'],'%'))
				$this->arra_cab2[$c]['anch']=($anch*$this->arra_cab2[$c]['anch'])/100;
			$this->Cell($this->arra_cab2[$c]['anch'],$this->arra_form['alto_cabe'],$this->arra_cab2[$c]['nomb'],$this->arra_cab2[$c]['bord'],$this->arra_cab2[$c]['calt'],$this->arra_cab2[$c]['alin'],$this->arra_cab2[$c]['llen']);
		}
		if($cabe)
			$this->Ln();
	}
}
function Footer() 
{ 
	if($this->Acta)
	{
		$pie=483;
		$this->SetFont('Helvetica','',7);     
		$this->TextFont("NOTA:  EL TRABAJADOR ES  RESPONSABLE  DIRECTO DE LA EXISTENCIA,  PERMANENCIA, CONSERVACION Y BUEN USO DE CADA UNO DE LOS BIENES DESCRITOS, POR LO QUE SE  RECOMIENDA  TOMAR  LAS  PROVIDENCIAS DEL  CASO PARA EVITAR  PERDIDA,  SUSTRACCION,  DETERIORO, ETC.  QUE  LUEGO  PODRIA  SER",$this->lMargin,$this->tMargin+$pie+7,5);
		$this->TextFont("             CONSIDERADO COMO DESCUIDO O NEGLIGENCIA. CUALQUIER MOVIMIENTO DENTRO O FUERA DE LA ENTIDAD, DEBERA SER COMUNICADO AL ENCARGADO DE CONTROL PATRIMONIAL, BAJO RESPONSABILIDAD.",$this->lMargin,$this->tMargin+$pie+14,5);
		$this->TextFont("             (DIRECTIVA Nro. 002-2009-GAF-PJ DE ADMINISTRACION, ASIGNACION Y CONTROL AMBIENTAL DE BIENES MUEBLES PATRIMONIALES DEL PODER JUDICIAL)",$this->lMargin,$this->tMargin+$pie+21,5);
		$this->SetFont('Helvetica','B',7);     
		$this->TextFont("___________________________________",$this->lMargin+150,$this->tMargin+$pie+60,5);
		$this->TextFont("___________________________________",$this->lMargin+560,$this->tMargin+$pie+60,5);
		$this->TextFont("USUARIO RESPONSABLE",$this->lMargin+168,$this->tMargin+$pie+67,5,'B');
		$this->TextFont("CONTROL PATRIMONIAL",$this->lMargin+578,$this->tMargin+$pie+67,5,'B');
	}
}
function datos($arra_dato)
{
	$anch=$this->wPt-($this->lMargin+$this->rMargin);
	for(reset($arra_dato);$p=key($arra_dato);next($arra_dato))
	{
			$this->addPage();
		for(reset($arra_dato[$p]);$f=key($arra_dato[$p]);next($arra_dato[$p]))
		{
			for(reset($arra_dato[$p][$f]);$c=key($arra_dato[$p][$f]);next($arra_dato[$p][$f]))
			{
				if(strstr($arra_dato[$p][$f][$c]['anch'],'%'))
					$arra_dato[$p][$f][$c]['anch']=($anch*$arra_dato[$p][$f][$c]['anch'])/100;

				if($arra_dato[$p][$f][$c]['tama'])
					$this->SetFontSize($arra_dato[$p][$f][$c]['tama']);
				else
					$this->SetFontSize(10);
				$this->SetFont('',"{$arra_dato[$p][$f][$c]['form']}");
				if($arra_dato[$p][$f][$c]['llen'] AND $arra_dato[$p][$f][$c]['gris'])
					$this->SetFillColor($arra_dato[$p][$f][$c]['gris']);
$xx=$arra_dato[$p][$f][$c]['mult'];
		
				if($arra_dato[$p][$f][$c]['sety'])	//addPage()
					$this->SetY($this->tMargin+$arra_dato[$p][$f][$c]['sety']);

				if($arra_dato[$p][$f][$c]['addp'])	//addPage()
					$this->addPage();
				else
				{
					if($arra_dato[$p][$f][$c]['nfot'] OR $arra_dato[$p][$f][$c]['huel'])
					{
						if($arra_dato[$p][$f][$c]['nfot'])
						{
							$this->Image($arra_dato[$p][$f][$c]['nfot'],$this->GetX(),$this->GetY(),$arra_dato[$p][$f][$c]['wfot'],$arra_dato[$p][$f][$c]['hfot']);
							//$this->Image($arra_dato[$p][$f][$c]['nfot'],$arra_dato[$p][$f][$c]['xfot'],$arra_dato[$p][$f][$c]['yfot'],$arra_dato[$p][$f][$c]['wfot'],$arra_dato[$p][$f][$c]['hfot']);
						}
						else
						{
//							$this->Image($arra_dato[$p][$f][$c]['huel'],530,200,40,'');
				$xxxx=530;
				$yyyy=300;
				$this->Image($arra_dato[$p][$f][$c]['huel'],$xxxx,$yyyy-100,'50','');
				$this->Rotate(90,$xxxx,$yyyy);
				$this->SetY($yyyy+48);
				$this->SetX($xxxx);
				$this->SetFontSize(6);
				$this->Cell(150,9,$arra_dato[$p][$f][$c]['nomb'],"T",0,'C',0);
				$this->SetY($yyyy+55);
				$this->SetX($xxxx);
				$this->Cell(150,8,"DNI Nro. ".$arra_dato[$p][$f][$c]['ndoc'],"",0,'C',0);
				$this->Rotate(0);
						}
					}
					else
					{
						if($arra_dato[$p][$f][$c]['mult'])
						{
							$this->MultiCell($arra_dato[$p][$f][$c]['anch'],$arra_dato[$p][$f][$c]['alto'],$arra_dato[$p][$f][$c]['nomb'],$arra_dato[$p][$f][$c]['bord'],$arra_dato[$p][$f][$c]['alin'],$arra_dato[$p][$f][$c]['llen']);
						}
						else
							$this->Cell($arra_dato[$p][$f][$c]['anch'],$arra_dato[$p][$f][$c]['alto'],$arra_dato[$p][$f][$c]['nomb'],$arra_dato[$p][$f][$c]['bord'],$arra_dato[$p][$f][$c]['calt'],$arra_dato[$p][$f][$c]['alin'],$arra_dato[$p][$f][$c]['llen']);
					}
				}
			}
			$this->Ln();
		}
	}
}
}


class concat_pdf extends FPDI
{
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
				$tplidx = $this->ImportPage($i,'/MediaBox');
				$s = $this->getTemplatesize($tplidx);
				$this->AddPage($s['h'] > $s['w'] ? 'P' : 'L');
				$this->useTemplate($tplidx,10,10,200);
			}
			if($this->EliminarArchivo==1)
				unlink($file);
		}
	}
}

class concat_carpeta_pdf extends FPDI
{
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
                                $this->AddPage($s['h'] > $s['w'] ? 'P' : 'L');
                                $this->useTemplate($tplidx);
                        }
                }
        }
}

?>
