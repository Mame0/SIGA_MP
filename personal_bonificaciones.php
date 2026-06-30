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
                echo"
                    <html><body>
                    <form name=\"form\" method=post action=\"personal_buscar.php\">
                        <input type=hidden name=\"flag_admi\" value=\"".($_POST['flag_admi'] ?? '')."\">
                        <input type=hidden name=\"dire_orig\" value=\"personal_bonificaciones.php\">
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

	if(isset($_POST['guardar_personal']))
	{
		$fdig=date("YmdHis");
		//$_POST['esta_pers']=1;
		if($_POST['iden_pers'])
		{
			$result=$Db->update('mp_admi_pers',['cona_pers'=>$_POST['cona_pers'],'rcon_pers'=>$_POST['rcon_pers'],'carcon_pers'=>$_POST['carcon_pers'],'iden_disc'=>$_POST['iden_disc'],'iden_ffaa'=>$_POST['iden_ffaa'],'iden_depo'=>$_POST['iden_depo']],['iden_pers'=>$_POST['iden_pers']]);
		}
		else
		{
			$result=$Db->insert('mp_admi_pers',['cona_pers'=>$_POST['cona_pers'],'rcon_pers'=>$_POST['rcon_pers'],'carcon_pers'=>$_POST['carcon_pers'],'iden_disc'=>$_POST['iden_disc'],'iden_ffaa'=>$_POST['iden_ffaa'],'iden_depo'=>$_POST['iden_depo']]);
			$_POST['iden_pers']=$Db->lastInsertId();
		}

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"personal_bonificaciones.php\">
                    <input type=hidden name=\"iden_pers\" value=\"".$_POST['iden_pers']."\">
					<input type=hidden name=\"busq_tipo\" value=\"".$_POST['busq_tipo']."\">
					<input type=hidden name=\"busq_dato\" value=\"".$_POST['busq_dato']."\">
					<input type=hidden name=\"busq_pagi_actu\" value=\"".$_POST['busq_pagi_actu']."\">
					<input type=hidden name=\"codi_form\" value=\"".$_POST['codi_form']."\">
					<input type=hidden name=\"flag_admi\" value=\"".($_POST['flag_admi'] ?? '')."\">
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
	$_POST['cona_pers']=$result_personal[0]['cona_pers'];
	$_POST['rcon_pers']=$result_personal[0]['rcon_pers'];
	$_POST['carcon_pers']=$result_personal[0]['carcon_pers'];
	$_POST['iden_disc']=$result_personal[0]['iden_disc'];
	$_POST['iden_ffaa']=$result_personal[0]['iden_ffaa'];
	$_POST['iden_depo']=$result_personal[0]['iden_depo'];
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
				if(confirm('Seguro que desea Guardar'))
				{
					document.form.guardar_personal.value='1';
					document.form.submit();
				}
				else
					return false;
			}
			function f_cancelar_documento()
			{
				document.form.action='personal_mantenimiento.php';
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
		echo"Bonificaciones Especiales<BR>".$_POST['appa_pers']." ".$_POST['apma_pers'].", ".$_POST['nomb_pers'];
	else
		echo"Crear Nuevo Personal";
?>
	</h4></b></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="iden_pers" value="<?=$_POST['iden_pers']?>">
			<input type=hidden name="busq_tipo" value="<?=htmlspecialchars($_POST['busq_tipo'] ?? '')?>">
			<input type=hidden name="busq_dato" value="<?=htmlspecialchars($_POST['busq_dato'] ?? '')?>">
			<input type=hidden name="codi_depe" value="<?=htmlspecialchars($_POST['codi_depe'] ?? '')?>">
			<input type=hidden name="busq_pagi_actu" value="<?=htmlspecialchars($_POST['busq_pagi_actu'] ?? '')?>">
			<input type=hidden name="codi_form" value="<?=htmlspecialchars($_POST['codi_form'] ?? '')?>">
			<input type=hidden name="flag_admi" value="<?=htmlspecialchars($_POST['flag_admi'] ?? '')?>">
			<main>
<?
	$html=new htmlclass;

    $arra_options_depe[0]="<- Seleccione ->";
    /*
    $result=$Db->query("select * from mp_admi_depe");
    foreach($result as $rows)
        $arra_options_depe[$rows['codi_depe']]=utf8_encode(utf8_decode($rows['nomb_depe']));
    */
    
    $arra_options_cona[0]="NO";
    $arra_options_cona[1]="SI";
    $arra_options_disc=$Db->get_options('mp_maes_discapacidad',1,0);
    $arra_options_ffaa=$Db->get_options('mp_maes_ffaa',1,0);
    $arra_options_dcal=$Db->get_options('mp_maes_deportista_calificado',1,0);
    
	//echo $html->put_title_demand("Variable &Eacute;tnica");
	echo $html->put_select("Inscrito&nbsp;en&nbsp;CONADIS",'cona_pers',$arra_options_cona,$_POST['cona_pers'],"");
	echo $html->put_text('text',"Resoluci&oacute;n&nbsp;de&nbsp;CONADIS","Ingrese Resolucion",'rcon_pers',$_POST['rcon_pers'],'','20','');
	echo $html->put_text('text',"Numero&nbsp;Carnet&nbsp;de&nbsp;CONADIS","Ingrese Carnet",'carcon_pers',$_POST['carcon_pers'],'','20','');
	echo"</main><main>";
	echo $html->put_select("Discapacidad",'iden_disc',$arra_options_disc,$_POST['iden_disc'],"");
	echo $html->put_select("Licenciado&nbsp;FFAA",'iden_ffaa',$arra_options_ffaa,$_POST['iden_ffaa'],"");
	echo $html->put_select("Deportista&nbsp;Calificado",'iden_depo',$arra_options_dcal,$_POST['iden_depo'],"");
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
