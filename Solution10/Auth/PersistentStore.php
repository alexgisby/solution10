<?php

namespace Solution10\Auth;

/**
 * Your PesistentStore class (probably a session class)
 * needs to implement this interface so Auth can read and write to it.
 * Up to you to write this!
 *
 * @package 	Solution10
 * @category  	Auth
 * @author 		Alex Gisby <alex@solution10.com>
 * @license 	MIT
 */
interface PersistentStore
{
	/**
	 * Reads the authentication data out of the session for a given named instance.
	 *
	 * @param 	string 	Instance name
	 * @return 	string 	Auth data string from the cookie / session etc
	 */
	public function auth_read($instance_name);

	/**
	 * Writes the authentication data into the session.
	 * 
	 * @param  string $instance_name Name of the Auth instance to write.
	 * @param  string $auth_data     Encrypted data to write to the store.
	 * @return bool 	True for success, false for failure.
	 */
	public function auth_write($instance_name, $auth_data);

	/**
	 * Called when a user successfully logs in
	 *
	 * @param  mixed $user_id The ID of the user who just signed in
	 * @return void
	 */
	public function auth_user_logged_in($user_id);

}