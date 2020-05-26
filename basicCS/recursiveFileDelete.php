<?php

// Recursively delete all files starting with "0aH". The underlying assumption
// here is that there are *a*lot* of files, so we'll be using the readdir
// functions which are faster than glob() or using RecursiveDirectoryIterators.
// So in this case we're choosing raw performance over simpler code.
//
// 2nd consideration: since we're concerned with being able to handle *a*lot*
// of files, we won't be storing any filenames but rather deleting any matches
// right away. As such, we're only returning the count/total of deleted files
// rather than a list of deleted filenames.
//
// 3rd consideration: the parameters given for this exercise state that there
// are a lot of files, it did not mention the possibility of having extremely *deeply*
// nested directory structures, as such a recursive approach should be OK.
// - If we have to deal with *deeply* nested directory structures (seems
//   improbable, but theoretically possible), we should opt for an iterative solution.
//
// Note: for the purpose of this exercise, we're not concerned about file permissions,
// thus no checks on whether we have permissions to create the test files or
// delete the actual files are performed.
//


function generateTestFiles (string $dirName, int $nFiles) : void
{
	@mkdir (__DIR__ . "/$dirName");
	for ($i=0; $i<$nFiles; $i++) {
		$fName = md5(rand(), false);
		if ($i%50 == 0) {
			$fName = '0aH' . $fName;
		}
		touch("$dirName/$fName");
	}
}


function deleteMatchingFiles (string $dirname, string $startStringOfFilename='0aH') : int
{
	$count = 0;

	if (is_dir(__DIR__ . "/$dirname")) {
		$dHandle = opendir(__DIR__ . "/$dirname");
	}

	if (!$dHandle) {
		// Throwing an exception here enables calling functions to know that
		// something broke, however, since file operations are not
		// transactional (ie: you can't rollback a file delete) there's not
		// much the calling code can do about this :-(
		throw new Exception("Unable to open directory $dirname");
	}

	while ($file = readdir($dHandle)) {
		if ($file == "." || $file == "..") {
			continue;
		}

		$fName = __DIR__ . "/$dirname/$file";
		if (is_dir($fName)) {
			$count += deleteMatchingFiles($fName, $startStringOfFilename);
		} elseif (strpos($file, $startStringOfFilename) === 0) {
			unlink($fName);
			$count++;
		}
	}

	closedir($dHandle);

	return $count;
}



///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////


$nFiles  = 100000;
$dirName = "TestDir";

generateTestFiles($dirName, $nFiles);
$count = deleteMatchingFiles($dirName);
print "Deleted $count files\n";

