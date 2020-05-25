<?php

// Generate 11 random numbers between 0 and 100
// This method of generating the random values is certainly not optimal, 
// but for the purpose of this excercise, we
//
// 1) Know that we're dealing with a small range of possible values
// 2) Are primarily re concerned with sorting speed rather than generation-speed
//
//
//
// Performance (on my machine, which has a lot of RAM (irrelevant for this excercise) 
// but is relatively slow in CPU terms (timings are for 1 million interations): 
//
// Count Sort: 18.629620075226 secs
// PHP Native Sort: 0.88299703598022 secs
//
//
// Performance estimate for 10 Billion iterations): 
//
// Count Sort: 186296 secs = 51.7 hours 
// PHP Native Sort: 8829 secs = 2.45 hours
//


// As such, we'll take this simple but practical approach for generating the random numbers
function generateUniqueRandom(int $min, int $max, int $quantity) : array
{
	$numbers = range($min, $max);
	shuffle($numbers);
	return array_slice($numbers, 0, $quantity);
}


// Tested various sorts, this is the best I was able to come up with, native PHP sort 
// still beats this by more than an order of magnitude, which makes sense since it 
// is implemented in native C vs. PHP code.
function countSort(array $values) : array
{
	$counts = array();
	$min    = PHP_INT_MAX;
	$max    = PHP_INT_MIN;
	foreach ($values as $v) {
		$counts[$v] = isset($counts[$v]) ? $counts[$v] + 1 : 1;
		if ($v < $min) {
			$min = $v;
		}
		if ($v > $max) {
			$max = $v;
		}
	}

	$sorted = array();
	for ($i=$min; $i<=$max; $i++) {
		if (isset($counts[$i])) {
			for ($j=0; $j<$counts[$i]; $j++) {
				$sorted[] = $i;
        		}
		}
	}

	return $sorted;
}



///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////


$min    = 0;
$max    = 100;
$count  = 11;
$values = generateUniqueRandom($min, $max, $count);
$nRuns  = 1000000;


$starttime = microtime(true);
for ($i=0; $i<$nRuns; $i++) {
	$sorted = countSort($values);
}
$endtime = microtime(true);
$diff1 = $endtime - $starttime;


$starttime = microtime(true);
for ($i=0; $i<$nRuns; $i++) {
	$v = $values;
	sort($v, SORT_NUMERIC);
}
$endtime = microtime(true);
$diff2 = $endtime - $starttime;


print "------- RESULTS ----------\n";
print "Count Sort: $diff1 secs\n";
print "PHP Native Sort: $diff2 secs\n";

