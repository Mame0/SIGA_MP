<?php
/* =====================================================================
 *  vacaciones_registro.php — Registro/programación de vacaciones
 *  Fase 2. Ver DOC_MODULO_VACACIONES.md
 * ===================================================================== */
require_once 'include/cabecera.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Vacaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .header-vaca { background-color: #073A6B; }
        .card-header { background-color: #073A6B; color: #fff; font-weight: bold; }
        .btn-primary { background-color: #073A6B; border-color: #073A6B; }
        .btn-primary:hover { background-color: #052849; border-color: #052849; }
        .text-primary { color: #073A6B !important; }
        .list-conductor { max-height: 240px; overflow-y: auto; }
        .periodo-chip { cursor: pointer; }
        .periodo-chip.activo { outline: 3px solid #073A6B; }
        .saldo-big { font-size: 2rem; font-weight: 700; }
    </style>
</head>
<body>
    <header class="header-vaca text-white p-3 mb-3">
        <div class="container-fluid d-flex align-items-center">
            <i class="bi bi-calendar2-plus fs-3 me-2"></i>
            <h1 class="h4 mb-0">Programación de Vacaciones</h1>
        </div>
    </header>

    <div class="container-fluid">
        <div id="alerta" class="alert d-none" role="alert"></div>

        <!-- Paso 1: Conductor -->
        <div class="card shadow-sm mb-3">
            <div class="card-header"><i class="bi bi-person-fill"></i> 1. Seleccionar conductor</div>
            <div class="card-body">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="buscaConductor" class="form-control" autocomplete="off"
                           placeholder="Escriba apellido, nombre o DNI (mín. 2 caracteres)...">
                </div>
                <div id="resultConductor" class="list-group list-conductor mt-2"></div>
                <div id="conductorSel" class="mt-2 fw-bold text-primary"></div>
            </div>
        </div>

        <!-- Paso 2: Periodos -->
        <div class="card shadow-sm mb-3 d-none" id="cardPeriodos">
            <div class="card-header"><i class="bi bi-calendar3"></i> 2. Periodo vacacional</div>
            <div class="card-body">
                <div id="listaPeriodos" class="d-flex flex-wrap gap-2"></div>
                <div id="zonaCrear" class="mt-3"></div>
            </div>
        </div>

        <!-- Paso 3: Tramos -->
        <div class="card shadow-sm mb-4 d-none" id="cardTramos">
            <div class="card-header d-flex justify-content-between">
                <span><i class="bi bi-calendar-range"></i> 3. Tramos del periodo <span id="etiqPeriodo"></span></span>
                <span>Saldo: <span id="saldoTxt" class="badge bg-light text-dark"></span></span>
            </div>
            <div class="card-body">
                <div class="row text-center mb-3">
                    <div class="col"><div class="text-muted small">ASIGNADOS</div><div class="saldo-big text-primary" id="kAsig">30</div></div>
                    <div class="col"><div class="text-muted small">PROGRAMADOS</div><div class="saldo-big" id="kUsa">0</div></div>
                    <div class="col"><div class="text-muted small">SALDO</div><div class="saldo-big text-danger" id="kSaldo">30</div></div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="text-muted mb-0">Tramos ya programados</h6>
                    <a id="lnkHistorial" class="small" href="#" target="_blank"><i class="bi bi-clock-history"></i> Ver historial</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead><tr><th>#</th><th>Inicio</th><th>Fin</th><th class="text-center">Días</th><th class="text-center">Acción</th></tr></thead>
                        <tbody id="tbodyTramos"></tbody>
                    </table>
                </div>

                <hr>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="text-muted mb-0" id="tituloEditor">Agregar nuevos tramos</h6>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary active" id="btnModoAgregar">
                            <i class="bi bi-plus-circle"></i> Agregar
                        </button>
                        <button type="button" class="btn btn-outline-warning" id="btnModoReprogramar">
                            <i class="bi bi-arrow-repeat"></i> Reprogramar todo
                        </button>
                    </div>
                </div>
                <div id="avisoModo" class="small text-warning-emphasis mt-1 d-none">
                    <i class="bi bi-exclamation-triangle"></i> Modo reprogramación: se reemplazarán <strong>todos</strong> los tramos activos por los de abajo.
                </div>
                <div id="nuevosTramos" class="mt-2"></div>
                <button class="btn btn-outline-secondary btn-sm mt-2" id="btnAddFila">
                    <i class="bi bi-plus-circle"></i> Agregar tramo
                </button>
                <div class="mt-2 small">Días <span id="lblTotal">a agregar</span>: <strong id="totalNuevo">0</strong></div>

                <div class="mt-3 text-end">
                    <button class="btn btn-primary" id="btnGuardar"><i class="bi bi-save"></i> Guardar programación</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const API = 'vacaciones_controller.php';
    let conductorId = null, periodoId = null;
    let modo = 'agregar';            // 'agregar' | 'reprogramar'
    let tramosActivos = [];          // tramos ACTIVOS del periodo seleccionado

    const $ = s => document.querySelector(s);
    function alerta(tipo, txt) {
        const a = $('#alerta');
        a.className = 'alert alert-' + tipo;
        a.textContent = txt;
        a.scrollIntoView({behavior:'smooth', block:'nearest'});
    }
    function diasEntre(fi, ff) {
        if (!fi || !ff) return 0;
        const a = new Date(fi), b = new Date(ff);
        if (b < a) return 0;
        return Math.round((b - a) / 86400000) + 1;
    }

    /* ---- Paso 1: búsqueda de conductor ---- */
    let tBusca;
    $('#buscaConductor').addEventListener('input', e => {
        clearTimeout(tBusca);
        const q = e.target.value.trim();
        const cont = $('#resultConductor');
        if (q.length < 2) { cont.innerHTML = ''; return; }
        tBusca = setTimeout(() => {
            fetch(`${API}?action=buscar_conductores&q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(lista => {
                    cont.innerHTML = '';
                    lista.forEach(c => {
                        const a = document.createElement('button');
                        a.type = 'button';
                        a.className = 'list-group-item list-group-item-action';
                        a.innerHTML = `<strong>${c.nombre_completo}</strong> <span class="text-muted small">— DNI ${c.ndoc} · ${c.regimen||'s/régimen'}</span>`;
                        a.onclick = () => seleccionarConductor(c);
                        cont.appendChild(a);
                    });
                });
        }, 250);
    });

    function seleccionarConductor(c) {
        conductorId = c.id_conductor;
        periodoId = null;
        $('#resultConductor').innerHTML = '';
        $('#buscaConductor').value = '';
        $('#conductorSel').innerHTML = `<i class="bi bi-check-circle-fill"></i> ${c.nombre_completo} (DNI ${c.ndoc})`;
        $('#cardTramos').classList.add('d-none');
        cargarPeriodos();
    }

    /* ---- Paso 2: periodos ---- */
    function cargarPeriodos() {
        $('#cardPeriodos').classList.remove('d-none');
        fetch(`${API}?action=obtener_periodos&id_conductor=${conductorId}`)
            .then(r => r.json())
            .then(d => {
                const cont = $('#listaPeriodos');
                cont.innerHTML = '';
                if (!d.periodos.length) {
                    cont.innerHTML = '<span class="text-muted">Sin periodos. Genere uno abajo.</span>';
                }
                d.periodos.forEach(p => {
                    const badge = p.estado === 'COMPLETO' ? 'success' : (p.estado === 'CERRADO' ? 'secondary' : 'warning');
                    const div = document.createElement('div');
                    div.className = 'card periodo-chip p-2';
                    div.style.minWidth = '160px';
                    div.innerHTML = `<div class="fw-bold text-primary">${p.etiqueta}</div>
                        <div class="small">Saldo: <strong>${p.saldo}</strong>/${p.dias_asignados}</div>
                        <span class="badge bg-${badge}">${p.estado}</span>`;
                    div.onclick = () => seleccionarPeriodo(p, div);
                    cont.appendChild(div);
                });
                cargarCreable();
            });
    }

    function cargarCreable() {
        fetch(`${API}?action=periodos_programables&id_conductor=${conductorId}`)
            .then(r => r.json())
            .then(d => {
                const z = $('#zonaCrear');
                if (d.puede_crear) {
                    z.innerHTML = `<button class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-plus-square"></i> Generar periodo ${d.puede_crear.etiqueta}</button>`;
                    z.querySelector('button').onclick = () => generarPeriodo(d.puede_crear.id_periodo_cat);
                } else {
                    z.innerHTML = `<span class="text-muted small"><i class="bi bi-lock"></i> ${d.motivo||''}</span>`;
                }
            });
    }

    function generarPeriodo(idCat) {
        const fd = new FormData();
        fd.append('id_conductor', conductorId);
        fd.append('id_periodo_cat', idCat);
        fetch(`${API}?action=generar_periodo`, {method:'POST', body:fd})
            .then(r => r.json())
            .then(d => {
                if (d.success) { alerta('success', d.message); cargarPeriodos(); }
                else alerta('danger', d.error);
            });
    }

    function seleccionarPeriodo(p, div) {
        periodoId = p.id_periodo;
        document.querySelectorAll('.periodo-chip').forEach(c => c.classList.remove('activo'));
        div.classList.add('activo');
        $('#etiqPeriodo').textContent = p.etiqueta;
        $('#lnkHistorial').href = `vacaciones_detalle.php`;
        setModo('agregar');
        cargarTramos();
    }

    /* ---- Paso 3: tramos ---- */
    function cargarTramos() {
        $('#cardTramos').classList.remove('d-none');
        fetch(`${API}?action=obtener_tramos&id_periodo=${periodoId}`)
            .then(r => r.json())
            .then(d => {
                tramosActivos = d.tramos || [];
                const tb = $('#tbodyTramos');
                tb.innerHTML = '';
                tramosActivos.forEach((t, i) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td>${i+1}</td><td>${t.fecha_inicio}</td><td>${t.fecha_fin}</td>
                        <td class="text-center">${t.dias}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm" title="Anular tramo">
                                <i class="bi bi-x-circle"></i></button></td>`;
                    tr.querySelector('button').onclick = () => anularTramo(t.id_tramo, t);
                    tb.appendChild(tr);
                });
                if (!tramosActivos.length) tb.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Sin tramos aún</td></tr>';
                $('#kAsig').textContent = d.saldo.dias_asignados;
                $('#kUsa').textContent = d.saldo.usados;
                $('#kSaldo').textContent = d.saldo.saldo;
                $('#saldoTxt').textContent = d.saldo.saldo + ' día(s)';
                pintarEditor();
            });
    }

    // Repinta el editor de tramos según el modo actual.
    function pintarEditor() {
        $('#nuevosTramos').innerHTML = '';
        if (modo === 'reprogramar' && tramosActivos.length) {
            tramosActivos.forEach(t => addFila(t.fecha_inicio, t.fecha_fin));
        } else {
            addFila();
        }
        recalc();
    }

    function setModo(nuevo) {
        modo = nuevo;
        const esRepro = (modo === 'reprogramar');
        $('#btnModoAgregar').classList.toggle('active', !esRepro);
        $('#btnModoReprogramar').classList.toggle('active', esRepro);
        $('#avisoModo').classList.toggle('d-none', !esRepro);
        $('#tituloEditor').textContent = esRepro ? 'Reprogramar (editar todos los tramos)' : 'Agregar nuevos tramos';
        $('#lblTotal').textContent = esRepro ? 'totales' : 'a agregar';
        $('#btnGuardar').innerHTML = esRepro
            ? '<i class="bi bi-arrow-repeat"></i> Aplicar reprogramación'
            : '<i class="bi bi-save"></i> Guardar programación';
        $('#btnGuardar').className = esRepro ? 'btn btn-warning' : 'btn btn-primary';
    }

    $('#btnModoAgregar').onclick = () => { if (modo !== 'agregar') { setModo('agregar'); pintarEditor(); } };
    $('#btnModoReprogramar').onclick = () => {
        if (!tramosActivos.length) { alerta('info', 'No hay tramos activos para reprogramar; use "Agregar".'); return; }
        if (modo !== 'reprogramar') { setModo('reprogramar'); pintarEditor(); }
    };

    function addFila(ini = '', fin = '') {
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-end mb-2 fila-tramo';
        row.innerHTML = `
            <div class="col-auto"><label class="form-label small mb-0">Inicio</label>
                <input type="date" class="form-control form-control-sm f-ini" value="${ini}"></div>
            <div class="col-auto"><label class="form-label small mb-0">Fin</label>
                <input type="date" class="form-control form-control-sm f-fin" value="${fin}"></div>
            <div class="col-auto"><span class="badge bg-info f-dias">0 días</span></div>
            <div class="col-auto"><button type="button" class="btn btn-outline-danger btn-sm f-del"><i class="bi bi-trash"></i></button></div>`;
        $('#nuevosTramos').appendChild(row);
        row.querySelectorAll('input').forEach(inp => inp.addEventListener('change', recalc));
        row.querySelector('.f-del').onclick = () => { row.remove(); recalc(); };
    }
    $('#btnAddFila').onclick = () => addFila();

    function anularTramo(idTramo, t) {
        if (!confirm(`¿Anular el tramo ${t.fecha_inicio} → ${t.fecha_fin} (${t.dias} días)? Se liberarán los días.`)) return;
        const fd = new FormData();
        fd.append('id_tramo', idTramo);
        fetch(`${API}?action=anular_tramo`, {method:'POST', body:fd})
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    alerta('success', d.message);
                    cargarTramos();
                    cargarPeriodos();
                } else {
                    alerta('danger', d.error);
                }
            });
    }

    function recalc() {
        let total = 0;
        document.querySelectorAll('.fila-tramo').forEach(r => {
            const fi = r.querySelector('.f-ini').value, ff = r.querySelector('.f-fin').value;
            const d = diasEntre(fi, ff);
            r.querySelector('.f-dias').textContent = d + ' días';
            total += d;
        });
        $('#totalNuevo').textContent = total;
    }

    $('#btnGuardar').onclick = () => {
        const tramos = [];
        let valido = true;
        document.querySelectorAll('.fila-tramo').forEach(r => {
            const fi = r.querySelector('.f-ini').value, ff = r.querySelector('.f-fin').value;
            if (fi && ff) tramos.push({fecha_inicio:fi, fecha_fin:ff});
            else if (fi || ff) valido = false;
        });
        if (!valido) { alerta('warning', 'Hay tramos con fecha incompleta.'); return; }
        if (!tramos.length) { alerta('warning', 'Agregue al menos un tramo con fechas.'); return; }

        const accion = (modo === 'reprogramar') ? 'reprogramar' : 'guardar_programacion';
        const fd = new FormData();
        fd.append('id_periodo', periodoId);
        fd.append('tramos', JSON.stringify(tramos));
        fetch(`${API}?action=${accion}`, {method:'POST', body:fd})
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    const okMsg = (modo === 'reprogramar') ? 'Reprogramación aplicada.' : '¡Periodo completo! 30 días programados.';
                    alerta(d.aviso ? 'warning' : 'success', d.aviso || okMsg);
                    setModo('agregar');
                    cargarTramos();
                    cargarPeriodos();
                } else {
                    alerta('danger', d.error);
                }
            });
    };
    </script>
</body>
</html>
