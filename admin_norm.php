<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	if(!empty($_POST['saveinfo']))	//guardar
	{
		$fdig=date("YmdHis");
		if($_POST['iden_norm']==9999)	//nuevo
		{
			$result=$Db->insert('iso_norm',['codi_norm'=>$_POST['codi_norm'],'nomb_norm'=>$_POST['nomb_norm'],'desc_norm'=>$_POST['desc_norm'],'orde_norm'=>$_POST['orde_norm'],'esta_norm'=>$_POST['esta_norm'],'digi_norm'=>$_SESSION['iden_oper'],'fdig_norm'=>$fdig]);
			unset($_POST);
		}
		else	//update
		{
			$result=$Db->update('iso_norm',['codi_norm'=>$_POST['codi_norm'],'nomb_norm'=>$_POST['nomb_norm'],'desc_norm'=>$_POST['desc_norm'],'orde_norm'=>$_POST['orde_norm'],'esta_norm'=>$_POST['esta_norm'],'digi_norm'=>$_SESSION['iden_oper'],'fdig_norm'=>$fdig],['iden_norm'=>$_POST['iden_norm']]);
			unset($_POST);
		}
	}
	if(!empty($_POST['iden_norm']))
	{
		$result=$Db->select('iso_norm',['iden_norm'=>$_POST['iden_norm']]);
		$_POST['codi_norm']=$result[0]['codi_norm'];
		$_POST['nomb_norm']=$result[0]['nomb_norm'];
		$_POST['desc_norm']=$result[0]['desc_norm'];
		$_POST['orde_norm']=$result[0]['orde_norm'];
		$_POST['esta_norm']=$result[0]['esta_norm'];
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>ISOLutions</title>
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
<h2><?=CONST_TITLE_NORM?></h2><br>
		</center>
<?
	//--------------
	$result=$Db->select('iso_norm','','','',['orde_norm'=>'ASC']);
	$arra_options_norm[0]="<- ".CONST_OPTION_SELECT." ->";
	$arra_options_norm[9999]="<- ".CONST_OPTION_NEW." ->";
	foreach ($result as $rows)
		$arra_options_norm[$rows['iden_norm']]=$rows['nomb_norm'];

	/*
	$directorio = opendir("./img/icons"); //ruta actual
	while ($archivo = readdir($directorio)) //obtenemos un archivo y luego otro sucesivamente
	{
		if (!is_dir($archivo))//verificamos si es o no un directorio
		{
			if(substr($archivo,0,1)!='.' AND substr($archivo,-4)=='.svg')
			{
				$archivo=substr($archivo,0,-4);
				$arra_options_icon[$archivo]=$archivo; 
			}
		}
	}
	*/

	$html=new htmlclass;
	echo $html->put_select(CONST_SUBTITLE_NORM,'iden_norm',$arra_options_norm,$_POST['iden_norm'],"onchange=\"document.form.submit()\" required");
	if(!empty($_POST['iden_norm']))
	{
		//-------------------------------
		echo $html->put_title(CONST_SUBTITLE_BASIC_INFORMATION);
		echo $html->put_text('text',CONST_SUBTITLE_CODE,CONST_PLACEHOLDER_CODE,'codi_norm',$_POST['codi_norm'],'','50','required pattern="[A-Za-z0-9 ]+" title="Solo letras y Números"');
		echo $html->put_text('text',CONST_SUBTITLE_NAME,CONST_PLACEHOLDER_NAME,'nomb_norm',$_POST['nomb_norm'],'','50','required pattern="[A-Za-z0-9 ]+" title="Solo letras y Números"');
		echo $html->put_text('text',CONST_SUBTITLE_DESC,CONST_PLACEHOLDER_DESC,'desc_norm',$_POST['desc_norm'],'','100','required pattern="[A-Za-z0-9 ]+" title="Solo letras y Números"');
		//echo $html->put_select(CONST_SUBTITLE_ICON,'icon_norm',$arra_options_icon,$_POST['icon_norm'],"required");
		echo $html->put_text('number',CONST_SUBTITLE_ORDER,CONST_PLACEHOLDER_ORDER,'orde_norm',$_POST['orde_norm'],'','3','required');
		echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_norm',$_POST['esta_norm'],CONST_OPTION_ENABLE,CONST_OPTION_DISABLE);
		echo"<BR><BR>";
		echo $html->put_submit(CONST_BUTTON_SAVE,'check()');
	}
?>
		</form>
	</body>
</html>
