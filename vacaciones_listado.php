<?php
/* =====================================================================
 *  vacaciones_listado.php — Listado de conductores (Módulo de Vacaciones)
 *  Fase 1. Ver DOC_MODULO_VACACIONES.md
 * ===================================================================== */
require_once 'include/cabecera.php';

define('VACA_CARGO_LABEL', 'ASISTENTE ADMINISTRATIVO (CONDUCTOR)');

// Listado de conductores activos (maestro local de vacaciones)
$conductores = $Db->query(
    "SELECT id_conductor, ndoc, appat, apmat, nombres, regimen, fecha_ingreso,
            CONCAT(appat,' ',apmat,', ',nombres) AS nombre_completo
     FROM mp_vaca_conductor
     WHERE estado = 1
     ORDER BY appat, apmat, nombres"
);
$total = is_array($conductores) ? count($conductores) : 0;

function vaca_fecha($f) {
    if (empty($f) || $f === '0000-00-00') return '-';
    $d = DateTime::createFromFormat('Y-m-d', $f);
    return $d ? $d->format('d/m/Y') : htmlspecialchars($f);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacaciones - Conductores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .header-vaca { background-color: #073A6B; }
        .card-header { background-color: #073A6B; color: #fff; font-weight: bold; }
        .btn-primary { background-color: #073A6B; border-color: #073A6B; }
        .btn-primary:hover { background-color: #052849; border-color: #052849; }
        .table th { background-color: #e9ecef; }
        .text-primary { color: #073A6B !important; }
    </style>
</head>
<body>
    <header class="header-vaca text-white p-3 mb-3">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="bi bi-person-vcard fs-2 me-2"></i>
                <h1 class="h4 mb-0">Vacaciones de Conductores</h1>
            </div>
            <button id="btnSync" class="btn btn-light btn-sm fw-bold">
                <i class="bi bi-arrow-repeat"></i> Sincronizar conductores
            </button>
        </div>
    </header>

    <div class="container-fluid">
        <div id="alerta" class="alert d-none" role="alert"></div>

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-people-fill"></i> Flota de conductores</span>
                <span class="badge bg-light text-dark">Total: <?= $total ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Apellidos y Nombres</th>
                                <th>Cargo</th>
                                <th class="text-center">Régimen</th>
                                <th class="text-center">Fecha de Ingreso</th>
                                <th class="text-center">DNI</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($total === 0): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No hay conductores cargados. Pulse
                                    <strong>"Sincronizar conductores"</strong> para traerlos del maestro de personal.
                                </td>
                            </tr>
                        <?php else: $i = 1; foreach ($conductores as $c): ?>
                            <tr>
                                <td class="text-center"><?= $i++ ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($c['nombre_completo']) ?></td>
                                <td><?= VACA_CARGO_LABEL ?></td>
                                <td class="text-center"><?= htmlspecialchars($c['regimen']) ?: '-' ?></td>
                                <td class="text-center"><?= vaca_fecha($c['fecha_ingreso']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($c['ndoc']) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const btn = document.getElementById('btnSync');
        const alerta = document.getElementById('alerta');

        function mostrarAlerta(tipo, texto) {
            alerta.className = 'alert alert-' + tipo;
            alerta.textContent = texto;
        }

        btn.addEventListener('click', () => {
            btn.disabled = true;
            const original = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Sincronizando...';

            fetch('vacaciones_controller.php?action=sincronizar_conductores', { method: 'POST' })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        mostrarAlerta('success', data.message);
                        setTimeout(() => location.reload(), 900);
                    } else {
                        mostrarAlerta('danger', data.error || 'Error desconocido al sincronizar.');
                        btn.disabled = false;
                        btn.innerHTML = original;
                    }
                })
                .catch(err => {
                    mostrarAlerta('danger', 'Error de red: ' + err.message);
                    btn.disabled = false;
                    btn.innerHTML = original;
                });
        });
    </script>
</body>
</html>
