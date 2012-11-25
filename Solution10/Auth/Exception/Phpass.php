<?php

namespace Solution10\Auth\Exception;

/**
 * Exception thrown when something goes wrong with phpass setup / usage.
 *
 * @package 	Solution10
 * @category 	Auth
 * @author 		Alex Gisby <alex@solution10.com>
 * @license 	MIT
 */
class Phpass extends \Exception
{
	/**
	 * When the cost value is not specified.
	 */
	const COST_NOT_SPECIFIED = 0;
}