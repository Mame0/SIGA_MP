<?php
require_once 'include/cabecera.php';

// Obtener el almacén seleccionado o por defecto el 1 (Almacén de Mantenimiento)
$almacenId = isset($_GET['almacen']) ? (int)$_GET['almacen'] : 1;

// Obtener lista de almacenes activos
$almacenes = $Db->query("SELECT id_almacen, nomb_almacen FROM mp_almacen_locales WHERE esta_almacen = 1 ORDER BY id_almacen ASC");

// Validar que el almacén seleccionado existe, de lo contrario tomar el primero
$almacenExiste = false;
foreach ($almacenes as $alm) {
    if ($alm['id_almacen'] == $almacenId) {
        $almacenExiste = true;
        break;
    }
}
if (!$almacenExiste && !empty($almacenes)) {
    $almacenId = $almacenes[0]['id_almacen'];
}

// Obtener el nombre del almacén actual
$nombreAlmacenActual = "Almacén Principal";
foreach ($almacenes as $alm) {
    if ($alm['id_almacen'] == $almacenId) {
        $nombreAlmacenActual = $alm['nomb_almacen'];
        break;
    }
}

// Obtener el listado de inventario para este almacén
$inventario = $Db->query("
    SELECT b.id_bien, b.codi_bien, b.desc_bien, b.unid_bien, b.marc_bien, b.cate_bien,
           COALESCE(i.stock_actual, 0) as stock_actual, 
           COALESCE(i.pu_actual, 0.0000) as pu_actual, 
           COALESCE(i.total_actual, 0.0000) as total_actual,
           (
               SELECT MAX(m.fech_cadu) 
               FROM mp_almacen_movimientos m 
               WHERE m.id_almacen = :almId AND m.id_bien = b.id_bien AND m.tipo_mov = 'INGRESO' AND m.fech_cadu IS NOT NULL
           ) as fecha_vencimiento
    FROM mp_almacen_bienes b
    INNER JOIN mp_almacen_inventario i ON b.id_bien = i.id_bien
    WHERE i.id_almacen = :almId
    ORDER BY b.desc_bien ASC
", [':almId' => $almacenId]);

// Calcular KPIs
$totalItems = 0;
$valorTotalInventario = 0.0;
$stockCriticoCount = 0;
$vencimientosCriticosCount = 0;

$hoy = new DateTime();
$limiteVencimiento = (clone $hoy)->modify('+30 days');

foreach ($inventario as &$item) {
    $totalItems++;
    $valorTotalInventario += (float)$item['total_actual'];
    
    // Stock crítico: stock > 0 pero <= 5 (solo para Ferretería. Las herramientas no son críticas si tienen stock >= 1)
    $esHerramienta = ($item['cate_bien'] === 'Herramienta');
    if ($item['stock_actual'] > 0 && !$esHerramienta && $item['stock_actual'] <= 5) {
        $stockCriticoCount++;
    }
    
    // Vencimiento crítico: stock > 0 y vencido o vence en <= 30 días
    if ($item['stock_actual'] > 0 && !empty($item['fecha_vencimiento'])) {
        $fechaVence = new DateTime($item['fecha_vencimiento']);
        if ($fechaVence <= $limiteVencimiento) {
            $vencimientosCriticosCount++;
        }
    }
}
unset($item); // romper referencia
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inventario Valorizado - <?=htmlspecialchars($nombreAlmacenActual)?> - Kardex</title>
    <link rel="stylesheet" href="libmenu/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="libmenu/fontawesome-free/css/v4-shims.min.css" />
    <link rel="stylesheet" href="css/almacen.css" />
</head>
<body>
    <div class="page-header">
        <div class="page-title">
            <i class="fas fa-cubes"></i>
            <h1>Kardex y Control de Almacén Valorizado</h1>
        </div>
        <div>
            <form method="GET" action="almacen_listado.php" style="margin: 0; display: inline-block;">
                <label for="almacen-select" style="font-weight: 600; color: var(--text-muted); margin-right: 8px;">
                    <i class="fas fa-warehouse" style="color: var(--primary-color);"></i> Ver Almacén:
                </label>
                <select id="almacen-select" name="almacen" class="form-select" onchange="this.form.submit()" style="display: inline-block; width: auto; background: var(--card-bg); color: var(--text-color); border: 1px solid var(--border-color); padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 13.5px; cursor: pointer;">
                    <?php foreach ($almacenes as $alm): ?>
                        <option value="<?=$alm['id_almacen']?>" <?=$alm['id_almacen'] == $almacenId ? 'selected' : ''?>>
                            <?=htmlspecialchars($alm['nomb_almacen'])?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>

    <!-- Dashboard Metrics -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon blue">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="metric-info">
                <span class="metric-title">Valor del Inventario</span>
                <span class="metric-value">S/. <?=number_format($valorTotalInventario, 2, '.', ',')?></span>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon green">
                <i class="fas fa-tag"></i>
            </div>
            <div class="metric-info">
                <span class="metric-title">Artículos en Catálogo</span>
                <span class="metric-value"><?=$totalItems?></span>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon yellow">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="metric-info">
                <span class="metric-title">Stock Crítico</span>
                <span class="metric-value"><?=$stockCriticoCount?></span>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon red">
                <i class="far fa-calendar-times"></i>
            </div>
            <div class="metric-info">
                <span class="metric-title">Vencimiento Próximo</span>
                <span class="metric-value"><?=$vencimientosCriticosCount?></span>
            </div>
        </div>
    </div>

    <!-- Control Bar -->
    <div class="control-bar">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="buscador" class="search-input" placeholder="Buscar por código o descripción de artículo..." onkeyup="filtrarTabla()" />
        </div>
        
        <div class="filters-group">
            <button class="filter-btn active" id="btn-all" onclick="aplicarFiltro('all')">
                <i class="fas fa-list"></i> Todos
            </button>
            <button class="filter-btn" id="btn-critico" onclick="aplicarFiltro('critico')">
                <i class="fas fa-exclamation-circle" style="color:var(--color-warning);"></i> Stock Crítico
            </button>
            <button class="filter-btn" id="btn-vencido" onclick="aplicarFiltro('vencido')">
                <i class="far fa-calendar-times" style="color:var(--color-danger);"></i> Vencidos / Próximos
            </button>
        </div>

        <div class="actions-group">
            <a href="almacen_migrar.php" class="btn-secondary" style="background: linear-gradient(135deg, #2ecc71, #27ae60); color: white; border: none;">
                <i class="fas fa-file-import"></i> Importar Excel
            </a>
            <a href="almacen_registro.php?almacen=<?=$almacenId?>" class="btn-primary">
                <i class="fas fa-plus-circle"></i> Nuevo Movimiento
            </a>
            <a href="almacen_registro.php?almacen=<?=$almacenId?>&nuevo_bien=1" class="btn-secondary">
                <i class="fas fa-cube"></i> Crear Artículo
            </a>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="modern-table" id="tabla-inventario">
                <thead>
                    <tr>
                        <th style="width: 100px;">Código</th>
                        <th>Descripción del Artículo</th>
                        <th style="width: 100px;">Unidad</th>
                        <th style="width: 120px;">Marca</th>
                        <th style="width: 120px;">Categoría</th>
                        <th style="width: 120px;">Stock</th>
                        <th style="width: 130px; text-align: right;">P.U. Promedio</th>
                        <th style="width: 130px; text-align: right;">Valor Total</th>
                        <th style="width: 140px;">Vencimiento</th>
                        <th style="width: 100px; text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($inventario)): ?>
                        <tr>
                            <td colspan="10" style="text-align: center; color: var(--text-muted); padding: 30px;">
                                No hay artículos registrados en el inventario de este almacén.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($inventario as $item): 
                            // Determinar badges de stock
                            $stock = (int)$item['stock_actual'];
                            $stockBadge = '';
                            $stockClass = '';
                            $esHerramienta = ($item['cate_bien'] === 'Herramienta');
                            
                            if ($stock === 0) {
                                $stockBadge = '<span class="badge badge-danger"><i class="fas fa-times"></i> Agotado</span>';
                                $stockClass = 'critico';
                            } elseif ($esHerramienta) {
                                // Para herramientas, tener stock >= 1 es óptimo
                                $stockBadge = '<span class="badge badge-success"><i class="fas fa-wrench"></i> ' . $stock . '</span>';
                                $stockClass = 'optimo';
                            } elseif ($stock <= 5) {
                                // Para ferretería, <= 5 es stock crítico
                                $stockBadge = '<span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Crítico: ' . $stock . '</span>';
                                $stockClass = 'critico';
                            } else {
                                $stockBadge = '<span class="badge badge-success"><i class="fas fa-check"></i> ' . $stock . '</span>';
                                $stockClass = 'optimo';
                            }

                            // Determinar badges de vencimiento
                            $venceBadge = '-';
                            $venceClass = '';
                            if (!empty($item['fecha_vencimiento'])) {
                                $fechaV = new DateTime($item['fecha_vencimiento']);
                                $intervalo = $hoy->diff($fechaV);
                                $diasRestantes = (int)$intervalo->format('%r%a');
                                
                                if ($diasRestantes < 0) {
                                    $venceBadge = '<span class="badge badge-danger"><i class="far fa-calendar-times"></i> Vencido: ' . $item['fecha_vencimiento'] . '</span>';
                                    $venceClass = 'vencido';
                                } elseif ($diasRestantes <= 30) {
                                    $venceBadge = '<span class="badge badge-warning"><i class="far fa-calendar-minus"></i> Vence: ' . $item['fecha_vencimiento'] . ' (' . $diasRestantes . ' d)</span>';
                                    $venceClass = 'vencido';
                                } else {
                                    $venceBadge = '<span class="badge badge-success">' . $item['fecha_vencimiento'] . '</span>';
                                    $venceClass = 'vencido-ok';
                                }
                            }
                        ?>
                            <tr data-stock-state="<?=$stockClass?>" data-vence-state="<?=$venceClass?>">
                                <td style="font-weight: 600; color: var(--primary-color);">
                                    <?=htmlspecialchars($item['codi_bien'] ?? 'S/C')?>
                                </td>
                                <td style="font-weight: 500;">
                                    <?=htmlspecialchars($item['desc_bien'])?>
                                </td>
                                <td>
                                    <span style="font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--text-muted);">
                                        <?=htmlspecialchars($item['unid_bien'])?>
                                    </span>
                                </td>
                                <td>
                                    <?=htmlspecialchars($item['marc_bien'] ?? '-')?>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: <?=$esHerramienta ? 'rgba(52, 152, 219, 0.15)' : 'rgba(155, 89, 182, 0.15)'?>; color: <?=$esHerramienta ? '#3498db' : '#9b59b6'?>; border: 1px solid <?=$esHerramienta ? '#3498db' : '#9b59b6'?>; border-radius: 6px; padding: 4px 8px; font-size: 11px; font-weight: 700;">
                                        <?=htmlspecialchars($esHerramienta ? 'Herramienta' : 'Ferretería')?>
                                    </span>
                                </td>
                                <td>
                                    <?=$stockBadge?>
                                </td>
                                <td style="text-align: right; font-family: monospace; font-weight: 600;">
                                    S/. <?=number_format((float)$item['pu_actual'], 4, '.', '')?>
                                </td>
                                <td style="text-align: right; font-weight: 600; color: var(--primary-color);">
                                    S/. <?=number_format((float)$item['total_actual'], 2, '.', ',')?>
                                </td>
                                <td>
                                    <?=$venceBadge?>
                                </td>
                                <td style="text-align: center;">
                                    <a href="almacen_detalle.php?id=<?=$item['id_bien']?>&almacen=<?=$almacenId?>" class="btn-secondary" style="padding: 6px 10px; display: inline-flex; border-radius: 8px;" title="Ver Kardex del Producto">
                                        <i class="fas fa-chart-line"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        let filtroActual = 'all';

        function aplicarFiltro(tipo) {
            filtroActual = tipo;
            
            // Toggle active class on buttons
            document.querySelectorAll('.filters-group .filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            if (tipo === 'all') document.getElementById('btn-all').classList.add('active');
            if (tipo === 'critico') document.getElementById('btn-critico').classList.add('active');
            if (tipo === 'vencido') document.getElementById('btn-vencido').classList.add('active');
            
            filtrarTabla();
        }

        function filtrarTabla() {
            const query = document.getElementById('buscador').value.toLowerCase().trim();
            const rows = document.querySelectorAll('#tabla-inventario tbody tr');
            
            rows.forEach(row => {
                if (row.cells.length === 1 && (row.cells[0].colSpan === 9 || row.cells[0].colSpan === 10)) {
                    return; // Ignorar fila de "no hay datos"
                }

                // Obtener texto de búsqueda
                const codigo = row.cells[0].textContent.toLowerCase();
                const descripcion = row.cells[1].textContent.toLowerCase();
                const marca = row.cells[3].textContent.toLowerCase();
                const categoria = row.cells[4].textContent.toLowerCase();
                const coincideTexto = codigo.includes(query) || descripcion.includes(query) || marca.includes(query) || categoria.includes(query);
                
                // Obtener estados de filtros
                const stockState = row.getAttribute('data-stock-state');
                const venceState = row.getAttribute('data-vence-state');
                
                let coincideFiltro = false;
                if (filtroActual === 'all') {
                    coincideFiltro = true;
                } else if (filtroActual === 'critico') {
                    coincideFiltro = (stockState === 'critico');
                } else if (filtroActual === 'vencido') {
                    coincideFiltro = (venceState === 'vencido');
                }
                
                if (coincideTexto && coincideFiltro) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        // Ajustar la altura del iframe de manera adaptativa
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
