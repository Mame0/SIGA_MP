<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	require_once 'include/registrar_acceso.php';

	$fdig=date("YmdHis");
	
	if($_POST['iden_pers_edit'])
	{
	    unset($_POST['iden_pers']);
	    $_SESSION['iden_pers_edit']=$_POST['iden_pers_edit'];
	}
	
	if(!$_POST['iden_pers'])
	{
	    if($_GET['flag_admi']==1)
	        $_POST['flag_admi']=$_GET['flag_admi'];
	    if($_POST['flag_admi']==1) //si es administrador
        {
            if($_SESSION['iden_pers_edit'])
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
                        <input type=hidden name=\"iden_pers\" value=\"".htmlspecialchars($_POST['iden_pers'])."\">
                        <input type=hidden name=\"flag_admi\" value=\"".$_POST['flag_admi']."\">
                        <input type=hidden name=\"dire_orig\" value=\"personal_idiomas.php\">
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

	if($_POST['agregar_idioma'])
	{
	    $cont=0;
	    $codi=$_POST['iden_idio'];
	    $posi=strpos($codi,'-');
	    $_POST['iden_idio']=substr($codi,0,$posi);
	    $_POST['iden_nive']=substr($codi,$posi+1);
	    $result=$Db->query("select * from mp_admi_pers_idio where iden_pers='$_POST[iden_pers]' AND iden_idio='$_POST[iden_idio]'");
	    foreach($result as $rows)
	        $cont++;
	    if($cont==0)
	        $result=$Db->insert('mp_admi_pers_idio',['iden_pers'=>$_POST['iden_pers'],'iden_idio'=>$_POST['iden_idio'],'iden_nive'=>$_POST['iden_nive']]);
	}
	
	if($_POST['eliminar_idioma'])
	{
	    $result=$Db->delete('mp_admi_pers_idio',['iden_pers'=>$_POST['iden_pers'],'iden_idio'=>$_POST['eliminar_idioma']]);
	}
	
	if($_POST['guardar_personal'])
	{
		$fdig=date(YmdHis);
		//$_POST['esta_pers']=1;
		if($_POST['iden_pers'])
		{
			$result=$Db->update('mp_admi_pers',['idio_pers'=>$_POST['idio_pers']],['iden_pers'=>$_POST['iden_pers']]);
		}
		else
		{
			$result=$Db->insert('mp_admi_pers',['idio_pers'=>$_POST['idio_pers']]);
			$_POST['iden_pers']=$Db->lastInsertId();
		}

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"personal_idiomas.php\">
                    <input type=hidden name=\"iden_pers\" value=\"".$_POST['iden_pers']."\">
					<input type=hidden name=\"busq_tipo\" value=\"".$_POST['busq_tipo']."\">
					<input type=hidden name=\"busq_dato\" value=\"".$_POST['busq_dato']."\">
					<input type=hidden name=\"busq_pagi_actu\" value=\"".$_POST['busq_pagi_actu']."\">
					<input type=hidden name=\"codi_form\" value=\"".$_POST['codi_form']."\">
					<input type=hidden name=\"flag_admi\" value=\"".$_POST['flag_admi']."\">
                    <input type=hidden name=\"dire_orig\" value=\"personal_idiomas.php\">
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
	$_POST['idio_pers']=$result_personal[0]['idio_pers'];
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
			function f_agregar_idioma()
			{
			    document.form.agregar_idioma.value='1';
				document.form.action='personal_idiomas.php';
				document.form.submit();
			}
			function f_eliminar(codi,nume)
			{
			    if(confirm('Seguro que desea eliminar item Nro. '+nume+'?'))
			    document.form.eliminar_idioma.value=codi;
				document.form.action='personal_idiomas.php';
				document.form.submit();
			}
			function f_buscar_personal()
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
		echo"Idiomas<BR>".$_POST['appa_pers']." ".$_POST['apma_pers'].", ".$_POST['nomb_pers'];
	else
		echo"Crear Nuevo Personal";
?>
	</h4></b></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="agregar_idioma">
			<input type=hidden name="eliminar_idioma">
			<input type=hidden name="iden_pers" value="<?=$_POST['iden_pers']?>">
			<input type=hidden name="busq_tipo" value="<?=$_POST['busq_tipo']?>">
			<input type=hidden name="busq_dato" value="<?=$_POST['busq_dato']?>">
			<input type=hidden name="codi_depe" value="<?=$_POST['codi_depe']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">
			<input type=hidden name="codi_form" value="<?=$_POST['codi_form']?>">
			<input type=hidden name="flag_admi" value="<?=$_POST['flag_admi']?>">
			<input type=hidden name="dire_orig" value="personal_idiomas.php">
<?
	$html=new htmlclass;
    
    //$arra_options_tsan=$Db->get_options('mp_maes_grupo_sanguineo',1,0);
    $arra_options_idio=$Db->get_options('mp_maes_idiomas',1,0);
    $arra_options_nive=$Db->get_options('mp_maes_idiomas_nivel',1,0);
    
    $arra_options_idio_nive[0]="<- Seleccione Idioma - Nivel ->";
    $result1=$Db->query("select * from mp_maes_idiomas where n_estado='1' order by x_nombre");
    foreach($result1 as $rows1)
    {
        $result2=$Db->query("select * from mp_maes_idiomas_nivel where n_estado='1' order by n_codigo");
        foreach($result2 as $rows2)
        {
            $codi=$rows1['n_codigo'].'-'.$rows2['n_codigo'];
            $nomb=$rows1['x_nombre'].' - NIVEL: '.$rows2['x_nombre'];
            $arra_options_idio_nive[$codi]=$nomb;
        }
    }
    
	//echo $html->put_title_demand("Variable &Eacute;tnica");
	echo"<main>";
	echo $html->put_select("Agregar&nbsp;Idioma&nbsp;Nivel",'iden_idio',$arra_options_idio_nive,'',"onchange='f_agregar_idioma()'");
	echo"</main>";
	echo"<BR>";
	$result_pagi=$Db->query("select * from mp_admi_pers_idio where iden_pers='$_POST[iden_pers]'");
    echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
    $head=['1'=>"Nº",'2'=>"IDIOMA",'3'=>"NIVEL",'4'=>"ELIMINAR"];
    echo $html->put_table_responsive_open();
    $cont=0;
	echo $html->put_table_responsive_header($head);
	foreach($result_pagi as $rows)
	{
		$cont++;
		$data=[	'1'=>$cont,
			'2'=>$arra_options_idio[$rows['iden_idio']],
			'3'=>$arra_options_nive[$rows['iden_nive']],
			'4'=>"<a href=\"javascript:f_eliminar('$rows[iden_idio]','$cont')\"><img src=\"img/icons/trash.svg\" width=\"20\">",
		];
		echo $html->put_table_responsive_data($head,$data);
	}
    //if($cont==0)
    //	echo $html->put_table_responsive_title("Usuario no tiene Enfermedades");
		
    echo $html->put_table_responsive_close();
    echo"</div>";

    echo"<main>";
	echo $html->put_text('text',"Otro&nbsp;Idioma","Ingrese Otro Idioma",'idio_pers',$_POST['idio_pers'],'','20','');
	echo"</main>";
	
	echo $html->put_separator_demand("30");
    if($_GET['flag_admi']==1 OR $_POST['flag_admi']==1) //si es administrador
    {
                echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_buscar_personal()\">&laquo; Nueva B&uacute;squeda</button>
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
