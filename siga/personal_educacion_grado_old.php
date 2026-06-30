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
                <form name=\"form\" method=post action=\"personal_familiares.php\">
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
        $_POST['desd_grad']=str_replace("-","",$_POST['desd_grad']);
        $_POST['hast_grad']=str_replace("-","",$_POST['hast_grad']);
        $_POST['fech_grad']=str_replace("-","",$_POST['fech_grad']);
		if($_POST['iden_pers'] AND $_POST['iden_grad'])
		{
			$result=$Db->update('mp_admi_pers_grad',['iden_pers'=>$_POST['iden_pers'],'iden_nive'=>$_POST['iden_nive'],'iden_esta'=>$_POST['iden_esta'],'iden_espe'=>$_POST['iden_espe'],'iden_inst'=>$_POST['iden_inst'],'ntit_grad'=>$_POST['ntit_grad'],'ncol_grad'=>$_POST['ncol_grad'],'desd_grad'=>$_POST['desd_grad'],'hast_grad'=>$_POST['hast_grad'],'fech_grad'=>$_POST['fech_grad'],'digi_grad'=>$_SESSION['iden_oper'],'fdig_grad'=>$fdig,'esta_grad'=>'1'],['iden_grad'=>$_POST['iden_grad']]);
		}
		else
		{
			$result=$Db->insert('mp_admi_pers_grad',['iden_pers'=>$_POST['iden_pers'],'iden_nive'=>$_POST['iden_nive'],'iden_esta'=>$_POST['iden_esta'],'iden_espe'=>$_POST['iden_espe'],'iden_inst'=>$_POST['iden_inst'],'ntit_grad'=>$_POST['ntit_grad'],'ncol_grad'=>$_POST['ncol_grad'],'desd_grad'=>$_POST['desd_grad'],'hast_grad'=>$_POST['hast_grad'],'fech_grad'=>$_POST['fech_grad'],'digi_grad'=>$_SESSION['iden_oper'],'fdig_grad'=>$fdig,'esta_grad'=>'1']);
			$_POST['iden_grad']=$Db->lastInsertId();
		}

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"personal_educacion.php\">
                    <input type=hidden name=\"iden_pers\" value=\"".$_POST['iden_pers']."\">
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
	$result_personal=$Db->select('mp_admi_pers', ['iden_pers'=>$_POST['iden_pers']], '', '', '');
	$_POST['appa_pers']=$result_personal[0]['appa_pers'];
	$_POST['apma_pers']=$result_personal[0]['apma_pers'];
	$_POST['nomb_pers']=$result_personal[0]['nomb_pers'];
	
	//echo"<HR>".$_POST['iden_fami']."<HR>";
	
	if($_POST['iden_grad'])
	{
	    $result_personal=$Db->select('mp_admi_pers_grad', ['iden_pers'=>$_POST['iden_pers'],'iden_grad'=>$_POST['iden_grad']], '', '', '');
	    $_POST['iden_nive']=$result_personal[0]['iden_nive'];
	    $_POST['iden_esta']=$result_personal[0]['iden_esta'];
	    $_POST['iden_espe']=$result_personal[0]['iden_espe'];
	    $_POST['iden_inst']=$result_personal[0]['iden_inst'];
	    $_POST['ntit_grad']=$result_personal[0]['ntit_grad'];
	    $_POST['ncol_grad']=$result_personal[0]['ncol_grad'];
	    $_POST['desd_grad']=substr($result_personal[0]['desd_grad'],0,4).'-'.substr($result_personal[0]['desd_grad'],4,2).'-'.substr($result_personal[0]['desd_grad'],6,2);
	    $_POST['hast_grad']=substr($result_personal[0]['hast_grad'],0,4).'-'.substr($result_personal[0]['hast_grad'],4,2).'-'.substr($result_personal[0]['hast_grad'],6,2);
	    $_POST['fech_grad']=substr($result_personal[0]['fech_grad'],0,4).'-'.substr($result_personal[0]['fech_grad'],4,2).'-'.substr($result_personal[0]['fech_grad'],6,2);
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
				if(document.form.iden_nive.selectedIndex=='0')
				{
					alert('Seleccione Nivel');
					document.form.iden_nive.focus();
					return false;
				}
				else
				{
					if(document.form.iden_esta.selectedIndex=='')
					{
						alert('Seleccione Estado');
						document.form.iden_esta.focus();
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
			function f_cancelar_documento()
			{
			    document.form.action='personal_educacion.php';
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
		echo"T&iacute;tulos y Grados<BR>".$_POST['appa_pers']." ".$_POST['apma_pers'].", ".$_POST['nomb_pers'];
	else
		echo"Crear Nuevo Personal";
?>
	</h4></b></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="iden_pers" value="<?=$_POST['iden_pers']?>">
			<input type=hidden name="iden_grad" value="<?=$_POST['iden_grad']?>">
			<input type=hidden name="busq_tipo" value="<?=$_POST['busq_tipo']?>">
			<input type=hidden name="busq_dato" value="<?=$_POST['busq_dato']?>">
			<input type=hidden name="codi_depe" value="<?=$_POST['codi_depe']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">
			<input type=hidden name="codi_form" value="<?=$_POST['codi_form']?>">
			<main>
<?
	$html=new htmlclass;

    /*
    $arra_options_depe[0]="<- Seleccione ->";
    $result=$Db->query("select * from mp_admi_depe");
    foreach($result as $rows)
        $arra_options_depe[$rows['codi_depe']]=utf8_encode(utf8_decode($rows['nomb_depe']));
    */
    $arra_options_vive[1]="SI";
    $arra_options_vive[0]="NO";
    
    $arra_options_nive=$Db->get_options('mp_maes_grado_nivel',1,0);
    $arra_options_esta=$Db->get_options('mp_maes_grado_estado',1,0);
    $arra_options_espe=$Db->get_options('mp_maes_grado_especialidades',1,0);
    $arra_options_inst=$Db->get_options('mp_maes_grado_instituciones',1,0);
    
    if($_POST['iden_grad'])
	    echo $html->put_title_demand("Editar Grado y/o T&iacute;tulo []");
	else
	    echo $html->put_title_demand("Agregar Nuevo Grado y/o T&iacute;tulo");
	
	echo $html->put_select("Nivel&nbsp;(*)",'iden_nive',$arra_options_nive,$_POST['iden_nive'],"");
	echo $html->put_select("Estado&nbsp;(*)",'iden_esta',$arra_options_esta,$_POST['iden_esta'],"");
	echo $html->put_select("Especialidad&nbsp;(*)",'iden_espe',$arra_options_espe,$_POST['iden_espe'],"");
	echo"</main><main>";  
	echo $html->put_select("Centro&nbsp;de&nbsp;Estudios&nbsp;(*)",'iden_espe',$arra_options_espe,$_POST['iden_espe'],"");
	echo $html->put_text('text',"Nro.&nbsp;T&iacute;tulo&nbsp;(*)","Ingrese Nro. T&iacute;tulo",'ntit_grad',$_POST['ntit_grad'],'','15','');
	echo $html->put_text('text',"N&uacute;mero&nbsp;de&nbsp;Colegiatura&nbsp;(*)","Ingrese Nro. Colegiatura",'ncol_grad',$_POST['ncol_grad'],'','15','');
	echo"</main><main>";
	echo $html->put_text('date',"Desde","",'desd_grad',$_POST['desd_grad'],'','20','');
	echo $html->put_text('date',"Hasta","",'hast_grad',$_POST['hast_grad'],'','20','');
	echo $html->put_text('date',"Fecha&nbsp;Obtenci&oacute;n","",'fech_grad',$_POST['fech_grad'],'','20','');

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
