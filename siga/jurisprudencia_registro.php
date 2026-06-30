<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");
	
	if($_POST['guardar_personal'])
	{
		$fdig=date(YmdHis);
		if($_POST['codi_docu'])
		{
			$result=$Db->update('mp_jurisprudencia_documento',['nomb_docu'=>$_POST['nomb_docu'],'expe_docu'=>$_POST['expe_docu'],'codi_espe'=>$_POST['codi_espe'],'esta_docu'=>$_POST['esta_docu'],'digi_docu'=>$_SESSION['iden_oper'],'fdig_docu'=>"$fdig"],['codi_docu'=>$_POST['codi_docu']]);
		}
		else
		{
			$result=$Db->insert('mp_jurisprudencia_documento',['nomb_docu'=>$_POST['nomb_docu'],'expe_docu'=>$_POST['expe_docu'],'codi_espe'=>$_POST['codi_espe'],'esta_docu'=>$_POST['esta_docu'],'digi_docu'=>$_SESSION['iden_oper'],'fdig_docu'=>"$fdig"]);
			$_POST['codi_docu']=$Db->lastInsertId();
		}
		if($_FILES['file_docu']['name'] AND $_FILES['file_docu']['size']>0)
		{
			if(strstr($_FILES['file_docu']['type'],"pdf") OR strstr($_FILES['file_docu']['type'],"pdf"))
			{
				//subir_archivo('logo',$_FILES['file_docu']['tmp_name'],"pers_".str_pad($_POST['codi_pers'], 6, "0", STR_PAD_LEFT).".pdf","");
				move_uploaded_file($_FILES['file_docu']['tmp_name'],"ftp/docu_".str_pad($_POST['codi_docu'], 6, "0", STR_PAD_LEFT).".pdf");
			}
			else
				echo"<script>alert('ERROR: Archivo no es un PDF');</script>";
		}
		unset($_POST['file_docu']);

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"jurisprudencia_buscar.php\">
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
	$result_documento=$Db->select('mp_jurisprudencia_documento', ['codi_docu'=>$_POST['codi_docu']], '', '', '');
	$_POST['nomb_docu']=$result_documento[0]['nomb_docu'];
	$_POST['expe_docu']=$result_documento[0]['expe_docu'];
	$_POST['codi_espe']=$result_documento[0]['codi_espe'];
	$_POST['esta_docu']=$result_documento[0]['esta_docu'];
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
				if(document.form.codi_espe.selectedIndex=='0')
				{
					alert('Seleccione Especialidad');
					document.form.codi_espe.focus();
					return false;
				}
				else
				{
					//if(document.form.expe_docu.value=='')
					//{
					//	alert('Ingrese Nro. expediente');
					//	document.form.expe_docu.focus();
					//	return false;
					//}
					//else
					//{
						if(document.form.nomb_docu.value=='')
						{
							alert('Ingrese Sumilla');
							document.form.nomb_docu.focus();
							return false;
						}
						else
						{
						    if(document.form.file_docu.value=='')
					        {
					            alert('Seleccione Archivo PDF');
					            document.form.file_docu.focus();
					            return false;
					        }
					        else
					        {
					    		if(confirm('Seguro que desea Guardar'))
					    		{
					    			document.form.guardar_personal.value='1';
					    			document.form.submit();
					    		}
					    		else
					    			return false;
						    }
						}
					//}
				}
			}
			function f_cancelar()
			{
				document.form.action='jurisprudencia_buscar.php';
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
	if($_POST['codi_docu'])
		echo"Editar Informaci&oacute;n Exp. ".$_POST['expe_docu'];
	else
		echo"Crear Nuevo Documento";
?>
	</h2></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="codi_docu" value="<?=$_POST['codi_docu']?>">
			<input type=hidden name="busq_tipo" value="<?=$_POST['busq_tipo']?>">
			<input type=hidden name="busq_dato" value="<?=$_POST['busq_dato']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">
			<main>
<?
	$html=new htmlclass;


	$arra_options_depe[0]="<- Seleccione ->";
	$result=$Db->select('ali_maes_pers_dependencia', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_depe[$rows['n_codigo']]=$rows['x_nombre'];

	$arra_options_carg[0]="<- Seleccione ->";
	$result=$Db->select('ali_maes_pers_cargo', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_carg[$rows['n_codigo']]=$rows['x_nombre'];

	$arra_options_moda[0]="<- Seleccione ->";
	$result=$Db->select('ali_maes_pers_modalidad', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_moda[$rows['n_codigo']]=$rows['x_nombre'];

	$arra_options_espe[0]="<- Seleccione ->";
        $result=$Db->select('mp_maes_jurisprudencia_especialidad', '', '', '', ['x_nombre'=>'ASC']);
        foreach($result as $rows)
                $arra_options_espe[$rows['n_codigo']]=$rows['x_nombre'];

	echo $html->put_title_demand("Informaci&oacute;n B&aacute;sica");
	echo $html->put_select("Especialidad",'codi_espe',$arra_options_espe,$_POST['codi_espe'],"");
	echo $html->put_text('text',"Nro.&nbsp;Expediente","Ingrese Nro. Expediente",'expe_docu',$_POST['expe_docu'],'','20','');
	echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_docu',$_POST['esta_docu'],CONST_OPTION_ENABLE,CONST_OPTION_DISABLE);
	echo $html->put_title_demand("Sumilla");
	echo $html->put_textarea("",'nomb_docu',$_POST['nomb_docu'],'style="height: 100px;"');
	echo"</main><main>";
	echo $html->put_title_demand("Subir Archivo");
	echo $html->put_upload_file("",'file_docu','','');
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
