<?
/*******************************************************************************
* Software: FPDF - Acta de Notas Centro de Idiomas - SIGACI                                       *
* Version:  1.00                                                               *
* Date:     2007-03-26                                                         *
* Author:   Oliver Laos Caceres                                                *
*******************************************************************************/

require_once("fpdi.php");
define('FPDF_FONTPATH','font/');

class listado_pdf extends fpdi
{
    var $size = 9;           //tamanho de la letra por defecto (recomendado 9)
    var $espacio_linea = 11; //espacio en puntos PS para el salto de línea (recomendado 13)
    var $y;
    var $pag = 1;
    var $plantilla = "";
    var $asignaturas = "";
//------------------------------------------------------------------------------//

/**
* Constructor
* ver FPDF-Manual
* Por defecto se trabaja en unidades puntos
*/
function listado_pdf($orientation='P',$unit='pt',$format='A4',$plantilla="")
{
    parent::fpdi($orientation,$unit,$format);
    $this->SetFont('Arial','',$this->size);
    if ($plantilla != "")
    {
       $this->plantilla = $plantilla;
       $pagecount = $this->setSourceFile($plantilla);
       $tplidx = $this->ImportPage(1);
       $this->addPage();
       $this->useTemplate($tplidx);
    }
    else
       $this->addPage();
}
//------------------------------------------------------------------------------//

function TextFont($txt,$x,$y,$fsize="",$fstyle="",$ftype="")
{
    // Cambio en el tipo y/o estilo de la fuente
    if ($ftype!="" || $fstyle!="")
    {
       $ffamil_actual = $this->FontFamily;
       $fstyle_actual = $this->FontStyle;
       $this->SetFont($ftype,$fstyle);
    }

    // Cambio en el tamanho de la fuente
    if ( is_int($fsize) || is_float($fsize) )
    {
       $fsize_actual = $this->FontSizePt;
       $this->SetFontSize($fsize);
    }

    $this->Text($x,$y,$txt);
    $this->SetFont($ffamil_actual,$fstyle_actual,$fsize_actual);
}
//------------------------------------------------------------------------------//

function cabecera($datos)
{
    # Datos Requeridos
    list($nesc,$mont_total) = $datos;
    $mont_total = number_format($mont_total,2);

    $this->TextFont("REPORTE DE VOUCHERS -- MATRICULA 2008-A",200,50);
    $this->TextFont("ESCUELA:",100,75);
    $this->TextFont($nesc,150,75);
    $this->TextFont("MONTO TOTAL: S/.",350,75);
    $this->TextFont($mont_total,440,75);
    $this->y = 105;
    $this->TextFont("Nro.",31,100);
    $this->TextFont("CUI",72,100);
    $this->TextFont("Apellidos y Nombres",165,100);
    $this->TextFont("Monto",380,100);
    $this->TextFont("Nro. Voucher",450,100);
    $this->Line(30,103,568,103);
}
//------------------------------------------------------------------------------//

function registro($alumno)
{
    # Formato datos
    $nro = str_pad($alumno[0],2,"0",STR_PAD_LEFT);
    $nombre = strtoupper($alumno[2]);

    $this->salto_linea();
    $y = $this->y;

    $this->TextFont($nro,31,$y);
    $this->TextFont($alumno[1],60,$y);
    $this->TextFont($nombre,130,$y,8);
    $this->SetXY(370,$y);
    $this->Cell(40,0,$alumno[3],0,1,'R');
    $this->TextFont($alumno[4],430,$y);

    $this->Line(30,$y+3,568,$y+3);
}
//------------------------------------------------------------------------------//

function cierre()
{
  echo "SC";
}
//------------------------------------------------------------------------------//

# Ejecuta salto de linea
function salto_linea()
{
    if ($this->y > 765)
       $this->finalizar_pagina();
    $this->y = $this->y + $this->espacio_linea;
}
//------------------------------------------------------------------------------

function finalizar_pagina()
{
    if ( $this->plantilla )
    {
       //$pagecount = $this->setSourceFile($this->plantilla);
       $tplidx = $this->ImportPage(2);
       $this->addPage();
       $this->useTemplate($tplidx);
    }
    else
      $this->addPage();

    $this->y = 134;
}

}
?>