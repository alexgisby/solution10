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

	/**
	 * Broadcast that an event is occuring.
	 *
	 * @param 	string 	Event Name
	 * @param 	array 	Parameters to pass to callback functions.
	 * @return 	this
	 */
	public function broadcast($event, array $params = array())
	{
		// Pass the name of the event as first param:
		array_unshift($params, $event);

		foreach($this->handlers[$event] as $handler)
		{
			if(is_callable($handler))
			{
				call_user_func_array($handler, $params);
			}
		}

		return $this;
	}

}