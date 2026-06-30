<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");

	if($_POST['guardar_item'])
	{
		$fdig=date(YmdHis);
		if($_POST['n_codigo'])
		{
			$result=$Db->update('mp_maes_item',['x_nombre'=>$_POST['x_nombre'],
			'n_estado'=>$_POST['n_estado'] ]  , ['n_codigo'=>$_POST['n_codigo']]  );
		}
		else
		{
			$result=$Db->insert('mp_maes_item',['x_nombre'=>$_POST['x_nombre'],
			'n_estado'=>$_POST['n_estado'] ]);
			$_POST['n_codigo']=$Db->lastInsertId();
		}

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"items_servicios.php\">
					<input type=hidden name=\"busq_tipo\" value=\"".$_POST['busq_tipo']."\">
					<input type=hidden name=\"busq_dato\" value=\"".$_POST['busq_dato']."\">
					<input type=hidden name=\"busq_pagi_actu\" value=\"".$_POST['busq_pagi_actu']."\">
                                </form>
                                <script>
                                        document.form.submit();
                                </script>
                        </body></html>
		";

	}
	$result_documento=$Db->select('mp_maes_item', ['n_codigo'=>$_POST['n_codigo']], '', '', '');
	$_POST['x_nombre']=$result_documento[0]['x_nombre'];
	$_POST['n_estado']=$result_documento[0]['n_estado'];

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>ITEMS / SERVICIOS</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		<script>
			function f_guardar()
			{
					if(document.form.x_nombre.value=='')
					{
						alert('Ingrese item / servicio');
						document.form.x_nombre.focus();
						return false;
					}

					if(confirm('Seguro que desea Guardar'))
					{
						document.form.guardar_item.value='1';
						document.form.submit();
					}
					else
						return false;
			}
			function f_cancelar()
			{
				document.form.action='items_servicios.php';
				document.form.submit();
			}
			function ajustar_altura()
                        {
                                parent.document.getElementById('body_iframe').height=parent.window.innerHeight-80;
                        }
                        ajustar_altura();
		</script>
	</head>
	<body style="margin-bottom: 30px;">
	<center><h2 style="color:#073A6B">
<?
	if($_POST['n_codigo'])
		//echo"Editar Informaci&oacute;n Exp. ".$_POST['expe_docu'];
		echo"Editar Informaci&oacute;n - Item / Sercicio : <i>" .$_POST['x_nombre'] . "</i>";
	else
		echo"REGISTRAR NUEVOS ITEMS / SERVICIOS";
?>
	</h2></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_item">
			<input type=hidden name="n_codigo" value="<?=$_POST['n_codigo']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">

<?
	$html=new htmlclass;

	echo "<main style='column-count:2;'>";
	echo $html->put_text('text',"Item&nbsp;/&nbsp;Servicio","Descripci&oacute;n",'x_nombre',$_POST['x_nombre'],'','50','');
	echo $html->put_select_estado(CONST_SUBTITLE_STATE,'n_estado',$_POST['n_estado'],CONST_OPTION_ENABLE,CONST_OPTION_DISABLE);
	echo "</main>";

	echo $html->put_separator_demand("30");

                echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_cancelar()\">&laquo; Cancelar</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"return f_guardar()\">Guardar &raquo;</button>
                                        </div>
                                </div>
                        </div>
                ";
?>
<center>
	</form>
	</body>
</html>
