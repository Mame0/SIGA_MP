<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;



	$horalocal = gmdate('Y-m-d H:i:s', time() + (-5 * 3600));//-5 es la zona horaria de perú
	$solofecha = substr($horalocal,0,10);




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
			$condadd=" where bien_descripcion like '%$_GET[text_busc]%' ";
		}

		$nomarhi="bienes_inventario.xlsx";
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
		$Nomhoja=substr("Bienes_Inventario",0,31);
		$objWorksheet1 = $objPHPExcel->createSheet(0);
		$objWorksheet1->setTitle($Nomhoja);
		$objPHPExcel->setActiveSheetIndex(0);

		$result_pagi=$Db->query("select mp_bienesinventario.*, mp_bienestecnologias.tecno_descripcion
		from mp_bienesinventario left join mp_bienestecnologias on mp_bienesinventario.bien_tecnologia = mp_bienestecnologias.tecno_id
		".$condadd." order by bien_descripcion asc ");
		$nfil=0;
		foreach($result_pagi as $rows) {
			$nfil++;
			$table[$nfil][1] = $rows['bien_codpatrimonial'];
			$table[$nfil][2] = $rows['bien_correlativo'];
			$table[$nfil][3] = utf8_encode($rows['bien_descripcion']);
			$table[$nfil][4] = utf8_encode($rows['bien_marca']);
			$table[$nfil][5] = utf8_encode($rows['bien_modelo']);
			$table[$nfil][6] = utf8_encode($rows['bien_serie']);
			$table[$nfil][7] = utf8_encode($rows['tecno_descripcion']);

			$table[$nfil][8] = (($rows['bien_cantidad']==1)?"NO":"SI");
			$table[$nfil][9] = (($rows['activo']==1)?"SI":"NO");//estado
		}
		$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A6', utf8_encode("COD PATRIMONIAL") )
				->setCellValue('B6', utf8_encode("CORRELATIVO") )
				->setCellValue('C6', utf8_encode("DESCRIPCION") )
				->setCellValue('D6', utf8_encode("MARCA") )
				->setCellValue('E6', utf8_encode("MODELO") )
				->setCellValue('F6', utf8_encode("SERIE") )
				->setCellValue('G6', utf8_encode("GP.TECNOL.") )
				->setCellValue('H6', utf8_encode("DISP.ALMACEN") )
				->setCellValue('I6', utf8_encode("OPERATIVO") ) ;


		$objPHPExcel->getActiveSheet()->getStyle('A6:I6')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->fromArray($table, null, "A7")
					->getStyle('A6:I' . ($nfil+6))->applyFromArray($stylebordes);

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('15');
		foreach(range('B','I') as $columnID) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
				->setAutoSize(true);
		}

		$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1',   utf8_encode("MINISTERIO PÚBLICO")  )
				->setCellValue('A2', "BIENES INVENTARIO");
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
		<title>Bienes Inventario</title>
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
			function f_editar(codi)
			{
				document.form.codi_bien.value=codi;
				document.form.action='bienes_inventario_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_nuevo()
			{
				document.form.codi_bien.value='';
				document.form.action='bienes_inventario_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_exportar() {
			event.preventDefault();
				//setTimeout(function(){
					$("#cargadorvacio").load("bienes_inventario.php?expxls=&text_busc="+document.form.text_busc.value, function(){
						window.location.href = "bienes_inventario.php?rt=temp/&dwld=bienes_inventario.xlsx&nwfile=bienes_inventario.xlsx"; //ocultarespera();
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
	<center><h2 style="color:#073A6B">BIENES INVENTARIO</h2></center>
		<form name="form" method="post" autocomplete="off">
			<input type=hidden name="codi_bien">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;

	echo"<main>";
	echo $html->put_title_demand("FORMULARIO DE BUSQUEDA");
	echo $html->put_text('text','Descripci&oacute;n&nbsp;del&nbsp;Bien',"Ingrese Texto a Buscar",'text_busc',$_POST['text_busc'],'','50','');
	echo $html->put_button_colum("&nbsp;","Buscar Bienes &raquo;","return check_buscar()");
	echo"</main>";

if(isset($_POST['text_busc']))
{
	$busc_item_pagi=40;      //cantidad de items por pagina
	$condadd="";
	if ($_POST['text_busc']!="") {
		$condadd=" where bien_descripcion like '%$_POST[text_busc]%' ";
	}

	$result=$Db->query("select * from mp_bienesinventario ".$condadd." ");

	$busc_tota_item=0;
	foreach($result as $rows) {
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("select mp_bienesinventario.*, mp_bienestecnologias.tecno_descripcion
	from mp_bienesinventario left join mp_bienestecnologias on mp_bienesinventario.bien_tecnologia = mp_bienestecnologias.tecno_id
	".$condadd." order by bien_descripcion asc limit $busc_limi_pagi,$busc_item_pagi");

	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("RESULTADOS DE B&Uacute;SQUEDA: $busc_tota_item ENCONTRADOS");
	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"C&oacute;d&nbsp;Patrimonial",'2'=>"Correlativo",'3'=>"Descripci&oacute;n",'4'=>"Marca",'5'=>"Modelo",'6'=>"Serie",'7'=>"Gp.Tecnol.",'8'=>"Disponible",'9'=>"Operativo",'10'=>"EDITAR"];

	echo $html->put_table_responsive_open();
	if($busc_tota_item) {
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows) {
			$data=['1'=>utf8_encode($rows['bien_codpatrimonial']),
				'2'=>utf8_encode($rows['bien_correlativo']),
				'3'=>utf8_encode($rows['bien_descripcion']),
				'4'=>utf8_encode($rows['bien_marca']),
				'5'=>utf8_encode($rows['bien_modelo']),
				'6'=>utf8_encode($rows['bien_serie']),
				'7'=>utf8_encode($rows['tecno_descripcion']),
				'8'=> (($rows['bien_cantidad']==1)?"SI":"NO") ,
				'9'=> (($rows['activo']==1)?"SI":"NO") ,
				'10'=>"<a href=\"javascript:f_editar('$rows[codi_bien]')\"><img src=\"img/icons/edit.svg\" width=\"20\">",
			];


			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("No existen bienes registrados");
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
                                                <button class=\"button_foot\" onclick=\"f_nuevo()\">Agregar Nuevo</button>
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
