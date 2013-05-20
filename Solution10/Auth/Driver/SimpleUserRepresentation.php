<?php

namespace Solution10\Auth\Driver;
use Solution10\Auth\UserRepresentation as UserRepresentation;

/**
 * SimpleUserRepresentation
 *
 * Very basic UserRepresentation that can be coupled with
 * the PDO component to provide simple auth.
 *
 * @package 	Solution10
 * @category 	Auth\Driver
 * @author 		Alex Gisby <alex@solution10.com>
 * @license 	MIT
 */
class SimpleUserRepresentation implements UserRepresentation
{
	/**
	 * @var 	array 	holds the data
	 */
	protected $data = array();

	/**
	 * Constructor. Pass in the row array to build up the user
	 *
	 * @param 	array 	Data for user
	 */
	public function __construct(array $data)
	{
		if(!array_key_exists('id', $data)) {
			throw new \Exception('Data passed to SimpleUserRepresentation must contain an ID');
		}
		
		$this->data = $data;
	}

	/**
	 * Returns the ID of this user.
	 *
	 * @return 	mixed
	 */
	public function id()
	{
		return $this->data['id'];
	}

	/**
	 * Magic get allows us to read properties of the row
	 *
	 * @param 	string 	Property name
	 * @return 	mixed
	 */
	public function __get($name)
	{
		if(array_key_exists($name, $this->data)) {
			return $this->data[$name];
		}

		return null;
	}

	/**
	 * Implement isset() magic method
	 *
	 * @param 	string 	Name to check
	 * @return 	bool
	 */
	public function __isset($name)
	{
		return array_key_exists($name, $this->data);
	}

}