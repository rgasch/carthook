<?php

// Generate 11 random numbers between 0 and 100 and sort them.
//
// 1) Know that we're dealing with a small range of possible values
// 2) Are primarily re concerned with sorting speed rather than generation-speed
//
// Performance (on my machine, which has a lot of RAM (irrelevant for this exercise)
// but is nothing special in CPU terms (timings are for 1 million interations)):
//
// Count Sort: 18.629620075226 secs
// PHP Native Sort: 0.88299703598022 secs
//
//
// Performance estimate for 10 Billion (ie: 10000x above timings) iterations:
//
// Count Sort: 18.6296 * 10000 = 1186296 secs = 51.7 hours
// PHP Native Sort: 0.8829 * 10000 = 8829 secs = 2.45 hours
//


// This method of generating the random values is certainly not optimal, but for the
// purpose of this exercise, we're only concerned with the sorting speed.
// As such, we'll take this simple but practical approach for generating the random numbers
function generateUniqueRandom(int $min, int $max, int $quantity) : array
{
	$numbers = range($min, $max);
	shuffle($numbers);
	return array_slice($numbers, 0, $quantity);
}


// Tested various sorts, this is the best I was able to come up with. This algorithm (counting
// sort) is highly dependent on the range of values we're trying to sort; for this case it's
// a really good candidate because we know that our range is [0-100].
//
// Native PHP sort still beats this by more than an order of magnitude, which makes sense since
// 1) We're sorting a small (11-item) array
// 2) Native PHP sort is implemented in compiled C vs. PHP code.
//
// #1 means that the penalty we're 'paying' for the (average) O(n log2 n) performance
// for native quicksort is small due to the small number of elements.
function countSort(array $values) : array
{
	$counts = array();
	$min    = PHP_INT_MAX;
	$max    = PHP_INT_MIN;
	foreach ($values as $v) {
	    // No need to check for duplicate entries, the method we use to generate the
        // random number array guarantees to deliver a unique set of values.
		$counts[$v] = 1;
		if ($v < $min) {
			$min = $v;
		} elseif ($v > $max) {
			$max = $v;
		}
	}

	// An alternative way of doing this would be to extract a sorted set of array keys
    // of $count and iterate over those. However, this sort would also cost CPU cycles
    // and given that we're only sorting numbers between the range [0-100], the current
    // approach is fine.
    // The consideration of which approach to use depends on the size of the array to
    // sort and the range of possible values. For this case, we're good.

	$sorted = array();
	for ($i=$min; $i<=$max; $i++) {
		if (isset($counts[$i])) {
		    // No need to do a loop to account for multiple/identical entries, our
            // input is guaranteed to consist of unique integers
            $sorted[] = $i;
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


// Self-implemented (PHP-based) sort
$starttime = microtime(true);
for ($i=0; $i<$nRuns; $i++) {
	$sorted = countSort($values);
}
$endtime = microtime(true);
$diff1 = $endtime - $starttime;


// PHP Native Sort for comparison
$starttime = microtime(true);
for ($i=0; $i<$nRuns; $i++) {
	$v = $values;
	sort($v, SORT_NUMERIC);
}
$endtime = microtime(true);
$diff2 = $endtime - $starttime;


print "------- Performance Timings ----------\n";
print "Count Sort: $diff1 secs\n";
print "PHP Native Sort: $diff2 secs\n";

