<?php
// Determinar si es una solicitud con token de seguridad o una solicitud normal
$isToken = (isset($_GET['token']) && $_GET['token'] === 'sigamigrate2026');
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || isset($_GET['action']);

if (!$isToken) {
    // Si no es por token, requiere sesión iniciada mediante cabecera
    require_once 'include/cabecera.php';
} else {
    session_start();
}

// Conexión a la Base de Datos
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'mpfnarequipa_siga';

$credentialsFile = 'classes/.credentials/db.php.ini';
if (!file_exists($credentialsFile)) {
    $credentialsFile = '.credentials/db.php.ini';
}

if (file_exists($credentialsFile)) {
    $cred = parse_ini_file($credentialsFile);
    if ($cred) {
        $host = isset($cred['host']) ? $cred['host'] : $host;
        $user = isset($cred['usuario']) ? $cred['usuario'] : $user;
        $pass = isset($cred['clave']) ? $cred['clave'] : $pass;
        $db = isset($cred['dbnombre']) ? $cred['dbnombre'] : $db;
    }
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    if ($isAjax) {
        echo json_encode(['success' => false, 'error' => 'Error de conexión: ' . $e->getMessage()]);
        exit;
    } else {
        die('Error de conexión a la base de datos: ' . $e->getMessage());
    }
}

// Acción de Migración
if (isset($_GET['action']) && $_GET['action'] === 'run') {
    header('Content-Type: application/json; charset=utf-8');
    
    // Validar autorización
    if (!$isToken && !isset($_SESSION['iden_oper'])) {
        echo json_encode(['success' => false, 'error' => 'Sesión no iniciada o no autorizado']);
        exit;
    }
    
    require_once 'simplexlsx.class.php';
    $file = 'Kardex_Almacen_Automatizado_v3.xlsx';
    
    if (!file_exists($file)) {
        echo json_encode(['success' => false, 'error' => 'El archivo Excel no existe en el servidor: ' . $file]);
        exit;
    }
    
    $xlsx = SimpleXLSX::parse($file);
    if (!$xlsx) {
        echo json_encode(['success' => false, 'error' => 'Error al leer el archivo Excel: ' . SimpleXLSX::parse_error()]);
        exit;
    }
    
    try {
        $pdo->beginTransaction();
        
        // 1. Limpieza de tablas (Eliminación segura)
        $logs[] = "Iniciando limpieza de base de datos...";
        $pdo->exec("DELETE FROM mp_almacen_movimientos");
        $pdo->exec("DELETE FROM mp_almacen_inventario");
        $pdo->exec("DELETE FROM mp_almacen_bienes");
        $logs[] = "Base de datos limpia para importación fresca.";
        
        $goods_catalog = []; // key => ['id' => int, 'code' => string]
        $used_codes = [];    // code => true
        
        $total_bienes = 0;
        $total_ingresos = 0;
        $total_salidas = 0;
        
        // Helper para limpieza de fechas de Excel
        $cleanDate = function($dateStr) {
            if (empty($dateStr)) return null;
            $dateStr = trim($dateStr);
            if ($dateStr === '1970-01-01 00:00:00' || $dateStr === '1970-01-01' || 
                $dateStr === '1901-05-25' || $dateStr === '1901-05-25 00:00:00') {
                return null;
            }
            if (preg_match('/^\d{4}-\d{2}-\d{2}/', $dateStr, $m)) {
                return $m[0];
            }
            return null;
        };
        
        $stmt_insert_bien = $pdo->prepare("INSERT INTO mp_almacen_bienes (codi_bien, desc_bien, unid_bien, marc_bien, esta_bien) VALUES (?, ?, ?, ?, 1)");
        $stmt_insert_mov = $pdo->prepare("INSERT INTO mp_almacen_movimientos (id_almacen, id_bien, tipo_mov, fech_mov, doc_mov, cant_mov, pu_mov, total_mov, fech_cadu, obse_mov) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_insert_inv = $pdo->prepare("INSERT INTO mp_almacen_inventario (id_almacen, id_bien, stock_actual, pu_actual, total_actual) VALUES (?, ?, ?, ?, ?)");
        
        // Rastreador de inventario en memoria para calcular el costo promedio móvil
        // key: almacenId . '_' . idBien => ['stock' => int, 'pu' => float, 'total' => float]
        $inventory_tracker = [];
        
        // Procesar Almacen Principal desde Hoja 2 y Hoja 3
        $sheets_to_process = [
            2 => 1, // Hoja index 2 -> Almacén Principal
            3 => 1  // Hoja index 3 -> Almacén Principal
        ];
        
        $collected_movements = [];
        
        foreach ($sheets_to_process as $sheetId => $almacenId) {
            $rows = $xlsx->rows($sheetId);
            if (!$rows) {
                $logs[] = "Advertencia: Hoja #$sheetId no se pudo leer o está vacía.";
                continue;
            }
            
            $sheet_name = $xlsx->sheetName($sheetId);
            $logs[] = "Extrayendo datos de hoja '$sheet_name'...";
            
            $rowCount = count($rows);
            $items_in_sheet = 0;
            
            for ($i = 3; $i < $rowCount; $i++) {
                $row = $rows[$i];
                if (empty($row[4]) || trim($row[4]) === '') {
                    continue; // Saltar filas sin descripción
                }
                
                // Extraer campos principales
                $code = !empty($row[3]) ? trim($row[3]) : null;
                $desc = trim($row[4]);
                $unit = !empty($row[5]) ? trim($row[5]) : 'UNIDAD';
                $brand = !empty($row[6]) ? trim($row[6]) : null;
                
                // Normalizar Código de Barras
                if ($code === '' || $code === '-') {
                    $code = null;
                }
                
                // Generar llave de agrupación para evitar registros duplicados del mismo producto
                $key = ($code !== null ? 'CODE:' . $code : 'NOCODE') . '|' . strtolower($desc) . '|' . strtolower($brand) . '|' . strtolower($unit);
                
                if (isset($goods_catalog[$key])) {
                    $id_bien = $goods_catalog[$key]['id'];
                    $code = $goods_catalog[$key]['code']; // Usar el código resuelto
                } else {
                    // Nuevo artículo en esta migración. Resolver conflicto de código único
                    if ($code !== null) {
                        if (isset($used_codes[$code])) {
                            // Código duplicado para un producto diferente. Agregar sufijo para respetar restricción UNIQUE de la DB
                            $suffix = 2;
                            $new_code = $code . '_' . $suffix;
                            while (isset($used_codes[$new_code])) {
                                $suffix++;
                                $new_code = $code . '_' . $suffix;
                            }
                            $logs[] = "Ajuste de código: El artículo '$desc' usa un código ya existente '$code'. Registrado como '$new_code'.";
                            $code = $new_code;
                        }
                        $used_codes[$code] = true;
                    }
                    
                    // Insertar en mp_almacen_bienes
                    $stmt_insert_bien->execute([$code, $desc, $unit, $brand]);
                    $id_bien = $pdo->lastInsertId();
                    $goods_catalog[$key] = [
                        'id' => $id_bien,
                        'code' => $code
                    ];
                    $total_bienes++;
                }
                
                $items_in_sheet++;
                
                // 1. Procesar INGRESO
                $oc = !empty($row[1]) ? trim($row[1]) : '';
                $oc_date = $cleanDate($row[2]);
                
                $ingreso_date = $cleanDate($row[7]);
                if ($ingreso_date === null) {
                    $ingreso_date = $oc_date !== null ? $oc_date : date('Y-m-d');
                }
                
                $ingreso_doc = !empty($row[8]) ? trim($row[8]) : '';
                if ($ingreso_doc !== '') {
                    $ingreso_doc = "NEA " . $ingreso_doc;
                } else {
                    $ingreso_doc = ($oc !== '') ? "O/C: " . $oc : "NEA S/N";
                }
                
                $cant_ingreso = !empty($row[9]) ? (int)$row[9] : 0;
                $pu_ingreso = !empty($row[10]) ? (float)$row[10] : 0.0;
                $total_ingreso = !empty($row[11]) ? (float)$row[11] : 0.0;
                
                $expiry_date = $cleanDate($row[19]);
                $observation = !empty($row[20]) ? trim($row[20]) : '';
                
                if ($cant_ingreso > 0) {
                    $calculated_total_ingreso = $cant_ingreso * $pu_ingreso;
                    $collected_movements[] = [
                        'id_bien' => $id_bien,
                        'tipo_mov' => 'INGRESO',
                        'fech_mov' => $ingreso_date,
                        'doc_mov' => $ingreso_doc,
                        'cant_mov' => $cant_ingreso,
                        'pu_mov' => $pu_ingreso,
                        'total_mov' => $calculated_total_ingreso,
                        'fech_cadu' => $expiry_date,
                        'obse_mov' => $observation
                    ];
                }
                
                // 2. Procesar SALIDA
                $salida_date = $cleanDate($row[12]);
                if ($salida_date === null) {
                    $salida_date = $ingreso_date;
                }
                
                $salida_doc = !empty($row[13]) ? trim($row[13]) : '';
                if ($salida_doc === '') {
                    $salida_doc = "Guía de Salida S/N";
                }
                
                $cant_salida = !empty($row[14]) ? (int)$row[14] : 0;
                
                if ($cant_salida > 0) {
                    $collected_movements[] = [
                        'id_bien' => $id_bien,
                        'tipo_mov' => 'SALIDA',
                        'fech_mov' => $salida_date,
                        'doc_mov' => $salida_doc,
                        'cant_mov' => $cant_salida,
                        'pu_mov' => null,
                        'total_mov' => null,
                        'fech_cadu' => null,
                        'obse_mov' => $observation
                    ];
                }
            }
            
            $logs[] = "Hoja '$sheet_name' extraída. $items_in_sheet artículos leídos.";
        }
        
        // Ordenar todos los movimientos cronológicamente
        $logs[] = "Ordenando cronológicamente " . count($collected_movements) . " movimientos consolidados...";
        usort($collected_movements, function($a, $b) {
            $dateA = strtotime($a['fech_mov']);
            $dateB = strtotime($b['fech_mov']);
            if ($dateA !== $dateB) {
                return $dateA <=> $dateB;
            }
            // En la misma fecha, los ingresos se procesan antes que las salidas
            if ($a['tipo_mov'] !== $b['tipo_mov']) {
                return $a['tipo_mov'] === 'INGRESO' ? -1 : 1;
            }
            return 0;
        });
        
        // Procesar y registrar movimientos ordenados
        $logs[] = "Procesando y calculando saldos valorizados (PUP)...";
        foreach ($collected_movements as $mov) {
            $id_bien = $mov['id_bien'];
            $almacenId = 1; // Siempre Almacén Principal
            $invKey = $almacenId . '_' . $id_bien;
            
            if (!isset($inventory_tracker[$invKey])) {
                $inventory_tracker[$invKey] = [
                    'stock' => 0,
                    'pu' => 0.0,
                    'total' => 0.0
                ];
            }
            
            if ($mov['tipo_mov'] === 'INGRESO') {
                $inventory_tracker[$invKey]['stock'] += $mov['cant_mov'];
                $inventory_tracker[$invKey]['total'] += $mov['total_mov'];
                $inventory_tracker[$invKey]['pu'] = $inventory_tracker[$invKey]['stock'] > 0 
                    ? $inventory_tracker[$invKey]['total'] / $inventory_tracker[$invKey]['stock'] 
                    : 0.0;
                
                $stmt_insert_mov->execute([
                    $almacenId,
                    $id_bien,
                    'INGRESO',
                    $mov['fech_mov'],
                    $mov['doc_mov'],
                    $mov['cant_mov'],
                    $mov['pu_mov'],
                    $mov['total_mov'],
                    $mov['fech_cadu'],
                    $mov['obse_mov']
                ]);
                $total_ingresos++;
            } else { // SALIDA
                $pu_salida = $inventory_tracker[$invKey]['pu'];
                $total_salida = $mov['cant_mov'] * $pu_salida;
                
                $inventory_tracker[$invKey]['stock'] -= $mov['cant_mov'];
                $inventory_tracker[$invKey]['total'] -= $total_salida;
                $inventory_tracker[$invKey]['pu'] = $inventory_tracker[$invKey]['stock'] > 0 
                    ? $pu_salida 
                    : 0.0;
                
                $stmt_insert_mov->execute([
                    $almacenId,
                    $id_bien,
                    'SALIDA',
                    $mov['fech_mov'],
                    $mov['doc_mov'],
                    $mov['cant_mov'],
                    $pu_salida,
                    $total_salida,
                    null,
                    $mov['obse_mov']
                ]);
                $total_salidas++;
            }
        }
        
        // 3. Registrar inventario final consolidado
        $logs[] = "Consolidando stocks actuales...";
        $total_inv_value = 0.0;
        foreach ($inventory_tracker as $invKey => $inv) {
            list($almacenId, $id_bien) = explode('_', $invKey);
            $stmt_insert_inv->execute([
                $almacenId,
                $id_bien,
                $inv['stock'],
                $inv['pu'],
                $inv['total']
            ]);
            $total_inv_value += $inv['total'];
        }
        
        $pdo->commit();
        $logs[] = "¡Sincronización finalizada con éxito!";
        
        echo json_encode([
            'success' => true,
            'stats' => [
                'total_bienes' => $total_bienes,
                'total_ingresos' => $total_ingresos,
                'total_salidas' => $total_salidas,
                'total_inv_value' => round($total_inv_value, 2)
            ],
            'logs' => $logs
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode([
            'success' => false,
            'error' => 'Error durante el proceso de importación: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Validar inicio de sesión para visualización de la interfaz web
if (!isset($_SESSION['iden_oper'])) {
    // Si no está logueado y no es AJAX, redirigir a login
    echo "<script>parent.location.href='index.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sincronización de Kardex desde Excel - SIGA</title>
    <link rel="stylesheet" href="libmenu/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="libmenu/fontawesome-free/css/v4-shims.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #8b5cf6;
            --primary-hover: #7c3aed;
            --secondary: #6366f1;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --background: #0f0c1b;
            --card-bg: rgba(30, 25, 50, 0.55);
            --border: rgba(255, 255, 255, 0.08);
            --text: #f3f4f6;
            --text-muted: #9ca3af;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at 50% 50%, #1c1535 0%, var(--background) 100%);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px 15px;
            overflow-x: hidden;
        }

        /* Glassmorphic Container */
        .sync-container {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 24px;
            width: 100%;
            max-width: 800px;
            padding: 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.8s ease-out;
            position: relative;
            overflow: hidden;
        }

        .sync-container::before {
            content: '';
            position: absolute;
            top: -150px;
            right: -150px;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.25) 0%, transparent 70%);
            z-index: 0;
            pointer-events: none;
        }

        .sync-container::after {
            content: '';
            position: absolute;
            bottom: -150px;
            left: -150px;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.25) 0%, transparent 70%);
            z-index: 0;
            pointer-events: none;
        }

        .content-relative {
            position: relative;
            z-index: 1;
        }

        /* Header block */
        .sync-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .sync-icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: inline-flex;
            justify-content: center;
            align-items: center;
            font-size: 36px;
            color: #fff;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(139, 92, 246, 0.4);
            transition: transform 0.5s ease;
        }

        .sync-container:hover .sync-icon-wrapper {
            transform: scale(1.08) rotate(10deg);
        }

        .sync-title {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #fff 30%, #c084fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }

        .sync-subtitle {
            font-size: 15px;
            color: var(--text-muted);
            line-height: 1.6;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Actions bar */
        .actions-bar {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 35px;
        }

        .btn {
            font-family: inherit;
            font-size: 15px;
            font-weight: 600;
            padding: 14px 28px;
            border-radius: 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
            outline: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff;
            border: none;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.45);
            background: linear-gradient(135deg, #9333ea, #4f46e5);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        /* Console styling */
        .console-wrapper {
            margin-bottom: 35px;
        }

        .console-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .console-box {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            height: 250px;
            overflow-y: auto;
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            line-height: 1.6;
            color: #10b981;
            scroll-behavior: smooth;
        }

        .console-box::-webkit-scrollbar {
            width: 8px;
        }
        .console-box::-webkit-scrollbar-track {
            background: transparent;
        }
        .console-box::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }
        .console-box::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .log-row {
            margin-bottom: 5px;
            animation: slideInLog 0.2s ease-out forwards;
            opacity: 0;
            transform: translateY(5px);
        }

        .log-row.info {
            color: #60a5fa;
        }

        .log-row.warn {
            color: var(--warning);
        }

        .log-row.err {
            color: var(--danger);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 15px;
            display: none; /* Hidden until loaded */
        }

        @media (max-width: 600px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(139, 92, 246, 0.2);
            transform: translateY(-2px);
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 13px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Loader */
        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s ease-in-out infinite;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes slideInLog {
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="sync-container">
        <div class="content-relative">
            <!-- Header -->
            <div class="sync-header">
                <div class="sync-icon-wrapper">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <h1 class="sync-title">Sincronización del Kardex</h1>
                <p class="sync-subtitle">
                    Esta herramienta sincroniza la base de datos de SIGA con los datos y movimientos actualizados del archivo de Excel <strong>Kardex_Almacen_Automatizado_v3.xlsx</strong> para el Almacén Principal.
                </p>
            </div>

            <!-- Actions -->
            <div class="actions-bar">
                <a href="almacen_listado.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Listado
                </a>
                <button id="btn-sync" class="btn btn-primary" onclick="startSync()">
                    <i class="fas fa-play"></i> Iniciar Importación
                    <div class="spinner" id="btn-spinner"></div>
                </button>
            </div>

            <!-- Console Log -->
            <div class="console-wrapper">
                <div class="console-title">
                    <i class="fas fa-terminal"></i> Terminal de Operación
                </div>
                <div class="console-box" id="console">
                    <div class="log-row info">Esperando orden de importación... Presione "Iniciar Importación".</div>
                </div>
            </div>

            <!-- Stats -->
            <div class="stats-grid" id="stats-panel">
                <div class="stat-card">
                    <div class="stat-value" id="stat-bienes">-</div>
                    <div class="stat-label">Artículos Registrados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="stat-movimientos">-</div>
                    <div class="stat-label">Movimientos Cargados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="stat-value-total">-</div>
                    <div class="stat-label">Valor Total del Inventario</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="stat-estado">Listo</div>
                    <div class="stat-label">Estado del Sistema</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const consoleBox = document.getElementById('console');
        const btnSync = document.getElementById('btn-sync');
        const btnSpinner = document.getElementById('btn-spinner');
        const statsPanel = document.getElementById('stats-panel');

        function addLog(text, type = 'success') {
            const row = document.createElement('div');
            row.className = `log-row ${type}`;
            row.textContent = `[${new Date().toLocaleTimeString()}] ${text}`;
            consoleBox.appendChild(row);
            consoleBox.scrollTop = consoleBox.scrollHeight;
        }

        function startSync() {
            // UI state updates
            btnSync.disabled = true;
            btnSpinner.style.display = 'block';
            statsPanel.style.display = 'none';
            consoleBox.innerHTML = '';
            
            addLog('Abriendo canal de comunicación con el servidor...', 'info');
            
            // Realizar solicitud ajax
            fetch('almacen_migrar.php?action=run')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('La respuesta de red no fue satisfactoria.');
                    }
                    return response.json();
                })
                .then(data => {
                    btnSync.disabled = false;
                    btnSpinner.style.display = 'none';
                    
                    if (data.success) {
                        // Imprimir logs devueltos por el servidor
                        data.logs.forEach(log => {
                            let type = 'success';
                            if (log.includes('Ajuste') || log.includes('Advertencia')) {
                                type = 'warn';
                            }
                            addLog(log, type);
                        });
                        
                        addLog('Importación exitosa y confirmada en la Base de Datos.', 'success');
                        
                        // Cargar y mostrar estadísticas
                        document.getElementById('stat-bienes').textContent = data.stats.total_bienes;
                        document.getElementById('stat-movimientos').textContent = data.stats.total_ingresos + data.stats.total_salidas;
                        document.getElementById('stat-value-total').textContent = `S/. ${data.stats.total_inv_value.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                        document.getElementById('stat-estado').textContent = 'Completado';
                        document.getElementById('stat-estado').style.color = 'var(--success)';
                        statsPanel.style.display = 'grid';
                        
                        // Ajustar tamaño del iframe principal adaptativo
                        ajustarAltura();
                    } else {
                        addLog(`Error en migración: ${data.error}`, 'err');
                    }
                })
                .catch(error => {
                    btnSync.disabled = false;
                    btnSpinner.style.display = 'none';
                    addLog(`Error de comunicación: ${error.message}`, 'err');
                });
        }

        function ajustarAltura() {
            if (window.parent && window.parent.document.getElementById('body_iframe')) {
                window.parent.document.getElementById('body_iframe').height = document.body.scrollHeight + 50;
            }
        }
        window.addEventListener('load', ajustarAltura);
    </script>
</body>
</html>
