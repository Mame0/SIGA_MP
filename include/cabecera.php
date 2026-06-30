<?php
	session_start();
define('BASE_URL', '/siga/');

	require_once 'classes/Db.class.php';
	$Db = new Db();

	$language = 'spanish'; // Fallback default language
	try {
		$result = $Db->select('mp_admi_conf', ['iden_conf' => 2]);
		if (!empty($result)) {
			foreach ($result as $rows => $valo) {
				$language_from_db = $valo['valo_conf'];
				if (!empty($language_from_db)) {
					$language = $language_from_db;
				}
			}
		}
	} catch (Exception $e) {
		// Do nothing, use the fallback language. This can happen if the table doesn't exist.
	}

	//idioma
	if (file_exists("include/languages/$language.php")) {
		require_once "include/languages/$language.php";
	} else {
		// If the selected language file doesn't exist, fall back to a default one that does.
		require_once "include/languages/spanish.php";
	}
	date_default_timezone_set('America/Lima');

	if ( !isset( $_SESSION['iden_oper'] ) ) 
	{
		//header('location: index.php');
		echo"<script>parent.location.href='index.php';</script>";
	}
	
	// Máxima duración de sesión activa en hora
	define( 'MAX_SESSION_TIEMPO', 3600 * 1 );

	// Controla cuando se ha creado y cuando tiempo ha recorrido 
	if ( isset( $_SESSION[ 'ULTIMA_ACTIVIDAD' ] ) && ( time() - $_SESSION[ 'ULTIMA_ACTIVIDAD' ] > MAX_SESSION_TIEMPO ) )
	{
		// Si ha pasado el tiempo sobre el limite destruye la session
		destruir_session();
	}

	$_SESSION[ 'ULTIMA_ACTIVIDAD' ] = time();

	// Función para destruir y resetear los parámetros de sesión
	function destruir_session()
	{
		$_SESSION = array();
		if ( ini_get( 'session.use_cookies' ) )
		{
			$params = session_get_cookie_params();
			setcookie(
				session_name(),
				'',
				time() - MAX_SESSION_TIEMPO,
				$params[ 'path' ],
				$params[ 'domain' ],
				$params[ 'secure' ],
				$params[ 'httponly' ] );
		}
		@session_destroy();
	}
	function formato_fecha_letras($fech)
	{
		switch(substr($fech,4,2))
		{
			case 1:		$mes='Enero';	break;
			case 2:		$mes='Febrero';	break;
			case 3:		$mes='Marzo';	break;
			case 4:		$mes='Abril';	break;
			case 5:		$mes='Mayo';	break;
			case 6:		$mes='Junio';	break;
			case 7:		$mes='Julio';	break;
			case 8:		$mes='Agosto';	break;
			case 9:		$mes='Setiembre';	break;
			case 10:	$mes='Octubre';	break;
			case 11:	$mes='Noviembre';	break;
			case 11:	$mes='Diciembre';	break;
		}
		if(substr($fech,6,2)>0)
			return substr($fech,6,2)." de $mes del ".substr($fech,0,4);
		else
			return "$mes del ".substr($fech,0,4);
	}
	function dar_formato_carpeta($codi,$anno=0)
	{
	    //$codi=ltrim(substr($codi,0,11),0).'-'.substr($codi,11,4).'-'.ltrim(substr($codi,15,6),0).'-'.number_format(substr($codi,21,4),0);
	    if($anno)
	        $codi=$anno.'-'.str_pad($codi, 4, "0", STR_PAD_LEFT);
	    return $codi;
	}
?>
