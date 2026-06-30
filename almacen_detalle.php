<?php
require_once 'include/cabecera.php';

$bienId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$almacenId = isset($_GET['almacen']) ? (int)$_GET['almacen'] : 1;

if ($bienId <= 0) {
    echo "<h1>Error: Artículo no especificado</h1>";
    exit;
}

// Obtener detalles del almacén
$almacenInfo = $Db->query("SELECT nomb_almacen FROM mp_almacen_locales WHERE id_almacen = :almId", [':almId' => $almacenId]);
if (empty($almacenInfo)) {
    echo "<h1>Error: Almacén no encontrado</h1>";
    exit;
}
$nombAlmacen = $almacenInfo[0]['nomb_almacen'];

// Obtener detalles del bien e inventario actual
$bienInfo = $Db->query("
    SELECT b.*, COALESCE(i.stock_actual, 0) as stock_actual, COALESCE(i.pu_actual, 0.0000) as pu_actual, COALESCE(i.total_actual, 0.0000) as total_actual
    FROM mp_almacen_bienes b
    LEFT JOIN mp_almacen_inventario i ON b.id_bien = i.id_bien AND i.id_almacen = :almId
    WHERE b.id_bien = :bienId
", [':almId' => $almacenId, ':bienId' => $bienId]);

if (empty($bienInfo)) {
    echo "<h1>Error: Artículo no encontrado</h1>";
    exit;
}
$bien = $bienInfo[0];

// Obtener movimientos chronológicamente para calcular el saldo corriendo
$movimientos = $Db->query("
    SELECT id_mov, tipo_mov, fech_mov, doc_mov, cant_mov, pu_mov, total_mov, fech_cadu, obse_mov
    FROM mp_almacen_movimientos
    WHERE id_almacen = :almId AND id_bien = :bienId
    ORDER BY fech_mov ASC, id_mov ASC
", [':almId' => $almacenId, ':bienId' => $bienId]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kardex Valorizado - <?=htmlspecialchars($bien['desc_bien'])?></title>
    <link rel="stylesheet" href="libmenu/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="libmenu/fontawesome-free/css/v4-shims.min.css" />
    <link rel="stylesheet" href="css/almacen.css" />
</head>
<body>
    <div class="page-header">
        <div class="page-title">
            <a href="almacen_listado.php?almacen=<?=$almacenId?>" class="btn-secondary" style="padding: 8px 12px; border-radius: 10px; text-decoration: none;" title="Volver al Listado">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1>Ficha de Kardex Valorizado</h1>
        </div>
        <div>
            <span class="badge badge-success" style="font-size: 14px; padding: 8px 14px;">
                <i class="fas fa-home"></i> <?=htmlspecialchars($nombAlmacen)?>
            </span>
        </div>
    </div>

    <!-- Informacion del Bien -->
    <div class="item-info-card">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--border-color); padding-bottom: 10px; margin-bottom: 15px; gap: 12px; flex-wrap: wrap;">
            <h2 style="color: var(--primary-color); font-size: 18px; font-weight: 700; margin: 0;">
                [<?=htmlspecialchars($bien['codi_bien'] ?? 'SIN CÓDIGO')?>] - <?=htmlspecialchars($bien['desc_bien'])?>
            </h2>
            <button onclick="abrirModalEditarBien(<?=htmlspecialchars(json_encode($bien, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT))?>)" class="btn-secondary" style="padding: 6px 12px; font-size: 12.5px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; transition: background-color 0.2s;" title="Editar Datos del Artículo">
                <i class="fas fa-edit" style="color: var(--accent-color);"></i> Editar Artículo
            </button>
        </div>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Unidad de Medida</span>
                <span class="info-value"><?=htmlspecialchars($bien['unid_bien'])?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Marca</span>
                <span class="info-value"><?=htmlspecialchars($bien['marc_bien'] ?? '-')?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Categoría</span>
                <span class="info-value">
                    <span class="badge" style="background-color: <?=($bien['cate_bien'] === 'Herramienta' ? 'rgba(52, 152, 219, 0.15)' : 'rgba(155, 89, 182, 0.15)')?>; color: <?=($bien['cate_bien'] === 'Herramienta' ? '#3498db' : '#9b59b6')?>; border: 1px solid <?=($bien['cate_bien'] === 'Herramienta' ? '#3498db' : '#9b59b6')?>; border-radius: 6px; padding: 2px 6px; font-size: 11px; font-weight: 700; display: inline-flex; vertical-align: middle;">
                        <?=htmlspecialchars($bien['cate_bien'] === 'Herramienta' ? 'Herramienta' : 'Ferretería')?>
                    </span>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Stock Actual</span>
                <span class="info-value" style="color: var(--primary-color);">
                    <?=$bien['stock_actual']?>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">P.U.P Actual</span>
                <span class="info-value">
                    S/. <?=number_format((float)$bien['pu_actual'], 4, '.', '')?>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Valorización Total</span>
                <span class="info-value" style="color: var(--color-success);">
                    S/. <?=number_format((float)$bien['total_actual'], 2, '.', ',')?>
                </span>
            </div>
        </div>
    </div>

    <!-- Kardex Movimientos Ledger -->
    <div class="page-title" style="margin-bottom: 16px;">
        <i class="fas fa-history" style="color: var(--primary-color);"></i>
        <h2 style="font-size: 18px; font-weight: 600;">Historial de Transacciones (Control de Saldo)</h2>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 90px; text-align: center; border-right: 1px solid var(--border-color); vertical-align: middle;">Fecha</th>
                        <th rowspan="2" style="vertical-align: middle; border-right: 1px solid var(--border-color);">Documento / Operación</th>
                        <th colspan="3" style="text-align: center; background-color: #f0fdf4; border-bottom: 1px solid var(--border-color); border-right: 1px solid var(--border-color);">Entradas</th>
                        <th colspan="3" style="text-align: center; background-color: #fef2f2; border-bottom: 1px solid var(--border-color); border-right: 1px solid var(--border-color);">Salidas</th>
                        <th colspan="3" style="text-align: center; background-color: #eff6ff; border-bottom: 1px solid var(--border-color); border-right: 1px solid var(--border-color);">Saldos Valorizados</th>
                        <th rowspan="2" style="vertical-align: middle; border-right: 1px solid var(--border-color);">Observaciones</th>
                        <th rowspan="2" style="vertical-align: middle; text-align: center; width: 80px;">Acciones</th>
                    </tr>
                    <tr>
                        <!-- Entradas -->
                        <th style="width: 80px; text-align: right; background-color: #f0fdf4; font-size: 11px;">Cant.</th>
                        <th style="width: 100px; text-align: right; background-color: #f0fdf4; font-size: 11px;">P.U.</th>
                        <th style="width: 100px; text-align: right; background-color: #f0fdf4; border-right: 1px solid var(--border-color); font-size: 11px;">Total</th>
                        <!-- Salidas -->
                        <th style="width: 80px; text-align: right; background-color: #fef2f2; font-size: 11px;">Cant.</th>
                        <th style="width: 100px; text-align: right; background-color: #fef2f2; font-size: 11px;">P.U.</th>
                        <th style="width: 100px; text-align: right; background-color: #fef2f2; border-right: 1px solid var(--border-color); font-size: 11px;">Total</th>
                        <!-- Saldos -->
                        <th style="width: 80px; text-align: right; background-color: #eff6ff; font-size: 11px;">Cant.</th>
                        <th style="width: 100px; text-align: right; background-color: #eff6ff; font-size: 11px;">P.U.P.</th>
                        <th style="width: 100px; text-align: right; background-color: #eff6ff; border-right: 1px solid var(--border-color); font-size: 11px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($movimientos)): ?>
                        <tr>
                            <td colspan="13" style="text-align: center; color: var(--text-muted); padding: 30px;">
                                No se registran movimientos para este artículo en el almacén seleccionado.
                            </td>
                        </tr>
                    <?php else: 
                        $runningStock = 0;
                        $runningTotal = 0.0000;
                        $runningPUP = 0.0000;
                        
                        foreach ($movimientos as $mov): 
                            $cant = (int)$mov['cant_mov'];
                            $pu = (float)$mov['pu_mov'];
                            $total = (float)$mov['total_mov'];
                            
                            $ingCant = '-';
                            $ingPU = '-';
                            $ingTotal = '-';
                            
                            $salCant = '-';
                            $salPU = '-';
                            $salTotal = '-';
                            
                            if ($mov['tipo_mov'] === 'INGRESO') {
                                $ingCant = $cant;
                                $ingPU = 'S/. ' . number_format($pu, 4, '.', '');
                                $ingTotal = 'S/. ' . number_format($total, 2, '.', ',');
                                
                                $runningStock += $cant;
                                $runningTotal += $total;
                                $runningPUP = $runningStock > 0 ? $runningTotal / $runningStock : 0.0000;
                                $rowClass = 'kardex-row-ingreso';
                            } else { // SALIDA
                                $salCant = $cant;
                                $salPU = 'S/. ' . number_format($pu, 4, '.', '');
                                $salTotal = 'S/. ' . number_format($total, 2, '.', ',');
                                
                                $runningStock -= $cant;
                                $runningTotal -= $total;
                                // El running P.U.P. se mantiene intacto tras la salida
                                if ($runningStock <= 0) {
                                    $runningStock = 0;
                                    $runningTotal = 0.0000;
                                    $runningPUP = 0.0000;
                                }
                                $rowClass = 'kardex-row-salida';
                            }
                            
                            $caduText = !empty($mov['fech_cadu']) ? ' (Vence: ' . $mov['fech_cadu'] . ')' : '';
                            $obse = htmlspecialchars($mov['obse_mov'] ?? '') . $caduText;
                    ?>
                            <tr class="<?=$rowClass?>">
                                <td style="text-align: center; font-weight: 500; font-size: 12.5px; border-right: 1px solid var(--border-color);">
                                    <?=date('d/m/Y', strtotime($mov['fech_mov']))?>
                                </td>
                                <td style="font-weight: 600; font-size: 13px; border-right: 1px solid var(--border-color);">
                                    <?=htmlspecialchars($mov['doc_mov'])?>
                                </td>
                                <!-- Entradas -->
                                <td style="text-align: right; font-weight: 600; color: var(--color-success); font-family: monospace;">
                                    <?=$ingCant?>
                                </td>
                                <td style="text-align: right; font-family: monospace; color: var(--text-muted);">
                                    <?=$ingPU?>
                                </td>
                                <td style="text-align: right; font-weight: 500; color: var(--color-success); border-right: 1px solid var(--border-color); font-family: monospace;">
                                    <?=$ingTotal?>
                                </td>
                                <!-- Salidas -->
                                <td style="text-align: right; font-weight: 600; color: var(--color-danger); font-family: monospace;">
                                    <?=$salCant?>
                                </td>
                                <td style="text-align: right; font-family: monospace; color: var(--text-muted);">
                                    <?=$salPU?>
                                </td>
                                <td style="text-align: right; font-weight: 500; color: var(--color-danger); border-right: 1px solid var(--border-color); font-family: monospace;">
                                    <?=$salTotal?>
                                </td>
                                <!-- Saldos -->
                                <td style="text-align: right; font-weight: 700; color: var(--primary-color); font-family: monospace; background-color: rgba(239, 246, 255, 0.4);">
                                    <?=$runningStock?>
                                </td>
                                <td style="text-align: right; font-weight: 600; font-family: monospace; background-color: rgba(239, 246, 255, 0.4);">
                                    S/. <?=number_format($runningPUP, 4, '.', '')?>
                                </td>
                                <td style="text-align: right; font-weight: 700; color: var(--primary-color); border-right: 1px solid var(--border-color); font-family: monospace; background-color: rgba(239, 246, 255, 0.4);">
                                    S/. <?=number_format($runningTotal, 2, '.', ',')?>
                                </td>
                                <td style="font-size: 12px; color: var(--text-muted); border-right: 1px solid var(--border-color);">
                                    <?=($obse !== '' ? $obse : '-')?>
                                </td>
                                <td style="text-align: center; vertical-align: middle; white-space: nowrap;">
                                    <button onclick="abrirModalEditarMovimiento(<?=htmlspecialchars(json_encode($mov, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT))?>)" style="background: none; border: none; color: var(--accent-color); cursor: pointer; padding: 4px 8px; font-size: 14px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'" title="Editar Detalles del Movimiento">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="eliminarMovimiento(<?=$mov['id_mov']?>)" style="background: none; border: none; color: var(--color-danger); cursor: pointer; padding: 4px 8px; font-size: 14px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'" title="Eliminar Movimiento">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL EDICION DE ARTICULO -->
    <div id="modal-editar-bien" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center; backdrop-filter: blur(4px);">
        <div class="form-card" style="margin: 0; max-width: 600px; width: 90%; animation: slideUp 0.3s ease; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
            <h2 style="color: var(--primary-color); font-size: 16px; margin-bottom: 20px; font-weight: 700; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; display: flex; justify-content: space-between; align-items: center;">
                <span><i class="fas fa-cube"></i> Editar Artículo en Catálogo</span>
                <button type="button" onclick="cerrarModalEditarBien()" style="background: none; border: none; font-size: 18px; color: var(--text-muted); cursor: pointer;"><i class="fas fa-times"></i></button>
            </h2>
            <form id="form-editar-bien" onsubmit="guardarEditarBien(event)">
                <input type="hidden" id="edit_id_bien" name="id_bien" />
                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit_codi_bien">Código de Artículo</label>
                        <input type="text" id="edit_codi_bien" name="codi_bien" class="form-control" placeholder="Ej. A-0245" />
                    </div>
                    <div class="form-group">
                        <label for="edit_unid_bien">Unidad de Medida (*)</label>
                        <select id="edit_unid_bien" name="edit_unid_bien" class="form-select" required>
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
                        <label for="edit_cate_bien">Categoría (*)</label>
                        <select id="edit_cate_bien" name="edit_cate_bien" class="form-select" required>
                            <option value="Ferreteria">Ferretería</option>
                            <option value="Herramienta">Herramienta</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_marc_bien">Marca</label>
                        <input type="text" id="edit_marc_bien" name="marc_bien" class="form-control" placeholder="Ej. Stanley" />
                    </div>
                    <div class="form-group full-width">
                        <label for="edit_desc_bien">Descripción (*)</label>
                        <input type="text" id="edit_desc_bien" name="desc_bien" class="form-control" required />
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="cerrarModalEditarBien()">Cancelar</button>
                    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDICION DE MOVIMIENTO -->
    <div id="modal-editar-movimiento" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center; backdrop-filter: blur(4px);">
        <div class="form-card" style="margin: 0; max-width: 500px; width: 90%; animation: slideUp 0.3s ease; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
            <h2 style="color: var(--primary-color); font-size: 16px; margin-bottom: 20px; font-weight: 700; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; display: flex; justify-content: space-between; align-items: center;">
                <span><i class="fas fa-edit"></i> Editar Detalles del Movimiento</span>
                <button type="button" onclick="cerrarModalEditarMovimiento()" style="background: none; border: none; font-size: 18px; color: var(--text-muted); cursor: pointer;"><i class="fas fa-times"></i></button>
            </h2>
            <form id="form-editar-movimiento" onsubmit="guardarEditarMovimiento(event)">
                <input type="hidden" id="edit_id_mov" name="id_mov" />
                <div class="form-grid" style="grid-template-columns: 1fr;">
                    <div class="form-group">
                        <label for="edit_doc_mov">Documento de Referencia (*)</label>
                        <input type="text" id="edit_doc_mov" name="doc_mov" class="form-control" required />
                    </div>
                    <div class="form-group" id="edit_grupo_cadu">
                        <label for="edit_fech_cadu">Fecha de Caducidad / Vencimiento</label>
                        <input type="date" id="edit_fech_cadu" name="fech_cadu" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="edit_obse_mov">Observaciones</label>
                        <textarea id="edit_obse_mov" name="obse_mov" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="cerrarModalEditarMovimiento()">Cancelar</button>
                    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function ajustarAltura() {
            if (window.parent && window.parent.document.getElementById('body_iframe')) {
                window.parent.document.getElementById('body_iframe').height = document.body.scrollHeight + 50;
            }
        }
        window.addEventListener('load', ajustarAltura);
        window.addEventListener('resize', ajustarAltura);

        function eliminarMovimiento(idMov) {
            if (confirm("¿Está seguro de que desea eliminar este movimiento?\nEsto recalculará todo el historial y stock actual del producto.")) {
                const formData = new URLSearchParams();
                formData.append('id_mov', idMov);

                fetch('almacen_controller.php?action=eliminar_movimiento', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: formData.toString()
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert("Error: " + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Ocurrió un error de red al intentar eliminar el movimiento.");
                });
            }
        }

        // Modales para Edición
        function abrirModalEditarBien(bien) {
            document.getElementById('edit_id_bien').value = bien.id_bien;
            document.getElementById('edit_codi_bien').value = bien.codi_bien || '';
            document.getElementById('edit_unid_bien').value = bien.unid_bien || 'UNIDAD';
            document.getElementById('edit_cate_bien').value = bien.cate_bien || 'Ferreteria';
            document.getElementById('edit_marc_bien').value = bien.marc_bien || '';
            document.getElementById('edit_desc_bien').value = bien.desc_bien || '';
            
            document.getElementById('modal-editar-bien').style.display = 'flex';
        }

        function cerrarModalEditarBien() {
            document.getElementById('modal-editar-bien').style.display = 'none';
        }

        function guardarEditarBien(e) {
            e.preventDefault();
            const formData = new URLSearchParams();
            formData.append('id_bien', document.getElementById('edit_id_bien').value);
            formData.append('codi_bien', document.getElementById('edit_codi_bien').value);
            formData.append('unid_bien', document.getElementById('edit_unid_bien').value);
            formData.append('cate_bien', document.getElementById('edit_cate_bien').value);
            formData.append('marc_bien', document.getElementById('edit_marc_bien').value);
            formData.append('desc_bien', document.getElementById('edit_desc_bien').value);

            fetch('almacen_controller.php?action=editar_bien', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: formData.toString()
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert("Error: " + data.error);
                }
            })
            .catch(err => {
                console.error(err);
                alert("Ocurrió un error al intentar guardar los cambios del artículo.");
            });
        }

        function abrirModalEditarMovimiento(mov) {
            document.getElementById('edit_id_mov').value = mov.id_mov;
            document.getElementById('edit_doc_mov').value = mov.doc_mov || '';
            document.getElementById('edit_obse_mov').value = mov.obse_mov || '';
            
            const grupoCadu = document.getElementById('edit_grupo_cadu');
            const inputCadu = document.getElementById('edit_fech_cadu');
            
            if (mov.tipo_mov === 'INGRESO') {
                grupoCadu.style.display = 'flex';
                inputCadu.value = mov.fech_cadu || '';
            } else {
                grupoCadu.style.display = 'none';
                inputCadu.value = '';
            }
            
            document.getElementById('modal-editar-movimiento').style.display = 'flex';
        }

        function cerrarModalEditarMovimiento() {
            document.getElementById('modal-editar-movimiento').style.display = 'none';
        }

        function guardarEditarMovimiento(e) {
            e.preventDefault();
            const formData = new URLSearchParams();
            formData.append('id_mov', document.getElementById('edit_id_mov').value);
            formData.append('doc_mov', document.getElementById('edit_doc_mov').value);
            formData.append('fech_cadu', document.getElementById('edit_fech_cadu').value);
            formData.append('obse_mov', document.getElementById('edit_obse_mov').value);

            fetch('almacen_controller.php?action=editar_movimiento_detalles', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: formData.toString()
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert("Error: " + data.error);
                }
            })
            .catch(err => {
                console.error(err);
                alert("Ocurrió un error al intentar guardar los cambios del movimiento.");
            });
        }
    </script>
</body>
</html>
