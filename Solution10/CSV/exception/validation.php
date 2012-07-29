<?php

namespace Solution10\CSV\Exception;

/**
 * Validation Exception. Thrown if something goes wrong during validation.
 *
 * @package 	Solution10\CSV
 * @category  	Exceptions
 * @author 		Alex Gisby <alex@solution10.com>
 */
class Validation extends Exception
{
	/**
	 * @var 	int 	Validation method not found error.
	 */
	const ERROR_UNKNOWN_METHOD = 1;
	
}