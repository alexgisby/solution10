<?php

namespace Solution10\Auth\Driver;
use Solution10\Auth\StorageDelegate as StorageDelegate;

/**
 * PDO Driver for Auth.
 *
 * Provides a reference implementation of the Auth StorageDelegate using PHP's
 * PDO database abstraction layer. Can also be used in anger.
 *
 * @package 	Solution10
 * @category 	Auth\Driver
 * @author 		Alex Gisby <alex@solution10.com>
 * @license 	MIT
 */
class PDO implements StorageDelegate
{
	/**
	 * @var 	PDO 	Holds the instance of the database connection
	 */
	protected $db;

	/**
	 * @var 	string 	Database table prefix for the auth tables
	 */
	protected $table_prefix = '';

	/**
	 * @var 	PDOStatement 	Fetching User by Username Statement
	 */
	protected $stmt_fetch_user_username;

	/**
	 * @var 	PDOStatement 	Fetching User Rep Statement
	 */
	protected $stmt_fetch_user_rep;

	/**
	 * @var 	PDOStatement 	Add Package to user statement
	 */
	protected $stmt_add_package;

	/**
	 * @var 	PDOStatement 	Remove package from user statement
	 */
	protected $stmt_remove_package;

	/**
	 * @var 	PDOStatement 	Fetch packages for a user statement
	 */
	protected $stmt_fetch_user_packages;

	/**
	 * @var 	PDOStatement 	Whether a user has a package statement
	 */
	protected $stmt_has_package;

	/**
	 * @var 	PDOStatement 	Overriding permission statement
	 */
	protected $stmt_override_permission;

	/**
	 * @var 	PDOStatement 	Fetching User overrides
	 */
	protected $stmt_fetch_user_overrides;

	/**
	 * @var 	PDOStatement 	Reset User overrides
	 */
	protected $stmt_reset_user_overrides;

	/**
	 * Constructor. Pass in the PDO connection you wish to use.
	 *
	 * @param 	PDO
	 * @return 	this
	 */
	public function __construct(\PDO $db, $table_prefix = 'auth_')
	{
		$this->db = $db;
		$this->table_prefix = $table_prefix;

		//
		// We can build the statements now as we just re-use them.
		//

		$this->stmt_fetch_user_username = $this->db->prepare(
			'SELECT * 
				FROM :table 
				WHERE 
					instance_name = :instance_name
					AND username = :username 
				LIMIT 1'
		);

		$this->stmt_fetch_user_rep = $this->db->prepare(
			'SELECT * 
				FROM :table 
				WHERE 
					instance_name = :instance_name
					AND id = :user_id 
				LIMIT 1'
		);

		$this->stmt_add_package = $this->db->prepare(
			'INSERT INTO 
					:table 
				SET 
					package_name = :pkg, 
					user_id = :user_id,
					instance_name = :instance_name'
		);

		$this->stmt_remove_package = $this->db->prepare(
			'DELETE FROM 
					:table 
				WHERE 
					instance_name = :instance_name
					AND package_name = :pkg 
					AND user_id = :user_id 
				LIMIT 1'
		);

		$this->stmt_fetch_user_packages = $this->db->prepare(
			'SELECT * 
				FROM :table 
				WHERE 
					instance_name = :instance_name
					AND user_id = :user_id'
		);

		$this->stmt_has_package = $this->db->prepare(
			'SELECT COUNT(id) 
				FROM :table 
				WHERE 
					instance_name = :instance_name,
					package_name = :package_name, 
					user_id = :user_id 
				LIMIT 1'
		);

		$this->stmt_override_permission = $this->db->prepare(
			'INSERT INTO 
					:table 
				SET 
					overrides = :overrides 
				WHERE 
					user_id = :user_id
					AND instance_name = :instance_name'
		);

		$this->stmt_fetch_user_overrides = $this->db->prepare(
			'SELECT * 
				FROM :table 
				WHERE 
					user_id = :user_id
					AND instance_name = :instance_name'
		);

		$this->stmt_reset_user_overrides = $this->db->prepare(
			'DELETE FROM 
					:table 
				WHERE 
					user_id = :user_id ,
					instance_name = :instance_name
				LIMIT 1'
		);
	}

	/**
	 * ------------ Implement Storage Delegate ---------------
	 */

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
	public function auth_fetch_user_by_username($instance_name, $username)
	{
		$tbl_name = $this->table_prefix . 'users';
		
	}

	/**
	 * Fetches the full user representation of a given ID. ie your active record
	 * instance or the like.
	 *
	 * @param 	string 	Instance name
	 * @param 	int 	ID of the logged in user
	 * @return 	UserRepresentation 	The representation you pass back must conform to the UserRepresentation interface.
	 */
	public function auth_fetch_user_representation($instance_name, $user_id);

	/**
	 * Adding a package to a given user.
	 *
	 * @param 	string 				Auth instance name
	 * @param 	UserRepresentation  User representation (taken from auth_fetch_user_representation)
	 * @param 	Auth\Package 		Package to add.
	 * @return 	bool
	 */
	public function auth_add_package_to_user($instance_name, UserRepresentation $user, Package $package);

	/**
	 * Removing a package from a given user.
	 *
	 * @param 	string 				Auth instance name
	 * @param 	UserRepresentation  User representation (taken from auth_fetch_user_representation)
	 * @param 	Auth\Package 		Package to remove.
	 * @return 	bool
	 */
	public function auth_remove_package_from_user($instance_name, UserRepresentation $user, Package $package);

	/**
	 * Fetching all packages for a user
	 *
	 * @param 	string 		Auth instance name
	 * @param 	UserRepresentation  		User representation (taken from auth_fetch_user_representation)
	 * @return 	array
	 */
	public function auth_fetch_packages_for_user($instance_name, UserRepresentation $user);

	/**
	 * Returns whether a user has a given package or not.
	 *
	 * @param 	string 				Auth instance name
	 * @param 	UserRepresentation  User representation
	 * @param 	Auth\Package 		Package to check for
	 * @return 	bool
	 */
	public function auth_user_has_package($instance_name, UserRepresentation $user, Package $package);

	/**
	 * Stores an overrided permission for a user
	 *
	 * @param 	string 	Auth instance name
	 * @param 	UserRepresentation 
	 * @param 	string 	Permission
	 * @param 	bool 	New value
	 * @return 	bool
	 */
	public function auth_override_permission_for_user($instance_name, UserRepresentation $user, $permission, $new_value);

	/**
	 * Fetches all the permission overrides for a given user.
	 *
	 * @param 	string 	Auth instance name
	 * @param 	UserRepresentation
	 * @return 	array 	An array of permission => (bool) values
	 */
	public function auth_fetch_overrides_for_user($instance_name, UserRepresentation $user);

	/**
	 * Removes all the overrides for a given user.
	 *
	 * @param 	string	Auth instance name
	 * @param 	UserRepresentation
	 * @return 	bool
	 */
	public function auth_reset_overrides_for_user($instance_name, UserRepresentation $user);

	/**
	 * Called when a user successfully logs in
	 *
	 * @param  mixed $user_id The ID of the user who just signed in
	 * @return void
	 * @deprecated  ?? Maybe ignore in favour of events?
	 */
	public function auth_user_logged_in($user_id);
}