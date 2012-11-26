<?php

namespace Solution10\Auth;

/**
 * Storage Delegate Interface
 *
 * Put simply, this is the class that reads and writes from the database
 * on behalf of Auth. Means you could use MySQL, Mongo, flat files, whatever
 * you want. Agnosticism for the win!
 *
 * @package  	Solution10
 * @category  	Auth
 * @author 		Alex Gisby <alex@solution10.com>
 * @license   	MIT
 */
interface StorageDelegate
{
	/**
	 * Fetches a user by their unique identifier.
	 *
	 * @param  mixed 	ID value
	 * @return mixed 	Who knows?
	 */
	public function auth_fetch_user_by_id($id);

	/**
	 * Fetches a user by their username. This function should return either an
	 * array containing:
	 *  - id: the unique identifier for this user
	 * 	- username: the username we just looked up
	 * 	- password: the hashed version of the users password.
	 * If it's a success, or false if there's no user by that name
	 *
	 * @param  string $username Username to search for
	 * @return array|bool
	 */
	public function auth_fetch_user_by_username($username);

	/**
	 * Called when a user successfully logs in
	 *
	 * @param  mixed $user_id The ID of the user who just signed in
	 * @return void
	 */
	public function auth_user_logged_in($user_id);
}