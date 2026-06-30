<?php
ob_start();
require_once 'include/cabecera.php';
require_once 'classes/Html.class.php';
require_once 'classes/Db.class.php';
$Db = new Db();
require_once 'include/registrar_acceso.php';

// Inicializar flag_admi para evitar warnings y simplificar la lógica
$_POST['flag_admi'] = $_POST['flag_admi'] ?? $_GET['flag_admi'] ?? 0;

$fdig = date("YmdHis");
$registra_nuevo = isset($_POST['crea_pers']) && $_POST['crea_pers'] == 1;
$mostrar_mensaje = false; // Variable de control para el mensaje

if(isset($_POST['iden_pers_edit']) && $_POST['iden_pers_edit']) {
    unset($_POST['iden_pers']);
    $_SESSION['iden_pers_edit'] = $_POST['iden_pers_edit'];
}

if(!$registra_nuevo && (!isset($_POST['iden_pers']) || $_POST['iden_pers'] === '' || $_POST['iden_pers'] === null)) {
    if($_POST['flag_admi'] == 1) {
        if(!empty($_SESSION['iden_pers_edit'])) {
            $_POST['iden_pers'] = $_SESSION['iden_pers_edit'];
        } else {
            if(!$registra_nuevo) {
                echo "
                <html><body>
                <form name='form' method='post' action='personal_buscar.php'>
                    <input type='hidden' name='flag_admi' value='".$_POST['flag_admi']."'>
                    <input type='hidden' name='dire_orig' value='personal_general.php'>
                </form>
                <script>document.form.submit();</script>
                </body></html>";
                exit;
            }
        }
    } else {
        $result = $Db->query("select * from mp_admi_pers where ndoc_pers='$_SESSION[ndoc_oper]'");
        foreach($result as $rows)
            $_POST['iden_pers'] = $rows['iden_pers'];
    }
}

$ro = (isset($_POST['flag_admi']) && $_POST['flag_admi'] == 1) ? '' : 'disabled';

if(!empty($_POST['guardar_personal'])) {
    $fdig = date("YmdHis");
    $is_update = !empty($_POST['iden_pers']);

    $data = [
        'iden_tdoc' => $_POST['iden_tdoc'],
        'ndoc_pers' => $_POST['ndoc_pers'],
        'iden_sexo' => $_POST['iden_sexo'],
        'appa_pers' => mb_strtoupper($_POST['appa_pers'] ?? '', 'UTF-8'),
        'apma_pers' => mb_strtoupper($_POST['apma_pers'] ?? '', 'UTF-8'),
        'nomb_pers' => mb_strtoupper($_POST['nomb_pers'] ?? '', 'UTF-8'),
        'nruc_pers' => $_POST['nruc_pers'] ?? '',
        'iden_nafp' => $_POST['iden_tafp'] ?? 0,
        'cusp_pers' => $_POST['cusp_pers'] ?? '',
        'iden_eciv' => $_POST['iden_eciv'] ?? 0,
        'fnac_pers' => str_replace('-', '', $_POST['fnac_pers']),
        'iden_pais' => $_POST['iden_pais'],
        'lnac_pers' => $_POST['iden_dist'],
        'acti_pers' => $_POST['acti_pers'] ?? 1
    ];

    if($is_update) {
        $updateResult = $Db->update('mp_admi_pers', $data, ['iden_pers' => $_POST['iden_pers']]);
        if($updateResult === false) {
            $error = $Db->getLastError();
            echo "Error al actualizar mp_admi_pers: $error";
            exit;
        }
        $iden_pers = $_POST['iden_pers'];
    } else {
        $data['esta_pers'] = 1;
        $iden_pers = $Db->insert('mp_admi_pers', $data);
        if($iden_pers === false) {
            $error = $Db->getLastError();
            echo "Error al insertar en mp_admi_pers: $error";
            exit;
        }

        $dni = $_POST['ndoc_pers'];
        $oper_creador = $_SESSION['iden_oper'];
        $operData = [
            'logi_oper' => $dni,
            'pass_oper' => md5($dni),
            'ndoc_oper' => $dni,
            'appa_oper' => mb_strtoupper($_POST['appa_pers'] ?? '', 'UTF-8'),
            'apma_oper' => mb_strtoupper($_POST['apma_pers'] ?? '', 'UTF-8'),
            'nomb_oper' => mb_strtoupper($_POST['nomb_pers'] ?? '', 'UTF-8'),
            'carg_oper' => '',
            'depe_oper' => '',
            'celu_oper' => '',
            'mail_oper' => '',
            'codi_depe' => 0,
            'codi_perf' => 0,
            'flag_band' => 0,
            'esta_oper' => 1,
            'fexp_oper' => '20300101',
            'digi_oper' => $oper_creador,
            'fdig_oper' => $fdig,
            'rese_oper' => 0
        ];
        $insertOpResult = $Db->insert('mp_admi_oper', $operData);
        if($insertOpResult === false) {
            $error = $Db->getLastError();
            echo "Error al insertar en mp_admi_oper: $error";
            exit;
        }
        $iden_oper = $Db->lastInsertId();

        $operRoleData = [
            'iden_oper' => $iden_oper,
            'iden_role' => 1
        ];
        $insertRoleResult = $Db->insert('mp_admi_oper_role', $operRoleData);
        if($insertRoleResult === false) {
            $error = $Db->getLastError();
            echo "Error al insertar en mp_admi_oper_role: $error";
            exit;
        }
    }

    // CONSERVAR IDEN_PERS EN SESIÓN PARA OTROS FORMULARIOS
    $_SESSION['iden_pers_edit'] = $iden_pers;
    
    $_POST['iden_pers'] = $iden_pers;
    $mostrar_mensaje = true; // Activar mensaje tras cargar página
}

// Cargar datos si iden_pers existe
if(!empty($_POST['iden_pers'])) {
    $result_personal = $Db->select('mp_admi_pers', ['iden_pers' => $_POST['iden_pers']]);
    if($result_personal) {
        $datos_personales = $result_personal[0];
        foreach($datos_personales as $key => $value) { $_POST[$key] = $value; }
        $_POST['iden_tafp'] = $datos_personales['iden_nafp'];
        $_POST['fnac_pers'] = !empty($datos_personales['fnac_pers']) ?
            substr($datos_personales['fnac_pers'],0,4) . '-' .
            substr($datos_personales['fnac_pers'],4,2) . '-' .
            substr($datos_personales['fnac_pers'],6,2) : '';
    }
}

$lnac_pers_guardado = $_POST['lnac_pers'] ?? '';
$_POST['iden_dpto'] = !empty($lnac_pers_guardado) ? substr($lnac_pers_guardado, 0, 2) : '04';
$_POST['iden_prov'] = !empty($lnac_pers_guardado) ? substr($lnac_pers_guardado, 0, 4) : '0401';
$_POST['iden_dist'] = $lnac_pers_guardado;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Datos Generales</title>
    <link rel="stylesheet" href="css/forms_demanda.css" />
    <link rel="stylesheet" href="css/forms_foot.css" />
    <link rel="stylesheet" href="css/forms_column.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
    
    <script type="text/javascript">
        $(document).ready(function(){
            let ubigeoData = [];
            const dptoSelect = $('#iden_dpto');
            const provSelect = $('#iden_prov');
            const distSelect = $('#iden_dist');
            const dptoGuardado = '<?=$_POST['iden_dpto']?>';
            const provGuardada = '<?=$_POST['iden_prov']?>';
            const distGuardado = '<?=$_POST['iden_dist']?>';
            
            $.getJSON('personal_ubigeo.php?Accion=GetTodoElUbigeo', function(data) {
                ubigeoData = data;
                cargarDepartamentos();
            });
            
            function cargarDepartamentos() {
                let departamentos = [...new Map(ubigeoData.map(item => [item['cdep'], item])).values()];
                dptoSelect.empty().append(new Option("<- Departamento ->", ""));
                departamentos.sort((a, b) => a.depa.localeCompare(b.depa)).forEach(dpto => {
                    dptoSelect.append(new Option(dpto.depa, dpto.cdep));
                });
                if (dptoGuardado) {
                    dptoSelect.val(dptoGuardado);
                    dptoSelect.trigger('change');
                }
            }
            
            dptoSelect.on('change', function() {
                const selectedDpto = $(this).val();
                let provincias = ubigeoData.filter(item => item.cdep === selectedDpto);
                let provinciasUnicas = [...new Map(provincias.map(item => [item['cpro'], item])).values()];
                provSelect.empty().append(new Option("<- Provincia ->", ""));
                distSelect.empty().append(new Option("<- Distrito ->", ""));
                provinciasUnicas.sort((a, b) => a.prov.localeCompare(b.prov)).forEach(prov => {
                    provSelect.append(new Option(prov.prov, prov.cdep + prov.cpro));
                });
                if (provGuardada) {
                    provSelect.val(provGuardada);
                    provSelect.trigger('change');
                }
            });
            
            provSelect.on('change', function() {
                const selectedProv = $(this).val();
                let distritos = ubigeoData.filter(item => (item.cdep + item.cpro) === selectedProv);
                distSelect.empty().append(new Option("<- Distrito ->", ""));
                distritos.sort((a, b) => a.dist.localeCompare(b.dist)).forEach(dist => {
                    distSelect.append(new Option(dist.dist, dist.cdep + dist.cpro + dist.cdis));
                });
                if (distGuardado) {
                    distSelect.val(distGuardado);
                }
            });

            // MOSTRAR MENSAJE DESPUÉS DE CARGAR COMPLETAMENTE LA PÁGINA
            <?php if($mostrar_mensaje): ?>
            setTimeout(function() {
                alert('¡Datos guardados correctamente!');
            }, 100);
            <?php endif; ?>
        });
    </script>
    
    <script>
        function f_guardar_personal() {
            if(document.form.iden_tdoc.value=='' || document.form.iden_tdoc.selectedIndex==0) {
                alert('Seleccione Tipo de Documento');
                document.form.iden_tdoc.focus();
                return false;
            }
            if(document.form.ndoc_pers.value.trim() === '') {
                alert('Ingrese Nro. de Documento');
                document.form.ndoc_pers.focus();
                return false;
            }
            if(document.form.iden_sexo.value=='' || document.form.iden_sexo.selectedIndex==0) {
                alert('Seleccione Sexo');
                document.form.iden_sexo.focus();
                return false;
            }
            if(document.form.appa_pers.value.trim() === '') {
                alert('Ingrese Apellido Paterno');
                document.form.appa_pers.focus();
                return false;
            }
            if(document.form.apma_pers.value.trim() === '') {
                alert('Ingrese Apellido Materno');
                document.form.apma_pers.focus();
                return false;
            }
            if(document.form.nomb_pers.value.trim() === '') {
                alert('Ingrese Nombres');
                document.form.nomb_pers.focus();
                return false;
            }
            if(document.form.iden_eciv.value=='' || document.form.iden_eciv.selectedIndex==0) {
                alert('Seleccione Estado Civil');
                document.form.iden_eciv.focus();
                return false;
            }
            if(document.form.fnac_pers.value.trim() === '') {
                alert('Ingrese Fecha de Nacimiento');
                document.form.fnac_pers.focus();
                return false;
            }
            if(document.form.iden_pais.value=='' || document.form.iden_pais.selectedIndex==0) {
                alert('Seleccione País');
                document.form.iden_pais.focus();
                return false;
            }
            if(document.form.iden_dpto.value=='' || document.form.iden_dpto.selectedIndex==0) {
                alert('Seleccione Departamento de Nacimiento');
                document.form.iden_dpto.focus();
                return false;
            }
            if(document.form.iden_prov.value=='' || document.form.iden_prov.selectedIndex==0) {
                alert('Seleccione Provincia de Nacimiento');
                document.form.iden_prov.focus();
                return false;
            }
            if(document.form.iden_dist.value=='' || document.form.iden_dist.selectedIndex==0) {
                alert('Seleccione Distrito de Nacimiento');
                document.form.iden_dist.focus();
                return false;
            }
            if(confirm('¿Seguro que desea Guardar?')) {
                document.form.guardar_personal.value='1';
                document.form.submit();
            }
        }
        function f_cancelar_documento() {
            document.form.action='personal_buscar.php';
            document.form.submit();
        }
        function ajustar_altura() {
            if(parent.document.getElementById('body_iframe'))
                parent.document.getElementById('body_iframe').height = parent.window.innerHeight-80;
        }
        ajustar_altura();
    </script>
</head>
<body style="margin-bottom: 30px;">
<center><h4 style="color:#073a6b"><b>
<?php
if(!empty($_POST['iden_pers']))
    echo "Datos Generales<BR>" . htmlspecialchars($_POST['appa_pers'] . " " . $_POST['apma_pers'] . ", " . $_POST['nomb_pers']);
else
    echo "Crear Nuevo Personal";
?>
</h4></b></center>
<form name="form" method="post" ENCTYPE="multipart/form-data">
    <input type="hidden" name="guardar_personal">
    <input type="hidden" name="flag_admi" value="<?=$_POST['flag_admi']?>">
    <input type="hidden" name="dire_orig" value="personal_general.php">
    <?php if(!empty($_POST['iden_pers'])): ?>
        <input type="hidden" name="iden_pers" value="<?=htmlspecialchars($_POST['iden_pers'])?>">
    <?php endif; ?>
    <?php if($registra_nuevo): ?>
        <input type="hidden" name="crea_pers" value="1">
    <?php endif; ?>
    <main>
    <?php
    $html=new htmlclass;
    if(empty($_POST['iden_tdoc'])) $_POST['iden_tdoc'] = 1;
    if(empty($_POST['iden_pais'])) $_POST['iden_pais'] = 348;
    $arra_options_tdoc = $Db->get_options('mp_maes_tdocumento',1,0);
    $arra_options_sexo = $Db->get_options('mp_maes_sexo',1,0);
    $arra_options_pais = $Db->get_options('mp_maes_pais',1,0);
    $arra_options_tafp = $Db->get_options('mp_maes_afp',1,0);
    $arra_options_eciv = $Db->get_options('mp_maes_estado_civil',1,0);

    echo $html->put_title_demand("Datos Generales");
    echo $html->put_select("Tipo&nbsp;Documento(*)",'iden_tdoc',$arra_options_tdoc,$_POST['iden_tdoc'] ?? '', "$ro");
    echo $html->put_text('text',"Nro.&nbsp;Documento(*)","Ingrese Nro. Documento",'ndoc_pers',$_POST['ndoc_pers'] ?? '','','15', "$ro");
    echo $html->put_select("Sexo(*)",'iden_sexo',$arra_options_sexo,$_POST['iden_sexo'] ?? '', "$ro");
    echo "</main><main>";
    echo $html->put_text('text',"Apellido&nbsp;Paterno(*)","Ingrese Apellido Paterno",'appa_pers',$_POST['appa_pers'] ?? '','','50', "$ro");
    echo $html->put_text('text',"Apellido&nbsp;Materno(*)","Ingrese Apellido Materno",'apma_pers',$_POST['apma_pers'] ?? '','','50', "$ro");
    echo $html->put_text('text',"Nombres(*)","Ingrese Nombres",'nomb_pers',$_POST['nomb_pers'] ?? '','','50', "$ro");
    echo "</main><main>";
    echo $html->put_text('text',"Nro.&nbsp;RUC","Ingrese Nro. RUC",'nruc_pers',$_POST['nruc_pers'] ?? '','','20', "$ro");
    echo $html->put_select("AFP",'iden_tafp',$arra_options_tafp,$_POST['iden_tafp'] ?? '', "$ro");
    echo $html->put_text('text',"C&oacute;digo&nbsp;de&nbsp;CUSPP","Ingrese CUSPP",'cusp_pers',$_POST['cusp_pers'] ?? '','','20', "$ro");
    echo "</main><main>";
    echo $html->put_select("Estado&nbsp;Civil(*)",'iden_eciv',$arra_options_eciv,$_POST['iden_eciv'] ?? '', "$ro");
    echo "</main><main>";
    echo $html->put_title_demand("Lugar y Fecha de Nacimiento");
    echo $html->put_text('date',"Fecha(*)","Ingrese fecha",'fnac_pers',$_POST['fnac_pers'] ?? '','','50', "$ro");
    echo $html->put_select_buscador("Pa&iacute;s(*)",'iden_pais',$arra_options_pais,$_POST['iden_pais'], "$ro");
    echo "</main><main>";
    echo $html->put_select("Departamento(*)",'iden_dpto',[],'', "$ro");
    echo $html->put_select("Provincia(*)",'iden_prov',[],'', "$ro");
    echo $html->put_select("Distrito(*)",'iden_dist',[],'', "$ro");
    echo "</main><main>";
    echo $html->put_title_demand("Estado del Trabajador");
    echo $html->put_select_estado(defined('CONST_SUBTITLE_STATE') ? CONST_SUBTITLE_STATE : 'Estado','acti_pers',$_POST['acti_pers'] ?? '','Activo','Inactivo', "$ro");
    echo "</main><main>";
    echo $html->put_separator_demand("30");
    if($_POST['flag_admi']==1) {
        echo "
            <div align=center class=\"foot\">
                <div align=center class=\"foot2\">
                    <div class=\"div_button_foot\">
                        <button class=\"button_foot\" onclick=\"f_cancelar_documento()\">&laquo; Nueva B&uacute;squeda</button>
                    </div>
                    <div class=\"div_button_foot\">
                        <button class=\"button_foot\" onclick=\"return f_guardar_personal()\">Guardar &raquo;</button>
                    </div>
                </div>
            </div>
        ";
    }
    ?>
</form>
</body>
</html>
<?php
if (ob_get_length()) {
    ob_end_flush();
}
?>
