<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;

if(isset($_GET['cd'])) {
	$result_depe=$Db->query("insert into mp_cbpi_preferenciaestad (desc_preferencia, tp_graf, cods_depe) values ('".$_GET['dsc']."','1','".$_GET['cd']."') ");
	exit();
}

if(isset($_GET['pf'])) {
	$result_depe=$Db->query("SELECT * FROM mp_cbpi_preferenciaestad where codi_pref=".$_GET['pf']." ");
	foreach($result_depe as $rows_dp)
		$codsdep=$rows_dp['cods_depe'];

	$_POST['cods_depe']=$codsdep;
}


?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>ESTADISTICA DE BIENES INCAUTADOS POR DISPOSICION</title>
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




<!--
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/lib/bootstrap.min.css">
  <script src="/lib/jquery-1.12.2.min.js"></script>
  <script src="/lib/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
  <h2>Form control: checkbox</h2>
  <p>The form below contains three checkboxes. The last option is disabled:</p>
  <form role="form">
<div style="height:200px; overflow: scroll;">
    <div class="checkbox">
      <label><input type="checkbox" value="">Option 1</label>
    </div>
    <div class="checkbox">
      <label><input type="checkbox" value="">Option 2</label>
    </div>
    <div class="checkbox disabled">
      <label><input type="checkbox" value="" disabled>Option 3</label>
    </div>
    <div class="checkbox disabled">
      <label><input type="checkbox" value="" disabled>Option 3</label>
    </div>
    <div class="checkbox disabled">
      <label><input type="checkbox" value="" disabled>Option 3</label>
    </div>
    <div class="checkbox disabled">
      <label><input type="checkbox" value="" disabled>Option 3</label>
    </div>
</div>
  </form>
</div>

</body>
</html>
-->




		<script>
			function graba_pref() {
				var cods_depe = document.getElementById('cods_depe').value;
				var desc_pref = document.getElementById('desc_pref').value;
				location.href= "cpbi_bienes_estaddispo2.php?cd="+cods_depe+"&dsc="+desc_pref;
				alert ("preferencia guardada");
				return false;
			}
			function check_buscar()
			{
				var cods=document.form.todo_depe.value;
				var myarr = cods.split("|");
				var coddep=0;
				var cods_depe="";
				for (x=0;x<myarr.length;x++) {
					coddep=myarr[x];
					if (document.getElementById('chk'+coddep).checked) {
						cods_depe=cods_depe+coddep+"|";
					}
				}
				document.getElementById('cods_depe').value=cods_depe;
			}
			function ajustar_altura()
                        {
                                parent.document.getElementById('body_iframe').height=parent.window.innerHeight-80;
                        }
                        ajustar_altura();
		</script>











	</head>
	<body style="margin-bottom: 30px;">
	<center><h2 style="color:#073A6B">ESTADISTICA DE BIENES POR DISPOSICION</h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_bien">
<?
	$html=new htmlclass;
	$condadd="";


	$arra_options_anno[0]="<- Seleccione ->";
	$result=$Db->query("SELECT distinct anno_regi FROM `mp_cpbi_bienes` order by anno_regi ");
	foreach($result as $rows) {
			$arra_options_anno[$rows['anno_regi']]= $rows['anno_regi'] ;
	}


	$tododep="";
	$cantdep=0;
	$result_depe=$Db->query("SELECT * FROM mp_admi_depe where depe_prin=1 ");
	foreach($result_depe as $rows_dp) {
		$coddep=$rows_dp['codi_depe'];

		$condadd="";
		$respad=$Db->query("SELECT * FROM mp_admi_depe where codi_padr=".$coddep." order by nomb_depe");
		foreach($respad as $rows_pa) {
			$condadd.=" or codi_depe=".$rows_pa['codi_depe']." ";
		}
		$result_depe=$Db->query("SELECT codi_epro, count( * ) AS cant FROM `mp_cpbi_bienes` where (codi_depe=".$coddep." ".$condadd.") and codi_disp=0 ");
		$cant=$result_depe[0]['cant'];
		if ($cant!=0) {
			$cantdep++;
			$lasdep[$cantdep][1]=$rows_dp['codi_depe'];
			//$lasdep[$cantdep][2]=$rows_dp['nomb_depe'];
			$lasdep[$cantdep][2]=$rows_dp['sigl_depe'];

			if ($tododep!="") {$tododep.="|";}
			$tododep.=$rows_dp['codi_depe'];
		}
	}

echo"<main style='column-count:1;'>";
echo $html->put_title_demand("DEPENDENCIAS A COMPARAR");
echo '<div class="row" style="height:150px; overflow: scroll;">';

$loscod=explode("|",$_POST['cods_depe']);
$cantcod=count($loscod);
for ($dep=1;$dep<=$cantdep;$dep++) {
	$coddep=$lasdep[$dep][1];
	$desdep=$lasdep[$dep][2];
	$add="";
	for ($x=1;$x<=$cantcod;$x++) {
		if ($coddep==$loscod[$x-1]) {
			$add=" checked='checked' ";
		}
	}
	echo '<div style="float: left; width: 250px;">';
    echo '<div class="checkbox" style="font-size: 12px;">
      <label style="padding:5px 5px 5px 5px;"><input type="checkbox" id="chk'.$coddep.'" name="chk'.$coddep.'" value="'.$coddep.'" '.$add.'> '.$desdep.'</label>
    </div>';
    echo '</div>';
}
echo '</div>';
echo"</main>";

echo"<main style='column-count:3;'>";
echo $html->put_select("A&ntilde;o&nbsp;Inicio",'anno_ini',$arra_options_anno,$_POST['anno_ini']," style='width:150px;' ");
echo $html->put_select("A&ntilde;o&nbsp;Fin",'anno_fin',$arra_options_anno,$_POST['anno_fin']," style='width:150px;' ");
echo $html->put_button_colum("&nbsp;","Generar Gr&aacute;fico &raquo;","return check_buscar()");
echo"</main>";




echo "<input type='hidden' id='todo_depe' name='todo_depe' value='".$tododep."'>";
echo "<input type='hidden' id='cods_depe' name='cods_depe' value='".$_POST['cods_depe']."'>";


if(isset($_POST['cods_depe'])) {  //genera grafico
	$anoini=$_POST['anno_ini'];
	$anofin=$_POST['anno_fin'];

	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("ESTADISTICA DE BIENES POR DISPOSICION");

	$head["1"]="Nº";
	$head["2"]="DEPENDENCIA";
	$head["3"]="TOTAL REGISTROS";
	$head["4"]="SIN DISPOSICION";
	$head["5"]="CON DISPOSICION";

	echo $html->put_table_responsive_open();
	echo $html->put_table_responsive_header($head);

$cantdepgraf=0;
	for ($dep=1;$dep<=$cantdep;$dep++) {
		$coddep=$lasdep[$dep][1];
		$desdep=$lasdep[$dep][2];

		$condadd="";
		for ($x=1;$x<=$cantcod;$x++) {
			if ($coddep==$loscod[$x-1]) {
				$condadd=" codi_depe='".$coddep."' ";
				$respad=$Db->query("SELECT * FROM mp_admi_depe where codi_padr=".$coddep." order by nomb_depe");
				foreach($respad as $rows_pa) {
					$condadd.=" or codi_depe=".$rows_pa['codi_depe']." ";
				}
				$condadd=" where (" . $condadd . ") ";
			}
		}

		if ($condadd!="") {
			$condadd.=" and anno_regi>='".$anoini."' and anno_regi<='".$anofin."' ";
		}

		if ($condadd!="") {
			$cantdepgraf++;

			//$losdep[$dep-1]=$desdep;
			//$data['1']=$dep;
			$losdep[$cantdepgraf-1]=$desdep;
			$data['1']=$cantdepgraf;


			$data['2']=(($desdep=="")?"NO ASIGNADO":   utf8_encode($desdep));
	//		if ($ubi==1) {
	//			$condadd=" where (codi_depe<>22 and codi_depe<>31 and codi_depe<>40 and codi_depe<>119 and codi_depe<>123 and codi_depe<>148) ";
	//		} else {
	//			$condadd=" where codi_depe='".$coddep."' ";
	//		}
			$result_esta=$Db->query("SELECT codi_ubic, count( * ) AS cant FROM `mp_cpbi_bienes` ".$condadd." ");
			$busc_tota_item=0;
			$xxa=$ano+2;
			$cant=$result_esta[0]['cant'];
			$data['3']=$cant;
			//$lascant[$dep][1]=$cant;//[$ano]
			$lascant[$cantdepgraf][1]=$cant;//[$ano]

			$result_esta=$Db->query("SELECT codi_ubic, count( * ) AS cant FROM `mp_cpbi_bienes` ".$condadd." and codi_disp=0 ");
			$busc_tota_item=0;
			$xxa=$ano+2;
			$cant=$result_esta[0]['cant'];
			$data['4']=$cant;
			//$lascant[$dep][2]=$cant;//[$ano]
			$lascant[$cantdepgraf][2]=$cant;//[$ano]

			$result_esta=$Db->query("SELECT codi_ubic, count( * ) AS cant FROM `mp_cpbi_bienes` ".$condadd." and codi_disp<>0 ");
			$busc_tota_item=0;
			$xxa=$ano+2;
			$cant=$result_esta[0]['cant'];
			$data['5']=$cant;
			//$lascant[$dep][3]=$cant;//[$ano]
			$lascant[$cantdepgraf][3]=$cant;//[$ano]

			echo $html->put_table_responsive_data($head,$data);
		}//si esta seleccionado



	}
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
  type: 'bar',
  data: {
    labels: arraydep,
    datasets: [
    <?
    for ($x=1;$x<=3;$x++) {
    	if ($x==1) {$titx="TOTAL REGISTROS";}
    	if ($x==2) {$titx="SIN DISPOSICION";}
    	if ($x==3) {$titx="CON DISPOSICION";}

    	echo "
    	{
    		label:'".$titx."',
      		data: [";
			for ($dep=1;$dep<=$cantdep;$dep++) {
				echo $lascant[$dep][$x];
				if ($dep!=$cantdep) {echo ",";}
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
		scales: {
            xAxes: [{
                ticks: {
                    autoSkip: false,
                    maxRotation: 90,
                    minRotation: 90
                }
            }]
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


/*
		echo $html->put_separator_demand("30");
		echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                        	<b>Descripci&oacute;n Preferencia:</b><input type='text' id='desc_pref' name='desc_pref' placeholder='Ingresa una descripcion'>
                                                <!--<button class=\"button_foot\" onclick=\"document.form.reset()\">Cancelar</button>-->
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"graba_pref()\">Grabar Preferencia</button>
                                        </div>
                                </div>
                        </div>
                ";
*/


}//codigos de dependencias





?>
<center>
	</form>
	</body>
</html>
