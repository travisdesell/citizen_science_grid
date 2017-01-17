#!/usr/bin/env php

<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname(dirname($cwd[__FILE__]));

if ($argc != 2) {
    echo "Please include the filename of the IDX file to test.\n";
    exit();
}

// open the idx file
$idx_filename = $argv[1];
echo "Opening $idx_filename... ";
if (!($idx = fopen($idx_filename, 'rb'))) {
    echo "Failure!\n";
    exit();
}
echo "Success!\n";

$formats = array(
    0x08 => 1,
    0x09 => 1,
    0x0B => 2,
    0x0C => 4,
    0x0D => 4,
    0x0E => 8
);

// read in the header
$header = unpack('C4', fread($idx, 4));
if ($header[1] != 0x00 || $header[2] != 0x00) {
    echo "\tERROR: Incorrect header format.\n";
    fclose($idx);
    exit();
} 

// check the format
$format = $header[3];
if (!isset($formats[$format])) {
    echo "\tERROR: Unknown data type: $format.\n";
    fclose($idx);
    exit();
}
$format = $formats[$format];
echo "\tData size: $format\n";

// read in the variable count
$count = $header[4];
if ($count < 1) {
    echo "\tERROR: Unknown number of variables.\n";
    fclose($idx);
    exit();
}
echo "\tNumber of variables: $count\n";

// read in each variable
$vars = array();
$total = 1;
for ($i = 1; $i <= $count; ++$i) {
    $var = unpack('N', fread($idx, 4))[1];
    echo "\t\tVariable $i:\t$var\n";
    $vars[] = $var;
    $total *= $var;
}

// read in all the data
echo "\n";
for ($i = 0; $i < $total; ++$i) {
    if (feof($idx)) {
        echo "\tERROR: EOF before reading in all data.\n";
        fclose($idx);
        exit();
    }

    fread($idx, $format);
}

echo "COMPLETED!\n";
echo "\tData read: $total\n";
echo "\tEOF?: ";

// say if we're EOF
fread($idx, 1);
if (feof($idx))
    echo "YES";
else
    echo "NO";
echo "\n\n";

fclose($idx);
?>
