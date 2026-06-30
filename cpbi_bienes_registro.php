<?php
require_once 'include/cabecera.php';
require_once 'classes/Html.class.php';
require_once 'classes/Db.class.php';

$db = new Db();

// --- Cargar datos del usuario en sesión y determinar si se autocompleta ---
$usuario_actual_para_autocompletar = null;
$autocompletar_responsable = false;
$usuarios_permitidos = [473, 995, 896, 1058, 770];

if (isset($_SESSION['iden_oper']) && in_array($_SESSION['iden_oper'], $usuarios_permitidos)) {
    $autocompletar_responsable = true;
    $oper_data = $db->query("SELECT CONCAT(nomb_oper, ' ', appa_oper, ' ', apma_oper) as nombre_completo, logi_oper FROM mp_admi_oper WHERE iden_oper = :id", [':id' => $_SESSION['iden_oper']]);
    if (!empty($oper_data)) {
        $usuario_actual_para_autocompletar = $oper_data[0];
    }
}

// Determinar el contexto: editar un bien existente o crear uno nuevo
$codi_bien = isset($_GET['codi_bien']) ? intval($_GET['codi_bien']) : 0;
$is_editing = $codi_bien > 0;

$success_message = '';
$error_message = '';

// --- Lógica de Guardado ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db->beginTransaction();
    try {
        // ACCIÓN 1: Guardar el nuevo bien (modo creación)
        if (isset($_POST['guardar_bien']) && !$is_editing) {
            // Verificar si el N° de Registro de Almacén ya existe
            $nume_regi_a_verificar = $_POST['nume_regi'] ?? '';
            $registro_existente = $db->query("SELECT codi_bien FROM mp_cpbi_bienes WHERE nume_regi = :nume_regi", [':nume_regi' => $nume_regi_a_verificar]);
            if (!empty($registro_existente)) {
                throw new Exception("El N° de Registro de Almacén '" . htmlspecialchars($nume_regi_a_verificar) . "' ya existe. Por favor, ingrese un número único.");
            }

            $data_bien = [
                'nume_regi' => $_POST['nume_regi'] ?? null,
                'desc_bien' => $_POST['desc_bien'] ?? null,
                'marc_bien' => $_POST['marc_bien'] ?? null,
                'seri_bien' => $_POST['seri_bien'] ?? null,
                'color_bien' => $_POST['color_bien'] ?? null,
                'tamano_bien' => $_POST['tamano_bien'] ?? null,
                'tplg_bien' => $_POST['tplg_bien'] ?? null,
                'id_tipo_bien' => !empty($_POST['id_tipo_bien']) ? $_POST['id_tipo_bien'] : null,
                'nume_carp' => $_POST['nume_carp'] ?? null,
                'nume_ofic' => $_POST['nume_ofic'] ?? null,
                'codi_deli' => !empty($_POST['codi_deli']) ? $_POST['codi_deli'] : null,
                'codi_fisc' => !empty($_POST['codi_fisc']) ? $_POST['codi_fisc'] : null,
                'codi_depe' => !empty($_POST['codi_depe']) ? $_POST['codi_depe'] : null,
                'codi_esta' => !empty($_POST['codi_esta']) ? $_POST['codi_esta'] : null,
                'codi_epro' => !empty($_POST['codi_epro']) ? $_POST['codi_epro'] : null,
                'fech_inte' => !empty($_POST['fech_inte']) ? str_replace('T', ' ', $_POST['fech_inte']) . ':00' : date('Y-m-d H:i:s'),
                'agraviante' => trim(
                    ($_POST['autor_appa'] ?? '') . ' ' . 
                    ($_POST['autor_apma'] ?? '') . ' ' . 
                    ($_POST['autor_nomb'] ?? '')),
                'agraviado' => trim(
                    ($_POST['agraviado_appa'] ?? '') . ' ' . 
                    ($_POST['agraviado_apma'] ?? '') . ' ' . 
                    ($_POST['agraviado_nomb'] ?? '')),
                'lugar_origen_incautacion' => $_POST['lugar_origen_incautacion'] ?? null,
                'descripcion_embalaje' => $_POST['descripcion_embalaje'] ?? null,
                'perecible' => (int)!empty($_POST['perecible']),
                'naturaleza_bien' => $_POST['naturaleza_bien'] ?? null,
                'drogas_tipo' => $_POST['drogas_tipo'] ?? null,
                'id_ubig_provincia' => !empty($_POST['id_ubig_provincia']) ? $_POST['id_ubig_provincia'] : null,
                'id_ubig_distrito' => !empty($_POST['id_ubig_distrito']) ? $_POST['id_ubig_distrito'] : null,
                'digi_oper_id' => $_SESSION['iden_oper']
            ];
            $new_codi_bien = $db->insert('mp_cpbi_bienes', $data_bien);
            if (!$new_codi_bien) {
                throw new Exception("Falló la creación del nuevo bien.");
            }
            $db->commit();
            header("Location: cpbi_bienes_registro.php?codi_bien=" . $new_codi_bien . "&status=created");
            exit();
        }

        // ACCIÓN 2: Guardar detalles iniciales (Recepción y Ubicación) (modo edición)
        if (isset($_POST['guardar_detalles']) && $is_editing) {
            $data_auditoria = [
                'codi_bien' => $codi_bien,
                'fecha_movimiento' => date('Y-m-d H:i:s'),
                'tipo_movimiento' => 'DETALLES_INICIALES',
                'responsable_entrega_nombre' => $_POST['responsable_entrega_nombre'],
                'responsable_entrega_dni' => $_POST['responsable_entrega_dni'],
                'responsable_recepcion_nombre' => $_POST['responsable_recepcion_nombre'],
                'responsable_recepcion_dni' => $_POST['responsable_recepcion_dni'],
                'observacion' => $_POST['observacion'],
                'ubicacion' => $_POST['ubicacion'] ?? null,
                'anaquel' => $_POST['anaquel'],
                'nivel' => $_POST['nivel'],
                'caja' => $_POST['caja'],
                'id_operador' => $_SESSION['iden_oper']
            ];
            $exists = $db->query("SELECT 1 FROM mp_cpbi_auditoria WHERE codi_bien = :cb AND tipo_movimiento = 'DETALLES_INICIALES'", [':cb' => $codi_bien]);
            if ($exists) {
                 $db->update('mp_cpbi_auditoria', $data_auditoria, ['codi_bien' => $codi_bien, 'tipo_movimiento' => 'DETALLES_INICIALES']);
            } else {
                 $db->insert('mp_cpbi_auditoria', $data_auditoria);
            }
            $success_message = "Datos de recepción y ubicación guardados correctamente.";
        }

        // ACCIÓN 3: Guardar archivo digital (modo edición)
        if (isset($_POST['guardar_archivo']) && $is_editing) {
            if (isset($_FILES['ruta_archivo_digital']) && $_FILES['ruta_archivo_digital']['error'] == 0) {
                $target_dir = "uploads/bienes_digitales/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $file_name = $codi_bien . '_' . time() . '_' . basename($_FILES["ruta_archivo_digital"]["name"]);
                $target_file = $target_dir . $file_name;
                if (move_uploaded_file($_FILES["ruta_archivo_digital"]["tmp_name"], $target_file)) {
                    $db->update('mp_cpbi_bienes', ['ruta_archivo_digital' => $target_file], ["codi_bien" => $codi_bien]);
                    $success_message = "Archivo digital guardado correctamente.";
                } else {
                    throw new Exception("Hubo un error al subir el archivo digital.");
                }
            } else {
                 throw new Exception("No se seleccionó ningún archivo o hubo un error en la subida.");
            }
        }

        $db->commit();

    } catch (Exception $e) {
        $db->rollBack();
        $error_message = "Error al guardar los datos: " . $e->getMessage();
    }
}

// --- Mensaje de éxito post-redirección ---
if (isset($_GET['status']) && $_GET['status'] == 'created') {
    $success_message = "Bien registrado con el código: $codi_bien. Ahora puede registrar los detalles de la recepción, ubicación y archivos.";
}

// --- Carga de datos para el formulario ---
$bien_data = [];
$detalles_data = [];

if ($is_editing) {
    $bien_data_result = $db->query(
        "SELECT b.*, d.nomb_depe 
         FROM mp_cpbi_bienes b
         LEFT JOIN mp_admi_depe d ON b.codi_depe = d.codi_depe
         WHERE b.codi_bien = :codi_bien",
        [':codi_bien' => $codi_bien]
    );
    $bien_data = !empty($bien_data_result) ? $bien_data_result[0] : [];
    
    $detalles_data_result = $db->query("SELECT * FROM mp_cpbi_auditoria WHERE codi_bien = :codi_bien AND tipo_movimiento = 'DETALLES_INICIALES' ORDER BY id_auditoria DESC LIMIT 1", [':codi_bien' => $codi_bien]);
    $detalles_data = !empty($detalles_data_result) ? $detalles_data_result[0] : [];
}

// Cargar datos para los selects
$tipos_bien = $db->query("SELECT n_codigo, CONCAT(n_codigo, ' - ', x_nombre) as nombre FROM mp_maes_cpbi_tipos ORDER BY x_nombre");
$delitos = $db->query("SELECT n_codigo, CONCAT(n_codigo, ' - ', x_nombre) as nombre FROM mp_maes_delito ORDER BY x_nombre");
$fiscales = $db->query("SELECT iden_pers as codi_fisc, CONCAT( appa_pers, ' ', apma_pers,' ',nomb_pers) as nombre FROM mp_admi_pers WHERE iden_rlab = '04' ORDER BY nombre");
$dependencias = $db->query("SELECT codi_depe, nomb_depe FROM mp_admi_depe WHERE esta_depe = 1 ORDER BY nomb_depe");
$estados = $db->query("SELECT n_codigo, CONCAT(n_codigo, ' - ', x_nombre) as nombre FROM mp_maes_cpbi_estado ORDER BY x_nombre");
$estados_proceso = $db->query("SELECT n_codigo, CONCAT(n_codigo, ' - ', x_nombre) as nombre FROM mp_maes_cpbi_estado_proceso ORDER BY x_nombre");
$provincias = $db->query("SELECT cpro, prov FROM mp_admi_ubig_reni WHERE cdep = '04' AND cpro != '00' AND cdis = '00' ORDER BY prov");

$distritos = [];
$selected_prov = $bien_data['id_ubig_provincia'] ?? '';
if (!empty($selected_prov)) {
    $distritos = $db->query("SELECT cpro, cdis, dist FROM mp_admi_ubig_reni WHERE cdep = '04' AND cpro = :cpro AND cdis != '00' ORDER BY dist", [':cpro' => substr($selected_prov, 2, 2)]);
}
?>
<?php
$page_title = $is_editing ? 'Editar Bien Incautado #' . $codi_bien : 'Registro de Nuevo Bien Incautado';
require_once 'include/page_header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title" style="color:white"><?php echo $is_editing ? 'Editar Bien Incautado #' . $codi_bien : 'Registro de Nuevo Bien Incautado'; ?></h5>
                </div>
                <div class="card-body">

                    <?php if ($success_message): ?>
                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                    <?php endif; ?>
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>

                    <!-- MODO CREACIÓN: Formulario para registrar el bien -->
                    <?php if (!$is_editing): ?>
                    <form action="cpbi_bienes_registro.php" method="post" enctype="multipart/form-data">
                        <!-- Datos del Bien -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header">
                                <i class="bi bi-box-seam"></i> Datos del Bien
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label" for="nume_regi">N° Registro de Almacén</label>
                                        <input type="text" class="form-control" id="nume_regi" name="nume_regi" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="fech_inte">Fecha de Internamiento</label>
                                        <input type="datetime-local" class="form-control" id="fech_inte" name="fech_inte" value="<?php echo date('Y-m-d\TH:i'); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="codi_esta">Estado del Bien</label>
                                        <select class="form-select" id="codi_esta" name="codi_esta" required>
                                            <option value="">-- Seleccione --</option>
                                            <?php foreach ($estados as $estado): ?>
                                                <option value="<?php echo $estado['n_codigo']; ?>"><?php echo htmlspecialchars($estado['nombre']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label" for="desc_bien">Descripción del Bien</label>
                                        <textarea class="form-control" id="desc_bien" name="desc_bien" rows="3" required></textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="marc_bien">Marca</label>
                                        <input type="text" class="form-control" id="marc_bien" name="marc_bien">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="seri_bien">N° de Serie</label>
                                        <input type="text" class="form-control" id="seri_bien" name="seri_bien" value="<?php echo htmlspecialchars($bien_data['seri_bien'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="color_bien">Color</label>
                                        <select class="form-select" id="color_bien" name="color_bien">
                                            <option value="">-- Seleccione --</option>
                                            <option value="Negro" <?php if (($bien_data['color_bien'] ?? '') == 'Negro') echo 'selected'; ?>>Negro</option>
                                            <option value="Blanco" <?php if (($bien_data['color_bien'] ?? '') == 'Blanco') echo 'selected'; ?>>Blanco</option>
                                            <option value="Gris" <?php if (($bien_data['color_bien'] ?? '') == 'Gris') echo 'selected'; ?>>Gris</option>
                                            <option value="Rojo" <?php if (($bien_data['color_bien'] ?? '') == 'Rojo') echo 'selected'; ?>>Rojo</option>
                                            <option value="Azul" <?php if (($bien_data['color_bien'] ?? '') == 'Azul') echo 'selected'; ?>>Azul</option>
                                            <option value="Verde" <?php if (($bien_data['color_bien'] ?? '') == 'Verde') echo 'selected'; ?>>Verde</option>
                                            <option value="Amarillo" <?php if (($bien_data['color_bien'] ?? '') == 'Amarillo') echo 'selected'; ?>>Amarillo</option>
                                            <option value="Marrón" <?php if (($bien_data['color_bien'] ?? '') == 'Marrón') echo 'selected'; ?>>Marrón</option>
                                            <option value="Otro" <?php if (($bien_data['color_bien'] ?? '') == 'Otro') echo 'selected'; ?>>Otro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="tamano_bien">Tamaño</label>
                                        <select class="form-select" id="tamano_bien" name="tamano_bien">
                                            <option value="">-- Seleccione --</option>
                                            <option value="Pequeño" <?php if (($bien_data['tamano_bien'] ?? '') == 'Pequeño') echo 'selected'; ?>>Pequeño</option>
                                            <option value="Mediano" <?php if (($bien_data['tamano_bien'] ?? '') == 'Mediano') echo 'selected'; ?>>Mediano</option>
                                            <option value="Grande" <?php if (($bien_data['tamano_bien'] ?? '') == 'Grande') echo 'selected'; ?>>Grande</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="id_tipo_bien">Tipo de bien</label>
                                        <select class="form-select" id="id_tipo_bien" name="id_tipo_bien" required>
                                            <option value="">-- Seleccione --</option>
                                            <?php foreach ($tipos_bien as $tipo): ?>
                                                <option value="<?php echo $tipo['n_codigo']; ?>"><?php echo htmlspecialchars($tipo['nombre']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="tplg_bien">Tipología</label>
                                        <select class="form-select" id="tplg_bien" name="tplg_bien" required>
                                            <option value="">-- Seleccione --</option>
                                            <option value="BIEN INCAUTADO">BIEN INCAUTADO</option>
                                            <option value="EVIDENCIA">EVIDENCIA</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label" for="descripcion_embalaje">Descripción del Embalaje</label>
                                        <input type="text" class="form-control" id="descripcion_embalaje" name="descripcion_embalaje">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="naturaleza_bien">Naturaleza</label>
                                        <input type="text" class="form-control" id="naturaleza_bien" name="naturaleza_bien" value="FÍSICA">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="drogas_tipo">Droga (Especificar tipo)</label>
                                        <input type="text" class="form-control" id="drogas_tipo" name="drogas_tipo">
                                    </div>
                                    <div class="col-md-3 align-self-center">
                                        <div class="form-check mt-4 pt-2">
                                            <input class="form-check-input" type="checkbox" id="perecible" name="perecible" value="1">
                                            <label class="form-check-label" for="perecible">Perecible</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Datos del Caso -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header">
                                <i class="bi bi-folder2-open"></i> Datos del Caso
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label" for="nume_carp">Código de Carpeta Fiscal</label>
                                        <input type="text" class="form-control" id="nume_carp" name="nume_carp" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="codi_fisc">Fiscal</label>
                                        <select class="form-select" id="codi_fisc" name="codi_fisc" required>
                                            <option value="">-- Seleccione --</option>
                                            <?php foreach ($fiscales as $fiscal): ?>
                                                <option value="<?php echo $fiscal['codi_fisc']; ?>"><?php echo htmlspecialchars($fiscal['nombre']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="codi_depe">Dependencia</label>
                                        <select class="form-select" id="codi_depe" name="codi_depe" required>
                                            <option value="">-- Seleccione --</option>
                                            <?php foreach ($dependencias as $dependencia): ?>
                                                <option value="<?php echo $dependencia['codi_depe']; ?>"><?php echo htmlspecialchars($dependencia['nomb_depe']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="codi_deli">Delito Investigado</label>
                                        <select class="form-select" id="codi_deli" name="codi_deli" required>
                                            <option value="">-- Seleccione --</option>
                                            <?php foreach ($delitos as $delito): ?>
                                                <option value="<?php echo $delito['n_codigo']; ?>" <?php if (($bien_data['codi_deli'] ?? '') == $delito['n_codigo']) echo 'selected'; ?>><?php echo htmlspecialchars($delito['nombre']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="nume_ofic">Nro de Oficio</label>
                                        <input type="text" class="form-control" id="nume_ofic" name="nume_ofic" value="<?php echo htmlspecialchars($bien_data['nume_ofic'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="codi_epro">Estado del Proceso</label>
                                        <select class="form-select" id="codi_epro" name="codi_epro" required>
                                            <option value="">-- Seleccione --</option>
                                            <?php foreach ($estados_proceso as $eproc): ?>
                                                <option value="<?php echo $eproc['n_codigo']; ?>"><?php echo htmlspecialchars($eproc['nombre']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold">Presunto Autor</label>
                                        <div class="row">
                                            <div class="col-md-4"><input type="text" class="form-control" name="autor_appa" placeholder="Apellido Paterno"></div>
                                            <div class="col-md-4"><input type="text" class="form-control" name="autor_apma" placeholder="Apellido Materno"></div>
                                            <div class="col-md-4"><input type="text" class="form-control" name="autor_nomb" placeholder="Nombres"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold">Agraviado</label>
                                        <div class="row">
                                            <div class="col-md-4"><input type="text" class="form-control" name="agraviado_appa" placeholder="Apellido Paterno"></div>
                                            <div class="col-md-4"><input type="text" class="form-control" name="agraviado_apma" placeholder="Apellido Materno"></div>
                                            <div class="col-md-4"><input type="text" class="form-control" name="agraviado_nomb" placeholder="Nombres"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Lugar de Recolección -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header">
                                <i class="bi bi-geo-alt"></i> Lugar de Recolección
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label" for="id_ubig_provincia">Provincia</label>
                                        <select class="form-select" id="id_ubig_provincia" name="id_ubig_provincia" required>
                                            <option value="">-- Seleccione Provincia --</option>
                                            <?php foreach ($provincias as $prov): ?>
                                                <option value="04<?php echo $prov['cpro']; ?>"><?php echo htmlspecialchars($prov['prov']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="id_ubig_distrito">Distrito</label>
                                        <select class="form-select" id="id_ubig_distrito" name="id_ubig_distrito" required>
                                            <option value="">-- Seleccione Provincia Primero --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="lugar_origen_incautacion">Lugar de Origen de la Incautación</label>
                                        <input type="text" class="form-control" id="lugar_origen_incautacion" name="lugar_origen_incautacion">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" name="guardar_bien" class="btn btn-primary btn-lg"><i class="bi bi-save"></i> Guardar Bien</button>
                            <a href="cpbi_bienes_incautados.php" class="btn btn-secondary btn-lg"><i class="bi bi-x-circle"></i> Cancelar</a>
                        </div>
                    </form>
                    <?php endif; ?>

                    <!-- MODO EDICIÓN: Mostrar datos y formularios para detalles -->
                    <?php if ($is_editing): ?>
                        <div class="card shadow-sm mb-4">
                            <div class="card-header">
                                <i class="bi bi-info-circle"></i> Datos del Caso y del Bien (Solo Lectura)
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3"><p><strong>N° Registro de Almacén:</strong> <?php echo htmlspecialchars($bien_data['nume_regi'] ?? ''); ?></p></div>
                                    <div class="col-md-3"><p><strong>Código de Carpeta Fiscal:</strong> <?php echo htmlspecialchars($bien_data['nume_carp'] ?? ''); ?></p></div>
                                    <div class="col-md-3"><p><strong>Nro de Oficio:</strong> <?php echo htmlspecialchars($bien_data['nume_ofic'] ?? ''); ?></p></div>
                                    <div class="col-md-3"><p><strong>Dependencia:</strong> <?php echo htmlspecialchars($bien_data['nomb_depe'] ?? 'No especificada'); ?></p></div>
                                    <div class="col-md-9"><p><strong>Descripción:</strong> <?php echo htmlspecialchars($bien_data['desc_bien'] ?? ''); ?></p></div>
                                    <div class="col-md-1"><p><strong>Color:</strong> <?php echo htmlspecialchars($bien_data['color_bien'] ?? ''); ?></p></div>
                                    <div class="col-md-2"><p><strong>Tamaño:</strong> <?php echo htmlspecialchars($bien_data['tamano_bien'] ?? ''); ?></p></div>
                                </div>
                            </div>
                        </div>

                        <!-- Formulario de Detalles Iniciales (Recepción y Ubicación) -->
                        <form action="cpbi_bienes_registro.php?codi_bien=<?php echo $codi_bien; ?>" method="post">
                            <div class="card shadow-sm mb-4">
                                <div class="card-header">
                                    <i class="bi bi-people"></i> Recepción, Custodia y Ubicación Inicial
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="responsable_entrega_nombre">Nombre del Responsable de la Entrega</label>
                                            <input type="text" class="form-control" id="responsable_entrega_nombre" name="responsable_entrega_nombre" value="<?php echo htmlspecialchars($detalles_data['responsable_entrega_nombre'] ?? ''); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="responsable_entrega_dni">DNI del Responsable de la Entrega</label>
                                            <input type="text" class="form-control" id="responsable_entrega_dni" name="responsable_entrega_dni" value="<?php echo htmlspecialchars($detalles_data['responsable_entrega_dni'] ?? ''); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="responsable_recepcion_nombre">Nombre del Responsable de Recepción</label>
                                            <input type="text" class="form-control" id="responsable_recepcion_nombre" name="responsable_recepcion_nombre" 
                                                   value="<?php echo htmlspecialchars($autocompletar_responsable ? ($usuario_actual_para_autocompletar['nombre_completo'] ?? '') : ($detalles_data['responsable_recepcion_nombre'] ?? '')); ?>" 
                                                   <?php echo $autocompletar_responsable ? 'readonly' : ''; ?> required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="responsable_recepcion_dni">DNI del Responsable de Recepción</label>
                                            <input type="text" class="form-control" id="responsable_recepcion_dni" name="responsable_recepcion_dni" 
                                                   value="<?php echo htmlspecialchars($autocompletar_responsable ? ($usuario_actual_para_autocompletar['logi_oper'] ?? '') : ($detalles_data['responsable_recepcion_dni'] ?? '')); ?>" 
                                                   <?php echo $autocompletar_responsable ? 'readonly' : ''; ?> required>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label" for="observacion">Observación</label>
                                            <textarea class="form-control" id="observacion" name="observacion" rows="2"><?php echo htmlspecialchars($detalles_data['observacion'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <label class="form-label" for="ubicacion">Ubicación</label>
                                            <select class="form-select" id="ubicacion" name="ubicacion">
                                                <option value="">-- Seleccione --</option>
                                                <option value="Almacen 1" <?php if (($detalles_data['ubicacion'] ?? '') == 'Almacen 1') echo 'selected'; ?>>Almacen 1</option>
                                                <option value="Almacen 2" <?php if (($detalles_data['ubicacion'] ?? '') == 'Almacen 2') echo 'selected'; ?>>Almacen 2</option>
                                                <option value="Almacen 3" <?php if (($detalles_data['ubicacion'] ?? '') == 'Almacen 3') echo 'selected'; ?>>Almacen 3</option>
                                                <option value="Parihuela" <?php if (($detalles_data['ubicacion'] ?? '') == 'Parihuela') echo 'selected'; ?>>Parihuela</option>
                                                <option value="Boveda" <?php if (($detalles_data['ubicacion'] ?? '') == 'Boveda') echo 'selected'; ?>>Bóveda</option>
                                            </select>
                                        </div>
                                        <hr>
                                        <div class="col-md-4">
                                            <label class="form-label" for="anaquel">N° de Anaquel</label>
                                            <input type="text" class="form-control" id="anaquel" name="anaquel" value="<?php echo htmlspecialchars($detalles_data['anaquel'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label" for="nivel">N° de Nivel</label>
                                            <input type="text" class="form-control" id="nivel" name="nivel" value="<?php echo htmlspecialchars($detalles_data['nivel'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label" for="caja">Caja</label>
                                            <input type="text" class="form-control" id="caja" name="caja" value="<?php echo htmlspecialchars($detalles_data['caja'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="text-center mt-3">
                                        <button type="submit" name="guardar_detalles" class="btn btn-success"><i class="bi bi-save"></i> Guardar Detalles Iniciales</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Formulario de Archivo Digital -->
                        <form action="cpbi_bienes_registro.php?codi_bien=<?php echo $codi_bien; ?>" method="post" enctype="multipart/form-data">
                            <div class="card shadow-sm mb-4">
                                <div class="card-header">
                                    <i class="bi bi-file-earmark-arrow-up"></i> Archivo Digital
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label" for="ruta_archivo_digital">Subir Archivo Digital (Acta, Oficio, etc.)</label>
                                        <input type="file" class="form-control" id="ruta_archivo_digital" name="ruta_archivo_digital">
                                        <?php if (!empty($bien_data['ruta_archivo_digital'])): ?>
                                            <p class="form-text text-muted">Archivo actual: <a href="<?php echo htmlspecialchars($bien_data['ruta_archivo_digital']); ?>" target="_blank">Ver Archivo</a></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" name="guardar_archivo" class="btn btn-success"><i class="bi bi-save"></i> Guardar Archivo</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <a href="cpbi_bienes_incautados.php" class="btn btn-secondary btn-lg"><i class="bi bi-list-ul"></i> Volver al Listado</a>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function(){
    $('#id_ubig_provincia').change(function(){
        var prov_code = $(this).val();
        if(prov_code){
            $.ajax({
                type:'GET',
                url:'get_distritos.php',
                data:'prov_code='+prov_code,
                success:function(data){
                    $('#id_ubig_distrito').html('<option value="">-- Seleccione Distrito --</option>');
                    $.each(data, function(i, distrito) {
                        var prov_only_code = prov_code.substr(-2);
                        var dist_full_code = '04' + prov_only_code + distrito.cdis;
                        $('#id_ubig_distrito').append('<option value="'+dist_full_code+'">'+distrito.dist+'</option>');
                    });
                },
                dataType: "json"
            }); 
        }else{
            $('#id_ubig_distrito').html('<option value="">-- Seleccione Provincia Primero --</option>');
        }
    });

    <?php if (!$is_editing): ?>
    if ($('#id_ubig_provincia').val()) {
        $('#id_ubig_provincia').trigger('change');
    }
    <?php endif; ?>
});
</script>
<?php require_once 'include/page_footer.php'; ?>
