<?php

use Solution10\Auth\Auth as Auth;
use Solution10\Auth\Tests\PersistentStoreMock as PersistentStoreMock;
use Solution10\Auth\Tests\StorageDelegateMock as StorageDelegateMock;

/**
 * Tests for the Auth class
 */
class AuthTest extends Solution10\Tests\TestCase
{
	protected $default_instance;
	protected $persistent_mock;
	protected $storage_mock;

	/**
	 * Instantiates a basic instance:
	 */
	public function setUp()
	{
		$this->persistent_mock = new PersistentStoreMock();
		$this->storage_mock = new StorageDelegateMock();
		$this->default_instance = new Auth('default', 
										$this->persistent_mock, 
										$this->storage_mock, 
										array(
											'phpass_cost' => 8,
										)
								);
	}

	/**
	 * Test construction
	 */
	public function testConstructor()
	{
		$store = new PersistentStoreMock();
		$storage = new StorageDelegateMock();
		$auth = new Auth('default', $store, $storage, array(
			'phpass_cost' => 8,
		));
		$this->assertTrue($auth instanceof Auth);
	}

	/**
	 * Test error with phpass cost not specified
	 *
	 * @expectedException 		Solution10\Auth\Exception\Phpass
	 * @expectedExceptionCode 	0
	 */
	public function testNoPhpassCost()
	{
		$auth = new Auth('default', $this->persistent_mock, $this->storage_mock, array());
	}

	/**
	 * Testing fetching the name of an instance
	 */
	public function testName()
	{
		$this->assertEquals('default', $this->default_instance->name());
	}

}