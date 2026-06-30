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
		<title>ANALISIS DE LA CARGA Y EVOLUCION DE CASOS ASIGNADOS</title>
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
				if (document.getElementById('tp_filtro').value == 1) {
					if (document.getElementById('ano_asig').value == 0) {
						alert ("SELECCIONE AÑO");
						return false;
					}
				}
				if (document.getElementById('tp_filtro').value == 2) {
					if (document.getElementById('fini_asig').value == "") {
						alert ("INGRESE FECHA INICIAL DE ASIGNACION");
						return false;
					}
					if (document.getElementById('ffin_asig').value == "") {
						alert ("INGRESE FECHA FINAL DE ASIGNACION");
						return false;
					}
				}
				document.form.action='';
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
	<center><h2 style="color:#073A6B">ANALISIS DE LA CARGA Y EVOLUCION DE CASOS ASIGNADOS</h2></center>
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

	$arra_options_tp[0]="TODA LA INFORMACION";
	$arra_options_tp[1]="FILTRADO POR A&Ntilde;O";
	$arra_options_tp[2]="FILTRADO POR INTERVALO DE FECHAS";

	echo"<main style='column-count:1;'>";
	echo $html->put_select("Dependencia",'codi_depe',$arra_options_depe,$_POST['codi_depe']," style='max-width:800px;' ");
	echo"</main>";
	echo"<main style='column-count:1;'>";
	echo $html->put_select("Fiscal",'id_fiscal',$arra_options_fisc,$_POST['id_fiscal']," style='max-width:800px;' ");
	echo"</main>";

	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_select("INFORMACI&Oacute;N&nbsp;A&nbsp;MOSTRAR",'tp_filtro',$arra_options_tp,$_POST['tp_filtro']," style='max-width:400px; ' onchange='oculta()' ");
	echo "</div>";


	if($_POST['tp_filtro']==1) {
		$adddisp=" display:block; ";
	} else {
		$adddisp=" display:none; ";
	}
	echo "<main id='tpasig_ano' name='tpasig_ano' style='column-count:1; ".$adddisp." '>";
	echo $html->put_select("A&ntilde;o&nbsp;Asignaci&oacute;n",'ano_asig',$arra_options_anoasig,$_POST['ano_asig']," style='max-width:200px;' ");
	echo "</main>";

	if($_POST['tp_filtro']==2) {
		$adddisp=" display:block; ";
	} else {
		$adddisp=" display:none; ";
	}
	echo "<main id='tpasig_fec' name='tpasig_fec' style='column-count:2; ".$adddisp." '>";
	echo $html->put_text('date',"Desde&nbsp;Fecha","",'fini_asig',$_POST['fini_asig'],'','10','');
	echo $html->put_text('date',"Hasta&nbsp;Fecha","",'ffin_asig',$_POST['ffin_asig'],'','10','');
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
		if($_POST['tp_filtro']==0) {
			$condfec="year(fe_asig)='".  date('Y') ."' ";
			$elano=date('Y');

			$campos=" 	SUM(IF(fe_asig like '".$elano."-01%', 1,0)) as asig1, SUM(IF(fe_conclusion like '".$elano."-01%', 1,0)) as resu1,
				SUM(IF(fe_asig like '".$elano."-02%', 1,0)) as asig2, SUM(IF(fe_conclusion like '".$elano."-02%', 1,0)) as resu2,
				SUM(IF(fe_asig like '".$elano."-03%', 1,0)) as asig3, SUM(IF(fe_conclusion like '".$elano."-03%', 1,0)) as resu3,
				SUM(IF(fe_asig like '".$elano."-04%', 1,0)) as asig4, SUM(IF(fe_conclusion like '".$elano."-04%', 1,0)) as resu4,
				SUM(IF(fe_asig like '".$elano."-05%', 1,0)) as asig5, SUM(IF(fe_conclusion like '".$elano."-05%', 1,0)) as resu5,
				SUM(IF(fe_asig like '".$elano."-06%', 1,0)) as asig6, SUM(IF(fe_conclusion like '".$elano."-06%', 1,0)) as resu6,
				SUM(IF(fe_asig like '".$elano."-07%', 1,0)) as asig7, SUM(IF(fe_conclusion like '".$elano."-07%', 1,0)) as resu7,
				SUM(IF(fe_asig like '".$elano."-08%', 1,0)) as asig8, SUM(IF(fe_conclusion like '".$elano."-08%', 1,0)) as resu8,
				SUM(IF(fe_asig like '".$elano."-09%', 1,0)) as asig9, SUM(IF(fe_conclusion like '".$elano."-09%', 1,0)) as resu9,
				SUM(IF(fe_asig like '".$elano."-10%', 1,0)) as asig10, SUM(IF(fe_conclusion like '".$elano."-10%', 1,0)) as resu10,
				SUM(IF(fe_asig like '".$elano."-11%', 1,0)) as asig11, SUM(IF(fe_conclusion like '".$elano."-11%', 1,0)) as resu11,
				SUM(IF(fe_asig like '".$elano."-12%', 1,0)) as asig12, SUM(IF(fe_conclusion like '".$elano."-12%', 1,0)) as resu12 ";
				$cant=12;

				$losmeses[1]="ENERO ".$elano;
				$losmeses[2]="FEBRERO ".$elano;
				$losmeses[3]="MARZO ".$elano;
				$losmeses[4]="ABRIL ".$elano;
				$losmeses[5]="MAYO ".$elano;
				$losmeses[6]="JUNIO ".$elano;
				$losmeses[7]="JULIO ".$elano;
				$losmeses[8]="AGOSTO ".$elano;
				$losmeses[9]="SEPTIEMBRE ".$elano;
				$losmeses[10]="OCTUBRE ".$elano;
				$losmeses[11]="NOVIEMBRE ".$elano;
				$losmeses[12]="DICIEMBRE ".$elano;
		}

		if($_POST['tp_filtro']==1) {
			$condfec="year(fe_asig)='".$_POST['ano_asig']."' ";
			$elano=$_POST['ano_asig'];

			$campos=" 	SUM(IF(fe_asig like '".$elano."-01%', 1,0)) as asig1, SUM(IF(fe_conclusion like '".$elano."-01%', 1,0)) as resu1,
				SUM(IF(fe_asig like '".$elano."-02%', 1,0)) as asig2, SUM(IF(fe_conclusion like '".$elano."-02%', 1,0)) as resu2,
				SUM(IF(fe_asig like '".$elano."-03%', 1,0)) as asig3, SUM(IF(fe_conclusion like '".$elano."-03%', 1,0)) as resu3,
				SUM(IF(fe_asig like '".$elano."-04%', 1,0)) as asig4, SUM(IF(fe_conclusion like '".$elano."-04%', 1,0)) as resu4,
				SUM(IF(fe_asig like '".$elano."-05%', 1,0)) as asig5, SUM(IF(fe_conclusion like '".$elano."-05%', 1,0)) as resu5,
				SUM(IF(fe_asig like '".$elano."-06%', 1,0)) as asig6, SUM(IF(fe_conclusion like '".$elano."-06%', 1,0)) as resu6,
				SUM(IF(fe_asig like '".$elano."-07%', 1,0)) as asig7, SUM(IF(fe_conclusion like '".$elano."-07%', 1,0)) as resu7,
				SUM(IF(fe_asig like '".$elano."-08%', 1,0)) as asig8, SUM(IF(fe_conclusion like '".$elano."-08%', 1,0)) as resu8,
				SUM(IF(fe_asig like '".$elano."-09%', 1,0)) as asig9, SUM(IF(fe_conclusion like '".$elano."-09%', 1,0)) as resu9,
				SUM(IF(fe_asig like '".$elano."-10%', 1,0)) as asig10, SUM(IF(fe_conclusion like '".$elano."-10%', 1,0)) as resu10,
				SUM(IF(fe_asig like '".$elano."-11%', 1,0)) as asig11, SUM(IF(fe_conclusion like '".$elano."-11%', 1,0)) as resu11,
				SUM(IF(fe_asig like '".$elano."-12%', 1,0)) as asig12, SUM(IF(fe_conclusion like '".$elano."-12%', 1,0)) as resu12 ";
				$cant=12;

				$losmeses[1]="ENERO ".$elano;
				$losmeses[2]="FEBRERO ".$elano;
				$losmeses[3]="MARZO ".$elano;
				$losmeses[4]="ABRIL ".$elano;
				$losmeses[5]="MAYO ".$elano;
				$losmeses[6]="JUNIO ".$elano;
				$losmeses[7]="JULIO ".$elano;
				$losmeses[8]="AGOSTO ".$elano;
				$losmeses[9]="SEPTIEMBRE ".$elano;
				$losmeses[10]="OCTUBRE ".$elano;
				$losmeses[11]="NOVIEMBRE ".$elano;
				$losmeses[12]="DICIEMBRE ".$elano;
		}
		if($_POST['tp_filtro']==2) {
			$condfec="fe_asig>='".$_POST['fini_asig']."' and fe_asig<='".$_POST['ffin_asig']."'";
			$elano=substr( $_POST['fini_asig'] ,0,4);


            $mes = substr($_POST['fini_asig'],5,2);// date("m", $_POST['fini_asig']);
            $ano = substr($_POST['fini_asig'],0,4);// date("Y", $_POST['fini_asig']);

            $mes2 = substr($_POST['ffin_asig'],5,2);// date("m", $_POST['ffin_asig']);
            $ano2 = substr($_POST['ffin_asig'],0,4);// date("Y", $_POST['ffin_asig']);
            $fecini=date("Y-m-d", mktime(0, 0, 0, $mes, 1, $ano));
            $fecfin=date("Y-m-d", mktime(0, 0, 0, $mes2, 1, $ano2));

			$cant=0;
			$m=0;
            while($fecini<=$fecfin){
            	if ($campos!="") {$campos.=", ";}
            	$cant++;
				$campos.=" SUM(IF(fe_asig like '". substr($fecini,0,7) ."%', 1,0)) as `asig".$cant."`, SUM(IF(fe_conclusion like '". substr($fecini,0,7) ."%', 1,0)) as `resu".$cant."` ";


				if (substr($fecini,5,2)=="01") { $nommes="ENERO ". substr($fecini,0,4);  }
				if (substr($fecini,5,2)=="02") { $nommes="FEBRERO ". substr($fecini,0,4);  }
				if (substr($fecini,5,2)=="03") { $nommes="MARZO ". substr($fecini,0,4);  }
				if (substr($fecini,5,2)=="04") { $nommes="ABRIL ". substr($fecini,0,4);  }
				if (substr($fecini,5,2)=="05") { $nommes="MAYO ". substr($fecini,0,4);  }
				if (substr($fecini,5,2)=="06") { $nommes="JUNIO ". substr($fecini,0,4);  }
				if (substr($fecini,5,2)=="07") { $nommes="JULIO ". substr($fecini,0,4);  }
				if (substr($fecini,5,2)=="08") { $nommes="AGOSTO ". substr($fecini,0,4);  }
				if (substr($fecini,5,2)=="09") { $nommes="SEPTIEMBRE ". substr($fecini,0,4);  }
				if (substr($fecini,5,2)=="10") { $nommes="OCTUBRE ". substr($fecini,0,4);  }
				if (substr($fecini,5,2)=="11") { $nommes="NOVIEMBRE ". substr($fecini,0,4);  }
				if (substr($fecini,5,2)=="12") { $nommes="DICIEMBRE ". substr($fecini,0,4);  }
				$losmeses[$cant]=$nommes;


				$m++;
                $fecini=date("Y-m-d", mktime(0, 0, 0, $mes+$m, 1, $ano));
            }

		}

	echo"<div style=\"width:90%;margin:auto;\">";//max-width:800px;

?>
<br>
<div class="tab">
  <button class="tablinks active" onclick="openCity(event, 'idtab1')">CASOS INGRESADOS</button>
<? 	if($_POST['id_fiscal']=="0") { ?>
  <button class="tablinks" onclick="openCity(event, 'idtab1b')">C.INGRESADOS (GRAFICO)</button>
<? } ?>

  <button class="tablinks" onclick="openCity(event, 'idtab3')">PRIN.OPORTUNIDAD<br>PENDIENTES 60 DIAS</button>
  <button class="tablinks" onclick="openCity(event, 'idtab2')">CASOS EN TRAMITE</button>
<? 	if($_POST['id_fiscal']!="0") { ?>
  <button class="tablinks" onclick="openCity(event, 'idtab2b')">C.TRAMITE (GRAFICO)</button>
<? } ?>

</div>
<?



$colores[1]="#2E2EFE";//"#A9A9F5";
$colores[2]="#FA5858";//"#F5A9A9";
$colores[3]="#58FA58";//"#A9F5A9";
$colores[4]="#F5D0A9";
$colores[5]="#A9E2F3";
$colores[6]="#F7FE2E";


	$result_pagi=$Db->query("SELECT id_fiscal, no_fiscal,
	SUM(
		IF(de_estado = 'CON PRINCIPIO DE OPORTUNIDAD (CALIFICA)',
			IF(datediff(now(),fe_asig) >= 180, 1,0)
		, 0)
	) as nro_pri_180,
	SUM(
		IF(de_estado = 'DENUNCIA PENDIENTE',
			if(condicion='EN TRAMITE',
				IF(datediff(now(),fe_asig) >= 60, 1,0)
			, 0)
		,0)
	) AS nro_pen_60 ,


	SUM(IF(de_estado = 'DENUNCIA PENDIENTE', if(condicion='EN TRAMITE', 1, 0),0)) AS nro_denupend ,
	SUM(IF(de_etapa = 'CALIFICACION', if(condicion='EN TRAMITE', 1, 0),0)) as nro_califica ,
	SUM(IF(de_etapa = 'INVESTIGACION PRELIMINAR', if(condicion='EN TRAMITE', 1, 0),0)) as nro_invpreli ,
	SUM(IF(de_etapa = 'INVESTIGACION PREPARATORIA', if(condicion='EN TRAMITE', 1, 0),0)) as nro_invprepa , ". $campos . "
	FROM `datasgf`
	where id_unico<>'' ".$condadd." and ".$condfec."
	group by id_fiscal, no_fiscal order by no_fiscal");

	$result_pagi2=$result_pagi;


echo '<div id="idtab1" class="tabcontent" style="display:block;">';
	echo $html->put_title_demand("CASOS INGRESADOS Y ASIGNADOS<br>Dependencia: ".$desdep ."<br>Fecha Generaci&oacute;n de Consulta: ". $horalocal);
	$filatab="<tr><td style='padding:5px; width:300px;'><b>FISCAL</b></td>";
	$filatab2="";
	$ncol=1;
	for ($xmes=1; $xmes<=$cant; $xmes++) {
		$filatab2.="<td style='padding:5px;'><b>ASIGNADO ".$losmeses[$xmes]."</b></td><td style='padding:5px;'><b>RESUELTO ".$losmeses[$xmes]."</b></td>";
	}
$ancho=300+ ((($cant*2)+2)*100);

		$filatab1="<td style='padding:5px; background-color:#CED8F6;'><b>TOTAL ASIGNADO</b></td>
		<td style='padding:5px; background-color:#F6CECE;'><b>TOTAL RESUELTO</b></td>";

		echo "<div style='overflow-x:auto;'>
		<table border=1 style='border-collapse: collapse; font-size:12px; width:".$ancho."px; text-align:center;'>".$filatab.$filatab1.$filatab2."</tr>";

	$cont=0;
	foreach($result_pagi2 as $rows) {
		$cont++;
		$filatab="<tr><td style='text-align: justify; padding:5px;'>".$rows['no_fiscal']."</td>";
		$filatab2="";
		$losfisc[$cont]=$rows['no_fiscal'];
		$sumasg=0;
		$sumres=0;
		for ($xmes=1; $xmes<=$cant; $xmes++) {
			$sumasg=$sumasg+$rows['asig'.$xmes];
			$sumres=$sumres+$rows['resu'.$xmes];
			$filatab2.="<td style='padding:5px;'>".$rows['asig'.$xmes]."</td><td style='padding:5px;'>".$rows['resu'.$xmes]."</td>";
		}
			$losfisc1[$cont][1]=$sumasg;
			$losfisc1[$cont][2]=$sumres;

		$filatab1="<td style='background-color:#CED8F6; padding:5px;'>".$sumasg."</td><td style='background-color:#F6CECE; padding:5px;'>".$sumres."</td>";
		echo $filatab.$filatab1.$filatab2."</tr>";
	}
	echo "</table></div>";

echo '</div>';//idtab1





	if($_POST['id_fiscal']=="0") {
		if($_POST['tp_filtro']==0 || $_POST['tp_filtro']==1) {
			$addtitulo = utf8_encode(" Año ".$elano);
		}
		if($_POST['tp_filtro']==2) {
			$addtitulo = " Del ".$_POST['fini_asig']." al ".$_POST['ffin_asig'];
		}


		echo '<div id="idtab1b" class="tabcontent">';

?>
                                    <div class="chart-pie pt-4" >
                                        <canvas id="asignado" height=500 width=800 style="border: 1px solid blue;"></canvas>
                                    </div>
                                    <div class="chart-pie pt-4" >
                                        <canvas id="resuelto" height=500 width=800 style="border: 1px solid red;"></canvas>
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
    	$descdisp="TOTAL CASOS ASIGNADOS";
    	echo "
    	{
    		label:'". $descdisp ."',
      		data: [";
			for ($xfi=1;$xfi<=$cont;$xfi++) {
				echo $losfisc1[$xfi][1];
				if ($xfi!=$cont) {echo ",";}
			}
      	echo "
      		],
      		backgroundColor: '".$colores[1]."',
      	},";
    ?>
    ],
  },

    options: {
    responsive: false,
      legend: { display: false },
      title: {
        display: true,
        text: ['<? echo $desdep; ?>','TOTAL DE CASOS ASIGNADOS A FISCALES','<? echo $addtitulo; ?>'],
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
    		label:'". $descdisp ."',
      		data: [";
			for ($xfi=1;$xfi<=$cont;$xfi++) {
				echo $losfisc1[$xfi][2];
				if ($xfi!=$cont) {echo ",";}
			}
      	echo "
      		],
      		backgroundColor: '".$colores[2]."',
      	},";
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


	}//si es un fiscal muestro grafico pie











$result_pagi3=$result_pagi;
echo '<div id="idtab3" class="tabcontent">';
	echo $html->put_title_demand("PRINCIPIOS DE OPORTUNIDAD Y CASOS PENDIENTES CON MAS DE 60 DIAS : ".$desdep);
		echo "<table border=1 style='border-collapse: collapse; font-size:12px; text-align:center;'><tr>
		<td style='padding:5px; width:300px;'><b>FISCAL</b></td>
		<td style='padding:5px;'><b>PRINCIPIOS DE OPORTUNIDAD CON MAS DE 180 DIAS DE SU ASIGNACION</b></td>
		<td style='padding:5px;'><b>DENUNCIA PENDIENTE CON MAS DE 60 DIAS SIN APERTURAR</b></td></tr>";
	foreach($result_pagi3 as $rows) {
		echo "<tr>
		<td style='text-align: justify; padding:5px;'>".$rows['no_fiscal']."</td>
		<td style='padding:5px;'>".$rows['nro_pri_180']."</td>
		<td style='padding:5px;'>".$rows['nro_pen_60']."</td></tr>";
	}
	echo "</table></div>";
echo '</div>';//idtab3


echo '<div id="idtab2" class="tabcontent">';

	echo $html->put_title_demand("CASOS EN TRAMITE : ".$desdep);


$ancho=300+ (5*130);
		echo "<div style='overflow-x:auto;'>
		<table border=1 style='border-collapse: collapse; font-size:12px; width:".$ancho."px; text-align:center;'><tr>
		<td style='padding:5px; width:300px;'><b>FISCAL</b></td>
		<td style='padding:5px;'><b>DENUNCIA PENDIENTES</b></td>
		<td style='padding:5px;'><b>CALIFICACION</b></td>
		<td style='padding:5px;'><b>INVESTIGACION PRELIMINAR</b></td>
		<td style='padding:5px;'><b>INVESTIGACION PREPARATORIA</b></td>
		<td style='padding:5px; background-color:#CED8F6;'><b>TOTAL EN TRAMITE (NO CONSIDERA INTERMEDIA, JUZGAMIENTO)</b></td></tr>";

	foreach($result_pagi as $rows) {
		$cont++;
		echo "<tr>
		<td style='text-align: justify; padding:5px;'>".$rows['no_fiscal']."</td>
		<td style='padding:5px;'>".$rows['nro_denupend']."</td>
		<td style='padding:5px;'>".$rows['nro_califica']."</td>
		<td style='padding:5px;'>".$rows['nro_invpreli']."</td>
		<td style='padding:5px;'>".$rows['nro_invprepa']."</td>
		<td style='padding:5px; background-color:#CED8F6;'>". ($rows['nro_denupend'] + $rows['nro_califica'] + $rows['nro_invpreli'] + $rows['nro_invprepa']) ."</td></tr>";

		if($_POST['id_fiscal']!="0") {
			$cant1=$rows['nro_denupend'];
			$cant2=$rows['nro_califica'];
			$cant3=$rows['nro_invpreli'];
			$cant4=$rows['nro_invprepa'];
		}
	}
	echo "</table></div>";

echo '</div>'; //idtab2


	$chart="grafico";

	if($_POST['id_fiscal']!="0") {

		echo '<div id="idtab2b" class="tabcontent">';

?>
<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Tp. Caso', 'Cantidad'],
          ['DENUNCIA PENDIENTE', <? echo $cant1; ?>],
          ['CALIFICACION', <? echo $cant2; ?>],
          ['INVESTIGACION PRELIMINAR', <? echo $cant3; ?>],
          ['INVESTIGACION PREPARATORIA', <? echo $cant4; ?>]
        ]);
        var options = {
          title: '<? echo $desdep; ?>\n<? echo $nomfisc; ?>',
          pieSliceText: 'percentage',
          is3D: true,
		  width:800,
	      height:500,
        };
        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
        chart.draw(data, options);
      }
    </script>

  </head>
  <body>
    <div id="piechart_3d" style="width: 900px; height: 500px;"></div>
  </body>
</html>



<!--
                                    <div class="chart-pie pt-4">
                                        <canvas id="<? echo $chart; ?>" height=500></canvas>
                                    </div>
-->



<?
		echo '</div>'; //idtab2b

	}//si es un fiscal muestro grafico pie





	echo "<br><br>";


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
