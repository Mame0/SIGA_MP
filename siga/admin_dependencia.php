<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	if(!empty($_POST['saveinfo']))	//guardar
	{
		$fdig=date("Ymdis");
		if($_POST['codi_depe']==9999)	//nuevo
		{
			$result=$Db->insert('mp_admi_depe',['nomb_depe'=>$_POST['nomb_depe'],'sigl_depe'=>$_POST['sigl_depe'],'codi_loca'=>$_POST['codi_loca'],'dire_depe'=>$_POST['dire_depe'],'tipo_depe'=>$_POST['tipo_depe'],'codi_padr'=>$_POST['codi_padr'],'esta_depe'=>$_POST['esta_depe'],'digi_depe'=>$_POST['digi_depe'],'fdig_depe'=>$fdig]);
			unset($_POST);
		}
		else	//update
		{
			$result=$Db->update('mp_admi_depe',['nomb_depe'=>$_POST['nomb_depe'],'sigl_depe'=>$_POST['sigl_depe'],'codi_loca'=>$_POST['codi_loca'],'dire_depe'=>$_POST['dire_depe'],'tipo_depe'=>$_POST['tipo_depe'],'codi_padr'=>$_POST['codi_padr'],'esta_depe'=>$_POST['esta_depe'],'digi_depe'=>$_POST['digi_depe'],'fdig_depe'=>$fdig],['codi_depe'=>$_POST['codi_depe']]);
			unset($_POST);
			//aqui correr una funcion que arregle todos los hijos
		}
	}
	if(!empty($_POST['codi_depe']))
	{
		$result=$Db->select('mp_admi_depe',['codi_depe'=>$_POST['codi_depe']]);
		$_POST['nomb_depe']=$result[0]['nomb_depe'];
		$_POST['sigl_depe']=$result[0]['sigl_depe'];
		$_POST['codi_loca']=$result[0]['codi_loca'];
		$_POST['dire_depe']=$result[0]['dire_depe'];
		$_POST['tipo_depe']=$result[0]['tipo_depe'];
		$_POST['esta_depe']=$result[0]['esta_depe'];
		$_POST['digi_depe']=$result[0]['digi_depe'];
		$_POST['codi_padr']=str_pad($result[0]['codi_padr'],4,'0',STR_PAD_LEFT);
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
<h2><?=CONST_TITLE_DEPENDENCIAS?></h2><br>
		</center>
<?
	$result=$Db->select('mp_admi_depe','','','',['codi_depe'=>'ASC']);
//$arra_depe[0]="prueba";

	foreach ($result as $rows)
	{
		$arra_depe[$rows['codi_padr']][$rows['codi_depe']]=$rows['nomb_depe'];
	}

	$separator="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

	$result_menu=$Db->select('mp_admi_menu','','','',['orde_menu'=>'ASC']);
	$arra_options_padr[0]="<- ".CONST_OPTION_SELECT." ->";
	$arra_options_depe[0]="<- ".CONST_OPTION_SELECT." ->";
	$arra_options_depe[9999]="<- ".CONST_OPTION_NEW." ->";
	foreach($arra_depe[0] as $codi => $nomb)
	{
		$arra_options_depe[$codi]=$nomb;
		$arra_options_padr[str_pad($codi,4,'0',STR_PAD_LEFT)]="$separator".$nomb;
		foreach($arra_depe[$codi] as $codi => $nomb)
		{
			$arra_options_depe[$codi]="$separator".$nomb;
			$arra_options_padr[str_pad($codi,4,'0',STR_PAD_LEFT)]="$separator$separator".$nomb;
			foreach($arra_depe[$codi] as $codi => $nomb)
			{
				$arra_options_depe[$codi]="$separator$separator".$nomb;
				$arra_options_padr[str_pad($codi,4,'0',STR_PAD_LEFT)]="$separator$separator$separator".$nomb;
				foreach($arra_depe[$codi] as $codi => $nomb)
			    {
			    	$arra_options_depe[$codi]="$separator$separator$separator".$nomb;
				    $arra_options_padr[str_pad($codi,4,'0',STR_PAD_LEFT)]="$separator$separator$separator$separator".$nomb;
				    foreach($arra_depe[$codi] as $codi => $nomb)
			        {
			    	    $arra_options_depe[$codi]="$separator$separator$separator$separator".$nomb;
				        $arra_options_padr[str_pad($codi,4,'0',STR_PAD_LEFT)]="$separator$separator$separator$separator$separator".$nomb;
			        }
			    }
			}
		}
	}
	
	//print_r($arra_options_depe);
	
	$arra_loca[0]="<- Seleccione ->";
	$result=$Db->select('mp_admi_loca','','','',['nom1_loca'=>'ASC']);
	foreach ($result as $rows)
	{
		$arra_loca[$rows['codi_loca']]=$rows['nom1_loca'];
	}

	$arra_options_tipo=$Db->get_options('mp_maes_tdependencia');
	$html=new htmlclass;
	echo $html->put_select(CONST_SUBTITLE_DEPENDENCIA,'codi_depe',$arra_options_depe,$_POST['codi_depe'],"onchange=\"document.form.submit()\" required");
	if(!empty($_POST['codi_depe']))
	{
		//-------------------------------
		echo $html->put_title(CONST_SUBTITLE_BASIC_INFORMATION);
		echo $html->put_text('text',CONST_SUBTITLE_NAME,CONST_PLACEHOLDER_NAME,'nomb_depe',$_POST['nomb_depe'],'','100','');
		echo $html->put_text('text',CONST_SUBTITLE_SIGLAS,CONST_PLACEHOLDER_SIGLAS,'sigl_depe',$_POST['sigl_depe'],'','20','');
		echo $html->put_select("Local",'codi_loca',$arra_loca,$_POST['codi_loca'],"required");
		echo $html->put_text('text',CONST_SUBTITLE_ADDRESS,CONST_PLACEHOLDER_ADDRESS,'dire_depe',$_POST['dire_depe'],'','100','');
		echo $html->put_select(CONST_SUBTITLE_TIPO,'tipo_depe',$arra_options_tipo,$_POST['tipo_depe'],"required");
		echo $html->put_select(CONST_SUBTITLE_PADR,'codi_padr',$arra_options_padr,$_POST['codi_padr'],"required");
		echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_depe',$_POST['esta_depe'],CONST_OPTION_ENABLE,CONST_OPTION_DISABLE);
		echo"<BR><BR>";
		echo $html->put_submit(CONST_BUTTON_SAVE,'check()');
	}
?>
		</form>
	</body>
</html>
