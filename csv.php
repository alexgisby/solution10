<?php

/**
 * Testing the CSV part of Solution10
 */

require_once 'Solution10.php';

header('Content-type: text/plain');

echo 'Loading Up' . PHP_EOL;

$schema = new Solution10\CSV\Schema();
$schema->add_field(0, 'full_name', array(
	'not_empty',
	function($value)
	{
		var_dump($value);
		return true;
	}
));

var_dump($schema); exit;

$csv = new Solution10\CSV\CSV('Solution10/CSV/tests/data/test.csv', $schema);

foreach($csv as $row)
{
	print_r($row);
}

exit(PHP_EOL . 'Done');