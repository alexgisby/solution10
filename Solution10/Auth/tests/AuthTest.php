<?php

use Solution10\Auth\Auth as Auth;

/**
 * Tests for the Auth class
 */
class AuthTest extends Solution10\Tests\TestCase
{
	/**
	 * Test construction
	 */
	public function testConstructor()
	{
		$auth = new Auth('default', array());
		$this->assertTrue($auth instanceof Auth);
	}

	/**
	 * Testing fetching the name of an instance
	 */
	public function testName()
	{
		$auth = new Auth('default', array());
		$this->assertEquals('default', $auth->name());
	}
}