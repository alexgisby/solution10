<?php

use Solution10\Auth\Auth as Auth;
use Solution10\Auth\Tests\Mocks\SessionDelegate as SessionDelegateMock;
use Solution10\Auth\Tests\Mocks\StorageDelegate as StorageDelegateMock;
use Solution10\Auth\Tests\Mocks\Package as PackageMock;
use Solution10\Auth\Tests\Mocks\UserRepresentation as UserRepMock;

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
	 * Testing the force_login() process with an ID
	 */
	public function testForceLoginInt()
	{
		$auth = new Auth('default', $this->session_mock, $this->storage_mock, array(
				'phpass_cost' => 8,
			));

		$this->assertFalse($auth->logged_in());
		$this->assertTrue($auth->force_login(1));
		$this->assertTrue($auth->logged_in());
		$this->assertEquals(new UserRepMock($this->storage_mock->users[1]), $auth->user());
	}

	/**
	 * Testing the force_login() process with a user rep
	 */
	public function testForceLoginUserRep()
	{
		$auth = new Auth('default', $this->session_mock, $this->storage_mock, array(
				'phpass_cost' => 8,
			));

		$user_rep = new UserRepMock($this->storage_mock->users[1]);

		$this->assertFalse($auth->logged_in());
		$this->assertTrue($auth->force_login($user_rep));
		$this->assertTrue($auth->logged_in());
		$this->assertEquals($user_rep, $auth->user());
	}

	/**
	 * Testing force login with an unknown user
	 */
	public function testForceLoginUnknownUser()
	{
		$auth = new Auth('default', $this->session_mock, $this->storage_mock, array(
				'phpass_cost' => 8,
			));

		$this->assertFalse($auth->logged_in());
		$this->assertFalse($auth->force_login(10));
		$this->assertFalse($auth->logged_in());
	}

	/**
	 * Testing force login with a bad user rep
	 */
	public function testForceLoginBadUserRep()
	{
		$auth = new Auth('default', $this->session_mock, $this->storage_mock, array(
				'phpass_cost' => 8,
			));

		$user = (object)array(
			'id' => 'not an instance of UserRepresentation',
		);

		$this->assertFalse($auth->logged_in());
		$this->assertFalse($auth->force_login($user));
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
		$user = $auth->user();
		$this->assertTrue($user instanceof \Solution10\Auth\UserRepresentation);
		$this->assertEquals(new UserRepMock($this->storage_mock->users[1]), $user);
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


	/**
	 * Testing adding a package to a user successfully
	 */
	public function testAddPackageInstanceSuccessful()
	{
		$storage_mock = new StorageDelegateMock();
		$auth = new Auth('default', $this->session_mock, $storage_mock, array(
				'phpass_cost' => 8,
			));

		$package = new PackageMock();
		$auth->add_package_to_user(1, $package);
		$this->assertEquals($package, $storage_mock->users[1]['packages'][0]);
	}

	/**
	 * Testing adding a package by string name
	 */
	public function testAddPackageStringSuccessful()
	{
		$storage_mock = new StorageDelegateMock();
		$auth = new Auth('default', $this->session_mock, $storage_mock, array(
				'phpass_cost' => 8,
			));

		$package = 'Solution10\Auth\Tests\Mocks\Package';
		$auth->add_package_to_user(1, $package);
		$this->assertEquals($package, get_class($storage_mock->users[1]['packages'][0]));
	}

	/**
	 * Testing adding a package to a user that doesn't exist
	 *
	 * @expectedException 		Solution10\Auth\Exception\Package
	 * @expectedExceptionCode 	0
	 */
	public function testAddPackageNoUser()
	{
		$storage_mock = new StorageDelegateMock();
		$auth = new Auth('default', $this->session_mock, $storage_mock, array(
				'phpass_cost' => 8,
			));

		$package = 'Solution10\Auth\Tests\Mocks\Package';
		$auth->add_package_to_user(10, $package);
	}

	/**
	 * Testing adding a package that doesn't exist
	 *
	 * @expectedException 		Solution10\Auth\Exception\Package
	 * @expectedExceptionCode 	1
	 */
	public function testAddPackageNotFound()
	{
		$storage_mock = new StorageDelegateMock();
		$auth = new Auth('default', $this->session_mock, $storage_mock, array(
				'phpass_cost' => 8,
			));

		$package = 'Solution10\Auth\Tests\Mocks\PackageNotExist';
		$auth->add_package_to_user(1, $package);
	}

	/**
	 * Testing adding a package that's not got Package as a parent.
	 *
	 * @expectedException 		Solution10\Auth\Exception\Package
	 * @expectedExceptionCode 	2
	 */
	public function testAddPackageBadLineage()
	{
		$storage_mock = new StorageDelegateMock();
		$auth = new Auth('default', $this->session_mock, $storage_mock, array(
				'phpass_cost' => 8,
			));

		$package = 'Solution10\Auth\Tests\Mocks\StorageDelegate';
		$auth->add_package_to_user(1, $package);
	}


	/**
	 * Tests removing a package successfully
	 */
	public function testRemovePackageInstanceSuccess()
	{
		$storage_mock = new StorageDelegateMock();
		$auth = new Auth('default', $this->session_mock, $storage_mock, array(
				'phpass_cost' => 8,
			));

		$package = new \Solution10\Auth\Tests\Mocks\Package();
		$auth->add_package_to_user(1, $package);

		// Now remove:
		$auth->remove_package_from_user(1, $package);
		$this->assertEquals(0, count($storage_mock->users[1]['packages']));
	}

	/**
	 * Tests removing a package string successfully
	 */
	public function testRemovePackageStringSuccess()
	{
		$storage_mock = new StorageDelegateMock();
		$auth = new Auth('default', $this->session_mock, $storage_mock, array(
				'phpass_cost' => 8,
			));

		$package = '\Solution10\Auth\Tests\Mocks\Package';
		$auth->add_package_to_user(1, $package);

		// Now remove:
		$auth->remove_package_from_user(1, $package);
		$this->assertEquals(0, count($storage_mock->users[1]['packages']));
	}

	/**
	 * Testing removing a package from a user that doesn't exist
	 *
	 * @expectedException 		Solution10\Auth\Exception\Package
	 * @expectedExceptionCode 	0
	 */
	public function testRemovePackageNoUser()
	{
		$storage_mock = new StorageDelegateMock();
		$auth = new Auth('default', $this->session_mock, $storage_mock, array(
				'phpass_cost' => 8,
			));

		$package = 'Solution10\Auth\Tests\Mocks\Package';
		$auth->remove_package_from_user(10, $package);
	}

	/**
	 * Tests removing a package that doesn't exist
	 * Should all be silent.
	 */
	public function testRemovePackageNotFound()
	{
		$storage_mock = new StorageDelegateMock();
		$auth = new Auth('default', $this->session_mock, $storage_mock, array(
				'phpass_cost' => 8,
			));

		$package = 'Solution10\Auth\Tests\Mocks\PackageNotFound';
		$auth->remove_package_from_user(1, $package);

		$this->assertTrue(true);
	}

	/**
	 * Tests fetching packages
	 */
	public function testUserPackages()
	{
		$storage_mock = new StorageDelegateMock();
		$auth = new Auth('default', $this->session_mock, $storage_mock, array(
				'phpass_cost' => 8,
			));

		$package = '\Solution10\Auth\Tests\Mocks\Package';
		$auth->add_package_to_user(1, $package);

		$this->assertEquals($auth->packages_for_user(1), $storage_mock->users[1]['packages']);
	}

	/**
	 * Tests fetching when there's no packages
	 */
	public function testUserNoPackages()
	{
		$storage_mock = new StorageDelegateMock();
		$auth = new Auth('default', $this->session_mock, $storage_mock, array(
				'phpass_cost' => 8,
			));

		$this->assertEquals($auth->packages_for_user(1), array());
	}

	/**
	 * Tests fetching the packages with unknown user
	 * @expectedException 		Solution10\Auth\Exception\Package
	 * @expectedExceptionCode 	0
	 */
	public function testUserPackagesNoUser()
	{
		$storage_mock = new StorageDelegateMock();
		$auth = new Auth('default', $this->session_mock, $storage_mock, array(
				'phpass_cost' => 8,
			));

		$auth->packages_for_user(10);
	}


	/**
	 * Testing if a user has a package
	 */
	public function testUserHasPackageInstance()
	{
		$storage_mock = new StorageDelegateMock();
		$auth = new Auth('default', $this->session_mock, $storage_mock, array(
				'phpass_cost' => 8,
			));

		$package = new \Solution10\Auth\Tests\Mocks\Package();
		$auth->add_package_to_user(1, $package);

		$this->assertTrue($auth->user_has_package(1, $package));
	}

	/**
	 * Testing if a user has a package given by a string
	 */
	public function testUserHasPackageString()
	{
		$storage_mock = new StorageDelegateMock();
		$auth = new Auth('default', $this->session_mock, $storage_mock, array(
				'phpass_cost' => 8,
			));

		$package = '\Solution10\Auth\Tests\Mocks\Package';
		$auth->add_package_to_user(1, $package);

		$this->assertTrue($auth->user_has_package(1, $package));
	}

	/**
	 * Testing asking if a user has a package that don't exist
	 */
	public function testUserHasPackageNotFound()
	{
		$storage_mock = new StorageDelegateMock();
		$auth = new Auth('default', $this->session_mock, $storage_mock, array(
				'phpass_cost' => 8,
			));

		$package = '\Solution10\Auth\Tests\Mocks\PackageNotFound';
		$this->assertFalse($auth->user_has_package(1, $package));
	}

	/**
	 * Testing when asking if a user has a packag they haven't been assigned
	 */
	public function testUserHasPackageNotAssigned()
	{
		$storage_mock = new StorageDelegateMock();
		$auth = new Auth('default', $this->session_mock, $storage_mock, array(
				'phpass_cost' => 8,
			));

		$package = '\Solution10\Auth\Tests\Mocks\Package';
		$this->assertFalse($auth->user_has_package(1, $package));
	}

	/**
	 * Test user has package with unknown user
	 *
	 * @expectedException 		Solution10\Auth\Exception\Package
	 * @expectedExceptionCode 	0
	 */
	public function testUserHasPackageNoUser()
	{
		$storage_mock = new StorageDelegateMock();
		$auth = new Auth('default', $this->session_mock, $storage_mock, array(
				'phpass_cost' => 8,
			));

		$auth->user_has_package(10, 'Doesnt matter');
	}

	/**
	 * Data setup for can() tests
	 */
	protected function can_instance()
	{
		$storage_mock = new StorageDelegateMock();
		$auth = new Auth('default', $this->session_mock, $storage_mock, array(
				'phpass_cost' => 8,
			));

		$auth->add_package_to_user(1, '\Solution10\Auth\Tests\Mocks\Package');
		return $auth;
	}


	/**
	 * Basic user_can() tests on an instance with a single package
	 */
	public function testCanBool()
	{
		$auth = $this->can_instance();
		$this->assertFalse($auth->user_can(1, 'login'));
	}

	public function testCanClosure()
	{
		$auth = $this->can_instance();
		$this->assertFalse($auth->user_can(1, 'closure', array('arg1', 'arg2')));
	}

	public function testCanInstance()
	{
		$auth = $this->can_instance();
		$this->assertFalse($auth->user_can(1, 'edit_post'));
	}

	public function testCanStaticString()
	{
		$auth = $this->can_instance();
		$this->assertFalse($auth->user_can(1, 'static_string'));
	}

	public function testCanStaticArray()
	{
		$auth = $this->can_instance();
		$this->assertFalse($auth->user_can(1, 'static_array'));
	}

	public function testCanClosureArgs()
	{
		$auth = $this->can_instance();
		$this->assertEquals('arg1arg2', $auth->user_can(1, 'closure_with_args', array('arg1', 'arg2')));
	}

	public function testCanUnknownPermission()
	{
		$auth = $this->can_instance();
		$this->assertFalse($auth->user_can(1, 'unknown_perm'));
	}

	/**
	 * Tests user_can() when another package has overidden everything
	 */
	protected function can_higher_instance()
	{
		$auth = $this->can_instance();
		$auth->add_package_to_user(1, 'Solution10\Auth\Tests\Mocks\HigherPackage');
		return $auth;
	}

	public function testHigherCanBool()
	{
		$auth = $this->can_higher_instance();
		$this->assertTrue($auth->user_can(1, 'login'));
	}

	public function testHigherCanClosure()
	{
		$auth = $this->can_higher_instance();
		$this->assertTrue($auth->user_can(1, 'closure', array('arg1', 'arg2')));
	}

	public function testHigherCanInstance()
	{
		$auth = $this->can_higher_instance();
		$this->assertTrue($auth->user_can(1, 'edit_post'));
	}

	public function testHigherCanStaticString()
	{
		$auth = $this->can_higher_instance();
		$this->assertTrue($auth->user_can(1, 'static_string'));
	}

	public function testHigherCanStaticArray()
	{
		$auth = $this->can_higher_instance();
		$this->assertTrue($auth->user_can(1, 'static_array'));
	}

	public function testHigherCanClosureArgs()
	{
		$auth = $this->can_higher_instance();
		$this->assertEquals('arg2arg1', $auth->user_can(1, 'closure_with_args', array('arg1', 'arg2')));
	}

	public function testHigherCanUnknownPermission()
	{
		$auth = $this->can_higher_instance();
		$this->assertFalse($auth->user_can(1, 'unknown_perm'));
	}

	/**
	 * Tests a package that is only partially overloaded
	 */
	public function testPartiallyOverloadedPackage()
	{
		$auth = $this->can_instance();
		$auth->add_package_to_user(1, 'Solution10\Auth\Tests\Mocks\PartialPackage');

		$this->assertTrue($auth->user_can(1, 'login'));
		$this->assertTrue($auth->user_can(1, 'closure'));
		$this->assertTrue($auth->user_can(1, 'edit_post'));
		$this->assertFalse($auth->user_can(1, 'static_string'));
		$this->assertFalse($auth->user_can(1, 'static_array'));
		$this->assertEquals('arg1arg2', $auth->user_can(1, 'closure_with_args', array('arg1', 'arg2')));
		$this->assertFalse($auth->user_can(1, 'unknown_perm'));
	}

	/**
	 * Tests rebuilding permissions when adding / removing packages
	 */
	public function testRebuildingPermissions()
	{
		$auth = $this->can_instance();
		$this->assertFalse($auth->user_can(1, 'login'));

		$auth->add_package_to_user(1, 'Solution10\Auth\Tests\Mocks\HigherPackage');
		$this->assertTrue($auth->user_can(1, 'login'));

		$auth->remove_package_from_user(1, 'Solution10\Auth\Tests\Mocks\HigherPackage');
		$this->assertFalse($auth->user_can(1, 'login'));
	}

	/**
	 * Tests can() on a user who is currently logged in
	 */
	public function testCan()
	{
		$auth = $this->can_instance();
		$auth->add_package_to_user(1, 'Solution10\Auth\Tests\Mocks\PartialPackage');

		$auth->force_login(1);

		// Reusing the partial package tests as they cover everything
		$this->assertTrue($auth->can('login'));
		$this->assertTrue($auth->can('closure'));
		$this->assertTrue($auth->can('edit_post'));
		$this->assertFalse($auth->can('static_string'));
		$this->assertFalse($auth->can('static_array'));
		$this->assertEquals('arg1arg2', $auth->can('closure_with_args', array('arg1', 'arg2')));
		$this->assertFalse($auth->can('unknown_perm'));
	}

	/**
	 * Testing can() when a user isn't logged in
	 */
	public function testCanNotLoggedIn()
	{
		$auth = $this->can_instance();
		$auth->add_package_to_user(1, 'Solution10\Auth\Tests\Mocks\PartialPackage');

		// Reusing the partial package tests as they cover everything
		$this->assertFalse($auth->can('login'));
		$this->assertFalse($auth->can('closure'));
		$this->assertFalse($auth->can('edit_post'));
		$this->assertFalse($auth->can('static_string'));
		$this->assertFalse($auth->can('static_array'));
		$this->assertFalse($auth->can('closure_with_args', array('arg1', 'arg2')));
		$this->assertFalse($auth->can('unknown_perm'));
	}

	/**
	 * Testing adding an override
	 */
	public function testOverrideBasic()
	{
		$auth = $this->can_instance();
		$this->assertFalse($auth->user_can(1, 'login'));
		$auth->override_permission_for_user(1, 'login', true);
		$this->assertTrue($auth->user_can(1, 'login'));
	}

	/**
	 * Testing resetting the packages on a user after making changes
	 */
	public function testResetUserPackages()
	{
		$auth = $this->can_instance();
		$this->assertFalse($auth->user_can(1, 'login'));
		$auth->override_permission_for_user(1, 'login', true);
		$this->assertTrue($auth->user_can(1, 'login'));

		// Now reset and check it works:
		$auth->reset_overrides_for_user(1);
		$this->assertFalse($auth->user_can(1, 'login'));
	}

}