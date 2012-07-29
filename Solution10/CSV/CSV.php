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
	 * @var 	Schema 	Schema for the file.
	 */
	protected $schema;
	
	/**
	 * @var 	array 	Bad rows stripped out of the CSV that do not validate.
	 */
	protected $bad_rows = array();
	
	/**
	 * Constructor
	 *
	 * @param 	string 	Filename of CSV or false if no file
	 * @param 	Schema	Schema to use for this file.
	 * @param 	string 	Escape character
	 */
	public function __construct($filepath = false, Schema $schema = null)
	{
		if($schema)
		{
			$this->schema($schema);
		}
		else
		{
			// Create a default schema:
			$this->schema(new Schema());
		}
		
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
				while(($row = fgetcsv($fh, $this->schema->max_line_length, 
											$this->schema->delimiter, 
											$this->schema->enclosure,
											$this->schema->escape_char)) !== false)
				{
					try
					{
						$this->schema->validate_row($row);
						$this->contents[] = $row;
					}
					catch(\Solution10\CSV\Exception\Validation $e)
					{
						// Bad row!
						$this->bad_rows[] = $row;
					}
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
	 * Gets / sets the Schema for the file.
	 *
	 * @param 	Schema 	(optional) set the schema for this CSV
	 * @return 	Schema 	The current Schema on this file
	 */
	public function schema(Schema $s = null)
	{
		if($s)
		{
			$this->schema = $s;
		}
		
		return $this->schema;
	}
	
	/**
	 * Retrieving the bad rows from the CSV file
	 */
	public function bad_rows()
	{
		return $this->bad_rows;
	}
}