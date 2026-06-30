<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;




$campo[1][1]="pers_fecnac";
$campo[1][2]="Fec.Nacimiento";
$campo[2][1]="pers_estciv";
$campo[2][2]="Est.Civil";
$campo[3][1]="pers_dni";
$campo[3][2]="DNI";
$campo[4][1]="pers_lugarnac";
$campo[4][2]="Lugar Nacimiento";
$campo[5][1]="pers_dire";
$campo[5][2]=utf8_encode("Dirección");
$campo[6][1]="pers_distr";
$campo[6][2]="Distrito";
$campo[7][1]="pers_refedir";
$campo[7][2]="Referencia Domicilio";
$campo[8][1]="pers_tlffijo";
$campo[8][2]="Tlfn.Fijo";
$campo[9][1]="pers_celu";
$campo[9][2]="Celular";
$campo[10][1]="pers_emailper";
$campo[10][2]="EMail Personal";
$campo[11][1]="pers_emailinst";
$campo[11][2]="EMail Institucional";
//persona 1, persona 2

$campo[12][1]="pers_grains";
$campo[12][2]=utf8_encode("Grado instrucción");
$campo[13][1]="pers_prof1";
$campo[13][2]=utf8_encode("Profesión 1");
$campo[14][1]="pers_prof2";
$campo[14][2]=utf8_encode("Profesión 2");

$campo[15][1]="pers_nrocole";
$campo[15][2]="Nro Colegiatura";
$campo[16][1]="pers_fecing";
$campo[16][2]="Fec.Ingreso";
$campo[17][1]="esccargo";
$campo[17][2]="Cargo";
$campo[18][1]="nomb_depe";
$campo[18][2]="Dependencia Actual";
$campo[19][1]="pers_reglab";
$campo[19][2]="Regimen Laboral";
$campo[20][1]="pers_plapres";
$campo[20][2]="Plaza Pesupuesto";
$campo[21][1]="pers_conyuge";
$campo[21][2]="Conyugue";

$campo[22][1]="pers_hijo1";
$campo[22][2]="Hijo 1";
$campo[23][1]="pers_fechijo1";
$campo[23][2]="Fec.Nac. 1";
$campo[24][1]="pers_sexohijo1";
$campo[24][2]="Sexo 1";

$campo[25][1]="pers_hijo2";
$campo[25][2]="Hijo 2";
$campo[26][1]="pers_fechijo2";
$campo[26][2]="Fec.Nac. 2";
$campo[27][1]="pers_sexohijo2";
$campo[27][2]="Sexo 2";

$campo[28][1]="pers_hijo3";
$campo[28][2]="Hijo 3";
$campo[29][1]="pers_fechijo3";
$campo[29][2]="Fec.Nac. 3";
$campo[30][1]="pers_sexohijo3";
$campo[30][2]="Sexo 3";

$campo[31][1]="pers_hijo4";
$campo[31][2]="Hijo 4";
$campo[32][1]="pers_fechijo4";
$campo[32][2]="Fec.Nac. 4";
$campo[33][1]="pers_sexohijo4";
$campo[33][2]="Sexo 4";

$campo[34][1]="pers_hijo5";
$campo[34][2]="Hijo 5";
$campo[35][1]="pers_fechijo5";
$campo[35][2]="Fec.Nac. 5";
$campo[36][1]="pers_sexohijo5";
$campo[36][2]="Sexo 5";

$campo[37][1]="pers_padre";
$campo[37][2]="Padre";
$campo[38][1]="pers_padredir";
$campo[38][2]=utf8_encode("Dirección");

$campo[39][1]="pers_madre";
$campo[39][2]="Madre";
$campo[40][1]="pers_madredir";
$campo[40][2]=utf8_encode("Dirección");

$campo[41][1]="pers_essalud";
$campo[41][2]="ESSALUD";
$campo[42][1]="pers_centroate";
$campo[42][2]=utf8_encode("Centro de atención o policlínico");
$campo[43][1]="pers_eps";
$campo[43][2]="EPS";
$campo[44][1]="pers_tpsangre";
$campo[44][2]="Tp.Sangre";
$campo[45][1]="pers_alergenf";
$campo[45][2]="Alergias/Enfermedades";
$campo[46][1]="pers_discap";
$campo[46][2]="Discapacidad";
$campo[47][1]="pers_conadis";
$campo[47][2]="CONADIS";
$campo[48][1]="pers_otroidi";
$campo[48][2]="Otro idioma";
$campo[49][1]="pers_hobfut";
$campo[49][2]="Hobbie Futbol";
$campo[50][1]="pers_hobbas";
$campo[50][2]="Hobbie basket";
$campo[51][1]="pers_hobnat";
$campo[51][2]=utf8_encode("Hobbie natación");
$campo[52][1]="pers_hobpin";
$campo[52][2]="Hobbie PingPong";
$campo[53][1]="pers_hobfro";
$campo[53][2]="Hobbie Fronton";
$campo[54][1]="pers_hobbai";
$campo[54][2]="Hobbie baile";
$campo[55][1]="pers_hobcoc";
$campo[55][2]="Hobbie cocina";
$campo[56][1]="pers_otrahab";
$campo[56][2]="Otra habilidad";


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
		if ($_GET['coddep']!=0) {
			$condadd=" where pers_depe = '".$_GET["coddep"]."' ";
		}
		if ($_GET['esciv']!="") {
			if ($condadd!="") {$condadd.=" and ";} else {$condadd.=" where ";}
			$condadd.=" pers_estciv='" . $_GET['esciv'] . "' ";
		}
		$cods = explode("|", $_GET['cods']);
		$cantcampos=count($cods);




		$nomarhi="personal_registrado.xlsx";
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

		$result_pagi=$Db->query("select mp_personal.*, mp_plan_escalaremunerativa.esccargo, mp_admi_depe.nomb_depe
		from (mp_personal left join mp_admi_depe on mp_personal.pers_depe=mp_admi_depe.codi_depe)
		left join mp_plan_escalaremunerativa on mp_personal.pers_cargo=mp_plan_escalaremunerativa.n_codigo
		".$condadd." order by pers_apepat asc, pers_apemat asc ");
		$nfil=0;
		$nomcmp[1][1]="";
		foreach($result_pagi as $rows) {
			$nfil++;
			$table[$nfil][1] = $nfil;
			$table[$nfil][2] = utf8_encode($rows['pers_apepat']);
			$table[$nfil][3] = utf8_encode($rows['pers_apemat']);
			$table[$nfil][4] = utf8_encode($rows['pers_nombres']);
			$table[$nfil][5] = utf8_encode( ($rows['activo']==1)?"Sí":"No"  );
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
				->setCellValue('B6', utf8_encode("APELLIDO PATERNO") )
				->setCellValue('C6', utf8_encode("APELLIDO MATERNO") )
				->setCellValue('D6', "NOMBRES")
				->setCellValue('E6', "ACTIVO" );

		if ($nomcmp!="") {
			$objPHPExcel->getActiveSheet()->fromArray($nomcmp, null, "F6");
		}
		$ultcolu=getNameFromNumber($nrocol-1);


		$objPHPExcel->getActiveSheet()->getStyle('A6:'.$ultcolu.'6')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->fromArray($table, null, "A7")
					->getStyle('A6:' . $ultcolu . ($nfil+6))->applyFromArray($stylebordes);

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('10');
		foreach(range('B','Z') as $columnID) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
				->setAutoSize(true);
		}
		if ($nrocol>26) {
			foreach(range('A','Z') as $columnID) {
				$objPHPExcel->getActiveSheet()->getColumnDimension("A".$columnID)
					->setAutoSize(true);
			}
		}

		$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1',   utf8_encode("MINISTERIO PÚBLICO")  )
				->setCellValue('A2', "PERSONAL REGISTRADO");
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
			function f_ver(codi)
			{
				document.form.action='ftp/'+codi;
				document.form.target="blank";
				document.form.submit();
			}
			function f_accion_tabla()
			{
				document.form.codi_pers.value='';
				document.form.action='datpersonal_consulta.php';
				document.form.target="";
				document.form.submit();
			}
			function f_editar(codi)
			{
				document.form.codi_pers.value=codi;
				document.form.action='datpersonal_consulta.php';
				document.form.target="";
				document.form.submit();
			}
			function f_nuevo()
			{
				document.form.codi_pers.value='';
				document.form.action='datpersonal_consulta.php';
				document.form.target="";
				document.form.submit();
			}
			function f_exportar() {
				event.preventDefault();
				setTimeout(function(){
					var cddep=document.form.pers_depe.value;
					var esciv=document.form.pers_estciv.value;
					var cods=document.form.cds.value;
					$("#cargadorvacio").load("datpersonal_consulta.php?expxls=&coddep="+cddep+"&estciv="+esciv+"&cods="+cods, function(){
						window.location.href = "datpersonal_consulta.php?rt=temp/&dwld=personal_registrado.xlsx&nwfile=personal_registrado.xlsx"; //ocultarespera();
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
	<center><h2 style="color:#073A6B">CONSULTA DE DATOS PERSONALES</h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_pers">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;



echo"<main style='column-count:1; height:210px;'>";
echo $html->put_title_demand("DATOS A MOSTRAR");
echo '<div class="row" style="height:150px; overflow: scroll;">';
for ($dep=1;$dep<=56;$dep++) {
	$add="";
	if (isset($_POST['chk'.$dep])) {
		$add=" checked='checked' ";
	}
	echo '<div style="float: left; width: 250px;">';
    echo '<div class="checkbox" style="font-size: 12px;">
      <label style="padding:5px 5px 5px 5px;"><input type="checkbox" id="chk'.$dep.'" name="chk'.$dep.'" value="x"  '.$add.'> '.$campo[$dep][2].'</label>
    </div>';
    echo '</div>';
}
echo '</div>';
echo"</main>";




	$arra_options_depe[0]="<- Seleccione ->";
	$cantdep=0;
	$result_depe=$Db->query("SELECT * FROM mp_admi_depe where depe_prin=1 ");
	foreach($result_depe as $rows_dp) {
		$coddep=$rows_dp['codi_depe'];
//		$condadd="";
//		$respad=$Db->query("SELECT * FROM mp_admi_depe where codi_padr=".$coddep." ");
//		foreach($respad as $rows_pa) {
//			$condadd.=" or codi_depe=".$rows_pa['codi_depe']." ";
//		}
//		$result_depe=$Db->query("SELECT codi_epro, count( * ) AS cant FROM `mp_cpbi_bienes` where (codi_depe=".$coddep." ".$condadd.") and codi_disp=0 ");
//		$cant=$result_depe[0]['cant'];
//		if ($cant!=0) {
			$cantdep++;
			$lasdep[$cantdep][1]=$rows_dp['codi_depe'];
			$lasdep[$cantdep][2]=$rows_dp['nomb_depe'];
			$arra_options_depe[$rows_dp['codi_depe']]= $rows_dp['nomb_depe'] ;
//		}
	}

	echo"<main style='column-count:1;'>";
	echo $html->put_select("Dependencia",'pers_depe',$arra_options_depe,$_POST['pers_depe']," style='max-width:800px;' ");
	echo"</main>";


	echo"<main style='column-count:2;'>";

	$arra_options_esta[""]= "Todos";
	$arra_options_esta["S"]= "Soltero(a)";
	$arra_options_esta["C"]= "Casado(a)";
	$arra_options_esta["V"]= "Viudo(a)";
	$arra_options_esta["D"]= "Divorciado(a)";
	echo $html->put_select("Est.Civil",'pers_estciv',$arra_options_esta,$_POST['pers_estciv'],"");

	echo $html->put_button_colum("&nbsp;","Mostrar Consulta &raquo;","return check_buscar()");
	echo"</main>";

if(isset($_POST['pers_depe']))
{
	$busc_item_pagi=40;      //cantidad de items por pagina
	$condadd="";
	if ($_POST['pers_depe']!=0) {
		$condadd.=" where (pers_depe = ".$_POST[pers_depe].") ";
	}
	if ($_POST['pers_estciv']!="") {
		if ($condadd!="") {
			$condadd.=" and (pers_estciv = '".$_POST[pers_estciv]."') ";
		} else {
			$condadd.=" where (pers_estciv = '".$_POST[pers_estciv]."') ";
		}
	}

	$result=$Db->query("select * from mp_personal ".$condadd." ");

	$busc_tota_item=0;
	foreach($result as $rows)
	{
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("select mp_personal.*, mp_plan_escalaremunerativa.esccargo, mp_admi_depe.nomb_depe
	from (mp_personal left join mp_admi_depe on mp_personal.pers_depe=mp_admi_depe.codi_depe)
	left join mp_plan_escalaremunerativa on mp_personal.pers_cargo=mp_plan_escalaremunerativa.n_codigo
	".$condadd." order by pers_apepat asc, pers_apemat asc limit $busc_limi_pagi,$busc_item_pagi");

	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("RESULTADOS DE B&Uacute;SQUEDA: $busc_tota_item ENCONTRADOS");
	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");

	$head['1']="NÂº";
	$head['2']="APELLIDO PATERNO";
	$head['3']="APELLIDO MATERNO";
	$head['4']="NOMBRES";
	$head['5']="ACTIVO";

$nrocol=5;
$cmpexp="";
for ($dep=1;$dep<=56;$dep++) {
	if (isset($_POST['chk'.$dep])) {
		$nrocol++;
		$head["'.$nrocol.'"]=$campo[$dep][2];
		if ($cmpexp!="") {$cmpexp.="|";}
		$cmpexp.=$dep;
	}
}




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
			$data['1']=$cont;
			$data['2']=utf8_encode($rows['pers_apepat']);
			$data['3']=utf8_encode($rows['pers_apemat']);
			$data['4']=utf8_encode($rows['pers_nombres']);
			$data['5']=($rows['activo']==1)?"S&iacute;":"No";

			$nrocol=5;
			for ($dep=1;$dep<=56;$dep++) {
				if (isset($_POST['chk'.$dep])) {
					$nrocol++;
					$data["'.$nrocol.'"]=utf8_encode( $rows[  $campo[$dep][1]  ] ) ;
				}
			}

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
