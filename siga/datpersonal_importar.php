<?php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

require_once "spreadsheets/vendor/autoload.php";
//$filename = "proyeccion.xlsx";
//header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheet‌​ml.sheet");
//header('Content-Disposition: attachment; filename="' . $filename. '"');

$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('datospers.xlsx');
$worksheet = $spreadsheet->getActiveSheet();
//$worksheet->setTitle("Mi Hoja");


for ($fil=2;$fil<=1000;$fil++) {
	//$meta=$worksheet->getCell("A".$fil)->getValue();
	$apepat=utf8_decode ( $worksheet->getCell("B".$fil)->getValue() );
	$apemat=utf8_decode ( $worksheet->getCell("C".$fil)->getValue() );
	$nombre=utf8_decode ( $worksheet->getCell("D".$fil)->getValue() );

	$fecnacim=$worksheet->getCell("E".$fil)->getValue();
	$fecnac = date('Y-m-d', PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($fecnacim));


	$estciv=$worksheet->getCell("F".$fil)->getValue();
	$dni=$worksheet->getCell("G".$fil)->getValue();
	$lugnac=utf8_decode ( $worksheet->getCell("H".$fil)->getValue() );
	$direcc=utf8_decode ( $worksheet->getCell("I".$fil)->getValue() );
	$distri=utf8_decode ( $worksheet->getCell("J".$fil)->getValue() );
	$refdom=utf8_decode ( $worksheet->getCell("K".$fil)->getValue() );

	$tlffijo= $worksheet->getCell("L".$fil)->getValue() ;
	$celular= $worksheet->getCell("M".$fil)->getValue() ;
	$emailp= $worksheet->getCell("N".$fil)->getValue() ;
	$emaili= $worksheet->getCell("O".$fil)->getValue() ;
	$persona1=utf8_decode ( $worksheet->getCell("P".$fil)->getValue() );
	$celuper1= $worksheet->getCell("Q".$fil)->getValue() ;
	$persona2=utf8_decode ( $worksheet->getCell("R".$fil)->getValue() );
	$celuper2= $worksheet->getCell("S".$fil)->getValue() ;
	$grainstr=utf8_decode ( $worksheet->getCell("T".$fil)->getValue() );
	$profesio=utf8_decode ( $worksheet->getCell("U".$fil)->getValue() );
	$otraprof=utf8_decode ( $worksheet->getCell("V".$fil)->getValue() );
	$nrocoleg= $worksheet->getCell("W".$fil)->getValue() ;

	$fecingre= $worksheet->getCell("X".$fil)->getValue() ;
	$fecing = date('Y-m-d', PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($fecingre));


	$cargo= $worksheet->getCell("Y".$fil)->getValue() ;
	$depend= $worksheet->getCell("Z".$fil)->getValue() ;
	$cargo=0;
	$depend=0;

	$reglab= $worksheet->getCell("AA".$fil)->getValue() ;
	$plaza= utf8_decode ($worksheet->getCell("AB".$fil)->getValue() );

	$conyugue= utf8_decode ($worksheet->getCell("AC".$fil)->getValue() );
	$hijo1= utf8_decode ($worksheet->getCell("AD".$fil)->getValue() );
	$fecnac1hj = $worksheet->getCell("AE".$fil)->getValue() ;
	$fecnac1 = date('Y-m-d', PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($fecnac1hj));

	$sexo1= $worksheet->getCell("AF".$fil)->getValue() ;
	$hijo2= utf8_decode ($worksheet->getCell("AG".$fil)->getValue() );
	$fecnac2hj = $worksheet->getCell("AH".$fil)->getValue() ;
	$fecnac2 = date('Y-m-d', PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($fecnac2hj));

	$sexo2= $worksheet->getCell("AI".$fil)->getValue() ;
	$hijo3= utf8_decode ($worksheet->getCell("AJ".$fil)->getValue() );
	$fecnac3hj = $worksheet->getCell("AK".$fil)->getValue() ;
	$fecnac3 = date('Y-m-d', PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($fecnac3hj));

	$sexo3= $worksheet->getCell("AL".$fil)->getValue() ;
	$hijo4= utf8_decode ($worksheet->getCell("AM".$fil)->getValue() );
	$fecnac4hj = $worksheet->getCell("AN".$fil)->getValue() ;
	$fecnac4 = date('Y-m-d', PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($fecnac4hj));

	$sexo4= $worksheet->getCell("AO".$fil)->getValue() ;
	$hijo5= utf8_decode ($worksheet->getCell("AP".$fil)->getValue() );
	$fecnac5hj = $worksheet->getCell("AQ".$fil)->getValue() ;
	$fecnac5 = date('Y-m-d', PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($fecnac5hj));

	$sexo5= $worksheet->getCell("AR".$fil)->getValue() ;

	$nompadre= utf8_decode ($worksheet->getCell("AS".$fil)->getValue() );
	$dirpadre= utf8_decode ($worksheet->getCell("AT".$fil)->getValue() );
	$nommadre= utf8_decode ($worksheet->getCell("AU".$fil)->getValue() );
	$dirmadre= utf8_decode ($worksheet->getCell("AV".$fil)->getValue() );
	$essalud= $worksheet->getCell("AW".$fil)->getValue() ;
	$centate= utf8_decode ($worksheet->getCell("AX".$fil)->getValue() );
	$eps= $worksheet->getCell("AY".$fil)->getValue() ;
	$tpsandre= $worksheet->getCell("AZ".$fil)->getValue() ;
	$alerenf= utf8_decode ($worksheet->getCell("BA".$fil)->getValue() );
	$discapa= utf8_decode ($worksheet->getCell("BB".$fil)->getValue() );

	$conadis= $worksheet->getCell("BC".$fil)->getValue() ;
	$otroidi= utf8_decode ($worksheet->getCell("BD".$fil)->getValue() );
	$hobfut= $worksheet->getCell("BE".$fil)->getValue() ;
	$hobbas= $worksheet->getCell("BF".$fil)->getValue() ;
	$hobnat= $worksheet->getCell("BG".$fil)->getValue() ;
	$hobpin= $worksheet->getCell("BH".$fil)->getValue() ;
	$hobfro= $worksheet->getCell("BI".$fil)->getValue() ;
	$hobbai= $worksheet->getCell("BJ".$fil)->getValue() ;
	$hobcoc= $worksheet->getCell("BK".$fil)->getValue() ;
	$otrahab= utf8_decode ($worksheet->getCell("BL".$fil)->getValue() );

	if ($dni=="") { break; }

	$result_depe=$Db->query("SELECT count( * ) AS cant FROM `mp_personal` where pers_dni='".$dni."' ");
	$cant=$result_depe[0]['cant'];
	if ($cant==0) {
			$result=$Db->insert('mp_personal',
			['pers_apepat'=>$apepat,'pers_apemat'=>$apemat,'pers_nombres'=> $nombre,
			'pers_fecnac'=>$fecnac,'pers_estciv'=>$estciv,'pers_dni'=>$dni,
			'pers_lugarnac'=>$lugnac, 'pers_dire'=>$direcc,'pers_distr'=>$distri,
			'pers_refedir'=>$refdom, 'pers_tlffijo'=>$tlffijo,'pers_celu'=>$celular,
			'pers_emailper'=>$emailp, 'pers_emailinst'=>$emaili,
			'pers_nomape_per1'=>$persona1,'pers_nrocel_per1'=>$celuper1,
			'pers_nomape_per2'=>$persona2,'pers_nrocel_per2'=>$celuper2,
			'pers_grains'=>$grainstr,'pers_prof1'=>$profesio,'pers_prof2'=>$otraprof,
			'pers_nrocole'=>$nrocoleg,'pers_fecing'=>$fecing,'pers_cargo'=>$cargo,
			'pers_depe'=>$depend,'pers_reglab'=>$reglab,'pers_plapres'=>$plaza,
			'pers_conyuge'=>$conyugue,
			'pers_hijo1'=>$hijo1,'pers_fechijo1'=>$fecnac1,'pers_sexohijo1'=>$sexo1,
			'pers_hijo2'=>$hijo2,'pers_fechijo2'=>$fecnac2,'pers_sexohijo2'=>$sexo2,
			'pers_hijo3'=>$hijo3,'pers_fechijo3'=>$fecnac3,'pers_sexohijo3'=>$sexo3,
			'pers_hijo4'=>$hijo4,'pers_fechijo4'=>$fecnac4,'pers_sexohijo4'=>$sexo4,
			'pers_hijo5'=>$hijo5,'pers_fechijo5'=>$fecnac5,'pers_sexohijo5'=>$sexo5,
			'pers_padre'=>$nompadre,'pers_padredir'=>$dirpadre,
			'pers_madre'=>$nommadre,'pers_madredir'=>$dirmadre,
			'pers_essalud'=>$essalud,'pers_centroate'=>$centate,'pers_eps'=>$eps,
			'pers_tpsangre'=>$tpsandre,'pers_alergenf'=>$alerenf,'pers_discap'=>$discapa,
			'pers_conadis'=>$conadis,'pers_otroidi'=>$otroidi,
			'pers_hobfut'=>$hobfut,'pers_hobbas'=>$hobbas,'pers_hobnat'=>$hobnat,
			'pers_hobpin'=>$hobpin,'pers_hobfro'=>$hobfro,'pers_hobbai'=>$hobbai,
			'pers_hobcoc'=>$hobcoc,'pers_otrahab'=>$otrahab ]);

	}



}
//$worksheet->setCellValue("D" . ($i + 1), "=SUM(D9:D$i)");

//$column = $worksheet->getColumnDimension("A");
//$column->setAutoSize(true);



//$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
//$writer->save("php://output");

?>
