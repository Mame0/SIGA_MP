<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	if(!empty($_POST['saveinfo']))	//guardar
	{
		$fdig=date("Ymdhis");
		if($_POST['codi_proc']==9999)	//nuevo
		{
			$result=$Db->insert('mp_concurso_proceso',['codi_exam'=>$_POST['codi_exam'],'nume_proc'=>$_POST['nume_proc'],'anno_proc'=>$_POST['anno_proc'],'regi_proc'=>$_POST['regi_proc'],'digi_proc'=>$_SESSION['iden_oper'],'fdig_proc'=>$fdig,'esta_proc'=>$_POST['esta_proc']]);
			unset($_POST);
		}
		else	//update
		{
			$result=$Db->update('mp_concurso_proceso',['codi_exam'=>$_POST['codi_exam'],'nume_proc'=>$_POST['nume_proc'],'anno_proc'=>$_POST['anno_proc'],'regi_proc'=>$_POST['regi_proc'],'digi_proc'=>$_SESSION['iden_oper'],'fdig_proc'=>$fdig,'esta_proc'=>$_POST['esta_proc']],['codi_proc'=>$_POST['codi_proc']]);
			unset($_POST);
		}
	}
	if(!empty($_POST['codi_proc']))
	{
		$result=$Db->select('mp_concurso_proceso',['codi_proc'=>$_POST['codi_proc']]);
		if($result) {
			$_POST['nume_proc']=$result[0]['nume_proc'];
			$_POST['anno_proc']=$result[0]['anno_proc'];
			$_POST['regi_proc']=$result[0]['regi_proc'];
			$_POST['codi_exam']=$result[0]['codi_exam'];
			$_POST['esta_proc']=$result[0]['esta_proc'];
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
<h2>Administrar Procesos
<br><font color=silver>Exámen -> Proceso -> Plaza</font></h2><br>
		</center>
<?
	//$result=$Db->select(['mp_admi_oper',['n_codi_ginstruccion','x_ginstruccion']],['n_estado'=>1]);
	//--------------
	
	$result=$Db->select('mp_concurso_examen','','','',['fech_exam'=>'ASC']);
	$arra_options_exam[0]="<- ".CONST_OPTION_SELECT." ->";
	foreach ($result as $rows)
		$arra_options_exam[$rows['codi_exam']]="[".$rows['fech_exam']."] ".$rows['nomb_exam'];
	
	$arra_options_regi[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_concurso_regimen', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_regi[$rows['n_codigo']]=$rows['x_nombre'];
	
	$arra_options_anno[2023]="2023";
	$arra_options_anno[2024]="2024";
	$arra_options_anno[2025]="2025";
	$arra_options_anno[2026]="2026";
	$arra_options_anno[2027]="2027";
	
	$result=$Db->select('mp_concurso_proceso','','','',['codi_proc'=>'ASC']);
	$arra_options_menu[0]="<- ".CONST_OPTION_SELECT." ->";
	$arra_options_menu[9999]="<- ".CONST_OPTION_NEW." ->";
	foreach ($result as $rows)
		$arra_options_menu[$rows['codi_proc']]="CONCURSO ".$arra_options_regi[$rows['regi_proc']]." Nro. ".$rows['nume_proc']."-".$rows['anno_proc'];

	$html=new htmlclass;
	echo $html->put_select('Seleccione Proceso: ','codi_proc',$arra_options_menu,(isset($_POST['codi_proc']) ? $_POST['codi_proc'] : ''),"onchange=\"document.form.submit()\" required");
	if(!empty($_POST['codi_proc']))
	{
		//-------------------------------
		echo $html->put_title(CONST_SUBTITLE_BASIC_INFORMATION);
		echo $html->put_select('Seleccione Examen: ','codi_exam',$arra_options_exam,$_POST['codi_exam'],"required");
		echo $html->put_text('number',"Número","Ingrese Número",'nume_proc',$_POST['nume_proc'],'','3','required');
		echo $html->put_select('Año','anno_proc',$arra_options_anno,$_POST['anno_proc'],"required");
		echo $html->put_select('Régimen','regi_proc',$arra_options_regi,$_POST['regi_proc'],"required");
		
		echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_proc',$_POST['esta_proc'],CONST_OPTION_ENABLE,CONST_OPTION_DISABLE);
		echo"<BR><BR>";
		echo $html->put_submit(CONST_BUTTON_SAVE,'check()');
	}
?>
		</form>
	</body>
</html>
