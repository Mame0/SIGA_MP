<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	require_once 'include/registrar_acceso.php';

	// Inicializar flag_admi para evitar warnings y simplificar la lógica
	$_POST['flag_admi'] = $_POST['flag_admi'] ?? $_GET['flag_admi'] ?? 0;
	
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;

	$fdig=date("YmdHis");
	
	if(isset($_POST['iden_pers_edit']) && $_POST['iden_pers_edit'])
	{
	    unset($_POST['iden_pers']);
	    $_SESSION['iden_pers_edit']=$_POST['iden_pers_edit'];
	}
	
	if(!isset($_POST['iden_pers']))
	{
	    if($_POST['flag_admi'] == 1) //si es administrador
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
	

    if(!empty($_POST['iden_fami_elim']))
    {
        $result=$Db->update('mp_admi_pers_fami',['digi_fami'=>$_SESSION['iden_oper'],'fdig_fami'=>$fdig,'esta_fami'=>'0'],['iden_fami'=>$_POST['iden_fami_elim']]);
    }
	if(!empty($_POST['guardar_personal']))
	{
		$fdig=date(YmdHis);
		//$_POST['esta_pers']=1;
		if($_POST['iden_pers'])
		{
			$result=$Db->update('mp_admi_pers',['iden_etni'=>$_POST['iden_etni'],'iden_leng'=>$_POST['iden_leng'],'olen_pers'=>$_POST['olen_pers']],['iden_pers'=>$_POST['iden_pers']]);
		}
		else
		{
			$result=$Db->insert('mp_admi_pers',['iden_etni'=>$_POST['iden_etni'],'iden_leng'=>$_POST['iden_leng'],'olen_pers'=>$_POST['olen_pers']]);
			$_POST['iden_pers']=$Db->lastInsertId();
		}

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"personal_etnica.php\">
                    <input type=hidden name=\"iden_pers\" value=\"".($_POST['iden_pers'] ?? '')."\">
					<input type=hidden name=\"busq_tipo\" value=\"".($_POST['busq_tipo'] ?? '')."\">
					<input type=hidden name=\"busq_dato\" value=\"".($_POST['busq_dato'] ?? '')."\">
					<input type=hidden name=\"busq_pagi_actu\" value=\"".($_POST['busq_pagi_actu'] ?? '')."\">
					<input type=hidden name=\"codi_form\" value=\"".($_POST['codi_form'] ?? '')."\">
					<input type=hidden name=\"flag_admi\" value=\"".($_POST['flag_admi'] ?? '')."\">
                    <input type=hidden name=\"dire_orig\" value=\"personal_familiares.php\">
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
			function f_agregar_familiar()
			{
                document.form.action='personal_familiares_registro.php';
                document.form.submit();
			}
			function f_editar(codi)
			{
			    document.form.iden_fami.value=codi;
				document.form.action='personal_familiares_registro.php';
				document.form.submit();
			}
			function f_eliminar(codi,appa,apma,nomb)
			{
			    if(confirm('Seguro que desea eliminar familiar: '+appa+' '+apma+', '+nomb))
			    {
			        document.form.iden_fami_elim.value=codi;
				    document.form.action='personal_familiares.php';
				    document.form.submit();
			    }
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
		echo"Datos Familiares<BR>".$_POST['appa_pers']." ".$_POST['apma_pers'].", ".$_POST['nomb_pers'];
	else
		echo"Crear Nuevo Personal";
?>
	</h4></b></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="iden_fami">
			<input type=hidden name="iden_fami_elim">
			<input type=hidden name="iden_pers" value="<?= htmlspecialchars($_POST['iden_pers'] ?? '') ?>">
			<input type=hidden name="busq_pagi_actu" value="<?= htmlspecialchars($_POST['busq_pagi_actu'] ?? '') ?>">
			<input type=hidden name="codi_form" value="<?= htmlspecialchars($_POST['codi_form'] ?? '') ?>">
			<input type=hidden name="flag_admi" value="<?= htmlspecialchars($_POST['flag_admi'] ?? '') ?>">
			<input type=hidden name="dire_orig" value="personal_familiares.php">
			
<?
	$html=new htmlclass;

    $arra_options_vive[1]="SI";
    $arra_options_vive[0]="NO";
    $arra_options_pare=$Db->get_options('mp_maes_tipo_familiar',1,0);
    $arra_options_tdoc=$Db->get_options('mp_maes_tdocumento',1,0);
    $arra_options_sexo=$Db->get_options('mp_maes_sexo',1,0);
    $arra_options_ocup=$Db->get_options('mp_maes_ocupacion',1,0);
    

    $busc_item_pagi=100;      //cantidad de items por pagina
        $result=$Db->query("select * from mp_admi_pers_fami where iden_pers='$_POST[iden_pers]' AND esta_fami='1'");
    	$busc_tota_item=0;
    	foreach($result as $rows)
    	{       
    		$busc_tota_item++;
    	}

    	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
    	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

    	$result_pagi=$Db->query("select * from mp_admi_pers_fami where iden_pers='$_POST[iden_pers]' AND esta_fami='1' order by iden_tipo,appa_fami,apma_fami,nomb_fami limit $busc_limi_pagi,$busc_item_pagi");
    	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
    	echo $html->put_title_demand("FAMILIARES REGISTRADOS: $busc_tota_item Familiares");

    	if($busc_tota_pagi>0  OR 5==5)
    		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
    	$head=['1'=>"Nº",'2'=>"NRO.DOC.",'3'=>"APELLIDOS Y NOMBRES",'4'=>"PARENTESCO",'5'=>"SEXO",'6'=>"VIVE",'7'=>"EDIT",'8'=>"ELIM"];
    	echo $html->put_table_responsive_open();
    	if($busc_tota_item OR 5==5)
    	{
    		echo $html->put_table_responsive_header($head);
    		$cont=$busc_limi_pagi;
    		$colo = ''; // Inicializar para evitar warning
    		foreach($result_pagi as $rows)
    		{
    			$cont++;
    	
    			$data=[	'1'=>$colo.$cont,
    				'2'=>$rows['ndoc_fami'],
    				'3'=>utf8_encode(utf8_decode(strtoupper($rows['appa_fami'].' '.$rows['apma_fami'].', '.$rows['nomb_fami']))),  
    				'4'=>$arra_options_pare[$rows['iden_tipo']],
    				'5'=>$arra_options_sexo[$rows['iden_sexo']],
    				'6'=>$arra_options_vive[$rows['vive_fami']],
    				'7'=>"<a href=\"javascript:f_editar('$rows[iden_fami]')\"><img src=\"img/icons/edit.svg\" width=\"20\">",
    				'8'=>"<a href=\"javascript:f_eliminar('$rows[iden_fami]','$rows[appa_fami]','$rows[apma_fami]','$rows[nomb_fami]')\"><img src=\"img/icons/trash.svg\" width=\"20\">",
    			];
    			echo $html->put_table_responsive_data($head,$data);
    		}
    	}
    	else
    		echo $html->put_table_responsive_title("Usuario no tiene familiares registrados");
		
    	echo $html->put_table_responsive_close();
    	if($busc_tota_pagi>0  OR 5==5)
    		echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
    	echo"</div>";



	echo $html->put_separator_demand("30");
    if($_POST['flag_admi']==1) //si es administrador
    {
                echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_cancelar_documento()\">&laquo; Nueva B&uacute;squeda</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"return f_agregar_familiar()\">Agregar Familiar &raquo;</button>
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
                                                <button class=\"button_foot\" onclick=\"f_cancelar_documento()\">&laquo; Cancelar</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"return f_agregar_familiar()\">Agregar Familiar &raquo;</button>
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
