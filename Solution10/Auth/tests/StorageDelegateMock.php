<?php

namespace Solution10\Auth\Tests;

/**
 * Storage Delegate Mock.
 */
class StorageDelegateMock implements \Solution10\Auth\StorageDelegate
{
	private $users = array(
		1 => array(
			'id' => 1,
			'username' => 'Alex',
			'email' => 'alex@solution10.com',
			'password' => '$2a$08$pQIwqrJ00RbAikHLcQ8tOuSrDFEvToDmbXxtXEFO8vJRC38cXZX76', // Alex
		),
		2 => array(
			'id' => 2,
			'username' => 'Lucie',
			'email' => 'lucie@solution10.com',
		),
	);


	public function auth_fetch_user_by_id($id)
	{
		return 	(array_key_exists($id, $this->users))? $this->users[$id] : false;
	}

	public function auth_fetch_user_by_username($username)
	{
		foreach($this->users as $user)
		{
			if($user['username'] === $username)
				return $user;
		}

		return false;
	}

	public function auth_user_logged_in($user_id)
	{
		// Do nothing for now.
		return true;
	}
}