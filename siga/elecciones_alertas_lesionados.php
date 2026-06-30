<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	
	$Db = new Db();
	
	$result=$Db->query("select codi_elec,nomb_elec from mp_elec_config WHERE habi_elec='1'");
	foreach($result as $rows)
	{
	    $_POST['nomb_elec']=$rows['nomb_elec'];
	    $_POST['codi_elec']=$rows['codi_elec'];
	}

	$fdig=date("YmdHis");
	
	//codi_aler	codi_elec	codi_usua	aler_ocur	codi_tale	fech_aler	ubig_aler	luga_aler	deta_aler	acci_aler	digi_aler	fdig_aler	esta_aler
if($_POST['codi_aler'])
{	
	$result_documento=$Db->select('mp_elec_alertas', ['codi_aler'=>$_POST['codi_aler']], '', '', '');
	$_POST['aler_ocur']=$result_documento[0]['aler_ocur'];
	$_POST['codi_tale']=$result_documento[0]['codi_tale'];
	if($result_documento[0]['fech_aler'])
	{
	    $_POST['hora_aler']=substr($result_documento[0]['fech_aler'],8,2).':'.substr($result_documento[0]['fech_aler'],10,2);
	    $_POST['fech_aler']=substr($result_documento[0]['fech_aler'],0,4).'-'.substr($result_documento[0]['fech_aler'],4,2).'-'.substr($result_documento[0]['fech_aler'],6,2);
	}
	
	$result=$Db->select('mp_maes_elecciones_alertas_tipo', '', '', '', ['n_codigo'=>'ASC']);
	foreach($result as $rows)
		$arra_options_tale[$rows['n_codigo']]=$rows['x_nombre'];
	$_POST['nomb_tale']=$arra_options_tale[$_POST['codi_tale']];
}	
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>SIOJAlimentos</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		<script>
		    function f_nuevo()
			{
				document.form.action='elecciones_alertas_lesionados_nuevo.php';
				document.form.submit();
			}
			function f_editar(codi)
			{
			    document.form.codi_lesi.value=codi;
				document.form.action='elecciones_alertas_lesionados_nuevo.php';
				document.form.submit();
			}
			function f_cancelar()
			{
				document.form.action='elecciones_alertas.php';
				document.form.submit();
			}
			function ajustar_altura()
                        {
                                parent.document.getElementById('body_iframe').height=parent.window.innerHeight-80;
                        }
                        ajustar_altura();
		</script>
	</head>
	<body style="margin-bottom: 30px;">
	<center><font style="color:#073A6B;font-weight: bold;"><?=$_POST['nomb_elec']?><BR><font style="font-size: 20;">FORMATO B<BR>LESIONADOS Y FALLECIDOS</font><br><u>TIPO</u>: <?=$_POST['nomb_tale']?><br><u>FECHA</u>: <?=$_POST['fech_aler']?> / <u>HORA</u>: <?=$_POST['hora_aler']?> / <u>USUARIO</u>: <?=$_SESSION['logi_oper']?></font></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="regresar_reporte" value="<?=$_POST['regresar_reporte']?>">
			<input type=hidden name="codi_aler" value="<?=$_POST['codi_aler']?>">
			<input type=hidden name="codi_lesi">
			<main>
<?
	$html=new htmlclass;
    
    //$arra_options_lesi_fall[0]="<- Seleccione ->";
    $arra_options_lesi_fall[1]="Lesionado";
    $arra_options_lesi_fall[2]="Fallecido";
	
	$arra_options_sexo[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_sexo', '', '', '', ['n_codigo'=>'ASC']);
	foreach($result as $rows)
		$arra_options_sexo[$rows['n_codigo']]=$rows['x_nombre'];

	echo $html->put_title_demand("Lesionados y Fallecidos");
	echo"</main>";
    $result_pagi=$Db->query("select * from mp_elec_alertas_lesionados WHERE codi_aler='".$_POST['codi_aler']."' order by codi_lesi");
	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	$head=['1'=>"NRO.",'2'=>"DNI",'3'=>"NOMBRES Y APELLIDOS",'4'=>"CONDICION",'5'=>"SEXO",'6'=>"EDAD",'7'=>"EDIT"];
	echo $html->put_table_responsive_open();
	//if($busc_tota_item)
	//{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$cont++;
			$del="";
			if($rows['esta_lesi']==0)
			    $del="<del><font color=red>";
			$data=[	'1'=>$cont,
				'2'=>$del.$rows['ndni_lesi'],
				'3'=>$del.$rows['nomb_lesi'],
				'4'=>$del.$arra_options_lesi_fall[$rows['lesi_fall']],
				'5'=>$del.substr($arra_options_sexo[$rows['sexo_lesi']],0,1),
				'6'=>$del.$rows['edad_lesi'],
				'7'=>"<a href=\"javascript:f_editar('$rows[codi_lesi]')\"><img src=\"img/icons/edit.svg\" width=\"20\">",
			];
			echo $html->put_table_responsive_data($head,$data);
		}
	//}
	//else
	//	echo $html->put_table_responsive_title("No Existen Registros".$_POST['anno_elec']);
	echo $html->put_table_responsive_close();
	echo"</div>";

	echo $html->put_separator_demand("30");

                echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_cancelar()\">&laquo; Regresar</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"return f_nuevo()\">Agregar Lesionado &raquo;</button>
                                        </div>
                                </div>
                        </div>
                ";
?>
<center>
	</form>
	</body>
</html>
