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
	
	
	/**
	 * Validates a row of data against the current schema
	 *
	 * @param 	array 	Row of data
	 * @throws 	Solution10\CSV\Exception\Validation
	 */
	public function validate_row(array $data)
	{
		foreach($this->fields as $index => $field)
		{
			if(!array_key_exists($index, $data))
			{
				// Index doesn't exist. Do something more useful here later.
				return false;
			}
			
			// Loop through the rules, calling them one by one:
			foreach($field['rules'] as $rule)
			{
				if(is_string($rule))
				{
					// Try and call this function on the Schema Object:
					$func_name = 'validate_' . $rule;
					if(method_exists($this, $func_name))
					{
						$this->$func_name($data[$index]);
					}
					else
					{
						// Throw an exception here to say we don't know this function.
						throw new Exception\Validation('Unknown validation method: "' . $rule . '"', Exception\Validation::ERROR_UNKNOWN_METHOD);
					}
				}
				elseif($rule instanceof \Closure)
				{
					// Callback function!
					$rule($data[$index]);
				}
			}
		}
	}
	
	/**
	 * ----------------------- Built in Validation ---------------------------
	 */
	
	/**
	 * Checks that an item is not null or empty.
	 *
	 * @param 	mixed 	Input data
	 * @throws 	Solution10\CSV\Exception\Validation
	 */
	protected function validate_not_empty($value)
	{
		if(!($value !== null && $value != '' && !empty($value)))
		{
			throw new Exception\Validation('Value is empty', Exception\Validation::ERROR_NOT_EMPTY);
		}
	}
	
	/**
	 * Validates an email address
	 *
	 * @param 	mixed 	Value
	 * @throws 	Solution10\CSV\Exception\Validation
	 */
	protected function validate_email($value)
	{
		if(filter_var($value, FILTER_VALIDATE_EMAIL) === false)
		{
			throw new Exception\Validation('Value is not a valid email', Exception\Validation::ERROR_EMAIL);
		}
	}
	
	
	
}