<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");
	
	if(!$_POST['iden_pers'])
	{
	    $result=$Db->query("select * from mp_admi_pers where ndoc_pers='$_SESSION[ndoc_oper]'");
        foreach($result as $rows)
            $_POST['iden_pers']=$rows['iden_pers'];
	}

	if($_POST['guardar_personal'])
	{
		$fdig=date(YmdHis);
		//$_POST['esta_pers']=1;
		if($_POST['iden_pers'])
		{
			$result=$Db->update('mp_admi_pers',['ndoc_pers'=>$_POST['ndoc_pers'],'appa_pers'=>$_POST['appa_pers'],'apma_pers'=>$_POST['apma_pers'],'nomb_pers'=>$_POST['nomb_pers'],'codi_depe'=>$_POST['codi_depe'],'codi_carg'=>$_POST['codi_carg'],'regi_labo'=>$_POST['regi_labo'],'fech_ingr'=>$_POST['fech_ingr'],'digi_pers'=>$_POST['digi_pers'],'esta_pers'=>$_POST['esta_pers']],['iden_pers'=>$_POST['iden_pers']]);
		}
		else
		{
			$result=$Db->insert('mp_admi_pers',['ndoc_pers'=>$_POST['ndoc_pers'],'appa_pers'=>$_POST['appa_pers'],'apma_pers'=>$_POST['apma_pers'],'nomb_pers'=>$_POST['nomb_pers'],'codi_depe'=>$_POST['codi_depe'],'codi_carg'=>$_POST['codi_carg'],'regi_labo'=>$_POST['regi_labo'],'fech_ingr'=>$_POST['fech_ingr'],'digi_pers'=>$_POST['digi_pers'],'esta_pers'=>$_POST['esta_pers']]);
			$_POST['iden_pers']=$Db->lastInsertId();
		}

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"personal_general.php\">
                    <input type=hidden name=\"iden_pers\" value=\"".$_POST['iden_pers']."\">
					<input type=hidden name=\"busq_tipo\" value=\"".$_POST['busq_tipo']."\">
					<input type=hidden name=\"busq_dato\" value=\"".$_POST['busq_dato']."\">
					<input type=hidden name=\"busq_pagi_actu\" value=\"".$_POST['busq_pagi_actu']."\">
					<input type=hidden name=\"codi_form\" value=\"".$_POST['codi_form']."\">
                                </form>
                                <script>
                                        document.form.submit();
                                </script>
                        </body></html>
		";

	}
	$result_personal=$Db->select('mp_admi_pers', ['iden_pers'=>$_POST['iden_pers']], '', '', '');
	$_POST['iden_tdoc']=$result_personal[0]['iden_tdoc'];
	$_POST['ndoc_pers']=$result_personal[0]['ndoc_pers'];
	$_POST['iden_sexo']=$result_personal[0]['iden_sexo'];
	$_POST['appa_pers']=$result_personal[0]['appa_pers'];
	$_POST['apma_pers']=$result_personal[0]['apma_pers'];
	$_POST['nomb_pers']=$result_personal[0]['nomb_pers'];
	$_POST['fing_pers']=$result_personal[0]['fing_pers'];
	$_POST['iden_eciv']=$result_personal[0]['iden_eciv'];
	$_POST['acti_pers']=$result_personal[0]['acti_pers'];
	
	$_POST['regi_labo']=$result_personal[0]['regi_labo'];
	$_POST['esta_pers']=$result_personal[0]['esta_pers'];
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title></title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		<!--
		<link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
        <link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/css/bootstrap-select.min.css'>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
        -->
		<script>
			function f_guardar_personal()
			{
				if(document.form.ndoc_pers.value=='')
				{
					alert('Ingrese Nro. de DNI');
					document.form.ndoc_pers.focus();
					return false;
				}
				else
				{
					if(document.form.appa_pers.value=='')
					{
						alert('Ingrese Apellido Paterno');
						document.form.appa_pers.focus();
						return false;
					}
					else
					{
						if(document.form.nomb_pers.value=='')
						{
							alert('Ingrese Nombres');
							document.form.nomb_pers.focus();
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
				}
			}
			function f_cancelar_documento()
			{
				document.form.action='personal_mantenimiento.php';
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
	<center><h4 style="color:#073a6b"><b>
<?
	if($_POST['iden_pers'])
		echo"Datos Generales<BR>".$_POST['appa_pers']." ".$_POST['apma_pers'].", ".$_POST['nomb_pers'];
	else
		echo"Crear Nuevo Personal";
?>
	</h4></b></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="iden_pers" value="<?=$_POST['iden_pers']?>">
			<input type=hidden name="busq_tipo" value="<?=$_POST['busq_tipo']?>">
			<input type=hidden name="busq_dato" value="<?=$_POST['busq_dato']?>">
			<input type=hidden name="codi_depe" value="<?=$_POST['codi_depe']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">
			<input type=hidden name="codi_form" value="<?=$_POST['codi_form']?>">
			<main>
<?
	$html=new htmlclass;

    if(!$_POST['iden_tdoc'])
        $_POST['iden_tdoc']=1;
    if(!$_POST['iden_pais'])
        $_POST['iden_pais']=348;
    $arra_options_tdoc=$Db->get_options('mp_maes_tdocumento',1,0);
    $arra_options_sexo=$Db->get_options('mp_maes_sexo',1,0);
    $arra_options_pais=$Db->get_options('mp_maes_pais',1,0);
    $arra_options_tafp=$Db->get_options('mp_maes_afp',1,0);
    $arra_options_eciv=$Db->get_options('mp_maes_estado_civil',1,0);
	
	echo $html->put_title_demand("Datos Generales");
	echo $html->put_select("Tipo&nbsp;Documento",'iden_tdoc',$arra_options_tdoc,$_POST['iden_tdoc'],"");
	echo $html->put_text('text',"Nro.&nbsp;Documento","Ingrese Nro. Documento",'ndoc_pers',$_POST['ndoc_pers'],'','15','');
	echo $html->put_select("Sexo",'iden_sexo',$arra_options_sexo,$_POST['iden_sexo'],"");
	echo"</main><main>";
	echo $html->put_text('text',"Apellido&nbsp;Paterno","Ingrese Apellido Paterno",'appa_pers',$_POST['appa_pers'],'','50','');
	echo $html->put_text('text',"Apellido&nbsp;Materno","Ingrese Apellido Materno",'apma_pers',$_POST['apma_pers'],'','50','');
	echo $html->put_text('text',"Nombres","Ingrese Nombres",'nomb_pers',$_POST['nomb_pers'],'','50','');
	echo"</main><main>";
	echo $html->put_text('text',"Nro.&nbsp;RUC","Ingrese Nro. RUC",'nruc_pers',$_POST['nruc_pers'],'','20','');
	echo $html->put_select("AFP",'iden_tafp',$arra_options_tafp,$_POST['iden_tafp'],"");
	echo $html->put_text('text',"C&oacute;digo&nbsp;de&nbsp;CUSPP","Ingrese CUSPP",'cusp_pers',$_POST['cusp_pers'],'','20','');
	echo"</main><main>";
	echo $html->put_select("Estado&nbsp;Civil",'iden_eciv',$arra_options_eciv,$_POST['iden_eciv'],"");
	echo"</main><main>";
	echo $html->put_title_demand("Lugar y Fecha de Nacimiento");
	echo $html->put_text('date',"Fecha","Ingrese fecha",'fnac_pers',$_POST['fnac_pers'],'','50','');
	echo $html->put_select_buscador("Pa&iacute;s",'iden_pais',$arra_options_pais,$_POST['iden_pais'],"");
	echo"</main><main>";
	echo $html->put_select("Departamento",'iden_dpto',$arra_options_dpto,$_POST['iden_dpto'],"");
	echo $html->put_select("Provincia",'iden_prov',$arra_options_prov,$_POST['iden_prov'],"");
	echo $html->put_select("Distrito",'iden_dist',$arra_options_dist,$_POST['iden_dist'],"");
	
	echo $html->put_title_demand("Informaci&oacute;n de Contacto");
	echo $html->put_text('text',"Celular&nbsp;Personal","Ingrese Celular Personal",'celu_pers',$_POST['celu_pers'],'','20','');
	echo $html->put_text('text',"Celular&nbsp;Institucional","Ingrese Celular Institucional",'celu_pers',$_POST['celu_pers'],'','20','');
	echo"</main><main>";
	echo $html->put_text('text',"Correo&nbsp;Personal","Ingrese Correo Personal",'mail_pers',$_POST['mail_pers'],'','20','');
	echo $html->put_text('text',"Correo&nbsp;Institucional","Ingrese Correo Institucional",'mail_pers',$_POST['mail_pers'],'','20','');
	//echo $html->put_title_demand("Otros");
	echo"</main><main>";
	
	echo $html->put_title_demand("Estado del Trabajador");
	echo $html->put_select_estado(CONST_SUBTITLE_STATE,'acti_pers',$_POST['acti_pers'],'Activo','Inactivo');
	echo"</main>";

	echo $html->put_separator_demand("30");

                echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_cancelar_documento()\">&laquo; Cancelar</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"return f_guardar_personal()\">Guardar &raquo;</button>
                                        </div>
                                </div>
                        </div>
                ";
?>
<center>
	</form>
	</body>
</html>
