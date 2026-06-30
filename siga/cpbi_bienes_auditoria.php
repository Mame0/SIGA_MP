<?php
require_once 'include/cabecera.php';
require_once 'classes/Db.class.php';

$Db = new Db();

$bien = null;
$historial = [];
$error_busqueda = '';
$nume_regi_buscado = isset($_POST['nume_regi']) ? trim($_POST['nume_regi']) : '';

// Función para obtener el nombre completo del operador
function getOperatorName($operatorId, $Db) {
    if (!$operatorId) return 'N/A';
    $operator = $Db->query("SELECT nomb_oper, appa_oper, apma_oper FROM mp_admi_oper WHERE iden_oper = :id", [':id' => $operatorId]);
    if ($operator) {
        return trim($operator[0]['nomb_oper'] . ' ' . $operator[0]['appa_oper'] . ' ' . $operator[0]['apma_oper']);
    }
    return 'Desconocido';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($nume_regi_buscado)) {
    // 1. Buscar el bien por número de registro
    $query_bien = "SELECT b.*, e.x_nombre as estado, ep.x_nombre as estado_proceso,
                   op.nomb_oper as reg_nomb_oper, op.appa_oper as reg_appa_oper, op.apma_oper as reg_apma_oper
                   FROM mp_cpbi_bienes b
                   LEFT JOIN mp_maes_cpbi_estado e ON b.codi_esta = e.n_codigo
                   LEFT JOIN mp_maes_cpbi_estado_proceso ep ON b.codi_epro = ep.n_codigo
                   LEFT JOIN mp_admi_oper op ON b.digi_oper_id = op.iden_oper
                   WHERE b.nume_regi = :nume_regi";
    $bien_result = $Db->query($query_bien, [':nume_regi' => $nume_regi_buscado]);
    $bien = !empty($bien_result) ? $bien_result[0] : null;

    if ($bien) {
        $codi_bien = $bien['codi_bien'];

        // 2. Obtener el registro inicial del bien
        $historial[] = [
            'fecha' => $bien['fech_inte'],
            'tipo_movimiento' => 'REGISTRO INICIAL',
            'detalle' => 'El bien fue registrado en el sistema.',
            'usuario' => trim($bien['reg_nomb_oper'] . ' ' . $bien['reg_appa_oper'] . ' ' . $bien['reg_apma_oper']),
            'archivo' => $bien['ruta_archivo_digital']
        ];

        // 3. Obtener los movimientos del bien
        $query_movs = "SELECT m.fech_movi, tm.x_nombre as tipo_movimiento, m.acta_movi, ep.x_nombre as estado_proceso, m.digi_movi, m.disp_movi,
                       op.nomb_oper as mov_nomb_oper, op.appa_oper as mov_appa_oper, op.apma_oper as mov_apma_oper
                       FROM mp_cpbi_bienes_movimiento m
                       LEFT JOIN mp_maes_cpbi_tipo_movimiento tm ON m.codi_tmov = tm.n_codigo
                       LEFT JOIN mp_maes_cpbi_estado_proceso ep ON m.esta_movi = ep.n_codigo
                       LEFT JOIN mp_admi_oper op ON m.digi_movi = op.iden_oper
                       WHERE m.codi_bien = :codi_bien
                       ORDER BY m.fech_movi ASC";
        $movimientos = $Db->query($query_movs, [':codi_bien' => $codi_bien]);

        foreach ($movimientos as $mov) {
            $historial[] = [
                'fecha' => $mov['fech_movi'],
                'tipo_movimiento' => $mov['tipo_movimiento'],
                'detalle' => 'Acta/Ref: ' . $mov['acta_movi'] . '. Nuevo estado: ' . $mov['estado_proceso'],
                'usuario' => trim($mov['mov_nomb_oper'] . ' ' . $mov['mov_appa_oper'] . ' ' . $mov['mov_apma_oper']),
                'archivo' => $mov['disp_movi']
            ];
        }

        // 4. Obtener los detalles iniciales de auditoría (recepción, ubicación)
        $query_auditoria_detalles = "SELECT a.fecha_movimiento, a.tipo_movimiento, a.observacion, a.responsable_entrega_nombre, a.responsable_recepcion_nombre,
                                     op.nomb_oper as aud_nomb_oper, op.appa_oper as aud_appa_oper, op.apma_oper as aud_apma_oper
                                     FROM mp_cpbi_auditoria a
                                     LEFT JOIN mp_admi_oper op ON a.id_operador = op.iden_oper
                                     WHERE a.codi_bien = :codi_bien AND a.tipo_movimiento = 'DETALLES_INICIALES'
                                     ORDER BY a.fecha_movimiento ASC";
        $auditoria_detalles = $Db->query($query_auditoria_detalles, [':codi_bien' => $codi_bien]);

        foreach ($auditoria_detalles as $aud) {
            $historial[] = [
                'fecha' => $aud['fecha_movimiento'],
                'tipo_movimiento' => 'DETALLES INICIALES',
                'detalle' => 'Resp. Entrega: ' . $aud['responsable_entrega_nombre'] . '. Resp. Recepción: ' . $aud['responsable_recepcion_nombre'] . '. Obs: ' . $aud['observacion'],
                'usuario' => trim($aud['aud_nomb_oper'] . ' ' . $aud['aud_appa_oper'] . ' ' . $aud['aud_apma_oper']),
                'archivo' => '' // No hay archivo digital asociado directamente a este tipo de auditoría
            ];
        }


        // 5. Ordenar todo el historial por fecha
        usort($historial, function($a, $b) {
            return strtotime($a['fecha']) - strtotime($b['fecha']);
        });

    } else {
        $error_busqueda = "No se encontró ningún bien con el número de registro '{$nume_regi_buscado}'.";
    }
}

$page_title = 'Auditoría de Bienes Incautados';
require_once 'include/page_header.php';
?>

<div class="container-fluid mt-4">
    <h2 class="text-center mb-4 text-primary"><i class="bi bi-clipboard2-data-fill"></i> Auditoría de Trazabilidad de Bienes</h2>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <i class="bi bi-search"></i> Buscar Bien por Número de Registro
        </div>
        <div class="card-body">
            <form method="POST" action="cpbi_bienes_auditoria.php" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="nume_regi" class="form-label">Número de Registro del Bien</label>
                    <input type="text" class="form-control" id="nume_regi" name="nume_regi" value="<?= htmlspecialchars($nume_regi_buscado) ?>" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Buscar Historial</button>
                </div>
                <div class="col-md-2">
                    <a href="cpbi_bienes_auditoria.php" class="btn btn-secondary w-100">Limpiar</a>
                </div>
            </form>
            <?php if ($error_busqueda): ?>
                <div class="alert alert-danger mt-3"><?= htmlspecialchars($error_busqueda) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($bien): ?>
    <!-- Card de Detalles del Bien -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Detalles del Bien</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><p><strong>Nro. Registro:</strong><br> <?= htmlspecialchars($bien['nume_regi']) ?></p></div>
                <div class="col-md-6"><p><strong>Descripción:</strong><br> <?= htmlspecialchars($bien['desc_bien']) ?></p></div>
                <div class="col-md-3"><p><strong>Estado Actual del Proceso:</strong><br> <?= htmlspecialchars($bien['estado_proceso']) ?></p></div>
            </div>
        </div>
    </div>

    <!-- Card de Historial -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Historial de Trazabilidad</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Tipo de Evento</th>
                            <th>Detalle</th>
                            <th>Responsable</th>
                            <th>Archivo Digital</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($historial)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No hay historial disponible para este bien.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($historial as $evento): ?>
                                <tr>
                                    <td><?= htmlspecialchars(date('d/m/Y H:i:s', strtotime($evento['fecha']))) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $evento['tipo_movimiento'] == 'REGISTRO INICIAL' || $evento['tipo_movimiento'] == 'DETALLES INICIALES' ? 'primary' : 'secondary' ?>">
                                            <?= htmlspecialchars($evento['tipo_movimiento']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($evento['detalle']) ?></td>
                                    <td><?= htmlspecialchars($evento['usuario']) ?></td>
                                    <td class="text-center">
                                        <?php if (!empty($evento['archivo'])): ?>
                                            <a href="<?= htmlspecialchars($evento['archivo']) ?>" target="_blank"><i class="bi bi-file-earmark-arrow-down-fill text-success fs-5"></i></a>
                                        <?php endif; ?>
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

<?php require_once 'include/page_footer.php'; ?>
