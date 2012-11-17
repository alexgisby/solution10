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
		$event = new Solution10\Events\Event('test.construct');
		$this->assertEquals('test.construct', $event->name());
	}
}