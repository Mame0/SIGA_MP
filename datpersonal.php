<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;



/*
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
		if ($_GET['text_busc']!="") {
			$condadd=" where desc_bien like '%$_GET[text_busc]%' ";
		}
		if ($_GET['codi_deli']!=0) {
			if ($condadd!="") {$condadd.=" and ";} else {$condadd.=" where ";}
			$condadd.=" mp_cpbi_bienes.codi_deli=" . $_GET['codi_deli'] . " ";
		}

		$nomarhi="archivos_incautados.xlsx";
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

		$result_pagi=$Db->query("select mp_cpbi_bienes.*, mp_maes_delito.x_nombre as desc_delito, mp_maes_cpbi_ubicacion.x_nombre as desc_ubica,
		mp_maes_cpbi_estado.x_nombre as desc_esta, nomb_depe, mp_maes_cpbi_estado_proceso.x_nombre as esta_proceso, mp_maes_personal.appa_pers, mp_maes_personal.apma_pers, mp_maes_personal.nomb_pers
		from (((((mp_cpbi_bienes left join mp_maes_cpbi_ubicacion on mp_cpbi_bienes.codi_ubic=mp_maes_cpbi_ubicacion.n_codigo)
		left join mp_maes_cpbi_estado on mp_cpbi_bienes.codi_esta=mp_maes_cpbi_estado.n_codigo)
		left join mp_maes_delito on mp_cpbi_bienes.codi_deli=mp_maes_delito.n_codigo)
		left join mp_admi_depe on mp_cpbi_bienes.codi_depe=mp_admi_depe.codi_depe)
		left join mp_maes_personal on mp_cpbi_bienes.codi_fisc=mp_maes_personal.iden_pers)
		left join mp_maes_cpbi_estado_proceso on mp_cpbi_bienes.codi_epro=mp_maes_cpbi_estado_proceso.n_codigo
		".$condadd." order by desc_bien asc limit $busc_limi_pagi,$busc_item_pagi");
		$nfil=0;
		foreach($result_pagi as $rows) {
			$nfil++;
			$table[$nfil][1] = $rows['nume_regi'];
			$table[$nfil][2] = utf8_encode($rows['desc_bien']);
			$table[$nfil][3] = $rows['nume_carp'];
			$table[$nfil][4] = utf8_encode($rows['desc_delito']);
			$table[$nfil][5] = $rows['nomb_depe'];
			$table[$nfil][6] = utf8_encode($rows['appa_pers']." ".$rows['apma_pers']." ".$rows['nomb_pers']);
			$table[$nfil][7] = $rows['fech_inte'];
			$table[$nfil][8] = $rows['desc_ubica'];
			$table[$nfil][9] = $rows['desc_esta'];
			$table[$nfil][10] = $rows['esta_proceso'];

		}
		$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A6', utf8_encode("N° REGISTRO") )
				->setCellValue('B6', utf8_encode("DESCRIPCIÓN") )
				->setCellValue('C6', utf8_encode("CARPETA") )
				->setCellValue('D6', "DELITO")
				->setCellValue('E6', "DEPENDENCIA" )
				->setCellValue('F6', "FISCAL" )
				->setCellValue('G6', "FECHA INT." )
				->setCellValue('H6', utf8_encode("UBICACIÓN") )
				->setCellValue('I6', "ESTADO" )
				->setCellValue('J6', "PROCESO" ) ;

		$objPHPExcel->getActiveSheet()->getStyle('A6:J6')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->fromArray($table, null, "A7")
					->getStyle('A6:J' . ($nfil+6))->applyFromArray($stylebordes);

		$objPHPExcel->getActiveSheet()->getStyle('E6:E6')->getAlignment()->setWrapText(true);


		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('20');
		foreach(range('B','J') as $columnID) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
				->setAutoSize(true);
		}
		//$objPHPExcel->getActiveSheet()->setAutoFilter("A6:O" . ($nfil+6));

		$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1',   utf8_encode("MINISTERIO PÚBLICO")  )
				->setCellValue('A2', "BIENES INCAUTADOS");
		$objPHPExcel->getActiveSheet()->getStyle('A2:A5')->getFont()->setBold(true);

		unset($table);

		$objPHPExcel->setActiveSheetIndex(0);
		$writer = new Xlsx($objPHPExcel);
		//$writer->save("temp/".$nomarhi);
		$writer->save("temp/".$nomarhi);


	exit();
	}
*/



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
				document.form.action='datpersonal_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_editar(codi)
			{
				document.form.codi_pers.value=codi;
				document.form.action='datpersonal_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_nuevo()
			{
				document.form.codi_pers.value='';
				document.form.action='datpersonal_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_exportar()
			{
//				setTimeout(function(){
//					$("#cargadorvacio").load("cpbi_bienes_incautados.php?expxls=&text_busc="+document.form.text_busc.value+"&codi_deli="+document.form.codi_deli.value, function(){
//						window.location.href = "cpbi_bienes_incautados.php?rt=&dwld=archivos_incautados.xlsx&nwfile=archivos_incautados.xlsx"; //ocultarespera();
//					});
//				}, 200);
			}
			function PadLeft(value, length)
			{
				return (value.toString().length < length) ? PadLeft("0" + value, length) :
				value;
			}
			function ajustar_altura()
                        {
//                                parent.document.getElementById('body_iframe').height=parent.window.innerHeight-80;
                        }
                        ajustar_altura();
		</script>
	</head>
	<body style="margin-bottom: 30px;">
	<center><h2 style="color:#073A6B">DATOS PERSONALES</h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_pers">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;

	echo"<main>";
	echo $html->put_title_demand("FORMULARIO DE BUSQUEDA");

	echo $html->put_text('text','B&uacute;squeda&nbsp;por&nbsp;Nombres',"Ingrese Texto a Buscar",'text_busc',$_POST['text_busc'],'','50','');
	echo $html->put_button_colum("&nbsp;","Buscar Personal &raquo;","return check_buscar()");
	echo"</main>";

if(isset($_POST['text_busc']))
{
	$busc_item_pagi=40;      //cantidad de items por pagina
	$condadd="";
	if ($_POST['text_busc']!="") {
		$condadd=" where (pers_apepat like '%$_POST[text_busc]%' or pers_apemat like '%$_POST[text_busc]%' or pers_nombres like '%$_POST[text_busc]%') ";
	}

	$result=$Db->query("select * from mp_personal ".$condadd." ");

	$busc_tota_item=0;
	foreach($result as $rows)
	{
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("select mp_personal.*
	from mp_personal
	".$condadd." order by pers_apepat asc, pers_apemat asc limit $busc_limi_pagi,$busc_item_pagi");

	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("RESULTADOS DE B&Uacute;SQUEDA: $busc_tota_item ENCONTRADOS");
	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"NÂº",'2'=>"APELLIDO PATERNO",'3'=>"APELLIDO MATERNO",'4'=>"NOMBRES",'5'=>"FEC.NACIMIENTO",'6'=>"EST.CIVIL",'7'=>"DNI",'8'=>"EDITAR"];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
		if ($rows['pers_estciv']=="S") {$estciv="Soltero(a)";}
		if ($rows['pers_estciv']=="C") {$estciv="Casado(a)";}
		if ($rows['pers_estciv']=="V") {$estciv="Viudo(a)";}
		if ($rows['pers_estciv']=="D") {$estciv="Divorsiado(a)";}


			$cont++;
			$data=[	'1'=>$cont,
				'2'=>$rows['pers_apepat'],
				'3'=>$rows['pers_apemat'],
				'4'=>$rows['pers_nombres'],
				'5'=>$rows['pers_fecnac'],
				'6'=>$estciv,
				'7'=>$rows['pers_dni'],
				'8'=>"<a href=\"javascript:f_editar('$rows[codi_pers]')\"><img src=\"img/icons/edit.svg\" width=\"20\">",
			];
			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("No existen datos registrados");
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
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"document.form.reset()\">Cancelar</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"f_nuevo()\">Agregar Nuevo</button>
                                        </div>
<!--                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"f_exportar()\">Exportar a Excel</button>
                                        </div>-->
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
