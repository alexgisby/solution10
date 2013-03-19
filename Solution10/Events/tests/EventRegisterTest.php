<?php

/**
 * This mock is used to test the events system. The callbacks
 * just change a public static variable which can then be verified.
 */
class InstanceMock
{
	public static $state;

	public function callback($event)
	{ 
		self::$state = $event['new_state'];
	}

	public static function static_callback($event)
	{
		self::$state = $event['new_state'];
	}
}

function functionMock($event)
{
	InstanceMock::$state = $event['new_state'];
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
			'new_state' => 'functionBroadcastState'
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
			'new_state' => 'memberBroadcastState'
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
			'new_state' => 'staticBroadcastState'
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
			'new_state' => 'staticStringBroadcastState'
		));

		$this->assertEquals('staticStringBroadcastState', InstanceMock::$state);
	}

	/**
	 * Testing anaonymous functions
	 */
	public function testAnonBroadcast()
	{
		$callback = function($event)
		{
			InstanceMock::$state = $event['new_state'];
		};

		$this->register->listen('test.anonbroadcast', $callback);

		$this->register->broadcast('test.anonbroadcast', array(
			'new_state' => 'anonBroadcastState'
		));

		$this->assertEquals('anonBroadcastState', InstanceMock::$state);
	}

	/**
	 * Testing multiple events on a broadcast
	 */
	public function testMultipleBroadcasts()
	{
		$callback1 = function($event)
		{
			InstanceMock::$state = $event['new_state'] . '1';
		};


		$callback2 = function($event)
		{
			InstanceMock::$state .= $event['new_state'] . '2';
		};


		$this->register->listen('test.multiplebroadcast', $callback1);
		$this->register->listen('test.multiplebroadcast', $callback2);

		$this->register->broadcast('test.multiplebroadcast', array(
			'new_state' => 'multipleBroadcast',
		));

		$this->assertEquals('multipleBroadcast1multipleBroadcast2', InstanceMock::$state);
	}

	/**
	 * Testing firing an event that doesn't have any callbacks
	 */
	public function testEventNoCallbacks()
	{
		$this->register->broadcast('test.noCallbacks');
		$this->assertTrue(true);
	}

	/**
	 * Tests that an event object is passed into objects:
	 */
	public function testEventObject()
	{
		$is_event 	= false;
		$event_name = '';

		$callback = function($event) use (&$is_event, &$event_name) {
			$is_event   = ($event instanceof Solution10\Events\Event);
			$event_name = $event->name();
		};

		$this->register->listen('test.eventObject', $callback);
		$this->register->broadcast('test.eventObject');

		$this->assertTrue($is_event);
		$this->assertEquals('test.eventObject', $event_name);
	}

	/**
	 * Test broadcasting a pre-created event.
	 */
	public function testBroadcastEventObject()
	{
		$this->register->listen('test.broadcastObject', function(){
			InstanceMock::$state = 'broadcastObjectValue';
		});

		$event = new Solution10\Events\Event('test.broadcastObject');
		$this->register->broadcast($event);
		$this->assertEquals('broadcastObjectValue', InstanceMock::$state);
	}

	/**
	 * Testing passing in a bad object to broadcast
	 *
	 * @expectedException 	\Solution10\Events\Exception
	 */
	public function testBroadcastBadObject()
	{
		$event = new stdClass();
		$this->register->broadcast($event);
	}

	/**
	 * Testing passing in a bad event string.
	 *
	 * @expectedException 	\Solution10\Events\Exception
	 */
	public function testBroadcastBadEventName()
	{
		$this->register->broadcast(12);
	}

	/**
	 * Testing stopping an event
	 */
	public function testStoppingEvent()
	{
		$callback1 = function($event)
		{
			InstanceMock::$state = '1';
			$event->stop();
		};


		$callback2 = function($event)
		{
			InstanceMock::$state = '2';
		};

		$this->register->listen('test.eventStop', $callback1);
		$this->register->listen('test.eventStop', $callback2);

		$this->register->broadcast('test.eventStop');

		$this->assertEquals('1', InstanceMock::$state);
	}

	/**
	 * Testing basic finally
	 */
	public function testFinally()
	{
		$finally = function($event)
		{
			InstanceMock::$state = 'finally';
		};

		$this->register->finally('test.finally', $finally);
		$this->register->broadcast('test.finally');

		$this->assertEquals('finally', InstanceMock::$state);
	}

	/**
	 * Testing that finally runs even when event is stopped
	 */
	public function testFinallyAfterStop()
	{
		$finally_fired = false;
		$c1_fired = false;
		$c2_fired = false;

		$finally = function($event) use (&$finally_fired)
		{
			$finally_fired = true;
		};

		$c1 = function($event) use (&$c1_fired)
		{
			$c1_fired = true;
			$event->stop();
		};

		$c2 = function($event) use (&$c2_fired)
		{
			$c2_fired = true;
		};

		$this->register->listen('test.finallyAfterStop', $c1);
		$this->register->listen('test.finallyAfterStop', $c2);
		$this->register->finally('test.finallyAfterStop', $finally);
		$this->register->broadcast('test.finallyAfterStop');

		$this->assertTrue($finally_fired);
		$this->assertTrue($c1_fired);
		$this->assertFalse($c2_fired);
	}


	/**
	 * Testing binding against all (*) events
	 */
	public function testBindAll()
	{
		$called = 0;
		$callback = function() use (&$called)
		{
			$called ++;
		};

		$this->register->listen('bindAll.*', $callback);

		// Broadcast two bindAll event and check that the counter is 2.
		$this->register->broadcast('bindAll.event1');
		$this->register->broadcast('bindAll.event2');
		$this->assertEquals(2, $called);
	}

	/**
	 * Advanced testing regex bindings
	 */
	public function testRegexCallbacks()
	{
		$called = 0;
		$callback = function() use(&$called)
		{
			$called ++;
		};

		$this->register->listen('app.user.*', $callback);
		$this->register->broadcast('app.user.register'); // 1
		$this->register->broadcast('app.user.login'); // 2
		$this->register->broadcast('app.user.login.error'); // 3
		$this->register->broadcast('app.error'); // Not fired

		$this->assertEquals(3, $called);
	} 

	/**
	 * Tests bind all on the global event namespace
	 */
	public function testBindAllEverything()
	{
		$called = 0;
		$callback = function() use (&$called)
		{
			$called ++;
		};

		$this->register->listen('(.*)', $callback);

		// Broadcast two bindAll event and check that the counter is 2.
		$this->register->broadcast('bindAll.event1');
		$this->register->broadcast('anotherNamespace.event');
		$this->assertEquals(2, $called);
	}

}