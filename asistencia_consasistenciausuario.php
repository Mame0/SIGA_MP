<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;


	if (isset($_GET["dwld"])){
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=".$_GET["nwfile"]);
		$fp = fopen($_GET["rt"].$_GET["dwld"], "rb"); // abre el archivo
		$buffer = fread($fp, filesize($_GET["rt"].$_GET["dwld"])); // escribe el archivo a una variable
		print $buffer; // al "imprimir" se esta enviando el archivo
		fclose($fp); // cierra la lectura
		unlink($_GET["rt"].$_GET["dwld"]);
		exit();
	}

	require dirname(__FILE__) . '/spreadsheets/vendor/autoload.php';
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	use PhpOffice\PhpSpreadsheet\Style\Border;
	use PhpOffice\PhpSpreadsheet\Style\Alignment;
	use PhpOffice\PhpSpreadsheet\Style\Fill;
	use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
	use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
	use PhpOffice\PhpSpreadsheet\Style\Conditional;
	use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
	use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
	use PhpOffice\PhpSpreadsheet\Style\Protection;
	use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
	use PhpOffice\PhpSpreadsheet\NamedRange;

	if(isset($_GET['codi_pers'])) {
		$codpers=$_GET['codi_pers'];
		$fechini=$_GET['fech_ini'];
		$fechfin=$_GET['fech_fin'];

		$nomarhi="asistencia_por_usuario.xlsx";
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename='.$nomarhi);
		header('Cache-Control: max-age=0');

		$objPHPExcel = new Spreadsheet();
		$objPHPExcel->setActiveSheetIndex(0);
		$stylebordes = array(
			'borders' => array(
				'allBorders' => array(
					'borderStyle' => Border::BORDER_THIN,
				),
			),
		);
		$Nomhoja=substr("Bienes_Incautados",0,31);
		$objWorksheet1 = $objPHPExcel->createSheet(0);
		$objWorksheet1->setTitle($Nomhoja);
		$objPHPExcel->setActiveSheetIndex(0);

		$result_pers=$Db->query("SELECT * FROM `mp_personal` where codi_pers=".$codpers." ");
		$dnipers=$result_pers[0]['pers_dni'];
		$nompers=$result_pers[0]['pers_apepat'] . " " . $result_pers[0]['pers_apemat'] . " " . $result_pers[0]['pers_nombres'];

		$result_asis=$Db->query("SELECT * FROM `mp_asistencia` where dni='".$dnipers."' and fecha<='".$fechfin."' and fecha>='".$fechini."' order by fecha");

		$nfil=0;
		foreach($result_asis as $rows) {
			$nfil++;
			$table[$nfil][1] = $nfil;
			$table[$nfil][2] = $rows['fecha'];
			$table[$nfil][3] = substr($rows['horaentrada'],0,5);
			$table[$nfil][4] = substr($rows['horasalida'],0,5);
			$table[$nfil][5] = substr($rows['horamarcaing'],0,5);
			$table[$nfil][6] = substr($rows['horamarcasal'],0,5);


			$table[$nfil][7] = "";
			$table[$nfil][8] = "";
			$table[$nfil][9] = "";
			$table[$nfil][10] = "";
			if ($rows['horasextra']!=0 || $rows['minutosextra']!=0) {
				$table[$nfil][7] = '=TIME("'.$rows['horasextra'].'","'.$rows['minutosextra'].'","0")';
			}
			$porc = explode(":", $rows['horastrabajadas']);
			if (count($porc)!=0 && $rows['horastrabajadas']!="00:00" && $rows['horastrabajadas']!="") {
				$hor = $porc[0] ;
				$min = $porc[1] ;
				$table[$nfil][8] = '=TIME("'.$hor.'","'.$min.'","0")';
			}
			$porc = explode(":", $rows['horasremotas']);
			if (count($porc)!=0 && $rows['horasremotas']!="00:00" && $rows['horasremotas']!="") {
				$hor = $porc[0] ;
				$min = $porc[1] ;
				$table[$nfil][9] = '=TIME("'.$hor.'","'.$min.'","0")';
			}
			$porc = explode(":", $rows['horastotales']);
			if (count($porc)!=0 && $rows['horastotales']!="00:00" && $rows['horastotales']!="") {
				$hor = $porc[0] ;
				$min = $porc[1] ;
				$table[$nfil][10] = '=TIME("'.$hor.'","'.$min.'","0")';
			}

			//$table[$nfil][7] = str_pad($rows['horasextra'], 2, "0", STR_PAD_LEFT)  .  ":" . str_pad($rows['minutosextra'], 2, "0", STR_PAD_LEFT);
			//$table[$nfil][8] = $rows['horastrabajadas'];
			//$table[$nfil][9] = $rows['horasremotas'];
			//$table[$nfil][10] = $rows['horastotales'];
			$table[$nfil][11] = $rows['licencia_descripcion'];
			$table[$nfil][12] = $rows['licencia_resolucion'];
		}
		$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A6', utf8_encode("#") )
				->setCellValue('B6', utf8_encode("FECHA ASISTENCIA") )
				->setCellValue('C6', utf8_encode("HORARIO INGRESO") )
				->setCellValue('D6', utf8_encode("HORARIO SALIDA") )
				->setCellValue('E6', utf8_encode("MARCA INGRESO") )
				->setCellValue('F6', utf8_encode("MARCA SALIDA") )
				->setCellValue('G6', utf8_encode("HORAS EXTRA") )
				->setCellValue('H6', utf8_encode("HORAS PRESENCIAL") )
				->setCellValue('I6', utf8_encode("HORAS REMOTO") )
				->setCellValue('J6', utf8_encode("HORAS TOTALES") )
				->setCellValue('K6', utf8_encode("Licencia Descripción") )
				->setCellValue('L6', utf8_encode("Licencia Resolución") )
				;

		$objPHPExcel->getActiveSheet()->getStyle('A6:L6')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->fromArray($table, null, "A7")
					->getStyle('A6:L' . ($nfil+6))->applyFromArray($stylebordes);

		$objPHPExcel->getActiveSheet()->getStyle('G7:J'.($nfil+6))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME3);


		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('10');
		foreach(range('B','L') as $columnID) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
				->setAutoSize(true);
		}
		//$objPHPExcel->getActiveSheet()->setAutoFilter("A6:O" . ($nfil+6));

		$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1',   utf8_encode("MINISTERIO PÚBLICO")  )
				->setCellValue('A2', "ASISTENCIA DE ". $nompers)
				->setCellValue('A3', "DEL ". $fechini . " AL ". $fechfin);
		$objPHPExcel->getActiveSheet()->getStyle('A2:A5')->getFont()->setBold(true);

		unset($table);

		$objPHPExcel->setActiveSheetIndex(0);
		$writer = new Xlsx($objPHPExcel);
		//$writer->save("temp/".$nomarhi);
		$writer->save("temp/".$nomarhi);

	exit();
	}



?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>CONSULTA DE ASISTENCIA DE PERSONAL</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script>
			function check_buscar()
			{
				document.form.action='';
				document.form.target="";
				document.form.submit();
			}
			function f_exportar()
			{
			event.preventDefault();
					$("#cargadorvacio").load("asistencia_consasistenciausuario.php?expxls=&codi_pers="+document.form.codi_pers.value+"&fech_ini="+document.form.fech_ini.value+"&fech_fin="+document.form.fech_fin.value, function(){
						window.location.href = "asistencia_consasistenciausuario.php?rt=temp/&dwld=asistencia_por_usuario.xlsx&nwfile=asistencia_por_usuario.xlsx";
					});
			}
		</script>

	</head>
	<body style="margin-bottom: 30px;">
	<center><h2 style="color:#073A6B">CONSULTA DE ASISTENCIA DE PERSONAL</h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_bien">
<?
	$html=new htmlclass;
	$condadd="";


	$arra_options_pers[0]="<- Seleccione ->";
	$result=$Db->query("SELECT * FROM `mp_personal` order by pers_apepat, pers_apemat, pers_nombres");
	foreach($result as $rows) {
			$arra_options_pers[$rows['codi_pers']]= utf8_encode( $rows['pers_apepat'] . " " . $rows['pers_apemat'] . " " . $rows['pers_nombres'] ) ;
	}


	echo"<main style='column-count:1;'>";
	echo $html->put_select("Seleccione&nbsp;Personal",'codi_pers',$arra_options_pers,$_POST['codi_pers']," style='width:600px;' ");
	echo"</main>";

	echo"<main style='column-count:3;'>";
	echo $html->put_text('date',"Fecha&nbsp;Inicial","aaaa-mm-dd",'fech_ini',$_POST['fech_ini'],'','10','');
	echo $html->put_text('date',"Fecha&nbsp;Final","aaaa-mm-dd",'fech_fin',$_POST['fech_fin'],'','10','');
	echo $html->put_button_colum("&nbsp;","Consultar &raquo;","return check_buscar()");
	echo"</main>";



if(isset($_POST['codi_pers'])) {  //genera
	$codpers=$_POST['codi_pers'];
	$fechini=$_POST['fech_ini'];
	$fechfin=$_POST['fech_fin'];

	$result_pers=$Db->query("SELECT * FROM `mp_personal` where codi_pers=".$codpers." ");
	$dnipers=$result_pers[0]['pers_dni'];
	$nompers=$result_pers[0]['pers_apepat'] . " " . $result_pers[0]['pers_apemat'] . " " . $result_pers[0]['pers_nombres'];

	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("ASISTENCIAS DE ". $nompers . " DEL ". $fechini . " AL ". $fechfin);

	$head["1"]="#";
	$head["2"]="FECHA&nbsp;ASISTENCIA";
	$head["3"]="HORARIO&nbsp;INGRESO";
	$head["4"]="HORARIO&nbsp;SALIDA";
	$head["5"]="MARCA&nbsp;INGRESO";
	$head["6"]="MARCA&nbsp;SALIDA";
	$head["7"]="HORAS&nbsp;EXTRA";
	$head["8"]="HORAS&nbsp;PRESENCIAL";
	$head["9"]="HORAS&nbsp;REMOTO";
	$head["10"]="HORAS&nbsp;TOTALES";
	$head["11"]="Licencia&nbsp;Descripci&oacute;n";
	$head["12"]="Licencia&nbsp;Resoluci&oacute;n";

	echo $html->put_table_responsive_open();
	echo $html->put_table_responsive_header($head);

	$colgra=0;
	$result_asis=$Db->query("SELECT * FROM `mp_asistencia` where dni='".$dnipers."' and fecha>='".$fechini."' and fecha<='".$fechfin."' order by fecha");
	foreach($result_asis as $rows) {
		$colgra++;
		$anno=$rows['anno_regi'];
		$losanos[$colgra-1]=$anno;
		$colu=$colgra+2;

		$data["1"]=$colgra;
		$data["2"]=$rows['fecha'];
		$data["3"]=substr($rows['horaentrada'],0,5);
		$data["4"]=substr($rows['horasalida'],0,5);
		$data["5"]=substr($rows['horamarcaing'],0,5);
		$data["6"]=substr($rows['horamarcasal'],0,5);
		$data["7"]= str_pad($rows['horasextra'], 2, "0", STR_PAD_LEFT)  .  ":" . str_pad($rows['minutosextra'], 2, "0", STR_PAD_LEFT);
		$data["8"]=$rows['horastrabajadas'];
		$data["9"]=$rows['horasremotas'];
		$data["10"]=$rows['horastotales'];
		$data["11"]=$rows['licencia_descripcion'];
		$data["12"]=$rows['licencia_resolucion'];

		echo $html->put_table_responsive_data($head,$data);

		unset($data);
	}
	echo $html->put_table_responsive_close();
	echo"</div>";

}//genera




		echo $html->put_separator_demand("30");
		echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <!--<div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"document.form.reset()\">Cancelar</button>
                                        </div>-->
                                        <!--<div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"f_nuevo()\">Agregar Nuevo</button>
                                        </div>-->
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"f_exportar()\">Exportar a Excel</button>
                                        </div>
                                </div>
                        </div>
                ";


?>
<div id='cargadorvacio'></div>

<center>
	</form>
	</body>
</html>
