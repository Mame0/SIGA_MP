<?
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
		<title>DETALLE DE MOVIMIENTOS POR PAGO</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script>
			function f_regresar() {
				document.form.action='items_consulta02_itemdepe.php';
				document.form.submit();
			}
		</script>

	</head>
	<body style="margin-bottom: 30px;">
	<center><h2 style="color:#073A6B">MOVIMIENTOS POR PAGOS DE SERVICIOS</h2></center>
		<form name="form" method="post">
			<input type=hidden id="codi_loca" name="codi_loca" value="<? echo $_POST['codi_loca']; ?>">

<?
	$html=new htmlclass;

if(isset($_POST['codi_item'])) {  //genera
	$coditem=$_POST['codi_item'];
	$codloca=$_POST['codi_loca'];
	$nrocont=$_POST['nro_contr'];


	$result=$Db->query("SELECT * FROM `mp_admi_loca` where codi_loca='".$codloca."' ");
	$local=$result[0]['nom1_loca'];

	$result=$Db->query("select * from mp_maes_item where n_codigo='".$coditem."' ");
	$descitem=$result[0]['x_nombre'];


	$busc_item_pagi=20;      //cantidad de items por pagina
	$result=$Db->query("select count(codi_item) as cantreg from mp_movs_item where codi_item='".$coditem."' and codi_loca='".$codloca."' and nro_contr='".$nrocont."' ");
	$busc_tota_item=$result[0]['cantreg'];
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;



	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("LOCAL : ". $local. "<br>SERVICIO : ". $descitem . " - N.Contrato  ". $nrocont );

	$head["1"]="#";
	$head["2"]="CICLO&nbsp;DE&nbsp;FACTURACI&Oacute;N";
	$head["3"]="FECHA&nbsp;VCTO";
	$head["4"]="FECHA&nbsp;PAGO";
	$head["5"]="MONTO&nbsp;PAGO";

	echo $html->put_table_responsive_open();
	echo $html->put_table_responsive_header($head);

	$colgra=0;
	$datait[0][0]="";
	$result_pagi=$Db->query("select mp_movs_item.* from mp_movs_item
	where codi_item='".$coditem."' and codi_loca='".$codloca."' and nro_contr='".$nrocont."'
	order by fech_pago desc limit $busc_limi_pagi,$busc_item_pagi");
	foreach($result_pagi as $rows) {
		$colgra++;
		$data["1"]=$colgra;
		$data["2"]=$rows['cicl_fact'];
		$data["3"]=$rows['fech_vcto'];
		$data["4"]=$rows['fech_pago'];
		$data["5"]=$rows['mont_pago'];

		$datait[$colgra][1]=$rows['cicl_fact'];
		$datait[$colgra][2]=$rows['mont_pago'];

		echo $html->put_table_responsive_data($head,$data);
		unset($data);
	}
	echo $html->put_table_responsive_close();
	echo"</div>";

	$chart="graficoit";
	echo '
	<table align="center" width=50%><tr><td>
		<canvas id="' .$chart. '" height=300></canvas>
	</td></tr></table>';


if ($colgra>12) {
	$cantmov=12;
} else {
	$cantmov=$colgra;
}



?>
<script src="chart.js/Chart.min.js"></script>
<script>
Chart.defaults.global.defaultFontFamily = 'Arial';
Chart.defaults.global.defaultFontColor = '#858796';

var ctx = document.getElementById("<? echo $chart; ?>");
var myPieChart = new Chart(ctx, {
  type: 'bar',

  <?
  echo "
  data: {
    labels: ['";
    for ($x=1;$x<=$cantmov;$x++) {
    	echo $datait[$x][1];
    	if ($x!=$cantmov) {echo ",";}
	}
	echo "'],";
  ?>

    datasets: [
    <?
    	echo "
    	{
    		label:'Montos Pagados',
      		data: [";
			for ($x=1;$x<=$cantmov;$x++) {
				echo $datait[$x][2];
				if ($x!=$cantmov) {echo ",";}
			}
      	echo "
      		],
      		backgroundColor: '#4e73df',
      	},";
    ?>
    ],
  },

  options: {
      title: {
        display: true,
        text: ['<? echo utf8_encode($local); ?>','<? echo utf8_encode("ITEM/SERVICIO : ". $descitem . " - Nro.Contrato ". $nrocont); ?>']
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
      display: false
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



}//genera




		echo $html->put_separator_demand("30");
		echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_regresar()\"> <- Regresar</button>
                                        </div>
                                </div>
                        </div>
                ";


?>
<div id='cargadorvacio'></div>

<center>
	</form>
	</body>
</html>
