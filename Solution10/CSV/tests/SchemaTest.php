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
		$schema->add_field(2, array(
			'name' => 'customer_name',
			'validation' => array(
				function($value)
				{
					return true;
				}
			),
		));
		
		$fields = $schema->fields();
		$this->assertEquals(count($fields), 1);
		$this->assertTrue(array_key_exists(2, $fields));
		$this->assertTrue($fields[2]['name'] == 'customer_name');
		$this->assertTrue(is_array($fields[2]['validation']));
		$this->assertTrue($fields[2]['validation'][0] instanceof \Closure);
	}
	
	/**
	 * Running a callback or closure rule
	 */
	public function testValidatingClosure()
	{
		$schema = new Solution10\CSV\Schema();
		$schema->add_field(0, array(
			'name' => 'customer_name',
			'validation' => array(
				function($value)
				{
					// Pretend to do some validation...
					return true;
				}
			),
		));
		
		$data = array('Alex');
		$this->assertTrue($schema->validate_row($data));
	}
	
	/**
	 * Testing exception thrown on not providing a function as callback
	 *
	 * @expectedException 		Solution10\CSV\Exception\Validation
	 * @expectedExceptionCode 	1
	 */
	public function testUnknownMethod()
	{
		$schema = new Solution10\CSV\Schema();
		$schema->add_field(0, array(
			'name' => 'customer_name',
			'validation' => array(
				'bad_name',
			),
		));
		
		$data = array('Alex');
		$schema->validate_row($data);
	}
	
	/**
	 * Testing out of index callbacks
	 *
	 * @expectedException 		Solution10\Collection\Exception\Index
	 */
	public function testBadIndexValidation()
	{
		$schema = new Solution10\CSV\Schema();
		$schema->add_field(1, array(
			'name' => 'customer_name',
			'validation' => array(
				function($value)
				{
					return true;
				}
			),
		));
		
		$data = array('Alex');
		$schema->validate_row($data);
	}
	
}