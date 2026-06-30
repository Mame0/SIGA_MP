<?php
/*
 * Safe and simple PHP PDO Database class.
 *
 * @author      nikiedev <igor.nikiforov@ukr.net>
 * @copyright   Copyright (©) 2018
 * @licence     https://opensource.org/licenses/MIT - The MIT License
 * @link        https://github.com/nikiedev/simple-php-pdo-database-class
 * @version     1.0.0 beta
 *
 * --------------------------------
 * | alpha  - development version |
 * | beta   - test / fix version  |
 * | stable - production version  |
 * --------------------------------
 *
 * [ About ]
 *
 * This class was created for developers,
 * who knows SQL syntax and wanted to find
 * an easy to use, modern PDO MySQL Wrapper.
 * Using this class it is not necessary even
 * to read the documentation to know how to use it.
 * Names of the methods say for themselves.
 * Any wishes and suggestions are welcome!
 *
 */
/**
 * Class Db
 * @package lib
 */
class Db
{
	protected $dbh = null;
	protected $query = null;
	protected $timestamp_writes = false;
	protected $prefix = null;
	/**
	 * Db constructor.
	 * Connection to the Database
	 *
	 * @param string $driver
	 * @param string $host
	 * @param string $user
	 * @param string $pass
	 * @param string $name
	 * @param string $charset
	 * @param null   $prefix
	 */
//$acceso= mysql_connect("172.28.206.20", "useralimentos", "Admin5I0J$$");
	public function __construct($driver = 'mysql', $charset = 'utf8', $prefix = null)
	//public function __construct($driver = 'mysql', $charset = 'latin1', $prefix = null)
	{
		$credentialsFile = '.credentials/db.php.ini';
		if (!file_exists($credentialsFile)) {
			$credentialsFile = 'classes/.credentials/db.php.ini';
		}
		if (!file_exists($credentialsFile)) {
			$credentialsFile = '../.credentials/db.php.ini';
		}
		$this->credenciales = parse_ini_file($credentialsFile);
		
		$host = $this->credenciales["host"];
		if ($host === 'localhost') {
			$host = '[::1]'; // Solucionar problemas de IPv6/IPv4 local en Windows
		}
		
		$dsn = 'mysql:dbname=' . $this->credenciales["dbnombre"] . ';host=' . $host . '';
		$pass = $this->credenciales["clave"];
		$user = $this->credenciales["usuario"];

		if (!empty($prefix))
		{
			$this->prefix = $prefix;
		}
		$dsn       .= ';charset=' . $charset;
		try
		{
			$this->dbh = new \PDO(
				$dsn, $user, $pass, [
					\PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
					\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
				]
			);
			
			// Desactivar modo estricto de MySQL para esta sesión
			$this->dbh->exec("SET SESSION sql_mode = ''");
		}
		catch (\PDOException $e)
		{
			error_log($e);
			return false;
		}
	}
	/**
	 * @param string $sql
	 *
	 * @return bool
	 */
	public function execute($sql)
	{
		$sth = $this->dbh->prepare($sql);
        return $this->query->execute();
    }

    /**
     * Initiates a transaction
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->dbh->beginTransaction();
    }

    /**
     * Commits a transaction
     * @return bool
     */
    public function commit()
    {
        return $this->dbh->commit();
    }

    /**
     * Rolls back a transaction
     * @return bool
     */
    public function rollBack()
    {
        return $this->dbh->rollBack();
    }

    /**
     * @param string $query
     * @param array  $params
     *
	 * @return array
	 */
	public function query($query, $params = [])
	{
		$this->query = $this->dbh->prepare($query);
		if (empty($params))
		{
			$res = $this->query->execute();
		}
		else
		{
			$res = $this->query->execute($params);
		}
		if ($res !== false)
		{
			return $this->query->fetchAll();
		}
		return [];
	}
	/**
	 * @param $database
	 *
	 * @return bool
	 */

	public function get_options($table,$only_enable=1,$insert_new=0)
	{
		if($only_enable)
			$result=$this->select($table, ['n_estado'=>'1'], '', '', ['n_codigo'=>'ASC']);
		else
			$result=$this->select($table, '', '', '', ['n_codigo'=>'ASC']);
		$response[0]="< ".CONST_OPTION_SELECT." >";
		if($insert_new)
			$response[9999]="< ".CONST_OPTION_NEW." >";
		foreach($result as $rows)
			$response[$rows['n_codigo']]=$rows['x_nombre'];
		return $response;
	}
	public function get_options_dpto($table)
	{
		$result=$this->query("select distinct cdep as codi,depa from ubig_reni where cdis=:m_cdis AND cpro=:m_cpro order by depa",[':m_cdis'=>'00',':m_cpro'=>'00']);
		$response[0]="< ".CONST_OPTION_SELECT." >";
		foreach($result as $rows)
			$response[$rows['codi']]=utf8_encode($rows['depa']);
		return $response;
	}
	public function get_options_prov($table,$dpto)
	{
		$result=$this->query("select distinct CONCAT(cdep,cpro) as codi,prov from ubig_reni where cdis=:m_cdis AND cpro<>:m_cpro AND cdep=:m_cdep order by prov",[':m_cdis'=>'00',':m_cpro'=>'00',':m_cdep'=>$dpto]);
		$response[0]="< ".CONST_OPTION_SELECT." >";
		foreach($result as $rows)
			$response[$rows['codi']]=utf8_encode($rows['prov']);
		return $response;
	}
	public function get_options_dist($table,$prov)
	{
		$result=$this->query("select distinct CONCAT(cdep,cpro,cdis) as codi,dist from ubig_reni where CONCAT(cdep,cpro)=:m_cpro AND cpro<>:m_cero AND cdis<>:m_cdis order by dist",[':m_cpro'=>$prov,':m_cero'=>'00',':m_cdis'=>'00']);
		$response[0]="< ".CONST_OPTION_SELECT." >";
		foreach($result as $rows)
			$response[$rows['codi']]=utf8_encode($rows['dist']);
		return $response;
	}
	public function get_perfil_reparto($depe,$perf)
	{
		$result=$this->query("select MAX(codi_band) as band from mp_band_documento where codi_depe=:m_depe AND codi_perf=:m_perf AND esta_band=:m_esta",[':m_depe'=>$depe,':m_perf'=>$perf,':m_esta'=>'1']);
		$band=$result[0]['band'];
		if($band)
		{
			$result=$this->select('mp_band_documento', ['codi_band'=>$band], '', '','');
			$oper_orig=$result[0]['codi_oper'];
		}
		else
			$oper_orig=0;
		$result=$this->query("select MIN(iden_oper) as oper from mp_admi_oper where codi_depe=:m_depe AND codi_perf=:m_perf AND iden_oper>:m_oper AND esta_oper=:m_esta",[':m_depe'=>$depe,':m_perf'=>$perf,':m_oper'=>$oper_orig,':m_esta'=>'1']);
		$oper_nuev=$result[0]['oper'];
		if(!$oper_nuev)
		{
			$result=$this->query("select MIN(iden_oper) as oper from mp_admi_oper where codi_depe=:m_depe AND codi_perf=:m_perf AND iden_oper<=:m_oper AND esta_oper=:m_esta",[':m_depe'=>$depe,':m_perf'=>$perf,':m_oper'=>$oper_orig,':m_esta'=>'1']);
			$oper_nuev=$result[0]['oper'];
		}
		return $oper_nuev;
	}

	public function useDatabase($database)
	{
		$sql_str     = 'USE ' . $database;
		$this->query = $this->dbh->prepare($sql_str);
		return $this->query->execute();
	}
	/**
	 * @param $database
	 *
	 * @return bool
	 */
	public function createDatabase($database)
	{
		$sql_str     = 'CREATE DATABASE IF NOT EXISTS ' . $database . ' DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;';
		$this->query = $this->dbh->prepare($sql_str);
		return $this->query->execute();
	}

	/**
	 * @param $table
	 *
	 * @param array $columns
	 * @return bool
	 */
	public function createTable($table, $columns)
	{
		$sql_str = 'CREATE TABLE IF NOT EXISTS ' . $this->prefix . $table . ' . (id INT(11) NOT NULL AUTO_INCREMENT ';
		foreach ($columns as $col_key => $col_val)
		{
			$sql_str .= ', ' . $col_key . ' ' . $col_val;
		}
		$sql_str .= ', PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

		$this->query = $this->dbh->prepare($sql_str);
		return $this->query->execute();
	}
	/**
	 * method select.
	 *    - retrieve information from the database, as an array
	 *
	 * @param string|array $table    - the name of the db table we are retreiving the rows from
	 * @param array        $where    - associative array representing the WHERE clause filters
	 * @param int          $limit    (optional) - the amount of rows to return
	 * @param int          $start    (optional) - the row to start on, indexed by zero
	 * @param array        $order_by (optional) - an array with order by clause
	 *
	 * @return mixed - associate representing the fetched table row, false on failure
	 */
	public function select($table, $where = [], $limit = null, $start = null, $order_by = [])
	{
		// building query string
		$sql_str = 'SELECT ';
		if (is_array($table))
		{
			if (is_array($table[1]))
			{
				$sql_str .= implode(', ', $table[1]) . ' FROM ';
			}
			else
			{
				$sql_str .= $table[1] . ' FROM ';
			}
			$sql_str .= $this->prefix . $table[0];
		}
		else
		{
			$sql_str .= ' * FROM ' . $this->prefix . $table;
		}
		$add_and = false;
		if (!empty($where) and is_array($where))
		{
			// append WHERE if necessary
			$sql_str .= ' WHERE ';
			// add each clause using parameter array
			foreach ($where as $key => $val)
			{
				// only add AND after the first clause item has been appended
				if ($add_and)
				{
					$sql_str .= ' AND ';
				}
				else
				{
					$add_and = true;
				}
				// append clause item
				$sql_str .= $key . ' = :' . $key;
			}
		}
		// add the order by clause if we have one
		if (!empty($order_by))
		{
			$sql_str   .= ' ORDER BY ';
			$add_comma = false;
			foreach ($order_by as $column => $order)
			{
				if ($add_comma)
				{
					$sql_str .= ', ';
				}
				else
				{
					$add_comma = true;
				}
				$sql_str .= $column . ' ' . $order;
			}
		}
		try
		{
			// now we attempt to retrieve the row using the sql string
			$pdoDriver = $this->dbh->getAttribute(\PDO::ATTR_DRIVER_NAME);
			//@TODO MS SQL Server & Oracle handle LIMITs differently, for now its disabled but we should address it later.
			$disableLimit = ['sqlsrv', 'mssql', 'oci'];
			// add the limit clause if we have one
			if (!empty($limit) and !in_array($pdoDriver, $disableLimit))
			{
				$sql_str .= ' LIMIT ' . (!empty($start) ? $start . ', ' : '') . $limit;
			}
			$this->query = $this->dbh->prepare($sql_str);
			if (!empty($where) and is_array($where))
			{
				// bind each parameter in the array
				foreach ($where as $key => $val)
				{
					$this->query->bindValue(':' . $key, $val);
				}
			}
			$this->query->execute();
			// now return the results, depending on if we want all or first row only
			if (!is_null($limit) and $limit == 1)
			{
				return $this->query->fetch();
			}
			else
			{
				$res = [];
				while ($row = $this->query->fetch())
				{
					$res[] = $row;
				}
				return $res;
				// return $this->query->fetchAll(); >> may be not best when there are many rows
			}
		}
		catch (\PDOException $e)
		{
			error_log($e);
			return false;
		}
	}

	/**
	 * method selectJoin.
	 *    - retrieve information from the database, as an array from several tables
	 *
	 * @param array $table_cols - items of the db tables we are retreiving the rows from and joining
	 * @param array $conditions - associative array representing the WHERE clause filters
	 * @param array $where (optional)
	 * @param int $limit (optional) - the amount of rows to return
	 * @param int $start (optional) - the row to start on, indexed by zero
	 * @param array $order_by (optional) - an array with order by clause
	 *
	 * @return mixed - associate representing the fetched table row, false on failure
	 */
	public function selectJoin($table_cols = [], $conditions = [], $where = [], $limit = null, $start = null, $order_by = [])
	{
		// building query string
		$sql_str = 'SELECT ';

		$key_number = 0;

		foreach ($table_cols as $table_name => $columns)
		{
			$sql_str .= $table_name . '.';

			$sql_str .= implode(', '.$table_name.'.', $columns);

			$key_number++;
			if ($key_number != count($table_cols))
			{
				$sql_str .= ', ';
			}

		}

		if (!function_exists('array_key_first')) {
			function array_key_first(array $arr) {
				foreach($arr as $key => $unused) {
					return $key;
				}
				return NULL;
			}
		}

		$sql_str .= ' FROM ' . $this->prefix . array_key_first($table_cols);

		foreach ($conditions as $cond_tbl => $cond_cols)
		{
			$sql_str .= ' JOIN ' . $this->prefix . $cond_tbl . ' ON ' . $this->prefix . $cond_cols[0] . ' = ' . $cond_cols[1];
		}

		$add_and = false;

		if (!empty($where) and is_array($where))
		{
			// append WHERE if necessary
			$sql_str .= ' WHERE ';
			// add each clause using parameter array
			foreach ($where as $key => $val)
			{
				// only add AND after the first clause item has been appended
				if ($add_and)
				{
					$sql_str .= ' AND ';
				}
				else
				{
					$add_and = true;
				}

				// append clause item
				$sql_str .= $key . ' = :' . $key;
			}
		}

		// add the order by clause if we have one
		if (!empty($order_by))
		{
			$sql_str   .= ' ORDER BY ';
			$add_comma = false;
			foreach ($order_by as $column => $order)
			{
				if ($add_comma)
				{
					$sql_str .= ', ';
				}
				else
				{
					$add_comma = true;
				}
				$sql_str .= $column . ' ' . $order;
			}
		}
		try
		{
			// now we attempt to retrieve the row using the sql string
			$pdoDriver = $this->dbh->getAttribute(\PDO::ATTR_DRIVER_NAME);

			//@TODO MS SQL Server & Oracle handle LIMITs differently, for now its disabled but we should address it later.
			$disableLimit = ['sqlsrv', 'mssql', 'oci'];

			// add the limit clause if we have one
			if (!empty($limit) and !in_array($pdoDriver, $disableLimit))
			{
				$sql_str .= ' LIMIT ' . (!empty($start) ? $start . ', ' : '') . $limit;
			}

			$this->query = $this->dbh->prepare($sql_str);

			if (!empty($where) and is_array($where))
			{
				// bind each parameter in the array
				foreach ($where as $key => $val)
				{
					$this->query->bindValue(':' . $key, $val);
				}
			}

			$this->query->execute();

			// now return the results, depending on if we want all or first row only
			if (!is_null($limit) and $limit == 1)
			{
				return $this->query->fetch();
			}
			else
			{
				$res = [];
				while ($row = $this->query->fetch())
				{
					$res[] = $row;
				}

				return $res;
				// return $this->query->fetchAll(); >> may be not best when there are many rows
			}

		}
		catch (\PDOException $e)
		{
			error_log($e);

			return false;
		}
	}

	/**
	 * method insert.
	 *    - adds a row to the specified table
	 *
	 * @param string $table          - the name of the db table we are adding row to
	 * @param array  $params         - associative array representing the columns and their respective values
	 * @param bool   $timestamp_this (Optional), if true we set date_created and date_modified values to now
	 *
	 * @return mixed - new primary key of inserted table, false on failure
	 */
	public function insert($table, $params, $timestamp_this = null)
	{
		if (is_null($timestamp_this))
		{
			$timestamp_this = $this->timestamp_writes;
		}
		// first we build the sql query string
		$columns_str = ' (';
		$values_str  = ' VALUES (';
		$add_comma   = false;
		// add each parameter into the query string
		foreach ($params as $key => $val)
		{
			// only add comma after the first parameter has been appended
			if ($add_comma)
			{
				$columns_str .= ', ';
				$values_str  .= ', ';
			}
			else
			{
				$add_comma = true;
			}
			// now append the parameter
			$columns_str .= $key;
			$values_str  .= ':' . $key;
		}
		// add the timestamp columns if necessary
		if ($timestamp_this === true)
		{
			$columns_str .= ($add_comma ? ', ' : '') . 'date_created, date_modified';
			$values_str  .= ($add_comma ? ', ' : '') . time() . ', ' . time();
		}
		// close the builder strings
		$columns_str .= ') ';
		$values_str  .= ')';
		// build final insert string
		$sql_str = 'INSERT INTO ' . $this->prefix . $table . $columns_str . $values_str;
//echo"$sql_str";
		// now we attempt to write this row into the database
		try
		{
			$this->query = $this->dbh->prepare($sql_str);
			// bind each parameter in the array
			foreach ($params as $key => $val)
			{
				//agregado por jesus para evitar los errores con NULL
				if(!$val && !is_numeric($val))
					$val='';
				//hasta aqui
				if ($val === 'CURRENT_TIMESTAMP' || $val === 'NOW()')
				{
					$val = date('Y-m-d H:i:s');
				}
				$this->query->bindValue(':' . $key, $val);
//echo"<HR>$key - $val";
			}
			$this->query->execute();
			$newID = $this->dbh->lastInsertId();
			// return the new id
			return $newID;
		}
		catch (\PDOException $e)
		{
			error_log($e);
//echo"<HR>$e";
			return false;
		}
	}
	/**
	 * method insertMultiple.
	 *    - adds multiple rows to a table with a single query
	 *
	 * @param string $table           - the name of the db table we are adding row to
	 * @param array  $columns         - contains the column names
	 * @param array  $rows            - contains the rows with values
	 * @param bool   $timestamp_these (Optional), if true we set date_created and date_modified values to NOW() for each row
	 *
	 * @return mixed - new primary key of inserted table, false on failure
	 */
	public function insertMultiple($table, $columns = [], $rows = [], $timestamp_these = null)
	{
		if (is_null($timestamp_these))
		{
			$timestamp_these = $this->timestamp_writes;
		}
		// generate the columns portion of the insert statement
		// adding the timestamp fields if needs be
		if ($timestamp_these === true)
		{
			$columns[] = 'date_created';
			$columns[] = 'date_modified';
		}
		$columns_str = ' (' . implode(',', $columns) . ') ';
		// generate the values portions of the string
		$values_str = 'VALUES ';
		$add_comma  = false;
		foreach ($rows as $row_index => $row_values)
		{
			// only add comma after the first row has been added
			if ($add_comma)
			{
				$values_str .= ', ';
			}
			else
			{
				$add_comma = true;
			}
			// here we will create the values string for a single row
			$values_str          .= ' (';
			$add_comma_for_value = false;
			foreach ($row_values as $value_index => $value)
			{
				if ($add_comma_for_value)
				{
					$values_str .= ', ';
				}
				else
				{
					$add_comma_for_value = true;
				}
				// generate the bind variable name based on the row and column index
				$values_str .= ':' . $row_index . '_' . $value_index;
			}
			// append timestamps if necessary
			if ($timestamp_these)
			{
				$values_str .= ($add_comma_for_value ? ', ' : '') . time() . ', ' . time();
			}
			$values_str .= ')';
		}
		// build final insert string
		$sql_str = 'INSERT INTO ' . $this->prefix . $table . $columns_str . $values_str;
		// now we attempt to write this multi insert query to the database using a transaction
		try
		{
			$this->dbh->beginTransaction();
			$this->query = $this->dbh->prepare($sql_str);
			// traverse the 2d array of rows and values to bind all parameters
			foreach ($rows as $row_index => $row_values)
			{
				foreach ($row_values as $value_index => $value)
				{
					$this->query->bindValue(':' . $row_index . '_' . $value_index, $value);
				}
			}
			// now lets execute the statement, commit the transaction and return
			$this->query->execute();
			$this->dbh->commit();
			return true;
		}
		catch (\PDOException $e)
		{
			$this->dbh->rollback();
			error_log($e);
			return false;
		}
	}

	public function selectUserBlob($iden)
	{
		$sql = "SELECT firm_oper FROM mp_admi_oper_firm WHERE iden_oper=:m_iden;";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(":m_iden" => $iden));
		$stmt->bindColumn(1, $firm, PDO::PARAM_LOB);

		$stmt->fetch(PDO::FETCH_BOUND);

		return array("firm_oper" => $firm);
	}

	public function dataUserBlob($iden,$firm)
	{
		$blob = fopen($firm, 'rb');

		$result=$this->select('mp_admi_oper_firm', ['iden_oper'=>$iden]);
		if($result[0]['iden_oper'])
			$sql = "UPDATE mp_admi_oper_firm SET firm_oper=:m_firm WHERE iden_oper=:m_iden";
		else
			$sql = "INSERT INTO mp_admi_oper_firm(iden_oper,firm_oper) VALUES(:m_iden,:m_firm)";

		$stmt = $this->dbh->prepare($sql);

		$stmt->bindParam(':m_iden',$iden);
		$stmt->bindParam(':m_firm',$blob,PDO::PARAM_LOB);
		return $stmt->execute();
	}


	/**
	 * @return string - the last inserted id. Needs in ajax often, for example
	 */
	public function lastInsertId()
	{
		return $this->dbh->lastInsertId();
	}
	
	public function rowCount()
	{
		return $this->dbh->rowCount();
	}

	/**
	 * method update.
	 *    - updates a row to the specified table
	 *
	 * @param string $table          - the name of the db table we are adding row to
	 * @param array  $params         - associative array representing the columns and their respective values to update
	 * @param array  $wheres         (Optional) - the where clause of the query
	 * @param bool   $timestamp_this (Optional) - if true we set date_created and date_modified values to now
	 *
	 * @return int|bool - the amount of rows updated, false on failure
	 */
	public function update($table, $params, $wheres = [], $timestamp_this = null)
	{
		if (is_null($timestamp_this))
		{
			$timestamp_this = $this->timestamp_writes;
		}
		// build the set part of the update query by
		// adding each parameter into the set query string
		$add_comma  = false;
		$set_string = '';
		foreach ($params as $key => $val)
		{
			// only add comma after the first parameter has been appended
			if ($add_comma)
			{
				$set_string .= ', ';
			}
			else
			{
				$add_comma = true;
			}
			// now append the parameter
			$set_string .= $key . '=:param_' . $key;
		}
		// add the timestamp columns if necessary
		if ($timestamp_this === true)
		{
			$set_string .= ($add_comma ? ', ' : '') . 'date_modified=' . time();
		}
		// lets add our where clause if we have one
		$where_string = '';
		if (!empty($wheres))
		{
			// load each key value pair, and implode them with an AND
			$where_array = [];
			foreach ($wheres as $key => $val)
			{
				$where_array[] = $key . '=:where_' . $key;
			}
			// build the final where string
			$where_string = ' WHERE ' . implode(' AND ', $where_array);
		}
		// build final update string
		$sql_str = 'UPDATE ' . $this->prefix . $table . ' SET ' . $set_string . $where_string;
		// now we attempt to write this row into the database
		try
		{
			$this->query = $this->dbh->prepare($sql_str);
			// bind each parameter in the array
			foreach ($params as $key => $val)
			{
				$this->query->bindValue(':param_' . $key, $val);
			}
			// bind each where item in the array
			foreach ($wheres as $key => $val)
			{
				$this->query->bindValue(':where_' . $key, $val);
			}
			// execute the update query
			$successful_update = $this->query->execute();
			// if we were successful, return the amount of rows updated, otherwise return false
			return ($successful_update == true) ? $this->query->rowCount() : false;
		}
		catch (\PDOException $e)
		{
			error_log($e);
			return false;
		}
	}
	/**
	 * method delete.
	 *    - deletes rows from a table based on the parameters
	 *
	 * @param $table  - the name of the db table we are deleting the rows from
	 * @param $params - associative array representing the WHERE clause filters
	 *
	 * @return bool - associate representing the fetched table row, false on failure
	 */
	public function delete($table, $params = [])
	{
		// building query string
		$sql_str = 'DELETE FROM ' . $this->prefix . $table;
		// append WHERE if necessary
		$sql_str .= (count($params) > 0 ? ' WHERE ' : '');
		$add_and = false;
		// add each clause using parameter array
		foreach ($params as $key => $val)
		{
			// only add AND after the first clause item has been appended
			if ($add_and)
			{
				$sql_str .= ' AND ';
			}
			else
			{
				$add_and = true;
			}
			// append clause item
			$sql_str .= $key . ' = :' . $key;
		}
		// now we attempt to retrieve the row using the sql string
		try
		{
			$this->query = $this->dbh->prepare($sql_str);
			// bind each parameter in the array
			foreach ($params as $key => $val)
			{
				$this->query->bindValue(':' . $key, $val);
			}
			// execute the delete query
			$successful_delete = $this->query->execute();
			// if we were successful, return the amount of rows updated, otherwise return false
			return ($successful_delete == true) ? $this->query->rowCount() : false;
		}
		catch (\PDOException $e)
		{
			error_log($e);
			return false;
		}
	}
	/**
	 * @param $table
	 *
	 * @return bool
	 */
	public function optimizeTable($table)
	{
		$sql_str     = 'OPTIMIZE TABLE ' . $this->prefix . $table . ';';
		$this->query = $this->dbh->prepare($sql_str);
		return $this->query->execute();
	}
	/**
	 * @param $table
	 *
	 * @return bool
	 */
	public function truncateTable($table)
	{
		$sql_str     = 'TRUNCATE TABLE ' . $this->prefix . $table . ';';
		$this->query = $this->dbh->prepare($sql_str);
		return $this->query->execute();
	}
	/**
	 * @param $table
	 *
	 * @return bool
	 */
	public function dropTable($table)
	{
		$sql_str     = 'DROP TABLE IF EXISTS ' . $this->prefix . $table;
		$this->query = $this->dbh->prepare($sql_str);
		return $this->query->execute();
	}
	/**
	 * @param $database
	 *
	 * @return bool
	 */
	public function dropDatabase($database)
	{
		$sql_str     = 'DROP DATABASE IF EXISTS ' . $database . ';';
		$this->query = $this->dbh->prepare($sql_str);
		return $this->query->execute();
	}
}
?>