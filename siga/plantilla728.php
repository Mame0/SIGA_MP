<?php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

require_once "spreadsheets/vendor/autoload.php";
$filename = "proyeccion728.xlsx";
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheet‌​ml.sheet");
header('Content-Disposition: attachment; filename="' . $filename. '"');

$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('plantillas/plantilla728.xlsx');
$worksheet = $spreadsheet->getActiveSheet();

$result_pagi=$Db->query("select mp_personal.*, esccargo, escnivel, escremunerabasica, escbenefextra, escbonificajurisdiccional, escgastosope, escaguinaldo, esccafae
from mp_personal left join mp_plan_escalaremunerativa on
mp_personal.pers_cargo=mp_plan_escalaremunerativa.n_codigo
where escdecretoley=728 and activo=1 order by pers_apepat asc, pers_apemat asc, pers_nombres asc ");
$nfil=0;
foreach($result_pagi as $rows) {
	$nfil++;
	$table01[$nfil][1] = $rows['pers_dni'] ;
	$table01[$nfil][2] = utf8_encode($rows['pers_apepat'] . " " . $rows['pers_apemat'] . " " . $rows['pers_nombres']);
	$table01[$nfil][3] = $rows['meta'] ;
	$table01[$nfil][4] = $rows['pers_fecing'] ;
	$table01[$nfil][5] = $rows['esccargo'] ;
	$table01[$nfil][6] = ($rows['eps']==1)?"RIMAC EPS EMPRESA PRESTADORA DE SALUD":"" ;
	$table01[$nfil][7] = "-";
	$table01[$nfil][8] = $rows['esccargo'] ;
	$table01[$nfil][9] = ($rows['activo']==1)?"ACTIVO":"SUSPENDIDO" ;


	if ($rows['activo']==1) {
		$table02[$nfil][1] = $rows['escremunerabasica'] ;
		$table03[$nfil][1] = $rows['clas_haberes'] ;
		$table04[$nfil][1] = ($rows['asignacionfamiliar']==1) ? (930*0.1) : 0 ;
	} else {
		$table02[$nfil][1] = 0;
		$table03[$nfil][1] = $rows['clas_haberes'] ;
		$table04[$nfil][1] = "";
	}
}

$worksheet->fromArray($table01, null, "B3");
$worksheet->fromArray($table02, null, "K3");
$worksheet->fromArray($table04, null, "M3");
$worksheet->fromArray($table03, null, "O3");


$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save("php://output");

?>
