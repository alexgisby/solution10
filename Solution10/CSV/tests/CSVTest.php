<?php

/**
 * Tests for the big daddy CSV class
 */
class CSVTest extends Solution10\Tests\TestCase
{
	/**
	 * @var 	CSV 	Test csv as it's used everywhere
	 */
	protected $csv;
	
	/**
	 * Set up the tests
	 */
	public function setUp()
	{
		$this->csv = new Solution10\CSV\CSV('Solution10/CSV/tests/data/test.csv');
	}
	
	/**
	 * Tests file finding
	 *
	 * @expectedException 		Solution10\CSV\Exception\File
	 * @expectedExceptionCode	1
	 */
	public function testFileFindFail()
	{
		$csv = new Solution10\CSV\CSV('Solution10/CSV/tests/data/does_not_exist.csv');
	}
	
	/**
	 * Tests file finding with a real file
	 */
	public function testFileFindSuccess()
	{
		$csv = new Solution10\CSV\CSV('Solution10/CSV/tests/data/test.csv');
		
		// Probably not the right way of doing this...
		$this->assertEquals(true, true);
	}
	
	
	/**
	 * Testing the creation of files with a schema.
	 */
	public function testAddingSchema()
	{
		$schema = new Solution10\CSV\Schema();
		$schema->delimiter = '	';
		$schema->enclosure = '@';
		$schema->escape_char = '_';
		$schema->max_line_length = 1000;
		
		$csv = new Solution10\CSV\CSV('Solution10/CSV/tests/data/test.csv', $schema);
		$this->assertTrue($csv instanceof Solution10\CSV\CSV);
	}
	
	/**
	 * Testing accessing the schema
	 */
	public function testRetrieveSchema()
	{
		$schema = new Solution10\CSV\Schema();
		$schema->delimiter = '	';
		$schema->enclosure = '@';
		$schema->escape_char = '_';
		$schema->max_line_length = 1000;
		
		$csv = new Solution10\CSV\CSV('Solution10/CSV/tests/data/test.csv', $schema);
		
		$this->assertTrue($csv->schema() instanceof Solution10\CSV\Schema);
	}
	
	/**
	 * Testing validation against a schema
	 */
	public function testSchemaValidation()
	{
		$schema = new Solution10\CSV\Schema();
		$schema->add_field(0, 'firstname', array(
			function($value)
			{
				if(!is_string($value) || strlen($value) < 1)
				{
					throw new Solution10\CSV\Exception\Validation('Value not long enough');
				}
			}
		));
		
		$csv = new Solution10\CSV\CSV('Solution10/CSV/tests/data/test.csv', $schema);
		$this->assertEquals(count($csv), 4); // All rows should pass.
	}
	
	/**
	 * Testing validation where a schema doesn't validate.
	 */
	public function testSchemaValidationFail()
	{
		$schema = new Solution10\CSV\Schema();
		$schema->add_field(0, 'firstname', array(
			function($value)
			{
				if(!is_string($value) || strlen($value) < 1)
				{
					throw new Solution10\CSV\Exception\Validation('Value not long enough');
				}
			}
		));
		
		$schema->add_field(2, 'email', array(
			function($value)
			{
				if(filter_var($value, FILTER_VALIDATE_EMAIL) === false)
				{
					throw new Solution10\CSV\Exception\Validation('Not valid email');
				}
			}
		));
		
		$csv = new Solution10\CSV\CSV('Solution10/CSV/tests/data/bad.csv', $schema);
		$this->assertEquals(count($csv), 2); // Only two rows pass validation.
		$this->assertEquals($csv[0][0], 'Alex');
		$this->assertEquals($csv[1][0], 'Jane');
	}
}