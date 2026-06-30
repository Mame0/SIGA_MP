<?
class BaseDatos
{
	define('HOST','localhost'); //AQUI VA TU HOST
	define('USER','root');
	define('PASS','root');
	define('DBNAME','isolutions');

	protected $conexion;
	protected $db;

	public function conectar()
	{
		$this->conexion = mysql_connect(HOST, USER, PASS);
		if ($this->conexion == 0) DIE("Lo sentimos, no se ha podido conectar con MySQL: " . mysql_error());
			$this->db = mysql_select_db(DBNAME, $this->conexion);
		if ($this->db == 0) DIE("Lo sentimos, no se ha podido conectar con la base datos: " . DBNAME);
			return true;
	}

	public function desconectar()
	{
		if ($this->conectar->conexion)
		{
			mysql_close($this->$conexion);
		}

	}

	public function pruebadb()
	{
		$tabla = "TU_TABLA";
		$query = mysql_query("SELECT count(*) from $tabla", $this->conexion);
		if ($query == 0)
			echo "Sentencia incorrecta llamado a tabla: $tabla.";
		else
		{
			$nregistrostotal = mysql_result($query, 0, 0);
			echo "Hay $nregistrostotal registros en la tabla: $tabla.";
			mysql_free_result($query);
		}
	}
}
?>
