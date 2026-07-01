<?php
/* =====================================================================
 *  vacaciones_calendario.php — Calendario de concurrencia de la flota
 *  Fase 3. Reemplaza la "sábana" manual del Excel.
 *  Ver DOC_MODULO_VACACIONES.md
 * ===================================================================== */
require_once 'include/cabecera.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario de Vacaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .header-vaca { background-color: #073A6B; }
        .text-primary { color: #073A6B !important; }
        .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }
        .cal-dow { text-align: center; font-weight: 700; color: #073A6B; padding: 6px 0; }
        .cal-cell {
            min-height: 78px; border: 1px solid #dee2e6; border-radius: 6px; padding: 4px;
            background: #fff; position: relative; cursor: default;
        }
        .cal-cell.empty { background: transparent; border: none; }
        .cal-num { font-weight: 600; font-size: .85rem; }
        .cal-occ { font-size: .72rem; margin-top: 4px; }
        .cal-cell.libre   { }
        .cal-cell.parcial { background: #fff3cd; }   /* hay ausencias pero hay cupo */
        .cal-cell.lleno   { background: #f8d7da; border-color: #dc3545; cursor: pointer; }
        .cal-cell.casi    { background: #ffe5b4; }    /* a 1 del tope */
        .badge-occ { font-size: .7rem; }
    </style>
</head>
<body>
    <header class="header-vaca text-white p-3 mb-3">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="bi bi-calendar3 fs-3 me-2"></i>
                <h1 class="h4 mb-0">Calendario de Vacaciones de la Flota</h1>
            </div>
            <button class="btn btn-light btn-sm fw-bold" id="btnConfig" data-bs-toggle="modal" data-bs-target="#modalConfig">
                <i class="bi bi-gear"></i> Tope de flota: <span id="topeHeader">4</span>
            </button>
        </div>
    </header>

    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <button class="btn btn-outline-secondary" id="btnPrev"><i class="bi bi-chevron-left"></i></button>
                    <h3 class="h5 mb-0 text-primary" id="tituloMes"></h3>
                    <button class="btn btn-outline-secondary" id="btnNext"><i class="bi bi-chevron-right"></i></button>
                </div>

                <div class="d-flex gap-3 mb-2 small flex-wrap">
                    <span><span class="badge" style="background:#fff;border:1px solid #dee2e6;color:#000">&nbsp;&nbsp;</span> Libre</span>
                    <span><span class="badge" style="background:#fff3cd;color:#000">&nbsp;&nbsp;</span> Con ausencias</span>
                    <span><span class="badge" style="background:#ffe5b4;color:#000">&nbsp;&nbsp;</span> A 1 del tope</span>
                    <span><span class="badge bg-danger">&nbsp;&nbsp;</span> Tope alcanzado (<span id="topeTxt">4</span>)</span>
                </div>

                <div class="cal-grid" id="dowRow"></div>
                <div class="cal-grid mt-1" id="calGrid"></div>
            </div>
        </div>
    </div>

    <!-- Modal detalle del día -->
    <div class="modal fade" id="modalDia" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header text-white" style="background:#073A6B">
            <h5 class="modal-title" id="modalDiaTitulo"></h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body"><ul id="modalDiaLista" class="mb-0"></ul></div>
        </div>
      </div>
    </div>

    <!-- Modal configuración del tope -->
    <div class="modal fade" id="modalConfig" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header text-white" style="background:#073A6B">
            <h5 class="modal-title"><i class="bi bi-gear"></i> Configuración de vacaciones</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div id="cfgAlerta" class="alert d-none py-2"></div>
            <div class="mb-3">
              <label class="form-label fw-bold">Tope de conductores de vacaciones por día</label>
              <input type="number" class="form-control" id="cfgTope" min="1" max="37" step="1">
              <div class="form-text">Máximo de ausencias simultáneas permitidas en toda la flota (por defecto 4).</div>
            </div>
            <div class="mb-1">
              <label class="form-label fw-bold">Días de vacaciones por periodo</label>
              <input type="number" class="form-control" id="cfgDias" min="1" max="60" step="1">
              <div class="form-text">Normalmente 30. Cambiarlo afecta el saldo de nuevos periodos.</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btnGuardarConfig"><i class="bi bi-save"></i> Guardar</button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const API = 'vacaciones_controller.php';
    const MESES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    const DOW = ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'];
    let cursor = new Date();
    cursor.setDate(1);
    let tope = 4;
    const modalDia = new bootstrap.Modal(document.getElementById('modalDia'));

    function fstr(d) {
        return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
    }

    // Encabezado de días de la semana
    document.getElementById('dowRow').innerHTML = DOW.map(d => `<div class="cal-dow">${d}</div>`).join('');

    function render(data) {
        const y = cursor.getFullYear(), m = cursor.getMonth();
        document.getElementById('tituloMes').textContent = MESES[m] + ' ' + y;
        document.getElementById('topeTxt').textContent = tope;

        const primero = new Date(y, m, 1);
        // offset Lunes=0 ... Domingo=6
        let offset = (primero.getDay() + 6) % 7;
        const diasMes = new Date(y, m+1, 0).getDate();

        const grid = document.getElementById('calGrid');
        grid.innerHTML = '';
        for (let i = 0; i < offset; i++) {
            const e = document.createElement('div');
            e.className = 'cal-cell empty';
            grid.appendChild(e);
        }
        for (let d = 1; d <= diasMes; d++) {
            const fecha = `${y}-${String(m+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            const info = data.dias[fecha];
            const occ = info ? info.ocupados : 0;
            const cell = document.createElement('div');
            let cls = 'libre';
            if (occ >= tope) cls = 'lleno';
            else if (occ === tope - 1) cls = 'casi';
            else if (occ > 0) cls = 'parcial';
            cell.className = 'cal-cell ' + cls;
            cell.innerHTML = `<div class="cal-num">${d}</div>` +
                (occ > 0 ? `<div class="cal-occ"><span class="badge badge-occ ${occ>=tope?'bg-danger':'bg-secondary'}">${occ}/${tope}</span></div>` : '');
            if (info && occ > 0) {
                cell.style.cursor = 'pointer';
                cell.onclick = () => {
                    document.getElementById('modalDiaTitulo').textContent = `Conductores de vacaciones · ${fecha}`;
                    document.getElementById('modalDiaLista').innerHTML =
                        info.conductores.map(n => `<li>${n}</li>`).join('');
                    modalDia.show();
                };
            }
            grid.appendChild(cell);
        }
    }

    function cargar() {
        const y = cursor.getFullYear(), m = cursor.getMonth();
        const desde = fstr(new Date(y, m, 1));
        const hasta = fstr(new Date(y, m+1, 0));
        fetch(`${API}?action=calendario_ocupacion&desde=${desde}&hasta=${hasta}`)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    tope = data.tope;
                    document.getElementById('topeHeader').textContent = tope;
                    render(data);
                }
            });
    }

    document.getElementById('btnPrev').onclick = () => { cursor.setMonth(cursor.getMonth()-1); cargar(); };
    document.getElementById('btnNext').onclick = () => { cursor.setMonth(cursor.getMonth()+1); cargar(); };

    /* ---- Configuración del tope de flota ---- */
    document.getElementById('modalConfig').addEventListener('show.bs.modal', () => {
        document.getElementById('cfgAlerta').classList.add('d-none');
        fetch(`${API}?action=obtener_config`)
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    document.getElementById('cfgTope').value = d.VACA_TOPE_FLOTA;
                    document.getElementById('cfgDias').value = d.VACA_DIAS_PERIODO;
                }
            });
    });

    document.getElementById('btnGuardarConfig').onclick = () => {
        const topeVal = parseInt(document.getElementById('cfgTope').value, 10);
        const diasVal = parseInt(document.getElementById('cfgDias').value, 10);
        const al = document.getElementById('cfgAlerta');
        if (!(topeVal >= 1 && topeVal <= 37)) {
            al.className = 'alert alert-warning py-2';
            al.textContent = 'El tope debe estar entre 1 y 37.';
            return;
        }
        const fd = new FormData();
        fd.append('tope', topeVal);
        if (diasVal >= 1 && diasVal <= 60) fd.append('dias', diasVal);
        fetch(`${API}?action=guardar_config`, {method:'POST', body:fd})
            .then(r => r.json())
            .then(d => {
                al.className = 'alert py-2 alert-' + (d.success ? 'success' : 'danger');
                al.textContent = d.success ? d.message : d.error;
                if (d.success) {
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById('modalConfig')).hide();
                        cargar();
                    }, 700);
                }
            });
    };

    cargar();
    </script>
</body>
</html>
