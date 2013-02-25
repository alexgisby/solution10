<?php

namespace Solution10\Events;

/**
 * Event Class.
 *
 * Event objects are passed into the callbacks and hold meta data about the event.
 * You can also prevent future events firing.
 *
 * @category 	Events
 * @author 		Alex Gisby <alex@solution10.com>
 * @license 	MIT
 */
class Event implements \ArrayAccess
{
	/**
	 * @var 	string 	Event name that this represents
	 */
	protected $event_name;

	/**
	 * @var 	bool 	Is the event stopped or not
	 */
	protected $is_stopped = false;

	/**
	 * @var 	array 	Extra parameters for the event to hold.
	 */
	protected $params = array();

	/**
	 * Constructor.
	 *
	 * @param 	string 	Event name
	 * @return 	this
	 */
	public function __construct($event_name)
	{
		$this->event_name = $event_name;
	}

	/**
	 * Fetches the event name
	 *
	 * @return 	string
	 */
	public function name()
	{
		return $this->event_name;
	}

	/**
	 * Stop an event. This will prevent any other callbacks from firing.
	 */
	public function stop()
	{
		$this->is_stopped = true;
	}

	/**
	 * Return if the event is stopped or not.
	 *
	 * @return 	bool
	 */
	public function is_stopped()
	{
		return $this->is_stopped;
	}

	/*
	 * Array Access Implementation:
	 *
	 * Array access allows you to pass around arbitrary data in the event.
	 */

	public function offsetExists($offset)
	{ 
		return array_key_exists($offset, $this->params); 
	}

	public function offsetGet($offset)
	{
		return (array_key_exists($offset, $this->params))? $this->params[$offset] : null;
	}

	public function offsetSet($offset, $value)
	{
		$this->params[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		unset($this->params[$offset]);
	}

}
