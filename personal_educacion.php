<?php
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
                        <input type=hidden name=\"dire_orig\" value=\"personal_educacion.php\">
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

	if(!empty($_POST['eliminar_grado'])) {
	    $result=$Db->delete('mp_admi_pers_grad',['iden_grad'=>$_POST['eliminar_grado']]);
	}
	
	if(!empty($_POST['eliminar_curso'])) {
	    $result=$Db->delete('mp_admi_pers_curs',['iden_curs'=>$_POST['eliminar_curso']]);
	}
	
	if(!empty($_POST['guardar_personal'])) {
		if($_POST['iden_pers']) {
			$result=$Db->update('mp_admi_pers',['iden_nedu'=>$_POST['iden_nedu'],'esta_nedu'=>$_POST['esta_nedu'],'inst_nedu'=>$_POST['inst_nedu'],'afin_nedu'=>$_POST['afin_nedu']],['iden_pers'=>$_POST['iden_pers']]);
		}
		echo"
            <html><body>
                <form name=\"form\" method=post action=\"personal_educacion.php\">
                    <input type=hidden name=\"iden_pers\" value=\"".htmlspecialchars($_POST['iden_pers'])."\">
                    <input type=hidden name=\"flag_admi\" value=\"".$_POST['flag_admi']."\">
                    <input type=hidden name=\"dire_orig\" value=\"personal_educacion.php\">
                </form>
                <script>document.form.submit();</script>
            </body></html>
		";
        exit;
	}
	
	$result_personal=$Db->select('mp_admi_pers', ['iden_pers'=>$_POST['iden_pers']]);
    if($result_personal) {
        $_POST['appa_pers']=$result_personal[0]['appa_pers'];
        $_POST['apma_pers']=$result_personal[0]['apma_pers'];
        $_POST['nomb_pers']=$result_personal[0]['nomb_pers'];
        $_POST['iden_nedu']=$result_personal[0]['iden_nedu'];
        $_POST['esta_nedu']=$result_personal[0]['esta_nedu'];
        $_POST['inst_nedu']=$result_personal[0]['inst_nedu'];
        $_POST['afin_nedu']=$result_personal[0]['afin_nedu'];
    }
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Formación Académica</title>
	    <link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<script>
			function f_guardar_personal() {
				if(document.form.iden_nedu.selectedIndex==0) { alert('Seleccione Nivel Educativo'); return false; }
				if(confirm('¿Seguro que desea Guardar?')) {
					document.form.guardar_personal.value='1';
					document.form.submit();
				}
			}
			function f_agregar_curso() { document.form.iden_curs.value=''; document.form.action='personal_educacion_curso.php'; document.form.submit(); }
			function f_agregar_grado() { document.form.iden_grad.value=''; document.form.action='personal_educacion_grado.php'; document.form.submit(); }
			function f_editar_curso(codi) { document.form.iden_curs.value=codi; document.form.action='personal_educacion_curso.php'; document.form.submit(); }
			function f_editar_grado(codi) { document.form.iden_grad.value=codi; document.form.action='personal_educacion_grado.php'; document.form.submit(); }
			function f_eliminar_grado(codi,nume) { if(confirm('¿Seguro que desea eliminar el item Nro '+nume+'?')) { document.form.eliminar_grado.value=codi; document.form.submit(); } }
			function f_eliminar_curso(codi,nume) { if(confirm('¿Seguro que desea eliminar el item Nro '+nume+'?')) { document.form.eliminar_curso.value=codi; document.form.submit(); } }
			function f_cancelar_documento() { window.close(); }
			function f_buscar_personal()
			{
				document.form.action='personal_buscar.php';
				document.form.submit();
			}
			function ajustar_altura() { if(parent.document.getElementById('body_iframe')) parent.document.getElementById('body_iframe').height=parent.window.innerHeight-80; }
            ajustar_altura();
		</script>
	</head>
	<body style="margin-bottom: 30px;">
	<center><h4 style="color:#073a6b"><b>
    <?php
        if($_POST['iden_pers']) echo"Formaci&oacute;n Acad&eacute;mica<BR>".htmlspecialchars($_POST['appa_pers']." ".$_POST['apma_pers'].", ".$_POST['nomb_pers']);
        else echo"Crear Nuevo Personal";
    ?>
	</h4></b></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="iden_curs">
			<input type=hidden name="iden_grad">
			<input type=hidden name="eliminar_grado">
			<input type=hidden name="eliminar_curso">
			<input type=hidden name="iden_pers" value="<?=htmlspecialchars($_POST['iden_pers'] ?? '')?>">
			<input type=hidden name="flag_admi" value="<?=htmlspecialchars($_POST['flag_admi'] ?? '')?>">
			<input type=hidden name="dire_orig" value="personal_educacion.php">
			<main>
        <?php
            $html=new htmlclass;
            
            $arra_options_nedu=$Db->get_options('mp_maes_nivel_educativo',1,0);
            $arra_options_inst=$Db->get_options('mp_maes_grado_instituciones',1,0);
            $arra_options_nive=$Db->get_options('mp_maes_grado_nivel',1,0);
            $arra_options_est_acad=$Db->get_options('mp_maes_grado_estado',1,0); // Estado Académico

            $arra_options_esta = [0 => "Incompleta", 1 => "Completa"];
            
            for($x=date("Y");$x>1960;$x--) $arra_options_afin[$x]=$x;
            
            echo $html->put_title_demand("Nivel Educativo");
            echo $html->put_select("Nivel",'iden_nedu',$arra_options_nedu,$_POST['iden_nedu'],"");
            echo $html->put_select("Completa/Incompleta",'esta_nedu',$arra_options_esta,$_POST['esta_nedu'],"");
            echo $html->put_select_buscador("Centro&nbsp;de&nbsp;Estudios",'inst_nedu',$arra_options_inst,$_POST['inst_nedu'],"");
            echo"</main><main>";
            echo $html->put_select("A&ntilde;o&nbsp;de&nbsp;Finalizaci&oacute;n",'afin_nedu',$arra_options_afin,$_POST['afin_nedu'],"");
            echo"</main><main>";
            echo"</main>";
            
            echo"<main>";
            echo $html->put_title_demand("T&iacute;tulos y Grados","<a href=\"javascript:f_agregar_grado()\">Agregar&nbsp;T&iacute;tulo</a>");
            echo"</main>";

            // --- LISTADO DE TÍTULOS Y GRADOS CON NOMBRES ---
            $query_grados = "SELECT g.*, n.x_nombre as nomb_nive, e.x_nombre as nomb_espe, i.x_nombre as nomb_inst, es.x_nombre as nomb_esta
                             FROM mp_admi_pers_grad g
                             LEFT JOIN mp_maes_grado_nivel n ON g.iden_nive = n.n_codigo
                             LEFT JOIN mp_maes_grado_especialidades e ON g.iden_espe = e.n_codigo
                             LEFT JOIN mp_maes_grado_instituciones i ON g.iden_inst = i.n_codigo
                             LEFT JOIN mp_maes_grado_estado es ON g.iden_esta = es.n_codigo
                             WHERE g.iden_pers = :iden_pers AND g.esta_grad = 1";
            $result_pagi_grados = $Db->query($query_grados, [':iden_pers' => $_POST['iden_pers']]);
            
            echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
            $head_grados=['1'=>"Nº",'2'=>"NIVEL",'3'=>"ESPECIALIDAD",'4'=>"ESTADO",'5'=>"INSTITUCION",'6'=>"EDIT",'7'=>"ELIM"];
            echo $html->put_table_responsive_open();
            $cont=0;
            echo $html->put_table_responsive_header($head_grados);
            if ($result_pagi_grados) {
                foreach($result_pagi_grados as $rows) {
                    $cont++;
                    $data=[	
                        '1'=>$cont,
                        '2'=>htmlspecialchars($rows['nomb_nive']),
                        '3'=>htmlspecialchars($rows['nomb_espe']),
                        '4'=>htmlspecialchars($rows['nomb_esta']),
                        '5'=>htmlspecialchars($rows['nomb_inst']),
                        '6'=>"<a href=\"javascript:f_editar_grado('{$rows['iden_grad']}')\"><img src=\"img/icons/edit.svg\" width=\"20\"></a>",
                        '7'=>"<a href=\"javascript:f_eliminar_grado('{$rows['iden_grad']}','{$cont}')\"><img src=\"img/icons/trash.svg\" width=\"20\"></a>",
                    ];
                    echo $html->put_table_responsive_data($head_grados,$data);
                }
            }
            if($cont==0) echo $html->put_table_responsive_title("No tiene grados/títulos registrados");
            echo $html->put_table_responsive_close();
            echo"</div>";

            echo"<main>";
            echo $html->put_title_demand("Cursos y/o Especializaci&oacute;n","<a href=\"javascript:f_agregar_curso()\">Agregar&nbsp;Curso</a>");
            echo"</main>";
            
            // --- LISTADO DE CURSOS CON NOMBRES ---
            $query_cursos = "SELECT c.*, i.x_nombre as nomb_inst
                             FROM mp_admi_pers_curs c
                             LEFT JOIN mp_maes_grado_instituciones i ON c.iden_inst = i.n_codigo
                             WHERE c.iden_pers = :iden_pers AND c.esta_curs = 1";
            $result_pagi_cursos=$Db->query($query_cursos, [':iden_pers' => $_POST['iden_pers']]);
            
            echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
            $head_cursos=['1'=>"Nº",'2'=>"DENOMINACIÓN",'3'=>"INSTITUCIÓN",'4'=>"DESDE/HASTA",'5'=>"HORAS",'6'=>"EDIT",'7'=>"ELIM"];
            echo $html->put_table_responsive_open();
            $cont=0;
            echo $html->put_table_responsive_header($head_cursos);
            if($result_pagi_cursos){
                foreach($result_pagi_cursos as $rows) {
                    $cont++;
                    
                    // Determinar qué institución mostrar: la del combo o la de "otro"
                    $institucion_mostrar = '';
                    if (!empty($rows['otro_inst'])) {
                        $institucion_mostrar = htmlspecialchars($rows['otro_inst']);
                    } elseif (!empty($rows['nomb_inst'])) {
                        $institucion_mostrar = htmlspecialchars($rows['nomb_inst']);
                    } else {
                        $institucion_mostrar = '-';
                    }
                    
                    $data=[	
                        '1'=>$cont,
                        '2'=>htmlspecialchars($rows['nomb_curs']),
                        '3'=>$institucion_mostrar,
                        '4'=>((strlen($rows['desd_curs'])==8 ? substr($rows['desd_curs'],6,2).'/'.substr($rows['desd_curs'],4,2).'/'.substr($rows['desd_curs'],0,4) : ($rows['desd_curs'] ?: '-')) . 
                        ' - ' . 
                        (strlen($rows['hast_curs'])==8 ? substr($rows['hast_curs'],6,2).'/'.substr($rows['hast_curs'],4,2).'/'.substr($rows['hast_curs'],0,4) : ($rows['hast_curs'] ?: '-'))),
                        '5'=>$rows['nhor_curs'],
                        '6'=>"<a href=\"javascript:f_editar_curso('{$rows['iden_curs']}')\"><img src=\"img/icons/edit.svg\" width=\"20\"></a>",
                        '7'=>"<a href=\"javascript:f_eliminar_curso('{$rows['iden_curs']}','{$cont}')\"><img src=\"img/icons/trash.svg\" width=\"20\"></a>",
                    ];
                    echo $html->put_table_responsive_data($head_cursos,$data);
                }
            }
            if($cont==0) echo $html->put_table_responsive_title("No tiene cursos registrados");
            echo $html->put_table_responsive_close();
            echo"</div>";
            
            echo $html->put_separator_demand("30");
            if((isset($_GET['flag_admi']) && $_GET['flag_admi']==1) OR (isset($_POST['flag_admi']) && $_POST['flag_admi']==1)) //si es administrador
            {
                echo"
			        <div align=center class=\"foot\">
                    <center>
                    <div align=center class=\"foot2\">
                        <div class=\"div_button_foot\"><button type=\"button\" class=\"button_foot\" onclick=\"f_buscar_personal()\">&laquo; Nueva B&uacute;squeda</button></div>
                        <div class=\"div_button_foot\"><center><button type=\"button\" class=\"button_foot\" onclick=\"return f_guardar_personal()\">Guardar &raquo;</button></center></div>
                    </div>
                    </center>
                    </div>
                ";
            }
            else
            {
                echo"
			        <div align=center class=\"foot\">
                    <center>
                    <div align=center class=\"foot2\">
                        <div class=\"div_button_foot\"><button type=\"button\" class=\"button_foot\" onclick=\"reset()\">&laquo; Cancelar</button></div>
                        <div class=\"div_button_foot\"><center><button type=\"button\" class=\"button_foot\" onclick=\"return f_guardar_personal()\">Guardar &raquo;</button></center></div>
                    </div>
                    </center>
                    </div>
                ";
            }
        ?>
		</form>
	</body>
</html>