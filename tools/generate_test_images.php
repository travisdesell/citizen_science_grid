#!/usr/bin/env php

<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname(dirname($cwd[__FILE__]));

require_once($cwd[__FILE__] . "/my_query.php");
require_once($cwd[__FILE__] . "/user.php");

/** Prints out the usage for the program and exits. */
function usage($what = null) {
    if ($what)
        echo "\n$what\n";

    echo "\nUsage: php generate_test_images.php -f file.json [--size INT] [--testing INT] [--background INT]\n\n";
    echo "\tfile.json     - the JSON file created by match_images.php\n";
    echo "\t--size        - size of the objects [DEFAULT: 20px]\n";
    echo "\t--testing     - percentage of testing images [DEFAULT: 20]\n";
    echo "\t--background  - percentage of background [DEFAULT: 80]\n";
    echo "\t--outdir / -o - output directory [DEFAULT: .]\n";

    exit();
}

/** Write out the IDX header with the given dimensions and values to the file. */
function write_idx_header(&$file, int $dimensions, array $values)
{
    // 0x00 first two bytes
    // 0x08 to signify unsigned byte values
    // number of dimension
    fwrite($file, pack("CCCC", 0x00, 0x00, 0x08, $dimensions));

    // write each of the values
    for ($i = 0; $i < $dimensions; ++$i) {
        fwrite($file, pack("N", $values[$i]));
    }
}

function _overlap(int $x, int $y, int $size, array &$arr)
{
    $x2 = $x + $size;
    $y2 = $y + $size;

    foreach ($arr as &$bg) {
        // these are all false, so we go to the next
        if ($x2 < $bg["x"]) continue;
        if ($x > ($bg["x"] + $bg["width"])) continue;
        if ($y2 < $bg["y"]) continue;
        if ($y > ($bg["y"] + $bg["height"])) continue;

        return true;
    }

    return false;
}

function overlap(int $x, int $y, int $size, array &$obs_arr, array &$bg_arr)
{
    return _overlap($x, $y, $size, $bg_arr) || _overlap($x, $y, $size, $obs_arr);
}

function shuffle_assoc(&$arr) {
    $keys = array_keys($arr);
    shuffle($keys);

    $newarr = array();
    foreach ($keys as $key) {
        $newarr[$key] = $arr[$key];
    }

    $arr = $newarr;
    return true;
}

function cleanup()
{
    global $mosaic_idx, $counts_idx, $counts_testing_idx, $mosaic_testing_idx;

    if (isset($counts_idx)) {
        fclose($counts_idx);
    }
    if (isset($mosaic_idx)) {
        fclose($mosaic_idx);
    }
    if (isset($counts_testing_idx)) {
        fclose($counts_testing_idx);
    }
    if (isset($mosaic_testing_idx)) {
        fclose($mosaic_testing_idx);
    }
}

/** Writes the region from the imagick to the idx */
function exportRegion(int $x, int $y, int $size, &$imagick, &$idx) {
    $areaIterator = $imagick->getPixelRegionIterator($x, $y, $size, $size);
    foreach ($areaIterator as $rowIterator) {
        foreach ($rowIterator as $pixel) { 
            // save the row pixel information
            $color = $pixel->getColor();
            fwrite($idx, pack('CCC', $color['r'], $color['g'], $color['b']));
        }
    }

    // clear the memory for the iterator
    $areaIterator->clear();
}

$shortops = "f:o:";
$longops = array(
    "size:",
    "testing:",
    "background:",
    "outdir:"
);

$options = getopt($shortops, $longops);
if (!$options || !isset($options['f'])) {
    usage();
}

$infile = $options['f'];

$outdir = null;
if (isset($options['o'])) {
    $outdir = $options['o'];
} else if (isset($options['outdir'])) {
    $outdir = $options['outdir'];
}

$size = 20;
if (isset($options['size'])) {
    $size = intval($options['size']);
}
if ($size < 10 || $size > 256) {
    usage("Mosaic size must be between 10 and 256"); 
}

$testing_percent = 20;
if (isset($options['testing'])) {
    $testing_percent = intval($options['testing']);
}
if ($testing_percent < 0) {
    $testing_percent = 0;
} else if ($testing_percent > 100) {
    $testing_percent = 100;
}

$background_percent = 20;
if (isset($options['background'])) {
    $background_percent = intval($options['background']);
}
if ($background_percent < 0) {
    $background_percent = 0;
} else if ($background_percent > 100) {
    $background_percent = 100;
}

$mosaics_json = json_decode(file_get_contents($infile)) or die("\nUnable to parse JSON file\n");

$datetime = date('Ymd');
echo "running generate_test_images.php at " . date('Y/m/d h:i:s a') . "\n";
echo "\tJSON file:  $infile\n";
echo "\tOutput Dir: $outdir\n";
echo "\tSize:       $size\n";
echo "\tTesting:    $testing_percent%\n";
echo "\tBackground: $background_percent%\n";

// open our counts.idx and mosaic.idx files
$counts_basename = "count_${size}px_${background_percent}percent_${datetime}";
$counts_idx_filename = "training_$counts_basename.idx";
if ($outdir) {
    $counts_idx_filename = "$outdir/$counts_idx_filename";
}
$counts_idx = fopen($counts_idx_filename, 'wb');
if (!$counts_idx) {
    echo "Fatal error: Failed to open $counts_idx_filename!\n";
    cleanup();
    exit();
}

$mosaic_basename = "mosaic_${size}px_${background_percent}percent_${datetime}";
$mosaic_idx_filename = "training_$mosaic_basename.idx";
if ($outdir) {
    $mosaic_idx_filename = "$outdir/$mosaic_idx_filename";
}
$mosaic_idx = fopen($mosaic_idx_filename, 'wb');
if (!$mosaic_idx) {
    echo "Fatal error: Failed to open $mosaic_idx_filename!\n";
    cleanup();
    exit();
}

if ($testing_percent > 0) {
    $counts_testing_idx_filename = "testing_$counts_basename.idx";
    if ($outdir) {
        $counts_testing_idx_filename = "$outdir/$counts_testing_idx_filename";
    }
    $counts_testing_idx = fopen($counts_testing_idx_filename, 'wb');
    if (!$counts_testing_idx) {
        echo "Fatal error: Failed to open $counts_testing_idx_filename!\n";
        cleanup();
        exit();
    }

    $mosaic_testing_idx_filename = "testing_$mosaic_basename.idx";
    if ($outdir) {
        $mosaic_testing_idx_filename = "$outdir/$mosaic_testing_idx_filename";
    }
    $mosaic_testing_idx = fopen($mosaic_testing_idx_filename, 'wb');
    if (!$mosaic_testing_idx) {
        echo "Fatal error: Failed to open $mosaic_testing_idx_filename!\n";
        cleanup();
        exit();
    }
}

echo "\tCounts IDX: $counts_idx_filename\n";
echo "\tMosaic IDX: $mosaic_idx_filename\n";
if ($testing_percent > 0) {
    echo "\tCounts Testing IDX: $counts_testing_idx_filename\n";
    echo "\tMosaic Testing IDX: $mosaic_testing_idx_filename\n";
}
echo "\n";

$testing_percent = $testing_percent / 100.0;
$bg_percent = $background_percent / 100.0;

$train_total = 0;
$test_total = 0;
$mosaics = array();

// first get the counts and locations
echo "\nGetting object and background locations and counts...\n";
foreach ($mosaics_json as $mosaic_filename => &$mosaic) {
    echo "\n$mosaic_filename:\n";
    if ($mosaic->results->expert_count < 100) {
        echo "\tNot enough expert observations (". $mosaic->results->expert_count ."). Skipping.\n";
        continue;
    }

    $width = $mosaic->width;
    $height = $mosaic->height;

    // get the counts
    $obs_count = intval($mosaic->results->expert_count);
    $bg_count = intval($obs_count * $bg_percent / (1 - $bg_percent));

    if ($testing_percent > 0) {
        $obs_test_count = intval(floor($obs_count * $testing_percent));
        $bg_test_count  = intval(floor($bg_count * $testing_percent));
    } else {
        $obs_test_count = 0;
        $bg_test_count  = 0; 
    }

    $obs_training_count = $obs_count - $obs_test_count;
    $bg_training_count  = $bg_count - $bg_test_count;

    $train_total += $obs_training_count + $bg_training_count;
    $test_total += $obs_test_count + $bg_test_count;

    // display some information
    echo "\tTraining observations: $obs_training_count\n";
    echo "\tTraining background:   $bg_training_count\n";
    echo "\tTesting observations:  $obs_test_count\n";
    echo "\tTesting background:    $bg_test_count\n";

    // get all our observations
    echo "\n\tGetting observations...\n";
    $obs_arr = array();
    foreach ($mosaic->results->expert_observations as $user_id => &$observations) {
        foreach ($observations as &$obs) {
            $obs_arr[] = array(
                "x" => $obs->x,
                "y" => $obs->y,
                "width" => $obs->width,
                "height" => $obs->height,
                "species_id" => $obs->species_id
            );
        }
    }
    echo "\t\tDone.\n";

    // randomly get bg locations
    echo "\tGetting background...\n\t\t";
    $bg_arr = array();
    $bg_step = intval($bg_count / 10);
    $bg_status = $bg_step;
    for ($i = 0; $i < $bg_count; ++$i) {
        do {
            $x = rand(0, $width - $size);
            $y = rand(0, $height - $size);
        } while (overlap($x, $y, $size, $obs_arr, $bg_arr));

        $bg_arr[] = array(
            "x" => $x,
            "y" => $y,
            "width" => $size,
            "height" => $size,
            "species_id" => 0
        );

        if ($i >= $bg_status) {
            echo ".";
            $bg_status += $bg_step;
        }
    }
    echo "\n\t\tDone.\n";

    // centralize our x / y for our $obs_arr
    foreach ($obs_arr as &$obs) {
        $x = $obs["x"] + intval(($obs["width"] + $size) / 2);
        if ($x < 0) {
            $x = 0;
        } else if ($x > ($width - $size)) {
            $x = $width - $size;
        }

        $y = $obs["y"] + intval(($obs["height"] + $size) / 2);
        if ($y < 0) {
            $y = 0;
        } else if ($y > ($height - $size)) {
            $y = $height - $size;
        }
    }

    $mosaic_arr = array(
        'filename' => $mosaic_filename,
        'width' => $width,
        'height' => $height
    );

    // shuffle for testing
    echo "\tShuffling are storing data...\n";
    if ($testing_percent > 0) {
        shuffle_assoc($obs_arr);
        shuffle_assoc($bg_arr);

        $mosaic_arr["obs_test_arr"]   = array_slice($obs_arr, 0, $obs_test_count, true);
        $mosaic_arr["bg_test_arr"]    = array_slice($bg_arr, 0, $bg_test_count, true);
        $mosaic_arr["obs_train_arr"]  = array_slice($obs_arr, $obs_test_count, $obs_training_count, true);
        $mosaic_arr["bg_train_arr"]   = array_slice($bg_arr, $bg_test_count, $bg_training_count, true); 
    } else {
        $mosaic_arr["obs_test_arr"]  = array();
        $mosaic_arr["bg_test_arr"]   = array();
        $mosaic_arr["obs_train_arr"] = $obs_arr;
        $mosaic_arr["bg_train_arr"]  = $bg_arr;
    }

    echo "\t\tObs Train:  ".count($mosaic_arr['obs_train_arr'])."\n";
    echo "\t\tBack Train: ".count($mosaic_arr['bg_train_arr'])."\n";
    if (count($mosaic_arr['obs_train_arr']) != $obs_training_count || count($mosaic_arr['bg_train_arr']) != $bg_training_count) {
        echo "\t\tERROR: Count mismatch! OBS: $obs_training_count || BG: $bg_training_count\n";
        continue;
    }

    if ($testing_percent > 0) {
        echo "\t\tObs Test:   ".count($mosaic_arr['obs_test_arr'])."\n";
        echo "\t\tBack Test:  ".count($mosaic_arr['bg_test_arr'])."\n";
        if (count($mosaic_arr['obs_test_arr']) != $obs_test_count || count($mosaic_arr['bg_test_arr']) != $bg_test_count) {
            echo "\t\tERROR: Count mismatch! OBS: $obs_test_count || BG: $bg_test_count\n";
            continue;
        }
    }

    $mosaics[$mosaic_filename] = $mosaic_arr;
    echo "\t\tDone.\n";
}
echo "\n";

// write our training headers
echo "Training objects: $train_total\n";
write_idx_header(
    $mosaic_idx,
    4,
    array(
        $train_total,
        $size,
        $size,
        3
    )
);
write_idx_header(
    $counts_idx,
    2,
    array(
        $train_total,
        2
    )
);

// write our testing headers
if ($testing_percent > 0) {
    echo "Testing objects: $test_total\n";
    write_idx_header(
        $mosaic_testing_idx,
        4,
        array(
            $test_total,
            $size,
            $size,
            3
        )
    );
    write_idx_header(
        $counts_testing_idx,
        2,
        array(
            $test_total,
            2
        )
    );
}

echo "\nSaving IDX files...\n";
foreach ($mosaics as $mosaic_filename => &$mosaic) {
    echo "\n$mosaic_filename\n";

    // open our imgfile and determine all the x, y bounds for the split
    echo "\tOpening image...\n";
    try {
        $imagick = new Imagick($mosaic_filename);
        $imagick->setFirstIterator();
    } catch (ImagickException $e) {
        echo "\t\tFailure:\n\t\t$e";
        continue;
    }

    // verify expected dimensions
    $width = $imagick->getImageWidth();
    $height = $imagick->getImageHeight();
    if ($width != $mosaic['width'] || $height != $mosaic['height']) {
        echo "\t\tERROR: Dimensions mismatch: (${mosaic['width']} x ${mosaic['height']}) vs ($width, $height)\n";
        $imagick->clear();
        continue;
    }
    
    echo "\t\tWidth x Height: $width x $height\n";
    echo "\t\tDone.\n";

    // training data is always added
    echo "\tExporting training data...\n";
    foreach ($mosaic['obs_train_arr'] as &$obs) {
        exportRegion($x, $y, $size, $imagick, $mosaic_idx);
        $white = $obs['species_id'] == 2 ? 1 : 0;
        $blue  = $obs['species_id'] == 1000000 ? 1 : 0;
        fwrite($counts_idx, pack('CC', $white, $blue));
    }

    foreach ($mosaic['bg_train_arr'] as &$bg) {
        exportRegion($x, $y, $size, $imagick, $mosaic_idx);
        fwrite($counts_idx, pack('CC', 0, 0));
    }
    echo "\t\tDone.\n";

    // testing data is optional
    if ($testing_percent > 0) {
        echo "\tExporting testing data...\n";
        foreach ($mosaic['obs_test_arr'] as &$obs) {
            exportRegion($x, $y, $size, $imagick, $mosaic_testing_idx);
            $white = $obs['species_id'] == 2 ? 1 : 0;
            $blue  = $obs['species_id'] == 1000000 ? 1 : 0;
            fwrite($counts_testing_idx, pack('CC', $white, $blue));
        }

        foreach ($mosaic['bg_test_arr'] as &$bg) {
            exportRegion($x, $y, $size, $imagick, $mosaic_testing_idx);
            fwrite($counts_testing_idx, pack('CC', 0, 0));
        }
        echo "\t\tDone.\n";
    }

    // free memory from the mosaic image
    $imagick->clear();
}

cleanup();

?>
