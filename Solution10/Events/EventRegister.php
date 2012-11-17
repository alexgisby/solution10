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
	 * @var 	array 	Finally Event Handlers
	 */
	protected $finally = array();

	/**
	 * Register a handler for an event.
	 *
	 * @param 	string 		Event Name
	 * @param 	callback 	Anything that can be considered a callback. String, array or lambda.
	 * @return 	this 		Chainable.
	 */
	public function listen($event, $callback)
	{
		$this->handlers[$event][] = $callback;
		return $this;
	}

	/**
	 * Registers a handler that runs at the end of an event chain, and always
	 * runs, regardless of whether the event was stopped or not.
	 * Callbacks will be given the event called, and so can check for themselves
	 * if it was stopped or not and act accordingly.
	 *
	 * @param 	string 		Event name
	 * @param 	callback 	Anything that can be called
	 * @return 	this 		Chainable
	 */
	public function finally($event, $callback)
	{
		$this->finally[$event][] = $callback;
		return $this;
	}


	/**
	 * Broadcast that an event is occuring.
	 *
	 * @param 	string 	Event Name
	 * @param 	array 	Parameters to pass to callback functions.
	 * @return 	this
	 */
	public function broadcast($event_name, array $params = array())
	{
		// Create the event object:
		$event = new Event($event_name);

		// Pass the name of the event as first param:
		array_unshift($params, $event);

		// Loop through the handlers:
		if(array_key_exists($event_name, $this->handlers))
		{
			foreach($this->handlers[$event_name] as $handler)
			{
				if(is_callable($handler) && !$event->is_stopped())
				{
					call_user_func_array($handler, $params);
				}

				// Kill the loop if stopped:
				if($event->is_stopped())
				{
					break;
				}
			}
		}

		// Loop through the finally callbacks:
		if(array_key_exists($event_name, $this->finally))
		{
			foreach($this->finally[$event_name] as $handler)
			{
				if(is_callable($handler))
				{
					call_user_func_array($handler, $params);
				}
			}
		}

		return $this;
	}

}
