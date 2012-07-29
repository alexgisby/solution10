<?php

namespace Solution10\CSV;

/**
 * CSV Schema Class
 *
 * The Schema is used to describe how the CSV file should look. It can be used to validate both
 * input before reading a file, and output before writing a file. It also sets the delimeter,
 * string enclosure etc settings.
 *
 * @package 	Solution10\CSV
 * @category  	Classes
 * @author 		Alex Gisby <alex@solution10.com>
 * @license 	MIT
 */
class Schema
{
	/**
	 * @var 	string 	Field delimiter. Default ','
	 */
	public $delimiter = ',';
	
	/**
	 * @var 	string 	String enclosure character. Default "
	 */
	public $enclosure = '"';
	
	/**
	 * @var 	string 	Escape character. Default \
	 */
	public $escape_char = '\\';
	
	/**
	 * @var 	int 	Max line length in the CSV. Set to 0 for unlimited length, but with a performance hit.
	 */
	public $max_line_length = 0;
	
	/**
	 * @var 	array 	Fields in the CSV.
	 */
	protected $fields = array();
	
}