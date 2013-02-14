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
 * @uses   		phpass, Solution10\Collection\Collection
 */
class Auth
{
	/**
	 * @var 	string 	Instance name
	 */
	protected $name;

	/**
	 * @var SessionDelegate Instance of the SessionDelegate interface.
	 */
	protected $session;

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
	 * @var  mixed 	The representation of the user that StorageDelegate passes back
	 */
	protected $user;

	/**
	 * @var 	array 	 Permissions cache
	 */
	protected $permissions_cache = array();

	/**
	 * Constructor. Pass in all the options for this instance, including all your
	 * hashing and salting stuff.
	 *
	 * @param 	string 			$name 				Name of this instance.
	 * @param   SessionDelegate $session 			The SessionDelegate implementation for storing Session type data
	 * @param   StorageDelegate $storage 			The StorageDelegate implementation for data access.
	 * @param 	array 			$options 			Options. Must contain, err, something.
	 * @return 	this
	 */
	public function __construct($name, SessionDelegate $session, StorageDelegate $storage, array $options)
	{
		$this->name = $name;
		$this->session = $session;
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
	 * @uses   StorageDelegate::auth_fetch_user_by_username
	 * @uses   PersistentStore::auth_write
	 */
	public function login($username, $password)
	{
		$user = $this->storage->auth_fetch_user_by_username($this->name(), $username);
		if(!$user)
			return false;

		if(!$this->check_password($password, $user['password']))
			return false;

		// Awesome, their details are good, log them in:
		$this->session->auth_write($this->name(), $user['id']);
		$this->storage->auth_user_logged_in($user['id']);

		// TODO: when events is done, probably worth broadcasting an event
		// here as well.
		
		return true;
	}

	/**
	 * Checking if a user is logged in or not.
	 *
	 * @return bool
	 * @uses   SessionDelegate::auth_read
	 */
	public function logged_in()
	{
		return (bool)$this->session->auth_read($this->name());
	}

	/**
	 * Logs a user out. Mostly just a call to the PersistentStore to null
	 * the session
	 *
	 * @return  void
	 * @uses   SessionDelegate::auth_delete
	 */
	public function logout()
	{
		$this->session->auth_delete($this->name());
		$this->user = false;
		// TODO: Again, probably broadcast an Event when this occurs
	}

	/**
	 * Forces a UserRepresentation or user ID to be logged in.
	 * Use this with extreme caution, it doesn't do any validation, it'll
	 * just blindly let them in. Only use this for post-registration steps
	 * if it all.
	 * 
	 * @param 	UserRepresentation|int
	 * @return 	bool 	Whether the force worked or not.
	 */
	public function force_login($user)
	{
		if(is_object($user) && ($user instanceof UserRepresentation) == false)
			return false;

		$user_id = (is_object($user))? $user->id() : $user;
		// $user = $this->storage->auth_fetch_user_by_username($this->name(), $username);
		$user = $this->storage->auth_fetch_user_representation($this->name(), $user_id);
		if(!$user)
			return false;

		$this->session->auth_write($this->name(), $user->id());
		$this->storage->auth_user_logged_in($user->id());

		return true;
	}

	/**
	 * Returns the currently logged in user. False if there's no user.
	 *
	 * @return 	mixed 	Whatever the StorageDelegate throws back
	 * @uses   StorageDelegate::auth_fetch_user_representation
	 */
	public function user()
	{
		if(!$this->logged_in())
			return false;

		if(!isset($this->user))
		{
			$this->user = $this->storage->auth_fetch_user_representation($this->name(), $this->session->auth_read($this->name()));
		}

		// If the user is false, we've got a bad-un, so kill the session:
		if(!$this->user)
			$this->logout();

		return $this->user;
	}

	/**
	 * Shortcut for loading user representation, will throw correct exception
	 * if the user is not found.
	 *
	 * @param 	mixed 	User primary key
	 * @return 	mixed 	User rep from auth_fetch_user_representation
	 * @throws 	PackageException
	 * @uses 	StorageDelegate
	 */
	protected function load_user_representation($user_id)
	{
		$user = $this->storage->auth_fetch_user_representation($this->name(), $user_id);
		if(!$user)
			throw new Exception\Package('User ' . $user_id . ' not found.', Exception\Package::USER_NOT_FOUND);

		return $user;
	}

	/**
	 * ------------ Package Management Functions ---------------
	 */

	/**
	 * Adds a package to a user
	 *
	 * @param 	mixed 	Primary key of the user
	 * @param 	mixed 	String name of package, or instance of package.
	 * @return 	this
	 * @throws 	PackageException
	 * @uses 	StorageDelegate 	Lots.
	 */
	public function add_package_to_user($user_id, $package)
	{
		$user = $this->load_user_representation($user_id);

		// Check that the package exists:
		if(is_string($package) && class_exists($package))
		{
			$package = new $package();
		}
		elseif(is_string($package) && !class_exists($package))
		{
			throw new Exception\Package('Package: ' . $package . ' not found.', Exception\Package::PACKAGE_NOT_FOUND);
		}

		// Check that the package is correct:
		if(!$package instanceof Package)
			throw new Exception\Package('Package: ' . get_class($package) . ' must inherit from Auth\Package', Exception\Package::PACKAGE_BAD_LINEAGE);

		// All good. Add the package to the user:
		$this->storage->auth_add_package_to_user($this->name(), $user, $package);

		// And rebuild the permissions:
		$this->build_permissions_for_user($user_id);

		return $this;
	}

	/**
	 * Removing a package from a user
	 *
	 * @param 	mixed 	Primary Key of the user
	 * @param 	mixed 	String name of the package ot instance of the package
	 * @return 	this
	 * @throws 	PackageException
	 * @uses 	StorageDelegate
	 */
	public function remove_package_from_user($user_id, $package)
	{
		$user = $this->load_user_representation($user_id);

		// We kind of don't care if the package doesn't exist, so even if it doesn't,
		// just palm it off on the StorageDelegate and let it fail silently.
		if((is_string($package) && class_exists($package)) || $package instanceof Package)
		{
			$package = (is_object($package))? $package : new $package();
			$this->storage->auth_remove_package_from_user($this->name(), $user, $package);

			// And rebuild the permissions:
			$this->build_permissions_for_user($user_id);
		}

		return $this;
	}

	/**
	 * Fetches the packages for a user.
	 *
	 * @param 	mixed 	Primary key of the user
	 * @return 	array
	 * @throws 	PackageException
	 * @uses 	StorageDelegate
	 */
	public function packages_for_user($user_id)
	{
		$user = $this->load_user_representation($user_id);
		return (array)$this->storage->auth_fetch_packages_for_user($this->name(), $user);
	}


	/**
	 * Checks to see if a user has a package or not. If package is not a valid Package
	 * or doesn't exist, function will fail silently and return false
	 *
	 * @param 	mixed 	Primary key of the user
	 * @param 	mixed 	String name of the package ot instance of the package
	 * @return 	bool
	 * @throws 	PackageException
	 * @uses 	StorageDelegate
	 */
	public function user_has_package($user_id, $package)
	{
		$user = $this->load_user_representation($user_id);

		if((is_string($package) && class_exists($package)) || $package instanceof Package)
		{
			$package = (is_object($package))? $package : new $package();
			return $this->storage->auth_user_has_package($this->name(), $user, $package);
		}

		return false;
	}


	/**
	 * --------------- Permissions! -----------------
	 */

	/**
	 * Builds up the permissions for a user by looping through,
	 * taking on the highest precedence of each tier.
	 */
	protected function build_permissions_for_user($user_id)
	{
		// Make use of Collection to do some clever sorting:
		$all_packages = $this->packages_for_user($user_id);
		$sorted_packages = new \Solution10\Collection\Collection($all_packages);
		$sorted_packages->sort_by_member('precedence');

		$permissions = array();
		foreach($sorted_packages as $package)
		{
			foreach($package->rules() as $name => $rule)
			{
				$permissions[$name] = $rule;
			}

			foreach($package->callbacks() as $name => $callback)
			{
				$permissions[$name] = $callback;
			}
		}

		// And now process any overrides that this user has:
		$user = $this->load_user_representation($user_id);
		$overrides = $this->storage->auth_fetch_overrides_for_user($this->name(), $user);
		foreach($overrides as $permission => $new_value)
		{
			if(array_key_exists($permission, $permissions))
			{
				$permissions[$permission] = $new_value;
			}
		}

		$this->permissions_cache[$user_id] = $permissions;
	}

	/**
	 * Can function. The most useful function in the whole thing
	 * 
	 * @param 	mixed 	User ID
	 * @param 	string 	Permission name to check in the Package
	 * @param 	array 	Arguments to pass to a package callback
	 * @return 	bool 	Yes or no
	 */
	public function user_can($user_id, $permission, array $args = array())
	{
		if(!array_key_exists($permission, $this->permissions_cache[$user_id]))
		{
			// No permission found. To be safe, we always return false in these
			// cases.
			return false;
		}

		$perm = $this->permissions_cache[$user_id][$permission];

		if(is_bool($perm))
		{
			return $perm;
		}
		else
		{
			return call_user_func_array($perm, $args);
		}

		// Defensively false anything else:
		return false;
	}


	/**
	 * Can() is a shortcut for user_can() for the currently logged in user
	 *
	 * @param 	string 	Permission name to check in the Package
	 * @param 	array 	Arguments to pass to a package callback
	 * @return 	bool 	Yes or no
	 */
	public function can($permission, array $args = array())
	{
		if(!$this->logged_in())
			return false;

		return $this->user_can($this->session->auth_read($this->name()), $permission, $args);
	}


	/**
	 * Overrides a permission for a user. You can use this to further customise
	 * a Package for a specific user, for instance, allowing a regular user to blog
	 * or revoking someone's commenting rights, all without setting up a new package.
	 *
	 * @param 	mixed 	User ID to change
	 * @param 	string 	Permission name to change
	 * @param 	bool 	New permission
	 * @return 	this
	 */
	public function override_permission_for_user($user_id, $permission, $new_value)
	{
		$user = $this->load_user_representation($user_id);
		
		if($this->storage->auth_override_permission_for_user($this->name(), $user, $permission, $new_value))
		{
			$this->build_permissions_for_user($user_id);
		}

		return $this;
	}

}