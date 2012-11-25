<?php

namespace Solution10\Auth;

/**
 * Authentication Library.
 *
 * @package 	Solution10
 * @category 	Auth
 * @author 		Alex Gisby <alex@solution10.com>
 * @license 	MIT
 * @uses   		phpass
 */
class Auth
{
	/**
	 * @var 	string 	Instance name
	 */
	protected $name;

	/**
	 * @var array Options for this instance.
	 */
	protected $options;

	/**
	 * Constructor. Pass in all the options for this instance, including all your
	 * hashing and salting stuff.
	 *
	 * @param string $name Name of this instance.
	 * @param array $options Options. Must contain, err, something.
	 * @return this
	 */
	public function __construct($name, array $options)
	{
		$this->name = $name;
		$this->options = $options;
	}

	/**
	 * Retrieving the name of the instance
	 *
	 * @return string 
	 */
	public function name()
	{
		return $this->name;
	}
}