<?php

namespace Solution10\Auth\Tests;

/**
 * Storage Delegate Mock.
 */
class StorageDelegateMock implements \Solution10\Auth\StorageDelegate
{
	private $users = array(
		1 => array(
			'name' => 'Alex',
			'email' => 'alex@solution10.com',
		),
		2 => array(
			'name' => 'Lucie',
			'email' => 'lucie@solution10.com',
		),
	);


	public function auth_fetch_user_by_id($id)
	{
		return 	(array_key_exists($id, $this->users))? $this->users[$id] : false;
	}
}