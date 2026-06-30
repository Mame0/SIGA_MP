<?php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	if(!empty($_POST['saveinfo']))	//guardar
	{
		$fdig=date("Ymdis");
		if($_POST['iden_menu']==9999)	//nuevo
		{
			$result=$Db->insert('mp_admi_menu',['nomb_menu'=>$_POST['nomb_menu'],'icon_menu'=>$_POST['icon_menu'],'orde_menu'=>$_POST['orde_menu'],'esta_menu'=>$_POST['esta_menu']]);
			unset($_POST);
		}
		else	//update
		{
			$result=$Db->update('mp_admi_menu',['nomb_menu'=>$_POST['nomb_menu'],'icon_menu'=>$_POST['icon_menu'],'orde_menu'=>$_POST['orde_menu'],'esta_menu'=>$_POST['esta_menu']],['iden_menu'=>$_POST['iden_menu']]);
			unset($_POST);
		}
	}
	if(!empty($_POST['iden_menu']))
	{
		$result=$Db->select('mp_admi_menu',['iden_menu'=>$_POST['iden_menu']]);
		$_POST['nomb_menu']=$result[0]['nomb_menu'];
		$_POST['icon_menu']=$result[0]['icon_menu'];
		$_POST['orde_menu']=$result[0]['orde_menu'];
		$_POST['esta_menu']=$result[0]['esta_menu'];
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
<h2><?=CONST_TITLE_MENUS?></h2><br>
		</center>
<?php
	//$result=$Db->select(['mp_admi_oper',['n_codi_ginstruccion','x_ginstruccion']],['n_estado'=>1]);
	//--------------
	$result=$Db->select('mp_admi_menu','','','',['orde_menu'=>'ASC']);
	$arra_options_menu[0]="<- ".CONST_OPTION_SELECT." ->";
	$arra_options_menu[9999]="<- ".CONST_OPTION_NEW." ->";
	foreach ($result as $rows)
		$arra_options_menu[$rows['iden_menu']]=$rows['nomb_menu'];

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
	echo $html->put_select(CONST_SUBTITLE_MENU,'iden_menu',$arra_options_menu,(isset($_POST['iden_menu']) ? $_POST['iden_menu'] : ''),"onchange=\"document.form.submit()\" required");
	if(!empty($_POST['iden_menu']))
	{
		//-------------------------------
		echo $html->put_title(CONST_SUBTITLE_BASIC_INFORMATION);
		echo $html->put_text('text',CONST_SUBTITLE_NAME,CONST_PLACEHOLDER_NAME,'nomb_menu',$_POST['nomb_menu'],'','50','required pattern="[A-Za-z_]+" title="Solo letras"');
		echo $html->put_select(CONST_SUBTITLE_ICON,'icon_menu',$arra_options_icon,$_POST['icon_menu'],"required");
		echo $html->put_text('number',CONST_SUBTITLE_ORDER,CONST_PLACEHOLDER_ORDER,'orde_menu',$_POST['orde_menu'],'','3','required');
		echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_menu',$_POST['esta_menu'],CONST_OPTION_ENABLE,CONST_OPTION_DISABLE);
		echo"<BR><BR>";
		echo $html->put_submit(CONST_BUTTON_SAVE,'check()');
	}
?>
		</form>
	</body>
</html>