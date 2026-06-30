<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	if(!empty($_POST['saveinfo']))	//guardar
	{
		$fdig=date("YmdHis");
		if($_POST['iden_vers']==9999)	//nuevo
		{
			$result=$Db->insert('iso_norm_vers',['codi_vers'=>$_POST['codi_vers'],'nomb_vers'=>$_POST['nomb_vers'],'desc_vers'=>$_POST['desc_vers'],'icon_vers'=>$_POST['icon_vers'],'orde_vers'=>$_POST['orde_vers'],'esta_vers'=>$_POST['esta_vers'],'digi_vers'=>$_SESSION['iden_oper'],'fdig_vers'=>$fdig]);
			unset($_POST);
		}
		else	//update
		{
			$result=$Db->update('iso_norm_vers',['codi_vers'=>$_POST['codi_vers'],'nomb_vers'=>$_POST['nomb_vers'],'desc_vers'=>$_POST['desc_vers'],'icon_vers'=>$_POST['icon_vers'],'orde_vers'=>$_POST['orde_vers'],'esta_vers'=>$_POST['esta_vers'],'digi_vers'=>$_SESSION['iden_oper'],'fdig_vers'=>$fdig],['iden_vers'=>$_POST['iden_vers']]);
			unset($_POST);
		}
	}
	if(!empty($_POST['iden_vers']))
	{
		$result=$Db->select('iso_norm_vers',['iden_vers'=>$_POST[iden_vers]]);
		$_POST['iden_norm']=$result[0]['iden_norm'];
		$_POST['codi_vers']=$result[0]['codi_vers'];
		$_POST['nomb_vers']=$result[0]['nomb_vers'];
		$_POST['desc_vers']=$result[0]['desc_vers'];
		$_POST['icon_vers']=$result[0]['icon_vers'];
		$_POST['orde_vers']=$result[0]['orde_vers'];
		$_POST['esta_vers']=$result[0]['esta_vers'];
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
<h2><?=CONST_TITLE_VERS?></h2><br>
		</center>
<?
	//--------------
	//$result=$Db->select('iso_norm_vers','','','',['orde_vers'=>'ASC']);
	$result=$Db->query("select iden_vers,CONCAT(codi_norm,' : ',codi_vers) as nomb_vers from iso_norm as a,iso_norm_vers as b where a.iden_norm=b.iden_norm");
	$arra_options_vers[0]="<- ".CONST_OPTION_SELECT." ->";
	$arra_options_vers[9999]="<- ".CONST_OPTION_NEW." ->";
	foreach ($result as $rows)
		$arra_options_vers[$rows['iden_vers']]=$rows['nomb_vers'];

	//--------------
	$result=$Db->select('iso_norm','','','',['orde_norm'=>'ASC']);
	$arra_options_norm[0]="<- ".CONST_OPTION_SELECT." ->";
	foreach ($result as $rows)
		$arra_options_norm[$rows['iden_norm']]=$rows['nomb_norm'];

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

	$html=new htmlclass;
	echo $html->put_select(CONST_SUBTITLE_VERS,'iden_vers',$arra_options_vers,$_POST['iden_vers'],"onchange=\"document.form.submit()\" required");
	if(!empty($_POST['iden_vers']))
	{
		//-------------------------------
		echo $html->put_title(CONST_SUBTITLE_BASIC_INFORMATION);
		echo $html->put_select(CONST_SUBTITLE_NORM,'iden_norm',$arra_options_norm,$_POST['iden_norm'],"required");
		echo $html->put_text('text',CONST_SUBTITLE_CODE,CONST_PLACEHOLDER_CODE,'codi_vers',$_POST['codi_vers'],'','50','required pattern="[A-Za-z0-9 ]+" title="Solo letras y Números"');
		echo $html->put_text('text',CONST_SUBTITLE_NAME,CONST_PLACEHOLDER_NAME,'nomb_vers',$_POST['nomb_vers'],'','50','required pattern="[A-Za-z0-9 ]+" title="Solo letras y Números"');
		echo $html->put_text('text',CONST_SUBTITLE_DESC,CONST_PLACEHOLDER_DESC,'desc_vers',$_POST['desc_vers'],'','100','required pattern="[A-Za-z0-9 ]+" title="Solo letras y Números"');
		echo $html->put_select(CONST_SUBTITLE_ICON,'icon_vers',$arra_options_icon,$_POST['icon_vers'],"required");
		echo $html->put_text('number',CONST_SUBTITLE_ORDER,CONST_PLACEHOLDER_ORDER,'orde_vers',$_POST['orde_vers'],'','3','required');
		echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_vers',$_POST['esta_vers'],CONST_OPTION_ENABLE,CONST_OPTION_DISABLE);
		echo"<BR><BR>";
		echo $html->put_submit(CONST_BUTTON_SAVE,'check()');
	}
?>
		</form>
	</body>
</html>
