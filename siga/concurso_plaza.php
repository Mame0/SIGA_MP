<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	if(!empty($_POST['saveinfo']))	//guardar
	{
		$fdig=date("Ymdhis");
		if($_POST['codi_plaz']==9999)	//nuevo
		{
			$result=$Db->insert('mp_concurso_plazas',['codi_proc'=>$_POST['codi_proc'],'nomb_plaz'=>$_POST['nomb_plaz'],'codi_carg'=>$_POST['codi_carg'],'digi_plaz'=>$_SESSION['iden_oper'],'fdig_plaz'=>$fdig,'esta_plaz'=>$_POST['esta_plaz']]);
			unset($_POST);
		}
		else	//update
		{
			$result=$Db->update('mp_concurso_plazas',['codi_proc'=>$_POST['codi_proc'],'nomb_plaz'=>$_POST['nomb_plaz'],'codi_carg'=>$_POST['codi_carg'],'digi_plaz'=>$_SESSION['iden_oper'],'fdig_plaz'=>$fdig,'esta_plaz'=>$_POST['esta_plaz']],['codi_plaz'=>$_POST['codi_plaz']]);
			unset($_POST);
		}
	}
	if(!empty($_POST['codi_plaz']))
	{
		$result=$Db->select('mp_concurso_plazas',['codi_plaz'=>$_POST['codi_plaz']]);
		if($result) {
			$_POST['codi_proc']=$result[0]['codi_proc'];
			$_POST['nomb_plaz']=$result[0]['nomb_plaz'];
			$_POST['codi_carg']=$result[0]['codi_carg'];
			$_POST['esta_plaz']=$result[0]['esta_plaz'];
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title></title>
		<link rel="stylesheet" href="css/forms.css" />
		<script>
			function check()
			{
				document.form.saveinfo.value=1;
				return true;
			}
		</script>
	</head>
	<body>
		<form name="form" method="post">
			<input type=hidden name="saveinfo">
		<center>
<h2>Administrar Plazas
<br><font color=silver>Exámen -> Proceso -> Plaza</font></h2><br>
		</center>
<?
	//$result=$Db->select(['mp_admi_oper',['n_codi_ginstruccion','x_ginstruccion']],['n_estado'=>1]);
	//--------------
	
	$result=$Db->select('mp_concurso_examen','','','',['fech_exam'=>'ASC']);
	$arra_options_exam[0]="<- ".CONST_OPTION_SELECT." ->";
	foreach ($result as $rows)
		$arra_options_exam[$rows['codi_exam']]="[".$rows['fech_exam']."] ";
	
	$arra_options_regi[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_concurso_regimen', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_regi[$rows['n_codigo']]=$rows['x_nombre'];
	
	$result=$Db->select('mp_concurso_proceso','','','',['codi_proc'=>'ASC']);
	$arra_options_proc[0]="<- ".CONST_OPTION_SELECT." ->";
	foreach ($result as $rows)
		$arra_options_proc[$rows['codi_proc']]=$arra_options_exam[$rows['codi_exam']]." ".$arra_options_regi[$rows['regi_proc']]." Nro. ".$rows['nume_proc']."-".$rows['anno_proc'];
	
	$arra_options_carg[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_fotocheck_cargo', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_carg[$rows['n_codigo']]=$rows['x_nombre'];
		
	$result=$Db->select('mp_concurso_plazas','','','',['codi_plaz'=>'ASC']);
	$arra_options_menu[0]="<- ".CONST_OPTION_SELECT." ->";
	$arra_options_menu[9999]="<- ".CONST_OPTION_NEW." ->";
	foreach ($result as $rows)
		$arra_options_menu[$rows['codi_plaz']]=$arra_options_proc[$rows['codi_proc']]." - ".$rows['nomb_plaz']." - [".$arra_options_carg[$rows['codi_carg']]."]";

	$html=new htmlclass;
	echo $html->put_select('Seleccione Plaza: ','codi_plaz',$arra_options_menu,(isset($_POST['codi_plaz']) ? $_POST['codi_plaz'] : ''),"onchange=\"document.form.submit()\" required");
	if(!empty($_POST['codi_plaz']))
	{
		//-------------------------------
		echo $html->put_title(CONST_SUBTITLE_BASIC_INFORMATION);
		echo $html->put_select('Seleccione Proceso: ','codi_proc',$arra_options_proc,$_POST['codi_proc'],"required");
		echo $html->put_text('text',"Nombre","Ejemplo: AFF1",'nomb_plaz',$_POST['nomb_plaz'],'','15','required');
		echo $html->put_select('Cargo','codi_carg',$arra_options_carg,$_POST['codi_carg'],"required");
		
		echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_plaz',$_POST['esta_plaz'],CONST_OPTION_ENABLE,CONST_OPTION_DISABLE);
		echo"<BR><BR>";
		echo $html->put_submit(CONST_BUTTON_SAVE,'check()');
	}
?>
		</form>
	</body>
</html>
