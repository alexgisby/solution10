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
}