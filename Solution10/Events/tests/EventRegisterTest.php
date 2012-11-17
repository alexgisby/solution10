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

function functionMock($event, $new_state)
{
	InstanceMock::$state = $new_state;
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
			$this->register->listen('test.register', 'foo') instanceof Solution10\Events\EventRegister
		);
	}

	/**
	 * Test adding a function callback.
	 */
	public function testFunctionCallback()
	{
		$callback = 'foo';

		$this->assertTrue(
			$this->register->listen('test.register', $callback) instanceof Solution10\Events\EventRegister
		);
	}

	/**
	 * Test adding a static call
	 */
	public function testStaticCallback()
	{
		$callback = array('InstanceMock', 'static_callback');

		$this->assertTrue(
			$this->register->listen('test.register', $callback) instanceof Solution10\Events\EventRegister
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
			$this->register->listen('test.register', $callback) instanceof Solution10\Events\EventRegister
		);
	}

	/**
	 * Test adding a string static callback
	 */
	public function testStringStaticCallback()
	{
		$callback = 'InstanceMock::static_callback';

		$this->assertTrue(
			$this->register->listen('test.register', $callback) instanceof Solution10\Events\EventRegister
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
			$this->register->listen('test.register', $callback) instanceof Solution10\Events\EventRegister
		);
	}

	/**
	 * ------------------ Testing Broadcasting Events ---------------
	 */

	/**
	 * Test function callbacks on broadcast
	 */
	public function testFunctionBroadcast()
	{
		$callback = 'functionMock';
		$this->register->listen('test.functionBroacast', $callback);

		$this->register->broadcast('test.functionBroacast', array(
			'functionBroadcastState'
		));

		$this->assertEquals('functionBroadcastState', InstanceMock::$state);
	}

	/**
	 * Testing Basic Event Broadcast
	 */
	public function testMemberBroadcast()
	{
		$instance = $instance = new InstanceMock();
		$callback = array($instance, 'callback');
		$this->register->listen('test.memberbroadcast', $callback);

		$this->register->broadcast('test.memberbroadcast', array(
			'memberBroadcastState'
		));

		$this->assertEquals('memberBroadcastState', $instance::$state);
	}

	/**
	 * Test static broadcast
	 */
	public function testStaticBroadcast()
	{
		$callback = array('InstanceMock', 'static_callback');
		$this->register->listen('test.staticbroadcast', $callback);

		$this->register->broadcast('test.staticbroadcast', array(
			'staticBroadcastState'
		));

		$this->assertEquals('staticBroadcastState', InstanceMock::$state);
	}

	/**
	 * Test static string broadcast
	 */
	public function testStaticStringBroadcast()
	{
		$callback = 'InstanceMock::static_callback';
		$this->register->listen('test.staticstringbroadcast', $callback);

		$this->register->broadcast('test.staticstringbroadcast', array(
			'staticStringBroadcastState'
		));

		$this->assertEquals('staticStringBroadcastState', InstanceMock::$state);
	}

	/**
	 * Testing anaonymous functions
	 */
	public function testAnonBroadcast()
	{
		$callback = function($event, $new_state)
		{
			InstanceMock::$state = $new_state;
		};

		$this->register->listen('test.anonbroadcast', $callback);

		$this->register->broadcast('test.anonbroadcast', array(
			'anonBroadcastState'
		));

		$this->assertEquals('anonBroadcastState', InstanceMock::$state);
	}

	/**
	 * Testing multiple events on a broadcast
	 */
	public function testMultipleBroadcasts()
	{
		$callback1 = function($event, $new_state)
		{
			InstanceMock::$state = $new_state . '1';
		};


		$callback2 = function($event, $new_state)
		{
			InstanceMock::$state .= $new_state . '2';
		};


		$this->register->listen('test.multiplebroadcast', $callback1);
		$this->register->listen('test.multiplebroadcast', $callback2);

		$this->register->broadcast('test.multiplebroadcast', array(
			'multipleBroadcast',
		));

		$this->assertEquals('multipleBroadcast1multipleBroadcast2', InstanceMock::$state);
	}

}