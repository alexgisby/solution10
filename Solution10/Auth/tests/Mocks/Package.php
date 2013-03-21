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
			->add_rule('login', false)
			->add_rule('logout', false)
			->add_rules(array(
					'view_profile' => true,
					'view_homepage' => false,
			  	))
			->add_callback('edit_post', array($this, 'edit_post'))
			->add_callbacks(array(
					'static_string' => __NAMESPACE__ . '\Package::static_string',
					'static_array' 	=> array(__NAMESPACE__ . '\Package', 'static_array'),
					'closure' => function() {
						return false;
					},
					'closure_with_args' => function($arg1, $arg2) {
						return $arg1 . $arg2;
					}
				));

	}

	public function name()
	{
		return 'TestPackage';
	}

	public function edit_post()
	{
		return false;
	}

	public static function static_string()
	{
		return false;
	}

	public static function static_array()
	{
		return false;
	}
}