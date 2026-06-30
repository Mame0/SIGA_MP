<?php
// Script de actualización de cables 100% Idempotente y seguro
header('Content-Type: text/plain; charset=utf-8');

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
    
    // 1. Verificar el estado actual de un cable de prueba (id 2108)
    $stmt = $pdo->prepare("SELECT pu_actual FROM mp_almacen_inventario WHERE id_bien = 2108 LIMIT 1");
    $stmt->execute();
    $testCable = $stmt->fetch();
    
    if (!$testCable) {
        throw new Exception("No se encontró el cable de prueba con ID 2108.");
    }
    
    $puActual = (float)$testCable['pu_actual'];
    echo "Precio unitario actual del cable de prueba: S/. " . number_format($puActual, 4) . "\n";
    
    if ($puActual <= 10.00) {
        echo "Los cables ya se encuentran en su valor correcto (por metro). No se requiere aplicar cambios nuevamente.\n";
        echo "Operación omitida para evitar duplicación.\n";
        exit;
    }
    
    // Si el precio es mayor a 10.00, está en estado original (rollos)
    echo "Cables detectados en estado original (rollos). Procediendo con la actualización...\n";
    
    $pdo->beginTransaction();
    
    // A. Asegurar unidad en METRO en mp_almacen_bienes
    $updBienes = $pdo->exec("
        UPDATE mp_almacen_bienes 
        SET unid_bien = 'METRO' 
        WHERE desc_bien LIKE '%CABLE%' AND desc_bien LIKE '%X 100%'
    ");
    echo " - Unidades de medida modificadas en mp_almacen_bienes: $updBienes\n";
    
    // B. Multiplicar stock por 100 y dividir precio unitario por 100 en mp_almacen_inventario
    $updInventario = $pdo->exec("
        UPDATE mp_almacen_inventario i
        INNER JOIN mp_almacen_bienes b ON i.id_bien = b.id_bien
        SET i.stock_actual = i.stock_actual * 100,
            i.pu_actual = i.pu_actual / 100.0,
            i.total_actual = i.stock_actual * (i.pu_actual / 100.0)
        WHERE b.desc_bien LIKE '%CABLE%' AND b.desc_bien LIKE '%X 100%'
    ");
    echo " - Registros de inventario actualizados en mp_almacen_inventario: $updInventario\n";
    
    // C. Multiplicar cantidad por 100 y dividir precio unitario por 100 en mp_almacen_movimientos
    $updMovs = $pdo->exec("
        UPDATE mp_almacen_movimientos m
        INNER JOIN mp_almacen_bienes b ON m.id_bien = b.id_bien
        SET m.cant_mov = m.cant_mov * 100,
            m.pu_mov = m.pu_mov / 100.0,
            m.total_mov = m.cant_mov * (m.pu_mov / 100.0)
        WHERE b.desc_bien LIKE '%CABLE%' AND b.desc_bien LIKE '%X 100%'
    ");
    echo " - Movimientos del Kardex actualizados en mp_almacen_movimientos: $updMovs\n";
    
    // D. Asegurar que los totales coincidan exactamente
    $pdo->exec("
        UPDATE mp_almacen_inventario i
        INNER JOIN mp_almacen_bienes b ON i.id_bien = b.id_bien
        SET i.total_actual = i.stock_actual * i.pu_actual
        WHERE b.desc_bien LIKE '%CABLE%' AND b.desc_bien LIKE '%X 100%'
    ");
    
    $pdo->exec("
        UPDATE mp_almacen_movimientos m
        INNER JOIN mp_almacen_bienes b ON m.id_bien = b.id_bien
        SET m.total_mov = m.cant_mov * m.pu_mov
        WHERE b.desc_bien LIKE '%CABLE%' AND b.desc_bien LIKE '%X 100%'
    ");
    
    $pdo->commit();
    echo "\n¡Actualización completada de manera exitosa y segura!\n";
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "ERROR: " . $e->getMessage() . "\n";
}
