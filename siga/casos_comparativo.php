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

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>COMPARATIVA DE CASOS ASIGNADOS</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>


    <!-- Page level plugins -->
    <script src="chart.js/Chart.min.js"></script>

		<script>
			function check_buscar()
			{
				document.form.action='';
				document.form.target="";
				document.form.submit();
			}
		</script>
		<script>
		function oculta() {
			if (document.getElementById('tp_filtro').value == 0) {
				document.getElementById('tpasig_ano').style.display = "none";
				document.getElementById('tpasig_fec').style.display = "none";
			}
			if (document.getElementById('tp_filtro').value == 1) {
				document.getElementById('tpasig_ano').style.display = "block";
				document.getElementById('tpasig_fec').style.display = "none";
			}
			if (document.getElementById('tp_filtro').value == 2) {
				document.getElementById('tpasig_ano').style.display = "none";
				document.getElementById('tpasig_fec').style.display = "block";
			}
		}
		</script>


	</head>
	<body style="margin-bottom: 30px;">
	<center><h2 style="color:#073A6B">COMPARATIVA DE CASOS ASIGNADOS Y RESUELTOS ENTRE A&Ntilde;OS</h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_bien">
<?
	$html=new htmlclass;
	$condadd="";


/*
	$arra_options_anno[0]="<- Seleccione ->";
	$result=$Db->query("SELECT distinct anno_regi FROM `mp_cpbi_bienes` where codi_disp=0 order by anno_regi ");
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
*/

	$result_depe=$Db->query("SELECT * FROM mp_admi_depe where depe_prin=1 ");
	foreach($result_depe as $rows_dp) {
			$arra_options_depe0[$rows_dp['codi_depe']]= $rows_dp['nomb_depe'] ;
	}
	$result_depe=$Db->query("SELECT distinct id_depe FROM datasgf order by id_depe ");
	foreach($result_depe as $rows_dp) {
			$arra_options_depe[$rows_dp['id_depe']]= $arra_options_depe0[$rows_dp['id_depe']] ;
	}

	$arra_options_fisc["0"]="<- TODAS LOS FISCALES ->";
	$result_depe=$Db->query("SELECT distinct id_fiscal, no_fiscal FROM datasgf order by no_fiscal ");
	foreach($result_depe as $rows_dp) {
			$arra_options_fisc[$rows_dp['id_fiscal']]= $rows_dp['no_fiscal'] ;
	}


	$arra_options_anoasig[0]="<- SELECCIONE A&Ntilde;O ->";
	$result_depe=$Db->query("SELECT distinct year(fe_asig) as ano_asig FROM datasgf order by year(fe_asig) ");
	foreach($result_depe as $rows_dp) {
			$arra_options_anoasig[$rows_dp['ano_asig']]= $rows_dp['ano_asig'] ;
	}

	echo"<main style='column-count:1;'>";
	echo $html->put_select("Dependencia",'codi_depe',$arra_options_depe,$_POST['codi_depe']," style='max-width:800px;' ");
	echo"</main>";
/*
	echo"<main style='column-count:1;'>";
	echo $html->put_select("Fiscal",'id_fiscal',$arra_options_fisc,$_POST['id_fiscal']," style='max-width:800px;' ");
	echo"</main>";
*/

	echo "<main id='tpasig_ano' name='tpasig_ano' style='column-count:2;'>";
	echo $html->put_select("A&ntilde;o&nbsp;Comparar&nbsp;1",'ano_asig',$arra_options_anoasig,$_POST['ano_asig']," style='max-width:200px;' ");
	echo $html->put_select("A&ntilde;o&nbsp;Comparar&nbsp;2",'ano_asig2',$arra_options_anoasig,$_POST['ano_asig2']," style='max-width:200px;' ");
	echo "</main>";

	echo"<main style='column-count:3;'>";
	echo $html->put_button_colum("&nbsp;","Mostrar Consulta &raquo;","return check_buscar()");
	echo"</main>";





if(isset($_POST['codi_depe'])) {  //genera grafico
		$coddep=$_POST['codi_depe'];
		$desdep="TODAS LAS FISCALIAS";
		$respad=$Db->query("SELECT * FROM mp_admi_depe where codi_depe=".$coddep." ");
		foreach($respad as $rows_pa) {
			$desdep=$rows_pa['nomb_depe'];
		}

		$condadd="";
		if ($coddep!=0) {
			$condadd.=" and id_depe=".$coddep." ";
		}

		$nomfisc="";
		if(isset($_POST['id_fiscal'])) {
			if($_POST['id_fiscal']!="0") {
				$condadd.=" and id_fiscal='".$_POST['id_fiscal']."' ";
				$result_fisc=$Db->query("SELECT id_fiscal, no_fiscal FROM datasgf where id_fiscal='".$_POST['id_fiscal']."' ");
				$nomfisc=$result_fisc[0]["no_fiscal"];
			}
		}
		$cant=0;
		$losmes[0]="";

		$campos="";


			$campos=" 	SUM(IF(year(fe_ing_caso)=". $_POST['ano_asig'] .", 1,0)) as ingano1,
			SUM(IF(year(fe_conclusion)=". $_POST['ano_asig'] .", 1,0)) as resano1,
			SUM(IF(year(fe_ing_caso)=". $_POST['ano_asig2'] .", 1,0)) as ingano2,
			SUM(IF(year(fe_conclusion)=". $_POST['ano_asig2'] .", 1,0)) as resano2 ";


	echo"<div style=\"width:90%;margin:auto;\">";//max-width:800px;

?>
<br>
<div class="tab">
  <button class="tablinks active" onclick="openCity(event, 'idtab1')">DATOS COMPARATIVOS</button>
  <button class="tablinks" onclick="openCity(event, 'idtab1b')">GRAFICO</button>
</div>
<?


$colores[1]="#2E2EFE";//"#A9A9F5";
$colores[2]="#FA5858";//"#F5A9A9";
$colores[3]="#58FA58";//"#A9F5A9";
$colores[4]="#F5D0A9";
$colores[5]="#A9E2F3";
$colores[6]="#F7FE2E";


	$result_pagi=$Db->query("SELECT id_fiscal, no_fiscal,
	". $campos . "
	FROM `datasgf`
	where id_unico<>'' ".$condadd."
	group by id_fiscal, no_fiscal order by no_fiscal");

	$result_pagi2=$result_pagi;


echo '<div id="idtab1" class="tabcontent" style="display:block;">';
	echo $html->put_title_demand("COMPARATIVA DE CASOS INGRESADOS Y RESUELTOS DE A&Ntilde;OS ".$_POST['ano_asig']." y ". $_POST['ano_asig2'] ."<br>Dependencia: ".$desdep ."<br>Fecha Generaci&oacute;n de Consulta: ". $horalocal);
	$filatab="<tr><td style='padding:5px; width:300px;'><b>FISCAL</b></td>";
	$filatab2="";
	$ncol=1;

		$filatab2.="<td style='padding:5px;'><b>INGRESADO ".$_POST['ano_asig']."</b></td>
		<td style='padding:5px;'><b>RESUELTO ".$_POST['ano_asig']."</b></td>
		<td style='padding:5px;'><b>INGRESADO ".$_POST['ano_asig2']."</b></td>
		<td style='padding:5px;'><b>RESUELTO ".$_POST['ano_asig2']."</b></td>
		</tr>";


		echo "<div style='overflow-x:auto;'>
		<table border=1 style='border-collapse: collapse; font-size:12px; text-align:center;'>".$filatab.$filatab2."</tr>";

	$cont=0;
	$sumasi1=0;
	$sumasi2=0;
	$sumres1=0;
	$sumres2=0;

	foreach($result_pagi2 as $rows) {
		$cont++;
		$filatab="<tr><td style='text-align: justify; padding:5px;'>".$rows['no_fiscal']."</td>";
		$filatab2="";
		$losfisc[$cont]=$rows['no_fiscal'];
		$sumasg=0;
		$sumres=0;

			$filatab2.="<td style='padding:5px;'>".$rows['ingano1']."</td>
			<td style='padding:5px;'>".$rows['resano1']."</td>
			<td style='padding:5px;'>".$rows['ingano2']."</td>
			<td style='padding:5px;'>".$rows['resano2']."</td>
			</tr>";

			$sumasi1=$sumasi1+$rows['ingano1'];
			$sumasi2=$sumasi2+$rows['ingano2'];
			$sumres1=$sumres1+$rows['resano1'];
			$sumres2=$sumres2+$rows['resano2'];


			$losfisc1[$cont][1]=$rows['ingano1'];
			$losfisc1[$cont][2]=$rows['ingano2'];
			$losfisc1[$cont][3]=$rows['resano1'];
			$losfisc1[$cont][4]=$rows['resano2'];

		echo $filatab.$filatab2."</tr>";
	}
	echo "<tr><td style='text-align: justify; padding:5px;'><b>TOTAL ACUMULADO</b></td>
				<td style='padding:5px;'>".$sumasi1."</td>
				<td style='padding:5px;'>".$sumres1."</td>
				<td style='padding:5px;'>".$sumasi2."</td>
				<td style='padding:5px;'>".$sumres2."</td>
			</tr>";

	echo "</table></div>";

echo '</div>';//idtab1





			$addtitulo = " ".$_POST['ano_asig']." y ".$_POST['ano_asig2'];


		echo '<div id="idtab1b" class="tabcontent">';

?>
                                    <div class="chart-pie pt-4" >
                                        <canvas id="asignado" height=500 width=1000 style="border: 1px solid blue; background-color:#ffffff;"></canvas>
                                    </div>
                                    <div class="chart-pie pt-4" >
                                        <canvas id="resuelto" height=500 width=1000 style="border: 1px solid red; background-color:#ffffff;"></canvas>
                                    </div>

<script>
Chart.defaults.global.defaultFontFamily = 'Arial';
Chart.defaults.global.defaultFontColor = '#858796';

var ctx = document.getElementById("asignado");
var myPieChart = new Chart(ctx, {
  type: 'bar',
  data: {
  <?
  	echo 'labels: [';
	for ($xfi=1;$xfi<=$cont;$xfi++) {
		echo '"' . $losfisc[$xfi] . '"';
		if ($xfi!=$cont) {echo ",";}
	}
	echo "],";
  ?>
    datasets: [
    <?
    	$descdisp="TOTAL CASOS INGRESADOS";
    	echo "
    	{
    		label:'".$_POST['ano_asig']."',
      		data: [";
			for ($xfi=1;$xfi<=$cont;$xfi++) {
				echo $losfisc1[$xfi][1];
				if ($xfi!=$cont) {echo ",";}
			}
      	echo "
      		],
      		backgroundColor: '".$colores[1]."',
      	},
    	{
    		label:'".$_POST['ano_asig2']."',
      		data: [";
			for ($xfi=1;$xfi<=$cont;$xfi++) {
				echo $losfisc1[$xfi][2];
				if ($xfi!=$cont) {echo ",";}
			}
      	echo "
      		],
      		backgroundColor: '".$colores[2]."',
      	},


      	";
    ?>
    ],
  },

    options: {
    responsive: false,
      legend: { display: false },
      title: {
        display: true,
        text: ['<? echo $desdep; ?>','TOTAL DE CASOS INGRESADOS A FISCALES','<? echo $addtitulo; ?>'],
      }
    }


});




Chart.defaults.global.defaultFontFamily = 'Arial';
Chart.defaults.global.defaultFontColor = '#858796';

var ctx = document.getElementById("resuelto");
var myPieChart = new Chart(ctx, {
  type: 'bar',
  data: {
  <?
  	echo 'labels: [';
	for ($xfi=1;$xfi<=$cont;$xfi++) {
		echo '"' . $losfisc[$xfi] . '"';
		if ($xfi!=$cont) {echo ",";}
	}
	echo "],";
  ?>
    datasets: [
    <?
    	$descdisp="TOTAL CASOS RESUELTOS";
    	echo "
    	{
    		label:'".$_POST['ano_asig']."',
      		data: [";
			for ($xfi=1;$xfi<=$cont;$xfi++) {
				echo $losfisc1[$xfi][3];
				if ($xfi!=$cont) {echo ",";}
			}
      	echo "
      		],
      		backgroundColor: '".$colores[1]."',
      	},
    	{
    		label:'".$_POST['ano_asig2']."',
      		data: [";
			for ($xfi=1;$xfi<=$cont;$xfi++) {
				echo $losfisc1[$xfi][4];
				if ($xfi!=$cont) {echo ",";}
			}
      	echo "
      		],
      		backgroundColor: '".$colores[2]."',
      	},
      	";
    ?>
    ],
  },

    options: {
    responsive: false,
      legend: { display: false },
      title: {
        display: true,
        text: ['<? echo $desdep; ?>','TOTAL DE CASOS RESUELTOS POR FISCALES','<? echo $addtitulo; ?>'],
      }
    }


});



</script>


<?

		echo '</div>';//idtab1b





	echo"</div>";


}//muestra consulta


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


<script>
function openCity(evt, cityName) {
	evt.preventDefault()

  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}
</script>
<style>
body {font-family: Arial;}

/* Style the tab */
.tab {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
  font-size: 14px;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}
</style>
