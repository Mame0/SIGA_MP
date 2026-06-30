<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	$result=$Db->query("select codi_elec,nomb_elec from mp_elec_config WHERE habi_elec='1'");
	foreach($result as $rows)
	{
	    $_POST['nomb_elec']=$rows['nomb_elec'];
	    $_POST['codi_elec']=$rows['codi_elec'];
	}

	$fdig=date("YmdHis");
	
	if($_POST['guardar_personal'])
	{
	    if($_POST['fech_aler'])
	        $_POST['fech_aler']=substr($_POST['fech_aler'],0,4).substr($_POST['fech_aler'],5,2).substr($_POST['fech_aler'],8,2).substr($_POST['hora_aler'],0,2).substr($_POST['hora_aler'],3,2).'00';
		if($_POST['codi_aler'])
		{
			$result=$Db->update('mp_elec_alertas',['codi_elec'=>$_POST['codi_elec'],'codi_usua'=>$_SESSION['logi_oper'],'aler_ocur'=>$_POST['aler_ocur'],'codi_tale'=>$_POST['codi_tale'],'fech_aler'=>$_POST['fech_aler'],'ubig_aler'=>$_POST['ubig_aler'],'luga_aler'=>$_POST['luga_aler'],'deta_aler'=>$_POST['deta_aler'],'acci_aler'=>$_POST['acci_aler'],'digi_aler'=>$_SESSION['iden_oper'],'fdig_aler'=>"$fdig",'esta_aler'=>$_POST['esta_aler']],['codi_aler'=>$_POST['codi_aler']]);
		}
		else
		{
			$result=$Db->insert('mp_elec_alertas',['codi_elec'=>$_POST['codi_elec'],'codi_usua'=>$_SESSION['logi_oper'],'aler_ocur'=>$_POST['aler_ocur'],'codi_tale'=>$_POST['codi_tale'],'fech_aler'=>$_POST['fech_aler'],'ubig_aler'=>$_POST['ubig_aler'],'luga_aler'=>$_POST['luga_aler'],'deta_aler'=>$_POST['deta_aler'],'acci_aler'=>$_POST['acci_aler'],'digi_aler'=>$_SESSION['iden_oper'],'fdig_aler'=>"$fdig",'esta_aler'=>$_POST['esta_aler']]);
			$_POST['codi_aler']=$Db->lastInsertId();
		}
		if($_POST['regresar_reporte'])
		    $dire="elecciones_alertas.php";
		else
		    $dire="elecciones_alertas_nuevo.php";
		    
		if($_FILES['file_docu']['name'] AND $_FILES['file_docu']['size']>0)
		{
			if(strstr($_FILES['file_docu']['type'],"pdf") OR strstr($_FILES['file_docu']['type'],"pdf"))
			{
			    //echo"<script>alert('OK!!!');</script>";
				//subir_archivo('logo',$_FILES['file_docu']['tmp_name'],"pers_".str_pad($_POST['codi_pers'], 6, "0", STR_PAD_LEFT).".pdf","");
				move_uploaded_file($_FILES['file_docu']['tmp_name'],"actas/aler_".str_pad($_POST['codi_aler'], 6, "0", STR_PAD_LEFT).".pdf");
			}
			else
				echo"<script>alert('ERROR: Archivo no es un PDF');</script>";
		}
		
		echo"
			<script>alert('".CONST_MENS_REG_OK."');</script>
                        <html><body>
                                <form name=\"form\" method=post action=\"$dire\">
					
                                </form>
                                <script>
                                        document.form.submit();
                                </script>
                        </body></html>
		";

	}
	//codi_aler	codi_elec	codi_usua	aler_ocur	codi_tale	fech_aler	ubig_aler	luga_aler	deta_aler	acci_aler	digi_aler	fdig_aler	esta_aler
	
	$result_documento=$Db->select('mp_elec_alertas', ['codi_aler'=>$_POST['codi_aler']], '', '', '');
	$_POST['aler_ocur']=$result_documento[0]['aler_ocur'];
	$_POST['codi_tale']=$result_documento[0]['codi_tale'];
	if($result_documento[0]['fech_aler'])
	{
	    $_POST['hora_aler']=substr($result_documento[0]['fech_aler'],8,2).':'.substr($result_documento[0]['fech_aler'],10,2);
	    $_POST['fech_aler']=substr($result_documento[0]['fech_aler'],0,4).'-'.substr($result_documento[0]['fech_aler'],4,2).'-'.substr($result_documento[0]['fech_aler'],6,2);
	}
	$_POST['ubig_aler']=$result_documento[0]['ubig_aler'];
	$_POST['luga_aler']=$result_documento[0]['luga_aler'];
	$_POST['deta_aler']=$result_documento[0]['deta_aler'];
	$_POST['acci_aler']=$result_documento[0]['acci_aler'];
	$_POST['esta_aler']=$result_documento[0]['esta_aler'];
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
			    if(document.form.aler_ocur.selectedIndex=='0')
				{
					alert('Seleccione Alerta u Ocurrencia');
					document.form.aler_ocur.focus();
					return false;
				}
				else
				{
				    if(document.form.ubig_aler.selectedIndex=='0')
				    {
				        alert('Seleccione Provincia y Distrito');
					    document.form.ubig_aler.focus();
					    return false;
				    }
				    else
				    {
						if(document.form.luga_aler.value=='')
						{
							alert('Ingrese lugar');
							document.form.luga_aler.focus();
							return false;
						}
						else
						{
						    if(document.form.codi_tale.selectedIndex=='0')
            				{
            					alert('Seleccione Tipo');
            					document.form.codi_tale.focus();
            					return false;
            				}   
    		        		else
            				{
    					    		if(confirm('VERIFIQUE SU INFORMACION ANTES DE REGISTRARLA:\n\nALERTA U OCURRENCIA: '+document.form.aler_ocur.options[document.form.aler_ocur.selectedIndex].text+'\nUBICACION: '+document.form.ubig_aler.options[document.form.ubig_aler.selectedIndex].text+'\nTIPO: '+document.form.codi_tale.options[document.form.codi_tale.selectedIndex].text+'\nFECHA: '+document.form.fech_aler.value+'\nHORA: '+document.form.hora_aler.value+'\n\nDESEA GUARDAR?'))
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
			}
			function f_cancelar()
			{
				document.form.action='elecciones_alertas.php';
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
	<center><font style="color:#073A6B;font-weight: bold;"><?=$_POST['nomb_elec']?><BR><font style="font-size: 20;">FORMATO B<BR>REGISTRAR NUEVAS ALERTAS U OCURRENCIAS</font><br>USUARIO: <?=$_SESSION['logi_oper']?></font></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="regresar_reporte" value="<?=$_POST['regresar_reporte']?>">
			<input type=hidden name="codi_aler" value="<?=$_POST['codi_aler']?>">
			<main>
<?
	$html=new htmlclass;
	
	if(!$_POST['ubig_aler'])
	    $_POST['ubig_aler']='01';

    $arra_options_aler_ocur[0]="<- Seleccione ->";
    $arra_options_aler_ocur[1]="Alerta";
    $arra_options_aler_ocur[2]="Ocurrencia";
    
    $arra_options_lesi_fall[0]="<- Seleccione ->";
    $arra_options_lesi_fall[1]="Lesionado";
    $arra_options_lesi_fall[2]="Fallecido";
	
	$arra_options_prov[0]="<- Seleccione ->";
	$result=$Db->query("select distinct cdep,cpro,cdis,prov,dist from ubig_reni WHERE cdep='04' AND cpro<>'00' AND cdis<>'00' order by cpro,cdis");
	foreach($result as $rows)
	{
	    $c=$rows['cdep'].$rows['cpro'].$rows['cdis'];
		$arra_options_prov[$c]=$rows['prov']." - ".$rows['dist'];
	}

	$arra_options_tale[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_elecciones_alertas_tipo', '', '', '', ['n_codigo'=>'ASC']);
	foreach($result as $rows)
		$arra_options_tale[$rows['n_codigo']]=$rows['x_nombre'];
	
	$arra_options_sexo[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_sexo', '', '', '', ['n_codigo'=>'ASC']);
	foreach($result as $rows)
		$arra_options_sexo[$rows['n_codigo']]=$rows['x_nombre'];
	
	if(strlen($_POST['fech_aler'])<8)
	{
	    $_POST['fech_aler']=date("Y-m-d");
	    $_POST['hora_aler']=date("H:i");
	}

	echo $html->put_title_demand("Ingrese Información");
	echo $html->put_select("Alerta&nbsp;u&nbsp;Ocurrencia",'aler_ocur',$arra_options_aler_ocur,$_POST['aler_ocur'],"");
	echo $html->put_text('date',"Fecha","Ingrese Fecha ",'fech_aler',$_POST['fech_aler'],'','8','');
	echo $html->put_text('time',"Hora","Ingrese Hora ",'hora_aler',$_POST['hora_aler'],'','8','');
	echo"</main><main>";
	echo $html->put_select("Ubicaci&oacute;n",'ubig_aler',$arra_options_prov,$_POST['ubig_aler'],"");
	echo $html->put_text('text',"Lugar","Ingrese Lugar",'luga_aler',$_POST['luga_aler'],'','200','');
	echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_aler',$_POST['esta_aler'],'Activo','Inactivo');
	echo $html->put_title_demand("Tipo de Alerta u Ocurrencia");
	echo $html->put_select("Tipo",'codi_tale',$arra_options_tale,$_POST['codi_tale'],"");
	echo $html->put_textarea("Detalle&nbsp;de&nbsp;Hechos",'deta_aler',$_POST['deta_aler'],'style="height: 100px;"');
	echo $html->put_textarea("Accionar&nbsp;Fiscal",'acci_aler',$_POST['acci_aler'],'style="height: 100px;"');
	//echo $html->put_title_demand("Lesionados y Fallecidos");
	//echo $html->put_select("Condici&oacute;n",'lesi_fall',$arra_options_lesi_fall,$_POST['lesi_fall'],"");
	//echo $html->put_text('text',"Nombres&nbsp;y&nbsp;Apellidos","Ingrese Nombres",'nomb_aler',$_POST['nomb_aler'],'','200','');
	//echo $html->put_text('text',"DNI","Ingrese DNI",'ndni_aler',$_POST['ndni_aler'],'','8','');
	//echo"</main><main>";
	//echo $html->put_select("Sexo",'sexo_aler',$arra_options_sexo,$_POST['sexo_aler'],"");
	//echo $html->put_text('text',"Edad","Ingrese Edad",'edad_aler',$_POST['edad_aler'],'','3','');
	echo $html->put_title_demand("Subir Acta");
	echo $html->put_upload_file("",'file_docu','','');
	//echo $html->put_upload_file("Hoja&nbsp;2",'file_doc2','','');
	//echo $html->put_upload_file("Hoja&nbsp;3",'file_doc3','','');
    
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
