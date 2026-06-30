<?
function do_conection()
{
	$acceso= mysql_connect("localhost", "root", "root");
	//$acceso= mysql_connect("localhost", "root", "");
	if (!$acceso)
	{
		poner_mensaje("ERROR DE CONEXION!!!");
		die();
	}
	if (!mysql_select_db("alimentos",$acceso))
	{
		poner_mensaje("ERROR AL ABRIR BASE DE DATOS!!!");
		die();
	}
	return $acceso;
}
?>
