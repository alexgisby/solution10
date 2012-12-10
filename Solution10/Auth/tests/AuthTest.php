<?php

use Solution10\Auth\Auth as Auth;
use Solution10\Auth\Tests\Mocks\SessionDelegate as SessionDelegateMock;
use Solution10\Auth\Tests\Mocks\StorageDelegate as StorageDelegateMock;

/**
 * Tests for the Auth class
 */
class AuthTest extends Solution10\Tests\TestCase
{
	protected $default_instance;
	protected $session_mock;
	protected $storage_mock;

	/**
	 * Instantiates a basic instance:
	 */
	public function setUp()
	{
		$this->session_mock = new SessionDelegateMock();
		$this->storage_mock = new StorageDelegateMock();
		$this->default_instance = new Auth('default', 
										$this->session_mock, 
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
		$session = new SessionDelegateMock();
		$storage = new StorageDelegateMock();
		$auth = new Auth('default', $session, $storage, array(
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
		$auth = new Auth('default', $this->session_mock, $this->storage_mock, array());
	}

	/**
	 * Testing fetching the name of an instance
	 */
	public function testName()
	{
		$this->assertEquals('default', $this->default_instance->name());
	}

	/**
	 * Test password hashing
	 */
	public function testHashing()
	{
		$pass = 'fgjkdfhgdf77989';
		$hashed = $this->default_instance->hash_password($pass);
		$this->assertEquals(60, strlen($hashed));
		$this->assertEquals(0, strpos($hashed, '$2a'));
	}

	/**
	 * Test password checking
	 */
	public function testPasswordCheck()
	{
		$pass = 'fjdggy744;0';
		$hashed = $this->default_instance->hash_password($pass);
		$this->assertTrue($this->default_instance->check_password($pass, $hashed));
	}

	/**
	 * Tests logging a user in successfully
	 */
	public function testSuccessfulLogin()
	{
		$this->assertTrue($this->default_instance->login('Alex', 'Alex'));
	}

	/**
	 * Test unsuccessful login
	 */
	public function testUnsuccessfulLogin()
	{
		$this->assertFalse($this->default_instance->login('Alex', 'wrong-password'));
	}

	/**
	 * Test login with a bad username
	 */
	public function testLoginBadUsername()
	{
		$this->assertFalse($this->default_instance->login('Jenny', 'password'));
	}

	/**
	 * Testing logged_in()
	 */
	public function testLoggedIn()
	{
		// Create a clean auth instance:
		$auth = new Auth('default', $this->session_mock, $this->storage_mock, array(
				'phpass_cost' => 8,
			));

		$this->assertFalse($auth->logged_in());

		$auth->login('Alex', 'Alex');
		$this->assertTrue($auth->logged_in());
	}

	/**
	 * Testing logout()
	 */
	public function testLogout()
	{
		// Create a clean auth instance:
		$auth = new Auth('default', $this->session_mock, $this->storage_mock, array(
				'phpass_cost' => 8,
			));

		$this->assertFalse($auth->logged_in());

		$auth->login('Alex', 'Alex');
		$this->assertTrue($auth->logged_in());

		$auth->logout();
		$this->assertFalse($auth->logged_in());
	}

	/**
	 * Testing fetching the user
	 */
	public function testUser()
	{
		$auth = new Auth('default', $this->session_mock, $this->storage_mock, array(
				'phpass_cost' => 8,
			));

		$auth->login('Alex', 'Alex');
		$this->assertEquals($this->storage_mock->users[1], $auth->user());
	}

	/**
	 * Testing fetching a user when not logged in
	 */
	public function testUserNotLoggedIn()
	{
		$this->default_instance->logout();
		$this->assertFalse($this->default_instance->user());
	}

	/**
	 * Testing when the storage can't find a user who claims to be logged in.
	 */
	public function testUserGone()
	{
		$storage_mock = new StorageDelegateMock();
		$auth = new Auth('default', $this->session_mock, $storage_mock, array(
				'phpass_cost' => 8,
			));

		$auth->login('Alex', 'Alex');

		// Everything should be fine at the moment:
		$this->assertTrue($auth->logged_in());

		// Unset the user from storage, so when we call user(), they're gone.
		unset($storage_mock->users[1]);

		$this->assertFalse($auth->user());
		$this->assertFalse($auth->logged_in());
	}

}