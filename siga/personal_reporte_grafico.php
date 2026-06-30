<?php
require_once ('classes/jpgraph/src/jpgraph.php');
require_once ('classes/jpgraph/src/jpgraph_pie.php');
require_once ('classes/jpgraph/src/jpgraph_pie3d.php');
require_once ('classes/Db.class.php');

// Recibir los filtros por GET
$filtros = [
    'codi_loca' => $_GET['codi_loca'] ?? '',
    'codi_depe' => $_GET['codi_depe'] ?? '',
    'codi_regi' => $_GET['codi_regi'] ?? '',
    'codi_carg' => $_GET['codi_carg'] ?? '',
    'codi_sexo' => $_GET['codi_sexo'] ?? '',
    'codi_hijo' => $_GET['codi_hijo'] ?? '',
    'edad_desd' => $_GET['edad_desd'] ?? '',
    'edad_hast' => $_GET['edad_hast'] ?? '',
    'codi_sind' => $_GET['codi_sind'] ?? '',
    'codi_moda' => $_GET['codi_moda'] ?? '',
    'codi_presu' => $_GET['codi_presu'] ?? '',
    'codi_conad' => $_GET['codi_conad'] ?? '',
];

$Db = new Db();

// Construir la consulta SQL con los filtros
$reporte_tipo = $_GET['reporte_tipo'] ?? 'sexo';

$sql_base = "FROM mp_admi_pers p
             LEFT JOIN mp_admi_depe d ON p.iden_depe = d.codi_depe";
$titulo_grafico = '';
$campo_agrupacion_nombre = '';
$campo_agrupacion_alias = '';

switch ($reporte_tipo) {
    case 'regimen':
        $sql_select = "SELECT r.x_nombre as regimen, COUNT(*) as cantidad";
        $sql_base .= " LEFT JOIN mp_maes_regimen_laboral r ON p.iden_rlab = r.n_codigo";
        $titulo_grafico = "Distribución de Personal por Régimen Laboral";
        $campo_agrupacion_nombre = "r.x_nombre";
        $campo_agrupacion_alias = "regimen";
        break;
    case 'modalidad':
        $sql_select = "SELECT m.x_nombre as modalidad, COUNT(*) as cantidad";
        $sql_base .= " LEFT JOIN mp_maes_modalidad_trabajo m ON p.iden_modtrab = m.n_codigo";
        $titulo_grafico = "Distribución de Personal por Modalidad de Trabajo";
        $campo_agrupacion_nombre = "m.x_nombre";
        $campo_agrupacion_alias = "modalidad";
        break;
    case 'cargo':
        $sql_select = "SELECT c.x_nombre as cargo, COUNT(*) as cantidad";
        $sql_base .= " LEFT JOIN mp_maes_cargo c ON p.iden_carg = c.n_codigo";
        $titulo_grafico = "Distribución de Personal por Cargo";
        $campo_agrupacion_nombre = "c.x_nombre";
        $campo_agrupacion_alias = "cargo";
        break;
    case 'sexo':
    default:
        $sql_select = "SELECT s.x_nombre as sexo, COUNT(*) as cantidad";
        $sql_base .= " LEFT JOIN mp_maes_sexo s ON p.iden_sexo = s.n_codigo";
        $titulo_grafico = "Distribución de Personal por Sexo";
        $campo_agrupacion_nombre = "s.x_nombre";
        $campo_agrupacion_alias = "sexo";
        break;
}

$sql = $sql_select . " " . $sql_base . " WHERE p.esta_pers = 1";

$params = [];

if (!empty($filtros['codi_loca'])) {
    if (is_array($filtros['codi_loca'])) {
        $in_loca = implode(',', array_map('intval', $filtros['codi_loca']));
        $sql .= " AND d.codi_loca IN ($in_loca)";
    } else {
        $sql .= " AND d.codi_loca = :codi_loca";
        $params[':codi_loca'] = $filtros['codi_loca'];
    }
}
if (!empty($filtros['codi_depe'])) {
    if (is_array($filtros['codi_depe'])) {
        $in_depe = implode(',', array_map('intval', $filtros['codi_depe']));
        $sql .= " AND p.iden_depe IN ($in_depe)";
    } else {
        $sql .= " AND p.iden_depe = :codi_depe";
        $params[':codi_depe'] = $filtros['codi_depe'];
    }
}
if (!empty($filtros['codi_regi'])) {
    if (is_array($filtros['codi_regi'])) {
        $in_regi = implode(',', array_map('intval', $filtros['codi_regi']));
        $sql .= " AND p.iden_rlab IN ($in_regi)";
    } else {
        $sql .= " AND p.iden_rlab = :codi_regi";
        $params[':codi_regi'] = $filtros['codi_regi'];
    }
}
if (!empty($filtros['codi_carg'])) {
    if (is_array($filtros['codi_carg'])) {
        $in_carg = implode(',', array_map('intval', $filtros['codi_carg']));
        $sql .= " AND p.iden_carg IN ($in_carg)";
    } else {
        $sql .= " AND p.iden_carg = :codi_carg";
        $params[':codi_carg'] = $filtros['codi_carg'];
    }
}
if (!empty($filtros['codi_sexo'])) {
    $sql .= " AND p.iden_sexo = :codi_sexo";
    $params[':codi_sexo'] = $filtros['codi_sexo'];
}
if (!empty($filtros['edad_desd']) && is_numeric($filtros['edad_desd'])) {
    $sql .= " AND YEAR(CURDATE()) - YEAR(STR_TO_DATE(p.fnac_pers, '%Y%m%d')) >= :edad_desd";
    $params[':edad_desd'] = $filtros['edad_desd'];
}
if (!empty($filtros['edad_hast']) && is_numeric($filtros['edad_hast'])) {
    $sql .= " AND YEAR(CURDATE()) - YEAR(STR_TO_DATE(p.fnac_pers, '%Y%m%d')) <= :edad_hast";
    $params[':edad_hast'] = $filtros['edad_hast'];
}
if ($filtros['codi_sind'] == '1') {
    $sql .= " AND p.iden_sind = 1";
} elseif ($filtros['codi_sind'] == '2') {
    $sql .= " AND (p.iden_sind = 0 OR p.iden_sind IS NULL)";
}
if ($filtros['codi_conad'] == '1') {
    $sql .= " AND p.cona_pers = 1";
} elseif ($filtros['codi_conad'] == '2') {
    $sql .= " AND (p.cona_pers = 0 OR p.cona_pers IS NULL)";
}
if (!empty($filtros['codi_presu']) && is_numeric($filtros['codi_presu'])) {
    $sql .= " AND p.iden_pres = :codi_presu";
    $params[':codi_presu'] = $filtros['codi_presu'];
}
if (!empty($filtros['codi_moda'])) {
    if (is_array($filtros['codi_moda'])) {
        $in_moda = implode(',', array_map('intval', $filtros['codi_moda']));
        $sql .= " AND p.iden_modtrab IN ($in_moda)";
    } elseif (is_numeric($filtros['codi_moda'])) {
        $sql .= " AND p.iden_modtrab = :codi_moda";
        $params[':codi_moda'] = $filtros['codi_moda'];
    }
}

$sql .= " GROUP BY " . $campo_agrupacion_nombre;

if ($reporte_tipo == 'cargo') {
    $sql .= " ORDER BY cantidad DESC LIMIT 10"; // Limit cargo to top 10
}

$datos_grafico = $Db->query($sql, $params);

$data = [];
$labels = [];
$colors = [];

foreach ($datos_grafico as $row) {
    if (!empty($row[$campo_agrupacion_alias])) {
        $data[] = $row['cantidad'];
        $labels[] = utf8_decode($row[$campo_agrupacion_alias]) . ' (%.1f%%)';
        
        if ($reporte_tipo == 'sexo') {
            $sexo = strtoupper($row[$campo_agrupacion_alias]);
            if ($sexo == 'MASCULINO') {
                $colors[] = '#4A90E2'; // Azul
            } elseif ($sexo == 'FEMENINO') {
                $colors[] = '#E91E63'; // Rosa
            } else {
                $colors[] = '#CCCCCC'; // Default
            }
        }
    }
}

if (empty($data)) {
    $data = [1];
    $labels = ["No hay datos"];
}

// Crear el gráfico
$graph = new PieGraph(450, 280, 'auto');
$graph->SetShadow();

$graph->title->Set(utf8_decode($titulo_grafico));
$graph->title->SetFont(FF_FONT1, FS_BOLD);

$p1 = new PiePlot3D($data);
$p1->SetSize(0.4);
$p1->SetCenter(0.5, 0.55);
$p1->SetLegends($labels);

if (!empty($colors)) {
    $p1->SetSliceColors($colors);
}

$graph->Add($p1);
$graph->Stroke();

?>