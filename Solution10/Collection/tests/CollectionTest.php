<?php

class CollectionTest extends Solution10\Tests\TestCase
{
	public $collection;
	
	/**
	 * Sets up a basic Collection to use in multiple tests
	 */
	public function setUp()
	{
		$this->collection = new Solution10\Collection\Collection(array(
			'Item1', 'Item2', 'Item3', 
		));
	}
	
	/**
	 * Tests the constructor of the Collection.
	 */
	public function testConstruct()
	{
		$collection = new Solution10\Collection\Collection(array(
			'Item1', 'Item2', 'Item3',
		));
		
		$this->assertEquals('Solution10\Collection\Collection', get_class($collection));
	}
	
	/**
	 * Tests constructor with no params
	 */
	public function testEmptyContructor()
	{
		$collection = new Solution10\Collection\Collection();
		$this->assertEquals('Solution10\Collection\Collection', get_class($collection));
	}
	
	/**
	 * Test the member counting function
	 */
	public function testMemberCount()
	{		
		$this->assertEquals(3, $this->collection->count());
	}
	
	/**
	 * Tests the count interface
	 */
	public function testCount()
	{
		$this->assertEquals(3, count($this->collection));
	}
	
	/**
	 * -------------- Selector Tests ----------------
	 */
	
	/**
	 * Tests adding an anonymous function as a callback
	 */
	public function testAnonFuncSelector()
	{
		$this->collection->add_selector('::test::', function(){
			return true;
		});
		
		$this->assertTrue($this->collection['::test::']);
	}
	
	/**
	 * Tests selecting by an index
	 */
	public function testArrayAccessIndex()
	{
		$item 	= $this->collection[0];
		
		// Check it's an array:
		$this->assertEquals('Item1', $item);
	}
	
	/**
	 * Tests ArrayAccess::offsetExists
	 */
	public function testArrayAccessIsset()
	{
		$this->assertEquals(true, isset($this->collection[0]));
	}
	
	/**
	 * Tests inserting records into the array
	 */
	public function testArrayAccessSetNull()
	{
		$this->collection[]	= 'NullItem';
		
		$this->assertEquals(4, count($this->collection));
		$this->assertEquals('NullItem', $this->collection[3]);
		
		// Re-read the data to restore it:
		$this->setUp();
	}
	
	/**
	 * Tests inserting records into the array
	 */
	public function testArrayAccessSetIndex()
	{
		$this->collection[3]	= 'Item4';
		
		// Check there's five elements:
		$this->assertEquals(4, count($this->collection));
		
		// Check the last one has a name of Jim
		$this->assertEquals('Item4', $this->collection[3]);
		
		// Re-read the data to restore it:
		$this->setUp();
	}
	
	/**
	 * Tests unsetting records from the rows
	 */
	public function testArrayAccessUnset()
	{
		unset($this->collection[2]);
		
		$this->assertEquals(2, count($this->collection));
		
		// Re-read the data to restore it:
		$this->setUp();
	}
	
	/**
	 * Tests Iterator.
	 */
	public function testIterator()
	{
		$iterations = 0;
		
		foreach($this->collection as $key => $item)
		{
			$this->assertEquals('Item' . ($key + 1), $item);
			$iterations ++;
		}
		
		$this->assertEquals(count($this->collection), $iterations);
	}
	
	
	/**
	 * -------------------- Splicing tests, of which there are many -----------------
	 */
	
	/**
	 * Tests out of bounds
	 *
	 * @expectedException 		Solution10\Collection\Exception\Bounds
	 * @expectedExceptionCode 	1
	 */
	public function testOOBStart()
	{
		$this->collection['10:20'];
	}
	
	/**
	 * Tests start > end
	 *
	 * @expectedException 		Solution10\Collection\Exception\Bounds
	 * @expectedExceptionCode 	2
	 */
	public function testOOBStartGTEnd()
	{
		$this->collection['2:0'];
	}
	
	/**
	 * Tests a bad index
	 *
	 * @expectedException 		Solution10\Collection\Exception\Index
	 */
	public function testBadIndex()
	{
		$this->collection['thisisabadindex'];
	}
	
	/**
	 * Testing basic splicing
	 */
	public function testBasicSplicing()
	{
		$splice = $this->collection['1:2'];
		
		$this->assertEquals(2, count($splice));
		$this->assertEquals('Item2', $splice[0]);
		$this->assertEquals('Item3', $splice[1]);
	}
	
	/**
	 * Tests the 'over-the-end' splice, where the end is greater than the length
	 */
	public function testOverTheEndSplicing()
	{
		$splice = $this->collection['1:10'];
		
		$this->assertEquals(2, count($splice));
		$this->assertEquals('Item2', $splice[0]);
		$this->assertEquals('Item3', $splice[1]);
	}
	
	/**
	 * Tests negative splicing
	 */
	public function testNegativeSplicing()
	{
		$splice = $this->collection['-1:3'];
		$this->assertEquals(1, count($splice));
		$this->assertEquals('Item3', $splice[0]);
	}
	
	
	/**
	 * Tests the END keyword
	 */
	public function testEndSplicing()
	{
		$splice = $this->collection['1:END'];
		$this->assertEquals(2, count($splice));
		$this->assertEquals('Item2', $splice[0]);
		$this->assertEquals('Item3', $splice[1]);
	}
	
	/**
	 * Test the :LAST selector
	 */
	public function testLastSelector()
	{
		$splice = $this->collection[':last'];
		$this->assertEquals('Item3', $splice);
	}
	
	
	/**
	 * ----------------- Sorting Tests ------------------------
	 */
	
	/**
	 * Testing basic ascending sorting 
	 */
	public function testSort()
	{
		$collection = new Solution10\Collection\Collection(array(
			'Apple', 'Orange', 'Banana', 'Cucumber',
		));
		
		$collection->sort(Solution10\Collection\Collection::SORT_ASC);
		$this->assertEquals('Apple', $collection[0]);
		$this->assertEquals('Banana', $collection[1]);
		$this->assertEquals('Cucumber', $collection[2]);
		$this->assertEquals('Orange', $collection[3]);
	}
	
	/**
	 * Testing basic descending sorting
	 */
	public function testRSort()
	{
		$collection = new Solution10\Collection\Collection(array(
			'Apple', 'Orange', 'Banana', 'Cucumber',
		));
		
		$collection->sort(Solution10\Collection\Collection::SORT_DESC);
		$this->assertEquals('Apple', $collection[3]);
		$this->assertEquals('Banana', $collection[2]);
		$this->assertEquals('Cucumber', $collection[1]);
		$this->assertEquals('Orange', $collection[0]);
	}
	
}