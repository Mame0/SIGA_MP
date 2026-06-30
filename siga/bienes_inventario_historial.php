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

	if(isset($_GET['expxls'])) {
		$condadd="";
		$descbien="";
		if ($_GET['text_busc']!="") {
			$codbien=-1;
			$result=$Db->query("select mp_bienesinventario.* from mp_bienesinventario where bien_codpatrimonial='".$_GET['text_busc']."' ");
			foreach($result as $rows) {
				$codbien=$rows['codi_bien'];
				$descbien=$rows['bien_descripcion'];
			}
			$condadd=" where (mp_bienes_movdetalle.codi_bien = '$codbien' ) ";
		}

		$nomarhi="historial_bienpatrimonial.xlsx";
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

		$result_pagi=$Db->query("select mp_bienes_movdetalle.bien_estado, mp_bienes_movdetalle.movi_tipo_is,
		movi_nroxanno, movi_fecha, mp_personal.pers_apepat, mp_personal.pers_apemat, mp_personal.pers_nombres, mp_admi_depe.abre_depe, movi_referencia, movi_elaboradopor
		from ((mp_bienes_movcabecera inner join mp_bienes_movdetalle on mp_bienes_movcabecera.codi_movi = mp_bienes_movdetalle.codi_movi)
		left join mp_personal on `mp_bienes_movcabecera`.movi_pers=mp_personal.codi_pers)
		left join mp_admi_depe on `mp_bienes_movcabecera`.movi_depe=mp_admi_depe.codi_depe
		".$condadd." order by movi_fecha, mp_bienes_movcabecera.codi_movi");

		$nfil=0;
		foreach($result_pagi as $rows) {
			if ($rows['movi_tipo_is']=="I") {$tpmov="Ingreso";}
			if ($rows['movi_tipo_is']=="S") {$tpmov="Salida";}

			$estad="";
			if ($rows['bien_estado']=="B") {$estad="Bueno";}
			if ($rows['bien_estado']=="R") {$estad="Regular";}
			if ($rows['bien_estado']=="M") {$estad="Malo";}

			$nfil++;
			$table[$nfil][1] = $nfil;
			$table[$nfil][2] = $rows['movi_nroxanno']."-". substr($rows['movi_fecha'],0,4);
			$table[$nfil][3] = $rows['movi_fecha'];
			$table[$nfil][4] = $tpmov;
			$table[$nfil][5] = $rows['pers_apepat']." ".$rows['pers_apemat']." ".$rows['pers_nombres'];
			$table[$nfil][6] = $rows['abre_depe'];
			$table[$nfil][7] = $rows['movi_referencia'];
			$table[$nfil][8] = $rows['movi_elaboradopor'];
			$table[$nfil][9] = $estad;
		}
		$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A6', utf8_encode("#") )
				->setCellValue('B6', utf8_encode("MOV-AÑO") )
				->setCellValue('C6', utf8_encode("FECHA") )
				->setCellValue('D6', "TIPO MOV.")
				->setCellValue('E6', "USUARIO" )
				->setCellValue('F6', "AREA/DEPENDENCIA" )
				->setCellValue('G6', "REFERENCIA" )
				->setCellValue('H6', utf8_encode("ELABORADO POR") )
				->setCellValue('I6', "ESTADO" ) ;

		$objPHPExcel->getActiveSheet()->getStyle('A6:I6')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->fromArray($table, null, "A7")
					->getStyle('A6:I' . ($nfil+6))->applyFromArray($stylebordes);

		//$objPHPExcel->getActiveSheet()->getStyle('E6:E6')->getAlignment()->setWrapText(true);


		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('10');
		foreach(range('B','I') as $columnID) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
				->setAutoSize(true);
		}
		//$objPHPExcel->getActiveSheet()->setAutoFilter("A6:O" . ($nfil+6));

		$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1',   utf8_encode("MINISTERIO PÚBLICO")  )
				->setCellValue('A2', "HISTORIAL DE MOVIMIENTOS - UBICACION DE BIENES")
				->setCellValue('A3', utf8_encode("CÓDIGO PATRIMONIAL: ". $_GET['text_busc']) )
				->setCellValue('A4', utf8_encode("DESCRIPCIÓN: ". $descbien) );
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

		<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

		<script>
			function check_buscar() {
				document.form.action='';
				document.form.target="";
				document.form.submit();
			}
			function f_ver(codi) {
				document.form.action='ftp/'+codi;
				document.form.target="blank";
				document.form.submit();
			}

			function f_exportar() {
				event.preventDefault();
				setTimeout(function(){
					$("#cargadorvacio").load("bienes_inventario_historial.php?expxls=&text_busc="+document.form.text_busc.value, function(){
						window.location.href = "bienes_inventario_historial.php?rt=temp/&dwld=historial_bienpatrimonial.xlsx&nwfile=historial_bienpatrimonial_"+ document.form.text_busc.value +".xlsx"; //ocultarespera();
					});
				}, 200);
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
	<center><h2 style="color:#073A6B">HISTORIAL DE MOVIMIENTOS - UBICACION DE BIENES</h2></center>
		<form name="form" method="post" >
			<input type=hidden name="codi_pers">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;


	echo"<main>";
	echo $html->put_title_demand("HISTORIAL DE MOVIMIENTOS - UBICACION DE BIENES");
	echo $html->put_text('text','C&oacute;digo&nbsp;Patrimonial',"",'text_busc',$_POST['text_busc'],'','10','');
	echo $html->put_button_colum("&nbsp;","Buscar Movimientos &raquo;","return check_buscar()");
	echo"</main>";

if(isset($_POST['text_busc'])) {
	$busc_item_pagi=40;      //cantidad de items por pagina
	$condadd="";
	$descbien="";
	if ($_POST['text_busc']!="") {
		$codbien=-1;
		$result=$Db->query("select mp_bienesinventario.* from mp_bienesinventario where bien_codpatrimonial='".$_POST['text_busc']."' ");
		foreach($result as $rows) {
			$codbien=$rows['codi_bien'];
			$descbien=$rows['bien_descripcion'];
		}
		$condadd=" where (mp_bienes_movdetalle.codi_bien = '$codbien' ) ";
	}

	$result=$Db->query("select mp_bienes_movdetalle.bien_estado, mp_bienes_movdetalle.movi_tipo_is,
	movi_nroxanno, movi_fecha, movi_usuariofila1, movi_usuariofila2, movi_referencia, movi_elaboradopor
	from mp_bienes_movcabecera inner join mp_bienes_movdetalle on mp_bienes_movcabecera.codi_movi = mp_bienes_movdetalle.codi_movi ".$condadd." order by movi_fecha");

	$busc_tota_item=0;
	foreach($result as $rows) {
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("select mp_bienes_movdetalle.bien_estado, mp_bienes_movdetalle.movi_tipo_is,
	movi_nroxanno, movi_fecha, mp_personal.pers_apepat, mp_personal.pers_apemat, mp_personal.pers_nombres, mp_admi_depe.abre_depe, movi_referencia, movi_elaboradopor
	from ((mp_bienes_movcabecera inner join mp_bienes_movdetalle on mp_bienes_movcabecera.codi_movi = mp_bienes_movdetalle.codi_movi)
	left join mp_personal on `mp_bienes_movcabecera`.movi_pers=mp_personal.codi_pers)
	left join mp_admi_depe on `mp_bienes_movcabecera`.movi_depe=mp_admi_depe.codi_depe
	".$condadd." order by movi_fecha, mp_bienes_movcabecera.codi_movi
	limit $busc_limi_pagi,$busc_item_pagi");

	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand($descbien . " : $busc_tota_item MOVIMIENTOS");
	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"#",'2'=>"MOV-A&Ntilde;O",'3'=>"FECHA",'4'=>"TIPO&nbsp;MOV.",'5'=>"USUARIO",'6'=>"AREA",'7'=>"REFERENCIA",'8'=>"ELABORADO&nbsp;POR",'9'=>"ESTADO"];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows) {
			if ($rows['movi_tipo_is']=="I") {$tpmov="Ingreso";}
			if ($rows['movi_tipo_is']=="S") {$tpmov="Salida";}

			$estad="";
			if ($rows['bien_estado']=="B") {$estad="Bueno";}
			if ($rows['bien_estado']=="R") {$estad="Regular";}
			if ($rows['bien_estado']=="M") {$estad="Malo";}

			$cont++;
			$data=[	'1'=>$cont,
				'2'=>$rows['movi_nroxanno']."-". substr($rows['movi_fecha'],0,4),
				'3'=>$rows['movi_fecha'],
				'4'=>$tpmov,
				'5'=>$rows['pers_apepat']." ".$rows['pers_apemat']." ".$rows['pers_nombres'] ,
				'6'=>$rows['abre_depe'] ,
				'7'=>$rows['movi_referencia'],
				'8'=>$rows['movi_elaboradopor'],
				'9'=>$estad,
			];
			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("No existen movimientos registrados");
	echo $html->put_table_responsive_close();
	if($busc_tota_pagi>0)
		echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	echo"</div>";
}


	if($busc_tota_item>0)
	{
		echo $html->put_separator_demand("30");
		echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"f_exportar()\">Exportar a Excel</button>
                                        </div>
                                </div>
                        </div>
                ";
	}
?>
<div id='cargadorvacio'></div>

<center>
	</form>
	</body>
</html>
