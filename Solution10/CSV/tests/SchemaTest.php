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
	
}