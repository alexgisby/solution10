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
	 * @param 	string|Event 	Event Name or event object itself
	 * @param 	array 			Callback parameters to pass to the event handler.
	 * @return 	this
	 */
	public function broadcast($event, array $params = array())
	{
		// Build the event object if required.
		if(is_string($event))
		{
			$event = new Event($event);
		}
		elseif(is_object($event) && !$event instanceof Event)
		{
			throw new Exception(get_class($event) . ' is not an instance of Event.');
		}
		elseif(!is_string($event) && !is_object($event))
		{
			throw new Exception('Invalid Event passed: ' . (string)$event);
		}

		// Pass the name of the event as first param:
		array_unshift($params, $event);

		// Loop through the handlers:
		if(array_key_exists($event->name(), $this->handlers))
		{
			foreach($this->handlers[$event->name()] as $handler)
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
		if(array_key_exists($event->name(), $this->finally))
		{
			foreach($this->finally[$event->name()] as $handler)
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
