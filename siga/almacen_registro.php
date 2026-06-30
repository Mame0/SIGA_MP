<?php
require_once 'include/cabecera.php';

// Obtener el almacén seleccionado de la URL
$almacenId = isset($_GET['almacen']) ? (int)$_GET['almacen'] : 1;

// Obtener lista de almacenes activos
$almacenes = $Db->query("SELECT id_almacen, nomb_almacen FROM mp_almacen_locales WHERE esta_almacen = 1 ORDER BY id_almacen ASC");

// Determinar si estamos en el flujo de Crear Nuevo Artículo (Bien) en el catálogo
$modoNuevoBien = isset($_GET['nuevo_bien']) && $_GET['nuevo_bien'] == 1;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $modoNuevoBien ? 'Crear Artículo' : 'Registrar Movimiento' ?> - Almacén</title>
    <link rel="stylesheet" href="libmenu/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="libmenu/fontawesome-free/css/v4-shims.min.css" />
    <link rel="stylesheet" href="css/almacen.css" />
</head>
<body>
    <div class="page-header">
        <div class="page-title">
            <a href="almacen_listado.php?almacen=<?=$almacenId?>" class="btn-secondary" style="padding: 8px 12px; border-radius: 10px; text-decoration: none;" title="Volver al Listado">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
            <h1><?= $modoNuevoBien ? 'Crear Nuevo Artículo' : 'Registrar Movimiento de Almacén' ?></h1>
        </div>
    </div>

    <!-- Mensaje Toast de Éxito o Error -->
    <div id="toast" class="toast-msg"></div>

    <?php if ($modoNuevoBien): ?>
        <!-- FORMULARIO CREAR BIEN EN CATÁLOGO -->
        <div class="form-card">
            <h2 style="color: var(--primary-color); font-size: 16px; margin-bottom: 20px; font-weight: 700; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
                <i class="fas fa-cube"></i> Datos del Nuevo Artículo en Catálogo
            </h2>
            
            <form id="form-nuevo-bien" onsubmit="guardarNuevoBien(event)">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="codi_bien">Código de Artículo (Opcional)</label>
                        <input type="text" id="codi_bien" name="codi_bien" class="form-control" placeholder="Ej. A-0245, 45201402" />
                    </div>

                    <div class="form-group">
                        <label for="unid_bien">Unidad de Medida (*)</label>
                        <select id="unid_bien" name="unid_bien" class="form-select" required>
                            <option value="UNIDAD">UNIDAD</option>
                            <option value="GALÓN">GALÓN</option>
                            <option value="CAJA">CAJA</option>
                            <option value="PAQUETE">PAQUETE</option>
                            <option value="ROLLO">ROLLO</option>
                            <option value="METRO">METRO</option>
                            <option value="MILLAR">MILLAR</option>
                            <option value="BOLSA">BOLSA</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="cate_bien">Categoría del Artículo (*)</label>
                        <select id="cate_bien" name="cate_bien" class="form-select" required>
                            <option value="Ferreteria">Ferretería</option>
                            <option value="Herramienta">Herramienta</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="desc_bien">Descripción del Artículo (*)</label>
                        <input type="text" id="desc_bien" name="desc_bien" class="form-control" placeholder="Ej. ACEITE DE MOTOR SAE 10W-30 GRADO MULTIGRAD" required />
                    </div>

                    <div class="form-group full-width">
                        <label for="marc_bien">Marca del Artículo (Opcional)</label>
                        <input type="text" id="marc_bien" name="marc_bien" class="form-control" placeholder="Ej. TOYOTA, 3M, TRAMONTINA" />
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="window.location.href='almacen_listado.php?almacen=<?=$almacenId?>'">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Guardar Artículo
                    </button>
                </div>
            </form>
        </div>

    <?php else: ?>
        <!-- FORMULARIO REGISTRAR MOVIMIENTO (INGRESO / SALIDA) -->
        <div class="form-card">
            <!-- Pestañas de Tipo de Movimiento -->
            <div class="type-tabs">
                <div class="type-tab active ingreso" onclick="setTipoMov('INGRESO')">
                    <i class="fas fa-arrow-down"></i> INGRESO (NEA / Compra)
                </div>
                <div class="type-tab salida" onclick="setTipoMov('SALIDA')">
                    <i class="fas fa-arrow-up"></i> SALIDA (Guía de Salida / Despacho)
                </div>
            </div>

            <form id="form-movimiento" onsubmit="guardarMovimiento(event)">
                <!-- Campos ocultos para tipo de movimiento e id del bien -->
                <input type="hidden" id="tipo_mov" name="tipo_mov" value="INGRESO" />
                <input type="hidden" id="id_bien" name="id_bien" required />

                <div class="form-grid">
                    <div class="form-group">
                        <label for="id_almacen">Almacén de Operación (*)</label>
                        <select id="id_almacen" name="id_almacen" class="form-select" onchange="actualizarInfoBien()" required>
                            <?php foreach ($almacenes as $alm): ?>
                                <option value="<?=$alm['id_almacen']?>" <?=$alm['id_almacen'] == $almacenId ? 'selected' : ''?>>
                                    <?=htmlspecialchars($alm['nomb_almacen'])?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group autocomplete-container">
                        <label for="buscar_articulo">Buscar Artículo (*)</label>
                        <input type="text" id="buscar_articulo" class="form-control" placeholder="Escriba descripción o código..." oninput="buscarBienes(this.value)" autocomplete="off" required />
                        <ul id="sugerencias" class="suggestions-list" style="display: none;"></ul>
                    </div>

                    <div class="form-group">
                        <label id="label-doc" for="doc_mov">Nro. Documento de Referencia (*)</label>
                        <input type="text" id="doc_mov" name="doc_mov" class="form-control" placeholder="Ej. NEA: 154, O/C: 450, Guía de Salida: 120" required />
                    </div>

                    <div class="form-group">
                        <label for="fech_mov">Fecha del Movimiento (*)</label>
                        <input type="date" id="fech_mov" name="fech_mov" class="form-control" value="<?=date('Y-m-d')?>" required />
                    </div>

                    <div class="form-group">
                        <label for="cant_mov">Cantidad (*)</label>
                        <input type="number" id="cant_mov" name="cant_mov" class="form-control" min="1" step="1" placeholder="Ej. 10" oninput="calcularTotalOperacion()" required />
                        <span id="stock-ayuda" style="font-size: 11.5px; font-weight: 600; color: var(--text-muted); margin-top: 4px;"></span>
                    </div>

                    <div class="form-group">
                        <label for="pu_mov">Precio Unitario (S/.) (*)</label>
                        <input type="number" id="pu_mov" name="pu_mov" class="form-control" min="0.0001" step="0.0001" placeholder="0.0000" oninput="calcularTotalOperacion()" required />
                        <span id="pu-ayuda" style="font-size: 11.5px; font-weight: 600; color: var(--text-muted); margin-top: 4px;"></span>
                    </div>

                    <div class="form-group" id="grupo-cadu">
                        <label for="fech_cadu">Fecha de Caducidad (Opcional)</label>
                        <input type="date" id="fech_cadu" name="fech_cadu" class="form-control" />
                    </div>

                    <div class="form-group">
                        <label>Total de Operación (S/.)</label>
                        <input type="text" id="total_operacion" class="form-control" value="S/. 0.00" disabled />
                    </div>

                    <div class="form-group full-width">
                        <label for="obse_mov">Observaciones / Comentarios</label>
                        <textarea id="obse_mov" name="obse_mov" class="form-control" rows="3" placeholder="Detalles adicionales sobre el ingreso o despacho..."></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="window.location.href='almacen_listado.php?almacen=<?=$almacenId?>'">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-exchange-alt"></i> Registrar Movimiento
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <script>
        // Variables globales
        let itemSeleccionado = null;

        // Mostrar notificaciones Toast
        function showToast(message, isSuccess = true) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.style.backgroundColor = isSuccess ? 'var(--color-success)' : 'var(--color-danger)';
            toast.style.display = 'block';
            
            setTimeout(() => {
                toast.style.display = 'none';
            }, 4000);
        }

        // --- FUNCIONES PARA MODO CREAR BIEN ---
        function guardarNuevoBien(e) {
            e.preventDefault();
            const form = document.getElementById('form-nuevo-bien');
            const data = new FormData(form);

            fetch('almacen_controller.php?action=registrar_bien', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    showToast(res.message, true);
                    setTimeout(() => {
                        window.location.href = 'almacen_listado.php?almacen=<?=$almacenId?>';
                    }, 1200);
                } else {
                    showToast('Error: ' + res.error, false);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Ocurrió un error al procesar la solicitud.', false);
            });
        }

        // --- FUNCIONES PARA REGISTRO DE MOVIMIENTOS ---
        function setTipoMov(tipo) {
            document.getElementById('tipo_mov').value = tipo;
            
            const tabs = document.querySelectorAll('.type-tabs .type-tab');
            tabs.forEach(t => t.classList.remove('active'));
            
            if (tipo === 'INGRESO') {
                document.querySelector('.type-tab.ingreso').classList.add('active');
                document.getElementById('grupo-cadu').style.display = '';
                document.getElementById('pu_mov').disabled = false;
                document.getElementById('pu_mov').value = '';
                document.getElementById('pu-ayuda').textContent = '';
                document.getElementById('label-doc').textContent = 'Nro. Documento de Referencia (NEA / O/C) (*)';
                document.getElementById('doc_mov').placeholder = 'Ej. NEA: 104, O/C: 450';
            } else {
                document.querySelector('.type-tab.salida').classList.add('active');
                document.getElementById('grupo-cadu').style.display = 'none';
                document.getElementById('fech_cadu').value = '';
                document.getElementById('label-doc').textContent = 'Nro. Guía de Salida (*)';
                document.getElementById('doc_mov').placeholder = 'Ej. Guía: 074';
                
                // Si hay un bien seleccionado, bloquear e inyectar el P.U.P
                if (itemSeleccionado) {
                    const puActual = parseFloat(itemSeleccionado.pu_actual) || 0;
                    document.getElementById('pu_mov').value = puActual.toFixed(4);
                    document.getElementById('pu_mov').disabled = true;
                    document.getElementById('pu-ayuda').textContent = 'P.U.P. vigente aplicado automáticamente.';
                } else {
                    document.getElementById('pu_mov').value = '';
                    document.getElementById('pu_mov').disabled = true;
                    document.getElementById('pu-ayuda').textContent = 'Seleccione un artículo para aplicar su P.U.P.';
                }
            }
            calcularTotalOperacion();
        }

        function buscarBienes(query) {
            const listado = document.getElementById('sugerencias');
            if (query.trim().length < 2) {
                listado.style.display = 'none';
                return;
            }

            fetch('almacen_controller.php?action=buscar_bienes&q=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                if (data.length === 0) {
                    listado.innerHTML = '<li class="suggestion-item" style="color:var(--text-muted);">No se encontraron coincidencias</li>';
                    listado.style.display = 'block';
                    return;
                }

                listado.innerHTML = '';
                data.forEach(item => {
                    const li = document.createElement('li');
                    li.className = 'suggestion-item';
                    li.innerHTML = `<strong>[${item.codi_bien || 'S/C'}]</strong> ${item.desc_bien} <span style="font-size:11px;color:var(--text-muted)">(${item.marc_bien || 'Genérico'})</span>`;
                    li.onclick = () => seleccionarBien(item);
                    listado.appendChild(li);
                });
                listado.style.display = 'block';
            })
            .catch(err => console.error('Error autocomplete:', err));
        }

        function seleccionarBien(item) {
            document.getElementById('buscar_articulo').value = item.desc_bien;
            document.getElementById('id_bien').value = item.id_bien;
            document.getElementById('sugerencias').style.display = 'none';
            
            actualizarInfoBien();
        }

        function actualizarInfoBien() {
            const idBien = document.getElementById('id_bien').value;
            const idAlmacen = document.getElementById('id_almacen').value;
            
            if (!idBien) return;

            fetch(`almacen_controller.php?action=obtener_bien&id_bien=${idBien}&id_almacen=${idAlmacen}`)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    itemSeleccionado = res.bien;
                    const stock = parseInt(res.bien.stock_actual) || 0;
                    const pu = parseFloat(res.bien.pu_actual) || 0;
                    
                    document.getElementById('stock-ayuda').textContent = `Stock actual disponible en este almacén: ${stock} unidades.`;
                    
                    // Si estamos en modo SALIDA, actualizar y bloquear P.U
                    const tipo = document.getElementById('tipo_mov').value;
                    if (tipo === 'SALIDA') {
                        document.getElementById('pu_mov').value = pu.toFixed(4);
                        document.getElementById('pu_mov').disabled = true;
                        document.getElementById('pu-ayuda').textContent = 'P.U.P. vigente aplicado automáticamente.';
                    } else {
                        // En ingreso no bloqueamos, pero mostramos el P.U.P anterior de referencia si lo desean
                        if (pu > 0) {
                            document.getElementById('pu-ayuda').textContent = `P.U.P de referencia: S/. ${pu.toFixed(4)}`;
                        } else {
                            document.getElementById('pu-ayuda').textContent = '';
                        }
                    }
                    calcularTotalOperacion();
                }
            })
            .catch(err => console.error('Error fetching bien info:', err));
        }

        function calcularTotalOperacion() {
            const cant = parseInt(document.getElementById('cant_mov').value) || 0;
            const pu = parseFloat(document.getElementById('pu_mov').value) || 0;
            const total = cant * pu;
            
            document.getElementById('total_operacion').value = `S/. ${total.toFixed(2)}`;
        }

        function guardarMovimiento(e) {
            e.preventDefault();
            const idBien = document.getElementById('id_bien').value;
            if (!idBien) {
                showToast('Por favor, busque y seleccione un artículo válido.', false);
                return;
            }

            const tipo = document.getElementById('tipo_mov').value;
            const cant = parseInt(document.getElementById('cant_mov').value) || 0;
            const stockActual = itemSeleccionado ? parseInt(itemSeleccionado.stock_actual) || 0 : 0;

            // Validación de stock para salidas
            if (tipo === 'SALIDA' && cant > stockActual) {
                showToast(`Error: Stock insuficiente. Solo dispone de ${stockActual} unidades.`, false);
                return;
            }

            const form = document.getElementById('form-movimiento');
            const data = new FormData(form);
            
            // Si el campo precio unitario está deshabilitado en POST, no se envía.
            // Lo habilitamos temporalmente antes del envío o lo añadimos manualmente
            if (document.getElementById('pu_mov').disabled) {
                data.append('pu_mov', document.getElementById('pu_mov').value);
            }

            fetch('almacen_controller.php?action=registrar_movimiento', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    showToast(res.message, true);
                    setTimeout(() => {
                        window.location.href = 'almacen_listado.php?almacen=' + document.getElementById('id_almacen').value;
                    }, 1200);
                } else {
                    showToast('Error: ' + res.error, false);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Ocurrió un error al procesar el registro.', false);
            });
        }

        // Cerrar autocompletado si hace clic fuera
        document.addEventListener('click', (e) => {
            const list = document.getElementById('sugerencias');
            if (e.target.id !== 'buscar_articulo' && e.target.className !== 'suggestion-item') {
                if (list) list.style.display = 'none';
            }
        });

        // Cargar por defecto
        window.onload = () => {
            if (document.getElementById('tipo_mov')) {
                setTipoMov('INGRESO');
            }
        };

        function ajustarAltura() {
            if (window.parent && window.parent.document.getElementById('body_iframe')) {
                window.parent.document.getElementById('body_iframe').height = document.body.scrollHeight + 50;
            }
        }
        window.addEventListener('load', ajustarAltura);
        window.addEventListener('resize', ajustarAltura);
    </script>
</body>
</html>
