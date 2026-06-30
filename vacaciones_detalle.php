<?php
/* =====================================================================
 *  vacaciones_detalle.php — Historial de cambios de un conductor/periodo
 *  Fase 4. Ver DOC_MODULO_VACACIONES.md
 * ===================================================================== */
require_once 'include/cabecera.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Vacaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .header-vaca { background-color: #073A6B; }
        .card-header { background-color: #073A6B; color: #fff; font-weight: bold; }
        .text-primary { color: #073A6B !important; }
        .list-conductor { max-height: 240px; overflow-y: auto; }
        .periodo-chip { cursor: pointer; }
        .periodo-chip.activo { outline: 3px solid #073A6B; }
        .timeline { position: relative; padding-left: 1.5rem; }
        .timeline .item { position: relative; padding-bottom: 1rem; border-left: 2px solid #dee2e6; padding-left: 1rem; }
        .timeline .item:last-child { border-left-color: transparent; }
        .timeline .dot { position: absolute; left: -7px; top: 2px; width: 12px; height: 12px; border-radius: 50%; }
        .tramo-pill { font-size: .8rem; }
    </style>
</head>
<body>
    <header class="header-vaca text-white p-3 mb-3">
        <div class="container-fluid d-flex align-items-center">
            <i class="bi bi-clock-history fs-3 me-2"></i>
            <h1 class="h4 mb-0">Historial de Vacaciones</h1>
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

        <!-- Paso 2: Periodos (filtro) -->
        <div class="card shadow-sm mb-3 d-none" id="cardPeriodos">
            <div class="card-header"><i class="bi bi-calendar3"></i> 2. Filtrar por periodo (opcional)</div>
            <div class="card-body">
                <div id="listaPeriodos" class="d-flex flex-wrap gap-2"></div>
            </div>
        </div>

        <!-- Paso 3: Historial -->
        <div class="card shadow-sm mb-4 d-none" id="cardHist">
            <div class="card-header"><i class="bi bi-list-ul"></i> 3. Movimientos</div>
            <div class="card-body">
                <div id="histVacio" class="text-center text-muted py-3 d-none">Sin movimientos registrados.</div>
                <div class="timeline" id="timeline"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const API = 'vacaciones_controller.php';
    let conductorId = null, periodoFiltro = null;

    const $ = s => document.querySelector(s);
    function alerta(tipo, txt) {
        const a = $('#alerta');
        a.className = 'alert alert-' + tipo;
        a.textContent = txt;
    }
    function fmt(f) {
        if (!f) return '-';
        const p = ('' + f).substr(0, 10).split('-');
        return p.length === 3 ? `${p[2]}/${p[1]}/${p[0]}` : f;
    }
    function fmtHora(f) {
        if (!f) return '';
        const s = ('' + f).replace('T', ' ');
        const [d, h] = s.split(' ');
        const p = d.split('-');
        return (p.length === 3 ? `${p[2]}/${p[1]}/${p[0]}` : d) + (h ? ' ' + h.substr(0, 5) : '');
    }

    const ACC = {
        CREA:       { color: '#198754', icon: 'plus-circle',   label: 'Creación' },
        REPROGRAMA: { color: '#fd7e14', icon: 'arrow-repeat',  label: 'Reprogramación' },
        ANULA:      { color: '#dc3545', icon: 'x-circle',      label: 'Anulación' }
    };

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
        periodoFiltro = null;
        $('#resultConductor').innerHTML = '';
        $('#buscaConductor').value = '';
        $('#conductorSel').innerHTML = `<i class="bi bi-check-circle-fill"></i> ${c.nombre_completo} (DNI ${c.ndoc})`;
        cargarPeriodos();
        cargarHistorial();
    }

    /* ---- Paso 2: periodos (filtro) ---- */
    function cargarPeriodos() {
        $('#cardPeriodos').classList.remove('d-none');
        fetch(`${API}?action=obtener_periodos&id_conductor=${conductorId}`)
            .then(r => r.json())
            .then(d => {
                const cont = $('#listaPeriodos');
                cont.innerHTML = '';
                const todos = document.createElement('div');
                todos.className = 'card periodo-chip p-2 activo';
                todos.style.minWidth = '120px';
                todos.innerHTML = `<div class="fw-bold text-primary">Todos</div><div class="small text-muted">los periodos</div>`;
                todos.onclick = () => seleccionarFiltro(null, todos);
                cont.appendChild(todos);
                (d.periodos || []).forEach(p => {
                    const badge = p.estado === 'COMPLETO' ? 'success' : (p.estado === 'CERRADO' ? 'secondary' : 'warning');
                    const div = document.createElement('div');
                    div.className = 'card periodo-chip p-2';
                    div.style.minWidth = '140px';
                    div.innerHTML = `<div class="fw-bold text-primary">${p.etiqueta}</div>
                        <div class="small">Saldo: <strong>${p.saldo}</strong>/${p.dias_asignados}</div>
                        <span class="badge bg-${badge}">${p.estado}</span>`;
                    div.onclick = () => seleccionarFiltro(p.id_periodo, div);
                    cont.appendChild(div);
                });
            });
    }

    function seleccionarFiltro(idPeriodo, div) {
        periodoFiltro = idPeriodo;
        document.querySelectorAll('.periodo-chip').forEach(c => c.classList.remove('activo'));
        div.classList.add('activo');
        cargarHistorial();
    }

    /* ---- Paso 3: historial ---- */
    function tramosHtml(lista, color) {
        if (!lista || !lista.length) return '<span class="text-muted small">—</span>';
        return lista.map(t =>
            `<span class="badge tramo-pill me-1 mb-1" style="background:${color}">${fmt(t.inicio)} → ${fmt(t.fin)} · ${t.dias}d</span>`
        ).join('');
    }

    function cargarHistorial() {
        $('#cardHist').classList.remove('d-none');
        let url = `${API}?action=obtener_historial&id_conductor=${conductorId}`;
        if (periodoFiltro) url += `&id_periodo=${periodoFiltro}`;
        fetch(url)
            .then(r => r.json())
            .then(d => {
                const tl = $('#timeline');
                tl.innerHTML = '';
                if (!d.success) { alerta('danger', d.error || 'Error al cargar el historial.'); return; }
                const hist = d.historial || [];
                $('#histVacio').classList.toggle('d-none', hist.length > 0);
                hist.forEach(h => {
                    const acc = ACC[h.accion] || { color: '#6c757d', icon: 'dot', label: h.accion };
                    const item = document.createElement('div');
                    item.className = 'item';
                    let cuerpo = '';
                    if (h.accion === 'CREA') {
                        cuerpo = `<div class="mt-1"><span class="text-muted small">Tramos programados:</span><br>${tramosHtml(h.detalle_despues, '#198754')}</div>`;
                    } else if (h.accion === 'REPROGRAMA') {
                        cuerpo = `<div class="mt-1"><span class="text-muted small">Antes:</span><br>${tramosHtml(h.detalle_antes, '#adb5bd')}</div>
                                  <div class="mt-1"><span class="text-muted small">Después:</span><br>${tramosHtml(h.detalle_despues, '#fd7e14')}</div>`;
                    } else if (h.accion === 'ANULA') {
                        cuerpo = `<div class="mt-1"><span class="text-muted small">Tramo anulado:</span><br>${tramosHtml(h.detalle_antes, '#dc3545')}</div>`;
                    }
                    item.innerHTML = `
                        <span class="dot" style="background:${acc.color}"></span>
                        <div class="d-flex justify-content-between flex-wrap">
                            <div class="fw-bold" style="color:${acc.color}">
                                <i class="bi bi-${acc.icon}"></i> ${acc.label}
                                <span class="badge bg-light text-dark ms-1">${h.etiqueta || ''}</span>
                            </div>
                            <div class="text-muted small">${fmtHora(h.fecha_hora)}${h.operador ? ' · ' + h.operador : ''}</div>
                        </div>
                        ${cuerpo}
                        <div class="small text-muted mt-1">
                            Liberados: <strong>${h.dias_liberados}</strong> ·
                            Consumidos: <strong>${h.dias_consumidos}</strong> ·
                            Saldo resultante: <strong>${h.saldo_resultante}</strong>
                        </div>`;
                    tl.appendChild(item);
                });
            })
            .catch(err => alerta('danger', 'Error de red: ' + err.message));
    }
    </script>
</body>
</html>
