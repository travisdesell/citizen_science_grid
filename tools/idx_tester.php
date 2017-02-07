#!/usr/bin/env php

<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname($cwd[__FILE__]);

require_once($cwd[__FILE__] . '/idx.php');

if ($argc < 2) {
    echo "Please include the filename of the IDX file to test.\n";
    exit();
}

// open the idx file
$args = array_slice($argv, 1);
foreach ($args as $idx_filename) {
    echo "\n";
    $idx = IDX::fromFile($idx_filename);
}

?>
