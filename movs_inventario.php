<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();


	if(isset($_GET['codpdf'])) {
		require_once '../librerias/mpdf8/vendor/autoload.php';
		$result=$Db->query("SELECT `mp_bienes_movcabecera`.*, mp_personal.pers_apepat, mp_personal.pers_apemat, mp_personal.pers_nombres, mp_admi_depe.abre_depe
		FROM (`mp_bienes_movcabecera` left join mp_personal on `mp_bienes_movcabecera`.movi_pers=mp_personal.codi_pers)
		left join mp_admi_depe on `mp_bienes_movcabecera`.movi_depe=mp_admi_depe.codi_depe
		where codi_movi='".$_GET['codpdf']."' ");
		$movano=$result[0]['movi_nroxanno'];
		$movfec=$result[0]['movi_fecha'];
		$movusu=$result[0]['pers_apepat']." ".$result[0]['pers_apemat']." ".$result[0]['pers_nombres'];
		$movare=$result[0]['abre_depe'];
		$movref=$result[0]['movi_referencia'];
		$movela=$result[0]['movi_elaboradopor'];
		$movtip=$result[0]['movi_tipo_is'];

		if ($movtip=="I") {
			$mov_deusu=$movusu;
			$mov_deare=$movare;
			$mov_a_usu="AREA DE TECNOLOGIAS DE LA INFORMACION, COMUNICACION Y ESTADISTICA";
			$mov_a_are="GERENCIA ADMINISTRATIVA - INFORMATICA";
		}
		if ($movtip=="S") {
			$mov_deusu="AREA DE TECNOLOGIAS DE LA INFORMACION, COMUNICACION Y ESTADISTICA";
			$mov_deare="GERENCIA ADMINISTRATIVA - INFORMATICA";
			$mov_a_usu=$movusu;
			$mov_a_are=$movare;
		}

		$html = '
		<table align="center" width=100%>
				<tr><td width=33% align="center" style="font-size:5pt;"><b><i><img src="img/logo.jpg" width="60"><br>Ministerio P&uacute;blico<br>Gerencia Administrativa de Arequipa<br>Area de Tecnolog&iacute;as de la Informaci&oacute;n</i></b></td>
				<td width=33% align="center" style="font-size:12pt;" valign="top"><b><i>'. utf8_encode("ANEXO N° 2") .'</i></b></td>
				<td width=33% align="center">
				<table width=80% border=1 align="center" style="border-collapse: collapse; font-size:8pt;">
				<tr><td align="center"><b><i>'. utf8_encode("N°") .'</i></b></td><td align="center"><b><i>DIA</i></b></td><td align="center"><b><i>MES</i></b></td><td align="center"><b><i>A&Ntilde;O</i></b></td></tr>
				<tr><td align="center">'.$movano.'</td><td align="center">'. substr($movfec,8,2) .'</td>
				<td align="center">'. substr($movfec,5,2) .'</td><td align="center">'. substr($movfec,0,4) .'</td></tr>
				</table>

				</td></tr>
		</table><br>

		<table align="center">
				<tr><td align="center" style="font-size:10pt;"><b><i>TRANSFERENCIA INTERNA DE BIENES MUEBLES</i></b></td></tr>
				<tr><td align="center" style="font-size:9pt;"><b><i>EQUIPOS INFORMATICOS</i></b></td></tr>
		</table><br>
		<table width="100%" style="border-collapse: collapse; font-size:8pt;">
				<tr><td width=40><b><i>DE:</i></b></td><td align="center" style="border-bottom: 1px solid #000000;">'.$mov_deusu.'</td></tr>
				<tr><td></td><td align="center" style="border-bottom: 1px solid #000000;">'.$mov_deare.'</td></tr>
				<tr><td><b><i>A:</i></b></td><td align="center" style="border-bottom: 1px solid #000000;">'.$mov_a_usu.'</td></tr>
				<tr><td></td><td align="center" style="border-bottom: 1px solid #000000;">'.$mov_a_are.'</td></tr>
				<tr><td><b><i>REF.:</i></b></td><td align="center" style="border-bottom: 1px solid #000000;">'.$movref.'</td></tr>
		</table><br>
		<table width="100%" border=1 style="border-collapse: collapse; font-size:7pt;">
				<tr><td align="center" width=20><b><i>'. utf8_encode("N°") .'</i></b></td>
				<td align="center" width=80><b><i>CODIGO<br>PATRIMONIAL</i></b></td>
				<td align="center"><b><i>DESCRIPCION</i></b></td>
				<td align="center"><b><i>MARCA</i></b></td>
				<td align="center"><b><i>MODELO</i></b></td>
				<td align="center"><b><i>SERIE</i></b></td>
				<td align="center" width=70><b><i>ESTADO DEL<br>BIEN</i></b></td></tr>';


		$result_pagi=$Db->query("select mp_bienes_movdetalle.*, bien_codpatrimonial,bien_descripcion,bien_marca,bien_modelo,bien_serie
		from mp_bienes_movdetalle inner join mp_bienesinventario on mp_bienes_movdetalle.codi_bien = mp_bienesinventario.codi_bien
		where codi_movi='".$_GET['codpdf']."' ");
		$contador=0;
		foreach($result_pagi as $rows) {
			$estado="";
			$contador++;
			if ( $rows['bien_estado']=="B" ) {$estado="BUENO";}
			if ( $rows['bien_estado']=="R" ) {$estado="REGULAR";}
			if ( $rows['bien_estado']=="M" ) {$estado="MALO";}
			$html .= '<tr><td align="center">'.$contador.'</td><td align="center">'.$rows['bien_codpatrimonial'].'</td>
			<td>'.$rows['bien_descripcion'].'</td>
			<td>'.$rows['bien_marca'].'</td>
			<td>'.$rows['bien_modelo'].'</td>
			<td>'.$rows['bien_serie'].'</td>
			<td align="center">'.$estado.'</td></tr>';
		}
		$html .= '</table>';
		$html .= '
		</table><br><br>
		<table width="100%" border=1 style="border-collapse: collapse; font-size:7pt;">
		<tr><td width=33% align="center"><b><i>'. utf8_encode("V°B°") .'</i></b></td><td width=33% align="center"><b><i>ENTREGUE CONFORME</i></b></td><td width=33% align="center"><b><i>RECIBI CONFORME</i></b></td></tr>
		<tr><td align="center"><br><br><br><br><br><br><br><i>(Firma y sello)</i></td>
		<td align="center"><br><br><br><br><br><br><br><i>(Firma y sello)</i></td>
		<td align="center"><br><br><br><br><br><br><br><i>(Firma y sello)</i></td></tr>
		</table>

		<br><br>
		<table width="80%" align="center" border=1 style="border-collapse: collapse; font-size:6pt;">
		<tr><td align="center" width=100><i>Distribuci&oacute;n:</i></td>
		<td><i>Area de Tecnolog&iacute;as de la Informaci&oacute;n<br>Area de Control Patrimonial y Bienes Incautados<br>Dependencia de entrega el(los) bien(es)<br>Dependencia que recibe el(los) bien(es)</i></td></tr>
		</table>
		<br>
		<table width="100%" border=1 style="border-collapse: collapse; font-size:6pt;">
				<tr><td width=40>
				<table width="100%"><tr>
				<td width=100 align="center"><i>Elaborado por:</i></td>
				<td align="center" style="border-bottom: 1px dotted #000000;"><i>'.$movela.'</i></td>
				</tr>
				<tr><td></td><td align="center"><i>(firma y sello)</i></td></tr>
				</table>
				</td></tr>
		</table><br>

		';

		$html0 = "<pageheader name='myHeaderNoNum' content-left='".$fecsvr."' content-center=''
		content-right='' header-style='font-family:sans-serif; font-size:7pt; color:#000088;'
		header-style-left='font-weight:bold; ' line='off' />
		<setpageheader name='myHeaderNoNum' page='O' value='on' show-this-page='1' />";//{DATE j-m-Y}

		$html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'c',
			'format' => 'A4-P',
			'default_font_size' => 10,
			'default_font' => 'Arial',
			'margin_left' => 30,
			'margin_right' => 20,
			'margin_top' => 10,
			'margin_bottom' => 20,
			'margin_header' => 1,
			'margin_footer' => 1
		]);

		$mpdf->SetDisplayMode('fullpage');
		$mpdf->mirrorMargins = 1;
		$mpdf->keepColumns = true;
		$mpdf->SetColumns(1,'J');
		$mpdf->WriteHTML($html0);
		$mpdf->WriteHTML($html,2);
		//$mpdf->Output();
		$mpdf->Output('temp/actatransfe.pdf');

		exit();
	}



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
			function f_accion_tabla() {
				document.form.codi_pers.value='';
				document.form.action='movs_inventario_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_editar(codi) {
				document.form.codi_pers.value=codi;
				document.form.action='movs_inventario_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_nuevo(tp) {
				document.form.codi_pers.value=tp;
				document.form.action='movs_inventario_registro.php';
				document.form.target="";
				document.form.submit();
			}

			function f_pdf(codmov) {
				var respuesta="";
				$.ajax({
					type: "GET",
					url: "movs_inventario.php",
					cache: false,
					data: { codpdf: codmov }
				}).done(function( respuesta2 ) {
					var html2=respuesta2.trim();
					window.open("temp/actatransfe.pdf", "_blank");
				});
				return false;
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
	<center><h2 style="color:#073A6B">MOVIMIENTOS DE BIENES INVENTARIOS</h2></center>
		<form name="form" method="post" >
			<input type=hidden name="codi_pers">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;

/*
	echo"<main>";
	echo $html->put_title_demand("FORMULARIO DE BUSQUEDA");
	echo $html->put_text('text','B&uacute;squeda&nbsp;por&nbsp;Nombres',"Ingrese Texto a Buscar",'text_busc',$_POST['text_busc'],'','50','');
	echo $html->put_button_colum("&nbsp;","Buscar Personal &raquo;","return check_buscar()");
	echo"</main>";
*/
//if(isset($_POST['text_busc']))
//{
	$busc_item_pagi=40;      //cantidad de items por pagina
	$condadd="";
//	if ($_POST['text_busc']!="") {
//		$condadd=" where (pers_apepat like '%$_POST[text_busc]%' or pers_apemat like '%$_POST[text_busc]%' or pers_nombres like '%$_POST[text_busc]%') ";
//	}

	$result=$Db->query("select mp_bienes_movcabecera.* from mp_bienes_movcabecera ".$condadd." ");
	$busc_tota_item=0;
	foreach($result as $rows) {
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("SELECT `mp_bienes_movcabecera`.*, mp_personal.pers_apepat, mp_personal.pers_apemat, mp_personal.pers_nombres, mp_admi_depe.abre_depe
		FROM (`mp_bienes_movcabecera` left join mp_personal on `mp_bienes_movcabecera`.movi_pers=mp_personal.codi_pers)
		left join mp_admi_depe on `mp_bienes_movcabecera`.movi_depe=mp_admi_depe.codi_depe ".$condadd." order by codi_movi desc limit $busc_limi_pagi,$busc_item_pagi");

	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
//	echo $html->put_title_demand("RESULTADOS DE B&Uacute;SQUEDA: $busc_tota_item ENCONTRADOS");
	echo $html->put_title_demand("TOTAL DE MOVIMIENTOS: $busc_tota_item ENCONTRADOS");
	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"NÂº",'2'=>"MOV-A&Ntilde;O",'3'=>"FECHA",'4'=>"TIPO&nbsp;MOV.",'5'=>"USUARIO",'6'=>"REFERENCIA",'7'=>"ELABORADO&nbsp;POR",'8'=>"PDF"];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows) {
		if ($rows['movi_tipo_is']=="I") {$tpmov="Ingreso";}
		if ($rows['movi_tipo_is']=="S") {$tpmov="Salida";}

			$cont++;
			$data=[	'1'=>$rows['codi_movi'],
				'2'=>$rows['movi_nroxanno']."-". substr($rows['movi_fecha'],0,4),
				'3'=>$rows['movi_fecha'],
				'4'=>$tpmov,
				'5'=>$rows['pers_apepat']." ".$rows['pers_apemat']." ".$rows['pers_nombres'] ."<br>". $rows['abre_depe'] ,
				'6'=>$rows['movi_referencia'],
				'7'=>$rows['movi_elaboradopor'],
				'8'=>"<a href=\"javascript:f_pdf('$rows[codi_movi]')\"><img src=\"img/icons/file.svg\" width=\"20\">",
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
//}


	//if($busc_tota_item>0)
	//{
		echo $html->put_separator_demand("30");
		echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_nuevo(1)\">Nueva Salida</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"f_nuevo(2)\">Nuevo Ingreso</button>
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
