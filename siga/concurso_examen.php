<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	if(!empty($_POST['saveinfo']))	//guardar
	{
		$fdig=date("Ymdhis");
		if($_POST['codi_exam']==9999)	//nuevo
		{
			$result=$Db->insert('mp_concurso_examen',['nomb_exam'=>$_POST['nomb_exam'],'fech_exam'=>$_POST['fech_exam'],'acti_exam'=>$_POST['acti_exam'],'digi_exam'=>$_SESSION['iden_oper'],'fdig_exam'=>$fdig,'esta_exam'=>$_POST['esta_exam']]);
			unset($_POST);
		}
		else	//update
		{
			$result=$Db->update('mp_concurso_examen',['nomb_exam'=>$_POST['nomb_exam'],'fech_exam'=>$_POST['fech_exam'],'acti_exam'=>$_POST['acti_exam'],'digi_exam'=>$_SESSION['iden_oper'],'fdig_exam'=>$fdig,'esta_exam'=>$_POST['esta_exam']],['codi_exam'=>$_POST['codi_exam']]);
			unset($_POST);
		}
	}
	if(!empty($_POST['codi_exam']))
	{
		$result=$Db->select('mp_concurso_examen',['codi_exam'=>$_POST['codi_exam']]);
		if($result) {
			$_POST['nomb_exam']=$result[0]['nomb_exam'];
			$_POST['fech_exam']=$result[0]['fech_exam'];
			$_POST['acti_exam']=$result[0]['acti_exam'];
			$_POST['esta_exam']=$result[0]['esta_exam'];
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
<h2>Administrar Exámenes
<br><font color=silver>Exámen -> Proceso -> Plaza</font>
</h2>
<br>
		</center>
<?
	//$result=$Db->select(['mp_admi_oper',['n_codi_ginstruccion','x_ginstruccion']],['n_estado'=>1]);
	//--------------
	$result=$Db->select('mp_concurso_examen','','','',['fech_exam'=>'ASC']);
	$arra_options_menu[0]="<- ".CONST_OPTION_SELECT." ->";
	$arra_options_menu[9999]="<- ".CONST_OPTION_NEW." ->";
	foreach ($result as $rows)
		$arra_options_menu[$rows['codi_exam']]="[".$rows['fech_exam']."] ".$rows['nomb_exam'];

	$html=new htmlclass;
	echo $html->put_select('Seleccione Exámen: ','codi_exam',$arra_options_menu,(isset($_POST['codi_exam']) ? $_POST['codi_exam'] : ''),"onchange=\"document.form.submit()\" required");
	if(!empty($_POST['codi_exam']))
	{
		//-------------------------------
		echo $html->put_title(CONST_SUBTITLE_BASIC_INFORMATION);
		echo $html->put_text('text',CONST_SUBTITLE_NAME,CONST_PLACEHOLDER_NAME,'nomb_exam',$_POST['nomb_exam'],'','50','required');
		//echo $html->put_select(CONST_SUBTITLE_ICON,'icon_menu',$arra_options_icon,$_POST['icon_menu'],"required");
		echo $html->put_text('date',"Fecha",CONST_PLACEHOLDER_ORDER,'fech_exam',$_POST['fech_exam'],'','3','required');
		echo $html->put_select_estado("Habilitado",'acti_exam',$_POST['acti_exam'],"Activo","Inactivo");
		echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_exam',$_POST['esta_exam'],CONST_OPTION_ENABLE,CONST_OPTION_DISABLE);
		echo"<BR><BR>";
		echo $html->put_submit(CONST_BUTTON_SAVE,'check()');
		
	}
?>
		</form>
	</body>
</html>
