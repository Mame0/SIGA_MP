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
	
	if($_POST['guardar_personal'])
	{
		if($_POST['codi_lesi'])
		{
			$result=$Db->update('mp_elec_alertas_lesionados',['codi_aler'=>$_POST['codi_aler'],'lesi_fall'=>$_POST['lesi_fall'],'nomb_lesi'=>$_POST['nomb_lesi'],'ndni_lesi'=>$_POST['ndni_lesi'],'sexo_lesi'=>$_POST['sexo_lesi'],'edad_lesi'=>$_POST['edad_lesi'],'digi_lesi'=>$_SESSION['iden_oper'],'fdig_lesi'=>"$fdig",'esta_lesi'=>$_POST['esta_lesi']],['codi_lesi'=>$_POST['codi_lesi']]);
		}
		else
		{
			$result=$Db->insert('mp_elec_alertas_lesionados',['codi_aler'=>$_POST['codi_aler'],'lesi_fall'=>$_POST['lesi_fall'],'nomb_lesi'=>$_POST['nomb_lesi'],'ndni_lesi'=>$_POST['ndni_lesi'],'sexo_lesi'=>$_POST['sexo_lesi'],'edad_lesi'=>$_POST['edad_lesi'],'digi_lesi'=>$_SESSION['iden_oper'],'fdig_lesi'=>"$fdig",'esta_lesi'=>$_POST['esta_lesi']]);
			$_POST['codi_part']=$Db->lastInsertId();
		}
		echo"
			<script>alert('".CONST_MENS_REG_OK."');</script>
                        <html><body>
                                <form name=\"form\" method=\"post\" action=\"elecciones_alertas_lesionados.php\">
					                <input type=\"hidden\" name=\"codi_aler\" value=\"".$_POST['codi_aler']."\">
                                </form>
                                <script>
                                        document.form.submit();
                                        
                                </script>
                                
                        </body></html>
		";
	}
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
	//$_POST['nomb_tale']=$_POST['codi_tale'];
	
	//echo"<HR>".$_POST['nomb_tale']."<HR>";
}	
if($_POST['codi_lesi'])
{
	$result_documento=$Db->select('mp_elec_alertas_lesionados', ['codi_lesi'=>$_POST['codi_lesi']], '', '', '');
	$_POST['lesi_fall']=$result_documento[0]['lesi_fall'];
	$_POST['nomb_lesi']=$result_documento[0]['nomb_lesi'];
	$_POST['ndni_lesi']=$result_documento[0]['ndni_lesi'];
	$_POST['sexo_lesi']=$result_documento[0]['sexo_lesi'];
	$_POST['edad_lesi']=$result_documento[0]['edad_lesi'];
	$_POST['esta_lesi']=$result_documento[0]['esta_lesi'];
	
	//echo"<HR>".$_POST['codi_lesi']."-".$_POST['esta_lesi']."<HR>";
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
			function f_guardar()
			{
			    if(document.form.lesi_fall.selectedIndex=='0')
				{
					alert('Seleccione Condicion');
					document.form.lesi_fall.focus();
					return false;
				}
				else
				{
				    if(document.form.nomb_lesi.value=='')
				    {
				        alert('Ingrese nombres y apellidos');
					    document.form.nomb_lesi.focus();
					    return false;
				    }
				    else
				    {
						if(document.form.ndni_lesi.value=='')
						{
							alert('Ingrese DNI');
							document.form.ndni_lesi.focus();
							return false;
						}
						else
						{
						    if(document.form.sexo_lesi.selectedIndex=='0')
            				{
            					alert('Seleccione Sexo');
            					document.form.sexo_lesi.focus();
            					return false;
            				}   
    		        		else
            				{
    					    		if(confirm('SEGURO QUE DESEA GUARDAR?'))
    					    		{
    					    			document.form.guardar_personal.value='1';
    					    			document.form.submit();
    					    		}
    					    		else
    					    			return false;
    						}
    				    }
    				}
				}
			}
			function f_cancelar()
			{
				document.form.action='elecciones_alertas_lesionados.php';
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
	<center><font style="color:#073A6B;font-weight: bold;"><?=$_POST['nomb_elec']?><BR><font style="font-size: 20;">FORMATO B<BR>AGREGAR LESIONADOS Y FALLECIDOS</font><br><u>TIPO</u>: <?=$_POST['nomb_tale']?><br><u>FECHA</u>: <?=$_POST['fech_aler']?> / <u>HORA</u>: <?=$_POST['hora_aler']?> / <u>USUARIO</u>: <?=$_SESSION['logi_oper']?></font></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="regresar_reporte" value="<?=$_POST['regresar_reporte']?>">
			<input type=hidden name="codi_aler" value="<?=$_POST['codi_aler']?>">
			<input type=hidden name="codi_lesi" value="<?=$_POST['codi_lesi']?>">
			<main>
<?
	$html=new htmlclass;
    
    $arra_options_lesi_fall[0]="<- Seleccione ->";
    $arra_options_lesi_fall[1]="Lesionado";
    $arra_options_lesi_fall[2]="Fallecido";
	
	$arra_options_sexo[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_sexo', '', '', '', ['n_codigo'=>'ASC']);
	foreach($result as $rows)
		$arra_options_sexo[$rows['n_codigo']]=$rows['x_nombre'];

	echo $html->put_title_demand("Lesionados y Fallecidos");
	echo $html->put_select("Condici&oacute;n",'lesi_fall',$arra_options_lesi_fall,$_POST['lesi_fall'],"");
	echo $html->put_text('text',"Nombres&nbsp;y&nbsp;Apellidos","Ingrese Nombres",'nomb_lesi',$_POST['nomb_lesi'],'','200','');
	echo $html->put_text('text',"DNI","Ingrese DNI",'ndni_lesi',$_POST['ndni_lesi'],'','20','');
	echo"</main><main>";
	echo $html->put_select("Sexo",'sexo_lesi',$arra_options_sexo,$_POST['sexo_lesi'],"");
	echo $html->put_text('text',"Edad","Ingrese Edad",'edad_lesi',$_POST['edad_lesi'],'','3','');
	//echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_lesi',$_POST['esta_lesi'],'Activo','Inactivo');
	echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_lesi',$_POST['esta_lesi'],'Activo','Inactivo');
	echo"</main>";
    //echo $html->put_separator_demand("30");
    
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
                                                <button class=\"button_foot\" onclick=\"return f_guardar()\">Guardar &raquo;</button>
                                        </div>
                                </div>
                        </div>
                ";
?>
<center>
	</form>
	</body>
</html>
