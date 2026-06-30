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

/* ---------------------------------------------------------------------
 *  Helpers
 * ------------------------------------------------------------------- */

// Lee un parámetro de configuración desde mp_admi_conf con fallback.
function vaca_config($pdo, $nombre, $default) {
    try {
        $st = $pdo->prepare("SELECT valo_conf FROM mp_admi_conf WHERE nomb_conf = ? LIMIT 1");
        $st->execute([$nombre]);
        $v = $st->fetchColumn();
        return ($v !== false && $v !== '') ? (int)$v : (int)$default;
    } catch (Exception $e) {
        return (int)$default;
    }
}

// Saldo de un periodo: asignados - SUM(dias de tramos ACTIVOS).
function vaca_saldo_periodo($pdo, $id_periodo) {
    $st = $pdo->prepare("SELECT dias_asignados FROM mp_vaca_periodo WHERE id_periodo = ?");
    $st->execute([$id_periodo]);
    $asig = $st->fetchColumn();
    if ($asig === false) return null;
    $st = $pdo->prepare("SELECT COALESCE(SUM(dias),0) FROM mp_vaca_tramo WHERE id_periodo = ? AND estado = 'ACTIVO'");
    $st->execute([$id_periodo]);
    $usados = (int)$st->fetchColumn();
    return ['dias_asignados' => (int)$asig, 'usados' => $usados, 'saldo' => (int)$asig - $usados];
}

// Recalcula y persiste el estado del periodo (INCOMPLETO/COMPLETO).
function vaca_recalcular_estado($pdo, $id_periodo) {
    $s = vaca_saldo_periodo($pdo, $id_periodo);
    if ($s === null) return null;
    $estado = ($s['saldo'] === 0) ? 'COMPLETO' : 'INCOMPLETO';
    $st = $pdo->prepare("UPDATE mp_vaca_periodo SET estado = ? WHERE id_periodo = ? AND estado <> 'CERRADO'");
    $st->execute([$estado, $id_periodo]);
    return $estado;
}

// Determina el siguiente periodo del catálogo que el conductor puede ABRIR,
// aplicando la regla "actual + 1 futuro" + guarda por aniversario.
// Devuelve ['cat'=>fila|null, 'motivo'=>string].
function vaca_siguiente_creable($pdo, $id_conductor) {
    $st = $pdo->prepare("SELECT fecha_ingreso FROM mp_vaca_conductor WHERE id_conductor = ?");
    $st->execute([$id_conductor]);
    $fing = $st->fetchColumn();
    if ($fing === false) return ['cat' => null, 'motivo' => 'Conductor no encontrado.'];
    $anioIngreso = (int)substr($fing, 0, 4);

    $cat = $pdo->query("SELECT * FROM mp_vaca_periodo_cat WHERE estado = 1 ORDER BY orden")->fetchAll();

    // Instancias existentes del conductor (por id_periodo_cat)
    $st = $pdo->prepare("SELECT id_periodo_cat, estado FROM mp_vaca_periodo WHERE id_conductor = ?");
    $st->execute([$id_conductor]);
    $inst = [];
    foreach ($st->fetchAll() as $r) $inst[(int)$r['id_periodo_cat']] = $r['estado'];

    // Orden máximo ya instanciado y nº de periodos abiertos (no CERRADO)
    $maxOrden = 0; $abiertos = 0;
    foreach ($cat as $c) {
        $cid = (int)$c['id_periodo_cat'];
        if (isset($inst[$cid])) {
            $maxOrden = max($maxOrden, (int)$c['orden']);
            if ($inst[$cid] !== 'CERRADO') $abiertos++;
        }
    }

    if ($abiertos >= 2) {
        return ['cat' => null, 'motivo' => 'Solo puede tener 2 periodos abiertos a la vez (actual + 1 futuro).'];
    }

    // Candidato = siguiente en secuencia; si no hay instancias, el primero con derecho generado.
    $candidato = null;
    foreach ($cat as $c) {
        if ($maxOrden === 0) {
            if ((int)$c['anio_inicio'] >= $anioIngreso + 1) { $candidato = $c; break; }
        } else if ((int)$c['orden'] === $maxOrden + 1) {
            $candidato = $c; break;
        }
    }

    if (!$candidato) {
        return ['cat' => null, 'motivo' => 'No hay un periodo siguiente disponible en el catálogo.'];
    }
    if ((int)$candidato['anio_inicio'] < $anioIngreso + 1) {
        return ['cat' => null, 'motivo' => 'El conductor aún no genera derecho a este periodo (menos de 1 año de servicio).'];
    }
    return ['cat' => $candidato, 'motivo' => ''];
}

// Verifica el tope de ausencias simultáneas de la flota para un conjunto de fechas.
// Devuelve las fechas en conflicto (donde sumar a este conductor superaría el tope).
function vaca_validar_cupo($pdo, $fechas, $idConductor, $tope) {
    if (empty($fechas)) return [];
    $place = implode(',', array_fill(0, count($fechas), '?'));
    $st = $pdo->prepare(
        "SELECT fecha, COUNT(DISTINCT id_conductor) AS ocupados
         FROM mp_vaca_dia
         WHERE estado = 'ACTIVO' AND id_conductor <> ? AND fecha IN ($place)
         GROUP BY fecha"
    );
    $st->execute(array_merge([$idConductor], $fechas));
    $conflictos = [];
    foreach ($st->fetchAll() as $r) {
        if ((int)$r['ocupados'] + 1 > $tope) {
            $conflictos[] = ['fecha' => $r['fecha'], 'ocupados' => (int)$r['ocupados']];
        }
    }
    return $conflictos;
}

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

    if ($action === 'obtener_periodos') {
        $idCond = isset($_GET['id_conductor']) ? (int)$_GET['id_conductor'] : 0;
        if ($idCond <= 0) {
            echo json_encode(['success' => false, 'error' => 'Conductor no válido.']);
            exit;
        }
        $st = $pdo->prepare(
            "SELECT id_periodo, id_periodo_cat, etiqueta, dias_asignados, estado
             FROM mp_vaca_periodo WHERE id_conductor = ?
             ORDER BY etiqueta"
        );
        $st->execute([$idCond]);
        $periodos = [];
        foreach ($st->fetchAll() as $p) {
            $s = vaca_saldo_periodo($pdo, (int)$p['id_periodo']);
            $p['usados'] = $s['usados'];
            $p['saldo']  = $s['saldo'];
            $periodos[] = $p;
        }
        echo json_encode(['success' => true, 'periodos' => $periodos]);
        exit;
    }

    if ($action === 'periodos_programables') {
        $idCond = isset($_GET['id_conductor']) ? (int)$_GET['id_conductor'] : 0;
        if ($idCond <= 0) {
            echo json_encode(['success' => false, 'error' => 'Conductor no válido.']);
            exit;
        }
        $sig = vaca_siguiente_creable($pdo, $idCond);
        echo json_encode([
            'success'    => true,
            'puede_crear' => $sig['cat'] ? [
                'id_periodo_cat' => (int)$sig['cat']['id_periodo_cat'],
                'etiqueta'       => $sig['cat']['etiqueta']
            ] : null,
            'motivo'     => $sig['motivo']
        ]);
        exit;
    }

    if ($action === 'obtener_tramos') {
        $idPer = isset($_GET['id_periodo']) ? (int)$_GET['id_periodo'] : 0;
        if ($idPer <= 0) {
            echo json_encode(['success' => false, 'error' => 'Periodo no válido.']);
            exit;
        }
        $st = $pdo->prepare(
            "SELECT id_tramo, fecha_inicio, fecha_fin, dias
             FROM mp_vaca_tramo
             WHERE id_periodo = ? AND estado = 'ACTIVO'
             ORDER BY fecha_inicio"
        );
        $st->execute([$idPer]);
        $tramos = $st->fetchAll();
        $s = vaca_saldo_periodo($pdo, $idPer);
        echo json_encode(['success' => true, 'tramos' => $tramos, 'saldo' => $s]);
        exit;
    }

    if ($action === 'calendario_ocupacion') {
        $desde = isset($_GET['desde']) ? trim($_GET['desde']) : '';
        $hasta = isset($_GET['hasta']) ? trim($_GET['hasta']) : '';
        $dd = DateTime::createFromFormat('Y-m-d', $desde);
        $dh = DateTime::createFromFormat('Y-m-d', $hasta);
        if (!$dd || !$dh) {
            echo json_encode(['success' => false, 'error' => 'Rango de fechas inválido.']);
            exit;
        }
        $st = $pdo->prepare(
            "SELECT d.fecha,
                    COUNT(DISTINCT d.id_conductor) AS ocupados,
                    GROUP_CONCAT(DISTINCT CONCAT(c.appat,' ',c.apmat,', ',c.nombres)
                                 ORDER BY c.appat SEPARATOR '|') AS nombres
             FROM mp_vaca_dia d
             JOIN mp_vaca_conductor c ON c.id_conductor = d.id_conductor
             WHERE d.estado = 'ACTIVO' AND d.fecha BETWEEN ? AND ?
             GROUP BY d.fecha
             ORDER BY d.fecha"
        );
        $st->execute([$desde, $hasta]);
        $dias = [];
        foreach ($st->fetchAll() as $r) {
            $dias[$r['fecha']] = [
                'ocupados'    => (int)$r['ocupados'],
                'conductores' => $r['nombres'] ? explode('|', $r['nombres']) : []
            ];
        }
        echo json_encode([
            'success' => true,
            'tope'    => vaca_config($pdo, 'VACA_TOPE_FLOTA', 4),
            'dias'    => $dias
        ]);
        exit;
    }

    if ($action === 'obtener_historial') {
        $idCond = isset($_GET['id_conductor']) ? (int)$_GET['id_conductor'] : 0;
        $idPer  = isset($_GET['id_periodo']) ? (int)$_GET['id_periodo'] : 0;
        if ($idCond <= 0 && $idPer <= 0) {
            echo json_encode(['success' => false, 'error' => 'Indique conductor o periodo.']);
            exit;
        }

        $cond = []; $bind = [];
        if ($idCond > 0) { $cond[] = 'h.id_conductor = ?'; $bind[] = $idCond; }
        if ($idPer  > 0) { $cond[] = 'h.id_periodo = ?';   $bind[] = $idPer; }
        $where = implode(' AND ', $cond);

        $st = $pdo->prepare(
            "SELECT h.id_hist, h.id_conductor, h.id_periodo, h.accion,
                    h.detalle_antes, h.detalle_despues,
                    h.dias_liberados, h.dias_consumidos, h.saldo_resultante,
                    h.id_oper, h.fecha_hora,
                    p.etiqueta,
                    TRIM(CONCAT(COALESCE(o.appa_oper,''),' ',COALESCE(o.apma_oper,''),' ',COALESCE(o.nomb_oper,''))) AS operador
             FROM mp_vaca_historial h
             LEFT JOIN mp_vaca_periodo p ON p.id_periodo = h.id_periodo
             LEFT JOIN mp_admi_oper    o ON o.iden_oper  = h.id_oper
             WHERE $where
             ORDER BY h.fecha_hora DESC, h.id_hist DESC"
        );
        $st->execute($bind);
        $hist = [];
        foreach ($st->fetchAll() as $r) {
            $r['detalle_antes']   = $r['detalle_antes']   ? json_decode($r['detalle_antes'], true)   : null;
            $r['detalle_despues'] = $r['detalle_despues'] ? json_decode($r['detalle_despues'], true) : null;
            $hist[] = $r;
        }
        echo json_encode(['success' => true, 'historial' => $hist]);
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

    if ($action === 'generar_periodo') {
        $idCond = isset($_POST['id_conductor']) ? (int)$_POST['id_conductor'] : 0;
        $idCat  = isset($_POST['id_periodo_cat']) ? (int)$_POST['id_periodo_cat'] : 0;
        if ($idCond <= 0 || $idCat <= 0) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos.']);
            exit;
        }
        try {
            // Validar contra la regla actual+1 futuro / aniversario
            $sig = vaca_siguiente_creable($pdo, $idCond);
            if (!$sig['cat'] || (int)$sig['cat']['id_periodo_cat'] !== $idCat) {
                echo json_encode(['success' => false, 'error' => $sig['motivo'] ?: 'Ese periodo no puede abrirse todavía.']);
                exit;
            }
            $dias = vaca_config($pdo, 'VACA_DIAS_PERIODO', 30);
            $st = $pdo->prepare(
                "INSERT INTO mp_vaca_periodo (id_conductor, id_periodo_cat, etiqueta, dias_asignados, estado)
                 VALUES (?, ?, ?, ?, 'INCOMPLETO')"
            );
            $st->execute([$idCond, $idCat, $sig['cat']['etiqueta'], $dias]);
            echo json_encode([
                'success'    => true,
                'message'    => 'Periodo ' . $sig['cat']['etiqueta'] . ' generado.',
                'id_periodo' => (int)$pdo->lastInsertId()
            ]);
        } catch (PDOException $e) {
            // Choque con UNIQUE(id_conductor, id_periodo_cat)
            if ($e->getCode() === '23000') {
                echo json_encode(['success' => false, 'error' => 'El conductor ya tiene ese periodo.']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
            }
        }
        exit;
    }

    if ($action === 'guardar_programacion') {
        $idPer = isset($_POST['id_periodo']) ? (int)$_POST['id_periodo'] : 0;
        $tramosRaw = isset($_POST['tramos']) ? $_POST['tramos'] : '';
        $tramos = json_decode($tramosRaw, true);
        $idOper = isset($_SESSION['iden_oper']) ? (int)$_SESSION['iden_oper'] : 0;

        if ($idPer <= 0 || !is_array($tramos) || count($tramos) === 0) {
            echo json_encode(['success' => false, 'error' => 'Debe indicar el periodo y al menos un tramo.']);
            exit;
        }

        try {
            // Periodo válido y abierto
            $st = $pdo->prepare("SELECT id_conductor, estado FROM mp_vaca_periodo WHERE id_periodo = ?");
            $st->execute([$idPer]);
            $per = $st->fetch();
            if (!$per) {
                throw new Exception('El periodo no existe.');
            }
            if ($per['estado'] === 'CERRADO') {
                throw new Exception('El periodo está cerrado y no admite cambios.');
            }
            $idCond = (int)$per['id_conductor'];

            // Normalizar y validar tramos; acumular fechas-día
            $tramosNorm = [];
            $fechasNuevas = [];
            foreach ($tramos as $t) {
                $fi = isset($t['fecha_inicio']) ? trim($t['fecha_inicio']) : '';
                $ff = isset($t['fecha_fin']) ? trim($t['fecha_fin']) : '';
                $di = DateTime::createFromFormat('Y-m-d', $fi);
                $df = DateTime::createFromFormat('Y-m-d', $ff);
                if (!$di || !$df || $di->format('Y-m-d') !== $fi || $df->format('Y-m-d') !== $ff) {
                    throw new Exception('Fecha inválida en un tramo.');
                }
                if ($df < $di) {
                    throw new Exception('La fecha de fin no puede ser anterior al inicio.');
                }
                $dias = (int)$di->diff($df)->days + 1;
                $diasFechas = [];
                $cursor = clone $di;
                while ($cursor <= $df) {
                    $f = $cursor->format('Y-m-d');
                    if (in_array($f, $fechasNuevas, true)) {
                        throw new Exception('Hay tramos que se solapan entre sí (' . $f . ').');
                    }
                    $fechasNuevas[] = $f;
                    $diasFechas[] = $f;
                    $cursor->modify('+1 day');
                }
                $tramosNorm[] = ['fi' => $fi, 'ff' => $ff, 'dias' => $dias, 'fechas' => $diasFechas];
            }

            // Validar saldo: total nuevos <= saldo disponible
            $saldoInfo = vaca_saldo_periodo($pdo, $idPer);
            $totalNuevos = array_sum(array_column($tramosNorm, 'dias'));
            if ($totalNuevos > $saldoInfo['saldo']) {
                throw new Exception('Excede los días disponibles. Saldo: ' . $saldoInfo['saldo'] . ', intenta agregar: ' . $totalNuevos . '.');
            }

            // Auto-solape: el conductor no puede estar ausente dos veces el mismo día
            $place = implode(',', array_fill(0, count($fechasNuevas), '?'));
            $st = $pdo->prepare(
                "SELECT DISTINCT fecha FROM mp_vaca_dia
                 WHERE estado = 'ACTIVO' AND id_conductor = ? AND fecha IN ($place)"
            );
            $st->execute(array_merge([$idCond], $fechasNuevas));
            $choque = $st->fetchAll(PDO::FETCH_COLUMN);
            if (!empty($choque)) {
                throw new Exception('El conductor ya tiene vacaciones en: ' . implode(', ', $choque) . '.');
            }

            // Tope de flota: máximo de ausencias simultáneas por día
            $tope = vaca_config($pdo, 'VACA_TOPE_FLOTA', 4);
            $conflictos = vaca_validar_cupo($pdo, $fechasNuevas, $idCond, $tope);
            if (!empty($conflictos)) {
                $fechasTxt = array_map(fn($c) => $c['fecha'], $conflictos);
                echo json_encode([
                    'success'     => false,
                    'tipo'        => 'cupo_flota',
                    'tope'        => $tope,
                    'conflictos'  => $conflictos,
                    'error'       => 'Se superaría el tope de ' . $tope . ' conductores de vacaciones el mismo día en: ' . implode(', ', $fechasTxt) . '.'
                ]);
                exit;
            }

            // Persistir
            $pdo->beginTransaction();
            $insTramo = $pdo->prepare(
                "INSERT INTO mp_vaca_tramo (id_periodo, id_conductor, fecha_inicio, fecha_fin, dias, estado, id_oper_reg)
                 VALUES (?, ?, ?, ?, ?, 'ACTIVO', ?)"
            );
            $insDia = $pdo->prepare(
                "INSERT INTO mp_vaca_dia (id_tramo, id_conductor, id_periodo, fecha, estado)
                 VALUES (?, ?, ?, ?, 'ACTIVO')"
            );
            foreach ($tramosNorm as $t) {
                $insTramo->execute([$idPer, $idCond, $t['fi'], $t['ff'], $t['dias'], $idOper]);
                $idTramo = (int)$pdo->lastInsertId();
                foreach ($t['fechas'] as $f) {
                    $insDia->execute([$idTramo, $idCond, $idPer, $f]);
                }
            }
            $estado = vaca_recalcular_estado($pdo, $idPer);
            $nuevoSaldo = vaca_saldo_periodo($pdo, $idPer);

            // Historial
            $insHist = $pdo->prepare(
                "INSERT INTO mp_vaca_historial
                    (id_conductor, id_periodo, accion, detalle_despues, dias_liberados, dias_consumidos, saldo_resultante, id_oper)
                 VALUES (?, ?, 'CREA', ?, 0, ?, ?, ?)"
            );
            $insHist->execute([
                $idCond, $idPer,
                json_encode(array_map(fn($t) => ['inicio' => $t['fi'], 'fin' => $t['ff'], 'dias' => $t['dias']], $tramosNorm)),
                $totalNuevos, $nuevoSaldo['saldo'], $idOper
            ]);

            $pdo->commit();

            $aviso = $nuevoSaldo['saldo'] > 0
                ? 'Faltan ' . $nuevoSaldo['saldo'] . ' día(s) por programar; debe completar 30.'
                : '';
            echo json_encode([
                'success' => true,
                'message' => 'Programación guardada.',
                'estado'  => $estado,
                'saldo'   => $nuevoSaldo,
                'aviso'   => $aviso
            ]);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'reprogramar') {
        $idPer = isset($_POST['id_periodo']) ? (int)$_POST['id_periodo'] : 0;
        $tramosRaw = isset($_POST['tramos']) ? $_POST['tramos'] : '';
        $tramos = json_decode($tramosRaw, true);
        $idOper = isset($_SESSION['iden_oper']) ? (int)$_SESSION['iden_oper'] : 0;

        if ($idPer <= 0 || !is_array($tramos) || count($tramos) === 0) {
            echo json_encode(['success' => false, 'error' => 'Debe indicar el periodo y al menos un tramo.']);
            exit;
        }

        try {
            // Normalizar y validar el set NUEVO (sin tocar BD todavía)
            $tramosNorm = [];
            $fechasNuevas = [];
            foreach ($tramos as $t) {
                $fi = isset($t['fecha_inicio']) ? trim($t['fecha_inicio']) : '';
                $ff = isset($t['fecha_fin']) ? trim($t['fecha_fin']) : '';
                $di = DateTime::createFromFormat('Y-m-d', $fi);
                $df = DateTime::createFromFormat('Y-m-d', $ff);
                if (!$di || !$df || $di->format('Y-m-d') !== $fi || $df->format('Y-m-d') !== $ff) {
                    throw new Exception('Fecha inválida en un tramo.');
                }
                if ($df < $di) {
                    throw new Exception('La fecha de fin no puede ser anterior al inicio.');
                }
                $dias = (int)$di->diff($df)->days + 1;
                $diasFechas = [];
                $cursor = clone $di;
                while ($cursor <= $df) {
                    $f = $cursor->format('Y-m-d');
                    if (in_array($f, $fechasNuevas, true)) {
                        throw new Exception('Hay tramos que se solapan entre sí (' . $f . ').');
                    }
                    $fechasNuevas[] = $f;
                    $diasFechas[] = $f;
                    $cursor->modify('+1 day');
                }
                $tramosNorm[] = ['fi' => $fi, 'ff' => $ff, 'dias' => $dias, 'fechas' => $diasFechas];
            }

            $pdo->beginTransaction();

            // Bloquear el periodo y validar que admite cambios
            $st = $pdo->prepare("SELECT id_conductor, dias_asignados, estado FROM mp_vaca_periodo WHERE id_periodo = ? FOR UPDATE");
            $st->execute([$idPer]);
            $per = $st->fetch();
            if (!$per) {
                throw new Exception('El periodo no existe.');
            }
            if ($per['estado'] === 'CERRADO') {
                throw new Exception('El periodo está cerrado y no admite cambios.');
            }
            $idCond = (int)$per['id_conductor'];
            $diasAsig = (int)$per['dias_asignados'];

            // Bloquear y capturar el set ACTIVO actual (snapshot "antes")
            $st = $pdo->prepare(
                "SELECT id_tramo, fecha_inicio, fecha_fin, dias
                 FROM mp_vaca_tramo
                 WHERE id_periodo = ? AND estado = 'ACTIVO'
                 ORDER BY fecha_inicio FOR UPDATE"
            );
            $st->execute([$idPer]);
            $viejos = $st->fetchAll();
            $diasLiberados = array_sum(array_column($viejos, 'dias'));
            $idOrigen = !empty($viejos) ? (int)min(array_column($viejos, 'id_tramo')) : null;

            // Liberar: marcar viejos REEMPLAZADO y eliminar sus días (vuelven al saldo)
            if (!empty($viejos)) {
                $idsViejos = array_map(fn($v) => (int)$v['id_tramo'], $viejos);
                $place = implode(',', array_fill(0, count($idsViejos), '?'));
                $pdo->prepare("DELETE FROM mp_vaca_dia WHERE id_tramo IN ($place)")->execute($idsViejos);
                $pdo->prepare("UPDATE mp_vaca_tramo SET estado = 'REEMPLAZADO' WHERE id_tramo IN ($place)")->execute($idsViejos);
            }

            // Validar saldo: total nuevo no puede exceder los días asignados
            $totalNuevos = array_sum(array_column($tramosNorm, 'dias'));
            if ($totalNuevos > $diasAsig) {
                throw new Exception('Excede los días asignados. Asignados: ' . $diasAsig . ', programados: ' . $totalNuevos . '.');
            }

            // Auto-solape: el conductor no puede estar ausente dos veces el mismo día
            // (los días de ESTE periodo ya se eliminaron; solo quedan los de otros periodos)
            $place = implode(',', array_fill(0, count($fechasNuevas), '?'));
            $st = $pdo->prepare(
                "SELECT DISTINCT fecha FROM mp_vaca_dia
                 WHERE estado = 'ACTIVO' AND id_conductor = ? AND fecha IN ($place)"
            );
            $st->execute(array_merge([$idCond], $fechasNuevas));
            $choque = $st->fetchAll(PDO::FETCH_COLUMN);
            if (!empty($choque)) {
                throw new Exception('El conductor ya tiene vacaciones (en otro periodo) en: ' . implode(', ', $choque) . '.');
            }

            // Tope de flota sobre el set nuevo (el conductor ya está liberado)
            $tope = vaca_config($pdo, 'VACA_TOPE_FLOTA', 4);
            $conflictos = vaca_validar_cupo($pdo, $fechasNuevas, $idCond, $tope);
            if (!empty($conflictos)) {
                $pdo->rollBack();
                $fechasTxt = array_map(fn($c) => $c['fecha'], $conflictos);
                echo json_encode([
                    'success'    => false,
                    'tipo'       => 'cupo_flota',
                    'tope'       => $tope,
                    'conflictos' => $conflictos,
                    'error'      => 'Se superaría el tope de ' . $tope . ' conductores de vacaciones el mismo día en: ' . implode(', ', $fechasTxt) . '.'
                ]);
                exit;
            }

            // Insertar tramos nuevos (encadenados al origen) y explotar sus días
            $insTramo = $pdo->prepare(
                "INSERT INTO mp_vaca_tramo (id_periodo, id_conductor, fecha_inicio, fecha_fin, dias, estado, id_tramo_origen, id_oper_reg)
                 VALUES (?, ?, ?, ?, ?, 'ACTIVO', ?, ?)"
            );
            $insDia = $pdo->prepare(
                "INSERT INTO mp_vaca_dia (id_tramo, id_conductor, id_periodo, fecha, estado)
                 VALUES (?, ?, ?, ?, 'ACTIVO')"
            );
            foreach ($tramosNorm as $t) {
                $insTramo->execute([$idPer, $idCond, $t['fi'], $t['ff'], $t['dias'], $idOrigen, $idOper]);
                $idTramo = (int)$pdo->lastInsertId();
                foreach ($t['fechas'] as $f) {
                    $insDia->execute([$idTramo, $idCond, $idPer, $f]);
                }
            }

            $estado = vaca_recalcular_estado($pdo, $idPer);
            $nuevoSaldo = vaca_saldo_periodo($pdo, $idPer);

            // Historial
            $insHist = $pdo->prepare(
                "INSERT INTO mp_vaca_historial
                    (id_conductor, id_periodo, accion, detalle_antes, detalle_despues, dias_liberados, dias_consumidos, saldo_resultante, id_oper)
                 VALUES (?, ?, 'REPROGRAMA', ?, ?, ?, ?, ?, ?)"
            );
            $insHist->execute([
                $idCond, $idPer,
                json_encode(array_map(fn($v) => ['inicio' => $v['fecha_inicio'], 'fin' => $v['fecha_fin'], 'dias' => (int)$v['dias']], $viejos)),
                json_encode(array_map(fn($t) => ['inicio' => $t['fi'], 'fin' => $t['ff'], 'dias' => $t['dias']], $tramosNorm)),
                $diasLiberados, $totalNuevos, $nuevoSaldo['saldo'], $idOper
            ]);

            $pdo->commit();

            $aviso = $nuevoSaldo['saldo'] > 0
                ? 'Faltan ' . $nuevoSaldo['saldo'] . ' día(s) por programar; debe completar 30.'
                : '';
            echo json_encode([
                'success'         => true,
                'message'         => 'Reprogramación aplicada.',
                'estado'          => $estado,
                'saldo'           => $nuevoSaldo,
                'dias_liberados'  => $diasLiberados,
                'dias_consumidos' => $totalNuevos,
                'aviso'           => $aviso
            ]);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'anular_tramo') {
        $idTramo = isset($_POST['id_tramo']) ? (int)$_POST['id_tramo'] : 0;
        $idOper  = isset($_SESSION['iden_oper']) ? (int)$_SESSION['iden_oper'] : 0;
        if ($idTramo <= 0) {
            echo json_encode(['success' => false, 'error' => 'Tramo no válido.']);
            exit;
        }

        try {
            $pdo->beginTransaction();

            // Bloquear el tramo y validar
            $st = $pdo->prepare(
                "SELECT id_tramo, id_periodo, id_conductor, fecha_inicio, fecha_fin, dias, estado
                 FROM mp_vaca_tramo WHERE id_tramo = ? FOR UPDATE"
            );
            $st->execute([$idTramo]);
            $tr = $st->fetch();
            if (!$tr) {
                throw new Exception('El tramo no existe.');
            }
            if ($tr['estado'] !== 'ACTIVO') {
                throw new Exception('El tramo no está activo; no puede anularse.');
            }
            $idPer  = (int)$tr['id_periodo'];
            $idCond = (int)$tr['id_conductor'];

            // El periodo no debe estar cerrado
            $st = $pdo->prepare("SELECT estado FROM mp_vaca_periodo WHERE id_periodo = ? FOR UPDATE");
            $st->execute([$idPer]);
            if ($st->fetchColumn() === 'CERRADO') {
                throw new Exception('El periodo está cerrado y no admite cambios.');
            }

            // Liberar: eliminar días y marcar el tramo ANULADO
            $pdo->prepare("DELETE FROM mp_vaca_dia WHERE id_tramo = ?")->execute([$idTramo]);
            $pdo->prepare("UPDATE mp_vaca_tramo SET estado = 'ANULADO' WHERE id_tramo = ?")->execute([$idTramo]);

            $estado = vaca_recalcular_estado($pdo, $idPer);
            $nuevoSaldo = vaca_saldo_periodo($pdo, $idPer);

            // Historial
            $insHist = $pdo->prepare(
                "INSERT INTO mp_vaca_historial
                    (id_conductor, id_periodo, accion, detalle_antes, detalle_despues, dias_liberados, dias_consumidos, saldo_resultante, id_oper)
                 VALUES (?, ?, 'ANULA', ?, NULL, ?, 0, ?, ?)"
            );
            $insHist->execute([
                $idCond, $idPer,
                json_encode([['inicio' => $tr['fecha_inicio'], 'fin' => $tr['fecha_fin'], 'dias' => (int)$tr['dias']]]),
                (int)$tr['dias'], $nuevoSaldo['saldo'], $idOper
            ]);

            $pdo->commit();

            echo json_encode([
                'success'        => true,
                'message'        => 'Tramo anulado. Se liberaron ' . (int)$tr['dias'] . ' día(s).',
                'estado'         => $estado,
                'saldo'          => $nuevoSaldo,
                'dias_liberados' => (int)$tr['dias']
            ]);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
}

echo json_encode(['success' => false, 'error' => 'Acción no permitida']);
?>
