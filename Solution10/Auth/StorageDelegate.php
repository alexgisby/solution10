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
	 * Fetches the full user representation of a given ID. ie your active record
	 * instance or the like.
	 *
	 * @param int $user_id ID of the logged in user
	 * @return 	mixed 	Whatever you want! Auth won't try and read this, just pass it about.
	 */
	public function auth_fetch_user_representation($user_id);

	/**
	 * Adding a package to a given user.
	 *
	 * @param 	string 			Auth instance name
	 * @param 	mixed 			User representation (taken from auth_fetch_user_representation)
	 * @param 	Auth\Package 	Package to add.
	 * @return 	bool
	 */
	public function auth_add_package_to_user($instance_name, $user, Package $package);

	/**
	 * Called when a user successfully logs in
	 *
	 * @param  mixed $user_id The ID of the user who just signed in
	 * @return void
	 * @deprecated  ?? Maybe ignore in favour of events?
	 */
	public function auth_user_logged_in($user_id);
}