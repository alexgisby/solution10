<?php

/**
 * Tests for the big daddy CSV class
 */
class CSVTest extends Solution10\Tests\TestCase
{
	/**
	 * @var 	CSV 	Test csv as it's used everywhere
	 */
	protected $csv;
	
	/**
	 * Set up the tests
	 */
	public function setUp()
	{
		$this->csv = new Solution10\CSV\CSV('Solution10/CSV/tests/data/test.csv');
	}
	
	/**
	 * Tests file finding
	 *
	 * @expectedException 		Solution10\CSV\Exception\File
	 * @expectedExceptionCode	1
	 */
	public function testFileFindFail()
	{
		$csv = new Solution10\CSV\CSV('Solution10/CSV/tests/data/does_not_exist.csv');
	}
	
	/**
	 * Tests file finding with a real file
	 */
	public function testFileFindSuccess()
	{
		$csv = new Solution10\CSV\CSV('Solution10/CSV/tests/data/test.csv');
		
		// Probably not the right way of doing this...
		$this->assertEquals(true, true);
	}
	
}