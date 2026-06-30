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
	    if($_POST['fech_dete'])
	        $_POST['fech_dete']=substr($_POST['fech_dete'],0,4).substr($_POST['fech_dete'],5,2).substr($_POST['fech_dete'],8,2).substr($_POST['hora_dete'],0,2).substr($_POST['hora_dete'],3,2).'00';
		if($_POST['codi_dete'])
		{
			$result=$Db->update('mp_elec_detenciones',['codi_elec'=>$_POST['codi_elec'],'codi_usua'=>$_SESSION['logi_oper'],'dete_inte'=>$_POST['dete_inte'],'fech_dete'=>$_POST['fech_dete'],'ubig_dete'=>$_POST['ubig_dete'],'nomb_dete'=>$_POST['nomb_dete'],'ndni_dete'=>$_POST['ndni_dete'],'edad_dete'=>$_POST['edad_dete'],'sexo_dete'=>$_POST['sexo_dete'],'codi_inte'=>$_POST['codi_inte'],'hora_moti'=>$_POST['hora_moti'],'deta_moti'=>$_POST['deta_moti'],'codi_acci'=>$_POST['codi_acci'],'codi_deli'=>$_POST['codi_deli'],'luga_inte'=>$_POST['luga_inte'],'deta_inte'=>$_POST['deta_inte'],'digi_dete'=>$_SESSION['iden_oper'],'fdig_dete'=>"$fdig",'esta_dete'=>'1'],['codi_dete'=>$_POST['codi_dete']]);
		}
		else
		{
			$result=$Db->insert('mp_elec_detenciones',['codi_elec'=>$_POST['codi_elec'],'codi_usua'=>$_SESSION['logi_oper'],'dete_inte'=>$_POST['dete_inte'],'fech_dete'=>$_POST['fech_dete'],'ubig_dete'=>$_POST['ubig_dete'],'nomb_dete'=>$_POST['nomb_dete'],'ndni_dete'=>$_POST['ndni_dete'],'edad_dete'=>$_POST['edad_dete'],'sexo_dete'=>$_POST['sexo_dete'],'codi_inte'=>$_POST['codi_inte'],'hora_moti'=>$_POST['hora_moti'],'deta_moti'=>$_POST['deta_moti'],'codi_acci'=>$_POST['codi_acci'],'codi_deli'=>$_POST['codi_deli'],'luga_inte'=>$_POST['luga_inte'],'deta_inte'=>$_POST['deta_inte'],'digi_dete'=>$_SESSION['iden_oper'],'fdig_dete'=>"$fdig",'esta_dete'=>'1']);
			$_POST['codi_part']=$Db->lastInsertId();
		}
		if($_POST['regresar_reporte'])
		    $dire="elecciones_detenciones.php";
		else
		    $dire="elecciones_detenciones_nuevo.php";
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
	//codi_dete	codi_elec	codi_usua	dete_inte	codi_tale	fech_dete	ubig_dete	luga_dete	deta_dete	acci_dete	digi_dete	fdig_dete	esta_dete
	
	$result_documento=$Db->select('mp_elec_detenciones', ['codi_dete'=>$_POST['codi_dete']], '', '', '');
	$_POST['dete_inte']=$result_documento[0]['dete_inte'];
	if($result_documento[0]['fech_dete'])
	{
	    $_POST['hora_dete']=substr($result_documento[0]['fech_dete'],8,2).':'.substr($result_documento[0]['fech_dete'],10,2);
	    $_POST['fech_dete']=substr($result_documento[0]['fech_dete'],0,4).'-'.substr($result_documento[0]['fech_dete'],4,2).'-'.substr($result_documento[0]['fech_dete'],6,2);
	}
	$_POST['ubig_dete']=$result_documento[0]['ubig_dete'];
	$_POST['nomb_dete']=$result_documento[0]['nomb_dete'];
	$_POST['ndni_dete']=$result_documento[0]['ndni_dete'];
	$_POST['edad_dete']=$result_documento[0]['edad_dete'];
	$_POST['sexo_dete']=$result_documento[0]['sexo_dete'];
	$_POST['codi_inte']=$result_documento[0]['codi_inte'];
	$_POST['hora_moti']=$result_documento[0]['hora_moti'];
	$_POST['deta_moti']=$result_documento[0]['deta_moti'];
	$_POST['codi_acci']=$result_documento[0]['codi_acci'];
	$_POST['codi_deli']=$result_documento[0]['codi_deli'];
	$_POST['luga_inte']=$result_documento[0]['luga_inte'];
	$_POST['deta_inte']=$result_documento[0]['deta_inte'];
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
			    if(document.form.dete_inte.selectedIndex=='0')
				{
					alert('Seleccione Detenido o Intervenido');
					document.form.dete_inte.focus();
					return false;
				}
				else
				{
				    if(document.form.ubig_dete.selectedIndex=='0')
				    {
				        alert('Seleccione Provincia y Distrito');
					    document.form.ubig_dete.focus();
					    return false;
				    }
				    else
				    {
						if(document.form.luga_inte.value=='')
						{
							alert('Ingrese lugar');
							document.form.luga_inte.focus();
							return false;
						}
						else
						{
						    if(document.form.codi_inte.selectedIndex=='0')
            				{
            					alert('Seleccione Motivo');
            					document.form.codi_inte.focus();
            					return false;
            				}   
    		        		else
            				{
    					    		if(confirm('VERIFIQUE SU INFORMACION ANTES DE REGISTRARLA:\n\nDETENIDO O INTERVENIDO: '+document.form.dete_inte.options[document.form.dete_inte.selectedIndex].text+'\nUBICACION: '+document.form.ubig_dete.options[document.form.ubig_dete.selectedIndex].text+'\nMOTIVO: '+document.form.codi_inte.options[document.form.codi_inte.selectedIndex].text+'\nFECHA: '+document.form.fech_dete.value+'\nHORA: '+document.form.hora_dete.value+'\n\nDESEA GUARDAR?'))
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
				document.form.action='elecciones_detenciones_reporte.php';
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
	<center><font style="color:#073A6B;font-weight: bold;"><?=$_POST['nomb_elec']?><BR><font style="font-size: 20;">DETENCIONES E INTERVENCIONES</font><br>REPORTE</font></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="regresar_reporte" value="<?=$_POST['regresar_reporte']?>">
			<input type=hidden name="codi_dete" value="<?=$_POST['codi_dete']?>">
			<main>
<?
	$html=new htmlclass;
	
	if(!$_POST['ubig_dete'])
	    $_POST['ubig_dete']='01';

    $arra_options_dete_inte[0]="<- Seleccione ->";
    $arra_options_dete_inte[1]="Detenido";
    $arra_options_dete_inte[2]="Intervenido";
 
	$arra_options_prov[0]="<- Seleccione ->";
	$result=$Db->query("select distinct cdep,cpro,cdis,prov,dist from ubig_reni WHERE cdep='04' AND cpro<>'00' AND cdis<>'00' order by cpro,cdis");
	foreach($result as $rows)
	{
	    $c=$rows['cdep'].$rows['cpro'].$rows['cdis'];
		$arra_options_prov[$c]=$rows['prov']." - ".$rows['dist'];
	}

	$arra_options_tale[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_elecciones_detenciones_tipo', '', '', '', ['n_codigo'=>'ASC']);
	foreach($result as $rows)
		$arra_options_tale[$rows['n_codigo']]=$rows['x_nombre'];
	
	$arra_options_inte[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_elecciones_intervencion', '', '', '', ['n_codigo'=>'ASC']);
	foreach($result as $rows)
		$arra_options_inte[$rows['n_codigo']]=$rows['x_nombre'];
		
	$arra_options_deli[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_elecciones_delito', '', '', '', ['n_codigo'=>'ASC']);
	foreach($result as $rows)
		$arra_options_deli[$rows['n_codigo']]=$rows['x_nombre'];
	
	$arra_options_acci[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_elecciones_accionar', '', '', '', ['n_codigo'=>'ASC']);
	foreach($result as $rows)
		$arra_options_acci[$rows['n_codigo']]=$rows['x_nombre'];
	
	$arra_options_sexo[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_sexo', '', '', '', ['n_codigo'=>'ASC']);
	foreach($result as $rows)
		$arra_options_sexo[$rows['n_codigo']]=$rows['x_nombre'];
	
	if(strlen($_POST['fech_dete'])<8)
	{
	    $_POST['fech_dete']=date("Y-m-d");
	    $_POST['hora_dete']=date("H:i");
	}

	echo $html->put_title_demand("Ingrese Información");
	echo $html->put_select("Detenido&nbsp;o&nbsp;Intervenido",'dete_inte',$arra_options_dete_inte,$_POST['dete_inte'],"disabled");
	echo $html->put_text('date',"Fecha","Ingrese Fecha ",'fech_dete',$_POST['fech_dete'],'','8','disabled');
	echo $html->put_text('time',"Hora","Ingrese Hora ",'hora_dete',$_POST['hora_dete'],'','8','disabled');
	echo"</main><main>";
	echo $html->put_select("Provincia&nbsp;-&nbsp;Distrito",'ubig_dete',$arra_options_prov,$_POST['ubig_dete'],"disabled");
	echo $html->put_text('text',"Lugar","Ingrese Lugar de intervenci&oacute;n o detenci&oacute;n",'luga_inte',$_POST['luga_inte'],'','100','disabled');
	echo $html->put_select("Accionar&nbsp;del&nbsp;MP",'codi_acci',$arra_options_acci,$_POST['codi_acci'],"disabled");
	
	echo $html->put_title_demand("Motivo de Intervenci&oacute;n o Detenci&oacute;n");
	echo $html->put_select("Motivo",'codi_inte',$arra_options_inte,$_POST['codi_inte'],"disabled");
	echo $html->put_text('time',"Hora","Ingrese Hora ",'hora_moti',$_POST['hora_moti'],'','8','disabled');
	echo $html->put_text('text',"Detalle","Ingrese Detalle",'deta_moti',$_POST['deta_moti'],'','100','disabled');
	
	echo $html->put_title_demand("Detenido o Intervenido");
	echo $html->put_text('text',"Nombres&nbsp;y&nbsp;Apellidos","Ingrese Nombres",'nomb_dete',$_POST['nomb_dete'],'','200','disabled');
	echo $html->put_text('text',"DNI","Ingrese DNI",'ndni_dete',$_POST['ndni_dete'],'','8','disabled');
	echo $html->put_text('text',"Edad","Ingrese Edad",'edad_dete',$_POST['edad_dete'],'','3','disabled');
	echo"</main><main>";
	echo $html->put_select("Sexo",'sexo_dete',$arra_options_sexo,$_POST['sexo_dete'],"disabled");
	echo $html->put_select("Tipo&nbsp;de&nbsp;Delito",'codi_deli',$arra_options_deli,$_POST['codi_deli'],"disabled");
	echo $html->put_textarea("Detalle&nbsp;de&nbsp;los&nbsp;Hechos",'deta_inte',$_POST['deta_inte'],'style="height: 100px;" disabled');
	
	//echo $html->put_title_demand("Subir Acta");
	//echo $html->put_upload_file("Hoja&nbsp;1",'file_doc1','','');
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
                                        <!--<div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"return f_guardar()\">Guardar &raquo;</button>
                                        </div>-->
                                </div>
                        </div>
                ";
?>
<center>
	</form>
	</body>
</html>
