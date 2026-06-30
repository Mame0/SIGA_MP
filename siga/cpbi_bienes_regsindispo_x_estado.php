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
		<title>ESTADISTICA DE BIENES INCAUTADOS POR UBICACION</title>
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
	<center><h2 style="color:#073A6B">REGISTROS SIN DISPOSICION POR ESTADO DE CARPETA FISCAL</h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_bien">
<?
	$html=new htmlclass;




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
		}
	}


	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("REGISTROS SIN DISPOSICION POR ESTADO DE CARPETA FISCAL");

	$head["1"]="Dependencia";
	$colgra=0;
	$result_anno=$Db->query("SELECT distinct anno_regi
	FROM `mp_cpbi_bienes` where codi_disp=0
	order by anno_regi");
	foreach($result_anno as $rows_aa) {
		$colgra++;
		$anno=$rows_aa['anno_regi'];
		$losanos[$colgra-1]=$anno;

		$colu=($colgra*3)-1;
		$head["$colu"]="ARCH.<br>".$anno;

		$colu=($colgra*3);
		$head["$colu"]="SENT.<br>".$anno;

		$colu=($colgra*3)+1;
		$head["$colu"]="Otros<br>".$anno;
	}
	echo $html->put_table_responsive_open();
	echo $html->put_table_responsive_header($head);
	for ($dep=1;$dep<=$cantdep;$dep++) {
		$coddep=$lasdep[$dep][1];
		$desdep=$lasdep[$dep][2];

		$condadd="";
		if ($coddep==0) {
			$data['1']="<b>DEPENDENCIA NO ASIGNADA</b>";
		} else {
			$data['1']=utf8_encode($desdep);//1;//$ubi;

			$respad=$Db->query("SELECT * FROM mp_admi_depe where codi_padr=".$coddep." ");
			foreach($respad as $rows_pa) {
				$condadd.=" or codi_depe=".$rows_pa['codi_depe']." ";
			}
		}
		for ($ano=1;$ano<=$colgra;$ano++) {
			$anno=$losanos[$ano-1];

			$result_esta=$Db->query("SELECT codi_epro, count( * ) AS cant FROM `mp_cpbi_bienes` where (codi_depe=".$coddep." ".$condadd.") and codi_disp=0 and anno_regi='".$anno."' and codi_epro=1 ");
			$cant=$result_esta[0]['cant'];
			$colu=($ano*3)-1;
			$data["$colu"]=$cant;

			$result_esta=$Db->query("SELECT codi_epro, count( * ) AS cant FROM `mp_cpbi_bienes` where (codi_depe=".$coddep." ".$condadd.") and codi_disp=0 and anno_regi='".$anno."' and codi_epro=8 ");
			$cant=$result_esta[0]['cant'];
			$colu=($ano*3);
			$data["$colu"]=$cant;

			$result_esta=$Db->query("SELECT codi_epro, count( * ) AS cant FROM `mp_cpbi_bienes` where (codi_depe=".$coddep." ".$condadd.") and codi_disp=0 and anno_regi='".$anno."' and (codi_epro<>1 and codi_epro<>8) ");
			$cant=$result_esta[0]['cant'];
			$colu=($ano*3)+1;
			$data["$colu"]=$cant;
		}
		echo $html->put_table_responsive_data($head,$data);
	}
	echo $html->put_table_responsive_close();

	echo"</div>";


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
