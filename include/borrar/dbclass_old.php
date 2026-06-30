<?
class dbmysql
{

	protected $conexion;
	protected $db;

	public function connect()
	{
	define("HOST","localhost"); //AQUI VA TU HOST
	define("USER","root");
	define("PASS","root");
	define("DBNAME","isolutions");
		$this->conexion = mysql_connect(HOST, USER, PASS);
		if ($this->conexion == 0) DIE("Lo sentimos, no se ha podido conectar con MySQL: " . mysql_error());
			$this->db = mysql_select_db(DBNAME, $this->conexion);
		if ($this->db == 0) DIE("Lo sentimos, no se ha podido conectar con la base datos: " . DBNAME);
			return $this->conexion;
	}
	//funcion para crear tablas
	public function creartabla($sql)
	{
		if ($this->conexion->query($sql) === TRUE)
		{
			echo "Se ha creado una tabla";
		}
		else
		{
			echo "Fallo:no se ha creado la tabla ".$this->conexion->error;
		}
	}
 
	//Guardar nuevos datos en la base de datos
	public function insertar($tabla, $camposdatos)
	{
		//separamos los datos por si son varios
		$campo = implode(", ", array_keys($camposdatos));
		$i=0;
		foreach($camposdatos as $indice=>$valor)
		{
			$dato[$i] = "'".$valor."'";
			$i++;
		}
		$datos = implode(", ",$dato);
	
		//Insertamos los valores en cada campo
		if($this->connection->query("INSERT INTO $tabla ($indice) VALUES ($dato)") === TRUE)
		{
			echo "Nuevo cliente insertado";
		}
		else
		{
			echo "Fallo no se ha insertado el cliente ".$this->conexion->error;
		}
	}
 
	//Borrar datos  de la base de datos
	public function borrar($tabla, $camposdatos)
	{
		$i=0;
		foreach($camposdatos as $indice=>$valor)
		{
			$dato[$i] = "'".$valor."'";
			$i++;
		}
	
		$campoydato = implode(" AND ",$dato);
		if($this->conexion->query("DELETE FROM $tabla WHERE $campoydato") === TRUE)
		{
			if(mysqli_affected_rows($this->conexion))
			{
				echo "Registro eliminado";
			}
			else
			{
				//echo "Fallo no se pudo eliminar el registro".$this->conexion->error;
			}
		}
	}
	public function Actualizar($tabla, $camposset, $camposcondicion)
	{
		//separamos los valores SET a modificar
		$i=0;
		foreach($camposset as $indice=>$dato)
		{
			$datoset[$i] = $indice." = '".$dato."'";
			$i++;
		}
		$consultaset = implode(", ",$datoset);
		$i=0;
		foreach($camposcondicion as $indice=>$datocondicion)
		{
			$condicion[$i] = $indice." = '".$datocondicion."'";
			$i++;
		}
		$consultacondicion = implode(" AND ",$condicion);

		//Actualización de registros
		if($this->conexion->query("UPDATE $tabla SET $consultaset WHERE $consultacondicion") === TRUE)
		{
			if(mysqli_affected_rows($this->conexion))
			{
				echo "Registro actualizado";
			}
			else
			{
				echo "Fallo no se pudo eliminar el registro".$this->conexion->error;
			}
		}
	}
 
	// funcion Buscar en una tabla
	public function buscar($tabla, $campos)
	{
		$campos=implode(",",$campos);
		$resultado=$this->conexion->query("SELECT $campos FROM $tabla");
		return $resultado->fetch_all(MYSQLI_ASSOC);
	}
}
?>
