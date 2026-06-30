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

		<script>
			function f_regresar() {
				document.form.action='ctrl_cons_local.php';
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
	$lati_loca=$result[0]['lati_loca'];
	$long_loca=$result[0]['long_loca'];
	$dire_loca=$result[0]['dire_loca'];

?>



	<center><ht style="color:#073A6B; font-size:1.3em;" ><b>MAPA DEL LOCAL : <? echo $local; ?><br><? echo $dire_loca; ?></b></ht></center>
		<form name="form" method="post">
			<input type=hidden id="codi_loca" name="codi_loca" value="<? echo $codloca; ?>">
			<input type=hidden id="codi_item" name="codi_item" >
			<input type=hidden id="nro_contr" name="nro_contr" >

<?
	$html=new htmlclass;

	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";

?>
	<iframe src="https://www.google.com/maps/embed?pb=!1m10!1m8!1m3!1d479.953399418338!2d<? echo $long_loca; ?>!3d<? echo $lati_loca; ?>!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses!2spe!4v1649783647292!5m2!1ses!2spe" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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