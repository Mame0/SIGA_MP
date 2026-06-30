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
		<title>CONTROL: INFORMACION LOCALES</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

		<script>
			function check_buscar()
			{
				document.form.action='';
				document.form.target="";
				document.form.submit();
			}
			function f_regresar() {

				document.form.action='items_movimientos.php';
				document.form.submit();
			}
			function f_itemservicio() {
				if (document.form.codi_loca.value==0) {
					alert ("SELECCIONE LOCAL");
					return false;
				}
				document.form.action='items_consulta02_itemdepe.php';
				document.form.submit();
			}
			function f_dependencias() {
				if (document.form.codi_loca.value==0) {
					alert ("SELECCIONE LOCAL");
					return false;
				}
				document.form.action='ctrl_cons_depelocal.php';
				document.form.submit();
			}
			function f_fotos() {
				if (document.form.codi_loca.value==0) {
					alert ("SELECCIONE LOCAL");
					return false;
				}
				document.form.action='ctrl_cons_fotos.php';
				document.form.submit();
			}
			function f_vermapa() {
				if (document.form.codi_loca.value==0) {
					alert ("SELECCIONE LOCAL");
					return false;
				}
				document.form.action='ctrl_cons_mapa.php';
				document.form.submit();
			}

		</script>

	</head>
	<body style="margin-bottom: 30px; font-family: sans-serif;">
	<center><ht style="color:#073A6B; font-size:1.3em;" ><b>CONTROL: INFORMACION LOCALES</b></ht></center>
		<form name="form" method="post">

<?
	$html=new htmlclass;


	$arra_options_loca[0]="<- Seleccione ->";
	$result=$Db->query("SELECT * FROM `mp_admi_loca` order by nom1_loca ");
	foreach($result as $rows) {
			$arra_options_loca[$rows['codi_loca']]=$rows['nom1_loca'];
	}


/*
	$arra_options_depe[0]="<- Seleccione ->";
	$result=$Db->query("select distinct mp_admi_depe.codi_depe, mp_admi_depe.nomb_depe
	from mp_maes_item_contr inner join mp_admi_depe on mp_maes_item_contr.codi_depe=mp_admi_depe.codi_depe
	order by nomb_depe ");
	foreach($result as $rows) {
			$arra_options_depe[$rows['codi_depe']]=$rows['nomb_depe'];
	}
*/



//	$result=$Db->query("SELECT * FROM `mp_admi_depe` where codi_depe='".$coddepe."' ");
//	$dependencia=$result[0]['nomb_depe'];

	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("SELECCIONE LOCAL A CONSULTAR");
	echo "</div>";

	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_select("<b>Local</b>",'codi_loca',$arra_options_loca,$_POST['codi_loca'],' onchange="oculta()" ');
	echo '<div style = "width:200px; ">';
	echo $html->put_button_colum("","Mostrar Informaci&oacute;n &raquo;","return check_buscar()");
	echo '</div>';
	echo "</div>";

if(isset($_POST['codi_loca'])) {
	if ($_POST['codi_loca']!=0) {

		$result=$Db->query("SELECT * FROM `mp_admi_loca` where codi_loca='".$_POST['codi_loca']."' ");
		$dire_loca=$result[0]['dire_loca'];
		$ubig_loca=$result[0]['ubig_loca'];
		$lati_loca=$result[0]['lati_loca'];
		$long_loca=$result[0]['long_loca'];
		$nom_loca=$result[0]['nom1_loca'];

		$result=$Db->query("SELECT count(*) as cantdepe FROM `mp_admi_depe` where codi_loca='".$_POST['codi_loca']."' and depe_prin=1 ");
		$cantdepe=$result[0]['cantdepe'];

		$depa_loca="";
		$prov_loca="";
		$dist_loca="";
		if ($ubig_loca!="") {
			$result=$Db->query("SELECT * FROM `ubigeo` where concat(cdep_reni,cpro_reni,cdis_reni)='".$ubig_loca."' ");
			$depa_loca=$result[0]['depa'];
			$prov_loca=$result[0]['prov'];
			$dist_loca=$result[0]['dist'];
		}

	$img="";
	$cantimg=0;
	if (file_exists("galeria/".$_POST['codi_loca'])) {
		$dir = opendir("galeria/".$_POST['codi_loca']);
		while ($elemento = readdir($dir)){
			if( $elemento != "." && $elemento != ".."){
				if( is_dir($path.$elemento) ){
				} else {
					$cantimg++;
					if ($img=="") {
						$img="galeria/" .$_POST['codi_loca']. "/" .$elemento;
					}
				}
			}
		}
	}


		echo"<div style=\"width:90%;max-width:800px;margin:auto;\" id='data_del_local'>";
	?>

		<hr>
		<div class="container-fluid" id="muestra">
			<div class="row">
				<div class="col-xl-8 col-md-6 mb-4">
					<div class="card border-left-primary shadow h-100 py-2">
						<div class="card-body">
							<div class="row no-gutters ">
								<div class="col mr-2">
									<b>Direcci&oacute;n : </b><? echo $dire_loca; ?><br>
									<b>Ubigeo : </b><? echo $ubig_loca; ?><br>
									<? if ($depa_loca!="") {echo "<b>&nbsp;&nbsp;Departamento : </b>".$depa_loca."<br><b>&nbsp;&nbsp;Provincia : </b>".$prov_loca."<br><b>&nbsp;&nbsp;Distrito : </b>".$dist_loca."<br>";} ?>
									<b>Nro. Dependencias : </b><? echo $cantdepe; ?><br>
									<b>Geolocalizaci&oacute;n : </b>
									<a href="javascript:f_vermapa();" style="text-decoration:none;"><? echo "Lat. ".$lati_loca." - Long.".$long_loca; ?></a>
									<br>
								</div>
							</div>
						</div>
					</div>
				</div>
				<? if ($cantimg!=0) { ?>
				<div class="col-xl-4 col-md-6 mb-4">
					<div class="card border-left-primary shadow h-100 py-2">
						<div class="card-body">
							<div class="row no-gutters ">
								<div class="col mr-2">
									<?
									echo '<img src="' .$img. '" class="img-thumbnail" >';
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<? } ?>
			</div>

			<div class="row">

				<div class="col-xl-4 col-md-6 mb-4">
					<div class="card border-left-primary shadow h-100 py-2">
						<div class="card-body">
							<div class="row no-gutters ">
								<a href="javascript:f_itemservicio();" style="text-decoration: none;">
								<div class="col mr-2">
									<div class="text-sm font-weight-bold text-primary text-uppercase mb-1 text-center">
									<table align="center"><tr><td><img src="img/servicios.jpg" width="100px"></td></tr></table>
									<b>SERVICIOS BASICOS</b>
									</div>
								</div>
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-4 col-md-6 mb-4">
					<div class="card border-left-primary shadow h-100 py-2">
						<div class="card-body">
							<div class="row no-gutters ">
								<a href="javascript:f_dependencias();" style="text-decoration: none;">
								<div class="col mr-2">
									<div class="text-sm font-weight-bold text-primary text-uppercase mb-1 text-center">
									<table align="center"><tr><td><img src="img/oficina.png" width="100px"></td></tr></table>
									<b>DEPENDENCIAS</b>
									</div>
								</div>
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-4 col-md-6 mb-4">
					<div class="card border-left-primary shadow h-100 py-2">
						<div class="card-body">
							<div class="row no-gutters ">
								<a href="javascript:f_fotos();" style="text-decoration: none;">
								<div class="col mr-2">
									<div class="text-sm font-weight-bold text-primary text-uppercase mb-1 text-center">
									<table align="center"><tr><td><img src="img/foto.png" width="100px"></td></tr></table>
									<b>FOTOS (<? echo $cantimg; ?>)</b>
									</div>
								</div>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	<?
		echo"</div>";
	}
}



/*
		echo $html->put_separator_demand("30");
		echo "
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_regresar()\"> <- Regresar</button>
                                        </div>
                                </div>
                        </div>
                ";
*/

?>

<div id='cargadorvacio'></div>
<center>
	</form>
	</body>
</html>

<script text="text/javascript">
	$(".chosen").chosen();
</script>


<script>
function oculta() {
	document.getElementById('data_del_local').style.display = "none";
}
</script>



