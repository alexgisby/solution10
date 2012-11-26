<?php

namespace Solution10\Auth;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor/phpass/PasswordHash.php';

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
	 * @var PersistentStore Instance of the PersistentStore interface (Session class basically)
	 */
	protected $persistent_store;

	/**
	 * @var 	StorageDelegate 	Storage Delegate implementation. DB access basically.
	 */
	protected $storage;

	/**
	 * @var array Options for this instance.
	 */
	protected $options;

	/**
	 * @var PasswordHash The Password hasher instance
	 */
	protected $hasher;

	/**
	 * Constructor. Pass in all the options for this instance, including all your
	 * hashing and salting stuff.
	 *
	 * @param 	string 			$name 				Name of this instance.
	 * @param   PersistentStore $persistent_store 	The PersistentStore implementation for storing Session type data
	 * @param   StorageDelegate $storage 			The StorageDelegate implementation for data access.
	 * @param 	array 			$options 			Options. Must contain, err, something.
	 * @return 	this
	 */
	public function __construct($name, PersistentStore $persistent_store, StorageDelegate $storage, array $options)
	{
		$this->name = $name;
		$this->persistent_store = $persistent_store;
		$this->storage = $storage;
		$this->options = $options;

		// Build up the phpass instance:
		if(!array_key_exists('phpass_cost', $options))
			throw new Exception\Phpass('phpass Cost Value must be specified', Exception\Phpass::COST_NOT_SPECIFIED);

		if(!array_key_exists('phpass_portable', $options))
			$options['phpass_portable'] = false; // Sane default.

		$this->hasher = new \PasswordHash($options['phpass_cost'], $options['phpass_portable']);
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

	/**
	 * Hashing a password
	 *
	 * @param  string 	Plaintext password to hash
	 * @return string 	Hashed representation of password
	 */
	public function hash_password($pass)
	{
		return $this->hasher->HashPassword($pass);
	}

	/**
	 * Checks if a password matches the hashed variant
	 *
	 * @param string $password 	Password to check
	 * @param string $hash 		Hash to check against
	 * @return bool
	 */
	public function check_password($pass, $hash)
	{
		return $this->hasher->CheckPassword($pass, $hash);
	}

	/**
	 * Attempt to log a user in. Will ask the PersistentStore to store the fact
	 * that a user logged in, and will tell & use StorageDelegate to fetch the data
	 * and that it occured.
	 *
	 * @param  string 	Username field value
	 * @param  string 	Password
	 * @return bool
	 */
	public function login($username, $password)
	{
		$user = $this->storage->auth_fetch_user_by_username($username);
		if(!$user)
			return false;

		if(!$this->check_password($password, $user['password']))
			return false;

		// Awesome, their details are good, log them in:
		$this->persistent_store->auth_write($this->name(), $user['id']);
		$this->storage->auth_user_logged_in($user['id']);

		// TODO: when events is done, probably worth broadcasting an event
		// here as well.
		
		return true;
	}

	/**
	 * Checking if a user is logged in or not.
	 *
	 * @return bool
	 */
	public function logged_in()
	{
		return (bool)$this->persistent_store->auth_read($this->name());
	}

	/**
	 * Logs a user out. Mostly just a call to the PersistentStore to null
	 * the session
	 *
	 * @return  void
	 */
	public function logout()
	{
		$this->persistent_store->auth_delete($this->name());
		// TODO: Again, probably broadcast an Event when this occurs
	}

}