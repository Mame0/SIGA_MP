<?

	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;



require_once "spreadsheets/vendor/autoload.php";
$filename = "compensacion_horas.xlsx";

$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('plantillas/plantilla_compensacion_horas.xlsx');
$worksheet = $spreadsheet->getActiveSheet();

if(isset($_GET['exp'])) {

	$result_pagi=$Db->query("select mp_personal.*, esccargo
	from mp_personal left join mp_plan_escalaremunerativa on mp_personal.pers_cargo=mp_plan_escalaremunerativa.n_codigo
	order by pers_apepat asc, pers_apemat asc, pers_nombres asc ");
	$nfil=0;
	$nfil2=0;
	$nfil3=0;
	$table01[1][1] = "";
	$table02[1][1] = "";
	$table03[1][1] = "";
	$codpers[1] = 0;
	foreach($result_pagi as $rows) {
		if ($rows['activo']==1) {
			$nfil++;
			$table01[$nfil][1] = "'" . $rows['pers_dni'] ;
			$table01[$nfil][2] = utf8_encode($rows['pers_apepat'] . " " . $rows['pers_apemat'] . " " . $rows['pers_nombres']);
			$table01[$nfil][3] = $rows['esccargo'] ;
			$table01[$nfil][4] = $rows['pers_reglab'] ;
			$table01[$nfil][5] = ($rows['activo']==1)?"ACTIVO":"INACTIVO" ;
			$table01[$nfil][6] = $rows['pers_fecing'] ;
		} else {
			$nfil2++;
			$table02[$nfil2][1] = "'" . $rows['pers_dni'] ;
			$table02[$nfil2][2] = utf8_encode($rows['pers_apepat'] . " " . $rows['pers_apemat'] . " " . $rows['pers_nombres']);
			$table02[$nfil2][3] = $rows['esccargo'] ;
			$table02[$nfil2][4] = $rows['pers_reglab'] ;
			$table02[$nfil2][5] = ($rows['activo']==1)?"ACTIVO":"INACTIVO" ;
			$table02[$nfil2][6] = $rows['pers_fecing'] ;
		}
		$nfil3++;
		$table03[$nfil3][1] = "'" . $rows['pers_dni'] ;
		$table03[$nfil3][2] = utf8_encode($rows['pers_apepat'] . " " . $rows['pers_apemat'] . " " . $rows['pers_nombres']);
		$table03[$nfil3][3] = $rows['esccargo'] ;
		$table03[$nfil3][4] = $rows['pers_reglab'] ;
		$table03[$nfil3][5] = ($rows['activo']==1)?"ACTIVO":"INACTIVO" ;
		$table03[$nfil3][6] = $rows['pers_fecing'] ;
		$table03[$nfil3][7] = "" ;
		$codpers[$nfil3] = $rows['codi_pers'] ;
	}
	for ($fil=1;$fil<=$nfil3;$fil++) {
		$cod=$codpers[$fil];
		$result=$Db->query("select mp_horascompensa_vacaciones.* from mp_horascompensa_vacaciones where vacapersonal='".$cod."' ");
		$busc_tota_item=0;
		foreach($result as $rows) {
			$busc_tota_item++;
		}
		if ($busc_tota_item!=0) {
			$table03[$fil][8] = $result[0]['vaca_expcea'];
			$table03[$fil][9] = $result[0]['vaca_anocea'];
			$table03[$fil][10] = $result[0]['vaca_resolucion'];
			$table03[$fil][11] = $result[0]['vaca_fecemision'];
			$table03[$fil][12] = $result[0]['vaca_periodo'];
			$table03[$fil][13] = $result[0]['vaca_fechaini'];
			$table03[$fil][14] = $result[0]['vaca_fechafin'];
		}
	}
	$worksheet = $spreadsheet->setActiveSheetIndex(0);
	$worksheet->fromArray($table01, null, "A3");
	$worksheet = $spreadsheet->setActiveSheetIndex(1);
	$worksheet->fromArray($table02, null, "A3");
	$worksheet = $spreadsheet->setActiveSheetIndex(2);
	$worksheet->fromArray($table03, null, "A3");


header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheet‌​ml.sheet");
header('Content-Disposition: attachment; filename="' . $filename. '"');

	$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
	$writer->save("php://output");

	exit();
}




?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>-</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script>
			function f_exportar()
			{
				event.preventDefault();
				window.location.href = "compensa_exporta.php";
			}

			function ajustar_altura()
                        {
                                parent.document.getElementById('body_iframe').height=parent.window.innerHeight-80;
                        }
                        ajustar_altura();
		</script>
	</head>
	<body style="margin-bottom: 30px;">
	<center><h2 style="color:#073A6B">EXPORTACION A PLANTILLA EXCEL DE COMPENSACIONES DE HORAS</h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_bien">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;

	echo"<main style='column-count:2;'>";
	echo $html->put_title_demand("SE EXPORTARA LOS DATOS EXISTENTES POR CAPACITACIONES/VACACIONES/SOBRETIEMPOS");
	echo $html->put_button_colum("&nbsp;","Exporta horas compensadas &raquo;","return f_exportar()");
	echo"</main>";

?>
<div id='cargadorvacio'></div>

<center>
	</form>
	</body>
</html>
