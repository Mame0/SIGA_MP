<?php
$mensaje = "🏠 Volver al menú principal";

$mensajeNormalizado = trim($mensaje);
$mensajeLimpio = mb_strtolower($mensajeNormalizado);

if (preg_match('/volver al men[uú] principal/ui', $mensajeLimpio)) {
    echo "Match 1\n";
} else {
    echo "No Match 1\n";
}

$key = "🔙 Volver a Licencias";
$keyLimpia = mb_strtolower(trim($key));
$msg = "🔙 Volver a Licencias";
$msgLimpio = mb_strtolower(trim($msg));

if ($keyLimpia === $msgLimpio) {
    echo "Match 2\n";
} else {
    echo "No Match 2\n";
}
?>
