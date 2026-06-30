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
		<title>FOTOS DEL LOCAL</title>
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


<?
if($_POST['codi_loca']!=0) {  //genera
	$codloca=$_POST['codi_loca'];

	$result=$Db->query("SELECT * FROM `mp_admi_loca` where codi_loca='".$codloca."' ");
	$local=$result[0]['nom1_loca'];
?>



	<center><ht style="color:#073A6B; font-size:1.3em;" ><b>FOTOS DEL LOCAL : <? echo $local; ?></b></ht></center>
		<form name="form" method="post">
			<input type=hidden id="codi_loca" name="codi_loca" value="<? echo $codloca; ?>">
			<input type=hidden id="codi_item" name="codi_item" >
			<input type=hidden id="nro_contr" name="nro_contr" >

<?
	$html=new htmlclass;




	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";


?>
	<div class="container-fluid" id="muestra">
		<div class="row">

<?
	$cantimg=0;

	if (file_exists("galeria/".$codloca)) {

		$dir = opendir("galeria/".$codloca);
		while ($elemento = readdir($dir)){
			if( $elemento != "." && $elemento != ".."){
				if( is_dir($path.$elemento) ){
				} else {
					$cantimg++;
					echo '<img src="galeria/' .$codloca. "/" .$elemento. '" class="img-thumbnail" width="304" height="236">';
				}
			}
		}
	}
	if ($cantimg==0) {
?>
			<div class="text-sm font-weight-bold text-primary text-uppercase mb-1 text-center">
			<b>NO HAY FOTOS ALMACENADAS DE ESTE LOCAL</b>
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