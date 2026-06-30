<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	switch($_POST['codi_form'])
	{
	    case 1: $nomb_tabl="mp_fotocheck_personal";  
	            break;
	    case 2: $nomb_tabl="mp_fotocheck_personal"; 
	            break;
	    case 3: $nomb_tabl="mp_fotocheck_secigra";  
	            break;
	}

	$fdig=date("YmdHis");

	if($_POST['guardar_personal'])
	{
		$fdig=date(YmdHis);
		//$_POST['esta_pers']=1;
		if($_POST['codi_pers'])
		{
			$result=$Db->update($nomb_tabl,['ndni_pers'=>$_POST['ndni_pers'],'appe_pers'=>$_POST['appe_pers'],'nomb_pers'=>$_POST['nomb_pers'],'codi_adic'=>$_POST['codi_adic'],'codi_depe'=>$_POST['codi_depe'],'codi_carg'=>$_POST['codi_carg'],'codi_regi'=>$_POST['codi_regi'],'habi_impr'=>$_POST['habi_impr'],'esta_impr'=>$_POST['esta_impr'],'esta_pers'=>$_POST['esta_pers']],['codi_pers'=>$_POST['codi_pers']]);
		}
		else
		{
			$result=$Db->insert($nomb_tabl,['ndni_pers'=>$_POST['ndni_pers'],'appe_pers'=>$_POST['appe_pers'],'nomb_pers'=>$_POST['nomb_pers'],'codi_adic'=>$_POST['codi_adic'],'codi_depe'=>$_POST['codi_depe'],'codi_carg'=>$_POST['codi_carg'],'codi_regi'=>$_POST['codi_regi'],'habi_impr'=>$_POST['habi_impr'],'esta_impr'=>$_POST['esta_impr'],'esta_pers'=>$_POST['esta_pers']]);
			$_POST['codi_pers']=$Db->lastInsertId();
		}
		if($_FILES['file_pers']['name'] AND $_FILES['file_pers']['size']>0)
		{
			if(strstr($_FILES['file_pers']['type'],"jpg") OR strstr($_FILES['file_pers']['type'],"jpeg"))
			{
				//subir_archivo('logo',$_FILES['file_pers']['tmp_name'],"pers_".str_pad($_POST['codi_pers'], 6, "0", STR_PAD_LEFT).".pdf","");
				move_uploaded_file($_FILES['file_pers']['tmp_name'],"classes/TCPDF/examples/fotos/".$_POST['ndni_pers'].".jpg");
			}
			else
				echo"<script>alert('ERROR: Archivo no es un JPG');</script>";
		}
		unset($_POST['file_pers']);

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"fotocheck_personal_listado.php\">
					<input type=hidden name=\"busq_tipo\" value=\"".$_POST['busq_tipo']."\">
					<input type=hidden name=\"busq_dato\" value=\"".$_POST['busq_dato']."\">
					<input type=hidden name=\"codi_depe\" value=\"".$_POST['codi_depe']."\">
					<input type=hidden name=\"busq_pagi_actu\" value=\"".$_POST['busq_pagi_actu']."\">
					<input type=hidden name=\"codi_form\" value=\"".$_POST['codi_form']."\">
                                </form>
                                <script>
                                        document.form.submit();
                                </script>
                        </body></html>
		";

	}
	$result_personal=$Db->select($nomb_tabl, ['codi_pers'=>$_POST['codi_pers']], '', '', '');
	$_POST['ndni_pers']=$result_personal[0]['ndni_pers'];
	$_POST['appe_pers']=$result_personal[0]['appe_pers'];
	$_POST['nomb_pers']=$result_personal[0]['nomb_pers'];
	$_POST['codi_adic']=$result_personal[0]['codi_adic'];
	$_POST['codi_depe']=$result_personal[0]['codi_depe'];
	$_POST['codi_carg']=$result_personal[0]['codi_carg'];
	$_POST['codi_regi']=$result_personal[0]['codi_regi'];
	$_POST['habi_impr']=$result_personal[0]['habi_impr'];
	$_POST['esta_impr']=$result_personal[0]['esta_impr'];
	$_POST['esta_pers']=$result_personal[0]['esta_pers'];
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
			function f_guardar_personal()
			{
				if(document.form.ndni_pers.value=='')
				{
					alert('Ingrese Nro. de DNI');
					document.form.ndni_pers.focus();
					return false;
				}
				else
				{
					if(document.form.appe_pers.value=='')
					{
						alert('Ingrese Apellidos');
						document.form.appe_pers.focus();
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
				document.form.action='fotocheck_personal_listado.php';
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
	<center><h2 style="color:#bb0400">
<?
	if($_POST['codi_pers'])
		echo"Editar Informaci&oacute;n de Personal<BR>".$_POST['apel_pers']." ".$_POST['nomb_pers'];
	else
		echo"Crear Nuevo Personal";
?>
	</h2></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="codi_pers" value="<?=$_POST['codi_pers']?>">
			<input type=hidden name="busq_tipo" value="<?=$_POST['busq_tipo']?>">
			<input type=hidden name="busq_dato" value="<?=$_POST['busq_dato']?>">
			<input type=hidden name="codi_depe" value="<?=$_POST['codi_depe']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">
			<input type=hidden name="codi_form" value="<?=$_POST['codi_form']?>">
			<main>
<?
	$html=new htmlclass;


	$arra_options_depe[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_fotocheck_dependencia', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_depe[$rows['n_codigo']]=utf8_encode(utf8_decode($rows['x_nombre']));

	$arra_options_carg[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_fotocheck_cargo', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_carg[$rows['n_codigo']]=utf8_encode(utf8_decode($rows['x_nombre']));

	$arra_options_regi[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_fotocheck_rlaboral', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_regi[$rows['n_codigo']]=utf8_encode($rows['x_nombre']);

	echo $html->put_title_demand("Informaci&oacute;n Personal");
	echo $html->put_text('text',"DNI","Ingrese Nro. DNI",'ndni_pers',$_POST['ndni_pers'],'','8','');
	echo $html->put_text('text',"Apellidos","Ingrese Apellidos",'appe_pers',$_POST['appe_pers'],'','50','');
	echo $html->put_text('text',"Nombres","Ingrese Nombres",'nomb_pers',$_POST['nomb_pers'],'','50','');
	echo $html->put_title_demand("Informaci&oacute;n Laboral");
	echo $html->put_select("Dependencia",'codi_depe',$arra_options_depe,$_POST['codi_depe'],"");
	echo $html->put_select("Régimen",'codi_regi',$arra_options_regi,$_POST['codi_regi'],"");
	echo $html->put_select("Cargo",'codi_carg',$arra_options_carg,$_POST['codi_carg'],"");
	echo $html->put_title_demand("Estado del Trabajador");
	echo $html->put_select_estado("Habilitado&nbsp;para&nbsp;Imprimir",'habi_impr',$_POST['habi_impr'],"SI","NO");
	echo $html->put_select_estado("Fotocheck&nbsp;Impreso",'esta_impr',$_POST['esta_impr'],"SI","NO");
	echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_pers',$_POST['esta_pers'],CONST_OPTION_ENABLE,CONST_OPTION_DISABLE);
	echo"</main><main>";
	echo $html->put_title_demand("Foto del Trabajador");
	echo $html->put_upload_file("Foto&nbsp;<a href=\"classes/TCPDF/examples/fotos/".$_POST['ndni_pers'].".jpg\" target=\"blank\">Ver</a>",'file_pers','','');
	if($_POST['codi_form']==3)
	{
	    echo $html->put_title_demand("Solo Secigristas");
	    echo $html->put_text('text',"Código&nbsp;Secigra","Ingrese Codigo",'codi_adic',$_POST['codi_adic'],'','20','');
	}
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
