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
	    if($_POST['fech_coor'])
	        $_POST['fech_coor']=substr($_POST['fech_coor'],0,4).substr($_POST['fech_coor'],5,2).substr($_POST['fech_coor'],8,2);
		if($_POST['codi_coor'])
		{
			$result=$Db->update('mp_elec_coordinaciones',['codi_elec'=>$_POST['codi_elec'],'codi_usua'=>$_SESSION['logi_oper'],'codi_inst'=>$_POST['codi_inst'],'fech_coor'=>$_POST['fech_coor'],'ubig_coor'=>$_POST['ubig_coor'],'obse_coor'=>$_POST['obse_coor'],'digi_coor'=>$_SESSION['iden_oper'],'fdig_coor'=>"$fdig",'esta_coor'=>'1'],['codi_coor'=>$_POST['codi_coor']]);
		}
		else
		{
			$result=$Db->insert('mp_elec_coordinaciones',['codi_elec'=>$_POST['codi_elec'],'codi_usua'=>$_SESSION['logi_oper'],'codi_inst'=>$_POST['codi_inst'],'fech_coor'=>$_POST['fech_coor'],'ubig_coor'=>$_POST['ubig_coor'],'obse_coor'=>$_POST['obse_coor'],'digi_coor'=>$_SESSION['iden_oper'],'fdig_coor'=>"$fdig",'esta_coor'=>'1']);
			$_POST['codi_coor']=$Db->lastInsertId();
		}
		if($_POST['regresar_reporte'])
		    $dire="elecciones_coordinaciones.php";
		else
		    $dire="elecciones_coordinaciones_nuevo.php";
		
		if($_FILES['file_docu']['name'] AND $_FILES['file_docu']['size']>0)
		{
			if(strstr($_FILES['file_docu']['type'],"pdf") OR strstr($_FILES['file_docu']['type'],"pdf"))
			{
			    //echo"<script>alert('OK!!!');</script>";
				//subir_archivo('logo',$_FILES['file_docu']['tmp_name'],"pers_".str_pad($_POST['codi_pers'], 6, "0", STR_PAD_LEFT).".pdf","");
				move_uploaded_file($_FILES['file_docu']['tmp_name'],"actas/coor_".str_pad($_POST['codi_coor'], 6, "0", STR_PAD_LEFT).".pdf");
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
	$result_documento=$Db->select('mp_elec_coordinaciones', ['codi_coor'=>$_POST['codi_coor']], '', '', '');
	$_POST['codi_inst']=$result_documento[0]['codi_inst'];
	$_POST['fech_coor']=substr($result_documento[0]['fech_coor'],0,4).'-'.substr($result_documento[0]['fech_coor'],4,2).'-'.substr($result_documento[0]['fech_coor'],6,2);
	$_POST['ubig_coor']=$result_documento[0]['ubig_coor'];
	$_POST['obse_coor']=$result_documento[0]['obse_coor'];
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
				if(document.form.codi_inst.selectedIndex=='0')
				{
					alert('Seleccione Inistitucion');
					document.form.codi_inst.focus();
					return false;
				}
				else
				{
				    if(document.form.ubig_coor.selectedIndex=='0')
				    {
				        alert('Seleccione Provincia');
					    document.form.ubig_coor.focus();
					    return false;
				    }
				    else
				    {
						if(document.form.fech_coor.value=='')
						{
							alert('Seleccione Fecha de Coordinacion');
							document.form.fech_coor.focus();
							return false;
						}
						else
						{
					    		if(confirm('VERIFIQUE SU INFORMACION ANTES DE REGISTRARLA:\n\nINSTITUCION: '+document.form.codi_inst.options[document.form.codi_inst.selectedIndex].text+'\nPROVINCIA: '+document.form.ubig_coor.options[document.form.ubig_coor.selectedIndex].text+'\nFECHA: '+document.form.fech_coor.value+'\n\nDESEA GUARDAR?'))
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
			function f_cancelar()
			{
				document.form.action='elecciones_coordinaciones.php';
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
	<center><font style="color:#073A6B;font-weight: bold;"><?=$_POST['nomb_elec']?><BR><font style="font-size: 20;">REGISTRAR COORDINACION INTERINSTITUCIONAL</font><br>USUARIO: <?=$_SESSION['logi_oper']?></font></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="regresar_reporte" value="<?=$_POST['regresar_reporte']?>">
			<input type=hidden name="codi_coor" value="<?=$_POST['codi_coor']?>">
			<main>
<?
	$html=new htmlclass;
	
	if(!$_POST['ubig_coor'])
	    $_POST['ubig_coor']='01';

    	
	$arra_options_prov[0]="<- Seleccione ->";
	$result=$Db->query("select distinct cdep,cpro,cdis,prov,dist from ubig_reni WHERE cdep='04' AND cpro<>'00' AND cdis<>'00' order by cpro,cdis");
	foreach($result as $rows)
	{
	    $c=$rows['cdep'].$rows['cpro'].$rows['cdis'];
		$arra_options_prov[$c]=$rows['prov']." - ".$rows['dist'];
	}

	$arra_options_inst[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_elecciones_instituciones', '', '', '', ['n_codigo'=>'ASC']);
	foreach($result as $rows)
		$arra_options_inst[$rows['n_codigo']]=$rows['x_nombre'];
	
	if(!$_POST['fech_coor'])
	    $_POST['fech_coor']=date("Y-m-d");

	echo $html->put_title_demand("Ingrese Información");
	echo $html->put_select("Institución",'codi_inst',$arra_options_inst,$_POST['codi_inst'],"");
	echo $html->put_select("Ubicaci&oacute;n",'ubig_coor',$arra_options_prov,$_POST['ubig_coor'],"");
	echo $html->put_text('date',"Fecha","Ingrese Fecha ",'fech_coor',$_POST['fech_coor'],'','8',' onchange="return solonumeros(this.value)"');
    echo $html->put_title_demand("Observaciones");
    echo $html->put_textarea("",'obse_coor',$_POST['obse_coor'],'style="height: 100px;"');
    echo $html->put_title_demand("Subir Acta");
	echo $html->put_upload_file("",'file_docu','','');
	//echo $html->put_text('text',"Nro.&nbsp;DNI","Ingrese Nro. DNI",'nume_docu',$_POST['nume_docu'],'','8',' onchange="return solonumeros(this.value)"');
	//echo $html->put_text('text',"Apellidos&nbsp;y&nbsp;Nombres","Ingrese Apellidos y Nombres",'nomb_part',$_POST['nomb_part'],'','100','pattern="[A-Za-z ]+" title="Solo letras"');
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
