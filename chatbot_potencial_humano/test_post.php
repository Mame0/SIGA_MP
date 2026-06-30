<?php
$data = array("mensaje" => "🔙 Volver a Licencias");
$json = json_encode($data);

$ch = curl_init('http://localhost/siga/chatbot_potencial_humano/api.php');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($json))
);

$result = curl_exec($ch);
echo "Response for '🔙 Volver a Licencias':\n" . $result . "\n";

$data2 = array("mensaje" => "🏠 Volver al menú principal");
$json2 = json_encode($data2);

$ch2 = curl_init('http://localhost/siga/chatbot_potencial_humano/api.php');
curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch2, CURLOPT_POSTFIELDS, $json2);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($json2))
);

$result2 = curl_exec($ch2);
echo "Response for '🏠 Volver al menú principal':\n" . $result2 . "\n";
?>
