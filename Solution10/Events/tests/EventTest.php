<?php

class EventTest extends Solution10\Tests\TestCase
{
	/**
	 * Testing construction
	 */
	public function testConstruct()
	{
		$event = new Solution10\Events\Event('test.construct');
		$this->assertTrue($event instanceof Solution10\Events\Event);
	}

	/**
	 * Testing event name
	 */
	public function testEventName()
	{
		$event = new Solution10\Events\Event('test.eventName');
		$this->assertEquals('test.eventName', $event->name());
	}
	
	/**
	 * Testing stopping an event
	 */
	public function testEventStop()
	{
		$event = new Solution10\Events\Event('test.eventStop');

		$this->assertFalse($event->is_stopped());
		$event->stop();
		$this->assertTrue($event->is_stopped());
	}

	/**
	 * Testing adding and retrieving event data
	 */
	public function testEventData()
	{
		$event = new Solution10\Events\Event('test.eventData');

		$this->assertFalse(isset($event['param1']));
		$event['param1'] = 'Hello World!';
		$this->assertTrue(isset($event['param1']));
		$this->assertEquals('Hello World!', $event['param1']);
		unset($event['param1']);
		$this->assertFalse(isset($event['param1']));
	}

}