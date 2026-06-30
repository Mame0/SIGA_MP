<?php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");
	
	if(empty($_POST['iden_pers'])) {
	    echo "<script>window.location.href='personal_mantenimiento.php';</script>"; exit;
	}

	if(!empty($_POST['guardar_personal'])) {
		$fdig=date("YmdHis");
        $_POST['desd_grad']=str_replace("-","",$_POST['desd_grad']);
        $_POST['hast_grad']=str_replace("-","",$_POST['hast_grad']);
        $_POST['fech_grad']=str_replace("-","",$_POST['fech_grad']);

        $data = [
            'iden_pers' => $_POST['iden_pers'],
            'iden_nive' => $_POST['iden_nive'],
            'iden_esta' => $_POST['iden_esta'],
            'iden_inst' => $_POST['iden_inst'],
            'iden_espe' => $_POST['iden_espe'],
            'ntit_grad' => $_POST['ntit_grad'],
            'ncol_grad' => $_POST['ncol_grad'],
            'desd_grad' => $_POST['desd_grad'],
            'hast_grad' => $_POST['hast_grad'],
            'fech_grad' => $_POST['fech_grad'],
            'digi_grad' => $_SESSION['iden_oper'],
            'fdig_grad' => $fdig,
            'esta_grad' => '1'
        ];

		if(!empty($_POST['iden_grad'])) {
			$result=$Db->update('mp_admi_pers_grad', $data, ['iden_grad'=>$_POST['iden_grad']]);
		} else {
			$result=$Db->insert('mp_admi_pers_grad', $data);
		}

		echo"
            <html><body>
                <form name=\"form\" method=post action=\"personal_educacion.php\">
                    <input type=hidden name=\"iden_pers\" value=\"".htmlspecialchars($_POST['iden_pers'])."\">
                    <input type=hidden name=\"flag_admi\" value=\"".$_POST['flag_admi']."\">
                    <input type=hidden name=\"dire_orig\" value=\"personal_educacion.php\">
                </form>
                <script>
                    alert('Registro de grado/título guardado.');
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
	
    $especialidad_actual = [];
	if(!empty($_POST['iden_grad'])) {
	    $result_grado=$Db->select('mp_admi_pers_grad', ['iden_grad'=>$_POST['iden_grad']]);
        if($result_grado){
            $datos_grado = $result_grado[0];
            foreach($datos_grado as $key => $value) $_POST[$key] = $value;
            $_POST['desd_grad']=!empty($datos_grado['desd_grad']) ? substr($datos_grado['desd_grad'],0,4).'-'.substr($datos_grado['desd_grad'],4,2).'-'.substr($datos_grado['desd_grad'],6,2) : '';
            $_POST['hast_grad']=!empty($datos_grado['hast_grad']) ? substr($datos_grado['hast_grad'],0,4).'-'.substr($datos_grado['hast_grad'],4,2).'-'.substr($datos_grado['hast_grad'],6,2) : '';
            $_POST['fech_grad']=!empty($datos_grado['fech_grad']) ? substr($datos_grado['fech_grad'],0,4).'-'.substr($datos_grado['fech_grad'],4,2).'-'.substr($datos_grado['fech_grad'],6,2) : '';
        
            $result_espe_actual = $Db->select('mp_maes_grado_especialidades', ['n_codigo' => $_POST['iden_espe']]);
            if ($result_espe_actual) {
                $especialidad_actual = [
                    'id' => $result_espe_actual[0]['n_codigo'],
                    'text' => $result_espe_actual[0]['x_nombre']
                ];
            }
        }
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Registro de Grados y Títulos</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/es.js"></script>
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<script>
			function f_guardar_personal() {
				if(document.form.iden_nive.value == '' || document.form.iden_nive.selectedIndex == 0) { 
					alert('Seleccione Nivel Educativo'); 
					document.form.iden_nive.focus(); 
					return false; 
				}
				if(document.form.iden_esta.value == '' || document.form.iden_esta.selectedIndex == 0) { 
					alert('Seleccione Estado del Estudio'); 
					document.form.iden_esta.focus(); 
					return false; 
				}
				if(document.form.iden_inst.value == '' || document.form.iden_inst.selectedIndex == 0) { 
					alert('Seleccione Centro de Estudios'); 
					document.form.iden_inst.focus(); 
					return false; 
				}
				if($('#iden_espe').val() == '' || $('#iden_espe').val() == null) { 
					alert('Seleccione Especialidad o Carrera'); 
					$('#iden_espe').select2('open'); 
					return false; 
				}

				if(confirm('¿Seguro que desea Guardar?')) {
					document.form.guardar_personal.value='1';
					document.form.submit();
				}
			}
			
			function f_cancelar_documento() { 
                document.form.action='personal_educacion.php'; 
                document.form.submit(); 
			}
			
			function ajustar_altura() { 
				if(parent.document.getElementById('body_iframe')) 
					parent.document.getElementById('body_iframe').height=parent.window.innerHeight-80; 
			}
            ajustar_altura();

            $(document).ready(function() {
                $('#iden_espe').select2({
                    placeholder: 'Escriba para buscar una especialidad',
                    minimumInputLength: 3,
                    language: "es", // NUEVO: Activa la traducción a español
                    ajax: {
                        url: 'buscar_especialidades.php',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term
                            };
                        },
                        processResults: function (data, params) {
                            if(data.error) {
                                return { results: [{ id: '', text: 'Error: ' + data.error, disabled: true }] };
                            }
                            return {
                                results: data
                            };
                        },
                        cache: true
                    }
                });

                <?php if (!empty($especialidad_actual)): ?>
                    var especialidadData = <?php echo json_encode($especialidad_actual, JSON_UNESCAPED_UNICODE); ?>;
                    var option = new Option(especialidadData.text, especialidadData.id, true, true);
                    $('#iden_espe').append(option).trigger('change');
                <?php endif; ?>
            });
		</script>
        <style>
            .select2-container .select2-selection--single { height: 35px !important; }
            .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 33px !important; }
            .select2-container--default .select2-selection--single .select2-selection__arrow { height: 33px !important; }
        </style>
	</head>
	<body style="margin-bottom: 30px;">
	<center><h4 style="color:#073a6b"><b>
    <?php
        if($_POST['iden_pers']) echo"T&iacute;tulos y Grados<BR>".htmlspecialchars($_POST['appa_pers']." ".$_POST['apma_pers'].", ".$_POST['nomb_pers']);
    ?>
	</h4></b></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="iden_pers" value="<?=htmlspecialchars($_POST['iden_pers'] ?? '')?>">
			<input type=hidden name="iden_grad" value="<?=htmlspecialchars($_POST['iden_grad'] ?? '')?>">
			<input type=hidden name="flag_admi" value="<?=$_POST['flag_admi'] ?? ''?>">
			<input type=hidden name="dire_orig" value="personal_educacion.php">
			<main>
            <?php
                $html=new htmlclass;
                $arra_options_nive=$Db->get_options('mp_maes_grado_nivel',1,0);
                $arra_options_esta=$Db->get_options('mp_maes_grado_estado',1,0);
                $arra_options_inst=$Db->get_options('mp_maes_grado_instituciones',1,0);
                
                if(!empty($_POST['iden_grad'])) echo $html->put_title_demand("Editar Grado y/o T&iacute;tulo");
                else echo $html->put_title_demand("Agregar Nuevo Grado y/o T&iacute;tulo");
                
                echo $html->put_select("Nivel&nbsp;Educativo&nbsp;(*)",'iden_nive',$arra_options_nive,$_POST['iden_nive'] ?? '',"");
                echo $html->put_select("Estado&nbsp;del&nbsp;Estudio&nbsp;(*)",'iden_esta',$arra_options_esta,$_POST['iden_esta'] ?? '',"");
                echo $html->put_select_buscador("Centro&nbsp;de&nbsp;Estudios&nbsp;(*)",'iden_inst',$arra_options_inst,$_POST['iden_inst'] ?? '',"");
                echo"</main><main>";
            ?>
            <div class="row">
                <div class="col-25">
                    <label for="iden_espe">Especialidad&nbsp;/&nbsp;Carrera&nbsp;(*)</label>
                </div>
                <div class="col-75">
                    <select id="iden_espe" name="iden_espe" style="width: 100%;"></select>
                </div>
            </div>
            <?php
                echo $html->put_text('text',"Nro.&nbsp;T&iacute;tulo","Ingrese Nro. T&iacute;tulo",'ntit_grad',$_POST['ntit_grad'] ?? '','','15','');
                echo $html->put_text('text',"N&uacute;mero&nbsp;de&nbsp;Colegiatura","Ingrese Nro. Colegiatura",'ncol_grad',$_POST['ncol_grad'] ?? '','','15','');
                echo"</main><main>";
                echo $html->put_text('date',"Desde","",'desd_grad',$_POST['desd_grad'] ?? '','','20','');
                echo $html->put_text('date',"Hasta","",'hast_grad',$_POST['hast_grad'] ?? '','','20','');
                echo $html->put_text('date',"Fecha&nbsp;Obtenci&oacute;n","",'fech_grad',$_POST['fech_grad'] ?? '','','20','');

                echo"</main>";
                echo $html->put_separator_demand("30");
            ?>
			<div align=center class="foot">
                <center>
                <div align=center class="foot2">
                    <div class="div_button_foot"><button type="button" class="button_foot" onclick="f_cancelar_documento()">&laquo; Cancelar</button></div>
                    <div class="div_button_foot"><center><button type="button" class="button_foot" onclick="return f_guardar_personal()">Guardar &raquo;</button></center></div>
                </div>
                </center>
            </div>
		</form>
	</body>
</html>