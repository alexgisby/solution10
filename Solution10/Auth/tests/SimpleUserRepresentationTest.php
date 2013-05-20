<?php

use Solution10\Auth\Driver\SimpleUserRepresentation as SimpleUserRepresentation;

/**
 * Tests for the SimpleUserRepresentation
 */
class SimpleUserRepresentationTest extends Solution10\Tests\TestCase
{
	public function testConstruct()
	{
		$rep = new SimpleUserRepresentation(array(
			'id' => 27,
			'username' => 'Alex',
		));

		$this->assertTrue($rep instanceof SimpleUserRepresentation);
	}

	/**
	 * Tests that we throw when not passed an ID
	 *
	 * @expectedException 	Exception
	 */
	public function testBadConstruct()
	{
		$rep = new SimpleUserRepresentation();
	}

	/**
	 * Tests that we return the correct ID from the representation
	 */
	public function testFetchId()
	{
		$rep = new SimpleUserRepresentation(array(
			'id' => 27,
			'username' => 'Alex'
		));

		$this->assertEquals(27, $rep->id());
	}

	/**
	 * Tests fetching a known property of the object (testing __get)
	 */
	public function testFetchProperty()
	{
		$rep = new SimpleUserRepresentation(array(
			'id' => 27,
			'username' => 'Alex'
		));

		$this->assertEquals(27, $rep->id);
		$this->assertEquals('Alex', $rep->username);
	}

	/**
	 * Tests Receiving NULL on a bad property name
	 */
	public function testFetchBadProperty()
	{
		$rep = new SimpleUserRepresentation(array(
			'id' => 27,
			'username' => 'Alex'
		));

		$this->assertNull($rep->password);
	}

	/**
	 * Testing isset() on the object
	 */
	public function testIsset()
	{
		$rep = new SimpleUserRepresentation(array(
			'id' => 27,
			'username' => 'Alex'
		));

		$this->assertTrue(isset($rep->id));
		$this->assertFalse(isset($rep->password));
	}
}