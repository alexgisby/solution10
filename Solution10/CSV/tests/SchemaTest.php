<?php

/**
 * Tests for the big daddy CSV class
 */
class SchemaTest extends Solution10\Tests\TestCase
{
	/**
	 * Constructor testing.
	 */
	public function testConstructor()
	{
		$schema = new Solution10\CSV\Schema();
		$this->assertTrue($schema instanceof Solution10\CSV\Schema);
	}
	
	
	/**
	 * Adding fields to the schema.
	 */
	public function testAddingFields()
	{
		$schema = new Solution10\CSV\Schema();
		$schema->add_field(2, 'customer_name', array('not_empty'));
		
		$fields = $schema->fields();
		$this->assertEquals(count($fields), 1);
		$this->assertTrue(array_key_exists(2, $fields));
		$this->assertTrue($fields[2]['name'] == 'customer_name');
		$this->assertTrue(is_array($fields[2]['rules']));
		$this->assertEquals($fields[2]['rules'][0], 'not_empty');
	}
	
	
	/**
	 * Validating rows against the schema
	 */
	public function testValidatingBasic()
	{
		$schema = new Solution10\CSV\Schema();
		$schema->add_field(0, 'customer_name', array('not_empty'));
		
		$good_data = array('Alex');
		$schema->validate_row($good_data);
	}
	
	/**
	 * Assigning an unknown rule.
	 *
	 * @expectedException 		Solution10\CSV\Exception\Validation
	 * @expectedExceptionCode 	1
	 */
	public function testValidatingUnknownMethod()
	{
		$schema = new Solution10\CSV\Schema();
		$schema->add_field(0, 'customer_name', array('unknown_method'));
		
		$data = array('Alex');
		$schema->validate_row($data);
	}
	
	/**
	 * Running a callback or closure rule
	 */
	public function testValidatingClosure()
	{
		$schema = new Solution10\CSV\Schema();
		$schema->add_field(0, 'customer_name', array(
			function($value)
			{
				return true;
			}
		));
		
		$data = array('Alex');
		$schema->validate_row($data);
	}
	
}