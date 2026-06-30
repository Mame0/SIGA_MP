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
        $_POST['desd_curs']=str_replace("-","",$_POST['desd_curs']);
        $_POST['hast_curs']=str_replace("-","",$_POST['hast_curs']);
		if($_POST['iden_pers'] AND $_POST['iden_curs'])
		{
			$result=$Db->update('mp_admi_pers_curs',['iden_pers'=>$_POST['iden_pers'],'nomb_curs'=>$_POST['nomb_curs'],'iden_inst'=>$_POST['iden_inst'],'nota_curs'=>$_POST['nota_curs'],'desd_curs'=>$_POST['desd_curs'],'hast_curs'=>$_POST['hast_curs'],'nhor_curs'=>$_POST['nhor_curs'],'digi_curs'=>$_SESSION['iden_oper'],'fdig_curs'=>$fdig,'esta_curs'=>'1'],['iden_curs'=>$_POST['iden_curs']]);
		}
		else
		{
			$result=$Db->insert('mp_admi_pers_curs',['iden_pers'=>$_POST['iden_pers'],'nomb_curs'=>$_POST['nomb_curs'],'iden_inst'=>$_POST['iden_inst'],'nota_curs'=>$_POST['nota_curs'],'desd_curs'=>$_POST['desd_curs'],'hast_curs'=>$_POST['hast_curs'],'nhor_curs'=>$_POST['nhor_curs'],'digi_curs'=>$_SESSION['iden_oper'],'fdig_curs'=>$fdig,'esta_curs'=>'1']);
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
	
	if($_POST['iden_curs'])
	{
	    $result_personal=$Db->select('mp_admi_pers_curs', ['iden_pers'=>$_POST['iden_pers'],'iden_curs'=>$_POST['iden_curs']], '', '', '');
	    $_POST['nomb_curs']=$result_personal[0]['nomb_curs'];
	    $_POST['iden_inst']=$result_personal[0]['iden_inst'];
	    $_POST['nota_curs']=$result_personal[0]['nota_curs'];
	    $_POST['nhor_curs']=$result_personal[0]['nhor_curs'];
	    $_POST['desd_curs']=substr($result_personal[0]['desd_curs'],0,4).'-'.substr($result_personal[0]['desd_curs'],4,2).'-'.substr($result_personal[0]['desd_curs'],6,2);
	    $_POST['hast_curs']=substr($result_personal[0]['hast_curs'],0,4).'-'.substr($result_personal[0]['hast_curs'],4,2).'-'.substr($result_personal[0]['hast_curs'],6,2);
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
				if(document.form.nomb_curs.value=='')
				{
					alert('Ingrese nombre del Curso');
					document.form.nomb_curs.focus();
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
		echo"Cursos y/o Especializaci&0acute;n<BR>".$_POST['appa_pers']." ".$_POST['apma_pers'].", ".$_POST['nomb_pers'];
	else
		echo"Crear Nuevo Personal";
?>
	</h4></b></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="iden_pers" value="<?=$_POST['iden_pers']?>">
			<input type=hidden name="iden_curs" value="<?=$_POST['iden_curs']?>">
			<input type=hidden name="busq_tipo" value="<?=$_POST['busq_tipo']?>">
			<input type=hidden name="busq_dato" value="<?=$_POST['busq_dato']?>">
			<input type=hidden name="codi_depe" value="<?=$_POST['codi_depe']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">
			<input type=hidden name="codi_form" value="<?=$_POST['codi_form']?>">
			<main>
<?
	$html=new htmlclass;

    $arra_options_inst=$Db->get_options('mp_maes_grado_instituciones',1,0);
    
    if($_POST['iden_grad'])
	    echo $html->put_title_demand("Editar Grado y/o T&iacute;tulo []");
	else
	    echo $html->put_title_demand("Agregar Nuevo Grado y/o T&iacute;tulo");
	
	echo $html->put_text('text',"Nombre.&nbsp;Curso&nbsp;(*)","Ingrese Nombre",'nomb_curs',$_POST['nomb_curs'],'','50','');
	echo $html->put_select("Centro&nbsp;de&nbsp;Estudios&nbsp;(*)",'iden_espe',$arra_options_inst,$_POST['iden_espe'],"");
	echo $html->put_text('number',"Nota&nbsp;Obtenida","",'nota_curs',$_POST['nota_curs'],'','20','');
	echo"</main><main>";
	echo $html->put_text('date',"Desde","",'desd_curs',$_POST['desd_curs'],'','20','');
	echo $html->put_text('date',"Hasta","",'hast_curs',$_POST['hast_curs'],'','20','');
	echo $html->put_text('number',"Nro.&nbsp;Horas","",'nhor_curs',$_POST['nhor_curs'],'','20','');

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
