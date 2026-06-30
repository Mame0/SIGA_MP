<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	if(!empty($_POST['saveinfo']))	//guardar
	{
		$fdig=date("Ymdis");
		if($_POST['codi_loca']==9999)	//nuevo
		{
			$result=$Db->insert('mp_admi_loca',['nom1_loca'=>$_POST['nom1_loca'],'nom2_loca'=>$_POST['nom2_loca'],'dire_loca'=>$_POST['dire_loca'],'ubig_loca'=>$_POST['ubig_loca'],'lati_loca'=>$_POST['lati_loca'],'long_loca'=>$_POST['long_loca'],'rang_loca'=>$_POST['rang_loca'],'esta_loca'=>$_POST['esta_loca'],'digi_loca'=>$_SESSION['iden_oper'],'fdig_loca'=>"$fdig"]);
			unset($_POST);
		}
		else	//update
		{
			$result=$Db->update('mp_admi_loca',['nom1_loca'=>$_POST['nom1_loca'],'nom2_loca'=>$_POST['nom2_loca'],'dire_loca'=>$_POST['dire_loca'],'ubig_loca'=>$_POST['ubig_loca'],'lati_loca'=>$_POST['lati_loca'],'long_loca'=>$_POST['long_loca'],'rang_loca'=>$_POST['rang_loca'],'esta_loca'=>$_POST['esta_loca'],'digi_loca'=>$_SESSION['iden_oper'],'fdig_loca'=>"$fdig"],['codi_loca'=>$_POST['codi_loca']]);
			unset($_POST);
		}
	}
	if(!empty($_POST['codi_loca']))
	{
		$result=$Db->select('mp_admi_loca',['codi_loca'=>$_POST['codi_loca']]);
		$_POST['nom1_loca']=$result[0]['nom1_loca'];
		$_POST['nom2_loca']=$result[0]['nom2_loca'];
		$_POST['dire_loca']=$result[0]['dire_loca'];
		$_POST['ubig_loca']=$result[0]['ubig_loca'];
		$_POST['lati_loca']=$result[0]['lati_loca'];
		$_POST['long_loca']=$result[0]['long_loca'];
		$_POST['rang_loca']=$result[0]['rang_loca'];
		$_POST['esta_loca']=$result[0]['esta_loca'];
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>DFArequipa</title>
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
<h2>ADMINISTRACIÓN DE LOCALES</h2><br>
		</center>
<?
    $arra_loca[0]="<- Seleccione ->";
    $arra_loca[9999]="<- Nuevo ->";
	$result=$Db->select('mp_admi_loca','','','',['nom1_loca'=>'ASC']);
	foreach ($result as $rows)
	{
		$arra_loca[$rows['codi_loca']]=$rows['nom1_loca'];
	}

	$html=new htmlclass;
	echo $html->put_select("Locales&nbsp;Disponibles",'codi_loca',$arra_loca,$_POST['codi_loca'],"onchange=\"document.form.submit()\" required");
	if(!empty($_POST['codi_loca']))
	{
		//-------------------------------
		echo $html->put_title(CONST_SUBTITLE_BASIC_INFORMATION);
		echo $html->put_text('text',"Nombre 1","Ingrese Nombre 1",'nom1_loca',$_POST['nom1_loca'],'','100','');
		echo $html->put_text('text',"Nombre 2","Ingrese Nombre 2",'nom2_loca',$_POST['nom2_loca'],'','200','');
		echo $html->put_text('text',"Dirección","Ingrese Direccióm",'dire_loca',$_POST['dire_loca'],'','200','');
		echo $html->put_text('text',"Ubigeo","Ingrese Ubigeo",'ubig_loca',$_POST['ubig_loca'],'','6','');
		echo $html->put_text('text',"Latitud","Ingrese Latitud",'lati_loca',$_POST['lati_loca'],'','20','');
		echo $html->put_text('text',"Longitud","Ingrese Longitud",'long_loca',$_POST['long_loca'],'','20','');
		echo $html->put_text('text',"Rango IP","Ingrese Rango",'rang_loca',$_POST['rang_loca'],'','20','');
		echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_loca',$_POST['esta_loca'],CONST_OPTION_ENABLE,CONST_OPTION_DISABLE);
		echo"<BR><BR>";
		echo $html->put_submit(CONST_BUTTON_SAVE,'check()');
	}
?>
		</form>
	</body>
</html>
