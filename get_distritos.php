<?php
require_once 'classes/Db.class.php';
header('Content-Type: application/json');

$db = new Db();
$distritos = [];
$provincia_code = isset($_GET['prov_code']) ? $_GET['prov_code'] : '';

if (!empty($provincia_code)) {
    // El código de ubigeo de la provincia en la BD parece ser solo los 2 últimos dígitos
    $ubig_prov = substr($provincia_code, -2);

    $distritos = $db->query("SELECT cdis, dist FROM mp_admi_ubig_reni WHERE cdep = '04' AND cpro = :cpro AND cdis != '00' ORDER BY dist", [':cpro' => $ubig_prov]);
}

echo json_encode($distritos);
?>