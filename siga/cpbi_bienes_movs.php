<?php
require_once 'include/cabecera.php';
require_once 'classes/Db.class.php';
require_once 'classes/Html.class.php';

$Db = new Db();
$html = new htmlclass();

$codi_bien = isset($_GET['codi_bien']) ? (int)$_GET['codi_bien'] : 0;

// --- Lógica para Guardar/Actualizar Movimientos ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_movimiento'])) {
    $codi_movi = isset($_POST['codi_movi']) ? (int)$_POST['codi_movi'] : 0;
    $codi_bien_post = isset($_POST['codi_bien']) ? (int)$_POST['codi_bien'] : 0;
    $ruta_archivo = $_POST['current_ruta_archivo_digital'] ?? ''; // Mantener el archivo anterior si no se sube uno nuevo

    // Lógica para manejar la subida del archivo digital
    if (isset($_FILES['digi_movi_file']) && $_FILES['digi_movi_file']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/movimientos_digitales/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = $codi_bien_post . '_' . time() . '_' . basename($_FILES["digi_movi_file"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["digi_movi_file"]["tmp_name"], $target_file)) {
            $ruta_archivo = $target_file;
        } else {
            // Opcional: manejar el error de subida
            header("Location: cpbi_bienes_movs.php?codi_bien=" . $codi_bien_post . "&status=error&msg=" . urlencode("Error al mover el archivo subido."));
            exit();
        }
    }

    $datos_movimiento = [
        'fech_movi' => !empty($_POST['fech_movi']) ? str_replace('T', ' ', $_POST['fech_movi']) . ':00' : date('Y-m-d H:i:s'),
        'codi_tmov' => $_POST['codi_tmov'],
        'acta_movi' => $_POST['acta_movi'],
        'esta_movi' => $_POST['esta_movi'],
        'disp_movi' => $_POST['disp_movi'] ?? '', // Campo de texto para la disposición
        'ruta_archivo_digital' => $ruta_archivo, // Guardar la ruta del archivo
        'digi_movi' => $_SESSION['iden_oper'],
        'fdig_movi' => date('YmdHis')
    ];

    try {
        if ($codi_movi > 0) {
            // Actualizar movimiento existente
            $Db->update('mp_cpbi_bienes_movimiento', $datos_movimiento, ['codi_movi' => $codi_movi]);
        } else {
            // Insertar nuevo movimiento
            $datos_movimiento['codi_bien'] = $codi_bien_post;
            $Db->insert('mp_cpbi_bienes_movimiento', $datos_movimiento);
        }

        // Actualizar el estado del proceso en la tabla principal del bien
        $Db->update('mp_cpbi_bienes', [
            'codi_epro' => $_POST['esta_movi']
        ], ['codi_bien' => $codi_bien_post]);

        // Redirigir para evitar reenvío de formulario
        header("Location: cpbi_bienes_movs.php?codi_bien=" . $codi_bien_post . "&status=success");
        exit();

    } catch (Exception $e) {
        header("Location: cpbi_bienes_movs.php?codi_bien=" . $codi_bien_post . "&status=error&msg=" . urlencode($e->getMessage()));
        exit();
    }
}

// --- Carga de datos para la vista ---
if ($codi_bien === 0 && isset($_POST['nume_regi'])) {
    // Búsqueda inicial por número de registro si no se pasó codi_bien
    $bien_encontrado_result = $Db->query("SELECT codi_bien FROM mp_cpbi_bienes WHERE nume_regi = :nume_regi", ['nume_regi' => $_POST['nume_regi']]);
    $bien_encontrado = !empty($bien_encontrado_result) ? $bien_encontrado_result[0] : null;

    if ($bien_encontrado) {
        $codi_bien = $bien_encontrado['codi_bien'];
        header("Location: cpbi_bienes_movs.php?codi_bien=" . $codi_bien);
        exit();
    } else {
        $bien = null;
        $movimientos = [];
        $error_busqueda = "No se encontró ningún bien con el número de registro '{$_POST['nume_regi']}'.";
    }
} elseif ($codi_bien > 0) {
    // Cargar datos del bien
    $query_bien = "SELECT b.*, e.x_nombre as estado, ep.x_nombre as estado_proceso
                   FROM mp_cpbi_bienes b
                   LEFT JOIN mp_maes_cpbi_estado e ON b.codi_esta = e.n_codigo
                   LEFT JOIN mp_maes_cpbi_estado_proceso ep ON b.codi_epro = ep.n_codigo
                   WHERE b.codi_bien = :codi_bien";
    $bien_result = $Db->query($query_bien, ['codi_bien' => $codi_bien]);
    $bien = !empty($bien_result) ? $bien_result[0] : null;

    // Cargar movimientos del bien (Corregido)
    $query_movs = "SELECT m.*, tm.x_nombre as desc_disp, ep.x_nombre as desc_esta, m.ruta_archivo_digital
                   FROM mp_cpbi_bienes_movimiento m
                   LEFT JOIN mp_maes_cpbi_tipo_movimiento tm ON m.codi_tmov = tm.n_codigo
                   LEFT JOIN mp_maes_cpbi_estado_proceso ep ON m.esta_movi = ep.n_codigo
                   WHERE m.codi_bien = :codi_bien
                   ORDER BY m.fech_movi DESC";
    $movimientos = $Db->query($query_movs, ['codi_bien' => $codi_bien]);
} else {
    $bien = null;
    $movimientos = [];
}

// Cargar datos para los selects del modal (Corregido)
$disposiciones = $Db->query("SELECT n_codigo, x_nombre FROM mp_maes_cpbi_tipo_movimiento WHERE n_estado = 1 ORDER BY x_nombre ASC");
$estados_proceso = $Db->query("SELECT n_codigo, x_nombre FROM mp_maes_cpbi_estado_proceso WHERE n_estado = 1 ORDER BY x_nombre ASC");

?>
<?php
$page_title = 'Historial de Movimientos del Bien';
require_once 'include/page_header.php';
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary"><i class="bi bi-arrows-move"></i> Historial de Movimientos</h2>
        <a href="cpbi_bienes_incautados.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver al Listado</a>
    </div>

    <?php if (!isset($bien) || $bien === null): ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Buscar Bien para ver Movimientos</h5>
                <form method="POST" action="cpbi_bienes_movs.php" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="nume_regi" class="form-label">N&uacute;mero de Registro del Bien</label>
                        <input type="text" class="form-control" id="nume_regi" name="nume_regi" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Buscar</button>
                    </div>
                </form>
                <?php if (isset($error_busqueda)): ?>
                    <div class="alert alert-danger mt-3"><?= htmlspecialchars($error_busqueda) ?></div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <!-- Card de Detalles del Bien -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Detalles del Bien</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3"><p><strong>Nro. Registro:</strong><br> <?= htmlspecialchars($bien['nume_regi']) ?></p></div>
                    <div class="col-md-6"><p><strong>Descripci&oacute;n:</strong><br> <?= htmlspecialchars($bien['desc_bien']) ?></p></div>
                    <div class="col-md-3"><p><strong>Marca / Serie:</strong><br> <?= htmlspecialchars($bien['marc_bien']) ?> / <?= htmlspecialchars($bien['seri_bien']) ?></p></div>
                    <div class="col-md-3"><p><strong>Estado Actual:</strong><br> <?= htmlspecialchars($bien['estado']) ?></p></div>
                    <div class="col-md-3"><p><strong>Estado del Proceso:</strong><br> <?= htmlspecialchars($bien['estado_proceso']) ?></p></div>
                    <div class="col-md-3"><p><strong>Nro. Carpeta:</strong><br> <?= htmlspecialchars($bien['nume_carp']) ?></p></div>
                </div>
            </div>
        </div>

        <!-- Card de Movimientos -->
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Movimientos Registrados</h5>
                <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#movimientoModal">
                    <i class="bi bi-plus-circle"></i> Registrar Nuevo Movimiento
                </button>
            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Fecha Movimiento</th>
                                                <th>Tipo de Movimiento</th>
                                                <th>Acta</th>
                                                <th>Disposición</th>
                                                <th>Estado del Proceso</th>
                                                <th>Archivo Digital</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($movimientos)): ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">No hay movimientos registrados para este bien.</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($movimientos as $mov): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($mov['fech_movi']))) ?></td>
                                                        <td><?= htmlspecialchars($mov['desc_disp']) ?></td>
                                                        <td><?= htmlspecialchars($mov['acta_movi']) ?></td>
                                                        <td><?= htmlspecialchars($mov['disp_movi']) ?></td>
                                                        <td><?= htmlspecialchars($mov['desc_esta']) ?></td>
                                                        <td class="text-center">
                                                            <?php if (!empty($mov['ruta_archivo_digital'])) : ?>
                                                                <a href="<?= htmlspecialchars($mov['ruta_archivo_digital']) ?>" target="_blank"><i class="bi bi-file-earmark-arrow-down-fill text-success fs-5"></i></a>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#movimientoModal"
                                                                    data-movimiento='<?= json_encode($mov) ?>'>
                                                                <i class="bi bi-pencil-square"></i> Editar
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal para Registrar/Editar Movimiento -->
<div class="modal" id="movimientoModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="cpbi_bienes_movs.php?codi_bien=<?= $codi_bien ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Registrar Movimiento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="codi_movi" id="codi_movi">
                    <input type="hidden" name="codi_bien" value="<?= $codi_bien ?>">
                    <input type="hidden" name="current_ruta_archivo_digital" id="current_ruta_archivo_digital">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="fech_movi" class="form-label">Fecha del Movimiento</label>
                            <input type="datetime-local" class="form-control" id="fech_movi" name="fech_movi" required value="<?= date('Y-m-d\TH:i') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="codi_tmov" class="form-label">Tipo de Movimiento</label>
                            <select class="form-select" id="codi_tmov" name="codi_tmov" required>
                                <option value="">-- Seleccione --</option>
                                <?php if($disposiciones) foreach ($disposiciones as $disp): ?>
                                    <option value="<?= $disp['n_codigo'] ?>"><?= htmlspecialchars($disp['x_nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="acta_movi" class="form-label">Acta / Documento de Referencia</label>
                            <input type="text" class="form-control" id="acta_movi" name="acta_movi">
                        </div>
                        <div class="col-md-12">
                            <label for="disp_movi" class="form-label">Disposición del Movimiento</label>
                            <input type="text" class="form-control" id="disp_movi" name="disp_movi">
                        </div>
                        <div class="col-md-12">
                            <label for="esta_movi" class="form-label">Nuevo Estado del Proceso</label>
                            <select class="form-select" id="esta_movi" name="esta_movi" required>
                                <option value="">-- Seleccione --</option>
                                <?php if($estados_proceso) foreach ($estados_proceso as $estado): ?>
                                    <option value="<?= $estado['n_codigo'] ?>"><?= htmlspecialchars($estado['x_nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="digi_movi_file" class="form-label">Archivo Digital</label>
                            <input type="file" class="form-control" id="digi_movi_file" name="digi_movi_file">
                            <div id="current_file_display" class="mt-2"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="guardar_movimiento" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var movimientoModal = document.getElementById('movimientoModal');
    movimientoModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; // Button that triggered the modal
        var modalTitle = movimientoModal.querySelector('.modal-title');
        var form = movimientoModal.querySelector('form');
        var currentRutaArchivoDigitalInput = document.getElementById('current_ruta_archivo_digital');
        var currentFileDisplay = document.getElementById('current_file_display');
        var fileInput = document.getElementById('digi_movi_file');

        // Reset form fields and file display
        form.reset();
        fileInput.value = ''; // Explicitly clear file input
        currentFileDisplay.innerHTML = '';
        currentRutaArchivoDigitalInput.value = '';
        document.getElementById('codi_movi').value = '';
        document.getElementById('fech_movi').value = "<?= date('Y-m-d\TH:i') ?>"; // Default to now

        var movimientoData = button.getAttribute('data-movimiento');

        if (movimientoData) {
            // --- MODO EDICIÓN ---
            modalTitle.textContent = 'Editar Movimiento';
            var movimiento = JSON.parse(movimientoData);
 
            // Poblar el formulario
            document.getElementById('codi_movi').value = movimiento.codi_movi;
            
            // La fecha viene como un string YYYY-MM-DD HH:MM:SS de la BD, pero el input date solo necesita YYYY-MM-DD
            if (movimiento.fech_movi) {
                // Formateamos a YYYY-MM-DDTHH:MM para el input datetime-local
                document.getElementById('fech_movi').value = movimiento.fech_movi.replace(' ', 'T').substring(0, 16);
            }
 
            document.getElementById('codi_tmov').value = movimiento.codi_tmov;
            document.getElementById('acta_movi').value = movimiento.acta_movi;
            document.getElementById('disp_movi').value = movimiento.disp_movi; // Populate the new disp_movi field
            document.getElementById('esta_movi').value = movimiento.esta_movi;
            
            // Manejar el archivo digital existente
            currentRutaArchivoDigitalInput.value = movimiento.ruta_archivo_digital || '';
            if (movimiento.ruta_archivo_digital) {
                currentFileDisplay.innerHTML = `<p class="form-text">Archivo actual: <a href="${movimiento.ruta_archivo_digital}" target="_blank" class="btn btn-sm btn-info">Ver Archivo</a></p>`;
            }

        } else {
            // --- MODO REGISTRO ---
            modalTitle.textContent = 'Registrar Nuevo Movimiento';
        }
    });
});
</script>

<?php require_once 'include/page_footer.php'; ?>
