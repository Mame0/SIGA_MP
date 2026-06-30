<?php
require_once(dirname(__FILE__).'/tcpdf_sioj.php');
class TCPDF_OFICIOS extends TCPDF_SIOJ {
	public function Iniciar()
	{
		// set document information
		$this->SetCreator('CSJAR');
		$this->SetAuthor('SIOJ Alimentos');
		$this->SetTitle('SIOJ Alimentos');
		$this->SetSubject('Documentos Digitales');

		// set image scale factor
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);

		$this->tamano_letra=9;

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
//$this->logo='/Applications/MAMP/htdocs/alimentos/img/logos/inst_'.str_pad($this->codi_inst,4,'0',STR_PAD_LEFT).'.jpg';
$this->logo=substr(__DIR__,0,-14).'/img/logos/inst_'.str_pad($this->codi_inst,4,'0',STR_PAD_LEFT).'.jpg';
//echo"<HR>".$this->logo."<HR>";
//die();

	}
	function datos_cabecera($codi_inst,$nomb_inst,$nomb_ofic,$dire_sede,$tele_sede,$nomb_anno)
	{
                $this->codi_inst=$codi_inst;
                $this->nomb_inst=$nomb_inst;
                $this->nomb_ofic=$nomb_ofic;
                $this->nomb_anno=$nomb_anno;
                $this->dire_sede=$dire_sede;
                $this->tele_sede=$tele_sede;
	}
	function poner_fecha_documento($contenido)
	{
		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=array(
			1=>array('anch'=>'100%','alto'=>'5',  'nomb'=>utf8_decode("$contenido"),'alin'=>'R','tama'=>$this->tamano_letra+3,'bord'=>"",'mult'=>"1"),
		);
	}
	function poner_numero_documento($contenido)
	{
		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=array(
			1=>array('anch'=>'100%','alto'=>'5',  'nomb'=>"$contenido\n",'alin'=>'J','tama'=>$this->tamano_letra+3,'bord'=>"",'mult'=>"1",'form'=>"BU"),
		);
	}
	function poner_dirigido_a1($contenido)
	{
		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=array(
			1=>array('anch'=>'100%','alto'=>'5',  'nomb'=>"$contenido\n",'alin'=>'J','tama'=>$this->tamano_letra+3,'bord'=>"",'mult'=>"",'form'=>"B"),
		);
	}
	function poner_dirigido_a2($contenido)
	{
		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=array(
			1=>array('anch'=>'100%','alto'=>'5',  'nomb'=>"$contenido\n",'alin'=>'J','tama'=>$this->tamano_letra+3,'bord'=>"",'mult'=>"1",'form'=>"B"),
		);
	}
	function poner_subtitulo($titulo,$contenido)
	{
		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=array(
			1=>array('anch'=>'20%','alto'=>'5',  'nomb'=>$titulo,'alin'=>'L','tama'=>$this->tamano_letra+3,'bord'=>"",'mult'=>"0"),
			2=>array('anch'=>'80%','alto'=>'5',  'nomb'=>$contenido."\n",'alin'=>'J','tama'=>$this->tamano_letra+3,'bord'=>"",'mult'=>"1"),
		);
	}
	function poner_parrafo($contenido)
	{
		$this->contador_fila++;
		$this->arra_dato[1][$this->contador_fila]=array(
			1=>array('anch'=>'100%','alto'=>'5',  'nomb'=>$this->sangria."$contenido\n",'alin'=>'J','tama'=>$this->tamano_letra+3,'bord'=>"",'mult'=>"1"),
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
					1=>array('anch'=>'40%','alto'=>'6',  'nomb'=>utf8_decode($arra_tabl[$f]->A),'alin'=>'L','tama'=>$this->tamano_letra+1,'bord'=>"BT",'gris'=>"1"),
					2=>array('anch'=>'60%','alto'=>'6',  'nomb'=>" ".utf8_decode($arra_tabl[$f]->B),'alin'=>'L','tama'=>$this->tamano_letra+1,'bord'=>"BT",'gris'=>"0"),
				);
			}
		}
	}
	public function Header()
	{
		if($this->logo)
                {
			switch($this->codi_inst)
			{
				case 3:		$alto_logo=15.6;
						$solo_logo=1;
						break;
				default:	$alto_logo=15;
						$solo_logo=0;
						break;
			}
			//$image_file = K_PATH_IMAGES.'logo_example.jpg';
			$this->Image($this->logo, 'C', 13, '', $alto_logo, 'JPG', false, 'C', false, 300, 'C', false, false, 0, false, false, false);
			$this->SetFont('Times', '', 7);
			$this->SetTextColor(125);
			$this->SetY(30);
			if($solo_logo==0)
			{
				$this->Cell('', 3.5, strtoupper($this->nomb_inst), 0 , false, 'C', 0, '', 0, false, 'M', 'M');
				$this->ln();
				$this->Cell('', 3.5, strtoupper($this->nomb_ofic),'B', false, 'C', 0, '', 0, false, 'M', 'M');
				$this->SetFont('Times', 'I', 9);
			}
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
                $this->Cell(0, 4, $this->dire_sede." - TELEFONO ".$this->tele_sede, 'T', false, 'C', 0, '', 0, false, 'T', 'M');
        }
} // END OF CLASS
