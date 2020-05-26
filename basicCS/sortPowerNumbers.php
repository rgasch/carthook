<?php

// Sort 10000 numbers where each number is random
//   rand(100,10000)^rand(100,10000)
// ie: a random number between 100-10000 raised to a random number between 100-10000.
//
// Since the numbers we generate have different exponents, we need to convert them
// to a common base (base-10, but could be any other base). This 'standardizes' the
// numbers and allows us to sort them based on the exponent which then becomes a
// simple sort.
//
// Performance (on my machine, which has a lot of RAM (irrelevant for this excercise)
// but is nothing special in CPU terms):
//
// Count Sort: 0.012 secs
// PHP Native Sort: 0.068 secs
//
// Since this basically runs in real-time, there's no need to estimate how long it
// would take.


// Generate array of random power numbers
function generateRandomArray(int $min=100, int $max=10000, int $quantity=10000) : array
{
	$rc = [];

	for ($i=0; $i<$quantity; $i++) {
		$base = rand($min, $max);
		$exp  = rand($min, $max);
		$rc[] = [ 'base'=>$base, 'exp'=>$exp ];
	}

	return $rc;
}


// Standardize numbers by converting to base-10 raised to an exponent
function convertToBase10 (array $powNumbers) : array
{
    foreach ($powNumbers as $k=>$v) {
        $powNumbers[$k]['base10Exp'] = $v['exp'] * log10($v['base']);
    }

    return $powNumbers;
}


// This is an adaptation of the /CountSort technique which uses a nested array
// structure to map overlapping base-10 exponents which have been cast to
// integers.
// Due to the number of elements (10000) this is technique is faster than native
// PHP quicksort.
function countSort(array $values) : array
{
	$counts = [];
	$min    = PHP_INT_MAX;
	$max    = PHP_INT_MIN;
	foreach ($values as $k=>$v) {
	    $key = (int)($v['base10Exp']);
		$counts[$key]['counts']      = isset($counts[$key]) ? $counts[$key]['counts'] + 1 : 1;
        $counts[$key]['origKeys'][]  = $k;
		if ($key < $min) {
			$min = $key;
		} elseif ($key > $max) {
			$max = $key;
		}
	}

	$sorted = [];
	for ($i=$min; $i<=$max; $i++) {
		if (isset($counts[$i])) {
		    if ($counts[$i]['counts'] == 1) {
		        // Default case, exact match/mapping
		        $sorted[] = $values[$counts[$i]['origKeys'][0]];
            } else {
		        // In case we have a collision (ie: multiple integer based-10 exponents
                // we need to perform another sort on the values in order to ensure that
                // we insert them in the correct order. Given the setup of this problem,
                // this should be an extremely rarely used code-path requiring the sort
                // of an extremely small (2 would be rare, 3 would be extremely rare, etc.)
                // set of values;
                $tValues = [];
                foreach ($counts[$i]['origKeys'] as $k) {
                    $tValues[] = $values[$k];
                }
                insertionSort ($tValues);
                foreach ($tValues as $v) {
                    $sorted[] = $v;
                }
            }
		}
	}

	return $sorted;
}


// Compare function for native PHP sort
function cmpBase10Exponent(array $a, array $b) : int
{
    return $a['base10Exp'] <=> $b['base10Exp'];
}


// We use insertion sort for sorting duplicate keys since it is
// faster when sorting a small set of elements.
// For the purpose of this exercise, duplicate *integer* base-10 exponents
// will be an extremely rare case which will almost never happen and if
// it happens, it will be a very short array to sort
function insertionSort (array &$array)
{
    $lim = count($array);
    for ($i=1; $i<$lim; $i++)
    {
        $item = $array[$i];
        $j = $i-1;

        while ($j >= 0 && $array[$j]['base10Exp'] > $item['base10Exp']) {
            $arr[$j+1] = $array[$j];
            $j--;
        }

        $arr[$j+1] = $item;
    }
}



///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////

$min   = 100;
$max   = 10000;
$qty   = 10000;
$nRuns = 1;

$powNumbers    = generateRandomArray($min, $max, $qty);
$powNumbersExp = convertToBase10 ($powNumbers);


// print first 3 entries for verification
print "//////////////////////////////////////////////////////////////////////\n";
print "//////////////////////////// SOURCE DATA /////////////////////////////\n";
print "//////////////////////////////////////////////////////////////////////\n";
var_dump (array_slice($powNumbersExp, 0, 3));


$starttime = microtime(true);
for ($i=0; $i<$nRuns; $i++) {
	$sorted = countSort($powNumbersExp);
}
$endtime = microtime(true);
$diff1 = $endtime - $starttime;

$starttime = microtime(true);
for ($i=0; $i<$nRuns; $i++) {
	$v = $powNumbersExp;
	usort($v, 'cmpBase10Exponent');
}
$endtime = microtime(true);
$diff2 = $endtime - $starttime;


// print first 3 sorted entries for verification
print "//////////////////////////////////////////////////////////////////////\n";
print "//////////////////////////// RESULT DATA /////////////////////////////\n";
print "//////////////////////////////////////////////////////////////////////\n";
var_dump (array_slice($v, 0, 3));


print "------- Performance Timings ----------\n";
print "Count Sort: $diff1 secs\n";
print "PHP Native Sort: $diff2 secs\n";

