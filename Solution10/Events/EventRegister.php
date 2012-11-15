<?php

namespace Solution10\Events;

/**
 * Event Register
 *
 * Use instances of this class to register event handlers for given events.
 *
 * @package 	Solution10
 * @category 	Events
 * @author 		Alex Gisby <alex@solution10.com>
 * @license 	MIT
 */
class EventRegister
{
	/**
	 * @var 	array 	Event handlers
	 */
	protected $handlers = array();

	/**
	 * Register a handler for an event.
	 *
	 * @param 	string 		Event Name
	 * @param 	callback  	Anything that can be considered a callback. String, array or lambda
	 * @return 	this 		Chainable.
	 */
	public function add_listener($event, $callback)
	{
		$this->handlers[$event][] = $callback;
		return $this;
	}

	
}