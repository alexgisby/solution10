<?php

/**
 * Testing the CSV part of Solution10
 */

require_once 'Solution10.php';

header('Content-type: text/plain');

echo 'Loading Up' . PHP_EOL;
$csv = new Solution10\CSV\CSV('Solution10/CSV/tests/data/test.csv');

foreach($csv as $row)
{
	print_r($row);
}

exit(PHP_EOL . 'Done');