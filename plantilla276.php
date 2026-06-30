<?php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

require_once "spreadsheets/vendor/autoload.php";
$filename = "proyeccion276.xlsx";
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheet‌​ml.sheet");
header('Content-Disposition: attachment; filename="' . $filename. '"');

$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('plantillas/plantilla276.xlsx');
$worksheet = $spreadsheet->getActiveSheet();

$result_pagi=$Db->query("select mp_personal.*, esccargo, escnivel, escremunerabasica, escbenefextra, escbonificajurisdiccional, escgastosope, escaguinaldo, esccafae
from mp_personal left join mp_plan_escalaremunerativa on
if(codcargopea<>0, mp_personal.codcargopea, mp_personal.pers_cargo)=mp_plan_escalaremunerativa.n_codigo
where escdecretoley=276 and activo=1 order by pers_apepat asc, pers_apemat asc, pers_nombres asc ");
$nfil=0;
foreach($result_pagi as $rows) {
	$nfil++;
	$table01[$nfil][1] = "'" . $rows['pers_dni'] ;
	$table01[$nfil][2] = "'" . $rows['pers_dni'] ;
	$table01[$nfil][3] = utf8_encode($rows['pers_apepat'] . " " . $rows['pers_apemat'] . " " . $rows['pers_nombres']);
	$table01[$nfil][4] = $rows['pers_fecing'] ;
//	$table01[$nfil][5] = "'" . $rows['pers_cargo'] ;
	$table01[$nfil][5] = ($rows['codcargopea']==0) ? $rows['esccargo'] : "'" . $rows['pers_cargo'] ;
	$table01[$nfil][6] = "-" ;
	$table01[$nfil][7] = ($rows['eps']==1)?"RIMAC EPS EMPRESA PRESTADORA DE SALUD":"" ;
	$table01[$nfil][8] = $rows['meta'] ;
//	$table01[$nfil][9] = ($rows['codcargopea']==0)?"":$rows['codcargopea'] ;
	$table01[$nfil][9] = ($rows['codcargopea']==0)?"":$rows['esccargo'] ;//($rows['codcargopea']==0)?"":$rows['codcargopea'] ;
	$table01[$nfil][10] = $rows['esccargo'] ;

	$table01[$nfil][11] = $rows['escnivel'] ;
	$table01[$nfil][12] = "400" ; // escolaridad cuando es enero
	$table01[$nfil][13] = ($rows['activo']==1)?"ACTIVO":"SUSPENDIDO" ;


	if ($rows['activo']==1) {
		$table02[$nfil][1] = $rows['escremunerabasica'] ;
		$table03[$nfil][1] = $rows['clas_haberes'] ;

		$table04[$nfil][1] = ($rows['escbenefextra']==0)?"":$rows['escbenefextra'] ;
		$table05[$nfil][1] = $rows['clas_benefextra'] ;

		$table06[$nfil][1] = ($rows['escbonificajurisdiccional']==0)?"":$rows['escbonificajurisdiccional'] ;
		$table07[$nfil][1] = $rows['clas_bonofiscal'] ;
	} else {
		$table02[$nfil][1] = 0;
		$table03[$nfil][1] = $rows['clas_haberes'] ;

		$table04[$nfil][1] = "";
		$table05[$nfil][1] = $rows['clas_benefextra'] ;

		$table06[$nfil][1] = 0;
		$table07[$nfil][1] = $rows['clas_bonofiscal'] ;
	}

	$table10[$nfil][1] = ($rows['clas_25retardo']=="")?"":($rows['escremunerabasica']*0.25) ;
	$table11[$nfil][1] = $rows['clas_25retardo'] ;

	$table14[$nfil][1] = ($rows['clas_cafae']=="")?"":$rows['esccafae'] ;
	$table15[$nfil][1] = $rows['clas_cafae'] ;

	$table17[$nfil][1] = $rows['clas_eps225'] ;
	$table19[$nfil][1] = $rows['clas_fondopens6porc'] ;


}


//gastos operativos y aguinaldo salen del cargo titular
$result_pagi=$Db->query("select clas_go, clas_aguinaldo, codcargopea, activo, escgastosope, escaguinaldo
from mp_personal left join mp_plan_escalaremunerativa on
mp_personal.pers_cargo=mp_plan_escalaremunerativa.n_codigo
where escdecretoley=276 and activo=1 order by pers_apepat asc, pers_apemat asc, pers_nombres asc ");
$nfil=0;
foreach($result_pagi as $rows) {
	$nfil++;
	if ($rows['codcargopea']==5) {
		$resultx=$Db->query("select escgastosope, escaguinaldo
		from mp_plan_escalaremunerativa where escdecretoley=276 and n_codigo=5 ");
		foreach($resultx as $rowpea) {
			$monto=$rowpea['escgastosope'];
		}

		if ($rows['activo']==1) {
			$table08[$nfil][1] = $monto ;
			$table09[$nfil][1] = $rows['clas_go'] ;

			$table12[$nfil][1] = ($rows['escaguinaldo']==0)?"":$rows['escaguinaldo'] ;
			$table13[$nfil][1] = $rows['clas_aguinaldo'] ;
		} else {
			$table08[$nfil][1] = 0;
			$table09[$nfil][1] = $rows['clas_go'] ;

			$table12[$nfil][1] = 0;
			$table13[$nfil][1] = $rows['clas_aguinaldo'] ;
		}
	} else {
		if ($rows['activo']==1) {
			$table08[$nfil][1] = ($rows['escgastosope']==0)?"":$rows['escgastosope'] ;
			$table09[$nfil][1] = $rows['clas_go'] ;

			$table12[$nfil][1] = ($rows['escaguinaldo']==0)?"":$rows['escaguinaldo'] ;
			$table13[$nfil][1] = $rows['clas_aguinaldo'] ;
		} else {
			$table08[$nfil][1] = 0;
			$table09[$nfil][1] = $rows['clas_go'] ;

			$table12[$nfil][1] = 0;
			$table13[$nfil][1] = $rows['clas_aguinaldo'] ;
		}
	}
}



$worksheet->fromArray($table01, null, "B3");
$worksheet->fromArray($table02, null, "O3");
$worksheet->fromArray($table03, null, "Q3");

$worksheet->fromArray($table04, null, "R3");
$worksheet->fromArray($table05, null, "T3");

$worksheet->fromArray($table06, null, "U3");
$worksheet->fromArray($table07, null, "W3");

$worksheet->fromArray($table08, null, "X3");
$worksheet->fromArray($table09, null, "Z3");

$worksheet->fromArray($table10, null, "AA3");
$worksheet->fromArray($table11, null, "AC3");

$worksheet->fromArray($table12, null, "AD3");
$worksheet->fromArray($table13, null, "AF3");

$worksheet->fromArray($table14, null, "AG3");
$worksheet->fromArray($table15, null, "AI3");


$worksheet->fromArray($table17, null, "AQ3");
$worksheet->fromArray($table19, null, "AT3");


$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save("php://output");

?>
