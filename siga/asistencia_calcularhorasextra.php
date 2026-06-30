<?
//classes/TCPDF/examples/personal_fotocheck.php
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

	if(isset($_GET['expxls'])) {
		$condadd="";
		if ($_GET['fecini']!="") {
			$condadd=" where fecha <= '$_GET[fecfin]' and fecha >= '$_GET[fecini]' ";
		}

		$nomarhi="horasextra.xlsx";
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
		$Nomhoja=substr("Horas_Extra_Trabajadas",0,31);
		$objWorksheet1 = $objPHPExcel->createSheet(0);
		$objWorksheet1->setTitle($Nomhoja);
		$objPHPExcel->setActiveSheetIndex(0);

		$result_pagi=$Db->query("select mp_asistencia.dni, mp_personal.pers_apepat, mp_personal.pers_apemat, mp_personal.pers_nombres, sum(horasextra) as horext, sum(minutosextra) as minext
		from mp_asistencia left join mp_personal on mp_asistencia.dni=mp_personal.pers_dni
		".$condadd." group by dni order by mp_personal.pers_apepat, mp_personal.pers_apemat, mp_personal.pers_nombres ");

		$nfil=0;
		foreach($result_pagi as $rows) {
			$horext=$rows['horext'];
			$minext=$rows['minext'];
			if ($minext>=60) {
				$horadd=intval($minext/60);
				$nuemin=$minext-($horadd*60);
				$horext=$horext+$horadd;
				$minext=$nuemin;
			}

			$nfil++;
			$table[$nfil][1] = $nfil;
			$table[$nfil][2] = $rows['dni'];
			$table[$nfil][3] = utf8_encode($rows['pers_apepat'] . " " . $rows['pers_apemat'] . " " . $rows['pers_nombres']);
			$table[$nfil][4] = $horext;
			$table[$nfil][5] = $minext;
		}
		$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A6', utf8_encode("#") )
				->setCellValue('B6', "DNI" )
				->setCellValue('C6', utf8_encode("APELLIDOS Y NOMBRES") )
				->setCellValue('D6', "HORAS EXTRA")
				->setCellValue('E6', "MINUTOS EXTRA");

		$objPHPExcel->getActiveSheet()->getStyle('A6:E6')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->fromArray($table, null, "A7")
					->getStyle('A6:E' . ($nfil+6))->applyFromArray($stylebordes);

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('10');
		foreach(range('B','E') as $columnID) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
				->setAutoSize(true);
		}
		//$objPHPExcel->getActiveSheet()->setAutoFilter("A6:O" . ($nfil+6));

		$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1',   utf8_encode("MINISTERIO PÚBLICO")  )
				->setCellValue('A2', "HORAS EXTRA ACUMULADAS")
				->setCellValue('A3', "DEL " . $_GET['fecini'] . " AL " . $_GET['fecfin']);
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
		<title>SIOJAlimentos</title>
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
			function f_ver(codi)
			{
				document.form.action='ftp/'+codi;
				document.form.target="blank";
				document.form.submit();
			}
			function f_accion_tabla()
			{
				document.form.codi_pers.value='';
				document.form.action='jurisprudencia_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_editar(codi)
			{
				document.form.codi_bien.value=codi;
				document.form.action='cpbi_bienes_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_nuevo()
			{
				document.form.codi_bien.value='';
				document.form.action='cpbi_bienes_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_exportar() {
			event.preventDefault();
				//setTimeout(function(){
					$("#cargadorvacio").load("asistencia_calcularhorasextra.php?expxls=&fecini="+document.form.fecini.value+"&fecfin="+document.form.fecfin.value, function(){
						window.location.href = "asistencia_calcularhorasextra.php?rt=temp/&dwld=horasextra.xlsx&nwfile=horasextra.xlsx"; //ocultarespera();
					});
				//}, 200);
			}
			function PadLeft(value, length)
			{
				return (value.toString().length < length) ? PadLeft("0" + value, length) :
				value;
			}
			function ajustar_altura()
                        {
                                parent.document.getElementById('body_iframe').height=parent.window.innerHeight-80;
                        }
                        ajustar_altura();
		</script>
	</head>
	<body style="margin-bottom: 30px;">
	<center><h2 style="color:#073A6B">CONSULTA DE HORAS EXTRA ACUMULADAS POR INTERVALO DE FECHA</h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_bien">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;

	$arra_options_pers[0]="<- Todos ->";
        $result=$Db->select('mp_personal', '', '', '', ['pers_apepat'=>'ASC', 'pers_apemat'=>'ASC', 'pers_nombres'=>'ASC']);
        foreach($result as $rows)
                $arra_options_pers[$rows['codi_pers']]= utf8_encode( $rows['pers_apepat']." ".$rows['pers_apemat']." ".$rows['pers_nombres'] );

$busc_tipo=1;	//1 nombre - 2 ndoc - 3 esca - 4 marc
$arra_options_tipo=array(1=>"Por Apellidos y Nombres","DNI","Escalafon","C&oacute;digo de Marcado");

	echo"<main style='column-count:2;'>";
	echo $html->put_title_demand("FORMULARIO DE BUSQUEDA");
	echo $html->put_text('date','Fecha&nbsp;Inicial',"aaaa-mm-dd",'fecini',$_POST['fecini'],'','10','');
	echo $html->put_text('date','Fecha&nbsp;Final',"aaaa-mm-dd",'fecfin',$_POST['fecfin'],'','10','');
	echo"</main>";

	echo"<main style='column-count:2;'>";
	echo $html->put_select("Nombre&nbsp;Servidor&nbsp;(Personal)",'codi_pers',$arra_options_pers,$_POST['codi_pers'],'');
	echo $html->put_button_colum("&nbsp;","Mostrar Acumulados &raquo;","return check_buscar()");
	echo"</main>";

if(isset($_POST['fecini'])) {
	$busc_item_pagi=40;      //cantidad de items por pagina
	$condadd="";
	if ($_POST['fecini']!="") {
		$condadd=" where fecha <= '$_POST[fecfin]' and fecha >= '$_POST[fecini]' ";
	}
	if ($_POST['codi_pers']!="0") {
		$result_pagi=$Db->query("select mp_personal.pers_dni from mp_personal where codi_pers='".$_POST['codi_pers']."' ");
		$dni=$result_pagi[0]['pers_dni'];
		$condadd.=" and mp_asistencia.dni= '".$dni."' ";
	}

	$result=$Db->query("select dni, count(*) as cant from mp_asistencia ".$condadd." group by dni");
	$busc_tota_item=0;
	foreach($result as $rows)
	{
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("select mp_asistencia.dni, mp_personal.pers_apepat, mp_personal.pers_apemat, mp_personal.pers_nombres, sum(horasextra) as horext, sum(minutosextra) as minext
	from mp_asistencia left join mp_personal on mp_asistencia.dni=mp_personal.pers_dni
	".$condadd." group by dni order by mp_personal.pers_apepat, mp_personal.pers_apemat, mp_personal.pers_nombres limit $busc_limi_pagi,$busc_item_pagi");

	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	//echo $html->put_title_demand("RESULTADOS DE B&Uacute;SQUEDA: $busc_tota_item ENCONTRADOS");
	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"#",'2'=>"DNI",'3'=>"APELLIDOS Y NOMBRES",'4'=>"HORAS EXTRA",'5'=>"MINUTOS EXTRA"];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows) {
			$horext=$rows['horext'];
			$minext=$rows['minext'];
			if ($minext>=60) {
				$horadd=intval($minext/60);
				$nuemin=$minext-($horadd*60);
				$horext=$horext+$horadd;
				$minext=$nuemin;
			}

			$cont++;
			$data=[	'1'=>$cont, '2'=>$rows['dni'],
				'3'=>utf8_encode($rows['pers_apepat'] . " " . $rows['pers_apemat'] . " " . $rows['pers_nombres'] ),
				'4'=>$horext ,
				'5'=>$minext ,
			];
			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("No existen información en el intervalo de fechas");
	echo $html->put_table_responsive_close();
	if($busc_tota_pagi>0)
		echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	echo"</div>";
}
	//if($busc_tota_item>0)
	//{
		echo $html->put_separator_demand("30");
		echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <!--<div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"document.form.reset()\">Cancelar</button>
                                        </div>-->
                                        <div class=\"div_button_foot\"><center>
                                                <!--<button class=\"button_foot\" onclick=\"f_nuevo()\">Agregar Nuevo</button>-->
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"f_exportar()\">Exportar a Excel</button>
                                        </div>
                                </div>
                        </div>
                ";
	//}
?>
<div id='cargadorvacio'></div>

<center>
	</form>
	</body>
</html>
