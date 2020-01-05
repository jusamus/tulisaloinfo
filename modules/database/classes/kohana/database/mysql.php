<?php defined('SYSPATH') or die('No direct script access.');
/**
 * MySQL database connection.
 *
 * @package    Kohana
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Kohana_Database_MySQL extends Database {

	// Use SET NAMES to set the character set
	protected static $_set_names;

	// MySQL uses a backtick for identifiers
	protected $_identifier = '`';

	public function connect()
	{
		if ($this->_connection)
			return;

		if (Database_MySQL::$_set_names === NULL)
		{
			// Determine if we can use mysql_set_charset(), which is only
			// available on PHP 5.2.3+ when compiled against MySQL 5.0+
			Database_MySQL::$_set_names = ! function_exists('mysqli_set_charset');
		}

		// Extract the connection parameters, adding required variabels
		extract($this->_config['connection'] + array(
			'database'   => '',
			'hostname'   => '',
			'port'       => NULL,
			'socket'     => NULL,
			'username'   => '',
			'password'   => '',
			'persistent' => FALSE,
		));

		// Clear the connection parameters for security
		unset($this->_config['connection']);
                
		try
		{
			if (empty($persistent))
			{
				// Create a connection and force it to be a new link
				$this->_connection = mysqli_connect($hostname, $username, $password);
			}
			else
			{
				// Create a persistent connection
				$this->_connection = mysqli_pconnect($hostname, $username, $password);
			}
		}
		catch (ErrorException $e)
		{
			// No connection exists
			$this->_connection = NULL;

			// Unable to connect to the database
			throw new Database_Exception(':error',
				array(':error' => mysqli_error($this->_connection)),
				mysqli_errno());
		}

		if ( ! mysqli_select_db($this->_connection, $database))
		{
			// Unable to select database
			throw new Database_Exception(':error',
				array(':error' => mysqli_error($this->_connection)),
				mysqli_errno($this->_connection));
		}

		if ( ! empty($this->_config['charset']))
		{
			// Set the character set
			$this->set_charset($this->_config['charset']);
		}
	}

	public function disconnect()
	{
		try
		{
			// Database is assumed disconnected
			$status = TRUE;

			if (is_resource($this->_connection))
			{
				$status = mysqli_close($this->_connection);
			}
		}
		catch (Exception $e)
		{
			// Database is probably not disconnected
			$status = is_resource($this->_connection);
		}

		return $status;
	}

	public function set_charset($charset)
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();

		if (Database_MySQL::$_set_names === TRUE)
		{
			// PHP is compiled against MySQL 4.x
			$status = (bool) mysqli_query($this->_connection, 'SET NAMES '.$this->quote($charset));
		}
		else
		{
			// PHP is compiled against MySQL 5.x
			$status = mysqli_set_charset($this->_connection, $charset);
		}

		if ($status === FALSE)
		{
			throw new Database_Exception(':error',
				array(':error' => mysqli_error($this->_connection)),
				mysqli_errno($this->_connection));
		}
	}

	public function query($type, $sql)
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();

		if ( ! empty($this->_config['profiling']))
		{
			// Benchmark this query for the current instance
			$benchmark = Profiler::start("Database ({$this->_instance})", $sql);
		}

		// Execute the query
		if (($result = mysqli_query($this->_connection, $sql)) === FALSE)
		{
			if (isset($benchmark))
			{
				// This benchmark is worthless
				Profiler::delete($benchmark);
			}

			throw new Database_Exception(':error [ :query ]',
				array(':error' => mysqli_error($this->_connection), ':query' => $sql),
				mysql_errno($this->_connection));
		}

		if (isset($benchmark))
		{
			Profiler::stop($benchmark);
		}

		// Set the last query
		$this->last_query = $sql;

		if ($type === Database::SELECT)
		{
			// Return an iterator of results
			return new Database_MySQL_Result($result, $sql);
		}
		elseif ($type === Database::INSERT)
		{
			// Return a list of insert id and rows created
			return array(
				mysqli_insert_id($this->_connection),
				mysqli_affected_rows($this->_connection),
			);
		}
		else
		{
			// Return the number of rows affected
			return mysqli_affected_rows($this->_connection);
		}
	}

	public function list_tables($like = NULL)
	{
		if (is_string($like))
		{
			// Search for table names
			$result = $this->query(Database::SELECT, 'SHOW TABLES LIKE '.$this->quote($like))->as_array();
		}
		else
		{
			// Find all table names
			$result = $this->query(Database::SELECT, 'SHOW TABLES')->as_array();
		}

		$tables = array();
		foreach ($result as $row)
		{
			// Get the table name from the results
			$tables[] = current($row);
		}

		return $tables;
	}

	public function list_columns($table, $like = NULL)
	{
		// Quote the table name
		$table = $this->quote_identifier($table);

		if (is_string($like))
		{
			// Search for column names
			$result = $this->query(Database::SELECT, 'SHOW COLUMNS FROM '.$table.' LIKE '.$this->quote($like));
		}
		else
		{
			// Find all column names
			$result = $this->query(Database::SELECT, 'SHOW COLUMNS FROM '.$table)->as_array();
		}

		$columns = array();
		foreach ($result as $row)
		{
			// Get the column name from the results
			$columns[] = $row['Field'];
		}

		return $columns;
	}

	public function escape($value)
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();

		if (($value = mysqli_real_escape_string($this->_connection, (string) $value)) === FALSE)
		{
			throw new Database_Exception(':error',
				array(':error' => mysqli_errno($this->_connection)),
				mysqli_error($this->_connection));
		}

		// SQL standard is to use single-quotes for all values
		return "'$value'";
	}

} // End Database_MySQL