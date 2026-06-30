<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");
	
	if(!$_POST['iden_pers'])
	{
	    echo"
            <html><body>
                <form name=\"form\" method=post action=\"personal_general.php\">
                    <input type=hidden name=\"iden_pers\" value=\"".$_POST['iden_pers']."\">
					<input type=hidden name=\"codi_form\" value=\"".$_POST['codi_form']."\">
                </form>
                <script>document.form.submit();</script>
            </body></html>
		";
	}

	if($_POST['guardar_personal'])
	{
		$fdig=date(YmdHis);
        $_POST['desd_expe']=str_replace("-","",$_POST['desd_expe']);
        $_POST['hast_expe']=str_replace("-","",$_POST['hast_expe']);
		if($_POST['iden_pers'] AND $_POST['iden_expe'])
		{
			$result=$Db->update('mp_admi_pers_expe',['iden_pers'=>$_POST['iden_pers'],'inst_expe'=>$_POST['inst_expe'],'iden_carg'=>$_POST['iden_carg'],'desd_expe'=>$_POST['desd_expe'],'hast_expe'=>$_POST['hast_expe'],'iden_cond'=>$_POST['iden_cond'],'iden_moti'=>$_POST['iden_moti'],'digi_expe'=>$_SESSION['iden_oper'],'fdig_expe'=>$fdig,'esta_expe'=>'1'],['iden_expe'=>$_POST['iden_expe']]);
		}
		else
		{
			$result=$Db->insert('mp_admi_pers_expe',['iden_pers'=>$_POST['iden_pers'],'inst_expe'=>$_POST['inst_expe'],'iden_carg'=>$_POST['iden_carg'],'desd_expe'=>$_POST['desd_expe'],'hast_expe'=>$_POST['hast_expe'],'iden_cond'=>$_POST['iden_cond'],'iden_moti'=>$_POST['iden_moti'],'digi_expe'=>$_SESSION['iden_oper'],'fdig_expe'=>$fdig,'esta_expe'=>'1']);
			$_POST['iden_expe']=$Db->lastInsertId();
		}

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"personal_experiencia.php\">
                    <input type=hidden name=\"iden_pers\" value=\"".$_POST['iden_pers']."\">
					<input type=hidden name=\"busq_tipo\" value=\"".$_POST['busq_tipo']."\">
					<input type=hidden name=\"busq_dato\" value=\"".$_POST['busq_dato']."\">
					<input type=hidden name=\"busq_pagi_actu\" value=\"".$_POST['busq_pagi_actu']."\">
					<input type=hidden name=\"codi_form\" value=\"".$_POST['codi_form']."\">
					<input type=hidden name=\"flag_admi\" value=\"".$_POST['flag_admi']."\">
                    <input type=hidden name=\"dire_orig\" value=\"personal_experiencia.php\">
                                </form>
                                <script>
                                        document.form.submit();
                                </script>
                        </body></html>
		";

	}
	$result_personal=$Db->select('mp_admi_pers', ['iden_pers'=>$_POST['iden_pers']], '', '', '');
	$_POST['appa_pers']=$result_personal[0]['appa_pers'];
	$_POST['apma_pers']=$result_personal[0]['apma_pers'];
	$_POST['nomb_pers']=$result_personal[0]['nomb_pers'];
	
	//echo"<HR>".$_POST['iden_fami']."<HR>";
	
	if($_POST['iden_expe'])
	{
	    $result_personal=$Db->select('mp_admi_pers_expe', ['iden_pers'=>$_POST['iden_pers'],'iden_expe'=>$_POST['iden_expe']], '', '', '');
	    $_POST['inst_expe']=$result_personal[0]['inst_expe'];
	    $_POST['iden_carg']=$result_personal[0]['iden_carg'];
	    $_POST['desd_expe']=substr($result_personal[0]['desd_expe'],0,4).'-'.substr($result_personal[0]['desd_expe'],4,2).'-'.substr($result_personal[0]['desd_expe'],6,2);
	    $_POST['hast_expe']=substr($result_personal[0]['hast_expe'],0,4).'-'.substr($result_personal[0]['hast_expe'],4,2).'-'.substr($result_personal[0]['hast_expe'],6,2);
	    $_POST['iden_cond']=$result_personal[0]['iden_cond'];
	    $_POST['iden_moti']=$result_personal[0]['iden_moti'];
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title></title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		<!--
		<link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
        <link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/css/bootstrap-select.min.css'>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
        -->
        
		<script>
			function f_guardar_personal()
			{
				if(document.form.iden_carg.selectedIndex=='0')
				{
					alert('Seleccione Cargo');
					document.form.iden_carg.focus();
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
			function f_cancelar_documento()
			{
			    document.form.action='personal_experiencia.php';
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
	<center><h4 style="color:#073a6b"><b>
<?
	if($_POST['iden_pers'])
		echo"Cursos y/o Especializaci&0acute;n<BR>".$_POST['appa_pers']." ".$_POST['apma_pers'].", ".$_POST['nomb_pers'];
	else
		echo"Crear Nuevo Personal";
?>
	</h4></b></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="iden_pers" value="<?=$_POST['iden_pers']?>">
			<input type=hidden name="iden_expe" value="<?=$_POST['iden_expe']?>">
			<input type=hidden name="busq_tipo" value="<?=$_POST['busq_tipo']?>">
			<input type=hidden name="busq_dato" value="<?=$_POST['busq_dato']?>">
			<input type=hidden name="codi_depe" value="<?=$_POST['codi_depe']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">
			<input type=hidden name="codi_form" value="<?=$_POST['codi_form']?>">
			<input type=hidden name="flag_admi" value="<?=$_POST['flag_admi']?>">
			<input type=hidden name="dire_orig" value="personal_familiares.php">
			<main>
<?
	$html=new htmlclass;

    $arra_options_carg=$Db->get_options('mp_maes_labo_cargos',1,0);
    $arra_options_cond=$Db->get_options('mp_maes_labo_condic_contractual',1,0);
    $arra_options_moti=$Db->get_options('mp_maes_labo_motivo_cese',1,0);
    
    if($_POST['iden_expe'])
	    echo $html->put_title_demand("Editar Experiencia Profesional []");
	else
	    echo $html->put_title_demand("Agregar Nueva Experiencia Profesional");
	
	echo $html->put_text('text',"Instituci&oacute;n&nbsp;(*)","Ingrese Institución",'inst_expe',$_POST['inst_expe'],'','100','');
	echo $html->put_select("Cargo&nbsp;(*)",'iden_carg',$arra_options_carg,$_POST['iden_carg'],"");
	echo $html->put_select("Condici&oacute;n&nbsp;Contractual&nbsp;(*)",'iden_cond',$arra_options_cond,$_POST['iden_cond'],"");
	echo"</main><main>";
	echo $html->put_text('date',"Desde","",'desd_expe',$_POST['desd_expe'],'','20','');
	echo $html->put_text('date',"Hasta","",'hast_expe',$_POST['hast_expe'],'','20','');
	echo $html->put_select("Motivo&nbsp;Cese&nbsp;(*)",'iden_moti',$arra_options_moti,$_POST['iden_moti'],"");

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
