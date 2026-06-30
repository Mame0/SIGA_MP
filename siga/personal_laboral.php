<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	require_once 'include/registrar_acceso.php';

	// Inicializar flag_admi para evitar warnings y simplificar la lógica
	$_POST['flag_admi'] = $_POST['flag_admi'] ?? $_GET['flag_admi'] ?? 0;
	$_POST['busq_tipo'] = $_POST['busq_tipo'] ?? '';
	$_POST['busq_dato'] = $_POST['busq_dato'] ?? '';
	$_POST['codi_depe'] = $_POST['codi_depe'] ?? '';
	$_POST['busq_pagi_actu'] = $_POST['busq_pagi_actu'] ?? '';
	$_POST['codi_form'] = $_POST['codi_form'] ?? '';

	$fdig=date("YmdHis");
	
	if(isset($_POST['iden_pers_edit']) && $_POST['iden_pers_edit'])
	{
		unset($_POST['iden_pers']);
		$_SESSION['iden_pers_edit']=$_POST['iden_pers_edit'];
	}
	
	if(!isset($_POST['iden_pers']))
	{
		if($_POST['flag_admi'] == 1) //si es administrador
		{
			if(isset($_SESSION['iden_pers_edit']) && $_SESSION['iden_pers_edit'])
				$_POST['iden_pers']=$_SESSION['iden_pers_edit'];
			else
			{
				//header("Location: personal_buscar.php");
				//echo"<script>window.location.replace(\"personal_buscar.php\");</script>";
				//echo"<HR>holaaaaa<HR>";
				//exit();
				echo"
					<html><body>
					<form name=\"form\" method=post action=\"personal_buscar.php\">
						<input type=hidden name=\"iden_pers\" value=\"".htmlspecialchars($_POST['iden_pers'] ?? '')."\">
						<input type=hidden name=\"flag_admi\" value=\"".$_POST['flag_admi']."\">
						<input type=hidden name=\"dire_orig\" value=\"personal_laboral.php\">
					</form>
					<script>
						document.form.submit();
					</script>
					</body></html>
				";
				exit;
			}
		}
		else
		{
			$result=$Db->query("select * from mp_admi_pers where ndoc_pers='$_SESSION[ndoc_oper]'");
			foreach($result as $rows)
				$_POST['iden_pers']=$rows['iden_pers'];
		}
	}
	// Se inicializa $ro para evitar warnings si flag_admi es 1
	$ro = "";
	if($_POST['flag_admi'] != 1)
		$ro = "disabled";

	if(!empty($_POST['guardar_personal']))
	{
		$fdig=date("YmdHis");
		//$_POST['esta_pers']=1;
		$_POST['fing_pers']=str_replace("-","",$_POST['fing_pers']);
		if($_POST['iden_pers'])
		{
			$result=$Db->update('mp_admi_pers',['iden_depe'=>$_POST['iden_depe'],'iden_rlab'=>$_POST['iden_rlab'],'iden_carg'=>$_POST['iden_carg'],'fing_pers'=>$_POST['fing_pers'],'iden_pres'=>$_POST['iden_pres'],'iden_modtrab'=>$_POST['iden_modtrab'],'iden_sind'=>$_POST['iden_sind'],'essa_pers'=>$_POST['essa_pers'],'iden_poli'=>$_POST['iden_poli'],'teps_pers'=>$_POST['teps_pers']],['iden_pers'=>$_POST['iden_pers']]);
		}
		else
		{
			$result=$Db->insert('mp_admi_pers',['iden_depe'=>$_POST['iden_depe'],'iden_rlab'=>$_POST['iden_rlab'],'iden_carg'=>$_POST['iden_carg'],'fing_pers'=>$_POST['fing_pers'],'iden_pres'=>$_POST['iden_pres'],'iden_modtrab'=>$_POST['iden_modtrab'],'iden_sind'=>$_POST['iden_sind'],'essa_pers'=>$_POST['essa_pers'],'iden_poli'=>$_POST['iden_poli'],'teps_pers'=>$_POST['teps_pers']]);
			$_POST['iden_pers']=$Db->lastInsertId();
		}

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
						<html><body>
								<form name=\"form\" method=post action=\"personal_laboral.php\">
					<input type=hidden name=\"iden_pers\" value=\"".($_POST['iden_pers'] ?? '')."\">
					<input type=hidden name=\"busq_tipo\" value=\"".($_POST['busq_tipo'] ?? '')."\">
					<input type=hidden name=\"busq_dato\" value=\"".($_POST['busq_dato'] ?? '')."\">
					<input type=hidden name=\"busq_pagi_actu\" value=\"".($_POST['busq_pagi_actu'] ?? '')."\">
					<input type=hidden name=\"codi_form\" value=\"".($_POST['codi_form'] ?? '')."\">
					<input type=hidden name=\"flag_admi\" value=\"".($_POST['flag_admi'] ?? '')."\">
					<input type=hidden name=\"dire_orig\" value=\"personal_laboral.php\">
								</form>
								<script>
										document.form.submit();
								</script>
						</body></html>
		";

	}
	$result_personal=$Db->select('mp_admi_pers', ['iden_pers'=>$_POST['iden_pers']], '', '', '');
	$_POST['appa_pers']=$result_personal[0]['appa_pers'];
	$_POST['apma_pers']=$result_personal[0]['apma_pers'];
	$_POST['nomb_pers']=$result_personal[0]['nomb_pers'];
	
	$_POST['iden_depe']=$result_personal[0]['iden_depe'];
	$_POST['iden_rlab']=$result_personal[0]['iden_rlab'];
	$_POST['iden_carg']=$result_personal[0]['iden_carg'];
	//$_POST['fing_pers']=$result_personal[0]['fing_pers'];
	$_POST['fing_pers']=substr($result_personal[0]['fing_pers'],0,4).'-'.substr($result_personal[0]['fing_pers'],4,2).'-'.substr($result_personal[0]['fing_pers'],6,2);
	$_POST['iden_pres']=$result_personal[0]['iden_pres'];
	$_POST['iden_modtrab']=$result_personal[0]['iden_modtrab'];
	$_POST['iden_sind']=$result_personal[0]['iden_sind'];
	$_POST['essa_pers']=$result_personal[0]['essa_pers'];
	$_POST['iden_poli']=$result_personal[0]['iden_poli'];
	$_POST['teps_pers']=$result_personal[0]['teps_pers'];
	
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
			   if(document.form.iden_depe.selectedIndex=='0') {
				   alert('Seleccione Dependencia');
				   document.form.iden_depe.focus();
				   return false;
			   }
			   if(document.form.iden_rlab.selectedIndex=='0') {
				   alert('Seleccione Régimen');
				   document.form.iden_rlab.focus();
				   return false;
			   }
			   if(document.form.iden_carg.selectedIndex=='0') {
				   alert('Seleccione Cargo');
				   document.form.iden_carg.focus();
				   return false;
			   }
			   if(document.form.fing_pers.value=='' || document.form.fing_pers.value=='0000-00-00') {
				   alert('Seleccione Fecha de Ingreso');
				   document.form.fing_pers.focus();
				   return false;
			   }			   
			   if(confirm('Seguro que desea Guardar')) {
				   document.form.guardar_personal.value='1';
				   document.form.submit();
			   } else {
				   return false;
			   }
		   }
			function f_cancelar_documento()
			{
				document.form.action='personal_buscar.php';
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
		echo"Informaci&oacute;n Laboral<BR>".$_POST['appa_pers']." ".$_POST['apma_pers'].", ".$_POST['nomb_pers'];
	else
		echo"Crear Nuevo Personal";
?>
	</h4></b></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="iden_pers" value="<?=$_POST['iden_pers']?>">
			<!--<input type=hidden name="busq_tipo" value="<?=$_POST['busq_tipo']?>">
			<input type=hidden name="busq_dato" value="<?=$_POST['busq_dato']?>">
			<input type=hidden name="codi_depe" value="<?=$_POST['codi_depe']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">
			<input type=hidden name="codi_form" value="<?=$_POST['codi_form']?>">-->
			<input type=hidden name="flag_admi" value="<?=$_POST['flag_admi']?>">
			<input type=hidden name="dire_orig" value="personal_laboral.php">
			<main>
<?
	$html=new htmlclass;

	$arra_options_depe[0]="<- Seleccione ->";
	/*
	$result=$Db->query("select * from mp_admi_depe");
	foreach($result as $rows)
		$arra_options_depe[$rows['codi_depe']]=utf8_encode(utf8_decode($rows['nomb_depe']));
	*/
	$result1=$Db->query("select * from mp_admi_depe where codi_padr=0 AND esta_depe=1 order by nomb_depe");
	$separador="|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	foreach($result1 as $rows1)
	{   
		if(strlen($rows1['abre_depe'])>70)  $rows1['abre_depe']=substr($rows1['abre_depe'],0,70).'...'; 
		$arra_options_depe[$rows1['codi_depe']]=$rows1['abre_depe'];
		$result2=$Db->query("select * from mp_admi_depe where codi_padr='".$rows1['codi_depe']."' AND esta_depe=1 order by nomb_depe");
		foreach($result2 as $rows2)
		{
			if(strlen($rows2['abre_depe'])>70)  $rows2['abre_depe']=substr($rows2['abre_depe'],0,70).'...';
			$arra_options_depe[$rows2['codi_depe']]=$separador.$rows2['abre_depe'];
			$result3=$Db->query("select * from mp_admi_depe where codi_padr='".$rows2['codi_depe']."' AND esta_depe=1 order by nomb_depe");
			foreach($result3 as $rows3)
			{
				if(strlen($rows3['abre_depe'])>70)  $rows3['abre_depe']=substr($rows3['abre_depe'],0,70).'...';
				$arra_options_depe[$rows3['codi_depe']]=$separador.$separador.$rows3['abre_depe'];
				$result4=$Db->query("select * from mp_admi_depe where codi_padr='".$rows3['codi_depe']."' AND esta_depe=1 order by nomb_depe");
				foreach($result4 as $rows4)
				{
					if(strlen($rows4['abre_depe'])>70)  $rows4['abre_depe']=substr($rows4['abre_depe'],0,70).'...';
					$arra_options_depe[$rows4['codi_depe']]=$separador.$separador.$separador.$rows4['abre_depe'];
					$result5=$Db->query("select * from mp_admi_depe where codi_padr='".$rows4['codi_depe']."' AND esta_depe=1 order by nomb_depe");
					foreach($result5 as $rows5)
					{
						if(strlen($rows5['abre_depe'])>70)  $rows5['abre_depe']=substr($rows5['abre_depe'],0,70).'...';
						$arra_options_depe[$rows5['codi_depe']]=$separador.$separador.$separador.$separador.$rows5['abre_depe'];
						$result6=$Db->query("select * from mp_admi_depe where codi_padr='".$rows5['codi_depe']."' AND esta_depe=1 order by nomb_depe");
						foreach($result6 as $rows6)
						{
							if(strlen($rows6['abre_depe'])>70)  $rows6['abre_depe']=substr($rows6['abre_depe'],0,70).'...';
							$arra_options_depe[$rows6['codi_depe']]=$separador.$separador.$separador.$separador.$separador.$rows6['abre_depe'];
							//echo"<tr>$separador$separador$separador$separador$separador<td width=1%><input type=checkbox name=\"chec_depe_".$rows6['codi_depe']."\" ".$arra_depe[$rows6['codi_depe']]."></td><td width=100% colspan=$colu style=\"font-size:small\">".$rows6['abre_depe']."</td></tr>";
						}
						
						
						
					}
				}
			}
		}
	}
	
	$arra_options_essa[1]="SI";
	$arra_options_essa[0]="NO";
	
	$arra_options_pres[1]="AREQUIPA";
	$arra_options_pres[2]="LIMA";
	
	$arra_options_sind[1]="SI";
	$arra_options_sind[0]="NO";

	$arra_options_teps[0]="NO";
	$arra_options_teps[1]="SI";
	
	$arra_options_regi=$Db->get_options('mp_maes_regimen_laboral',1,0);
	$arra_options_modtrab=$Db->get_options('mp_maes_modalidad_trabajo',1,0);
	//$arra_options_pres=$Db->get_options('mp_maes_personal_presupuesto',1,0);
	$arra_options_poli=$Db->get_options('mp_maes_essalud_sedes',1,0);
	
	$arra_options_carg[0]="<- Seleccione ->";
	$arra_options_carg[-1]="<- Seleccione ->";
	$result=$Db->select('mp_maes_cargo', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_carg[$rows['n_codigo']]=utf8_encode(utf8_decode($rows['x_nombre']));

	echo $html->put_title_demand("Informaci&oacute;n Laboral");
	echo $html->put_select("Dependencia",'iden_depe',$arra_options_depe,$_POST['iden_depe'],"$ro");
	echo $html->put_select("Régimen",'iden_rlab',$arra_options_regi,$_POST['iden_rlab'],"$ro");
	echo $html->put_select_buscador("Cargo",'iden_carg',$arra_options_carg,$_POST['iden_carg'],"$ro");
	echo"</main><main>";
	echo $html->put_text('date',"Fecha&nbsp;de&nbsp;Ingreso","",'fing_pers',$_POST['fing_pers'],'','20',"$ro");
	echo $html->put_select("Plaza&nbsp;con&nbsp;Presupuesto",'iden_pres',$arra_options_pres,$_POST['iden_pres'],"$ro");
	echo $html->put_select("Modalidad&nbsp;de&nbsp;Trabajo",'iden_modtrab',$arra_options_modtrab,$_POST['iden_modtrab'],"$ro");
	echo"</main><main>";
	echo $html->put_select("Sindicalizado",'iden_sind',$arra_options_sind,$_POST['iden_sind'],"$ro");
	echo $html->put_title_demand("Informaci&oacute;n de Seguro");
	echo $html->put_select("Pertenece&nbsp;a&nbsp;ESSALUD",'essa_pers',$arra_options_essa,$_POST['essa_pers'],"$ro");
	echo $html->put_select("Policlinico&nbsp;ESSALUD",'iden_poli',$arra_options_poli,$_POST['iden_poli'],"$ro");
	echo $html->put_select("Tiene&nbsp;EPS?",'teps_pers',$arra_options_teps,$_POST['teps_pers'],"$ro");

	echo"</main>";

	echo $html->put_separator_demand("30");
	if($_POST['flag_admi']==1) //si es administrador
	{
				echo"
						<div align=center class=\"foot\">
								<center>
								<div align=center class=\"foot2\">
										<div class=\"div_button_foot\" style=\"\">
												<button class=\"button_foot\" onclick=\"f_cancelar_documento()\">&laquo; Nueva B&uacute;squeda</button>
										</div>
										<div class=\"div_button_foot\"><center>
												<button class=\"button_foot\" onclick=\"return f_guardar_personal()\">Guardar &raquo;</button>
										</div>
								</div>
						</div>
				";
	}
?>
<center>
	</form>
	</body>
</html>
