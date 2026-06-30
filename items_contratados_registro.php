<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");

	if($_POST['guardar_item'])
	{
		$fdig=date(YmdHis);
		if($_POST['codi_auto'])
		{
			$result=$Db->update('mp_maes_item_contr',['codi_item'=>$_POST['codi_item'],'codi_loca'=>$_POST['codi_loca'],
			'nro_contr'=>$_POST['nro_contr'],'fech_inic'=>$_POST['fech_inic'],
			'acti_esta'=>$_POST['acti_esta'] ]  , ['codi_auto'=>$_POST['codi_auto']]  );
		}
		else
		{
			$result=$Db->insert('mp_maes_item_contr',['codi_item'=>$_POST['codi_item'],'codi_loca'=>$_POST['codi_loca'],
			'nro_contr'=>$_POST['nro_contr'],'fech_inic'=>$_POST['fech_inic'],
			'acti_esta'=>$_POST['acti_esta'] ]);
			$_POST['codi_auto']=$Db->lastInsertId();
		}

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"items_contratados.php\">
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
	$result_documento=$Db->select('mp_maes_item_contr', ['codi_auto'=>$_POST['codi_auto']], '', '', '');
	$_POST['codi_item']=$result_documento[0]['codi_item'];
	$_POST['codi_loca']=$result_documento[0]['codi_loca'];
	$_POST['nro_contr']=$result_documento[0]['nro_contr'];
	$_POST['fech_inic']=$result_documento[0]['fech_inic'];
	$_POST['acti_esta']=$result_documento[0]['acti_esta'];

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>ITEMS CONTRATADOS</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		<script>
			function f_guardar()
			{
					if(document.form.codi_item.value==0) {
						alert('Seleccione item a contratar');
						document.form.codi_item.focus();
						return false;
					}
					if(document.form.codi_loca.value==0) {
						alert('Seleccione local');
						document.form.codi_loca.focus();
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
				document.form.action='items_contratados.php';
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

	$arra_options_loca[0]="<- Seleccione ->";
	$result=$Db->select('mp_admi_loca', '', '', '', ['nom1_loca'=>'ASC']);
	foreach($result as $rows)
			$arra_options_loca[$rows['codi_loca']]=$rows['nom1_loca'];

	$arra_options_item[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_item', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
			$arra_options_item[$rows['n_codigo']]=$rows['x_nombre'];


	if($_POST['codi_auto'])
		//echo"Editar Informaci&oacute;n Exp. ".$_POST['expe_docu'];
		echo"Editar Informaci&oacute;n - Item Contratado : <i>" . $arra_options_item[$_POST['codi_item']] . "</i>";
	else
		echo"REGISTRAR NUEVOS ITEMS CONTRATADOS";
?>
	</h2></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_item">
			<input type=hidden name="codi_auto" value="<?=$_POST['codi_auto']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">

<?
	$html=new htmlclass;

	echo "<main style='column-count:2;'>";
	echo $html->put_select("Item&nbsp;/&nbsp;Servicio",'codi_item',$arra_options_item,$_POST['codi_item'],'');
	echo $html->put_select("Local",'codi_loca',$arra_options_loca,$_POST['codi_loca'],'');
	echo "</main>";

	echo "<main style='column-count:3;'>";
	echo $html->put_text('text',"Nro&nbsp;Contrato","",'nro_contr',$_POST['nro_contr'],'','20','');
	echo $html->put_text('date',"Fec.&nbsp;inicio&nbsp;contrato","",'fech_inic',$_POST['fech_inic'],'','10','');
	echo $html->put_select_estado(CONST_SUBTITLE_STATE,'acti_esta',$_POST['acti_esta'],CONST_OPTION_ENABLE,CONST_OPTION_DISABLE);
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
