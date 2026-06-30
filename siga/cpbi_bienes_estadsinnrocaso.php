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
		<title>ESTADISTICA DE BIENES INCAUTADOS CON/SIN NRO CASO FISCAL</title>
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
	</head>
	<body style="margin-bottom: 30px;">
	<center><h2 style="color:#073A6B">ESTADISTICA DE BIENES INCAUTADOS CON/SIN NRO CASO FISCAL</h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_bien">
<?
	$html=new htmlclass;
	$condadd="";



	//SIN DISPOSICION EN CARPETA FISCAL
	$arra_options_anno[0]="<- Seleccione ->";
	$result=$Db->query("SELECT distinct anno_regi FROM `mp_cpbi_bienes` order by anno_regi ");
	foreach($result as $rows) {
			$arra_options_anno[$rows['anno_regi']]= $rows['anno_regi'] ;
	}

	$arra_options_depe[0]="<- TODAS LAS FISCALIAS ->";
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


	$arra_options_distfiscal[0]="<- Todos ->";
	$result=$Db->query("SELECT * FROM `mp_maes_distritofiscal` order by descripcion ");
	foreach($result as $rows) {
			$arra_options_distfiscal[$rows['n_codigo']]= $rows['descripcion'] ;
	}

	$arra_options_fisc[0]="<- Todos ->";
	$result=$Db->select('mp_maes_personal', '', '', '', ['appa_pers'=>'ASC', 'apma_pers'=>'ASC', 'nomb_pers'=>'ASC']);
	foreach($result as $rows)
			$arra_options_fisc[$rows['iden_pers']]= $rows['appa_pers']." ".$rows['apma_pers']." ".$rows['nomb_pers'] ;


	echo"<main style='column-count:1;'>";
	echo $html->put_select("Dependencia&nbsp;a&nbsp;generar&nbsp;estad&iacute;stica",'codi_depe',$arra_options_depe,$_POST['codi_depe']," style='max-width:800px;' ");
	echo"</main>";

	echo"<main style='column-count:2;'>";
	echo $html->put_select("Distrito&nbsp;Fiscal",'n_codigo',$arra_options_distfiscal,$_POST['n_codigo']," style='max-width:600px;' ");
	echo $html->put_select("Fiscal",'iden_pers',$arra_options_fisc,$_POST['iden_pers'],'style="max-width:600px;"');
	echo"</main>";

	echo"<main style='column-count:3;'>";
	echo $html->put_select("A&ntilde;o&nbsp;Inicio",'anno_ini',$arra_options_anno,$_POST['anno_ini']," style='width:150px;' ");
	//echo "Desde el A&ntilde;o: <select id='anno_ini' name='anno_ini' style='width:100px;'>$optionsini</select>";
	echo $html->put_select("A&ntilde;o&nbsp;Fin",'anno_fin',$arra_options_anno,$_POST['anno_fin']," style='width:150px;' ");
	//echo "Hasta el A&ntilde;o: <select id='anno_fin' name='anno_fin' style='width:100px;'>$optionsfin</select>";
	echo $html->put_button_colum("&nbsp;","Generar Gr&aacute;fico &raquo;","return check_buscar()");
	echo"</main>";



if(isset($_POST['anno_ini'])) {  //genera grafico
	$anoini=$_POST['anno_ini'];
	$anofin=$_POST['anno_fin'];

	$distfisc=$_POST['n_codigo'];
	$codfisca=$_POST['iden_pers'];

		$coddep=$_POST['codi_depe'];
		$desdep="TODAS LAS FISCALIAS";
		$respad=$Db->query("SELECT * FROM mp_admi_depe where codi_depe=".$coddep." ");
		foreach($respad as $rows_pa) {
			$desdep=$rows_pa['nomb_depe'];
		}

		$condadd="";
		if ($coddep!=0) {
			$condadd.=" and codi_depe=".$coddep." ";
		}

		if ($distfisc!=0) {
			$condadd.=" and codi_distfiscal=".$distfisc." ";
		}
		if ($codfisca!=0) {
			$condadd.=" and codi_fisc=".$codfisca." ";
		}


	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("ESTADISTICA CON/SIN NRO CASO FISCAL : ". $desdep);

	$head["1"]="Nº";
	$head["2"]="TP.REGISTRO";

	$colgra=0;
//	$result_anno=$Db->query("SELECT distinct anno_regi FROM `mp_cpbi_bienes` order by anno_regi");
	$result_anno=$Db->query("SELECT distinct anno_regi FROM `mp_cpbi_bienes` where anno_regi<='".$anofin."' and anno_regi>='".$anoini."' ".$condadd." order by anno_regi");
	foreach($result_anno as $rows_aa) {
		$colgra++;
		$anno=$rows_aa['anno_regi'];
		$losanos[$colgra-1]=$anno;
		$colu=$colgra+2;
		$head["$colu"]=$anno;
	}

	echo $html->put_table_responsive_open();
	echo $html->put_table_responsive_header($head);
//	for ($ubi=1;$ubi<=$cantubi;$ubi++) {
//		$codubi=$lasubi[$ubi][1];
//		$desubi=$lasubi[$ubi][2];
		$data['1']=1;//$ubi;
		$data['2']="S/CASO";//(($desubi=="")?"NO ASIGNADO":   utf8_encode($desubi));

		for ($ano=1;$ano<=$colgra;$ano++) {
			$anno=$losanos[$ano-1];
			$result_esta=$Db->query("SELECT count( * ) AS cant FROM `mp_cpbi_bienes` where anno_regi='".$anno."' ".$condadd." and nume_carp='' ");//and codi_ubic='".$codubi."'
			$busc_tota_item=0;
			$xxa=$ano+2;
			$cant=$result_esta[0]['cant'];
			$data["$xxa"]=$cant;
			$lascant[1][$ano]=$cant;
		}
		echo $html->put_table_responsive_data($head,$data);

		$data['1']=2;//$ubi;
		$data['2']="C/CASO";//(($desubi=="")?"NO ASIGNADO":   utf8_encode($desubi));

		for ($ano=1;$ano<=$colgra;$ano++) {
			$anno=$losanos[$ano-1];
			$result_esta=$Db->query("SELECT count( * ) AS cant FROM `mp_cpbi_bienes` where anno_regi='".$anno."' ".$condadd." and nume_carp<>'' ");//and codi_ubic='".$codubi."'
			$busc_tota_item=0;
			$xxa=$ano+2;
			$cant=$result_esta[0]['cant'];
			$data["$xxa"]=$cant;
			$lascant[2][$ano]=$cant;
		}
		echo $html->put_table_responsive_data($head,$data);



//	}
	echo $html->put_table_responsive_close();

	$chart="grafico";


$colores[1]="#A9A9F5";
$colores[2]="#F5A9A9";
$colores[3]="#A9F5A9";
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
                                        <canvas id="<? echo $chart; ?>" height=300></canvas>
                                    </div>
<!--
                                </div>
                            </div>
                    	</div>
                    </div>
-->

<script>

var arrayano=<?php echo json_encode($losanos);?>;

// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Arial';//'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

// Pie Chart Example
var ctx = document.getElementById("<? echo $chart; ?>");
var myPieChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: arrayano,
    datasets: [
    <?
    for ($x=1;$x<=2;$x++) {
//    for ($x=1;$x<=1;$x++) {
    	if ($x==1) {$descdisp="S/CASO";}
    	if ($x==2) {$descdisp="C/CASO";}
    	echo "
    	{
    		label:'". $descdisp ."',
      		data: [";
			for ($ano=1;$ano<=$colgra;$ano++) {
				echo $lascant[$x][$ano];
				if ($ano!=$colgra) {echo ",";}
			}
      	echo "
      		],
      		backgroundColor: '".$colores[$x]."',
      	},";
    }
    ?>
    ],
  },
  options: {
      title: {
        display: true,
        text: '<? echo utf8_encode("ESTAD. BIENES CON/SIN NRO CASO FISCAL : ". $desdep); ?>',
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
	animation: {
		onComplete: function () {
			var ctx = this.chart.ctx;
			ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontFamily, 'normal', Chart.defaults.global.defaultFontFamily);
			ctx.fillStyle = "black";
			ctx.textAlign = 'center';
			ctx.textBaseline = 'bottom';

			this.data.datasets.forEach(function (dataset)
			{
				for (var i = 0; i < dataset.data.length; i++) {
					for(var key in dataset._meta)
					{
						var model = dataset._meta[key].data[i]._model;
						ctx.fillText(dataset.data[i], model.x, model.y - 5);
					}
				}
			});
		}
	}
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
