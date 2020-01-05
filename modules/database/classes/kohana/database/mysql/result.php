<?php defined('SYSPATH') or die('No direct script access.');
/**
 * MySQL database result.
 *
 * @package    Kohana
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Kohana_Database_MySQL_Result extends Database_Result {

	public function __construct($result, $sql)
	{
		parent::__construct($result, $sql);

		// Find the number of rows in the result
		$this->_total_rows = mysqli_num_rows($result);
	}

	public function __destruct()
	{
		if (is_resource($this->_result))
		{
			mysqli_free_result($this->_result);
		}
	}

	public function as_array($key = NULL, $value = NULL)
	{
		$array = array();

		if ($this->_total_rows > 0)
		{
			// Seek to the beginning of the result
			mysqli_data_seek($this->_result, 0);

			while ($row = mysqli_fetch_assoc($this->_result))
			{
				if ($key !== NULL)
				{
					if ($value !== NULL)
					{
						// Return the result as a $key => $value list
						$array[$row[$key]] = $row[$value];
					}
					else
					{
						// Return the result as a $key => $row list
						$array[$row[$key]] = $row;
					}
				}
				else
				{
					// Add each row to the array
					$array[] = $row;
				}
			}
		}

		return $array;
	}

	public function seek($offset)
	{
		if ($this->offsetExists($offset) AND mysqli_data_seek($this->_result, $offset))
		{
			// Set the current row to the offset
			$this->_current_row = $offset;

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function offsetGet($offset)
	{
		if ( ! $this->seek($offset))
			return FALSE;

		// Return an array of the row
		return mysqli_fetch_assoc($this->_result);
	}

} // End Database_MySQL_Result_Select