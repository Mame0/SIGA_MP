<?php 
require_once('lib/nusoap.php');
//require_once('ws/lib/nusoap.php');
	 $soapClient->http_encoding='UTF-8'; 
    $soapClient->defencoding='UTF-8'; 
    $soapClient->decode_utf8 = false; 


//$client = new nusoap_client('http://localhost/pioj/include/ws/consultaReniecService.wsdl','wsdl');
$client  = new nusoap_client ('http://172.28.7.200:8080/Sij001WS/services/personaExpedientesWS?wsdl');

$param = array('apePat' => 'mamani'
		,'apeMat' => 'mamani'
		,'nombs' => 'juan');

$resulta = $client->call('listarPersonaExpedientes', array($param));
print_r($client);
print_r($param);
if($resulta!=null){
	echo '<h2>Resultado01</h2><pre>';

	 $datos=explode("	",$resulta['instanciaActual']);
	 $nom_campos=array(
		0=>"Juzgado",
		1=>"expediente"
					
		);
	 for($i=0;$i<58;$i++){
	 print_r($nom_campos[$i]."->".$datos[$i]."\n");
	 }
}

echo '<h2>Resultado</h2><pre>'; 	
print_r($resulta);
print_r($client);
echo '</pre>';
 


?>