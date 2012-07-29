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
	
	
	/**
	 * Test that the rows count is correct
	 */
	public function testCount()
	{
		$this->assertEquals(4, count($this->csv));
	}
	
	
	/**
	 * Tests selecting by an index
	 */
	public function testArrayAccessIndex()
	{
		$item 	= $this->csv[0];
		
		// Check it's an array:
		$this->assertEquals(true, is_array($item));
		
		// Check it has 3 elements:
		$this->assertEquals(3, count($item));
		
		// Check the first element is Alex:
		$this->assertEquals('Alex', $item[0]);
	}
	
	/**
	 * Tests ArrayAccess::offsetExists
	 */
	public function testArrayAccessIsset()
	{
		$this->assertEquals(true, isset($this->csv[0]));
	}
	
	/**
	 * Tests inserting records into the array
	 */
	public function testArrayAccessSetNull()
	{
		$this->csv[]	= array('Jim', 11, 'jim@example.com');
		
		// Check there's five elements:
		$this->assertEquals(5, count($this->csv));
		
		// Check the last one has a name of Jim
		$this->assertEquals('Jim', $this->csv[4][0]);
		
		// Re-read the data to restore it:
		$this->setUp();
	}
	
	/**
	 * Tests inserting records into the array
	 */
	public function testArrayAccessSetIndex()
	{
		$this->csv[4]	= array('Jim', 11, 'jim@example.com');
		
		// Check there's five elements:
		$this->assertEquals(5, count($this->csv));
		
		// Check the last one has a name of Jim
		$this->assertEquals('Jim', $this->csv[4][0]);
		
		// Re-read the data to restore it:
		$this->setUp();
	}
	
	/**
	 * Tests unsetting records from the rows
	 */
	public function testArrayAccessUnset()
	{
		unset($this->csv[3]);
		
		$this->assertEquals(3, count($this->csv));
		
		// Re-read the data to restore it:
		$this->setUp();
	}
	
	/**
	 * Tests Iterator.
	 */
	public function testIterator()
	{
		$iterations = 0;
		
		foreach($this->csv as $key => $row)
		{
			$this->assertEquals(true, is_array($row));
			$this->assertEquals($this->csv[$iterations][0], $row[0]);
			
			$iterations ++;
		}
		
		$this->assertEquals(4, $iterations);
	}
	
	
	/**
	 * -------------------- Splicing tests, of which there are many -----------------
	 */
	
	/**
	 * Tests out of bounds
	 *
	 * @expectedException 		Solution10\CSV\Exception\Bounds
	 * @expectedExceptionCode 	1
	 */
	public function testOOBStart()
	{
		$this->csv['10:20'];
	}
	
	/**
	 * Tests start > end
	 *
	 * @expectedException 		Solution10\CSV\Exception\Bounds
	 * @expectedExceptionCode 	2
	 */
	public function testOOBStartGTEnd()
	{
		$this->csv['2:0'];
	}
	
	/**
	 * Tests a bad index
	 *
	 * @expectedException 		Solution10\CSV\Exception\Index
	 */
	public function testBadIndex()
	{
		$this->csv['thisisabadindex'];
	}
	
	/**
	 * Testing basic splicing
	 */
	public function testBasicSplicing()
	{
		$splice = $this->csv['1:2'];
		
		$this->assertEquals(2, count($splice));
		$this->assertEquals('Tim', $splice[0][0]);
		$this->assertEquals('Jane', $splice[1][0]);
	}
	
	/**
	 * Tests the 'over-the-end' splice, where the end is greater than the length
	 */
	public function testOverTheEndSplicing()
	{
		$splice = $this->csv['2:10'];
		
		$this->assertEquals(2, count($splice));
		$this->assertEquals('Jane', $splice[0][0]);
		$this->assertEquals('Hannah', $splice[1][0]);
	}
	
	/**
	 * Tests negative splicing
	 */
	public function testNegativeSplicing()
	{
		$splice = $this->csv['-1:3'];
		$this->assertEquals(1, count($splice));
		$this->assertEquals('Hannah', $splice[0][0]);
	}
	
	
	/**
	 * Tests the END keyword
	 */
	public function testEndSplicing()
	{
		$splice = $this->csv['1:END'];
		$this->assertEquals(3, count($splice));
		$this->assertEquals('Tim', $splice[0][0]);
		$this->assertEquals('Jane', $splice[1][0]);
		$this->assertEquals('Hannah', $splice[2][0]);
	}
	
	/**
	 * Test the :LAST selector
	 */
	public function testLastSelector()
	{
		$splice = $this->csv[':last'];
		$this->assertEquals('Hannah', $splice[0]);
	}
	
}