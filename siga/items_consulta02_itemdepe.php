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
		<title>SERVICIOS BASICOS POR LOCAL</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

		<script>
			function f_regresar() {

				document.form.action='ctrl_cons_local.php';
				document.form.submit();
			}
			function f_verdetalle(cd,ci,nc)
			{
				document.form.codi_loca.value=cd;
				document.form.codi_item.value=ci;
				document.form.nro_contr.value=nc;
				document.form.action='items_consulta03_detalle.php';
				document.form.submit();
			}
		</script>

	</head>
	<body style="margin-bottom: 30px; font-family: sans-serif;">
	<center><ht style="color:#073A6B; font-size:1.3em;" ><b>SERVICIOS BASICOS POR LOCAL</b></ht></center>
		<form name="form" method="post">
			<input type=hidden id="codi_loca" name="codi_loca" value="<? echo $_POST['codi_loca']; ?>">
			<input type=hidden id="codi_item" name="codi_item" >
			<input type=hidden id="nro_contr" name="nro_contr" >

<?
	$html=new htmlclass;

if($_POST['codi_loca']!=0) {  //genera
	$codloca=$_POST['codi_loca'];

	$result=$Db->query("SELECT * FROM `mp_admi_loca` where codi_loca='".$codloca."' ");
	$local=$result[0]['nom1_loca'];



	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("SERVICIOS BASICOS CONTRATADOS POR : ". $local );


?>
	<div class="container-fluid" id="muestra">
		<div class="row">
<?
	$result_pagi=$Db->query("SELECT mp_maes_item_contr.*, mp_maes_item.x_nombre
	FROM `mp_maes_item_contr` inner join mp_maes_item on `mp_maes_item_contr`.codi_item=mp_maes_item.n_codigo
	where codi_loca='".$codloca."' order by x_nombre, nro_contr");

	$cantserv=0;
	foreach($result_pagi as $rows) {
	$cod_loca=$rows['codi_loca'];
	$cod_item=$rows['codi_item'];
	$nro_cont=$rows['nro_contr'];
	$cantserv++;
?>
			<div class="col-xl-4 col-md-6 mb-4">
				<div class="card border-left-primary shadow h-100 py-2">
					<div class="card-body">
						<div class="row no-gutters ">
							<a href="javascript:f_verdetalle(<? echo $cod_loca; ?>,<? echo $cod_item; ?>,'<? echo $nro_cont; ?>');" style="text-decoration: none;">
							<div class="col mr-2">
								<div class="text-sm font-weight-bold text-primary text-uppercase mb-1 text-center">
								<b><? echo $rows['x_nombre']; ?></b>
								</div>
								<div class="text-xs font-weight-bold text-danger text-center"><b>Contrato : <? echo $rows['nro_contr']; ?></b></div>
							</div>
							</a>
						</div>
					</div>
				</div>
			</div>

<?

	}//foreach

	if ($cantserv==0) {
?>
			<div class="text-sm font-weight-bold text-primary text-uppercase mb-1 text-center">
			<b>ESTE LOCAL NO TIENE REGISTRADO NINGUN SERVICIO BASICO CONTRATADO</b>
			</div>
<?
	}


?>
		</div>
	</div>

<?

	echo"</div>";


}//genera


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


?>

<div id='cargadorvacio'></div>
<center>
	</form>
	</body>
</html>