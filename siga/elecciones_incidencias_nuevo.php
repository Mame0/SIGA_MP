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
		if($_POST['codi_acta'])
		{
			$result=$Db->update('mp_elec_acta',['codi_elec'=>$_POST['codi_elec'],'codi_usua'=>$_SESSION['logi_oper'],'codi_loca'=>$_POST['codi_loca'],'codi_tact'=>$_POST['codi_tact'],'codi_deli'=>$_POST['codi_deli'],'dete_inte'=>$_POST['dete_inte'],'cant_homb'=>$_POST['cant_homb'],'cant_muje'=>$_POST['cant_muje'],'digi_acta'=>$_SESSION['iden_oper'],'fdig_acta'=>"$fdig",'esta_acta'=>'1'],['codi_acta'=>$_POST['codi_acta']]);
		}
		else
		{
			$result=$Db->insert('mp_elec_acta',['codi_elec'=>$_POST['codi_elec'],'codi_usua'=>$_SESSION['logi_oper'],'codi_loca'=>$_POST['codi_loca'],'codi_tact'=>$_POST['codi_tact'],'codi_deli'=>$_POST['codi_deli'],'dete_inte'=>$_POST['dete_inte'],'cant_homb'=>$_POST['cant_homb'],'cant_muje'=>$_POST['cant_muje'],'digi_acta'=>$_SESSION['iden_oper'],'fdig_acta'=>"$fdig",'esta_acta'=>'1']);
			$_POST['codi_part']=$Db->lastInsertId();
		}
		if($_POST['regresar_reporte'])
		    $dire="elecciones_incidencias.php";
		else
		    $dire="elecciones_incidencias_nuevo.php";
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
	$result_documento=$Db->select('mp_elec_acta', ['codi_acta'=>$_POST['codi_acta']], '', '', '');
	$_POST['codi_loca']=$result_documento[0]['codi_loca'];
	$_POST['codi_tact']=$result_documento[0]['codi_tact'];
	$_POST['codi_deli']=$result_documento[0]['codi_deli'];
	$_POST['dete_inte']=$result_documento[0]['dete_inte'];
	$_POST['cant_homb']=$result_documento[0]['cant_homb'];
	$_POST['cant_muje']=$result_documento[0]['cant_muje'];
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
				if(document.form.codi_loca.selectedIndex=='0')
				{
					alert('Seleccione Local');
					document.form.codi_loca.focus();
					return false;
				}
				else
				{
				    if(document.form.codi_tact.selectedIndex=='0')
				    {
				        alert('Seleccione Tipo de Acta');
					    document.form.codi_tact.focus();
					    return false;
				    }
				    else
				    {
						if(document.form.codi_deli.selectedIndex=='0')
						{
							alert('Seleccione Motivo');
							document.form.codi_deli.focus();
							return false;
						}
						else
						{
						    if(document.form.file_doc1.value=='')
					        {
					            alert('Seleccione Archivo que contiene Acta');
					            document.form.file_doc1.focus();
					            return false;
					        }
					        else
					        {
					    		if(confirm('VERIFIQUE SU INFORMACION ANTES DE REGISTRARLA:\n\nLOCAL: '+document.form.codi_loca.options[document.form.codi_loca.selectedIndex].text+'\nTIPO DE ACTA: '+document.form.codi_tact.options[document.form.codi_tact.selectedIndex].text+'\nMOTIVO: '+document.form.codi_deli.options[document.form.codi_deli.selectedIndex].text+'\nDETENIDO/INTERVENIDO: '+document.form.dete_inte.options[document.form.dete_inte.selectedIndex].text+' / '+document.form.cant_homb.options[document.form.cant_homb.selectedIndex].text+' hombre(s) / '+document.form.cant_muje.options[document.form.cant_muje.selectedIndex].text+' mujer(es)\n\nDESEA GUARDAR?'))
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
				document.form.action='elecciones_incidencias_nuevo.php';
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
	<center><font style="color:#073A6B;font-weight: bold;"><?=$_POST['nomb_elec']?><BR><font style="font-size: 20;">REGISTRO DE ACTAS</font><br>USUARIO: <?=$_SESSION['logi_oper']?></font></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="regresar_reporte" value="<?=$_POST['regresar_reporte']?>">
			<input type=hidden name="codi_acta" value="<?=$_POST['codi_acta']?>">
			<main>
<?
	$html=new htmlclass;

    $arra_options_loca[0]="<- Seleccione Local ->";
	$result=$Db->query("select a.codi_loca,a.nomb_loca from mp_elec_locales as a,mp_elec_usuario_local as b WHERE a.codi_loca=b.codi_loca AND b.codi_usua='".$_SESSION['logi_oper']."'");
	foreach($result as $rows)
		$arra_options_loca[$rows['codi_loca']]=$rows['nomb_loca'];

	$arra_options_acta[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_elecciones_acta_tipo', '', '', '', ['n_codigo'=>'ASC']);
	foreach($result as $rows)
		$arra_options_acta[$rows['n_codigo']]=$rows['x_nombre'];
	
	$arra_options_moti[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_elecciones_intervencion', '', '', '', ['n_codigo'=>'ASC']);
	foreach($result as $rows)
		$arra_options_moti[$rows['n_codigo']]=$rows['n_codigo'].". ".$rows['x_nombre'];
		
	$arra_options_dete[0]="Ninguno";
	$arra_options_dete[1]="Detenido";
	$arra_options_dete[2]="Intervenido";
	
	$arra_options_cant[0]=0;
	$arra_options_cant[1]=1;
	$arra_options_cant[2]=2;
	$arra_options_cant[3]=3;
	$arra_options_cant[4]=4;
	$arra_options_cant[5]=5;
	$arra_options_cant[6]=6;
	$arra_options_cant[7]=7;
	$arra_options_cant[8]=8;
	$arra_options_cant[9]=9;

	echo $html->put_title_demand("Ingrese Ocurrencia");
	echo $html->put_select("Local",'codi_loca',$arra_options_loca,$_POST['codi_loca'],"");
	echo $html->put_select("Tipo&nbsp;de&nbsp;Acta",'codi_tact',$arra_options_acta,$_POST['codi_tact'],"");
	echo $html->put_select("Motivo",'codi_deli',$arra_options_moti,$_POST['codi_deli'],"");
	echo $html->put_title_demand("Detenido o Intervenido?");
	echo $html->put_select("Detenido/Intervenido",'dete_inte',$arra_options_dete,$_POST['dete_inte'],"");
	echo $html->put_select("Cantidad&nbsp;Hombres",'cant_homb',$arra_options_cant,$_POST['cant_homb'],"");
	echo $html->put_select("Cantidad&nbsp;Mujeres",'cant_muje',$arra_options_cant,$_POST['cant_muje'],"");
	echo $html->put_title_demand("Subir Acta");
	echo $html->put_upload_file("Hoja&nbsp;1",'file_doc1','','');
	echo $html->put_upload_file("Hoja&nbsp;2",'file_doc2','','');
	echo $html->put_upload_file("Hoja&nbsp;3",'file_doc3','','');
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
