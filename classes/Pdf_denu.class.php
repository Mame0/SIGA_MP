<?
//require_once('TCPDF/tcpdf.php');
//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

//require_once('TCPDF/tcpdf.php');
//class documento_pdf extends TCPDF
require_once("pdfwrite/fpdi.php");
class documento_pdf extends fpdi
{
	public $arra_dato = array();
	function documento_pdf($orientation='P',$unit='pt',$format='A4',$plantilla="")
	{
		parent::fpdi($orientation,$unit,$format);
		//parent::TCPDF($orientation,$unit,$format);
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
	function poner_frame_inicio()
	{
		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=array(
			1=>array('anch'=>'100%','alto'=>'3',  'nomb'=>"",'alin'=>'C','tama'=>$this->size_font,'bord'=>"TLR")
		);
	}
	function poner_frame_ln()
	{
		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=array(
			1=>array('anch'=>'100%','alto'=>'3',  'nomb'=>"",'alin'=>'C','tama'=>$this->size_font,'bord'=>"LR")
		);
	}
	function poner_frame_final()
	{
		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=array(
			1=>array('anch'=>'100%','alto'=>'3',  'nomb'=>"",'alin'=>'C','tama'=>$this->size_font,'bord'=>"LRB")
		);
	}
	function poner_titulo($contenido)
	{
		$this->poner_frame_inicio();
		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=array(
			1=>array('anch'=>'1%','alto'=>'17',  'nomb'=>"",'alin'=>'C','tama'=>$this->size_font,'bord'=>"L"),
			2=>array('anch'=>'98%','alto'=>'17',  'nomb'=>"$contenido",'alin'=>'C','tama'=>$this->size_font+5,'bord'=>""),
			3=>array('anch'=>'1%','alto'=>'17',  'nomb'=>"",'alin'=>'C','tama'=>$this->size_font,'bord'=>"R"),
		);
		$this->poner_frame_final();
	}
	function poner_subtitulo($contenido)
	{
		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=array(
			1=>array('anch'=>'100%','alto'=>'17',  'nomb'=>$contenido,'alin'=>'L','tama'=>$this->size_font,'bord'=>"")
		);
	}
	function poner_contenido($contenido,$ancho)
	{
		$i=1;
		$arra_cont[$i]=array('anch'=>'1%','alto'=>'17',  'nomb'=>'','alin'=>'L','tama'=>$this->size_font,'bord'=>"L");
		foreach($contenido as $row => $cont)
		{
			if($i==1)
				$alin='L';
			else
				$alin='R';
			$i++;
			$arra_cont[$i]=array('anch'=>$ancho[$i-1],'alto'=>'17',  'nomb'=>$row, 'alin'=>$alin,'tama'=>$this->size_font,'bord'=>"");
			$i++;
			$arra_cont[$i]=array('anch'=>$ancho[$i-1],'alto'=>'17',  'nomb'=>$cont,'alin'=>'L','tama'=>$this->size_font+3,'bord'=>"TLRB");
		}
		$i++;
		$arra_cont[$i]=array('anch'=>'1%','alto'=>'17',  'nomb'=>'','alin'=>'L','tama'=>$this->size_font,'bord'=>"R");

		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=$arra_cont;
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
