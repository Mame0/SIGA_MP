<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");
	
	if($_POST['guardar_personal'])
	{
		$fdig=date(YmdHis);
		if($_POST['codi_comp'])
		{
			$result=$Db->update('mp_',['nomb_docu'=>$_POST['nomb_docu'],'expe_docu'=>$_POST['expe_docu'],'codi_espe'=>$_POST['codi_espe'],'esta_docu'=>$_POST['esta_docu'],'digi_docu'=>$_SESSION['iden_oper'],'fdig_docu'=>"$fdig"],['codi_docu'=>$_POST['codi_docu']]);
		}
		else
		{
			$result=$Db->insert('mp_comp_compras',[
			    'nomb_comp'=>$_POST['nomb_comp'],
			    'inic_comp'=>$_POST['inic_comp'],
			    'fina_comp'=>$_POST['fina_comp'],
			    'codi_rubr'=>$_POST['codi_rubr'],
			    'flag_mail'=>$_POST['flag_mail'],
			    'esta_comp'=>$_POST['esta_comp'],
			    'digi_comp'=>$_SESSION['iden_oper'],
			    'fdig_comp'=>"$fdig"
			]);
			$_POST['codi_comp']=$Db->lastInsertId();
		}
		if($_FILES['file_docu']['name'] AND $_FILES['file_docu']['size']>0)
		{
			if(strstr($_FILES['file_docu']['type'],"pdf") OR strstr($_FILES['file_docu']['type'],"pdf"))
			{
				//subir_archivo('logo',$_FILES['file_docu']['tmp_name'],"pers_".str_pad($_POST['codi_pers'], 6, "0", STR_PAD_LEFT).".pdf","");
				move_uploaded_file($_FILES['file_docu']['tmp_name'],"ftp/comp_".str_pad($_POST['codi_comp'], 6, "0", STR_PAD_LEFT).".pdf");
			}
			else
				echo"<script>alert('ERROR: Archivo no es un PDF');</script>";
		}
		unset($_POST['file_docu']);

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"compras_nueva.php\">
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
	$result_comp=$Db->select('mp_comp_compras', ['codi_comp'=>$_POST['codi_comp']], '', '', '');
	$_POST['nomb_comp']=$result_comp[0]['nomb_comp'];
	$_POST['inic_comp']=$result_comp[0]['inic_comp'];
	$_POST['fina_comp']=$result_comp[0]['fina_comp'];
	$_POST['codi_rubr']=$result_comp[0]['codi_rubr'];
	$_POST['flag_mail']=$result_comp[0]['flag_mail'];
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
				if(document.form.codi_rubr.selectedIndex=='0')
				{
					alert('Seleccione Rubro');
					document.form.codi_rubr.focus();
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
						if(document.form.inic_comp.value=='')
						{
							alert('Ingrese Fecha Inicial');
							document.form.inic_comp.focus();
							return false;
						}
						else
						{
						    if(document.form.fina_comp.value=='')
					        {
					            alert('Seleccione Fecha Final');
					            document.form.fina_comp.focus();
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
				document.form.action='compras_vigentes.php';
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
		echo "<main><div style=\"display:block;column-span:all;\">".$_POST['nomb_comp']."</div></main>";
		
							
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

	$arra_options_rubro[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_comp_rubro', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_rubro[$rows['n_codigo']]=$rows['x_nombre'];
	
	$arra_options_notif[0]="No";
	$arra_options_notif[1]="Si";

	echo $html->put_title_demand("Información Importante");
	echo $html->put_text('text',"Desde","Ingrese Fecha de Inicio",'inic_comp',$_POST['inic_comp'],'','20','disabled');
	echo $html->put_text('text',"Hasta","Ingrese Fecha de Fin",'fina_comp',$_POST['fina_comp'],'','20','disabled');
	echo $html->put_text('text',"Rubro","Ingrese Rubro",'codi_rubr',$arra_options_rubro[$_POST['codi_rubr']],'','20','disabled');
	echo $html->put_title_demand("Ingrese Propuesta");
	echo $html->put_text('text',"Precio","Ingrese Precio",'inic_comp',$_POST['xxx'],'','20','');
	echo $html->put_text('number',"Plazo&nbsp;de&nbsp;entrega&nbsp;(días)","Ingrese Nro. Días",'fina_comp',$_POST['fina_comp'],'','20','');
	echo $html->put_select("Precio&nbsp;Incluye&nbsp;IGV?",'flag_mail',$arra_options_notif,$_POST['flag_mail'],"");
	echo $html->put_select("Incluye&nbsp;Transporte?",'flag_mail',$arra_options_notif,$_POST['flag_mail'],"");
	//echo $html->put_select("Plazo de entrega (días)",'codi_rubr',$arra_options_rubro,$_POST['codi_rubr'],"");
	echo $html->put_upload_file("Subir&nbsp;Propuesta",'file_docu','','');
	echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_comp',$_POST['esta_comp'],CONST_OPTION_ENABLE,CONST_OPTION_DISABLE);
	//echo $html->put_title_demand("Sumilla");
	
	//echo"</main><main>";
	//echo $html->put_title_demand("Subir TDR");
	//echo $html->put_upload_file("",'file_docu','','');
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
                                                <button class=\"button_foot\" onclick=\"return f_guardar()\">Enviar &raquo;</button>
                                        </div>
                                </div>
                        </div>
                ";
?>
<center>
	</form>
	</body>
</html>
