<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	if(!empty($_POST['saveinfo']))	//guardar
	{
		$fdig=date("Ymdhis");
		if($_POST['codi_inve']==9999)	//nuevo
		{
			$result=$Db->insert('mp_patr_inve_mant',['nomb_inve'=>$_POST['nomb_inve'],'fech_inve'=>$_POST['fech_inve'],'acti_inve'=>$_POST['acti_inve'],'digi_inve'=>$_SESSION['iden_oper'],'fdig_inve'=>$fdig,'esta_inve'=>$_POST['esta_inve']]);
			unset($_POST);
		}
		else	//update
		{
			$result=$Db->update('mp_patr_inve_mant',['nomb_inve'=>$_POST['nomb_inve'],'fech_inve'=>$_POST['fech_inve'],'acti_inve'=>$_POST['acti_inve'],'digi_inve'=>$_SESSION['iden_oper'],'fdig_inve'=>$fdig,'esta_inve'=>$_POST['esta_inve']],['codi_inve'=>$_POST['codi_inve']]);
			unset($_POST);
		}
	}
	if(!empty($_POST['codi_inve']))
	{
		$result=$Db->select('mp_patr_inve_mant',['codi_inve'=>$_POST[codi_inve]]);
		$_POST['nomb_inve']=$result[0]['nomb_inve'];
		$_POST['fech_inve']=$result[0]['fech_inve'];
		$_POST['acti_inve']=$result[0]['acti_inve'];
		$_POST['esta_inve']=$result[0]['esta_inve'];
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
<h4><b>Administrar Inventarios</b>
</h4>
<br>
		</center>
<?
	//$result=$Db->select(['mp_admi_oper',['n_codi_ginstruccion','x_ginstruccion']],['n_estado'=>1]);
	//--------------
	$result=$Db->select('mp_patr_inve_mant','','','',['fech_inve'=>'ASC']);
	$arra_options_menu[0]="<- ".CONST_OPTION_SELECT." ->";
	$arra_options_menu[9999]="<- ".CONST_OPTION_NEW." ->";
	foreach ($result as $rows)
		$arra_options_menu[$rows['codi_inve']]="[".$rows['fech_inve']."] ".$rows['nomb_inve'];

	$html=new htmlclass;
	echo $html->put_select('Seleccione Inventario: ','codi_inve',$arra_options_menu,$_POST['codi_inve'],"onchange=\"document.form.submit()\" required");
	if(!empty($_POST['codi_inve']))
	{
		//-------------------------------
		echo $html->put_title(CONST_SUBTITLE_BASIC_INFORMATION);
		echo $html->put_text('text',CONST_SUBTITLE_NAME,CONST_PLACEHOLDER_NAME,'nomb_inve',$_POST['nomb_inve'],'','50','required');
		//echo $html->put_select(CONST_SUBTITLE_ICON,'icon_menu',$arra_options_icon,$_POST['icon_menu'],"required");
		echo $html->put_text('date',"Fecha",CONST_PLACEHOLDER_ORDER,'fech_inve',$_POST['fech_inve'],'','3','required');
		echo $html->put_select_estado("Habilitado",'acti_inve',$_POST['acti_inve'],"Activo","Inactivo");
		echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_inve',$_POST['esta_inve'],CONST_OPTION_ENABLE,CONST_OPTION_DISABLE);
		echo"<BR><BR>";
		echo $html->put_submit(CONST_BUTTON_SAVE,'check()');
		
	}
?>
		</form>
	</body>
</html>
