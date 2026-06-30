<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>ESTADISTICA DE REGISTROS SIN DISPOSICION VS ESTADO CASO POR DEPENDENCIA</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>


    <!-- Page level plugins -->
    <script src="chart.js/Chart.min.js"></script>
<!--
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
-->

		<script>
			function check_buscar()
			{
				document.form.action='';
				document.form.target="";
				document.form.submit();
			}
			function ajustar_altura()
                        {
                                parent.document.getElementById('body_iframe').height=parent.window.innerHeight-80;
                        }
                        ajustar_altura();
		</script>

	</head>
	<body style="margin-bottom: 30px;">
	<center><h2 style="color:#073A6B">ESTADISTICA DE REGISTROS SIN DISPOSICION VS ESTADO CASO POR DEPENDENCIA</h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_bien">
<?
	$html=new htmlclass;
	$condadd="";

	//SIN DISPOSICION EN CARPETA FISCAL
	$arra_options_anno[0]="<- Seleccione ->";
	$result=$Db->query("SELECT distinct anno_regi FROM `mp_cpbi_bienes` where codi_disp=0 order by anno_regi ");
	foreach($result as $rows) {
			$arra_options_anno[$rows['anno_regi']]= $rows['anno_regi'] ;
	}


	$arra_options_depe[0]="<- Seleccione ->";
	$cantdep=0;
	$result_depe=$Db->query("SELECT * FROM mp_admi_depe where depe_prin=1 ");
	foreach($result_depe as $rows_dp) {
		$coddep=$rows_dp['codi_depe'];

		$condadd="";
		$respad=$Db->query("SELECT * FROM mp_admi_depe where codi_padr=".$coddep." ");
		foreach($respad as $rows_pa) {
			$condadd.=" or codi_depe=".$rows_pa['codi_depe']." ";
		}
		$result_depe=$Db->query("SELECT codi_epro, count( * ) AS cant FROM `mp_cpbi_bienes` where (codi_depe=".$coddep." ".$condadd.") and codi_disp=0 ");
		$cant=$result_depe[0]['cant'];
		if ($cant!=0) {
			$cantdep++;
			$lasdep[$cantdep][1]=$rows_dp['codi_depe'];
			$lasdep[$cantdep][2]=$rows_dp['nomb_depe'];

			$arra_options_depe[$rows_dp['codi_depe']]= $rows_dp['nomb_depe'] ;
		}
	}






	echo"<main style='column-count:1;'>";
	echo $html->put_select("Dependencia&nbsp;a&nbsp;generar&nbsp;estad&iacute;stica",'codi_depe',$arra_options_depe,$_POST['codi_depe']," style='max-width:800px;' ");
	echo"</main>";
	echo"<main style='column-count:3;'>";
	echo $html->put_select("A&ntilde;o&nbsp;Inicio",'anno_ini',$arra_options_anno,$_POST['anno_ini']," style='width:150px;' ");
	//echo "Desde el A&ntilde;o: <select id='anno_ini' name='anno_ini' style='width:100px;'>$optionsini</select>";
	echo $html->put_select("A&ntilde;o&nbsp;Fin",'anno_fin',$arra_options_anno,$_POST['anno_fin']," style='width:150px;' ");
	//echo "Hasta el A&ntilde;o: <select id='anno_fin' name='anno_fin' style='width:100px;'>$optionsfin</select>";
	echo $html->put_button_colum("&nbsp;","Generar Gr&aacute;fico &raquo;","return check_buscar()");
	echo"</main>";



if(isset($_POST['codi_depe'])) {  //genera grafico
	$anoini=$_POST['anno_ini'];
	$anofin=$_POST['anno_fin'];


	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("ESTADISTICA DE REGISTROS SIN DISPOSICION vs ESTADO CASO");

	$head["1"]="Dependencia";
	$head["2"]="ARCHIVO";
	$head["3"]="SENTENCIA";
	$head["4"]="Otros";

	echo $html->put_table_responsive_open();
	echo $html->put_table_responsive_header($head);


		$coddep=$_POST['codi_depe'];
		$respad=$Db->query("SELECT * FROM mp_admi_depe where codi_depe=".$coddep." ");
		foreach($respad as $rows_pa) {
			$desdep=$rows_pa['nomb_depe'];
		}

		$condadd="";
		if ($coddep==0) {
			$data['1']="<b>DEPENDENCIA NO ASIGNADA</b>";
		} else {
			$data['1']=utf8_encode($desdep);
			$respad=$Db->query("SELECT * FROM mp_admi_depe where codi_padr=".$coddep." ");
			foreach($respad as $rows_pa) {
				$condadd.=" or codi_depe=".$rows_pa['codi_depe']." ";
			}
		}

		$result_esta=$Db->query("SELECT codi_epro, count( * ) AS cant FROM `mp_cpbi_bienes` where (codi_depe=".$coddep." ".$condadd.") and codi_disp=0 and (anno_regi>='".$anoini."' and anno_regi<='".$anofin."') and codi_epro=1 ");
		$cant1=$result_esta[0]['cant'];
		$data[2]=$cant1;

		$result_esta=$Db->query("SELECT codi_epro, count( * ) AS cant FROM `mp_cpbi_bienes` where (codi_depe=".$coddep." ".$condadd.") and codi_disp=0 and (anno_regi>='".$anoini."' and anno_regi<='".$anofin."') and codi_epro=8 ");
		$cant2=$result_esta[0]['cant'];
		$data[3]=$cant2;

		$result_esta=$Db->query("SELECT codi_epro, count( * ) AS cant FROM `mp_cpbi_bienes` where (codi_depe=".$coddep." ".$condadd.") and codi_disp=0 and (anno_regi>='".$anoini."' and anno_regi<='".$anofin."') and (codi_epro<>1 and codi_epro<>8) ");
		$cant3=$result_esta[0]['cant'];
		$data[4]=$cant3;

		echo $html->put_table_responsive_data($head,$data);

	echo $html->put_table_responsive_close();

	$chart="grafico";


$colores[1]="#2E2EFE";//"#A9A9F5";
$colores[2]="#FA5858";//"#F5A9A9";
$colores[3]="#58FA58";//"#A9F5A9";
$colores[4]="#F5D0A9";
$colores[5]="#A9E2F3";
$colores[6]="#F7FE2E";
?>
                    <!-- Content Row -->
<!--
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">PENSIONES POR ESTADO</h6>
                                </div>
                                <div class="card-body">
-->
                                    <div class="chart-pie pt-4">
                                        <canvas id="<? echo $chart; ?>" height=500></canvas>
                                    </div>
<!--
                                </div>
                            </div>
                    	</div>
                    </div>
-->

<script>

var arraydep=<?php echo json_encode($losdep);?>;

// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Arial';//'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

// Pie Chart Example
var ctx = document.getElementById("<? echo $chart; ?>");
var myPieChart = new Chart(ctx, {
  type: 'pie',
  data: {
	labels: ['ARCHIVO', 'SENTENCIA', 'OTROS'],
	datasets: [
		{
			label: 'Dataset 1',
			data: [<? echo $cant1; ?>,<? echo $cant2; ?>,<? echo $cant3; ?>],
			backgroundColor: ["<? echo $colores[1]; ?>","<? echo $colores[2]; ?>","<? echo $colores[3]; ?>"],
		}
	],
  },
  options: {
      title: {
        display: true,
        text: ['<? echo utf8_encode($desdep); ?>','<? echo utf8_encode("Registros Sin Disposición vs Estado Caso (".$anoini." al ".$anofin.")"); ?>'],
      },
    maintainAspectRatio: false,
    tooltips: {
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      caretPadding: 10,
    },
    legend: {
      display: true
    },
    cutoutPercentage: 0,
  },

});
</script>

<?
	echo"</div>";

}//genera grafico

/*
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
                                </div>
                        </div>
                ";
*/
?>
<center>
	</form>
	</body>
</html>
