<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");

	if($_POST['guardar_personal'])
	{
		$fdig=date(YmdHis);

		$result=$Db->update('mp_comp_compras',['fina_comp'=>$_POST['fina_comp']],['codi_comp'=>$_POST['codi_comp']]);

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"compras_seguimiento.php\">
					<input type=hidden name=\"busq_tipo\" value=\"".$_POST['busq_tipo']."\">
					<input type=hidden name=\"busq_dato\" value=\"".$_POST['busq_dato']."\">
					<input type=hidden name=\"busq_pagi_actu\" value=\"".$_POST['busq_pagi_actu']."\">
                                </form>
                                <script>
                                        document.form.submit();
                                </script>
                        </body></html>
		";

	exit();
	}
	$result_documento=$Db->select('mp_comp_compras', ['codi_comp'=>$_POST['codi_comp']], '', '', '');
	$_POST['nomb_comp']=$result_documento[0]['nomb_comp'];
	$_POST['inic_comp']=$result_documento[0]['inic_comp'];
	$_POST['fina_comp']=$result_documento[0]['fina_comp'];
	$_POST['codi_rubr']=$result_documento[0]['codi_rubr'];


?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>SIOJAlimentos</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		<script>
			function f_guardar()
			{
					if(document.form.fina_comp.value=='')
					{
						alert('Ingrese nueva fecha de vigencia');
						document.form.fina_comp.focus();
						return false;
					}

					if(confirm('SE VA AMPLIAR LA VIGENCIA DE ESTA COMPRA, ESTA SEGURO QUE DESEA GUARDAR?'))
					{
						document.form.guardar_personal.value='1';
						document.form.submit();
					}
					else
						return false;
			}
			function f_cancelar()
			{
				document.form.action='compras_seguimiento.php';
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
	if($_POST['codi_comp'])
		echo"AMPLIAR FECHA DE VIGENCIA PARA PROCESO DE COMPRA";
	else
		echo"-";
?>
	</h2></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="codi_comp" value="<?=$_POST['codi_comp']?>">
<?
	$html=new htmlclass;

	$arra_options_rubro[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_comp_rubro', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_rubro[$rows['n_codigo']]=$rows['x_nombre'];

	$arra_options_notif[0]="No";
	$arra_options_notif[1]="Si";


	echo "<main style='column-count:1;'>";
	echo $html->put_title_demand("Ampliaci&oacute;n de Vigencia de Proceso de Compra");
	echo $html->put_info("Nombre",$_POST['nomb_comp']);
	echo $html->put_info("Rubro",$arra_options_rubro[$_POST['codi_rubr']]);
	echo "</main>";

	echo "<main style='column-count:2;'>";
	echo $html->put_title_demand("Fechas de Vigencia");
	echo $html->put_info("Vigencia&nbsp;Inicia",$_POST['inic_comp']);
	echo $html->put_info("Vigencia&nbsp;Finaliza",$_POST['fina_comp']);
	echo "</main>";

	echo "<main style='column-count:2;'>";
	echo $html->put_text('date',"Nueva&nbsp;Fecha&nbsp;Vigencia","",'fina_comp',$_POST['fina_comp'],'','20','');

/*
	echo $html->put_title_demand("Ingrese Informacion Importante");
	echo $html->put_select("Rubro",'codi_rubr',$arra_options_rubro,$_POST['codi_rubr'],"");
	echo $html->put_select("Notificar&nbsp;Correo",'flag_mail',$arra_options_notif,$_POST['flag_mail'],"");
	echo $html->put_text('date',"Desde","Ingrese Nro. Expediente",'inic_comp',$_POST['inic_comp'],'','20','');
	echo $html->put_text('date',"Hasta","Ingrese Nro. Expediente",'fina_comp',$_POST['fina_comp'],'','20','');
	echo $html->put_upload_file("Subir&nbsp;TDR",'file_docu','','');
	echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_comp',$_POST['esta_comp'],CONST_OPTION_ENABLE,CONST_OPTION_DISABLE);
*/
	echo"</main>";

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
