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
	
	$result_documento=$Db->select('mp_elec_difusion', ['codi_difu'=>$_POST['codi_difu']], '', '', '');
	$_POST['codi_tdif']=$result_documento[0]['codi_tdif'];
	$_POST['fech_difu']=substr($result_documento[0]['fech_difu'],0,4).'-'.substr($result_documento[0]['fech_difu'],4,2).'-'.substr($result_documento[0]['fech_difu'],6,2);
	$_POST['ubig_difu']=$result_documento[0]['ubig_difu'];
	$_POST['obse_difu']=$result_documento[0]['obse_difu'];
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
				if(document.form.codi_tdif.selectedIndex=='0')
				{
					alert('Seleccione Tipo');
					document.form.codi_tdif.focus();
					return false;
				}
				else
				{
				    if(document.form.ubig_difu.selectedIndex=='0')
				    {
				        alert('Seleccione Provincia');
					    document.form.ubig_difu.focus();
					    return false;
				    }
				    else
				    {
						if(document.form.fech_difu.value=='')
						{
							alert('Seleccione Fecha de Difusion');
							document.form.fech_difu.focus();
							return false;
						}
						else
						{
					    		if(confirm('VERIFIQUE SU INFORMACION ANTES DE REGISTRARLA:\n\nTIPO: '+document.form.codi_tdif.options[document.form.codi_tdif.selectedIndex].text+'\nPROVINCIA: '+document.form.ubig_difu.options[document.form.ubig_difu.selectedIndex].text+'\nFECHA: '+document.form.fech_difu.value+'\n\nDESEA GUARDAR?'))
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
				document.form.action='elecciones_difusion_reporte.php';
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
	<center><font style="color:#073A6B;font-weight: bold;"><?=$_POST['nomb_elec']?><BR><font style="font-size: 20;">ACTIVIDADES DE DIFUSION</font><br>REPORTE</font></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="regresar_reporte" value="<?=$_POST['regresar_reporte']?>">
			<input type=hidden name="codi_difu" value="<?=$_POST['codi_difu']?>">
			<main>
<?
	$html=new htmlclass;
	
	if(!$_POST['ubig_difu'])
	    $_POST['ubig_difu']='01';

	$arra_options_prov[0]="<- Seleccione ->";
	$result=$Db->query("select distinct cdep,cpro,cdis,prov,dist from ubig_reni WHERE cdep='04' AND cpro<>'00' AND cdis<>'00' order by cpro,cdis");
	foreach($result as $rows)
	{
	    $c=$rows['cdep'].$rows['cpro'].$rows['cdis'];
		$arra_options_prov[$c]=$rows['prov']." - ".$rows['dist'];
	}

	$arra_options_tdif[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_elecciones_difusion', '', '', '', ['n_codigo'=>'ASC']);
	foreach($result as $rows)
		$arra_options_tdif[$rows['n_codigo']]=$rows['x_nombre'];
	
	if(!$_POST['fech_difu'])
	    $_POST['fech_difu']=date("Y-m-d");

	echo $html->put_title_demand("Ingrese Información");
	echo $html->put_select("Tipo",'codi_tdif',$arra_options_tdif,$_POST['codi_tdif'],"disabled");
	echo $html->put_select("Ubicaci&oacute;n",'ubig_difu',$arra_options_prov,$_POST['ubig_difu'],"disabled");
	echo $html->put_text('date',"Fecha","Ingrese Fecha ",'fech_difu',$_POST['fech_difu'],'','8',' onchange="return solonumeros(this.value)" disabled');
    echo $html->put_title_demand("Observaciones");
    echo $html->put_textarea("",'obse_difu',$_POST['obse_difu'],'style="height: 100px;" disabled');
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
