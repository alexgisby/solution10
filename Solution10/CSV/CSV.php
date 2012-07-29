<?php

namespace Solution10\CSV;

/**
 * Main entry point of the class.
 *
 * @package 	Solution10\CSV
 * @category  	Classes
 * @author 		Alex Gisby <alex@solution10.com>
 */
class CSV implements \Countable, \ArrayAccess, \Iterator
{
	/**
	 * @var 	string 	Filename
	 */
	protected $filepath;
	
	/**
	 * @var 	array 	Rows
	 */
	protected $rows;
	
	/**
	 * @var 	string 	delimiter
	 */
	protected $delimiter;
	
	/**
	 * @var 	string 	enclosure
	 */
	protected $enclosure;
	
	/**
	 * @var 	string 	Escape character
	 */
	protected $escape_char;
	
	/**
	 * Constructor
	 *
	 * @param 	string 	Filename of CSV or false if no file
	 * @param 	string 	Delimiter between fields
	 * @param 	string 	String enclosure character
	 * @param 	string 	Escape character
	 */
	public function __construct($filepath = false, $delimiter = ',', $enclosure = '"')
	{
		$this->delimiter 	= $delimiter;
		$this->enclosure 	= $enclosure;
		
		if($filepath)
		{
			$this->read_file($filepath);
		}
	}
	
	
	/**
	 * Reads in a file.
	 *
	 * @param 	string 	Path to file
	 * @return 	this
	 */
	public function read_file($filepath)
	{
		if(file_exists($filepath))
		{
			$this->filepath = $filepath;
			
			// Loop through the file and store the rows:
			if(($fh = @fopen($filepath, 'r')) !== false)
			{
				$this->rows = array();
				while(($row = fgetcsv($fh, 1000)) !== false)
				{
					$this->rows[] = $row;
				}
				
				fclose($fh);
			}
			else
			{
				throw new Exception\File($filepath, Exception\File::ERROR_READ);
			}
			
		}
		else
		{
			throw new Exception\File($filepath, Exception\File::ERROR_EXISTS);
		}
		
		return $this;
	}
	
	
	
	/**
	 * ------------------------ Countable Implementation ---------------------------
	 */
	
	public function count()
	{
		return count($this->rows);
	}
	
	
	/**
	 * ----------------------- Array Access Implementation -------------------------
	 */
	
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->rows);
	}
	
	
	/**
	 * This function contains a lot of the magic with splicing and such.
	 *
	 * @param 	mixed 	INT for numeric index. String splice selector otherwise.
	 * @return 	array
	 */
	public function offsetGet($offset)
	{
		if(is_int($offset))
		{
			return $this->rows[$offset];
		}
		elseif($offset == ':last')
		{
			// Shortcut for fetching the last item in the CSV:
			return $this->rows[count($this->rows) - 1];
		}
		elseif(preg_match('/(?P<start>-?[0-9]+):(?P<end>[0-9]+|END)/', $offset, $matches))
		{
			$start 	= $matches['start'];
			$end	= $matches['end'];
			
			// You can use the END keyword to select everything up until an end point.
			if($end == 'END')
				$end = count($this->rows) - 1;
			
			// If the start is negative, we count backwards from the end of the CSV. array_slice can handle negative
			// offsets, but bounds checking gets a bit gnarly.
			if($start < 0)
				$start = (count($this->rows)) - abs($start);
			
			// Check the bounds:
			if($start >= count($this->rows))
			{
				throw new Exception\Bounds('Start index (' . $start . ') is beyond the end of the file', Exception\Bounds::ERROR_START_OUT_OF_RANGE);
			}
			elseif($start > $end)
			{
				throw new Exception\Bounds('Start index is greater than end index: ' . $start . ' > ' . $end, Exception\Bounds::ERROR_START_GT_END);
			}
			
			// If the end index is > the total, we set to the total:
			if($end >= count($this->rows))
			{
				$end = count($this->rows) - 1;
			}
			
			// Now work out the params for array_slice
			$offset = $start;
			$length	= ($end - $start) + 1;
			
			return array_slice($this->rows, $offset, $length);
		}
		
		// We've got an index we don't know what to do with. Throw an exception:
		throw new Exception\Index('Unknown index: ' . $offset);
	}
	
	public function offsetSet($offset, $value)
	{
		if(is_null($offset))
		{
			$this->rows[] = $value;
		}
		else
		{
			$this->rows[$offset] = $value;
		}
	}
	
	public function offsetUnset($offset)
	{
		unset($this->rows[$offset]);
	}
	
	
	/**
	 * ---------------------- Array Access Implementation ----------------------------
	 */
	
	protected $iter_current_pos = 0;
	
	public function current()
	{
		return $this->rows[$this->iter_current_pos];
	}
	
	public function key()
	{
		return $this->iter_current_pos;
	}
	
	public function next()
	{
		$this->iter_current_pos ++;
	}
	
	public function rewind()
	{
		$this->iter_current_pos = 0;
	}
	
	public function valid()
	{
		return isset($this->rows[$this->iter_current_pos]);
	}
}