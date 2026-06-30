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
					<input type=hidden name=\"flag_admi\" value=\"".$_POST['flag_admi']."\">
                </form>
                <script>document.form.submit();</script>
            </body></html>
		";
	}

	if($_POST['guardar_personal'])
	{
		$fdig=date("YmdHis");
        if($_POST['iden_pers'] AND $_POST['iden_emer'])
		{
			$result=$Db->update('mp_admi_pers_emer',['iden_pers'=>$_POST['iden_pers'],'appa_emer'=>$_POST['appa_emer'],'apma_emer'=>$_POST['apma_emer'],'nomb_emer'=>$_POST['nomb_emer'],'tfij_emer'=>$_POST['tfij_emer'],'tcel_emer'=>$_POST['tcel_emer'],'digi_emer'=>$_SESSION['iden_oper'],'fdig_emer'=>$fdig,'esta_emer'=>'1'],['iden_emer'=>$_POST['iden_emer']]);
		}
		else
		{
			$result=$Db->insert('mp_admi_pers_emer',['iden_pers'=>$_POST['iden_pers'],'appa_emer'=>$_POST['appa_emer'],'apma_emer'=>$_POST['apma_emer'],'nomb_emer'=>$_POST['nomb_emer'],'tfij_emer'=>$_POST['tfij_emer'],'tcel_emer'=>$_POST['tcel_emer'],'digi_emer'=>$_SESSION['iden_oper'],'fdig_emer'=>$fdig,'esta_emer'=>'1'],['iden_emer'=>$_POST['iden_emer']],['iden_emer'=>$_POST['iden_emer']]);
			$_POST['iden_emer']=$Db->lastInsertId();
		}

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"personal_emergencia.php\">
                    <input type=hidden name=\"iden_pers\" value=\"".$_POST['iden_pers']."\">
					<input type=hidden name=\"busq_tipo\" value=\"".$_POST['busq_tipo']."\">
					<input type=hidden name=\"busq_dato\" value=\"".$_POST['busq_dato']."\">
					<input type=hidden name=\"busq_pagi_actu\" value=\"".$_POST['busq_pagi_actu']."\">
					<input type=hidden name=\"codi_form\" value=\"".$_POST['codi_form']."\">
					<input type=hidden name=\"flag_admi\" value=\"".$_POST['flag_admi']."\">
                    <input type=hidden name=\"dire_orig\" value=\"personal_emergencia.php\">
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
	
	if($_POST['iden_emer'])
	{
	    $result_personal=$Db->select('mp_admi_pers_emer', ['iden_pers'=>$_POST['iden_pers'],'iden_emer'=>$_POST['iden_emer']], '', '', '');
	    $_POST['appa_emer']=$result_personal[0]['appa_emer'];
	    $_POST['apma_emer']=$result_personal[0]['apma_emer'];
	    $_POST['nomb_emer']=$result_personal[0]['nomb_emer'];
	    $_POST['tfij_emer']=$result_personal[0]['tfij_emer'];
	    $_POST['tcel_emer']=$result_personal[0]['tcel_emer'];
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
				if(document.form.appa_emer.value=='')
				{
					alert('Ingrese Apellido Paterno');
					document.form.appa_emer.focus();
					return false;
				}
				else
				{
					if(document.form.apma_emer.value=='')
					{
						alert('Ingrese Apellido Materno');
						document.form.apma_emer.focus();
						return false;
					}
					else
					{
					    if(document.form.nomb_emer.value=='')
					    {
    						alert('Ingrese Nombres');
						    document.form.nomb_emer.focus();
						    return false;
					    }
					    else
					    {
					                if(document.form.tcel_emer.value=='')
					                {
    						            alert('Ingrese Tel. Celular');
						                document.form.tcel_emer.focus();
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
			}
			function f_cancelar_documento()
			{
				document.form.action='personal_emergencia.php';
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
		echo"Contacto de Emergencia<BR>".$_POST['appa_pers']." ".$_POST['apma_pers'].", ".$_POST['nomb_pers'];
	else
		echo"Crear Nuevo Personal";
?>
	</h4></b></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="iden_pers" value="<?=$_POST['iden_pers']?>">
			<input type=hidden name="iden_emer" value="<?=$_POST['iden_emer']?>">
			<input type=hidden name="busq_tipo" value="<?=$_POST['busq_tipo']?>">
			<input type=hidden name="busq_dato" value="<?=$_POST['busq_dato']?>">
			<input type=hidden name="codi_depe" value="<?=$_POST['codi_depe']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">
			<input type=hidden name="codi_form" value="<?=$_POST['codi_form']?>">
			<input type=hidden name="flag_admi" value="<?=$_POST['flag_admi']?>">
			<input type=hidden name="dire_orig" value="personal_emergencia.php">
			<main>
<?
	$html=new htmlclass;

    /*
    $arra_options_depe[0]="<- Seleccione ->";
    $result=$Db->query("select * from mp_admi_depe");
    foreach($result as $rows)
        $arra_options_depe[$rows['codi_depe']]=utf8_encode(utf8_decode($rows['nomb_depe']));
    */
    
    if($_POST['iden_emer'])
	    echo $html->put_title_demand("Editar Información de Contacto de Emergencia [$_POST[appa_emer] $_POST[apma_emer], $_POST[nomb_emer]]");
	else
	    echo $html->put_title_demand("Agregar Nuevo Contacto de Emergencia");
	echo $html->put_text('text',"Apellido&nbsp;Paterno&nbsp;(*)","Ingrese Apellido Paterno",'appa_emer',$_POST['appa_emer'],'','50','');
	echo $html->put_text('text',"Apellido&nbsp;Materno&nbsp;(*)","Ingrese Apellido Materno",'apma_emer',$_POST['apma_emer'],'','50','');
	echo $html->put_text('text',"Nombres","Ingrese Nombres&nbsp;(*)",'nomb_emer',$_POST['nomb_emer'],'','50','');
	echo"</main><main>";
	echo $html->put_text('text',"Tel&eacute;fono&nbsp;Fijo","Ingrese Número",'tfij_emer',$_POST['tfij_emer'],'','20','');
	echo $html->put_text('text',"Tel&eacute;fono&nbsp;Celular","Ingrese Número",'tcel_emer',$_POST['tcel_emer'],'','20','');
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
