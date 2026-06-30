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
		<title>REGISTROS POR TIPO DE DISPOSICION POR A&Ntilde;O</title>
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
				var cods=document.form.todo_disp.value;
				var myarr = cods.split("|");
				var coddis=0;
				var cods_disp="";
				for (x=0;x<myarr.length;x++) {
					coddis=myarr[x];
					if (document.getElementById('chk'+coddis).checked) {
						cods_disp=cods_disp+coddis+"|";
					}
				}
				document.getElementById('cods_disp').value=cods_disp;
			}
		</script>


	</head>
	<body style="margin-bottom: 30px;">
	<center><h2 style="color:#073A6B">REGISTROS POR TIPO DE DISPOSICION POR A&Ntilde;O</h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_bien">
<?
	$html=new htmlclass;
	$condadd="";

	$tododis="0";
	$cantdis=1;

	$lasdis[$cantdis][1]=0;
	$lasdis[$cantdis][2]="SIN DISPOSICION";
	$result_disp=$Db->query("SELECT * FROM mp_maes_tpdisposicion ");
	foreach($result_disp as $rows_dp) {
		$cantdis++;
		$lasdis[$cantdis][1]=$rows_dp['n_codigo'];
		$lasdis[$cantdis][2]=$rows_dp['x_nombre'];
		if ($tododis!="") {$tododis.="|";}
		$tododis.=$rows_dp['n_codigo'];
	}

echo"<main style='column-count:1;'>";
echo $html->put_title_demand("FILTRAR POR DISPOSICIONES");
echo '<div class="row" style="height:150px; overflow: scroll;">';

$loscod=explode("|",$_POST['cods_disp']);
$cantcod=count($loscod);
for ($dis=1;$dis<=$cantdis;$dis++) {
	$coddis=$lasdis[$dis][1];
	$desdis=utf8_encode( $lasdis[$dis][2] );
	$add="";
	for ($x=1;$x<=$cantcod;$x++) {
		if ($coddis==$loscod[$x-1]) {
			$add=" checked='checked' ";
		}
	}
	echo '<div style="float: left; width: 250px;">';
    echo '<div class="checkbox" style="font-size: 12px;">
      <label style="padding:5px 5px 5px 5px;"><input type="checkbox" id="chk'.$coddis.'" name="chk'.$coddis.'" value="'.$coddis.'" '.$add.'> '.$desdis.'</label>
    </div>';
    echo '</div>';
}
echo '</div>';
echo"</main>";


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
	echo $html->put_select("Dependencia/Fiscalia",'codi_depe',$arra_options_depe,$_POST['codi_depe']," style='max-width:800px;' ");
	echo"</main>";

	echo"<main style='column-count:2;'>";
	echo $html->put_select("Distrito&nbsp;Fiscal",'n_codigo',$arra_options_distfiscal,$_POST['n_codigo']," style='max-width:600px;' ");
	echo $html->put_select("Fiscal",'iden_pers',$arra_options_fisc,$_POST['iden_pers'],'style="max-width:600px;"');
	echo"</main>";




echo"<main style='column-count:3;'>";
	echo $html->put_button_colum("","Generar Consulta &raquo;","return check_buscar()");
echo"</main>";


echo "<input type='hidden' id='todo_disp' name='todo_disp' value='".$tododis."'>";
echo "<input type='hidden' id='cods_disp' name='cods_disp' value='".$_POST['cods_disp']."'>";


if(isset($_POST['cods_disp'])) {  //genera grafico
	$distfisc=$_POST['n_codigo'];
	$codfisca=$_POST['iden_pers'];
	$coddep=$_POST['codi_depe'];

	$condadd="";
	for ($dis=1;$dis<=$cantdis;$dis++) {
		$coddis=$lasdis[$dis][1];
		$desdis=$lasdis[$dis][2];
		for ($x=1;$x<=$cantcod;$x++) {
			if ($coddis==$loscod[$x-1]) {
				if ($condadd!="") {
					$condadd.=" or codi_disp='".$coddis."' ";
				} else {
					$condadd.=" codi_disp='".$coddis."' ";
				}
			}
		}
	}
	$condadd=" where (" . $condadd . ") ";

	$condadd2="";

		if ($coddep!=0) {
			$condadd.=" and codi_depe=".$coddep." ";
			$condadd2.=" and codi_depe=".$coddep." ";
		}

		if ($distfisc!=0) {
			$condadd.=" and codi_distfiscal=".$distfisc." ";
			$condadd2.=" and codi_distfiscal=".$distfisc." ";
		}
		if ($codfisca!=0) {
			$condadd.=" and codi_fisc=".$codfisca." ";
			$condadd2.=" and codi_fisc=".$codfisca." ";
		}


	$cantdisp=0;
	$result_disp=$Db->query("SELECT distinct mp_cpbi_bienes.codi_disp, mp_maes_tpdisposicion.x_nombre as desc_disp
	FROM `mp_cpbi_bienes` LEFT JOIN mp_maes_tpdisposicion ON `mp_cpbi_bienes`.codi_disp = mp_maes_tpdisposicion.n_codigo ".$condadd." order by codi_disp");
	foreach($result_disp as $rows_dp) {
		$cantdisp++;
		$lasdisp[$cantdisp][1]=$rows_dp['codi_disp'];
		$lasdisp[$cantdisp][2]=$rows_dp['desc_disp'];
	}

	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("REGISTROS POR TIPO DE DISPOSICION POR A&Ntilde;O");

	$head["1"]="TP.REGISTRO";
	$colgra=0;
	$result_anno=$Db->query("SELECT distinct anno_regi
	FROM `mp_cpbi_bienes` where codi_disp=0 ".$condadd2."
	order by anno_regi");
	foreach($result_anno as $rows_aa) {
		$colgra++;
		$anno=$rows_aa['anno_regi'];
		$losanos[$colgra-1]=$anno;

		$colu=$colgra+1;
		$head["$colu"]="Registros<br>".$anno;
	}
	echo $html->put_table_responsive_open();
	echo $html->put_table_responsive_header($head);
	for ($disp=1;$disp<=$cantdisp;$disp++) {
		$coddisp=$lasdisp[$disp][1];
		$desdisp=$lasdisp[$disp][2];
		if ($coddisp==0) {
			$data['1']="<b>S/DISPOSICION</b>";
		} else {
			$data['1']=utf8_encode($desdisp);//1;//$ubi;
		}
		for ($ano=1;$ano<=$colgra;$ano++) {
			$anno=$losanos[$ano-1];
			$result_esta=$Db->query("SELECT codi_epro, count( * ) AS cant FROM `mp_cpbi_bienes` where codi_disp=".$coddisp." and anno_regi='".$anno."' ".$condadd2." ");
			$cant=$result_esta[0]['cant'];
			$colu=$ano+1;
			$data["$colu"]=$cant;
		}
		echo $html->put_table_responsive_data($head,$data);
	}
	echo $html->put_table_responsive_close();

	echo"</div>";

}  //genera grafico

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
