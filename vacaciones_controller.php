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

/* ---------------------------------------------------------------------
 *  Helpers de importación desde Excel (Fase 6)
 * ------------------------------------------------------------------- */

// Normaliza texto para comparar nombres: mayúsculas, sin tildes, espacios colapsados.
function vaca_norm_txt($s) {
    $s = trim((string)$s);
    $map = ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n',
            'Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ü'=>'U','Ñ'=>'N'];
    $s = strtr($s, $map);
    $s = mb_strtoupper($s, 'UTF-8');
    $s = preg_replace('/\s+/', ' ', $s);
    return trim($s);
}

// Parte "APELLIDOS Y NOMBRES" (formato "APPAT APMAT, NOMBRES") en [appat, apmat, nombres].
function vaca_partir_nombre($nombre) {
    $nombre = preg_replace('/\s+/', ' ', trim((string)$nombre));
    $appat = ''; $apmat = ''; $nombres = '';
    if (strpos($nombre, ',') !== false) {
        list($apellidos, $nom) = array_map('trim', explode(',', $nombre, 2));
        $nombres = $nom;
        $partes = explode(' ', $apellidos);
        $appat = isset($partes[0]) ? $partes[0] : '';
        $apmat = trim(implode(' ', array_slice($partes, 1)));
    } else {
        $partes = explode(' ', $nombre);
        $appat = isset($partes[0]) ? $partes[0] : '';
        $apmat = isset($partes[1]) ? $partes[1] : '';
        $nombres = trim(implode(' ', array_slice($partes, 2)));
    }
    return [mb_strtoupper($appat, 'UTF-8'), mb_strtoupper($apmat, 'UTF-8'), mb_strtoupper($nombres, 'UTF-8')];
}

// Parsea una fecha en varios formatos (dd/mm/yyyy, yyyy-mm-dd, serial Excel) → 'Y-m-d' o null.
function vaca_parse_fecha($v) {
    $v = trim((string)$v);
    if ($v === '') return null;
    if (is_numeric($v) && strpos($v, '/') === false && strpos($v, '-') === false) {
        $n = (float)$v;
        if ($n > 59 && $n < 60000) { // serial de Excel (epoch 1899-12-30)
            $d = DateTime::createFromFormat('Y-m-d', '1899-12-30');
            $d->modify('+' . (int)$n . ' days');
            return $d->format('Y-m-d');
        }
    }
    foreach (['d/m/Y', 'd-m-Y', 'Y-m-d', 'Y/m/d', 'd/m/y'] as $f) {
        $d = DateTime::createFromFormat($f . '|', $v);
        $e = DateTime::getLastErrors();
        if ($d && $e['warning_count'] == 0 && $e['error_count'] == 0) {
            return $d->format('Y-m-d');
        }
    }
    $t = strtotime($v);
    return $t ? date('Y-m-d', $t) : null;
}

// Lee las filas de un archivo subido (.xlsx/.xls con PhpSpreadsheet, .csv manual).
// Devuelve array de filas, cada una array de celdas (strings).
function vaca_leer_archivo($tmpPath, $nombreOriginal) {
    $ext = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
    if ($ext === 'csv' || $ext === 'txt') {
        $filas = [];
        if (($h = fopen($tmpPath, 'r')) !== false) {
            $primera = fgets($h);
            rewind($h);
            $delim = (substr_count($primera, ';') > substr_count($primera, ',')) ? ';'
                   : ((strpos($primera, "\t") !== false) ? "\t" : ',');
            while (($row = fgetcsv($h, 0, $delim)) !== false) $filas[] = $row;
            fclose($h);
        }
        return $filas;
    }
    // Excel vía PhpSpreadsheet
    $autoload = 'spreadsheets/vendor/autoload.php';
    if (!file_exists($autoload)) $autoload = '../spreadsheets/vendor/autoload.php';
    require_once $autoload;
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($tmpPath);
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($tmpPath);
    return $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
}

// Lee filas desde texto pegado (TSV o CSV).
function vaca_leer_texto($texto) {
    $filas = [];
    $lineas = preg_split('/\r\n|\r|\n/', trim((string)$texto));
    foreach ($lineas as $ln) {
        if (trim($ln) === '') continue;
        if (strpos($ln, "\t") !== false)      $filas[] = explode("\t", $ln);
        else if (strpos($ln, ';') !== false)  $filas[] = explode(';', $ln);
        else                                   $filas[] = str_getcsv($ln, ',');
    }
    return $filas;
}

// Procesa las filas crudas: resuelve conductor/periodo, valida y marca estado.
// NO escribe en BD. Devuelve ['filas'=>[...], 'resumen'=>[...]].
// Columnas esperadas (por posición): 0=Apellidos y Nombres, 3=Periodo,
// 4=Fecha inicio, 5=Fecha fin, 6=Total (CARGO=1 y REGIMEN=2 son informativos).
function vaca_procesar_importacion($pdo, $rawRows, $tope) {
    // Mapas de referencia
    $conds = $pdo->query(
        "SELECT id_conductor, appat, apmat, nombres, regimen FROM mp_vaca_conductor WHERE estado = 1"
    )->fetchAll();
    $mapCond = [];
    foreach ($conds as $c) {
        $key = vaca_norm_txt($c['appat'] . ' ' . $c['apmat'] . ', ' . $c['nombres']);
        $mapCond[$key] = $c;
        // clave alterna sin coma
        $mapCond[vaca_norm_txt($c['appat'] . ' ' . $c['apmat'] . ' ' . $c['nombres'])] = $c;
    }
    $cats = $pdo->query("SELECT id_periodo_cat, etiqueta FROM mp_vaca_periodo_cat WHERE estado = 1")->fetchAll();
    $mapCat = [];
    foreach ($cats as $c) $mapCat[vaca_norm_txt($c['etiqueta'])] = $c;

    $filas = [];
    $vistos = [];                 // dedupe intra-archivo: "ckey|cat|fi|ff"
    $porPeriodo = [];             // acumulado de días por "ckey|cat" (para saldo)
    $ocupacionNueva = [];         // fecha => set de ckey (para tope)
    $n_ok = 0; $n_warn = 0; $n_err = 0; $n_dup = 0; $n_nuevo = 0;

    foreach ($rawRows as $i => $r) {
        if (!is_array($r)) continue;
        $r = array_values($r);
        $nombre = isset($r[0]) ? trim((string)$r[0]) : '';
        $regimen = isset($r[2]) ? trim((string)$r[2]) : '';
        $periodoTxt = isset($r[3]) ? trim((string)$r[3]) : '';
        $iniTxt = isset($r[4]) ? trim((string)$r[4]) : '';
        $finTxt = isset($r[5]) ? trim((string)$r[5]) : '';
        $totalTxt = isset($r[6]) ? trim((string)$r[6]) : '';

        // Saltar fila vacía o encabezado
        if ($nombre === '' && $periodoTxt === '' && $iniTxt === '') continue;
        $nkey = vaca_norm_txt($nombre);
        if ($nkey === 'APELLIDOS Y NOMBRES' || strpos($nkey, 'APELLIDOS Y NOMBRE') === 0) continue;

        $fila = [
            'nombre' => $nombre, 'regimen' => $regimen, 'etiqueta' => $periodoTxt,
            'fi' => null, 'ff' => null, 'dias' => 0, 'total_excel' => $totalTxt,
            'id_conductor' => null, 'id_periodo_cat' => null,
            'appat' => '', 'apmat' => '', 'nombres' => '',
            'estado' => 'ok', 'motivo' => ''
        ];

        // Conductor: existente o candidato a tercero (nuevo)
        $cond = isset($mapCond[$nkey]) ? $mapCond[$nkey] : null;
        $esNuevo = false;
        if ($cond) {
            $fila['id_conductor'] = (int)$cond['id_conductor'];
            $ckey  = (int)$cond['id_conductor'];   // clave para acumular en el archivo
            $idcSql = (int)$cond['id_conductor'];   // id real para consultas a BD
        } else {
            $esNuevo = true;
            list($ap, $am, $no) = vaca_partir_nombre($nombre);
            $fila['appat'] = $ap; $fila['apmat'] = $am; $fila['nombres'] = $no;
            if ($ap === '') {
                $fila['estado'] = 'error';
                $fila['motivo'] = 'No se pudo interpretar el nombre para crear el tercero.';
                $filas[] = $fila; $n_err++; continue;
            }
            $ckey  = 'NEW:' . $nkey;                // se agrupa por nombre normalizado
            $idcSql = -1;                           // no existe en BD todavía
        }

        // Periodo
        $cat = isset($mapCat[vaca_norm_txt($periodoTxt)]) ? $mapCat[vaca_norm_txt($periodoTxt)] : null;
        if (!$cat) {
            $fila['estado'] = 'error';
            $fila['motivo'] = 'Periodo "' . $periodoTxt . '" no existe en el catálogo.';
            $filas[] = $fila; $n_err++; continue;
        }
        $fila['id_periodo_cat'] = (int)$cat['id_periodo_cat'];
        $fila['etiqueta'] = $cat['etiqueta'];

        // Fechas
        $fi = vaca_parse_fecha($iniTxt);
        $ff = vaca_parse_fecha($finTxt);
        if (!$fi || !$ff) {
            $fila['estado'] = 'error';
            $fila['motivo'] = 'Fecha inválida (inicio="' . $iniTxt . '", fin="' . $finTxt . '").';
            $filas[] = $fila; $n_err++; continue;
        }
        if ($ff < $fi) {
            $fila['estado'] = 'error';
            $fila['motivo'] = 'La fecha de fin es anterior al inicio.';
            $filas[] = $fila; $n_err++; continue;
        }
        $fila['fi'] = $fi; $fila['ff'] = $ff;
        $di = new DateTime($fi); $df = new DateTime($ff);
        $dias = (int)$di->diff($df)->days + 1;
        $fila['dias'] = $dias;

        // Dedupe intra-archivo
        $dkey = $ckey . '|' . $fila['id_periodo_cat'] . '|' . $fi . '|' . $ff;
        if (isset($vistos[$dkey])) {
            $fila['estado'] = 'dup';
            $fila['motivo'] = 'Fila duplicada dentro del archivo (se omite).';
            $filas[] = $fila; $n_dup++; continue;
        }
        $vistos[$dkey] = true;

        // Duplicado contra la BD (solo aplica a conductores existentes)
        if (!$esNuevo) {
            $st = $pdo->prepare(
                "SELECT COUNT(*) FROM mp_vaca_tramo t
                 JOIN mp_vaca_periodo p ON p.id_periodo = t.id_periodo
                 WHERE t.estado='ACTIVO' AND t.id_conductor=? AND p.id_periodo_cat=?
                   AND t.fecha_inicio=? AND t.fecha_fin=?"
            );
            $st->execute([$idcSql, $fila['id_periodo_cat'], $fi, $ff]);
            if ((int)$st->fetchColumn() > 0) {
                $fila['estado'] = 'dup';
                $fila['motivo'] = 'Ya existe este mismo tramo en el sistema (se omite).';
                $filas[] = $fila; $n_dup++; continue;
            }
        }

        // Saldo del periodo (existentes ACTIVOS + acumulado del archivo + este)
        $pkey = $ckey . '|' . $fila['id_periodo_cat'];
        if (!isset($porPeriodo[$pkey])) {
            $base = 0;
            if (!$esNuevo) {
                $st = $pdo->prepare(
                    "SELECT COALESCE(SUM(t.dias),0) FROM mp_vaca_tramo t
                     JOIN mp_vaca_periodo p ON p.id_periodo = t.id_periodo
                     WHERE t.estado='ACTIVO' AND t.id_conductor=? AND p.id_periodo_cat=?"
                );
                $st->execute([$idcSql, $fila['id_periodo_cat']]);
                $base = (int)$st->fetchColumn();
            }
            $porPeriodo[$pkey] = $base;
        }
        if ($porPeriodo[$pkey] + $dias > 30) {
            $fila['estado'] = 'error';
            $fila['motivo'] = 'Supera 30 días en el periodo ' . $fila['etiqueta']
                            . ' (ya suma ' . $porPeriodo[$pkey] . ').';
            $filas[] = $fila; $n_err++; continue;
        }

        // Auto-solape del conductor (BD solo si ya existe; siempre contra el acumulado del archivo)
        $fechas = [];
        $cur = clone $di;
        while ($cur <= $df) { $fechas[] = $cur->format('Y-m-d'); $cur->modify('+1 day'); }
        $solapa = null;
        foreach ($fechas as $f) {
            if (isset($ocupacionNueva[$f][$ckey])) { $solapa = $f; break; }
        }
        if (!$solapa && !$esNuevo) {
            $place = implode(',', array_fill(0, count($fechas), '?'));
            $st = $pdo->prepare(
                "SELECT fecha FROM mp_vaca_dia WHERE estado='ACTIVO' AND id_conductor=? AND fecha IN ($place) LIMIT 1"
            );
            $st->execute(array_merge([$idcSql], $fechas));
            $solapa = $st->fetchColumn() ?: null;
        }
        if ($solapa) {
            $fila['estado'] = 'error';
            $fila['motivo'] = 'El conductor ya tiene vacaciones el ' . $solapa . ' (solape).';
            $filas[] = $fila; $n_err++; continue;
        }

        // Tope de flota (solo ADVERTENCIA, no bloquea)
        $diasConflicto = [];
        foreach ($fechas as $f) {
            if (!isset($ocupacionNueva[$f])) $ocupacionNueva[$f] = [];
            $stmt = $pdo->prepare(
                "SELECT COUNT(DISTINCT id_conductor) FROM mp_vaca_dia
                 WHERE estado='ACTIVO' AND id_conductor<>? AND fecha=?"
            );
            $stmt->execute([$idcSql, $f]);   // idcSql=-1 en terceros → cuenta a todos los demás
            $baseBD = (int)$stmt->fetchColumn();
            $otrosArchivo = 0;
            foreach ($ocupacionNueva[$f] as $cid => $x) if ($cid !== $ckey) $otrosArchivo++;
            $total = $baseBD + $otrosArchivo + 1;
            if ($total > $tope) $diasConflicto[] = $f;
        }
        foreach ($fechas as $f) $ocupacionNueva[$f][$ckey] = true;
        $porPeriodo[$pkey] += $dias;

        $avisoTope = !empty($diasConflicto)
            ? ' Supera el tope de ' . $tope . ' en: ' . implode(', ', $diasConflicto) . '.'
            : '';

        if ($esNuevo) {
            $fila['estado'] = 'nuevo';
            $fila['motivo'] = 'No está en la base; se creará como TERCERO al confirmar.' . $avisoTope;
            $n_nuevo++;
        } elseif ($avisoTope !== '') {
            $fila['estado'] = 'warn';
            $fila['motivo'] = trim($avisoTope) . ' (se importa igual).';
            $n_warn++;
        } else {
            $n_ok++;
        }
        $filas[] = $fila;
    }

    return [
        'filas'   => $filas,
        'resumen' => ['ok' => $n_ok, 'warn' => $n_warn, 'error' => $n_err, 'dup' => $n_dup,
                      'nuevo' => $n_nuevo, 'importables' => $n_ok + $n_warn, 'total' => count($filas)]
    ];
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
            "SELECT id_conductor, ndoc, appat, apmat, nombres, regimen, es_tercero,
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
            "SELECT id_conductor, iden_pers, ndoc, appat, apmat, nombres, regimen, fecha_ingreso, es_tercero,
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

    if ($action === 'obtener_config') {
        echo json_encode([
            'success'            => true,
            'VACA_TOPE_FLOTA'    => vaca_config($pdo, 'VACA_TOPE_FLOTA', 4),
            'VACA_DIAS_PERIODO'  => vaca_config($pdo, 'VACA_DIAS_PERIODO', 30)
        ]);
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

    if ($action === 'guardar_config') {
        $tope = isset($_POST['tope']) ? (int)$_POST['tope'] : 0;
        $dias = isset($_POST['dias']) ? (int)$_POST['dias'] : 0;
        if ($tope < 1 || $tope > 37) {
            echo json_encode(['success' => false, 'error' => 'El tope debe estar entre 1 y 37.']);
            exit;
        }
        try {
            $set = function($nombre, $valor) use ($pdo) {
                $st = $pdo->prepare("SELECT COUNT(*) FROM mp_admi_conf WHERE nomb_conf = ?");
                $st->execute([$nombre]);
                if ((int)$st->fetchColumn() > 0) {
                    $pdo->prepare("UPDATE mp_admi_conf SET valo_conf = ? WHERE nomb_conf = ?")
                        ->execute([$valor, $nombre]);
                } else {
                    $pdo->prepare("INSERT INTO mp_admi_conf (nomb_conf, desc_conf, valo_conf) VALUES (?, ?, ?)")
                        ->execute([$nombre, 'Configuracion del modulo de vacaciones', $valor]);
                }
            };
            $set('VACA_TOPE_FLOTA', (string)$tope);
            if ($dias >= 1 && $dias <= 60) $set('VACA_DIAS_PERIODO', (string)$dias);
            echo json_encode(['success' => true, 'message' => 'Configuración actualizada. Tope de flota: ' . $tope . '.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Error al guardar: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'crear_tercero') {
        $appat = isset($_POST['appat']) ? trim($_POST['appat']) : '';
        $apmat = isset($_POST['apmat']) ? trim($_POST['apmat']) : '';
        $nombres = isset($_POST['nombres']) ? trim($_POST['nombres']) : '';
        $regimen = isset($_POST['regimen']) ? trim($_POST['regimen']) : 'TERCEROS';
        $ndoc = isset($_POST['ndoc']) ? trim($_POST['ndoc']) : '';
        $fing = isset($_POST['fecha_ingreso']) ? trim($_POST['fecha_ingreso']) : '';

        if ($appat === '' || $nombres === '') {
            echo json_encode(['success' => false, 'error' => 'Apellido paterno y nombres son obligatorios.']);
            exit;
        }
        $fingOk = vaca_parse_fecha($fing);
        try {
            // Evitar duplicar por nombre normalizado
            $nk = vaca_norm_txt($appat . ' ' . $apmat . ', ' . $nombres);
            foreach ($pdo->query("SELECT id_conductor, appat, apmat, nombres FROM mp_vaca_conductor WHERE estado=1")->fetchAll() as $c) {
                if (vaca_norm_txt($c['appat'] . ' ' . $c['apmat'] . ', ' . $c['nombres']) === $nk) {
                    echo json_encode(['success' => false, 'error' => 'Ya existe un conductor con ese nombre.']);
                    exit;
                }
            }
            $st = $pdo->prepare(
                "INSERT INTO mp_vaca_conductor
                    (iden_pers, ndoc, appat, apmat, nombres, regimen, fecha_ingreso, es_tercero, estado)
                 VALUES (NULL, ?, ?, ?, ?, ?, ?, 1, 1)"
            );
            $st->execute([
                substr($ndoc, 0, 8),
                mb_strtoupper($appat, 'UTF-8'), mb_strtoupper($apmat, 'UTF-8'), mb_strtoupper($nombres, 'UTF-8'),
                $regimen !== '' ? $regimen : 'TERCEROS',
                $fingOk ?: date('Y-m-d')
            ]);
            echo json_encode(['success' => true, 'message' => 'Tercero registrado.', 'id_conductor' => (int)$pdo->lastInsertId()]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Error al crear el tercero: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'importar_previsualizar') {
        try {
            $rawRows = [];
            if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
                $rawRows = vaca_leer_archivo($_FILES['archivo']['tmp_name'], $_FILES['archivo']['name']);
            } elseif (isset($_POST['pegado']) && trim($_POST['pegado']) !== '') {
                $rawRows = vaca_leer_texto($_POST['pegado']);
            } else {
                echo json_encode(['success' => false, 'error' => 'No se recibió archivo ni texto para importar.']);
                exit;
            }
            if (empty($rawRows)) {
                echo json_encode(['success' => false, 'error' => 'No se pudieron leer filas del origen.']);
                exit;
            }
            $tope = vaca_config($pdo, 'VACA_TOPE_FLOTA', 4);
            $res = vaca_procesar_importacion($pdo, $rawRows, $tope);
            echo json_encode([
                'success' => true,
                'tope'    => $tope,
                'filas'   => $res['filas'],
                'resumen' => $res['resumen']
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Error al leer el origen: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'importar_confirmar') {
        $filasRaw = isset($_POST['filas']) ? $_POST['filas'] : '';
        $filas = json_decode($filasRaw, true);
        $idOper = isset($_SESSION['iden_oper']) ? (int)$_SESSION['iden_oper'] : 0;
        if (!is_array($filas) || count($filas) === 0) {
            echo json_encode(['success' => false, 'error' => 'No hay filas para confirmar.']);
            exit;
        }
        try {
            $diasPeriodo = vaca_config($pdo, 'VACA_DIAS_PERIODO', 30);
            $pdo->beginTransaction();

            $insTramo = $pdo->prepare(
                "INSERT INTO mp_vaca_tramo (id_periodo, id_conductor, fecha_inicio, fecha_fin, dias, estado, id_oper_reg)
                 VALUES (?, ?, ?, ?, ?, 'ACTIVO', ?)"
            );
            $insDia = $pdo->prepare(
                "INSERT INTO mp_vaca_dia (id_tramo, id_conductor, id_periodo, fecha, estado)
                 VALUES (?, ?, ?, ?, 'ACTIVO')"
            );

            $cachePer = [];      // "idc|idcat" => id_periodo
            $porPer   = [];      // id_periodo => ['idc','tramos'=>[],'consumidos']
            $cacheTercero = [];  // nombre normalizado => id_conductor recién creado
            $importados = 0;
            $creados = 0;
            $omitidos = [];

            foreach ($filas as $f) {
                $idc   = isset($f['id_conductor']) ? (int)$f['id_conductor'] : 0;
                $idcat = isset($f['id_periodo_cat']) ? (int)$f['id_periodo_cat'] : 0;
                $fi    = isset($f['fi']) ? trim($f['fi']) : '';
                $ff    = isset($f['ff']) ? trim($f['ff']) : '';
                $nombre = isset($f['nombre']) ? $f['nombre'] : ('conductor ' . $idc);

                // Crear el conductor tercero si la fila lo pide y aún no existe
                if ($idc <= 0 && !empty($f['crear_tercero'])) {
                    $nk = vaca_norm_txt($nombre);
                    if (isset($cacheTercero[$nk])) {
                        $idc = $cacheTercero[$nk];
                    } else {
                        $ap = isset($f['appat']) ? trim($f['appat']) : '';
                        $am = isset($f['apmat']) ? trim($f['apmat']) : '';
                        $no = isset($f['nombres']) ? trim($f['nombres']) : '';
                        if ($ap === '' && $no === '') { list($ap, $am, $no) = vaca_partir_nombre($nombre); }
                        $reg = isset($f['regimen']) ? trim($f['regimen']) : '';
                        $pdo->prepare(
                            "INSERT INTO mp_vaca_conductor
                                (iden_pers, ndoc, appat, apmat, nombres, regimen, fecha_ingreso, es_tercero, estado)
                             VALUES (NULL, '', ?, ?, ?, ?, CURDATE(), 1, 1)"
                        )->execute([$ap, $am, $no, $reg]);
                        $idc = (int)$pdo->lastInsertId();
                        $cacheTercero[$nk] = $idc;
                        $creados++;
                    }
                }

                $di = DateTime::createFromFormat('Y-m-d', $fi);
                $df = DateTime::createFromFormat('Y-m-d', $ff);
                if ($idc <= 0 || $idcat <= 0 || !$di || !$df || $df < $di) {
                    $omitidos[] = "$nombre: sin conductor o datos inválidos"; continue;
                }
                $dias = (int)$di->diff($df)->days + 1;

                // Asegurar la instancia de periodo (crear si falta; bypass ventana en importación)
                $keyP = $idc . '|' . $idcat;
                if (!isset($cachePer[$keyP])) {
                    $st = $pdo->prepare("SELECT id_periodo FROM mp_vaca_periodo WHERE id_conductor=? AND id_periodo_cat=?");
                    $st->execute([$idc, $idcat]);
                    $idPer = $st->fetchColumn();
                    if (!$idPer) {
                        $stc = $pdo->prepare("SELECT etiqueta FROM mp_vaca_periodo_cat WHERE id_periodo_cat=?");
                        $stc->execute([$idcat]);
                        $etq = $stc->fetchColumn();
                        if (!$etq) { $omitidos[] = "$nombre: periodo inexistente"; continue; }
                        $pdo->prepare(
                            "INSERT INTO mp_vaca_periodo (id_conductor, id_periodo_cat, etiqueta, dias_asignados, estado)
                             VALUES (?, ?, ?, ?, 'INCOMPLETO')"
                        )->execute([$idc, $idcat, $etq, $diasPeriodo]);
                        $idPer = (int)$pdo->lastInsertId();
                    }
                    $cachePer[$keyP] = (int)$idPer;
                }
                $idPer = $cachePer[$keyP];

                // Revalidar saldo dentro de la transacción (refleja inserciones previas)
                $s = vaca_saldo_periodo($pdo, $idPer);
                if ($dias > $s['saldo']) {
                    $omitidos[] = "$nombre ($fi→$ff): excede saldo del periodo"; continue;
                }

                // Explotar fechas + revalidar solape y duplicado dentro de la transacción
                $fechas = [];
                $cur = clone $di;
                while ($cur <= $df) { $fechas[] = $cur->format('Y-m-d'); $cur->modify('+1 day'); }

                $place = implode(',', array_fill(0, count($fechas), '?'));
                $st = $pdo->prepare(
                    "SELECT fecha FROM mp_vaca_dia WHERE estado='ACTIVO' AND id_conductor=? AND fecha IN ($place) LIMIT 1"
                );
                $st->execute(array_merge([$idc], $fechas));
                if ($st->fetchColumn()) { $omitidos[] = "$nombre ($fi→$ff): solape con otra vacación"; continue; }

                $st = $pdo->prepare(
                    "SELECT COUNT(*) FROM mp_vaca_tramo WHERE estado='ACTIVO' AND id_conductor=? AND id_periodo=? AND fecha_inicio=? AND fecha_fin=?"
                );
                $st->execute([$idc, $idPer, $fi, $ff]);
                if ((int)$st->fetchColumn() > 0) { $omitidos[] = "$nombre ($fi→$ff): duplicado"; continue; }

                // Insertar
                $insTramo->execute([$idPer, $idc, $fi, $ff, $dias, $idOper]);
                $idTramo = (int)$pdo->lastInsertId();
                foreach ($fechas as $fe) $insDia->execute([$idTramo, $idc, $idPer, $fe]);
                $importados++;

                if (!isset($porPer[$idPer])) $porPer[$idPer] = ['idc' => $idc, 'tramos' => [], 'consumidos' => 0];
                $porPer[$idPer]['tramos'][] = ['inicio' => $fi, 'fin' => $ff, 'dias' => $dias];
                $porPer[$idPer]['consumidos'] += $dias;
            }

            // Recalcular estado + historial por periodo afectado
            $insHist = $pdo->prepare(
                "INSERT INTO mp_vaca_historial
                    (id_conductor, id_periodo, accion, detalle_despues, dias_liberados, dias_consumidos, saldo_resultante, id_oper)
                 VALUES (?, ?, 'CREA', ?, 0, ?, ?, ?)"
            );
            foreach ($porPer as $idPer => $info) {
                vaca_recalcular_estado($pdo, $idPer);
                $s = vaca_saldo_periodo($pdo, $idPer);
                $insHist->execute([
                    $info['idc'], $idPer, json_encode($info['tramos']),
                    $info['consumidos'], $s['saldo'], $idOper
                ]);
            }

            $pdo->commit();
            $msg = "Importación completada: $importados tramo(s) cargado(s)";
            $msg .= $creados > 0 ? ", $creados tercero(s) creado(s)." : ".";
            echo json_encode([
                'success'    => true,
                'message'    => $msg,
                'importados' => $importados,
                'creados'    => $creados,
                'omitidos'   => $omitidos
            ]);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => 'Error en la importación: ' . $e->getMessage()]);
        }
        exit;
    }
}

echo json_encode(['success' => false, 'error' => 'Acción no permitida']);
?>
