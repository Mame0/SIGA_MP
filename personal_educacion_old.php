<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");
	
	if(!$_POST['iden_pers'])
	{
	    $result=$Db->query("select * from mp_admi_pers where ndoc_pers='$_SESSION[ndoc_oper]'");
        foreach($result as $rows)
            $_POST['iden_pers']=$rows['iden_pers'];
	}

	if($_POST['eliminar_grado'])
	{
	    $result=$Db->delete('mp_admi_pers_grad',['iden_grad'=>$_POST['eliminar_grado']]);
	}
	
	if($_POST['eliminar_curso'])
	{
	    $result=$Db->delete('mp_admi_pers_curs',['iden_curs'=>$_POST['eliminar_curso']]);
	}
	
	if($_POST['guardar_personal'])
	{
		$fdig=date(YmdHis);
		//$_POST['esta_pers']=1;
		if($_POST['iden_pers'])
		{
			$result=$Db->update('mp_admi_pers',['iden_nedu'=>$_POST['iden_nedu'],'esta_nedu'=>$_POST['esta_nedu'],'inst_nedu'=>$_POST['inst_nedu'],'afin_nedu'=>$_POST['afin_nedu']],['iden_pers'=>$_POST['iden_pers']]);
		}
		else
		{
			$result=$Db->insert('mp_admi_pers',['iden_nedu'=>$_POST['iden_nedu'],'esta_nedu'=>$_POST['esta_nedu'],'inst_nedu'=>$_POST['inst_nedu'],'afin_nedu'=>$_POST['afin_nedu']]);
			$_POST['iden_pers']=$Db->lastInsertId();
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
	$_POST['iden_nedu']=$result_personal[0]['iden_nedu'];
	$_POST['esta_nedu']=$result_personal[0]['esta_nedu'];
	$_POST['inst_nedu']=$result_personal[0]['inst_nedu'];
	$_POST['afin_nedu']=$result_personal[0]['afin_nedu'];
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
				if(document.form.iden_nedu.selectedIndex=='0')
				{
					alert('Seleccione Nivel');
					document.form.iden_nedu.focus();
					return false;
				}
				else
				{
				    if(document.form.afin_nedu.selectedIndex=='0')
    				{
	    				alert('Seleccione Estado');
		    			document.form.afin_nedu.focus();
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
			function f_agregar_curso()
			{
			    document.form.iden_curs.value='';
			    document.form.action='personal_educacion_curso.php';
				document.form.submit();
			}
			function f_agregar_grado()
			{
			    document.form.iden_grad.value='';
			    document.form.action='personal_educacion_grado.php';
				document.form.submit();
			}
			function f_editar_curso(codi)
			{
			    document.form.iden_curs.value=codi;
			    document.form.action='personal_educacion_curso.php';
				document.form.submit();
			}
			function f_editar_grado(codi)
			{
			    document.form.iden_grad.value=codi;
			    document.form.action='personal_educacion_grado.php';
				document.form.submit();
			}
			function f_eliminar_grado(codi,nume)
			{
			    if(confirm('Seguro que desea eliminar item Nro '+nume+'?'))
			    {
			        document.form.eliminar_grado.value=codi;
				    document.form.action='personal_educacion.php';
				    document.form.submit();
			    }
			}
			function f_eliminar_curso(codi,nume)
			{
			    if(confirm('Seguro que desea eliminar item Nro '+nume+'?'))
			    {
			        document.form.eliminar_curso.value=codi;
				    document.form.action='personal_educacion.php';
				    document.form.submit();
			    }
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
		echo"Formaci&oacute;n Acad&eacute;mica<BR>".$_POST['appa_pers']." ".$_POST['apma_pers'].", ".$_POST['nomb_pers'];
	else
		echo"Crear Nuevo Personal";
?>
	</h4></b></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="iden_curs">
			<input type=hidden name="iden_grad">
			<input type=hidden name="eliminar_grado">
			<input type=hidden name="eliminar_curso">
			<input type=hidden name="iden_pers" value="<?=$_POST['iden_pers']?>">
			<input type=hidden name="busq_tipo" value="<?=$_POST['busq_tipo']?>">
			<input type=hidden name="busq_dato" value="<?=$_POST['busq_dato']?>">
			<input type=hidden name="codi_depe" value="<?=$_POST['codi_depe']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">
			<input type=hidden name="codi_form" value="<?=$_POST['codi_form']?>">
<?
	$html=new htmlclass;
    
    $arra_options_nedu=$Db->get_options('mp_maes_nivel_educativo',1,0);
    $arra_options_inst=$Db->get_options('mp_maes_grado_instituciones',1,0);
    
    $arra_options_nive=$Db->get_options('mp_maes_grado_nivel',1,0);
    
    $arra_options_esta[0]="Incompleta";
    $arra_options_esta[1]="Completa";
    
    for($x=1960;$x<date("Y");$x++)
        $arra_options_afin[$x]=$x;
    
    echo"<main>";
	echo $html->put_title_demand("Nivel Educativo");
	echo $html->put_select("Nivel",'iden_nedu',$arra_options_nedu,$_POST['iden_nedu'],"");
	echo $html->put_select("Completa/Incompleta",'esta_nedu',$arra_options_esta,$_POST['esta_nedu'],"");
	echo $html->put_select("Centro&nbsp;de&nbsp;Estudios",'inst_nedu',$arra_options_inst,$_POST['inst_nedu'],"");
	echo"</main><main>";
	echo $html->put_select("Año&nbsp;de&nbsp;Finalizaci&oacute;n",'afin_nedu',$arra_options_afin,$_POST['afin_nedu'],"");
	
	echo $html->put_title_demand("T&iacute;tulos y Grados","<a href=\"javascript:f_agregar_grado()\">Agregar&nbsp;T&iacutetulo</a>");
	echo"</main>";
	$result_pagi=$Db->query("select * from mp_admi_pers_grad where iden_pers='$_POST[iden_pers]'");
    echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
    $head=['1'=>"Nº",'2'=>"NIVEL",'3'=>"ESPECIALIDAD",'4'=>"ESTADO",'5'=>"INSTITUCION",'6'=>"EDIT",'7'=>"ELIM"];
    echo $html->put_table_responsive_open();
    $cont=0;
	echo $html->put_table_responsive_header($head);
	foreach($result_pagi as $rows)
	{
		$cont++;
		$data=[	'1'=>$cont,
			'2'=>$arra_options_nive[$rows['iden_nive']],
			'6'=>"<a href=\"javascript:f_editar_grado('$rows[iden_grad]')\"><img src=\"img/icons/edit.svg\" width=\"20\">",
			'7'=>"<a href=\"javascript:f_eliminar_grado('$rows[iden_grad]','$cont')\"><img src=\"img/icons/trash.svg\" width=\"20\">",
		];
		echo $html->put_table_responsive_data($head,$data);
	}
    //if($cont==0)
    //	echo $html->put_table_responsive_title("Usuario no tiene Enfermedades");
		
    echo $html->put_table_responsive_close();
    echo"</div>";

    echo"<main>";
    echo $html->put_title_demand("Cursos y/o Especialización","<a href=\"javascript:f_agregar_curso()\">Agregar&nbsp;Curso</a>");
    echo"</main>";
    
    $result_pagi=$Db->query("select * from mp_admi_pers_curs where iden_pers='$_POST[iden_pers]'");
    echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
    $head=['1'=>"Nº",'2'=>"DENOMINACION",'3'=>"INSTITUCION",'4'=>"DESDE/HASTA",'5'=>"HORAS",'6'=>"EDIT",'7'=>"ELIM"];
    echo $html->put_table_responsive_open();
    $cont=0;
	echo $html->put_table_responsive_header($head);
	foreach($result_pagi as $rows)
	{
		$cont++;
		$data=[	'1'=>$cont,
			'2'=>$rows['nomb_curs'],
			'6'=>"<a href=\"javascript:f_editar_curso('$rows[iden_curs]')\"><img src=\"img/icons/edit.svg\" width=\"20\">",
			'7'=>"<a href=\"javascript:f_eliminar_curso('$rows[iden_curs]','$cont')\"><img src=\"img/icons/trash.svg\" width=\"20\">",
		];
		echo $html->put_table_responsive_data($head,$data);
	}
    //if($cont==0)
    //	echo $html->put_table_responsive_title("Usuario no tiene Enfermedades");
		
    echo $html->put_table_responsive_close();
    echo"</div>";
    
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
