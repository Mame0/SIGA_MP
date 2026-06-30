<?php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

require_once "spreadsheets/vendor/autoload.php";

$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('PEA 1057 - MARZO INF.xlsx');
$spreadsheet->setActiveSheetIndex(0);
$worksheet = $spreadsheet->getActiveSheet();
$montos1057[0]=0;
for ($fil=2;$fil<=212;$fil++) {
	$dniper=$worksheet->getCell("B".$fil)->getValue();
	$remune=$worksheet->getCell("G".$fil)->getValue();
	if ($dniper!="") {
		$montos1057[$dniper]=$remune;
	}
}

$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('HORAS_DE_TRABAJO_NO_LABORADAS.xlsx');
$spreadsheet->setActiveSheetIndex(0);
$worksheet = $spreadsheet->getActiveSheet();

for ($fil=5;$fil<=390;$fil++) {//390
	$dniper=$worksheet->getCell("A".$fil)->getValue();
	$tothoras = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(14,$fil)->getCalculatedValue();//columna N
	$regimen=$worksheet->getCell("D".$fil)->getValue();

	$xxx=$tothoras*24;
	$yyy=intval($xxx);
	$zzz=round(($xxx-$yyy)*60,0);

	$totmin=($yyy*60)+$zzz;



	if ($dniper=="") { break; }

	if ($tothoras!=0) {
		$remuneracion=0;
		if ($regimen==1057) {
			$remuneracion=$montos1057[$dniper];
		} else {
			$result_depe=$Db->query("SELECT escremunerabasica
			FROM `mp_personal` left join mp_plan_escalaremunerativa on `mp_personal`.pers_cargo=mp_plan_escalaremunerativa.n_codigo
			where pers_dni='".$dniper."' ");
			$remuneracion=$result_depe[0]['escremunerabasica'];
			if ($remuneracion==0) {
			$remuneracion=$montos1057[$dniper];
			}
		}
		//30*8*60 = 14400 minutos al mes
		$rem_x_minuto=$remuneracion/14400;
		$montominutos=round($totmin*$rem_x_minuto,2);
		$spreadsheet->setActiveSheetIndex(0)
				->setCellValue("O".$fil,   $totmin  )
				->setCellValue("P".$fil,   $remuneracion  )
				->setCellValue("Q".$fil,   $rem_x_minuto  )
				->setCellValue("R".$fil,   $montominutos  );
	}

}


$spreadsheet->setActiveSheetIndex(1);
$worksheet = $spreadsheet->getActiveSheet();

for ($fil=5;$fil<=82;$fil++) {
	$dniper=$worksheet->getCell("A".$fil)->getValue();
	$tothoras = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(16,$fil)->getCalculatedValue();//columna P
	$regimen=$worksheet->getCell("D".$fil)->getValue();

	$xxx=$tothoras*24;
	$yyy=intval($xxx);
	$zzz=round(($xxx-$yyy)*60,0);

	$totmin=($yyy*60)+$zzz;



	if ($dniper=="") { break; }

	if ($tothoras!=0) {
		$remuneracion=0;
		if ($regimen==1057) {
			$remuneracion=$montos1057[$dniper];
		} else {
			$result_depe=$Db->query("SELECT escremunerabasica
			FROM `mp_personal` left join mp_plan_escalaremunerativa on `mp_personal`.pers_cargo=mp_plan_escalaremunerativa.n_codigo
			where pers_dni='".$dniper."' ");
			$remuneracion=$result_depe[0]['escremunerabasica'];

			if ($remuneracion==0) {
			$remuneracion=$montos1057[$dniper];
			}
		}
		//30*8*60 = 14400 minutos al mes
		$rem_x_minuto=$remuneracion/14400;
		$montominutos=round($totmin*$rem_x_minuto,2);
		$spreadsheet->setActiveSheetIndex(1)
				->setCellValue("Q".$fil,   $totmin  )
				->setCellValue("R".$fil,   $remuneracion  )
				->setCellValue("S".$fil,   $rem_x_minuto  )
				->setCellValue("T".$fil,   $montominutos  );
	}

}



//$worksheet->setCellValue("D" . ($i + 1), "=SUM(D9:D$i)");

//$column = $worksheet->getColumnDimension("A");
//$column->setAutoSize(true);



//$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
//$writer->save("php://output");

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save("resultado.xlsx");


?>
