<?php

namespace Solution10\Auth\Tests\Mocks;

/**
 * General Package Mock
 */
class Package extends \Solution10\Auth\Package
{
	public function init()
	{
		$this
			->add_rule('login', true)
			->add_rule('logout', false)
			->add_rules(array(
					'view_profile' => true,
					'view_homepage' => false,
			  	))
			->add_callback('edit_post', array($this, 'edit_post'))
			->add_callbacks(array(
					'static_string' => 'PackageMock::static_string',
					'static_array' 	=> array('PackageMock', 'static_array'),
					'closure' => function() {
						return false;
					}
				));

	}

	public function edit_post()
	{
		return false;
	}

	public static function static_string()
	{
		return true;
	}

	public static function static_array()
	{
		return false;
	}
}