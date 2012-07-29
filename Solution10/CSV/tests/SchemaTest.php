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
}