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
	
	
	/**
	 * Adds a field into the schema. Optionally add in rules for this field.
	 *
	 * @param 	int 	Numerical index of this field in the CSV. Column number essentially.
	 * @param 	string 	Field name
	 * @param 	array 	Validation rules for this field.
	 * @return 	this
	 */
	public function add_field($index, $name, $rules = array())
	{
		$this->fields[$index] = array(
			'name' => $name,
			'rules' => $rules,
		);
		
		return $this;
	}
	
	/**
	 * Fetching all the fields from the Schema
	 *
	 * @return 	array
	 */
	public function fields()
	{
		return $this->fields;
	}
	
}