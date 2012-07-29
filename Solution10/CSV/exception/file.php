<?php

namespace Solution10\CSV\Exception;

/**
 * File Exception. Thrown if the file cannot be read or doesn't exist.
 *
 * @package 	csvIO
 * @category  	Exceptions
 * @author 		Alex Gisby <alex@solution10.com>
 */
class File extends Exception
{
	/**
	 * @var 	int 	File exists error
	 */
	const ERROR_EXISTS = 1;
	
	/**
	 * @var 	int 	Read file error
	 */
	const ERROR_READ = 2;
	
	
	/**
	 * @var 	int 	Write file error
	 */
	const ERROR_WRITE = 3;
	
	
	/**
	 * Constructor
	 *
	 * @param 	string 	Filename
	 * @param 	int 	Error type
	 */
	public function __construct($filename, $type)
	{
		// Print a friendly message:
		$message = 'File: ' . $filename . ' ';
		
		switch($type)
		{
			case File::ERROR_EXISTS:
				$message .= 'does not exist.';
			break;
			
			case File::ERROR_READ:
				$message .= 'cannot be read due to permissions.';
			break;
			
			case File::ERROR_WRITE:
				$message .= 'cannot be written due to permissions.';
			break;
		}
		
		return parent::__construct($message, $type);
	}
	
}