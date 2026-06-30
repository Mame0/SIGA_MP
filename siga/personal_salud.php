<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	require_once 'include/registrar_acceso.php';

	$fdig=date("YmdHis");
	
	if(isset($_POST['iden_pers_edit']) && $_POST['iden_pers_edit'])
	{
	    unset($_POST['iden_pers']);
	    $_SESSION['iden_pers_edit']=$_POST['iden_pers_edit'];
	}
	
	if(!isset($_POST['iden_pers']))
	{
	    if(isset($_GET['flag_admi']) && $_GET['flag_admi']==1)
	        $_POST['flag_admi']=$_GET['flag_admi'];
	    if(isset($_POST['flag_admi']) && $_POST['flag_admi']==1) //si es administrador
        {
            if(isset($_SESSION['iden_pers_edit']) && $_SESSION['iden_pers_edit'])
                $_POST['iden_pers']=$_SESSION['iden_pers_edit'];
            else
            {
                //header("Location: personal_buscar.php");
                //echo"<script>window.location.replace(\"personal_buscar.php\");</script>";
                //echo"<HR>holaaaaa<HR>";
                //exit();
                echo"
                    <html><body>
                    <form name=\"form\" method=post action=\"personal_buscar.php\">
                        <input type=hidden name=\"iden_pers\" value=\"".htmlspecialchars($_POST['iden_pers'] ?? '')."\">
                        <input type=hidden name=\"flag_admi\" value=\"".$_POST['flag_admi']."\">
                        <input type=hidden name=\"dire_orig\" value=\"personal_familiares.php\">
                    </form>
                    <script>
                        document.form.submit();
                    </script>
                    </body></html>
		        ";
                exit;
            }
        }
        else
        {
            $result=$Db->query("select * from mp_admi_pers where ndoc_pers='$_SESSION[ndoc_oper]'");
            foreach($result as $rows)
                $_POST['iden_pers']=$rows['iden_pers'];
        }
	}

	if(isset($_POST['agregar_enfermedad']) && $_POST['agregar_enfermedad'])
	{
	    $cont=0;
	    $result=$Db->query("select * from mp_admi_pers_enfe where iden_pers='$_POST[iden_pers]' AND iden_enfe='$_POST[iden_enfe]'");
	    foreach($result as $rows)
	        $cont++;
	    if($cont==0)
	        $result=$Db->insert('mp_admi_pers_enfe',['iden_pers'=>$_POST['iden_pers'],'iden_enfe'=>$_POST['iden_enfe']]);
	}
	
	if(isset($_POST['eliminar_enfermedad']) && $_POST['eliminar_enfermedad'])
	{
	    $result=$Db->delete('mp_admi_pers_enfe',['iden_pers'=>$_POST['iden_pers'],'iden_enfe'=>$_POST['eliminar_enfermedad']]);
	}
	
	if(isset($_POST['guardar_personal']) && $_POST['guardar_personal'])
	{
		$fdig=date("YmdHis");
		//$_POST['esta_pers']=1;
		if($_POST['iden_pers'])
		{
			$result=$Db->update('mp_admi_pers',['iden_tsan'=>$_POST['iden_tsan'],'aler_pers'=>$_POST['aler_pers'],'enfe_pers'=>$_POST['enfe_pers']],['iden_pers'=>$_POST['iden_pers']]);
		}
		else
		{
			$result=$Db->insert('mp_admi_pers',['iden_etni'=>$_POST['iden_etni'],'iden_leng'=>$_POST['iden_leng'],'olen_pers'=>$_POST['olen_pers']]);
			$_POST['iden_pers']=$Db->lastInsertId();
		}

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"personal_salud.php\">
                    <input type=hidden name=\"iden_pers\" value=\"".htmlspecialchars($_POST['iden_pers'] ?? '')."\">
					<input type=hidden name=\"busq_tipo\" value=\"".htmlspecialchars($_POST['busq_tipo'] ?? '')."\">
					<input type=hidden name=\"busq_dato\" value=\"".htmlspecialchars($_POST['busq_dato'] ?? '')."\">
					<input type=hidden name=\"busq_pagi_actu\" value=\"".htmlspecialchars($_POST['busq_pagi_actu'] ?? '')."\">
					<input type=hidden name=\"codi_form\" value=\"".htmlspecialchars($_POST['codi_form'] ?? '')."\">
					<input type=hidden name=\"flag_admi\" value=\"".htmlspecialchars($_POST['flag_admi'] ?? '')."\">
                    <input type=hidden name=\"dire_orig\" value=\"personal_salud.php\">
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
	$_POST['iden_tsan']=$result_personal[0]['iden_tsan'];
	$_POST['aler_pers']=$result_personal[0]['aler_pers'];
	$_POST['enfe_pers']=$result_personal[0]['enfe_pers'];
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
				if(document.form.iden_tsan.selectedIndex=='0')
				{
					alert('Seleccione Tipo de Sangre');
					document.form.iden_tsan.focus();
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
			function f_agregar_enfermedad()
			{
			    document.form.agregar_enfermedad.value='1';
				document.form.action='personal_salud.php';
				document.form.submit();
			}
			function f_eliminar(codi)
			{
			    document.form.eliminar_enfermedad.value=codi;
				document.form.action='personal_salud.php';
				document.form.submit();
			}
			function f_cancelar_documento()
			{
				document.form.action='personal_buscar.php';
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
		echo"Datos de Salud<BR>".$_POST['appa_pers']." ".$_POST['apma_pers'].", ".$_POST['nomb_pers'];
	else
		echo"Crear Nuevo Personal";
?>
	</h4></b></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="agregar_enfermedad">
			<input type=hidden name="eliminar_enfermedad">
			<input type=hidden name="iden_pers" value="<?=htmlspecialchars($_POST['iden_pers'] ?? '')?>">
			<input type=hidden name="busq_tipo" value="<?=htmlspecialchars($_POST['busq_tipo'] ?? '')?>">
			<input type=hidden name="busq_dato" value="<?=htmlspecialchars($_POST['busq_dato'] ?? '')?>">
			<input type=hidden name="codi_depe" value="<?=htmlspecialchars($_POST['codi_depe'] ?? '')?>">
			<input type=hidden name="busq_pagi_actu" value="<?=htmlspecialchars($_POST['busq_pagi_actu'] ?? '')?>">
			<input type=hidden name="codi_form" value="<?=htmlspecialchars($_POST['codi_form'] ?? '')?>">
			<input type=hidden name="flag_admi" value="<?=htmlspecialchars($_POST['flag_admi'] ?? '')?>">
			<input type=hidden name="dire_orig" value="personal_salud.php">
<?
	$html=new htmlclass;
    
    $arra_options_tsan=$Db->get_options('mp_maes_grupo_sanguineo',1,0);
    $arra_options_enfe=$Db->get_options('mp_maes_enfermedades_tipo',1,0);
    
	//echo $html->put_title_demand("Variable &Eacute;tnica");
	echo"<main>";
	echo $html->put_select("Tipo&nbsp;de&nbsp;Sangre",'iden_tsan',$arra_options_tsan,$_POST['iden_tsan'],"");
	echo $html->put_text('text',"Alergias","Ingrese Alergias",'aler_pers',$_POST['aler_pers'],'','20','');
	echo $html->put_text('text',"Otras&nbsp;Enfermedades","Ingrese Otras Enfermedades",'enfe_pers',$_POST['enfe_pers'],'','20','');
	echo"</main>";
	echo"<main>";
	echo $html->put_select("Agregar&nbsp;Enfermedades",'iden_enfe',$arra_options_enfe,'',"onchange='f_agregar_enfermedad()'");
	echo"</main>";
	echo"<BR>";
	$result_pagi=$Db->query("select * from mp_admi_pers_enfe where iden_pers='$_POST[iden_pers]'");
    echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
    $head=['1'=>"Nº",'2'=>"ENFERMEDAD",'3'=>"ELIMINAR"];
    echo $html->put_table_responsive_open();
    $cont=0;
	echo $html->put_table_responsive_header($head);
	foreach($result_pagi as $rows)
	{
		$cont++;
		$data=[	'1'=>$cont,
			'2'=>$arra_options_enfe[$rows['iden_enfe']],
			'3'=>"<a href=\"javascript:f_eliminar('$rows[iden_enfe]')\"><img src=\"img/icons/trash.svg\" width=\"20\">",
		];
		echo $html->put_table_responsive_data($head,$data);
	}
    //if($cont==0)
    //	echo $html->put_table_responsive_title("Usuario no tiene Enfermedades");
		
    echo $html->put_table_responsive_close();
    echo"</div>";

	echo $html->put_separator_demand("30");
    if((isset($_GET['flag_admi']) && $_GET['flag_admi']==1) OR (isset($_POST['flag_admi']) && $_POST['flag_admi']==1)) //si es administrador
    {
                echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_cancelar_documento()\">&laquo; Nueva B&uacute;squeda</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"return f_guardar_personal()\">Guardar &raquo;</button>
                                        </div>
                                </div>
                        </div>
                ";
    }
    else
    {
                echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"reset()\">&laquo; Cancelar</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"return f_guardar_personal()\">Guardar &raquo;</button>
                                        </div>
                                </div>
                        </div>
                ";
    }
?>
<center>
	</form>
	</body>
</html>
