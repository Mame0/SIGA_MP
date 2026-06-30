<?php
/* =====================================================================
 *  vacaciones_controller.php — API JSON del Módulo de Vacaciones de Conductores
 *  Estilo: espejo de almacen_controller.php (PDO + transacciones).
 *  Ver diseño en DOC_MODULO_VACACIONES.md
 *  Fase 1: sincronizar_conductores, buscar_conductores, listar_conductores.
 * ===================================================================== */
header('Content-Type: application/json; charset=utf-8');
session_start();

// --- Conexión (mismo patrón que almacen_controller.php) ---
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'mpfnarequipa_siga';

$credentialsFile = 'classes/.credentials/db.php.ini';
if (!file_exists($credentialsFile)) {
    $credentialsFile = '.credentials/db.php.ini';
}
if (file_exists($credentialsFile)) {
    $cred = parse_ini_file($credentialsFile);
    if ($cred) {
        $host = isset($cred['host'])     ? $cred['host']     : $host;
        $user = isset($cred['usuario'])  ? $cred['usuario']  : $user;
        $pass = isset($cred['clave'])    ? $cred['clave']    : $pass;
        $db   = isset($cred['dbnombre']) ? $cred['dbnombre'] : $db;
    }
}
if ($host === 'localhost') {
    $host = '127.0.0.1'; // evita problemas IPv6/IPv4 en Windows
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos: ' . $e->getMessage()]);
    exit;
}

// Código de cargo de los conductores en mp_maes_cargo
define('VACA_CARGO_CONDUCTOR', 6); // 'ASIST. ADM.(CONDUCTOR)'

$action = isset($_GET['action']) ? $_GET['action'] : '';

/* ---------------------------------------------------------------------
 *  GET
 * ------------------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if ($action === 'buscar_conductores') {
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        if (strlen($q) < 2) {
            echo json_encode([]);
            exit;
        }
        $like = "%$q%";
        $stmt = $pdo->prepare(
            "SELECT id_conductor, ndoc, appat, apmat, nombres, regimen,
                    CONCAT(appat,' ',apmat,', ',nombres) AS nombre_completo
             FROM mp_vaca_conductor
             WHERE estado = 1
               AND (CONCAT(appat,' ',apmat,' ',nombres) LIKE ? OR ndoc LIKE ?)
             ORDER BY appat, apmat
             LIMIT 15"
        );
        $stmt->execute([$like, $like]);
        echo json_encode($stmt->fetchAll());
        exit;
    }

    if ($action === 'listar_conductores') {
        $stmt = $pdo->query(
            "SELECT id_conductor, iden_pers, ndoc, appat, apmat, nombres, regimen, fecha_ingreso,
                    CONCAT(appat,' ',apmat,', ',nombres) AS nombre_completo
             FROM mp_vaca_conductor
             WHERE estado = 1
             ORDER BY appat, apmat, nombres"
        );
        echo json_encode(['success' => true, 'conductores' => $stmt->fetchAll()]);
        exit;
    }
}

/* ---------------------------------------------------------------------
 *  POST
 * ------------------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($action === 'sincronizar_conductores') {
        try {
            // Conductores activos desde el maestro de personal (codi_carg = 6)
            $stmt = $pdo->prepare(
                "SELECT p.iden_pers, p.ndoc_pers, p.appa_pers, p.apma_pers, p.nomb_pers,
                        p.fech_ingr, COALESCE(r.x_nombre,'') AS regimen
                 FROM mp_maes_personal p
                 LEFT JOIN mp_maes_regimen_laboral r ON r.n_codigo = p.regi_labo
                 WHERE p.codi_carg = ? AND p.esta_pers = 1"
            );
            $stmt->execute([VACA_CARGO_CONDUCTOR]);
            $personal = $stmt->fetchAll();

            // iden_pers ya existentes en el maestro de vacaciones
            $existentes = [];
            foreach ($pdo->query("SELECT iden_pers FROM mp_vaca_conductor")->fetchAll() as $row) {
                $existentes[(int)$row['iden_pers']] = true;
            }

            $pdo->beginTransaction();

            $sqlIns = $pdo->prepare(
                "INSERT INTO mp_vaca_conductor (iden_pers, ndoc, appat, apmat, nombres, regimen, fecha_ingreso)
                 VALUES (:iden, :ndoc, :appat, :apmat, :nombres, :regimen, :fing)"
            );
            $sqlUpd = $pdo->prepare(
                "UPDATE mp_vaca_conductor
                 SET ndoc=:ndoc, appat=:appat, apmat=:apmat, nombres=:nombres,
                     regimen=:regimen, fecha_ingreso=:fing, estado=1
                 WHERE iden_pers=:iden"
            );

            $nuevos = 0; $actualizados = 0;
            foreach ($personal as $p) {
                $iden = (int)$p['iden_pers'];
                $params = [
                    ':iden'    => $iden,
                    ':ndoc'    => trim($p['ndoc_pers']),
                    ':appat'   => trim($p['appa_pers']),
                    ':apmat'   => trim($p['apma_pers']),
                    ':nombres' => trim($p['nomb_pers']),
                    ':regimen' => trim($p['regimen']),
                    ':fing'    => $p['fech_ingr'],
                ];
                if (isset($existentes[$iden])) {
                    $sqlUpd->execute($params);
                    $actualizados++;
                } else {
                    $sqlIns->execute($params);
                    $nuevos++;
                }
            }

            $pdo->commit();
            echo json_encode([
                'success'      => true,
                'message'      => "Sincronización completa: $nuevos nuevo(s), $actualizados actualizado(s).",
                'nuevos'       => $nuevos,
                'actualizados' => $actualizados,
                'total'        => count($personal)
            ]);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => 'Error al sincronizar: ' . $e->getMessage()]);
        }
        exit;
    }
}

echo json_encode(['success' => false, 'error' => 'Acción no permitida']);
?>
