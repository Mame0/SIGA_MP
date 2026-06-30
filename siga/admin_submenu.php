<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	if(!empty($_POST['saveinfo']))	//guardar
	{
		$fdig=date("Ymdis");
		$menu=substr($_POST['iden_padr'],0,4)+1-1;
		$padr=substr($_POST['iden_padr'],5,4)+1-1;
		if($_POST['iden_subm']==9999)	//nuevo
		{
			$result=$Db->insert('mp_admi_subm',['iden_menu'=>$menu,'nomb_subm'=>$_POST['nomb_subm'],'icon_subm'=>$_POST['icon_subm'],'page_subm'=>$_POST['page_subm'],'iden_padr'=>$padr,'orde_subm'=>$_POST['orde_subm'],'esta_subm'=>$_POST['esta_subm']]);
			unset($_POST);
		}
		else	//update
		{
			$result=$Db->update('mp_admi_subm',['iden_menu'=>$menu,'nomb_subm'=>$_POST['nomb_subm'],'icon_subm'=>$_POST['icon_subm'],'page_subm'=>$_POST['page_subm'],'iden_padr'=>$padr,'orde_subm'=>$_POST['orde_subm'],'esta_subm'=>$_POST['esta_subm']],['iden_subm'=>$_POST['iden_subm']]);
			unset($_POST);
			//aqui correr una funcion que arregle todos los hijos
		}
	}
	if(!empty($_POST['iden_subm']))
	{
		$result=$Db->select('mp_admi_subm',['iden_subm'=>$_POST['iden_subm']]);
		$_POST['nomb_subm']=$result[0]['nomb_subm'];
		$_POST['icon_subm']=$result[0]['icon_subm'];
		$_POST['page_subm']=$result[0]['page_subm'];
		$_POST['orde_subm']=$result[0]['orde_subm'];
		$_POST['esta_subm']=$result[0]['esta_subm'];
		$_POST['iden_padr']=str_pad($result[0]['iden_menu'],4,'0',STR_PAD_LEFT).'_'.str_pad($result[0]['iden_padr'],4,'0',STR_PAD_LEFT);
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
<h2><?=CONST_TITLE_SUBMENUS?></h2><br>
		</center>
<?
	$result=$Db->select('mp_admi_subm','','','',['orde_subm'=>'ASC']);
	foreach ($result as $rows)
	{
		$arra_subm[$rows['iden_menu']][$rows['iden_padr']][$rows['iden_subm']]=$rows['nomb_subm'];
	}

	$separator="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

	$result_menu=$Db->select('mp_admi_menu','','','',['orde_menu'=>'ASC']);
	$arra_options_padr[0]="<- ".CONST_OPTION_SELECT." ->";
	$arra_options_subm[0]="<- ".CONST_OPTION_SELECT." ->";
	$arra_options_subm[9999]="<- ".CONST_OPTION_NEW." ->";
	foreach ($result_menu as $rows_menu)
	{
		$menu4=str_pad($rows_menu['iden_menu'],4,'0',STR_PAD_LEFT);
		$arra_options_subm['9999_'.$rows_menu['iden_menu']]=$rows_menu['nomb_menu'];
		$arra_options_padr[$menu4.'_0000']=$rows_menu['nomb_menu'];
		if(isset($arra_subm[$rows_menu['iden_menu']][0]))
		{
			foreach($arra_subm[$rows_menu['iden_menu']][0] as $codi => $nomb)
			{
				$arra_options_subm[$codi]=$nomb;
				$arra_options_padr[$menu4.'_'.str_pad($codi,4,'0',STR_PAD_LEFT)]="$separator".$nomb;
				if(isset($arra_subm[$rows_menu['iden_menu']][$codi]))
				{
					foreach($arra_subm[$rows_menu['iden_menu']][$codi] as $codi => $nomb)
					{
						$arra_options_subm[$codi]="$separator".$nomb;
						$arra_options_padr[$menu4.'_'.str_pad($codi,4,'0',STR_PAD_LEFT)]="$separator$separator".$nomb;
						if(isset($arra_subm[$rows_menu['iden_menu']][$codi]))
						{
							foreach($arra_subm[$rows_menu['iden_menu']][$codi] as $codi => $nomb)
							{
								$arra_options_subm[$codi]="$separator$separator".$nomb;
								$arra_options_padr[$menu4.'_'.str_pad($codi,4,'0',STR_PAD_LEFT)]="$separator$separator$separator".$nomb;
							}
						}
					}
				}
			}
		}
	}
	$directorio = opendir("./img/icons"); //ruta actual
	$arra_options_icon[0]="<- ".CONST_OPTION_SELECT." ->";
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
	echo $html->put_select(CONST_SUBTITLE_SUBMENU,'iden_subm',$arra_options_subm,$_POST['iden_subm'],"onchange=\"document.form.submit()\" required");
	if(!empty($_POST['iden_subm']))
	{
		//-------------------------------
		echo $html->put_title(CONST_SUBTITLE_BASIC_INFORMATION);
		echo $html->put_text('text',CONST_SUBTITLE_NAME,CONST_PLACEHOLDER_NAME,'nomb_subm',$_POST['nomb_subm'],'','50','required pattern="[A-Za-z_]+" title="Solo letras"');
		echo $html->put_select(CONST_SUBTITLE_ICON,'icon_subm',$arra_options_icon,$_POST['icon_subm'],"required");
		echo $html->put_text('text',CONST_SUBTITLE_ADDRESS,CONST_PLACEHOLDER_ADDRESS,'page_subm',$_POST['page_subm'],'','100','');
		echo $html->put_select(CONST_SUBTITLE_PADR,'iden_padr',$arra_options_padr,$_POST['iden_padr'],"required");
		echo $html->put_text('number',CONST_SUBTITLE_ORDER,CONST_PLACEHOLDER_ORDER,'orde_subm',$_POST['orde_subm'],'','3','required');
		echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_subm',$_POST['esta_subm'],CONST_OPTION_ENABLE,CONST_OPTION_DISABLE);
		echo"<BR><BR>";
		echo $html->put_submit(CONST_BUTTON_SAVE,'check()');
	}
?>
		</form>
	</body>
</html>
