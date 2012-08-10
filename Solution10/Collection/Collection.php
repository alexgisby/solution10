<?php

namespace Solution10\Collection;

/**
 * Solution10: Collection
 *
 * The Collection class is a general purpose class that holds a collection or set of data.
 * Think of it as an array on steroids. Useful features of Collections are quick slicing and
 * selecting portions of the data set and basic querying of the dataset.
 *
 * @package 	Solution10
 * @category 	Collection
 * @author 		Alex Gisby <alex@solution10.com>
 * @license 	MIT
 */
class Collection implements \Countable, \ArrayAccess, \Iterator
{
	const SORT_ASC = 1;
	const SORT_DESC = 2;

	/**
	 * @var 	array 	Data container
	 */
	protected $contents = array();
	
	/**
	 * @var 	array 	Selectors
	 */
	protected $selectors = array();
	
	/**
	 * Constructor
	 * Optionally pass in an array to start your Collection.
	 *
	 * @param 	array 	Collection items
	 * @return 	Collection
	 */
	public function __construct(array $initial_contents = array())
	{
		$this->contents = $initial_contents;
		
		$this->add_base_selectors();
	}
	
	/**
	 * Add a new selector type into the Collection.
	 * This allows you to add a new selector type on-the-fly to the collection.
	 *
	 * For example:
	 *		$collection->add_selector('[a-z0-9]', function(collection, matches){
	 *			// Do stuff here.
	 *		});
	 *
	 * 		$result = $collection['callselectorabove'];
	 *
	 * @param 	string 		Regex to match for selector to be invoked.
	 * @param 	callback 	Function (anonymous or otherwise) to call when the selector is found.
	 * @return 	this
	 */
	public function add_selector($selector, $callback)
	{
		$this->selectors[$selector] = $callback;
		return $this;
	}
	
	/**
	 * Adds the default selectors
	 */
	protected function add_base_selectors()
	{
		$this->add_selector('(?P<start>-?[0-9]+):(?P<end>[0-9]+|END)', array($this, 'splice'));
	}
	
	
	/**
	 * Calls a selector or throws an exception if it doesn't know what it is.
	 *
	 * @param 	string 		Key passed to the collection
	 * @throws	Exception\Index
	 * @return 	mixed 		Generally a new Collection instance, but theoretically anything.
	 */
	protected function call_selector($key)
	{
		foreach($this->selectors as $selector => $callback)
		{
			$regex = '/' . $selector . '/i';
			if(preg_match($regex, $key, $matches))
			{
				return call_user_func_array($callback, array($this, $matches));
			}
		}
		
		throw new Exception\Index('Unknown index: ' . $key);
	}
	
	/**
	 * ------------------------ Countable Implementation ---------------------------
	 */
	
	/**
	 * Returns the number of elements in this collection.
	 * Allows for the count() PHP function to be used.
	 *
	 * @return 	int
	 */
	public function count()
	{
		return count($this->contents);
	}
	
	
	/**
	 * ----------------------- Array Access Implementation -------------------------
	 */
	
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->contents);
	}
	
	
	/**
	 * This function contains a lot of the magic with splicing and such.
	 *
	 * @param 	mixed 	INT for numeric index. String splice selector otherwise.
	 * @return 	array
	 */
	public function offsetGet($offset)
	{
		if(is_int($offset))
		{
			return $this->contents[$offset];
		}
		elseif($offset == ':last')
		{
			// Shortcut for fetching the last item in the CSV:
			return $this->contents[count($this->contents) - 1];
		}
		elseif(preg_match('/(?P<start>-?[0-9]+):(?P<end>[0-9]+|END)/', $offset, $matches))
		{
			$start 	= $matches['start'];
			$end	= $matches['end'];
			
			// You can use the END keyword to select everything up until an end point.
			if($end == 'END')
				$end = count($this->contents) - 1;
			
			// If the start is negative, we count backwards from the end of the CSV. array_slice can handle negative
			// offsets, but bounds checking gets a bit gnarly.
			if($start < 0)
				$start = (count($this->contents)) - abs($start);
			
			// Check the bounds:
			if($start >= count($this->contents))
			{
				throw new Exception\Bounds('Start index (' . $start . ') is beyond the end of the file', Exception\Bounds::ERROR_START_OUT_OF_RANGE);
			}
			elseif($start > $end)
			{
				throw new Exception\Bounds('Start index is greater than end index: ' . $start . ' > ' . $end, Exception\Bounds::ERROR_START_GT_END);
			}
			
			// If the end index is > the total, we set to the total:
			if($end >= count($this->contents))
			{
				$end = count($this->contents) - 1;
			}
			
			// Now work out the params for array_slice
			$offset = $start;
			$length	= ($end - $start) + 1;
			
			return array_slice($this->contents, $offset, $length);
		}
		else
		{
			return $this->call_selector($offset);
		}
		
		// We've got an index we don't know what to do with. Throw an exception:
		throw new Exception\Index('Unknown index: ' . $offset);
	}
	
	public function offsetSet($offset, $value)
	{
		if(is_null($offset))
		{
			$this->contents[] = $value;
		}
		else
		{
			$this->contents[$offset] = $value;
		}
	}
	
	public function offsetUnset($offset)
	{
		unset($this->contents[$offset]);
	}
	
	
	/**
	 * ---------------------- Array Access Implementation ----------------------------
	 */
	
	protected $iter_current_pos = 0;
	
	public function current()
	{
		return $this->contents[$this->iter_current_pos];
	}
	
	public function key()
	{
		return $this->iter_current_pos;
	}
	
	public function next()
	{
		$this->iter_current_pos ++;
	}
	
	public function rewind()
	{
		$this->iter_current_pos = 0;
	}
	
	public function valid()
	{
		return isset($this->contents[$this->iter_current_pos]);
	}
	
	
	/**
	 * ---------------------------------- Sorting -----------------------------------
	 */
	
	/**
	 * Sorts the contents of the Collection. Uses the same sort flags as sort() and asort().
	 * It
	 *
	 * @param 	int 	Sort direction (use the class constants)
	 * @param 	bool 	Whether to preserve the keys of the collection or not. Default false.
	 * @param 	int 	Sort flags (see http://php.net/sort)
	 * @return 	this
	 */
	public function sort($direction, $preserve_keys = false, $flags = SORT_REGULAR)
	{
		switch($direction)
		{
			case self::SORT_ASC:
				($preserve_keys)? asort($this->contents, $flags) : sort($this->contents, $flags);
			break;
			
			case self::SORT_DESC:
				($preserve_keys)? arsort($this->contents, $flags) : rsort($this->contents, $flags);
			break;
			
			default:
				throw new Exception('Unknown sort direction: ' . $direction);
			break;
		}
		
		return $this;
	}
	
	
}