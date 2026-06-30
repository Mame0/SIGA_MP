<?php
require_once(dirname(__FILE__).'/tcpdf_sioj.php');
class TCPDF_DEMANDAS extends TCPDF_SIOJ {
	public function Iniciar()
	{
		// set document information
		$this->SetCreator('CSJAR');
		$this->SetAuthor('SIOJ Alimentos');
		$this->SetTitle('SIOJ Alimentos');
		$this->SetSubject('Documentos Digitales');

		// set image scale factor
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);

		$this->tamano_letra=6;

		// set margins  $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf_margin_izq=30;
		$pdf_margin_der=30;
		$pdf_margin_sup=45;
		$pdf_margin_inf=20;
		$pdf_margin_cab=5;
		$pdf_margin_pie=10;
		$this->SetMargins($pdf_margin_izq,$pdf_margin_sup,$pdf_margin_der);
		$this->SetAutoPageBreak(TRUE, $pdf_margin_inf);
		$this->SetHeaderMargin($pdf_margin_cab);
		$this->SetFooterMargin($pdf_margin_pie);

		// set header and footer fonts
		$this->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$this->logo='/Applications/MAMP/htdocs/alimentos/img/logos/inst_0001.jpg';
	}
	function poner_frame_inicio()
	{
		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=array(
			1=>array('anch'=>'100%','alto'=>'2',  'nomb'=>"",'alin'=>'C','tama'=>'1','bord'=>"TLR")
		);
	}
	function poner_frame_ln()
	{
		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=array(
			1=>array('anch'=>'100%','alto'=>'1',  'nomb'=>"",'alin'=>'C','tama'=>$this->tamano_letra,'bord'=>"LR")
		);
	}
	function poner_frame_final()
	{
		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=array(
			1=>array('anch'=>'100%','alto'=>'2',  'nomb'=>"",'alin'=>'C','tama'=>'1','bord'=>"LRB")
		);
	}
	function poner_titulo($contenido)
	{
		$this->poner_frame_inicio();
		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=array(
			1=>array('anch'=>'1%','alto'=>'6',  'nomb'=>"",'alin'=>'C','tama'=>$this->tamano_letra,'bord'=>"L"),
			2=>array('anch'=>'98%','alto'=>'6',  'nomb'=>"$contenido",'alin'=>'C','tama'=>$this->tamano_letra+5,'bord'=>""),
			3=>array('anch'=>'1%','alto'=>'6',  'nomb'=>"",'alin'=>'C','tama'=>$this->tamano_letra,'bord'=>"R"),
		);
		$this->poner_frame_final();
	}
	function poner_subtitulo($contenido)
	{
		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=array(
			1=>array('anch'=>'100%','alto'=>'4',  'nomb'=>$contenido,'alin'=>'L','tama'=>$this->tamano_letra,'bord'=>"")
		);
	}
	function poner_contenido($contenido,$ancho)
	{
		$i=1;
		$arra_cont[$i]=array('anch'=>'1%','alto'=>'6',  'nomb'=>'','alin'=>'L','tama'=>$this->tamano_letra,'bord'=>"L");
		foreach($contenido as $row => $cont)
		{
			if($i==1)
				$alin='L';
			else
				$alin='R';
			$i++;
			$arra_cont[$i]=array('anch'=>$ancho[$i-1],'alto'=>'6',  'nomb'=>$row, 'alin'=>$alin,'tama'=>$this->tamano_letra,'bord'=>"");
			$i++;
			$arra_cont[$i]=array('anch'=>$ancho[$i-1],'alto'=>'6',  'nomb'=>$cont,'alin'=>'L','tama'=>$this->tamano_letra+3,'bord'=>"TLRB");
		}
		$i++;
		$arra_cont[$i]=array('anch'=>'1%','alto'=>'6',  'nomb'=>'','alin'=>'L','tama'=>$this->tamano_letra,'bord'=>"R");

		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=$arra_cont;
	}
	function datos_cabecera($codi_inst,$nomb_inst,$nomb_ofic,$nomb_anno)
	{
                $this->codi_inst=$codi_inst;
                $this->nomb_inst=$nomb_inst;
                $this->nomb_ofic=$nomb_ofic;
                $this->nomb_anno=$nomb_anno;
	}
	public function Header()
	{
		if($this->logo)
                {
			//$image_file = K_PATH_IMAGES.'logo_example.jpg';
                        $alto_logo=15;
			$this->Image($this->logo, 'C', 13, '', $alto_logo, 'JPG', false, 'C', false, 300, 'C', false, false, 0, false, false, false);
			$this->SetFont('Times', '', 7);
			$this->SetTextColor(125);
			$this->SetY(30);
			$this->Cell('', 3.5, 'CORTE SUPERIOR DE JUSTICIA DE AREQUIPA', 0, false, 'C', 0, '', 0, false, 'M', 'M');
			$this->ln();
			$this->Cell('', 3.5, 'COORDINACION DE ESTUDIOS PROYECTOS Y RACIONALIZACION','B', false, 'C', 0, '', 0, false, 'M', 'M');
			$this->SetFont('Times', 'I', 9);
			$this->ln();
			$this->Cell('', 4, 'AÑO DE LA UNIVERSALIZACIÓN DE LA SALUD','', false, 'C', 0, '', 0, false, 'T', 'M');
                }
        }
	public function Footer() {
                // Position at 15 mm from bottom
                $this->SetY(-20);
                // Set font
                $this->SetFont('helvetica', 'I', 8);
		$this->SetTextColor(125);
                // Page number
                //$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 1, false, 'C', 0, '', 0, false, 'T', 'M');
                $this->Cell(0, 4, 'ESQUINA PLAZA ESPAÑA S/N CERCADO - AREQUIPA - TELEFONO 054382520', 'T', false, 'C', 0, '', 0, false, 'T', 'M');
        }
} // END OF CLASS
