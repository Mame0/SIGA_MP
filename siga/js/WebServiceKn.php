<?php
header('Content-Type: text/plain; charset=UTF-8');
    $lcId = $_GET['Id'];
    $lcTexto = "[";	
    require_once('ws/lib_nusoap/nusoap.php');
	 $soapClient->http_encoding='UTF-8'; 
    $soapClient->defencoding='UTF-8'; 
    $soapClient->decode_utf8 = false; 
	
    $_SESSION['client'] = new nusoap_client('http://localhost/alimentos/js/ws/consultaReniecService.wsdl','wsdl');//sino ni
   
	$err = $_SESSION['client']->getError();
    if ($err) {	echo 'Error en Constructor' . $err ; }
    if (@$_POST['Boton1'] == 'Buscar') {
		if($_REQUEST['tipo']=='xdni'){
			
			$_SESSION['tipoBusqueda']='2';
			$_SESSION['Dni']=$_REQUEST['dni'];
			$_SESSION['Nombres']=null;
			$_SESSION['App']=null;
			$_SESSION['Apm']=null;
			$_SESSION['Tipo']='2';
			$_SERVER['REMOTE_ADDR'];
			fxBuscar();

		}else {
			$_SESSION['tipoBusqueda']='2';
			$_SESSION['Dni']=$_REQUEST['dni'];
			$_SESSION['Nombres']=null;
			$_SESSION['App']=null;
			$_SESSION['Apm']=null;
			$_SESSION['Tipo']='2';
			$_SERVER['REMOTE_ADDR'];
			fxBuscar();
		}
	}	
    $_SESSION['tipoBusqueda']='2';
	$_SESSION['Dni']=$lcId;
	$_SESSION['Nombres']=null;
	$_SESSION['App']=null;
	$_SESSION['Apm']=null;
	$_SESSION['Tipo']='2';	
	$_SERVER['REMOTE_ADDR'];
	
		$param = array('req_trama' => null
			,'req_dniConsultante' => '29709217'
			,'req_tipoConsulta' => $_SESSION['tipoBusqueda']
			,'req_usuario' => 'JBARREDAM'
			,'req_ip' => $_SERVER['REMOTE_ADDR']
			,'req_dni' => $_SESSION['Dni']
			,'req_nombres' => $_SESSION['Nombres']
			,'req_apellidoPaterno' => $_SESSION['App']
			,'req_apellidoMaterno' => $_SESSION['Apm']
			,'req_nroRegistros' => $_SESSION['Tipo']
			,'req_grupo' => null
			,'req_dniApoderado' => null
			,'req_tipoVinculoApoderado' => null);
			
		$result = $_SESSION['client']->call('consultaReniec', array($param));
		$_SESSION['paDatos'] = (Array)$_SESSION['client'];
		$datos  = explode("	",$_SESSION['paDatos']['responseData']);			
	    $imagen = (Array)$_SESSION['paDatos'][57];
	    $newstring = str_ireplace("<NS1:res_listaPersonas></NS1:res_listaPersonas></NS1:consultaReniecResponse></soapenv:Body></soapenv:Envelope>", " ", $imagen);
	    $firma=$result['res_firma'];
		$datos[57] = '<DATA><NS1:res_persona>'.$datos[57];
		$imagen = (Array)$datos[57];			
		$newstring = str_ireplace("<NS1:res_listaPersonas></NS1:res_listaPersonas></NS1:consultaReniecResponse></soapenv:Body></soapenv:Envelope>", " ", $imagen[0]);
		$newstring = $newstring.'</DATA>';
		$xml = simplexml_load_string($newstring);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
		//$array['res_foto']
		//$array['res_firma']
		/*$file = fopen("archivo.txt", "a");
		foreach($datos as $i){
            fwrite($file, $i.PHP_EOL);
		}
        fclose($file);*/
		$foto = $array['res_foto'];
		$firma = $array['res_firma'];		
		if (!empty($datos[5])){
		   $lcTexto .= "{
			'NOMBRE': '$datos[5]', 
			'APP': '$datos[2]', 
			'APM': '$datos[3]', 
			'nombresp': '$datos[31]', 
			'nombresm': '$datos[34]',
			'fechanac': '$datos[28]',
			'sexo': '$datos[17]',
			'nivele': '$datos[15]',
			'estadoc': '$datos[14]',
			'udep_domi': '$datos[6]',
			'upro_domi': '$datos[7]',
			'udis_domi': '$datos[8]',
			'udep_naci': '$datos[20]',
			'upro_naci': '$datos[21]',
			'udis_naci': '$datos[22]',
			'lugarnac': '$datos[24]',
			'pref_dire': '$datos[41]',
			'nomb_dire': '$datos[42]',
			'nume_dire': '$datos[43]',
			'blok_dire': '$datos[44]',
			'inte_dire': '$datos[45]',
			'urba_dire': '$datos[46]',
			'atap_dire': '$datos[47]',
			'manz_dire': '$datos[48]',
			'lote_dire': '$datos[49]',
			'fech_cadu': '$datos[39]',
			'digi_veri': '$datos[1]',
			'FOTO': '$foto',
			'FIRMA': '$firma'}";
		}else{
		   $lcTexto .= "{'NOMBRE': 'no encontrado', 'APP': 'no encontrado','APM': 'no encontrado' }";
		}
	 $lcTexto .="]";
     echo utf8_encode($lcTexto);	
     return;
	 
	 
	 /*
	 0=>"Numero de DNI",		1=>"Digito de verificacion",		2=>"Apellido Paterno",		3=>"Apellido Materno",
				4=>"Apellido de Casada",		5=>"Nombres",		6=>"Codigo de Ubigeo departamento domicilio",
				7=>"Codigo de Ubigeo provincia domicilio",		8=>"Codigo de Ubigeo distrito domicilio",
				9=>"Codigo de Ubigeo localidad domicilio",		10=>"Departamento ",		11=>"Provincia domicilio",		12=>"Distrito domicilio",
				13=>"Localidad domicilio",		14=>"Estado Civil",		15=>"Grado de instrucción",		16=>"Estatura",
				17=>"Sexo",		18=>"Tipo de documento presentado al registrarse en la RENIEC",		19=>"Numero de documento presentado al registrarse en la RENIEC.",		20=>"Codigo de Ubigeo del Departamento de Nacimiento",
				21=>"Codigo de Ubigeo de la Provincia de Nacimiento",		22=>"Codigo de Ubigeo del Distrito de Nacimiento",
				23=>"Codigo de Ubigeo de la Localidad de Nacimiento",		24=>"Departamento de Nacimiento",
				25=>"Provincia de Nacimiento",		26=>"Distrito de Nacimiento",		27=>"Localidad de Nacimiento",		28=>"Fecha de Nacimiento",
				29=>"Tipo de Documento del Padre",		30=>"Numero de documento del padre",		31=>"Nombre del padre",		32=>"Tipo de Documento de la Madre",
				33=>"Numero de Documento de la Madre",		34=>"Nombre de la madre",		35=>"Fecha de Inscripción",
				36=>"Fecha de Expedicion",		37=>"Fecha de Fallecimiento",		38=>"Constancia de Votación",
				39=>"Fecha de Caducidad",		40=>"Restriccion",		41=>"Prefijo de Direccion",				42=>"Direccion",		43=>"Numero de direccion",		44=>"Block o Chalet",
				45=>"Interior",		46=>"Urbanizacion",		47=>"Etapa",		48=>"Manzana",				49=>"Lote",		50=>"Prefijo de Bloc o Chalet",		51=>"Prefijo de departamento piso interior",
				52=>"Prefijo de urbanizacion Cond. Residencia",		53=>"Campo Reservado",				54=>"Longitud de la Foto",		55=>"Longitud de la Firma",		56=>"Campo 1 reservado para la foto y firma",
				57=>"Campo 2 reservado para la foto y firma"
	 */
	 
	 
	 
?>
