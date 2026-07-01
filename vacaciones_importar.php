<?php
/* =====================================================================
 *  vacaciones_importar.php — Carga masiva de programación desde Excel
 *  Fase 6. Sube .xlsx/.csv o pega la tabla; previsualiza y confirma.
 *  Formato: APELLIDOS Y NOMBRES | CARGO | REGIMEN | PERIODO |
 *           FECHA DE INICIO | FECHA DE FIN | TOTAL VAC.
 *  Ver DOC_MODULO_VACACIONES.md
 * ===================================================================== */
require_once 'include/cabecera.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar Vacaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .header-vaca { background-color: #073A6B; }
        .card-header { background-color: #073A6B; color: #fff; font-weight: bold; }
        .btn-primary { background-color: #073A6B; border-color: #073A6B; }
        .btn-primary:hover { background-color: #052849; border-color: #052849; }
        .text-primary { color: #073A6B !important; }
        .row-ok    td { }
        .row-warn  { background: #fff3cd !important; }
        .row-error { background: #f8d7da !important; }
        .row-dup   { background: #e2e3e5 !important; color:#6c757d; }
        textarea.pegar { font-family: monospace; font-size: .8rem; }
        .mono { font-family: monospace; }
    </style>
</head>
<body>
    <header class="header-vaca text-white p-3 mb-3">
        <div class="container-fluid d-flex align-items-center">
            <i class="bi bi-file-earmark-arrow-up fs-3 me-2"></i>
            <h1 class="h4 mb-0">Importar Programación de Vacaciones</h1>
        </div>
    </header>

    <div class="container-fluid">
        <div id="alerta" class="alert d-none" role="alert"></div>

        <div class="card shadow-sm mb-3">
            <div class="card-header"><i class="bi bi-upload"></i> 1. Origen de datos</div>
            <div class="card-body">
                <div class="alert alert-light border small mb-3">
                    <strong>Columnas esperadas (en este orden):</strong>
                    <span class="mono">APELLIDOS Y NOMBRES · CARGO · REGIMEN · PERIODO · FECHA DE INICIO · FECHA DE FIN · TOTAL VAC.</span><br>
                    El conductor se busca por <em>APELLIDOS Y NOMBRES</em> y el periodo por su etiqueta
                    (p. ej. <span class="mono">2025-2026</span>). Fechas <span class="mono">dd/mm/aaaa</span>.
                    Si una fila supera el tope de flota se importa igual, con advertencia.<br>
                    <strong>¿Chofer que no está en la base (tercero)?</strong> Aparecerá como
                    <span class="badge bg-info text-dark">Nuevo tercero</span>: marca la casilla <em>crear</em> y se
                    registrará automáticamente al confirmar (régimen tomado del Excel).
                </div>

                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-bold"><i class="bi bi-filetype-xlsx"></i> Opción A — Subir archivo</label>
                        <input type="file" id="archivo" class="form-control" accept=".xlsx,.xls,.csv,.txt">
                        <div class="form-text">Formatos: .xlsx, .xls, .csv</div>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label fw-bold"><i class="bi bi-clipboard"></i> Opción B — Pegar tabla (copiada de Excel)</label>
                        <textarea id="pegado" class="form-control pegar" rows="5"
                                  placeholder="Pega aquí las filas copiadas desde Excel (una fila por línea)..."></textarea>
                    </div>
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary" id="btnPrever"><i class="bi bi-eye"></i> Previsualizar</button>
                    <span class="text-muted small ms-2">Nada se guarda hasta que confirmes.</span>
                </div>
            </div>
        </div>

        <!-- Previsualización -->
        <div class="card shadow-sm mb-4 d-none" id="cardPrev">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-table"></i> 2. Previsualización</span>
                <span id="resumen"></span>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height:460px;overflow:auto;">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead class="table-light" style="position:sticky;top:0;">
                            <tr>
                                <th>#</th><th>Estado</th><th>Apellidos y Nombres</th><th>Régimen</th>
                                <th>Periodo</th><th class="text-center">Inicio</th><th class="text-center">Fin</th>
                                <th class="text-center">Días</th><th>Observación</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyPrev"></tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <div class="small text-muted">Se importan las filas <span class="badge bg-success">OK</span>,
                        <span class="badge bg-warning text-dark">Advertencia</span> y las
                        <span class="badge bg-info text-dark">Nuevo tercero</span> marcadas. Las
                        <span class="badge bg-danger">Error</span> y <span class="badge bg-secondary">Duplicadas</span> se omiten.</div>
                    <button class="btn btn-success" id="btnConfirmar" disabled>
                        <i class="bi bi-check2-circle"></i> Confirmar importación (<span id="nImport">0</span>)
                    </button>
                </div>
            </div>
        </div>

        <!-- Resultado -->
        <div class="card shadow-sm mb-4 d-none" id="cardResult">
            <div class="card-header"><i class="bi bi-clipboard-check"></i> 3. Resultado</div>
            <div class="card-body">
                <div id="resultMsg"></div>
                <ul id="resultOmitidos" class="small text-muted mt-2"></ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const API = 'vacaciones_controller.php';
    let filasPrev = [];

    const $ = s => document.querySelector(s);
    function alerta(tipo, txt) {
        const a = $('#alerta');
        a.className = 'alert alert-' + tipo;
        a.innerHTML = txt;
        a.classList.remove('d-none');
        a.scrollIntoView({behavior:'smooth', block:'nearest'});
    }
    function fmt(f) {
        if (!f) return '-';
        const p = ('' + f).substr(0,10).split('-');
        return p.length === 3 ? `${p[2]}/${p[1]}/${p[0]}` : f;
    }
    const BADGE = {
        ok:    '<span class="badge bg-success">OK</span>',
        warn:  '<span class="badge bg-warning text-dark">Advertencia</span>',
        error: '<span class="badge bg-danger">Error</span>',
        dup:   '<span class="badge bg-secondary">Duplicada</span>',
        nuevo: '<span class="badge bg-info text-dark">Nuevo tercero</span>'
    };
    function recomputarImportables() {
        let n = 0;
        filasPrev.forEach((f, i) => {
            if (f.estado === 'ok' || f.estado === 'warn') n++;
            else if (f.estado === 'nuevo') {
                const chk = document.querySelector(`.chk-nuevo[data-i="${i}"]`);
                if (chk && chk.checked) n++;
            }
        });
        document.getElementById('nImport').textContent = n;
        document.getElementById('btnConfirmar').disabled = (n === 0);
    }

    $('#btnPrever').addEventListener('click', () => {
        const file = $('#archivo').files[0];
        const pegado = $('#pegado').value.trim();
        if (!file && !pegado) { alerta('warning', 'Sube un archivo o pega la tabla.'); return; }

        const fd = new FormData();
        if (file) fd.append('archivo', file);
        else fd.append('pegado', pegado);

        const btn = $('#btnPrever');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';

        fetch(`${API}?action=importar_previsualizar`, {method:'POST', body:fd})
            .then(r => r.json())
            .then(d => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-eye"></i> Previsualizar';
                if (!d.success) { alerta('danger', d.error); return; }
                $('#alerta').classList.add('d-none');
                pintarPrevisualizacion(d);
            })
            .catch(e => { btn.disabled = false; btn.innerHTML = '<i class="bi bi-eye"></i> Previsualizar'; alerta('danger', 'Error de red: ' + e.message); });
    });

    function pintarPrevisualizacion(d) {
        filasPrev = d.filas || [];
        $('#cardPrev').classList.remove('d-none');
        $('#cardResult').classList.add('d-none');
        const tb = $('#tbodyPrev');
        tb.innerHTML = '';
        filasPrev.forEach((f, i) => {
            const tr = document.createElement('tr');
            tr.className = 'row-' + f.estado;
            let celdaEstado = BADGE[f.estado] || f.estado;
            if (f.estado === 'nuevo') {
                celdaEstado += `<br><label class="small"><input type="checkbox" class="chk-nuevo" data-i="${i}" checked> crear</label>`;
            }
            tr.innerHTML = `<td>${i+1}</td><td>${celdaEstado}</td>
                <td>${f.nombre||''}</td><td>${f.regimen||''}</td><td>${f.etiqueta||''}</td>
                <td class="text-center">${fmt(f.fi)}</td><td class="text-center">${fmt(f.ff)}</td>
                <td class="text-center">${f.dias||''}</td><td class="small">${f.motivo||''}</td>`;
            tb.appendChild(tr);
        });
        document.querySelectorAll('.chk-nuevo').forEach(c => c.addEventListener('change', recomputarImportables));
        const r = d.resumen;
        $('#resumen').innerHTML =
            `<span class="badge bg-success">OK ${r.ok}</span>
             <span class="badge bg-warning text-dark">Adv ${r.warn}</span>
             <span class="badge bg-info text-dark">Nuevo ${r.nuevo||0}</span>
             <span class="badge bg-danger">Error ${r.error}</span>
             <span class="badge bg-secondary">Dup ${r.dup}</span>
             <span class="badge bg-light text-dark">Tope ${d.tope}</span>`;
        recomputarImportables();
    }

    $('#btnConfirmar').addEventListener('click', () => {
        const aImportar = [];
        filasPrev.forEach((f, i) => {
            if (f.estado === 'ok' || f.estado === 'warn') {
                aImportar.push({id_conductor:f.id_conductor, id_periodo_cat:f.id_periodo_cat,
                                etiqueta:f.etiqueta, fi:f.fi, ff:f.ff, dias:f.dias, nombre:f.nombre});
            } else if (f.estado === 'nuevo') {
                const chk = document.querySelector(`.chk-nuevo[data-i="${i}"]`);
                if (chk && chk.checked) {
                    aImportar.push({id_conductor:null, crear_tercero:true,
                                    appat:f.appat, apmat:f.apmat, nombres:f.nombres, regimen:f.regimen,
                                    id_periodo_cat:f.id_periodo_cat, etiqueta:f.etiqueta,
                                    fi:f.fi, ff:f.ff, dias:f.dias, nombre:f.nombre});
                }
            }
        });
        if (!aImportar.length) { alerta('warning', 'No hay filas importables.'); return; }
        if (!confirm(`¿Confirmar la importación de ${aImportar.length} tramo(s)?`)) return;

        const fd = new FormData();
        fd.append('filas', JSON.stringify(aImportar));
        const btn = $('#btnConfirmar');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Importando...';

        fetch(`${API}?action=importar_confirmar`, {method:'POST', body:fd})
            .then(r => r.json())
            .then(d => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check2-circle"></i> Confirmar importación (<span id="nImport">' + aImportar.length + '</span>)';
                $('#cardResult').classList.remove('d-none');
                if (!d.success) {
                    $('#resultMsg').innerHTML = '<div class="alert alert-danger mb-0">' + d.error + '</div>';
                    return;
                }
                $('#resultMsg').innerHTML = '<div class="alert alert-success mb-0">' + d.message + '</div>';
                const ul = $('#resultOmitidos');
                ul.innerHTML = '';
                (d.omitidos || []).forEach(o => { const li = document.createElement('li'); li.textContent = o; ul.appendChild(li); });
                $('#btnConfirmar').disabled = true;
                $('#cardResult').scrollIntoView({behavior:'smooth', block:'nearest'});
            })
            .catch(e => { btn.disabled = false; alerta('danger', 'Error de red: ' + e.message); });
    });
    </script>
</body>
</html>
