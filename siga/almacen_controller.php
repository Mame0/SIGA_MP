<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

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
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos: ' . $e->getMessage()]);
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'buscar_bienes') {
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        if (strlen($q) < 2) {
            echo json_encode([]);
            exit;
        }
        
        $stmt = $pdo->prepare("SELECT id_bien, codi_bien, desc_bien, unid_bien, marc_bien, cate_bien 
                               FROM mp_almacen_bienes 
                               WHERE desc_bien LIKE ? OR codi_bien LIKE ? 
                               LIMIT 15");
        $stmt->execute(["%$q%", "%$q%"]);
        echo json_encode($stmt->fetchAll());
        exit;
    }
    
    if ($action === 'obtener_bien') {
        $idBien = isset($_GET['id_bien']) ? (int)$_GET['id_bien'] : 0;
        $idAlmacen = isset($_GET['id_almacen']) ? (int)$_GET['id_almacen'] : 1;
        
        $stmt = $pdo->prepare("SELECT b.*, COALESCE(i.stock_actual, 0) as stock_actual, COALESCE(i.pu_actual, 0.0) as pu_actual 
                               FROM mp_almacen_bienes b 
                               LEFT JOIN mp_almacen_inventario i ON b.id_bien = i.id_bien AND i.id_almacen = ? 
                               WHERE b.id_bien = ?");
        $stmt->execute([$idAlmacen, $idBien]);
        $bien = $stmt->fetch();
        
        if ($bien) {
            echo json_encode(['success' => true, 'bien' => $bien]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Bien no encontrado']);
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'registrar_bien') {
        $codigo = !empty($_POST['codi_bien']) ? trim($_POST['codi_bien']) : null;
        $descripcion = !empty($_POST['desc_bien']) ? trim($_POST['desc_bien']) : '';
        $unidad = !empty($_POST['unid_bien']) ? trim($_POST['unid_bien']) : 'UNIDAD';
        $marca = !empty($_POST['marc_bien']) ? trim($_POST['marc_bien']) : null;
        $categoria = !empty($_POST['cate_bien']) ? trim($_POST['cate_bien']) : 'Ferreteria';
        
        if (empty($descripcion)) {
            echo json_encode(['success' => false, 'error' => 'La descripción es obligatoria']);
            exit;
        }
        
        try {
            $pdo->beginTransaction();
            
            // Verificar si ya existe por código
            if ($codigo) {
                $stmt = $pdo->prepare("SELECT id_bien FROM mp_almacen_bienes WHERE codi_bien = ?");
                $stmt->execute([$codigo]);
                if ($stmt->fetch()) {
                    throw new Exception("Ya existe un artículo registrado con el código " . htmlspecialchars($codigo));
                }
            }
            
            // Verificar por descripción
            $stmt = $pdo->prepare("SELECT id_bien FROM mp_almacen_bienes WHERE desc_bien = ?");
            $stmt->execute([$descripcion]);
            if ($stmt->fetch()) {
                throw new Exception("Ya existe un artículo registrado con esa descripción");
            }
            
            $stmt = $pdo->prepare("INSERT INTO mp_almacen_bienes (codi_bien, desc_bien, unid_bien, marc_bien, cate_bien) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$codigo, $descripcion, $unidad, $marca, $categoria]);
            $idBien = $pdo->lastInsertId();
            
            $pdo->commit();
            echo json_encode(['success' => true, 'id_bien' => $idBien, 'message' => 'Artículo registrado con éxito en el catálogo']);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
    
    if ($action === 'registrar_movimiento') {
        $idAlmacen = isset($_POST['id_almacen']) ? (int)$_POST['id_almacen'] : 0;
        $idBien = isset($_POST['id_bien']) ? (int)$_POST['id_bien'] : 0;
        $tipoMov = isset($_POST['tipo_mov']) ? $_POST['tipo_mov'] : '';
        $fechMov = !empty($_POST['fech_mov']) ? trim($_POST['fech_mov']) : date('Y-m-d');
        $docMov = !empty($_POST['doc_mov']) ? trim($_POST['doc_mov']) : '';
        $cantMov = isset($_POST['cant_mov']) ? (int)$_POST['cant_mov'] : 0;
        $puMov = isset($_POST['pu_mov']) ? (float)$_POST['pu_mov'] : 0.0;
        $fechaCadu = !empty($_POST['fech_cadu']) ? trim($_POST['fech_cadu']) : null;
        $observaciones = !empty($_POST['obse_mov']) ? trim($_POST['obse_mov']) : null;
        
        if ($idAlmacen <= 0 || $idBien <= 0 || !in_array($tipoMov, ['INGRESO', 'SALIDA']) || empty($docMov) || $cantMov <= 0) {
            echo json_encode(['success' => false, 'error' => 'Todos los campos marcados como obligatorios son requeridos.']);
            exit;
        }
        
        try {
            $pdo->beginTransaction();
            
            // Obtener stock actual e info de inventario
            $stmt = $pdo->prepare("SELECT stock_actual, pu_actual, total_actual 
                                   FROM mp_almacen_inventario 
                                   WHERE id_almacen = ? AND id_bien = ? 
                                   FOR UPDATE");
            $stmt->execute([$idAlmacen, $idBien]);
            $inventario = $stmt->fetch();
            
            if (!$inventario) {
                // Si no existe registro de inventario, lo inicializamos en 0
                $inventario = ['stock_actual' => 0, 'pu_actual' => 0.0000, 'total_actual' => 0.0000];
                $stmt = $pdo->prepare("INSERT INTO mp_almacen_inventario (id_almacen, id_bien, stock_actual, pu_actual, total_actual) VALUES (?, ?, 0, 0, 0)");
                $stmt->execute([$idAlmacen, $idBien]);
            }
            
            $stockActual = (int)$inventario['stock_actual'];
            $puActual = (float)$inventario['pu_actual'];
            $totalActual = (float)$inventario['total_actual'];
            
            if ($tipoMov === 'INGRESO') {
                if ($puMov <= 0) {
                    throw new Exception("El precio unitario de ingreso debe ser mayor a cero.");
                }
                
                $totalMov = $cantMov * $puMov;
                $nuevoStock = $stockActual + $cantMov;
                $nuevoTotal = $totalActual + $totalMov;
                $nuevoPU = $nuevoStock > 0 ? $nuevoTotal / $nuevoStock : 0.0;
                
                // 1. Insertar movimiento
                $stmt = $pdo->prepare("INSERT INTO mp_almacen_movimientos (id_almacen, id_bien, tipo_mov, fech_mov, doc_mov, cant_mov, pu_mov, total_mov, fech_cadu, obse_mov) 
                                       VALUES (?, ?, 'INGRESO', ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$idAlmacen, $idBien, $fechMov, $docMov, $cantMov, $puMov, $totalMov, $fechaCadu, $observaciones]);
                
                // 2. Actualizar inventario
                $stmt = $pdo->prepare("UPDATE mp_almacen_inventario 
                                       SET stock_actual = ?, pu_actual = ?, total_actual = ? 
                                       WHERE id_almacen = ? AND id_bien = ?");
                $stmt->execute([$nuevoStock, $nuevoPU, $nuevoTotal, $idAlmacen, $idBien]);
                
            } else { // SALIDA
                if ($cantMov > $stockActual) {
                    throw new Exception("Stock insuficiente. Stock actual disponible: $stockActual unidades.");
                }
                
                // En las salidas se aplica el costo promedio unitario actual
                $puMov = $puActual;
                $totalMov = $cantMov * $puMov;
                
                $nuevoStock = $stockActual - $cantMov;
                $nuevoTotal = $totalActual - $totalMov;
                $nuevoPU = $nuevoStock > 0 ? $puActual : 0.0; // Mantiene el mismo P.U. si queda stock
                
                // 1. Insertar movimiento
                $stmt = $pdo->prepare("INSERT INTO mp_almacen_movimientos (id_almacen, id_bien, tipo_mov, fech_mov, doc_mov, cant_mov, pu_mov, total_mov, obse_mov) 
                                       VALUES (?, ?, 'SALIDA', ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$idAlmacen, $idBien, $fechMov, $docMov, $cantMov, $puMov, $totalMov, $observaciones]);
                
                // 2. Actualizar inventario
                $stmt = $pdo->prepare("UPDATE mp_almacen_inventario 
                                       SET stock_actual = ?, pu_actual = ?, total_actual = ? 
                                       WHERE id_almacen = ? AND id_bien = ?");
                $stmt->execute([$nuevoStock, $nuevoPU, $nuevoTotal, $idAlmacen, $idBien]);
            }
            
            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Movimiento registrado con éxito.']);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
    
    if ($action === 'eliminar_movimiento') {
        $idMov = isset($_POST['id_mov']) ? (int)$_POST['id_mov'] : 0;
        
        if ($idMov <= 0) {
            echo json_encode(['success' => false, 'error' => 'ID de movimiento no válido.']);
            exit;
        }
        
        try {
            $pdo->beginTransaction();
            
            // 1. Obtener datos del movimiento para saber a qué bien y almacén pertenece
            $stmt = $pdo->prepare("SELECT id_almacen, id_bien, tipo_mov, cant_mov FROM mp_almacen_movimientos WHERE id_mov = ? FOR UPDATE");
            $stmt->execute([$idMov]);
            $movInfo = $stmt->fetch();
            
            if (!$movInfo) {
                throw new Exception("El movimiento que intenta eliminar no existe.");
            }
            
            $idAlmacen = (int)$movInfo['id_almacen'];
            $idBien = (int)$movInfo['id_bien'];
            
            // 2. Eliminar el movimiento
            $stmt = $pdo->prepare("DELETE FROM mp_almacen_movimientos WHERE id_mov = ?");
            $stmt->execute([$idMov]);
            
            // 3. Recalcular todo el historial de este artículo cronológicamente
            $stmt = $pdo->prepare("SELECT id_mov, tipo_mov, cant_mov, pu_mov, total_mov 
                                   FROM mp_almacen_movimientos 
                                   WHERE id_almacen = ? AND id_bien = ? 
                                   ORDER BY fech_mov ASC, id_mov ASC FOR UPDATE");
            $stmt->execute([$idAlmacen, $idBien]);
            $movs = $stmt->fetchAll();
            
            $runningStock = 0;
            $runningTotal = 0.0000;
            $runningPUP = 0.0000;
            
            $stmtUpdate = $pdo->prepare("UPDATE mp_almacen_movimientos SET pu_mov = ?, total_mov = ? WHERE id_mov = ?");
            
            foreach ($movs as $m) {
                $mId = (int)$m['id_mov'];
                $mTipo = $m['tipo_mov'];
                $mCant = (int)$m['cant_mov'];
                $mPU = (float)$m['pu_mov'];
                $mTotal = (float)$m['total_mov'];
                
                if ($mTipo === 'INGRESO') {
                    $runningStock += $mCant;
                    $runningTotal += $mTotal;
                    $runningPUP = $runningStock > 0 ? $runningTotal / $runningStock : 0.0000;
                } else { // SALIDA
                    // Validación de stock crítico durante el recálculo
                    if ($mCant > $runningStock) {
                        throw new Exception("No se puede eliminar este movimiento porque causaría que la salida posterior (ID: $mId) exceda el stock disponible, dejando el inventario en negativo.");
                    }
                    
                    // Las salidas se recalculan con el P.U.P. acumulado hasta este punto
                    $nuevoPU = $runningPUP;
                    $nuevoTotal = $mCant * $nuevoPU;
                    
                    // Si el precio promedio o el total han variado, actualizamos la base de datos
                    if (abs($mPU - $nuevoPU) > 0.0001 || abs($mTotal - $nuevoTotal) > 0.01) {
                        $stmtUpdate->execute([$nuevoPU, $nuevoTotal, $mId]);
                    }
                    
                    $runningStock -= $mCant;
                    $runningTotal -= $nuevoTotal;
                    
                    if ($runningStock <= 0) {
                        $runningStock = 0;
                        $runningTotal = 0.0000;
                        $runningPUP = 0.0000;
                    }
                }
            }
            
            // 4. Actualizar o eliminar de la tabla de inventario
            if (count($movs) === 0) {
                $stmt = $pdo->prepare("DELETE FROM mp_almacen_inventario WHERE id_almacen = ? AND id_bien = ?");
                $stmt->execute([$idAlmacen, $idBien]);
            } else {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM mp_almacen_inventario WHERE id_almacen = ? AND id_bien = ?");
                $stmt->execute([$idAlmacen, $idBien]);
                $exists = $stmt->fetchColumn() > 0;
                
                if ($exists) {
                    $stmt = $pdo->prepare("UPDATE mp_almacen_inventario 
                                           SET stock_actual = ?, pu_actual = ?, total_actual = ? 
                                           WHERE id_almacen = ? AND id_bien = ?");
                    $stmt->execute([$runningStock, $runningPUP, $runningTotal, $idAlmacen, $idBien]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO mp_almacen_inventario (id_almacen, id_bien, stock_actual, pu_actual, total_actual) 
                                           VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$idAlmacen, $idBien, $runningStock, $runningPUP, $runningTotal]);
                }
            }
            
            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Movimiento eliminado y Kardex recalculado correctamente.']);
            
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
    
    if ($action === 'editar_bien') {
        $idBien = isset($_POST['id_bien']) ? (int)$_POST['id_bien'] : 0;
        $codigo = !empty($_POST['codi_bien']) ? trim($_POST['codi_bien']) : null;
        $descripcion = !empty($_POST['desc_bien']) ? trim($_POST['desc_bien']) : '';
        $unidad = !empty($_POST['unid_bien']) ? trim($_POST['unid_bien']) : 'UNIDAD';
        $marca = !empty($_POST['marc_bien']) ? trim($_POST['marc_bien']) : null;
        $categoria = !empty($_POST['cate_bien']) ? trim($_POST['cate_bien']) : 'Ferreteria';
        
        if ($idBien <= 0 || empty($descripcion)) {
            echo json_encode(['success' => false, 'error' => 'El ID del artículo y la descripción son obligatorios.']);
            exit;
        }
        
        try {
            $pdo->beginTransaction();
            
            // Verificar si ya existe por código (excluyendo el actual)
            if ($codigo) {
                $stmt = $pdo->prepare("SELECT id_bien FROM mp_almacen_bienes WHERE codi_bien = ? AND id_bien != ?");
                $stmt->execute([$codigo, $idBien]);
                if ($stmt->fetch()) {
                    throw new Exception("Ya existe otro artículo registrado con el código " . htmlspecialchars($codigo));
                }
            }
            
            // Verificar por descripción (excluyendo el actual)
            $stmt = $pdo->prepare("SELECT id_bien FROM mp_almacen_bienes WHERE desc_bien = ? AND id_bien != ?");
            $stmt->execute([$descripcion, $idBien]);
            if ($stmt->fetch()) {
                throw new Exception("Ya existe otro artículo registrado con esa descripción.");
            }
            
            $stmt = $pdo->prepare("UPDATE mp_almacen_bienes 
                                   SET codi_bien = ?, desc_bien = ?, unid_bien = ?, marc_bien = ?, cate_bien = ? 
                                   WHERE id_bien = ?");
            $stmt->execute([$codigo, $descripcion, $unidad, $marca, $categoria, $idBien]);
            
            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Artículo actualizado con éxito en el catálogo.']);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
    
    if ($action === 'editar_movimiento_detalles') {
        $idMov = isset($_POST['id_mov']) ? (int)$_POST['id_mov'] : 0;
        $docMov = !empty($_POST['doc_mov']) ? trim($_POST['doc_mov']) : '';
        $fechaCadu = !empty($_POST['fech_cadu']) ? trim($_POST['fech_cadu']) : null;
        $observaciones = !empty($_POST['obse_mov']) ? trim($_POST['obse_mov']) : null;
        
        if ($idMov <= 0) {
            echo json_encode(['success' => false, 'error' => 'ID de movimiento no válido.']);
            exit;
        }
        
        try {
            $pdo->beginTransaction();
            
            // Obtener el tipo de movimiento para saber si corresponde fecha de caducidad
            $stmt = $pdo->prepare("SELECT tipo_mov FROM mp_almacen_movimientos WHERE id_mov = ?");
            $stmt->execute([$idMov]);
            $tipoMov = $stmt->fetchColumn();
            
            if (!$tipoMov) {
                throw new Exception("El movimiento no existe.");
            }
            
            // Actualizar campos
            if ($tipoMov === 'INGRESO') {
                $stmt = $pdo->prepare("UPDATE mp_almacen_movimientos 
                                       SET doc_mov = ?, fech_cadu = ?, obse_mov = ? 
                                       WHERE id_mov = ?");
                $stmt->execute([$docMov, $fechaCadu, $observaciones, $idMov]);
            } else {
                $stmt = $pdo->prepare("UPDATE mp_almacen_movimientos 
                                       SET doc_mov = ?, obse_mov = ? 
                                       WHERE id_mov = ?");
                $stmt->execute([$docMov, $observaciones, $idMov]);
            }
            
            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Detalles del movimiento actualizados correctamente.']);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
}

echo json_encode(['success' => false, 'error' => 'Acción no permitida']);
?>
