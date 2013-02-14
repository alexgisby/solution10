<?php

namespace Solution10\Auth\Tests\Mocks;

/**
 * Storage Delegate Mock.
 */
class StorageDelegate implements \Solution10\Auth\StorageDelegate
{
	// Public only so the tests can access this data to verify:
	public $users = array(
		1 => array(
			'id' => 1,
			'username' => 'Alex',
			'email' => 'alex@solution10.com',
			'password' => '$2a$08$pQIwqrJ00RbAikHLcQ8tOuSrDFEvToDmbXxtXEFO8vJRC38cXZX76', // Alex
			'packages' => array(),
			'overrides' => array(),
		),
		2 => array(
			'id' => 2,
			'username' => 'Lucie',
			'email' => 'lucie@solution10.com',
			'packages' => array(),
			'overrides' => array(),
		),
	);

	public function auth_fetch_user_by_username($instance_name, $username)
	{
		foreach($this->users as $user)
		{
			if($user['username'] === $username)
				return $user;
		}

		return false;
	}

	public function auth_fetch_user_representation($instance_name, $user_id)
	{
		return (array_key_exists($user_id, $this->users))? new UserRepresentation($this->users[$user_id]) : false;
	}

	public function auth_add_package_to_user($instance_name, \Solution10\Auth\UserRepresentation $user, \Solution10\Auth\Package $package)
	{
		foreach($this->users as &$u)
		{
			if($u['id'] == $user->id())
			{
				$u['packages'][] = $package;
			}
		}

		return true;
	}


	public function auth_remove_package_from_user($instance_name, \Solution10\Auth\UserRepresentation $user, \Solution10\Auth\Package $package)
	{
		foreach($this->users as &$u)
		{
			if($u['id'] == $user->id())
			{
				foreach($u['packages'] as $idx => $p)
				{
					if($p->name() === $package->name())
					{
						unset($u['packages'][$idx]);
						return true;
					}
				}
			}
		}

		return true;
	}

	public function auth_fetch_packages_for_user($instance_name, \Solution10\Auth\UserRepresentation $user)
	{
		foreach($this->users as $u)
		{
			if($u['id'] == $user->id())
			{
				return $u['packages'];
			}
		}

		return array();
	}

	public function auth_user_has_package($instance_name, \Solution10\Auth\UserRepresentation $user, \Solution10\Auth\Package $package)
	{
		foreach($this->users[$user->id()]['packages'] as $p)
		{
			if($p->name() === $package->name())
			{
				return true;
			}
		}

		return false;
	}

	public function auth_override_permission_for_user($instance_name, \Solution10\Auth\UserRepresentation $user, $permission, $new_value)
	{
		if(array_key_exists($user->id(), $this->users))
		{
			$this->users[$user->id()]['overrides'][$permission] = $new_value;
			return true;
		}

		return false;
	}

	public function auth_fetch_overrides_for_user($instance_name, \Solution10\Auth\UserRepresentation $user)
	{
		if(array_key_exists($user->id(), $this->users))
		{
			return $this->users[$user->id()]['overrides'];
		}

		return array();
	}



	public function auth_user_logged_in($user_id)
	{
		// Do nothing for now.
		return true;
	}
}