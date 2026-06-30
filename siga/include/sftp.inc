<?
$sftp_local=2;	//1:ftp y 2:local
GLOBAL $sftp_local;
function obtener_carpeta($tipo,$n_ano_ingreso,$ftp)
{
	GLOBAL $sftp_local;
	if($sftp_local==1)
	{
		$ftp->mkdir($n_ano_ingreso);
		$ftp->mkdir("$n_ano_ingreso/$tipo");
		$carpeta="$n_ano_ingreso/$tipo";
	}
	else
	{
		switch($tipo)
		{
			case 'logo':	$carpeta="img/logos";
					break;
			default:	$carpeta="img/varios";
					break;
		}
	}
	return $carpeta;
}
function subir_archivo($tipo,$fuente,$destino,$n_ano_ingreso)
{
	GLOBAL $sftp_local;
	if($sftp_local==1)
	{
		include_once "clases/SFTP.php";
		$ftp = new SFTP("172.28.4.204", "sioj", "5ioj@dmin");
		if($ftp->connect())
		{
			$ftp->passive = true;
			$carpeta=obtener_carpeta($tipo,$n_ano_ingreso,$ftp);
			if($ftp->put($fuente, "$carpeta/$destino",FTP_BINARY))
			{ 
			}
			else
			{ 
				print "<br />Upload failed: $carpeta/$destino" . $ftp->error; 
			}
		}
		else
			echo"ERROR DE CONEXION AL SERVIDOR FTPs";
	}
	else
	{
		$carpeta=obtener_carpeta($tipo,$n_ano_ingreso,'');
		move_uploaded_file("$fuente","$carpeta/$destino");
	}
}
function obtener_archivo($tipo,$nombre,$n_ano_ingreso,$alias='')
{
	GLOBAL $sftp_local;
	if($sftp_local==1)
	{
		include_once "clases/SFTP.php";
		$ftp = new SFTP("172.28.4.204", "sioj", "5ioj@dmin");
		$ubicacion=getcwd()."/";
		$directorio="archivos_temporales/".date("Ym");
		if(!file_exists("$ubicacion$directorio"))
			mkdir("$ubicacion$directorio", 0777, true);
		if($alias)
			$alias=$alias;
		else
			$alias=$nombre;
		$archivo = "$directorio/".str_pad(mt_rand(1, 9000000), 9, "0", STR_PAD_LEFT)."_$alias";
		if($ftp->connect())
		{
			$carpeta=obtener_carpeta($tipo,$n_ano_ingreso,$ftp);
			$ftp->get("$carpeta/$nombre", "$ubicacion$archivo",FTP_BINARY);
			$ftp->close();
		}
	}
	else
	{
		$carpeta=obtener_carpeta($tipo,$n_ano_ingreso,'');
		$archivo = "$carpeta/$nombre";
	}
	return $archivo;
}
?>
