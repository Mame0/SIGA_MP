<?php
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
    echo "Error de conexión: " . $e->getMessage() . "\n";
    exit;
}

// Datos a actualizar por descripción, documento y cantidad (independiente de los IDs de base de datos)
$updates = [
    // ['desc_bien', 'doc_mov', 'cant_mov', 'new_cadu', 'new_obse']
    ['OLEO MATE SINTETICO COLOR AMARILLO', 'NEA S/N', 8, '2021-07-01', 'LOTE 2018'],
    ['OLEO MATE SINTETICO COLOR AZUL ELECTRICO', 'NEA S/N', 11, '2022-07-01', 'LOTE 2018'],
    ['OLEO MATE SINTETICO COLOR GRANITO', 'NEA S/N', 24, '2021-07-01', 'LOTE 2018'],
    ['OLEO MATE SINTETICO COLOR NEGRO', 'NEA S/N', 2, '2021-07-01', 'LOTE 2018'],
    ['PINTURA EN SPRAY COLOR NEGRO BRILLANTE', 'NEA S/N', 3, '2026-08-31', ''],
    ['PINTURA LATEX DE ACABADO MATE COLOR BLANCO X  (1 GALON)', 'NEA S/N', 2, '2025-07-01', 'VENCIDO LOTE 2022'],
    ['PINTURA LATEX DE ACABADO MATE COLOR COLONIAL  X  (1 GALON)', 'NEA S/N', 2, '2025-07-01', 'VENCIDO LOTE 2022'],
    ['PINTURA LATEX DE ACABADO MATE COLOR GRANITO  X  (1 GALON)', 'NEA S/N', 2, '2025-07-01', 'VENCIDO LOTE 2022'],
    ['PINTURA LATEX DE ACABADO MATE COLOR MARFIL  X  (1 GALON)', 'O/C: 298', 3, '2026-07-01', 'VENCIDO LOTE 2023'],
    ['PINTURA LATEX DE ACABADO MATE COLOR ROJO TEJA X  (1 GALON)', 'NEA S/N', 3, '2025-07-01', 'VENCIDO LOTE 2022'],
    ['PINTURA LATEX DE ACABADO SATINADO COLOR AZUL X  (1 GALON)', 'NEA 3302', 9, '2025-07-01', 'VENCIDO LOTE 2022'],
    ['PINTURA LATEX DE ACABADO SATINADO COLOR AZUL X  (1 GALON)', 'NEA 1106', 4, '2026-07-01', 'VENCIDO LOTE 2023'],
    ['PINTURA LATEX DE ACABADO SATINADO COLOR AZUL X  (1 GALON)', 'O/C: 298', 2, '2026-07-01', 'VENCIDO LOTE 2023'],
    ['PINTURA LATEX DE ACABADO SATINADO COLOR BLANCO INVIERNO X  (1 GALON)', 'NEA S/N', 4, '2025-07-01', 'VENCIDO LOTE 2022'],
    ['PINTURA LATEX DE ACABADO SATINADO COLOR BLANCO X  (1 GALON)', 'O/C: 257', 10, '2027-07-01', 'LOTE 2025'],
    ['PINTURA LATEX DE ACABADO SATINADO COLOR BLANCO X  (4 GALON)', 'NEA S/N', 2, '2027-07-01', 'LOTE 2025'],
    ['PINTURA LATEX DE ACABADO SATINADO COLOR GRIS CLARO X  (4 GALON)', 'NEA S/N', 1, '2027-07-01', 'LOTE 2025'],
    ['PINTURA LATEX MATE COLOR ALBARICOQUE', 'NEA S/N', 3, '2025-07-01', 'VENCIDO LOTE 2022'],
    ['PINTURA LATEX MATE COLOR VERDE PERMANENTE', 'NEA S/N', 3, '2025-07-01', 'VENCIDO LOTE 2022'],
];

echo "Iniciando migración de fechas y lotes en base de datos...\n<br>";

try {
    $pdo->beginTransaction();
    
    // Consulta para buscar el movimiento uniendo con bienes para mayor precisión semántica
    $stmtFind = $pdo->prepare("
        SELECT m.id_mov 
        FROM mp_almacen_movimientos m
        JOIN mp_almacen_bienes b ON m.id_bien = b.id_bien
        WHERE b.desc_bien = ? AND m.doc_mov = ? AND m.cant_mov = ? AND m.tipo_mov = 'INGRESO'
    ");
    
    $stmtUpdate = $pdo->prepare("UPDATE mp_almacen_movimientos SET fech_cadu = ?, obse_mov = ? WHERE id_mov = ?");
    
    $actualizados = 0;
    foreach ($updates as $upd) {
        $desc = $upd[0];
        $doc = $upd[1];
        $cant = $upd[2];
        $cadu = $upd[3];
        $obse = $upd[4];
        
        $stmtFind->execute([$desc, $doc, $cant]);
        $movs = $stmtFind->fetchAll();
        
        if (empty($movs)) {
            echo "Aviso: No se encontró movimiento para: '$desc' (Doc: $doc, Cant: $cant)\n<br>";
            continue;
        }
        
        foreach ($movs as $mov) {
            $stmtUpdate->execute([$cadu, $obse, $mov['id_mov']]);
            echo "Correcto: '$desc' -> Mov ID: {$mov['id_mov']} actualizado (Vence: $cadu, Obs: '$obse')\n<br>";
            $actualizados++;
        }
    }
    
    $pdo->commit();
    echo "\n<br><b>Migración finalizada con éxito. Se actualizaron $actualizados movimientos.</b>\n";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "\n<br><b>Error durante la migración: " . $e->getMessage() . "</b>\n";
}
?>
