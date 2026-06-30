<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");

	if($_POST['guardar_personal'])
	{
		$fdig=date("YmdHis");
		//$_POST['esta_pers']=1;
		if($_POST['iden_pers'])
		{
			$result=$Db->update('mp_maes_personal',['ndoc_pers'=>$_POST['ndoc_pers'],'appa_pers'=>$_POST['appa_pers'],'apma_pers'=>$_POST['apma_pers'],'nomb_pers'=>$_POST['nomb_pers'],'codi_depe'=>$_POST['codi_depe'],'codi_carg'=>$_POST['codi_carg'],'regi_labo'=>$_POST['regi_labo'],'fech_ingr'=>$_POST['fech_ingr'],'digi_pers'=>$_POST['digi_pers'],'esta_pers'=>$_POST['esta_pers']],['iden_pers'=>$_POST['iden_pers']]);
		}
		else
		{
			$result=$Db->insert('mp_maes_personal',['ndoc_pers'=>$_POST['ndoc_pers'],'appa_pers'=>$_POST['appa_pers'],'apma_pers'=>$_POST['apma_pers'],'nomb_pers'=>$_POST['nomb_pers'],'codi_depe'=>$_POST['codi_depe'],'codi_carg'=>$_POST['codi_carg'],'regi_labo'=>$_POST['regi_labo'],'fech_ingr'=>$_POST['fech_ingr'],'digi_pers'=>$_POST['digi_pers'],'esta_pers'=>$_POST['esta_pers']]);
			$_POST['iden_pers']=$Db->lastInsertId();
		}

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"potencial_mantenimiento.php\">
					<input type=hidden name=\"busq_tipo\" value=\"".$_POST['busq_tipo']."\">
					<input type=hidden name=\"busq_dato\" value=\"".$_POST['busq_dato']."\">
					<input type=hidden name=\"busq_pagi_actu\" value=\"".$_POST['busq_pagi_actu']."\">
					<input type=hidden name=\"codi_form\" value=\"".$_POST['codi_form']."\">
                                </form>
                                <script>
                                        document.form.submit();
                                </script>
                        </body></html>
		";

	}
	$result_personal=$Db->select('mp_maes_personal', ['iden_pers'=>$_POST['iden_pers']], '', '', '');
	$_POST['ndoc_pers']=$result_personal[0]['ndoc_pers'];
	$_POST['appa_pers']=$result_personal[0]['appa_pers'];
	$_POST['apma_pers']=$result_personal[0]['apma_pers'];
	$_POST['nomb_pers']=$result_personal[0]['nomb_pers'];
	$_POST['codi_depe']=$result_personal[0]['codi_depe'];
	$_POST['codi_carg']=$result_personal[0]['codi_carg'];
	$_POST['regi_labo']=$result_personal[0]['regi_labo'];
	$_POST['esta_pers']=$result_personal[0]['esta_pers'];
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
			function f_guardar_personal()
			{
				if(document.form.ndoc_pers.value=='')
				{
					alert('Ingrese Nro. de DNI');
					document.form.ndoc_pers.focus();
					return false;
				}
				else
				{
					if(document.form.appa_pers.value=='')
					{
						alert('Ingrese Apellido Paterno');
						document.form.appa_pers.focus();
						return false;
					}
					else
					{
						if(document.form.nomb_pers.value=='')
						{
							alert('Ingrese Nombres');
							document.form.nomb_pers.focus();
							return false;
						}
						else
						{
							if(confirm('Seguro que desea Guardar'))
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
			function f_cancelar_documento()
			{
				document.form.action='potencial_mantenimiento.php';
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
	<center><h2 style="color:#bb0400">
<?
	if($_POST['iden_pers'])
		echo"Editar Informaci&oacute;n de Personal<BR>".$_POST['appa_pers']." ".$_POST['apma_pers'].", ".$_POST['nomb_pers'];
	else
		echo"Crear Nuevo Personal";
?>
	</h2></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="iden_pers" value="<?=$_POST['iden_pers']?>">
			<input type=hidden name="busq_tipo" value="<?=$_POST['busq_tipo']?>">
			<input type=hidden name="busq_dato" value="<?=$_POST['busq_dato']?>">
			<input type=hidden name="codi_depe" value="<?=$_POST['codi_depe']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">
			<input type=hidden name="codi_form" value="<?=$_POST['codi_form']?>">
			<main>
<?
	$html=new htmlclass;

    $arra_options_depe[0]="<- Seleccione ->";
    /*
    $result=$Db->query("select * from mp_admi_depe");
    foreach($result as $rows)
        $arra_options_depe[$rows['codi_depe']]=utf8_encode(utf8_decode($rows['nomb_depe']));
    */
    $result1=$Db->query("select * from mp_admi_depe where codi_padr=0 AND esta_depe=1 order by nomb_depe");
	$separador="|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	foreach($result1 as $rows1)
	{   
	    if(strlen($rows1['abre_depe'])>70)  $rows1['abre_depe']=substr($rows1['abre_depe'],0,70).'...'; 
	    $arra_options_depe[$rows1['codi_depe']]=$rows1['abre_depe'];
		$result2=$Db->query("select * from mp_admi_depe where codi_padr='".$rows1['codi_depe']."' AND esta_depe=1 order by nomb_depe");
		foreach($result2 as $rows2)
		{
		    if(strlen($rows2['abre_depe'])>70)  $rows2['abre_depe']=substr($rows2['abre_depe'],0,70).'...';
		    $arra_options_depe[$rows2['codi_depe']]=$separador.$rows2['abre_depe'];
		    $result3=$Db->query("select * from mp_admi_depe where codi_padr='".$rows2['codi_depe']."' AND esta_depe=1 order by nomb_depe");
	    	foreach($result3 as $rows3)
    		{
		        if(strlen($rows3['abre_depe'])>70)  $rows3['abre_depe']=substr($rows3['abre_depe'],0,70).'...';
		        $arra_options_depe[$rows3['codi_depe']]=$separador.$separador.$rows3['abre_depe'];
		        $result4=$Db->query("select * from mp_admi_depe where codi_padr='".$rows3['codi_depe']."' AND esta_depe=1 order by nomb_depe");
	    	    foreach($result4 as $rows4)
    		    {
		            if(strlen($rows4['abre_depe'])>70)  $rows4['abre_depe']=substr($rows4['abre_depe'],0,70).'...';
		            $arra_options_depe[$rows4['codi_depe']]=$separador.$separador.$separador.$rows4['abre_depe'];
    		        $result5=$Db->query("select * from mp_admi_depe where codi_padr='".$rows4['codi_depe']."' AND esta_depe=1 order by nomb_depe");
    	    	    foreach($result5 as $rows5)
        		    {
		                if(strlen($rows5['abre_depe'])>70)  $rows5['abre_depe']=substr($rows5['abre_depe'],0,70).'...';
		                $arra_options_depe[$rows5['codi_depe']]=$separador.$separador.$separador.$separador.$rows5['abre_depe'];
    		            $result6=$Db->query("select * from mp_admi_depe where codi_padr='".$rows5['codi_depe']."' AND esta_depe=1 order by nomb_depe");
        	    	    foreach($result6 as $rows6)
            		    {
    		                if(strlen($rows6['abre_depe'])>70)  $rows6['abre_depe']=substr($rows6['abre_depe'],0,70).'...';
    		                $arra_options_depe[$rows6['codi_depe']]=$separador.$separador.$separador.$separador.$separador.$rows6['abre_depe'];
        		            //echo"<tr>$separador$separador$separador$separador$separador<td width=1%><input type=checkbox name=\"chec_depe_".$rows6['codi_depe']."\" ".$arra_depe[$rows6['codi_depe']]."></td><td width=100% colspan=$colu style=\"font-size:small\">".$rows6['abre_depe']."</td></tr>";
            		    }
    		            
    		            
    		            
	    	        }
	    	    }
		    }
		}
	}
    
    
    
    
    
    
    
	
	
	$arra_options_carg[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_cargo', '', '', '', ['x_nombre'=>'ASC']);
    foreach($result as $rows)
        $arra_options_carg[$rows['n_codigo']]=utf8_encode(utf8_decode($rows['x_nombre']));

	$arra_options_regi[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_fotocheck_rlaboral', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_regi[$rows['n_codigo']]=utf8_encode($rows['x_nombre']);

	echo $html->put_title_demand("Informaci&oacute;n Personal");
	echo $html->put_text('text',"DNI","Ingrese Nro. DNI",'ndoc_pers',$_POST['ndoc_pers'],'','8','');
	echo"</main><main>";
	echo $html->put_text('text',"Apellido&nbsp;Paterno","Ingrese Apellido Paterno",'appa_pers',$_POST['appa_pers'],'','50','');
	echo $html->put_text('text',"Apellido&nbsp;Materno","Ingrese Apellido Materno",'apma_pers',$_POST['apma_pers'],'','50','');
	echo $html->put_text('text',"Nombres","Ingrese Nombres",'nomb_pers',$_POST['nomb_pers'],'','50','');
	echo $html->put_title_demand("Informaci&oacute;n Laboral");
	echo $html->put_select("Dependencia",'codi_depe',$arra_options_depe,$_POST['codi_depe'],"");
	echo $html->put_select("Régimen",'regi_labo',$arra_options_regi,$_POST['regi_labo'],"");
	echo $html->put_select("Cargo",'codi_carg',$arra_options_carg,$_POST['codi_carg'],"");
	echo $html->put_title_demand("Estado del Trabajador");
	echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_pers',$_POST['esta_pers'],CONST_OPTION_ENABLE,CONST_OPTION_DISABLE);
	echo"</main>";

	echo $html->put_separator_demand("30");

                echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_cancelar_documento()\">&laquo; Cancelar</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"return f_guardar_personal()\">Guardar &raquo;</button>
                                        </div>
                                </div>
                        </div>
                ";
?>
<center>
	</form>
	</body>
</html>
