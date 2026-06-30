<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	if(!empty($_POST['saveinfo']))	//guardar
	{
		$result=$Db->update('mp_admi_conf',['valo_conf'=>$_POST['conf_time']],['iden_conf'=>1]);
		$result=$Db->update('mp_admi_conf',['valo_conf'=>$_POST['conf_lang']],['iden_conf'=>2]);
		$result=$Db->update('mp_admi_conf',['valo_conf'=>$_POST['conf_item']],['iden_conf'=>4]);
		$result=$Db->update('mp_admi_conf',['valo_conf'=>$_POST['nomb_anno']],['iden_conf'=>5]);
		unset($_POST);
	}
	
	$result=$Db->select('mp_admi_conf');
	foreach($result as $rows => $valo)
		$valo_conf[$valo['iden_conf']]=$valo['valo_conf'];
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>ISOlutions</title>
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
<h2><?=CONST_TITLE_SETTINGS?></h2><br>
		</center>
<?
	$directorio = opendir("./include/languages"); //ruta actual
	while ($archivo = readdir($directorio)) //obtenemos un archivo y luego otro sucesivamente
	{
		if (!is_dir($archivo))//verificamos si es o no un directorio
		{
			if(substr($archivo,0,1)!='.' AND substr($archivo,-4)=='.php')
			{
				$archivo=substr($archivo,0,-4);
				$arra_options_lang[$archivo]=$archivo; 
			}
		}
	}

		//-------------------------------
	$html=new htmlclass;
		echo $html->put_select(CONST_SUBTITLE_LANG,'conf_lang',$arra_options_lang,$valo_conf[2],"");
		echo $html->put_text('number',CONST_SUBTITLE_TIME,CONST_PLACEHOLDER_TIME,'conf_time',$valo_conf[1],'','','required');
		echo $html->put_text('number',CONST_SUBTITLE_ITEMS_X_PAGINA,'','conf_item',$valo_conf[4],'','','required');
		echo $html->put_text('text',CONST_SUBTITLE_NOMB_ANNO,'','nomb_anno',$valo_conf[5],'','','required');

	echo"<BR>";
	echo $html->put_submit(CONST_BUTTON_SAVE,'check()');
?>
	</body>
</html>
