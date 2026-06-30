<?php
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
    echo "Conexión a la base de datos establecida con éxito.\n";
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage() . "\n");
}

try {
    $pdo->beginTransaction();

    // 1. Asegurar o actualizar el Almacén 1 a "Almacén Principal"
    echo "Actualizando nombre y ubicación del Almacén 1...\n";
    $stmt = $pdo->prepare("INSERT INTO mp_almacen_locales (id_almacen, nomb_almacen, ubig_almacen, esta_almacen) 
                           VALUES (1, 'Almacén Principal', 'Sede Central Arequipa', 1)
                           ON DUPLICATE KEY UPDATE nomb_almacen = 'Almacén Principal', ubig_almacen = 'Sede Central Arequipa', esta_almacen = 1");
    $stmt->execute();

    // 2. Fusionar inventario de Almacén 2 a Almacén 1
    echo "Obteniendo inventario del Almacén 2...\n";
    $stmt = $pdo->query("SELECT id_bien, stock_actual, pu_actual, total_actual FROM mp_almacen_inventario WHERE id_almacen = 2");
    $itemsAlmacen2 = $stmt->fetchAll();

    foreach ($itemsAlmacen2 as $item) {
        $idBien = (int)$item['id_bien'];
        $stock2 = (int)$item['stock_actual'];
        $total2 = (float)$item['total_actual'];

        // Verificar si existe en Almacén 1
        $checkStmt = $pdo->prepare("SELECT stock_actual, total_actual FROM mp_almacen_inventario WHERE id_almacen = 1 AND id_bien = ?");
        $checkStmt->execute([$idBien]);
        $itemAlmacen1 = $checkStmt->fetch();

        if ($itemAlmacen1) {
            // Existe, por ende sumamos stock y total y recalculamos precio unitario
            $newStock = (int)$itemAlmacen1['stock_actual'] + $stock2;
            $newTotal = (float)$itemAlmacen1['total_actual'] + $total2;
            $newPU = $newStock > 0 ? $newTotal / $newStock : 0.0000;

            echo "Fusionando Bien ID {$idBien}: sumando {$stock2} unidades al Almacén 1 (Stock resultante: {$newStock})...\n";
            $updateStmt = $pdo->prepare("UPDATE mp_almacen_inventario 
                                         SET stock_actual = ?, pu_actual = ?, total_actual = ? 
                                         WHERE id_almacen = 1 AND id_bien = ?");
            $updateStmt->execute([$newStock, $newPU, $newTotal, $idBien]);
        } else {
            // No existe, insertamos nuevo registro
            echo "Insertando Bien ID {$idBien} en Almacén 1 desde Almacén 2...\n";
            $insertStmt = $pdo->prepare("INSERT INTO mp_almacen_inventario (id_almacen, id_bien, stock_actual, pu_actual, total_actual) 
                                         VALUES (1, ?, ?, ?, ?)");
            $insertStmt->execute([$idBien, $stock2, $item['pu_actual'], $total2]);
        }
    }

    // 3. Mover todos los movimientos históricos del Almacén 2 al Almacén 1
    echo "Actualizando todos los movimientos del Almacén 2 al Almacén 1...\n";
    $stmtUpdateMovs = $pdo->prepare("UPDATE mp_almacen_movimientos SET id_almacen = 1 WHERE id_almacen = 2");
    $stmtUpdateMovs->execute();
    $movsCount = $stmtUpdateMovs->rowCount();
    echo "Se actualizaron {$movsCount} movimientos históricos.\n";

    // 4. Limpiar Almacén 2 del inventario y de locales
    echo "Eliminando registros de inventario huérfanos del Almacén 2...\n";
    $pdo->exec("DELETE FROM mp_almacen_inventario WHERE id_almacen = 2");

    echo "Eliminando Almacén 2 de locales...\n";
    $pdo->exec("DELETE FROM mp_almacen_locales WHERE id_almacen = 2");

    $pdo->commit();
    echo "¡Fusión de base de datos completada con éxito!\n";
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "ERROR durante la migración: " . $e->getMessage() . "\n";
}
?>
