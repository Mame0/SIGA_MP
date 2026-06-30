<?php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");
	
	if(empty($_POST['iden_pers']))
	{
	    echo"
            <html><body>
                <form name=\"form\" method=post action=\"personal_educacion.php\">
                    <input type=hidden name=\"iden_pers\" value=\"".htmlspecialchars($_POST['iden_pers'])."\">
					<input type=hidden name=\"codi_form\" value=\"".htmlspecialchars($_POST['codi_form'])."\">
					<input type=hidden name=\"flag_admi\" value=\"".$_POST['flag_admi']."\">
                </form>
                <script>document.form.submit();</script>
            </body></html>
		";
        exit;
	}

	if(!empty($_POST['guardar_personal']))
	{
		$fdig=date("YmdHis");
        $_POST['desd_curs']=str_replace("-","",$_POST['desd_curs']);
        $_POST['hast_curs']=str_replace("-","",$_POST['hast_curs']);

        //campo correcto 'iden_inst' y nuevo campo 'otro_inst'
        $data = [
            'iden_pers' => $_POST['iden_pers'],
            'nomb_curs' => $_POST['nomb_curs'],
            'iden_inst' => $_POST['iden_inst'] ?? null,
            'otro_inst' => $_POST['otro_inst'] ?? null, // Nuevo campo
            'nota_curs' => $_POST['nota_curs'] ?? null,
            'desd_curs' => $_POST['desd_curs'],
            'hast_curs' => $_POST['hast_curs'],
            'nhor_curs' => $_POST['nhor_curs'],
            'digi_curs' => $_SESSION['iden_oper'],
            'fdig_curs' => $fdig,
            'esta_curs' => '1'
        ];

		if(!empty($_POST['iden_curs']))
		{
			$result=$Db->update('mp_admi_pers_curs', $data, ['iden_curs'=>$_POST['iden_curs']]);
		}
		else
		{
			$result=$Db->insert('mp_admi_pers_curs', $data);
            
			$_POST['iden_curs']=$Db->lastInsertId();
		}

		echo"
            <html><body>
                <form name=\"form\" method=post action=\"personal_educacion.php\">
                    <input type=hidden name=\"iden_pers\" value=\"".htmlspecialchars($_POST['iden_pers'])."\">
                    <input type=hidden name=\"flag_admi\" value=\"".$_POST['flag_admi']."\">
                    <input type=hidden name=\"dire_orig\" value=\"personal_educacion.php\">
                </form>
                <script>
                    alert('Registro de curso guardado correctamente.');
                    document.form.submit();
                </script>
            </body></html>
		";
        exit;
	}

	$result_personal=$Db->select('mp_admi_pers', ['iden_pers'=>$_POST['iden_pers']]);
	$_POST['appa_pers']=$result_personal[0]['appa_pers'];
	$_POST['apma_pers']=$result_personal[0]['apma_pers'];
	$_POST['nomb_pers']=$result_personal[0]['nomb_pers'];
	
	if(!empty($_POST['iden_curs']))
	{
	    $result_personal=$Db->select('mp_admi_pers_curs', ['iden_curs'=>$_POST['iden_curs']]);
	    if ($result_personal) {
	        $datos_curso = $result_personal[0];
            foreach($datos_curso as $key => $value) { $_POST[$key] = $value; }
            $_POST['desd_curs']=!empty($datos_curso['desd_curs']) ? substr($datos_curso['desd_curs'],0,4).'-'.substr($datos_curso['desd_curs'],4,2).'-'.substr($datos_curso['desd_curs'],6,2) : '';
	        $_POST['hast_curs']=!empty($datos_curso['hast_curs']) ? substr($datos_curso['hast_curs'],0,4).'-'.substr($datos_curso['hast_curs'],4,2).'-'.substr($datos_curso['hast_curs'],6,2) : '';
	    }
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Registro de Cursos</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<script>
			function f_guardar_personal()
			{
				if(document.form.nomb_curs.value=='')
				{
					alert('Ingrese nombre del Curso');
					document.form.nomb_curs.focus();
					return false;
				}
				
				// Validar que se seleccione una institución o se ingrese otra
				if(document.form.iden_inst.selectedIndex==0 && document.form.otro_inst.value.trim()=='')
				{
					alert('Seleccione un Centro de Estudios o ingrese Otro Centro de Estudios');
					document.form.iden_inst.focus();
					return false;
				}
				
				if(confirm('¿Seguro que desea Guardar?')) {
                    document.form.guardar_personal.value='1';
                    document.form.submit();
                }
				else
				{
					return false;
				}
			}
			function f_cancelar_documento()
			{
			    document.form.action='personal_educacion.php';
				document.form.submit();
			}
			
			// Función mejorada para manejar la habilitación/deshabilitación de campos
			function toggleCamposInstitucion() {
				var selectInst = document.form.iden_inst;
				var otroInst = document.form.otro_inst;
				
				if(otroInst.value.trim() != '') {
					selectInst.selectedIndex = 0;
					selectInst.disabled = true;
				} 
				else {
					selectInst.disabled = false;
				}
				
				if(selectInst.selectedIndex > 0) {
					otroInst.value = '';
					otroInst.disabled = true;
				} 
				// Si no hay opción seleccionada en el select, habilitar "otro_inst"
				else if(selectInst.selectedIndex == 0) {
					otroInst.disabled = false;
				}
			}
			
			function onChangeSelect() {
				toggleCamposInstitucion();
			}
			
			function onKeyUpOtroInst() {
				toggleCamposInstitucion();
			}
			
			// Función para detectar cuando se borra contenido del campo "otro_inst"
			function onInputOtroInst() {
				var otroInst = document.form.otro_inst;
				var selectInst = document.form.iden_inst;
				
				if(otroInst.value.trim() == '') {
					selectInst.disabled = false;
				} else {
					selectInst.selectedIndex = 0;
					selectInst.disabled = true;
				}
			}
			
			function ajustar_altura()
            {
                if(parent.document.getElementById('body_iframe'))
                    parent.document.getElementById('body_iframe').height=parent.window.innerHeight-80;
            }
            ajustar_altura();
		</script>
	</head>
	<body style="margin-bottom: 30px;">
	<center><h4 style="color:#073a6b"><b>
    <?php
        if($_POST['iden_pers'])
            echo"Cursos y/o Especializaci&oacute;n<BR>".htmlspecialchars($_POST['appa_pers']." ".$_POST['apma_pers'].", ".$_POST['nomb_pers']);
    ?>
	</h4></b></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="iden_pers" value="<?=htmlspecialchars($_POST['iden_pers'] ?? '')?>">
			<input type=hidden name="iden_curs" value="<?=htmlspecialchars($_POST['iden_curs'] ?? '')?>">
			<input type=hidden name="flag_admi" value="<?=$_POST['flag_admi']?>">
			<input type=hidden name="dire_orig" value="personal_educacion.php">
			<main>
            <?php
                $html=new htmlclass;

                $arra_options_inst=$Db->get_options('mp_maes_grado_instituciones',1,0);
                
                if($_POST['iden_curs'])
                    echo $html->put_title_demand("Editar Curso y/o Especializaci&oacute;n");
                else
                    echo $html->put_title_demand("Agregar Nuevo Curso y/o Especializaci&oacute;n");
                
                echo $html->put_text('text',"Nombre&nbsp;Curso&nbsp;(*)","Ingrese Nombre",'nomb_curs',$_POST['nomb_curs'] ?? '','','100','');
                echo $html->put_select_buscador("Centro&nbsp;de&nbsp;Estudios&nbsp;(*)",'iden_inst',$arra_options_inst,$_POST['iden_inst'] ?? '',"onchange=\"onChangeSelect()\"");
                echo $html->put_text('text',"Otro&nbsp;Centro&nbsp;de&nbsp;Estudios","Ingrese otro centro de estudios",'otro_inst',$_POST['otro_inst'] ?? '','oninput="onInputOtroInst()" onkeyup="onKeyUpOtroInst()"','100','');
                echo"</main><main>";
                echo $html->put_text('date',"Desde","",'desd_curs',$_POST['desd_curs'] ?? '','','20','');
                echo $html->put_text('date',"Hasta","",'hast_curs',$_POST['hast_curs'] ?? '','','20','');
                echo $html->put_text('number',"Nro.&nbsp;Horas","",'nhor_curs',$_POST['nhor_curs'] ?? '','','20','');
                echo"</main><main>";
                //echo $html->put_text('number',"Nota&nbsp;Obtenida","",'nota_curs',$_POST['nota_curs'] ?? '','','20','');

                echo"</main>";

                echo $html->put_separator_demand("30");

                echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button type=\"button\" class=\"button_foot\" onclick=\"f_cancelar_documento()\">&laquo; Cancelar</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button type=\"button\" class=\"button_foot\" onclick=\"return f_guardar_personal()\">Guardar &raquo;</button>
                                        </center></div>
                                </div>
                                </center>
                        </div>
                ";
            ?>
            <script>
                window.onload = function() {
                    toggleCamposInstitucion();
                };
            </script>
		</form>
	</body>
</html>