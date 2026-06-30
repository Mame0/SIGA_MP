<?
require_once("pdfwrite/fpdi.php");
class documento_pdf extends fpdi
{
	public $arra_dato = array();
	function documento_pdf($orientation='P',$unit='pt',$format='A4',$plantilla="")
	{
		parent::fpdi($orientation,$unit,$format);
		$this->SetFont('Arial','',$this->size);
                $this->AliasNbPages();
                $this->ComenzarCabecera=1;
                $this->SinDatosCabecera=1;

                $this->SetAutoPageBreak(true,50);
                $this->ancho_pagina=555;          //alto de una pagina A4 555
                $this->alto_pagina=830;         //ancho de una pagina A4 LANDSCAPE 830
                $this->lMargin=90;
                $this->rMargin=90;
                $this->tMargin=40;

		$this->tamano_letra=9;
        	$this->valor_gris=220;
		//$this->sangria="                         ";
		$this->contador_fila=0;
	}
	function datos_cabecera($codi_inst,$nomb_inst,$nomb_ofic,$nomb_anno)
	{
                $this->codi_inst=$codi_inst;
                $this->nomb_inst=$nomb_inst;
                $this->nomb_ofic=$nomb_ofic;
                $this->nomb_anno=$nomb_anno;
	}
	function poner_fecha_documento($contenido)
	{
		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=array(
			1=>array('anch'=>'100%','alto'=>'32',  'nomb'=>utf8_decode("$contenido"),'alin'=>'R','tama'=>$this->tamano_letra+3,'bord'=>"",'mult'=>"1"),
		);
	}
	function poner_numero_documento($contenido)
	{
		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=array(
			1=>array('anch'=>'100%','alto'=>'16',  'nomb'=>utf8_decode("$contenido"),'alin'=>'J','tama'=>$this->tamano_letra+3,'bord'=>"",'mult'=>"1",'form'=>"BU"),
		);
	}
	function poner_dirigido_a1($contenido)
	{
		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=array(
			1=>array('anch'=>'100%','alto'=>'16',  'nomb'=>utf8_decode("$contenido"),'alin'=>'J','tama'=>$this->tamano_letra+3,'bord'=>"",'mult'=>"",'form'=>"B"),
		);
	}
	function poner_dirigido_a2($contenido)
	{
		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=array(
			1=>array('anch'=>'100%','alto'=>'16',  'nomb'=>utf8_decode("$contenido"),'alin'=>'J','tama'=>$this->tamano_letra+3,'bord'=>"",'mult'=>"1",'form'=>"B"),
		);
	}
	function poner_firma_digital2()
	{

$posx=370;
$posy=10;
$anch=175;
$alto=50;
$this->Image('img/firma_csjar.jpg', $posx, $posy, $anch, $alto, 'JPG');
$this->SetFont('courier', '', 7);
$this->TextFont('Firmado digitalmente por:',$posx+75,$posy+12);
$this->TextFont('MORAN OVIEDO',$posx+75,$posy+19);
$this->TextFont('Victoria Mafalda',$posx+75,$posy+26);
$this->TextFont('DNI: 29709217',$posx+75,$posy+33);
$this->TextFont('Fecha: '.date("d/m/Y H:i:s"),$posx+75,$posy+40);
		$this->SetFont('Arial','',$this->size);
	}
	function poner_firma_digital()
	{
$posx=130;
$posy=2;
$posy=200;
$anch=70;
$alto=20.8;
$this->Image('img/firma_csjar.jpg', $posx, $posy, $anch, $alto, 'JPG');
$this->SetFont('courier', '', 7);
$this->Text($posx+29,$posy+2,'Firmado digitalmente por:');
$this->Text($posx+29,$posy+5,'BARREDA MAYHUA');
$this->Text($posx+29,$posy+8,'Jesus Manuel');
$this->Text($posx+29,$posy+11,'DNI: 29709217');
$this->Text($posx+29,$posy+14,'Fecha: 02/06/2020 13:12:12');
		$this->SetFont('Arial','',$this->size);
	}
	function poner_firma($firm,$nomb,$depe,$inst)
	{
		if($nomb AND $firm)
		{
$this->TextFont('Firmado Digitalmente por:',130,4);

//	 TextFont($txt,$x,$y,$fsize="",$fstyle="",$ftype="")
			//para centrar la firma
			list($width, $height) = getimagesize($firm);
			$scale=$width/$height;
			$anch_celd=200;
			$alto_firm=70;
			$cent_firm=($anch_celd-($scale*$alto_firm))/2;

			$this->contador_fila++;
			$this->arra_dato[1][$this->contador_fila]=array(
				1=>array('anch'=>$anch_celd,'alto'=>'20',  'nfot'=>$firm,'hfot'=>$alto_firm,'alin'=>'C','cent_firm'=>$cent_firm,'tama'=>$this->tamano_letra+3,'bord'=>"BTLR"),
				2=>array('anch'=>$anch_celc,'alto'=>$alto_firm,  'nomb'=>"",'alin'=>'C','tama'=>$tama+3,'bord'=>""),
			);
			$this->contador_fila++;
			$this->arra_dato[1][$this->contador_fila]=array(
				1=>array('anch'=>'200','alto'=>'5',  'nomb'=>"",'alin'=>'C','tama'=>$this->tamano_letra+2,'bord'=>"T",'mult'=>"",'form'=>""),
			);
			$this->contador_fila++;
			$this->arra_dato[1][$this->contador_fila]=array(
				1=>array('anch'=>'200','alto'=>'11',  'nomb'=>$nomb,'alin'=>'C','tama'=>$this->tamano_letra+2,'bord'=>"",'mult'=>"",'form'=>"B"),
			);
			if($depe)
			{
				$this->contador_fila++;
				$this->arra_dato[1][$this->contador_fila]=array(
					1=>array('anch'=>'200','alto'=>'11',  'nomb'=>$depe,'alin'=>'C','tama'=>$this->tamano_letra,'bord'=>"",'mult'=>"",'form'=>""),
				);
			}
			if($inst)
			{
				$this->contador_fila++;
				$this->arra_dato[1][$this->contador_fila]=array(
					1=>array('anch'=>'200','alto'=>'11',  'nomb'=>$inst,'alin'=>'C','tama'=>$this->tamano_letra,'bord'=>"",'mult'=>"",'form'=>""),
				);
			}
		}
	}
	function poner_subtitulo($titulo,$contenido)
	{
		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=array(
			1=>array('anch'=>'20%','alto'=>'16',  'nomb'=>$titulo,'alin'=>'L','tama'=>$this->tamano_letra+3,'bord'=>"",'mult'=>"0"),
			2=>array('anch'=>'80%','alto'=>'16',  'nomb'=>$contenido,'alin'=>'J','tama'=>$this->tamano_letra+3,'bord'=>"",'mult'=>"1"),
		);
	}
	function poner_parrafo($contenido)
	{
		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=array(
			1=>array('anch'=>'100%','alto'=>'16',  'nomb'=>$this->sangria.utf8_decode("$contenido"),'alin'=>'J','tama'=>$this->tamano_letra+3,'bord'=>"",'mult'=>"1"),
		);
	}
	function poner_tabla($contenido)
	{
		$arra_tabl=json_decode($contenido);
		for($f=0;$f<10;$f++)
		{
			if($arra_tabl[$f]->A)
			{
				$this->contador_fila++;
				$this->arra_dato[1][$this->contador_fila]=array(
					1=>array('anch'=>'40%','alto'=>'18',  'nomb'=>utf8_decode($arra_tabl[$f]->A),'alin'=>'L','tama'=>$this->tamano_letra+1,'bord'=>"BTLR",'gris'=>"1"),
					2=>array('anch'=>'60%','alto'=>'18',  'nomb'=>" ".utf8_decode($arra_tabl[$f]->B),'alin'=>'L','tama'=>$this->tamano_letra+1,'bord'=>"BTLR",'gris'=>"0"),
				);
			}
		}
		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=array(
			1=>array('anch'=>'100%','alto'=>'9',  'nomb'=>" ",'alin'=>'J','tama'=>$this->tamano_letra+3,'bord'=>"",'mult'=>"1"),
		);
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
		//$this->Cell(ancho,alto,nombre,borde,calt,alin,llenado);
		//1=>array('anch'=>'50%','alto'=>'335',  'nfot'=>"$imag",'wfot'=>'475','hfot'=>'334','alin'=>'L','tama'=>$tama,'bord'=>""),
		$anch=$this->wPt-($this->lMargin+$this->rMargin);
		$this->SetFontSize(7);
		$this->SetFont('Times','');
		$this->SetTextColor(125);

		if($this->codi_inst)
			$this->logo="img/logos/inst_".str_pad($this->codi_inst,4,'0',STR_PAD_LEFT).".jpg";
			

		if($this->logo)
		{
			list($width, $height, $type, $attr) = getimagesize($this->logo);
			$factor=$width/$height;
			$alto_logo=45;
			$ancho_logo=$alto_logo*$factor;
			$this->Image($this->logo,($this->wPt-$ancho_logo)/2,$this->tMargin,'',$alto_logo);
			$this->SetY($this->tMargin+$alto_logo+2);
		}
		if($this->nomb_inst)
		{
			$this->Cell("$anch",10,utf8_decode($this->nomb_inst),'','','C','0');
			$this->Ln();
		}
		if($this->nomb_ofic)
		{
			$this->Cell("$anch",10,utf8_decode($this->nomb_ofic),'','','C','0');
			$this->Ln();
		}
		$this->Cell("$anch",1,' ','B','','C','0');
			$this->Ln();
		if($this->nomb_anno)
		{
			$this->SetFont('Times','I');
			$this->Cell("$anch",12,utf8_decode($this->nomb_anno),'','','C','0');
			$this->Ln();
		}
//$this->TextFont(utf8_decode("OPERADOR: ".$this->nomb_oper),$this->lMargin,570,7,'');
//$this->TextFont('BARREDA MAYHUA',$posx+29,$posy+5,'7','');
if($this->forzar_firma)
	$this->poner_firma_digital2();

		$this->Ln();
		$this->Ln();
		$posicion=$this->GetY();
		$this->SetY(780);
		$dire_ofic="ESQUINA PLAZA ESPAÑA S/N CERCADO - AREQUIPA - TELEFONO 054-382520";
		$this->Cell("$anch",10,utf8_decode($dire_ofic),'T','','C','0');
			$this->Ln();
		$this->SetY($posicion);
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
function poner_datos()
{
	$anch=$this->wPt-($this->lMargin+$this->rMargin);
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
		
				if($this->arra_dato[$p][$f][$c]['sety'])	//addPage()
					$this->SetY($this->tMargin+$this->arra_dato[$p][$f][$c]['sety']);

				if($this->arra_dato[$p][$f][$c]['addp'])	//addPage()
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
//							$this->Image($this->arra_dato[$p][$f][$c]['huel'],530,200,40,'');
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
