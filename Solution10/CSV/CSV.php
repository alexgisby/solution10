<?php

namespace Solution10\CSV;

/**
 * CSV Parsing Class.
 *
 * This class is a subclass of Collection designed specifically for manipulating CSV (comma separated values)
 * files. Since it's a subclass, you can iterate over, splice and count the CSV file exactly as if it was
 * an instance of Collection.
 *
 * @package 	Solution10\CSV
 * @category  	Classes
 * @author 		Alex Gisby <alex@solution10.com>
 */
class CSV extends \Solution10\Collection\Collection
{
	/**
	 * @var 	string 	Filename
	 */
	protected $filepath;
	
	
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
				$this->contents = array();
				while(($row = fgetcsv($fh, 1000)) !== false)
				{
					$this->contents[] = $row;
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
	
}