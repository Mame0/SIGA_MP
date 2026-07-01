<?php
/* =====================================================================
 *  vacaciones_listado.php — Listado de conductores (Módulo de Vacaciones)
 *  Fase 1. Ver DOC_MODULO_VACACIONES.md
 * ===================================================================== */
require_once 'include/cabecera.php';

define('VACA_CARGO_LABEL', 'ASISTENTE ADMINISTRATIVO (CONDUCTOR)');

// Listado de conductores activos (maestro local de vacaciones)
$conductores = $Db->query(
    "SELECT id_conductor, ndoc, appat, apmat, nombres, regimen, fecha_ingreso, es_tercero,
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
            <div class="d-flex gap-2">
                <button id="btnTercero" class="btn btn-warning btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTercero">
                    <i class="bi bi-person-plus"></i> Agregar tercero
                </button>
                <button id="btnSync" class="btn btn-light btn-sm fw-bold">
                    <i class="bi bi-arrow-repeat"></i> Sincronizar conductores
                </button>
            </div>
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
                                <td class="fw-semibold">
                                    <?= htmlspecialchars($c['nombre_completo']) ?>
                                    <?php if (!empty($c['es_tercero'])): ?>
                                        <span class="badge bg-warning text-dark">Tercero</span>
                                    <?php endif; ?>
                                </td>
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

    <!-- Modal: agregar chofer tercero -->
    <div class="modal fade" id="modalTercero" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header text-white" style="background:#073A6B">
            <h5 class="modal-title"><i class="bi bi-person-plus"></i> Agregar chofer tercero</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div id="tAlerta" class="alert d-none py-2"></div>
            <p class="small text-muted">Para choferes contratados que <strong>no están</strong> en el maestro de personal.</p>
            <div class="row g-2">
              <div class="col-md-6"><label class="form-label small mb-0">Apellido paterno *</label>
                <input type="text" class="form-control form-control-sm" id="tAppat"></div>
              <div class="col-md-6"><label class="form-label small mb-0">Apellido materno</label>
                <input type="text" class="form-control form-control-sm" id="tApmat"></div>
              <div class="col-12"><label class="form-label small mb-0">Nombres *</label>
                <input type="text" class="form-control form-control-sm" id="tNombres"></div>
              <div class="col-md-4"><label class="form-label small mb-0">DNI</label>
                <input type="text" class="form-control form-control-sm" id="tNdoc" maxlength="8"></div>
              <div class="col-md-4"><label class="form-label small mb-0">Régimen</label>
                <input type="text" class="form-control form-control-sm" id="tRegimen" value="TERCEROS"></div>
              <div class="col-md-4"><label class="form-label small mb-0">Fecha de ingreso</label>
                <input type="date" class="form-control form-control-sm" id="tFing"></div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btnGuardarTercero"><i class="bi bi-save"></i> Guardar</button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const btn = document.getElementById('btnSync');
        const alerta = document.getElementById('alerta');

        document.getElementById('btnGuardarTercero').addEventListener('click', () => {
            const al = document.getElementById('tAlerta');
            const appat = document.getElementById('tAppat').value.trim();
            const nombres = document.getElementById('tNombres').value.trim();
            if (!appat || !nombres) {
                al.className = 'alert alert-warning py-2';
                al.textContent = 'Apellido paterno y nombres son obligatorios.';
                return;
            }
            const fd = new FormData();
            fd.append('appat', appat);
            fd.append('apmat', document.getElementById('tApmat').value.trim());
            fd.append('nombres', nombres);
            fd.append('ndoc', document.getElementById('tNdoc').value.trim());
            fd.append('regimen', document.getElementById('tRegimen').value.trim());
            fd.append('fecha_ingreso', document.getElementById('tFing').value);
            fetch('vacaciones_controller.php?action=crear_tercero', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    al.className = 'alert py-2 alert-' + (d.success ? 'success' : 'danger');
                    al.textContent = d.success ? d.message : d.error;
                    if (d.success) setTimeout(() => location.reload(), 800);
                })
                .catch(e => { al.className = 'alert alert-danger py-2'; al.textContent = 'Error de red: ' + e.message; });
        });

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
