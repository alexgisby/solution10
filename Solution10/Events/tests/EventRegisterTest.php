<?php

/**
 * This mock is used to test the events system. The callbacks
 * just change a public static variable which can then be verified.
 */
class InstanceMock
{
	public static $state;

	public function callback($event, $new_state)
	{ 
		self::$state = $new_state;
	}

	public static function static_callback($event, $new_state)
	{
		self::$state = $new_state;
	}
}

/**
 * Tests for the Event Register
 */
class EventRegisterTest extends Solution10\Tests\TestCase
{
	protected $register;

	/**
	 * Set up the tests
	 */
	public function setUp()
	{
		$this->register = new Solution10\Events\EventRegister();
	}

	/**
	 * Tests construction
	 */
	public function testConstruction()
	{
		$this->assertTrue($this->register instanceof Solution10\Events\EventRegister);
	}

	/**
	 * Test adding a listener
	 */
	public function testRegisterEventChainable()
	{
		$this->assertTrue(
			$this->register->add_listener('test.register', 'foo') instanceof Solution10\Events\EventRegister
		);
	}

	/**
	 * Test adding a function callback.
	 */
	public function testFunctionCallback()
	{
		$callback = 'foo';

		$this->assertTrue(
			$this->register->add_listener('test.register', $callback) instanceof Solution10\Events\EventRegister
		);
	}

	/**
	 * Test adding a static call
	 */
	public function testStaticCallback()
	{
		$callback = array('InstanceMock', 'static_callback');

		$this->assertTrue(
			$this->register->add_listener('test.register', $callback) instanceof Solution10\Events\EventRegister
		);
	}

	/**
	 * Test adding an instance method
	 */
	public function testInstanceCallback()
	{
		$instance = new InstanceMock();

		$callback = array($instance, 'callback');

		$this->assertTrue(
			$this->register->add_listener('test.register', $callback) instanceof Solution10\Events\EventRegister
		);
	}

	/**
	 * Test adding a string static callback
	 */
	public function testStringStaticCallback()
	{
		$callback = 'InstanceMock::static_callback';

		$this->assertTrue(
			$this->register->add_listener('test.register', $callback) instanceof Solution10\Events\EventRegister
		);
	}

	/**
	 * Test adding an anonymous function
	 */
	public function testAnonCallback()
	{
		$callback = function()
		{ };

		$this->assertTrue(
			$this->register->add_listener('test.register', $callback) instanceof Solution10\Events\EventRegister
		);
	}

	/**
	 * ------------------ Testing Broadcasting Events ---------------
	 */

	/**
	 * Testing Basic Event Broadcast
	 */
	public function testMemberBroadcast()
	{
		$instance = $instance = new InstanceMock();
		$callback = array($instance, 'callback');
		$this->register->add_listener('test.memberbroadcast', $callback);

		$this->register->broadcast('test.memberbroadcast', array(
			'memberBroadcastState'
		));

		$this->assertEquals('memberBroadcastState', $instance::$state);
	}


}