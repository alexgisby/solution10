<?php

use Solution10\Auth\Auth as Auth;
use Solution10\Auth\Tests\PersistentStoreMock as PersistentStoreMock;

/**
 * Tests for the Auth class
 */
class AuthTest extends Solution10\Tests\TestCase
{
	protected $default_instance;
	protected $store_mock;

	/**
	 * Instantiates a basic instance:
	 */
	public function setUp()
	{
		$this->store_mock = new PersistentStoreMock();
		$this->default_instance = new Auth('default', $this->store_mock, array());
	}

	/**
	 * Test construction
	 */
	public function testConstructor()
	{
		$store = new PersistentStoreMock();
		$auth = new Auth('default', $store, array());
		$this->assertTrue($auth instanceof Auth);
	}

	/**
	 * Testing fetching the name of an instance
	 */
	public function testName()
	{
		$this->assertEquals('default', $this->default_instance->name());
	}
}