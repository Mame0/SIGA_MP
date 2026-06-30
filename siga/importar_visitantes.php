<?php
/**
 * Script de importación de visitantes.csv → tabla mp_visitantes
 * EJECUTAR UNA SOLA VEZ desde el navegador:
 *   http://localhost/siga/importar_visitantes.php
 * Borrar este archivo después de usarlo.
 */
require_once 'classes/Db.class.php';
$Db = new Db();

// 1. Crear tabla si no existe
$Db->query("CREATE TABLE IF NOT EXISTS `mp_visitantes` (
  `codi_visi` INT(11) NOT NULL AUTO_INCREMENT,
  `ndoc_visi` VARCHAR(20) NOT NULL,
  `appa_visi` VARCHAR(60) NOT NULL DEFAULT '',
  `apma_visi` VARCHAR(60) NOT NULL DEFAULT '',
  `nomb_visi` VARCHAR(100) NOT NULL DEFAULT '',
  `codi_depe` INT(11) NOT NULL DEFAULT 0,
  `codi_pers` INT(11) NOT NULL DEFAULT 0,
  `esta_visi` TINYINT(1) NOT NULL DEFAULT 1,
  `fdig_visi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`codi_visi`),
  UNIQUE KEY `uk_ndoc_visi` (`ndoc_visi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci");

function dividirNombreCompleto($nombreCompleto)
{
    $partes = explode(' ', trim($nombreCompleto));
    $nombres = [];
    $apellidos = [];

    if (count($partes) >= 3) {
        $apellidos[] = array_shift($partes);
        $apellidos[] = array_shift($partes);
        $nombres = $partes;
    } elseif (count($partes) == 2) {
        $apellidos[] = array_shift($partes);
        $nombres = $partes;
    } else {
        $nombres = $partes;
    }

    if (count($apellidos) == 2) {
        $apellidoPaterno = $apellidos[0];
        $apellidoMaterno = $apellidos[1];
    } else {
        $apellidoPaterno = $apellidos[0] ?? '';
        $apellidoMaterno = '';
    }

    return [
        'nomb' => implode(' ', $nombres),
        'appa' => $apellidoPaterno,
        'apma' => $apellidoMaterno
    ];
}

$csvFile = __DIR__ . '/visitantes.csv';
if (!file_exists($csvFile)) {
    die("<b style='color:red'>ERROR:</b> No se encontró visitantes.csv en " . __DIR__);
}

$handle = fopen($csvFile, 'r');
$insertados = 0;
$omitidos   = 0;
$lineaNum   = 0;

echo "<pre style='font-family:monospace;font-size:13px'>";
echo "Leyendo archivo: $csvFile\n";
echo str_repeat("-", 60) . "\n";

while (($line = fgets($handle)) !== false) {
    $lineaNum++;
    // Convertir posible encoding Windows-1252 / Latin1 → UTF-8
    $line = mb_convert_encoding($line, 'UTF-8', 'Windows-1252');
    $cols = explode(';', trim($line));

    if ($lineaNum === 1) {
        echo "Cabecera: " . implode(' | ', array_slice($cols, 0, 5)) . "\n\n";
        continue;
    }

    if (count($cols) < 5) { $omitidos++; continue; }

    $ndoc          = trim($cols[2]);
    $nomb_completo = trim($cols[1]);
    $codi_depe     = (int) trim($cols[3]);
    $codi_pers     = (int) trim($cols[4]);

    if (empty($ndoc) || empty($nomb_completo)) { $omitidos++; continue; }

    $nombres = dividirNombreCompleto($nomb_completo);

    $appa = addslashes($nombres['appa']);
    $apma = addslashes($nombres['apma']);
    $nomb = addslashes($nombres['nomb']);

    // INSERT IGNORE respeta la UNIQUE KEY en ndoc_visi (no falla en duplicados)
    $res = $Db->query(
        "INSERT IGNORE INTO mp_visitantes (ndoc_visi, appa_visi, apma_visi, nomb_visi, codi_depe, codi_pers, esta_visi)
         VALUES ('$ndoc', '$appa', '$apma', '$nomb', $codi_depe, $codi_pers, 1)"
    );

    if ($res !== false) {
        $insertados++;
        echo "OK  L$lineaNum | DNI: $ndoc | $nombres[appa] $nombres[apma], $nombres[nomb]\n";
    } else {
        $omitidos++;
        echo "IGN L$lineaNum | DNI: $ndoc (duplicado o error)\n";
    }
}

fclose($handle);

echo "\n" . str_repeat("=", 60) . "\n";
echo "✅ Importación completada\n";
echo "   Registros procesados : " . ($lineaNum - 1) . "\n";
echo "   Insertados           : $insertados\n";
echo "   Omitidos/Duplicados  : $omitidos\n";
echo str_repeat("=", 60) . "\n";
echo "\n<b style='color:green'>Puedes eliminar este archivo (importar_visitantes.php) de forma segura.</b>";
echo "</pre>";
?>
