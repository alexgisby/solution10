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
	 * Fetches a user by their username. This function should return either an
	 * array containing:
	 *  - id: the unique identifier for this user
	 * 	- username: the username we just looked up
	 * 	- password: the hashed version of the users password.
	 * If it's a success, or false if there's no user by that name
	 *
	 * @param 	string 	Instance name
	 * @param  	string 	Username to search for
	 * @return 	array|bool
	 */
	public function auth_fetch_user_by_username($instance_name, $username);

	/**
	 * Fetches the full user representation of a given ID. ie your active record
	 * instance or the like.
	 *
	 * @param 	string 	Instance name
	 * @param 	int 	ID of the logged in user
	 * @return 	mixed 	Whatever you want! Auth won't try and read this, just pass it about.
	 */
	public function auth_fetch_user_representation($instance_name, $user_id);

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
	 * Removing a package from a given user.
	 *
	 * @param 	string 			Auth instance name
	 * @param 	mixed 			User representation (taken from auth_fetch_user_representation)
	 * @param 	Auth\Package 	Package to remove.
	 * @return 	bool
	 */
	public function auth_remove_package_from_user($instance_name, $user, Package $package);

	/**
	 * Fetching all packages for a user
	 *
	 * @param 	string 		Auth instance name
	 * @param 	mixed 		User representation (taken from auth_fetch_user_representation)
	 * @return 	array
	 */
	public function auth_fetch_packages_for_user($instance_name, $user);

	/**
	 * Returns whether a user has a given package or not.
	 *
	 * @param 	string 	Auth instance name
	 * @param 	mixed 	User representation
	 * @param 	Auth\Package 	Package to check for
	 * @return 	bool
	 */
	public function auth_user_has_package($instance_name, $user, Package $package);

	/**
	 * Called when a user successfully logs in
	 *
	 * @param  mixed $user_id The ID of the user who just signed in
	 * @return void
	 * @deprecated  ?? Maybe ignore in favour of events?
	 */
	public function auth_user_logged_in($user_id);
}