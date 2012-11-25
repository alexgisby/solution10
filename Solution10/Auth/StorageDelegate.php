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
}