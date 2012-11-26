<?php

namespace Solution10\Auth\Tests;

/**
 * Persistent Store test Mock
 */
class PersistentStoreMock implements \Solution10\Auth\PersistentStore
{
	protected $storage = array();

	/**
	 * Reads the authentication data out of the session for a given named instance.
	 *
	 * @param 	string 			Instance name
	 * @return 	string|false 	Auth data string from the cookie / session etc
	 */
	public function auth_read($instance_name)
	{
		return (array_key_exists($instance_name, $this->storage))? $this->storage[$instance_name] : false;
	}

	/**
	 * Writes the authentication data into the session.
	 * 
	 * @param  string $instance_name Name of the Auth instance to write.
	 * @param  string $auth_data     Encrypted data to write to the store.
	 * @return bool 	True for success, false for failure.
	 */
	public function auth_write($instance_name, $auth_data)
	{
		$this->storage[$instance_name] = $auth_data;
		return true;
	}
}