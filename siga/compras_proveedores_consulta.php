<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;


function getNameFromNumber($num) {
    $numeric = $num % 26;
    $letter = chr(65 + $numeric);
    $num2 = intval($num / 26);
    if ($num2 > 0) {
        return getNameFromNumber($num2 - 1) . $letter;
    } else {
        return $letter;
    }
}

	$horalocal = gmdate('Y-m-d H:i:s', time() + (-5 * 3600));//-5 es la zona horaria de perú
	$solofecha = substr($horalocal,0,10);
	$soloano = substr($solofecha,0,4) ;


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

		if ($_GET['nruc_prov']!="") {
			$condadd.=" where (nruc_prov = '".$_GET['nruc_prov']."') ";
		}
		if ($_GET['nomb_prov']!="") {
			if ($condadd!="") {
				$condadd.=" and (nomb_prov like '%".$_GET['nomb_prov']."%' ) ";
			} else {
				$condadd.=" where (nomb_prov like '%".$_GET['nomb_prov']."%' ) ";
			}
		}
		if ($_GET['nomb_come']!="") {
			if ($condadd!="") {
				$condadd.=" and (nomb_come like '%".$_GET['nomb_come']."%' ) ";
			} else {
				$condadd.=" where (nomb_come like '%".$_GET['nomb_come']."%' ) ";
			}
		}
		if ($_GET['deta_acti']!="") {
			if ($condadd!="") {
				$condadd.=" and (deta_acti like '%".$_GET['deta_acti']."%' ) ";
			} else {
				$condadd.=" where (deta_acti like '%".$_GET['deta_acti']."%') ";
			}
		}


		$nomarhi="proveedores.xlsx";
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
		$Nomhoja=substr("Proveedores",0,31);
		$objWorksheet1 = $objPHPExcel->createSheet(0);
		$objWorksheet1->setTitle($Nomhoja);
		$objPHPExcel->setActiveSheetIndex(0);


	$arra_options_rubro[0]="";
        $result=$Db->select('mp_maes_comp_rubro', '', '', '', ['x_nombre'=>'ASC']);
        foreach($result as $rows)
                $arra_options_rubro[$rows['n_codigo']]=$rows['x_nombre'];


	$arra_options_tpru[0]="";
	$arra_options_tpru[1]="BIENES";
	$arra_options_tpru[2]="SERVICIOS";
	$arra_options_tpru[3]="BIENES Y SERVICIOS";


		$result_pagi=$Db->query("select mp_comp_proveedores.* from mp_comp_proveedores
		".$condadd." order by nomb_prov asc ");
		$nfil=0;
		$nomcmp[1][1]="";
		foreach($result_pagi as $rows) {
			$nfil++;
			$table[$nfil][1] = $nfil;
			$table[$nfil][2] = utf8_encode($rows['nruc_prov']);
			$table[$nfil][3] = utf8_encode($rows['nomb_prov']);
			$table[$nfil][4] = utf8_encode($rows['nomb_come']);
			$table[$nfil][5] = utf8_encode($rows['dire_prov']);
			$table[$nfil][6] = utf8_encode($rows['mail_prov']);
			$table[$nfil][7] = utf8_encode($rows['fono_prov']);
			$table[$nfil][8] = utf8_encode($rows['repr_legal']);
			$table[$nfil][9] = utf8_encode($rows['cont_prov']);
			$table[$nfil][10] = (($rows['rnp_prov']==1)?"SI":"NO") ;
			$table[$nfil][11] = (($rows['mype_prov']==1)?"SI":"NO") ;
			$table[$nfil][12] = $arra_options_tpru[$rows['tipo_rubr']];
			$table[$nfil][13] = $arra_options_rubro[$rows['codi_rubr']];
			$table[$nfil][14] = utf8_encode($rows['deta_acti']);

			$nrocol=5;
			for ($dep=0;$dep<=$cantcampos-1;$dep++) {
				$nrocol++;
				$nrocmp=$cods[$dep];
				$table[$nfil][$nrocol]=utf8_encode( $rows[  $campo[$nrocmp][1]  ] ) ;

				$nomcmp[1][$dep+1]= $campo[$nrocmp][2] ;
			}
		}
		$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A6', utf8_encode("#") )
				->setCellValue('B6', utf8_encode("RUC") )
				->setCellValue('C6', utf8_encode("RAZÓN SOCIAL") )
				->setCellValue('D6', utf8_encode("NOMBRE COMERCIAL") )
				->setCellValue('E6', utf8_encode("DIRECCIÓN") )
				->setCellValue('F6', utf8_encode("CORREO ELECTRÓNICO") )
				->setCellValue('G6', utf8_encode("TELÉFONO") )
				->setCellValue('H6', utf8_encode("REPRESENTANTE LEGAL") )
				->setCellValue('I6', utf8_encode("CONTACTO") )
				->setCellValue('J6', utf8_encode("RNP") )
				->setCellValue('K6', utf8_encode("MYPE") )
				->setCellValue('L6', utf8_encode("TP RUBRO") )
				->setCellValue('M6', utf8_encode("ACT. COMERCIAL") )
				->setCellValue('N6', utf8_encode("ACT. COMERCIAL DETALLE") ) ;

		$objPHPExcel->getActiveSheet()->getStyle('A6:N6')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->fromArray($table, null, "A7")
					->getStyle('A6:N' . ($nfil+6))->applyFromArray($stylebordes);

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('10');
		foreach(range('B','M') as $columnID) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
				->setAutoSize(true);
		}
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth('60');

		$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1',   utf8_encode("MINISTERIO PÚBLICO")  )
				->setCellValue('A2', "CONSULTA DE PROVEEDORES")
				->setCellValue('A3', "Fecha y Hora: ". $horalocal);

		$objPHPExcel->getActiveSheet()->getStyle('A1:A5')->getFont()->setBold(true);

		unset($table);

		$objPHPExcel->setActiveSheetIndex(0);
		$writer = new Xlsx($objPHPExcel);
		$writer->save("temp/".$nomarhi);

	exit();
	}



?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Consulta</title>
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
			function f_exportar() {
				event.preventDefault();
				setTimeout(function(){
					var nruc=document.form.nruc_prov.value;
					var nomp=document.form.nomb_prov.value;
					var noco=document.form.nomb_come.value;
					var deta=document.form.deta_acti.value;
					$("#cargadorvacio").load("compras_proveedores_consulta.php?expxls=&nruc_prov="+nruc+"&nomb_prov="+nomp+"&nomb_come="+noco+"&deta_acti="+deta, function(){
						window.location.href = "compras_proveedores_consulta.php?rt=temp/&dwld=proveedores.xlsx&nwfile=proveedores.xlsx"; //ocultarespera();
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
	<center><h2 style="color:#073A6B">CONSULTA DE PROVEEDORES</h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_pers">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;



echo"<main style='column-count:2;'>";
echo $html->put_title_demand("BUSCAR PROVEEDORES (Ingrese los campos para filtrar y crear consulta)");
	echo $html->put_text('text',"RUC","Nro.&nbsp;de&nbsp;RUC",'nruc_prov',$_POST['nruc_prov'],'','11','');
	echo $html->put_text('text',"Raz&oacute;n&nbsp;Social&nbsp;(parte&nbsp;del&nbsp;nombre)","Raz&oacute;n social",'nomb_prov',$_POST['nomb_prov'],'','50','');
echo"</main>";
echo"<main style='column-count:2;'>";
	echo $html->put_text('text',"Nombre&nbsp;Comercial&nbsp;(parte&nbsp;del&nbsp;nombre)","Nombre Comercial",'nomb_come',$_POST['nomb_come'],'','50','');
	echo $html->put_text('text',"Actividad&nbsp;comercial&nbsp;(parte&nbsp;del&nbsp;detalle)","Ingrese parte del detalle",'deta_acti',$_POST['deta_acti'],'','50','');
echo"</main>";

echo"<main>";
	echo $html->put_button_colum("","Mostrar Consulta &raquo;","return check_buscar()");
echo"</main>";

if(isset($_POST['nruc_prov']))
{
	$busc_item_pagi=40;      //cantidad de items por pagina
	$condadd="";

	if ($_POST['nruc_prov']!="") {
		$condadd.=" where (nruc_prov = '".$_POST['nruc_prov']."') ";
	}
	if ($_POST['nomb_prov']!="") {
		if ($condadd!="") {
			$condadd.=" and (nomb_prov like '%".$_POST['nomb_prov']."%' ) ";
		} else {
			$condadd.=" where (nomb_prov like '%".$_POST['nomb_prov']."%' ) ";
		}
	}
	if ($_POST['nomb_come']!="") {
		if ($condadd!="") {
			$condadd.=" and (nomb_come like '%".$_POST['nomb_come']."%' ) ";
		} else {
			$condadd.=" where (nomb_come like '%".$_POST['nomb_come']."%' ) ";
		}
	}
	if ($_POST['deta_acti']!="") {
		if ($condadd!="") {
			$condadd.=" and (deta_acti like '%".$_POST['deta_acti']."%' ) ";
		} else {
			$condadd.=" where (deta_acti like '%".$_POST['deta_acti']."%') ";
		}
	}

	$result=$Db->query("select * from mp_comp_proveedores ".$condadd." ");
	$busc_tota_item=0;
	foreach($result as $rows) {
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$arra_options_rubro[0]="";
        $result=$Db->select('mp_maes_comp_rubro', '', '', '', ['x_nombre'=>'ASC']);
        foreach($result as $rows)
                $arra_options_rubro[$rows['n_codigo']]=$rows['x_nombre'];


	$arra_options_tpru[0]="";
	$arra_options_tpru[1]="BIENES";
	$arra_options_tpru[2]="SERVICIOS";
	$arra_options_tpru[3]="BIENES Y SERVICIOS";

	$result_pagi=$Db->query("select mp_comp_proveedores.* from mp_comp_proveedores
	".$condadd." order by nomb_prov asc limit $busc_limi_pagi,$busc_item_pagi");

	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("RESULTADOS DE B&Uacute;SQUEDA: $busc_tota_item ENCONTRADOS");
	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");

	$head['1']="#";
	$head['2']="RUC";
	$head['3']="RAZON SOCIAL";
	$head['4']="NOMBRE COMERCIAL";
	$head['5']="DIRECCION";
	$head['6']="CORREO ELECTRONICO";
	$head['7']="TELEFONO";
	$head['8']="REPRESENTANTE LEGAL";
	$head['9']="CONTACTO";
	$head['10']="RNP";
	$head['11']="MYPE";
	$head['12']="TP RUBRO";
	$head['13']="ACT. COMERCIAL";
	$head['14']="ACT. COMERCIAL DETALLE";


	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows) {
			$cont++;
			$data['1']=$cont;
			$data['2']=utf8_encode($rows['nruc_prov']);
			$data['3']=utf8_encode($rows['nomb_prov']);
			$data['4']=utf8_encode($rows['nomb_come']);
			$data['5']=utf8_encode($rows['dire_prov']);
			$data['6']=utf8_encode($rows['mail_prov']);
			$data['7']=utf8_encode($rows['fono_prov']);
			$data['8']=utf8_encode($rows['repr_legal']);
			$data['9']=utf8_encode($rows['cont_prov']);
			$data['10']= (($rows['rnp_prov']==1)?"SI":"NO") ;
			$data['11']= (($rows['mype_prov']==1)?"SI":"NO") ;
			$data['12']=$arra_options_tpru[$rows['tipo_rubr']];
			$data['13']=$arra_options_rubro[$rows['codi_rubr']];
			$data['14']=utf8_encode($rows['deta_acti']);
			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("No existen datos registrados");
	echo $html->put_table_responsive_close();
	if($busc_tota_pagi>0)
		echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	echo"</div>";


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
		echo "<input type='hidden' id='cds' name='cds' value='".$cmpexp."' >";
}

?>
<div id='cargadorvacio'></div>

<center>
	</form>
	</body>
</html>
