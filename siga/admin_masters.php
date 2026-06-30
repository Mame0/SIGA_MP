<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	if(!empty($_POST['saveinfo']))	//guardar
	{
		$fdig=date("YmdHis");
		
		if($_POST['n_codigo']==9999)   //nuevo
		{
			$result=$Db->insert($_POST['iden_tabl'],['x_nombre'=>$_POST['x_nombre'],'n_estado'=>$_POST['n_estado']]);
			$_POST['n_codigo']=$Db->lastInsertId();
		}
		else	//update
		{	
			$result=$Db->update($_POST['iden_tabl'],['x_nombre'=>$_POST['x_nombre'],'n_estado'=>$_POST['n_estado']],['n_codigo'=>$_POST['n_codigo']]);
		}

		if($result)
		{
			echo"<script>alert('".constant("CONST_MENS_REG_OK")."')</script>";
			unset($_POST['n_codigo'],$_POST['x_nombre'],$_POST['n_estado']);
		}
		else
			echo"<script>alert('".constant("CONST_MENS_REG_ERROR")."')</script>";
	}
	if(!empty($_POST['n_codigo']))
	{
		$result=$Db->select($_POST['iden_tabl'],['n_codigo'=>$_POST['n_codigo']]);
		$_POST['x_nombre']=$result[0]['x_nombre'];
		$_POST['n_estado']=$result[0]['n_estado'];
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>SIOJAlimentos</title>
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
<h2><?=CONST_TITLE_USER?></h2><br>
		</center>
<?
	$result=$Db->query("show tables like 'mp_maes%'");
	$arra_options_tabl[0]="<- Seleccione Tabla ->";
	foreach ($result as $rows => $tabl)
	{
		foreach ($tabl as $cod => $dat)
			$arra_options_tabl[$dat]=$dat;
	}

	$html=new htmlclass;
	//echo $html->put_select(CONST_SUBTITLE_TABLE,'iden_tabl',$arra_options_tabl,$_POST['iden_tabl'],"onchange=\"document.form.n_codigo.value='';document.form.submit()\" required");
	echo $html->put_select(CONST_SUBTITLE_TABLE,'iden_tabl',$arra_options_tabl,$_POST['iden_tabl'],"onchange=\"document.form.submit()\" required");
	if(!empty($_POST['iden_tabl']))
	{
		$arra_options_codigo=$Db->get_options($_POST['iden_tabl'],0,1);
		//-------------------------------
		echo $html->put_title(CONST_SUBTITLE_SELECT_OPTION);
		echo $html->put_select(CONST_SUBTITLE_NAME,'n_codigo',$arra_options_codigo,$_POST['n_codigo'],"onchange=\"document.form.submit()\" required");
		if(!empty($_POST['n_codigo']))
		{
			echo $html->put_title(CONST_SUBTITLE_BASIC_INFORMATION);
			echo $html->put_text('text',CONST_SUBTITLE_NAME,CONST_PLACEHOLDER_NAME,'x_nombre',$_POST['x_nombre'],'','300','required title="Solo letras"');
			echo $html->put_select_estado(CONST_SUBTITLE_STATE,'n_estado',$_POST['n_estado'],CONST_OPTION_ENABLE,CONST_OPTION_DISABLE);
			//-------------------------------
			echo"<BR><BR>";
			echo $html->put_submit(CONST_BUTTON_SAVE,'check()');
		}
	}
?>
		</form>
	</body>
</html>
