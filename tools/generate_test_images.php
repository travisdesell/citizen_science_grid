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
    echo "\tfile.json      - the JSON file created by match_images.php\n";
    echo "\t--size         - size of the objects [DEFAULT: 20px]\n";
    echo "\t--testing      - percentage of testing images [DEFAULT: 20]\n";
    echo "\t--background   - percentage of background [DEFAULT: 80]\n";
    echo "\t--outdir / -o  - output directory [DEFAULT: .]\n";
    echo "\t--matched / -m - add in the matched data [DEFAULT: OFF]\n";
    echo "\t--citizen / -c - add in all citizen data [DEFAULT: OFF]\n";
    echo "\t--expert / -e  - add in expert data [DEFAULT: ON]\n";
    echo "\t--rotate / -r  - randomly rotate the given amount of times\n";
    echo "\t--noblack / -b - limits the black from the background\n";
    echo "\t--year / -y    - year to generate for\n";
    echo "\t--shift / -s   - correct for blueshift [DEFAULT: ON for 2015]\n";

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

/** Writes the region from the imagick to the idx 
 * @return true if the region has color, false otherwise 
 */
function exportRegion(int $x, int $y, int $size, &$imagick, &$idx, $shift) {
    $areaIterator = $imagick->getPixelRegionIterator($x, $y, $size, $size);
    $hascolor = false;

    $rshift = 233.0 / 150.0;
    $gshift = 255.0 / 189.0;
    $bshift = 236.0 / 190.0;

    foreach ($areaIterator as $rowIterator) {
        foreach ($rowIterator as $pixel) { 
            // save the row pixel information
            $color = $pixel->getColor();
            $r = $color['r'];
            $g = $color['g'];
            $b = $color['b'];

            if ($shift) {
                $r = intval($r * $rshift);
                $g = intval($g * $gshift);
                $b = intval($b * $bshift);

                if ($r > 255) $r = 255;
                if ($g > 255) $g = 255;
                if ($b > 255) $b = 255;
            }

            fwrite($idx, pack('CCC', $r, $g, $b));

            if (!$hascolor) {
                $hascolor = $r > 0 || $g > 0 || $b > 0;
            }
        }
    }

    // clear the memory for the iterator
    $areaIterator->clear();
    return $hascolor;
}

$shortops = "f:o:mcer:by:s";
$longops = array(
    "size:",
    "testing:",
    "background:",
    "outdir:",
    "matched",
    "citizen",
    "expert",
    "rotate:",
    "noblack",
    "year:",
    "shift"
);

$options = getopt($shortops, $longops);
if (!$options || !isset($options['f'])) {
    usage();
}

$infile = $options['f'];

$citizen = false;
$matched = false;

if (isset($options['c']) || isset($options['citizen'])) {
    $citizen = true;
}
if (!$citizen && (isset($options['m']) || isset($options['matched']))) {
    $matched = true;
}

$expert = !$citizen && !$matched;
if (isset($options['e']) || isset($options['expert'])) {
    $expert = true;
}

$noblack = false;
if (isset($options['b']) || isset($options['noblack'])) {
    $noblack = true;
}

$year = 2015;
if (isset($options['y'])) {
    $year = intval($options['y']);
} else if (isset($options['year'])) {
    $year = intval($options['year']);
}

$shifted = $year == 2015;
if (isset($options['s']) || isset($options['shift'])) {
    $shifted = true;
}


$outdir = null;
if (isset($options['o'])) {
    $outdir = $options['o'];
} else if (isset($options['outdir'])) {
    $outdir = $options['outdir'];
}

$rotate = 0;
if (isset($options['r'])) {
    $rotate = intval($options['r']);
} else if (isset($options['rotate'])) {
    $rotate = intval($options['rotate']);
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

$background_percent = 80;
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
echo "\tExpert:     $expert\n";
echo "\tCitizen:    $citizen\n";
echo "\tMatched:    $matched\n";
echo "\tYear:       $year\n";
echo "\tShifting?:  $shifted\n";
echo "\tRotations:  $rotate [NOT YET IMPLEMENTED]\n";
echo "\tNo Black?:  $noblack [NOT YET IMPLEMENTED]\n";

$basename = "${size}px_${background_percent}percent";
if ($expert) {
    $basename .= "_expert";
}
if ($citizen) {
    $basename .= "_citizen";
}
if ($matched) {
    $basename .= "_matched";
}
if ($rotate > 0) {
    $basename .= "_${rotate}rot";
}
if ($shifted) {
    $basename .= "_shifted";
}
$basename .= "_${year}_$datetime";

// open our counts.idx and mosaic.idx files
$counts_basename = "count_$basename";
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

$mosaic_basename = "mosaic_$basename";
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

    // get the mosaic year
    $result = query_wildlife_video_db("SELECT year FROM mosaic_images WHERE filename='$mosaic_filename' LIMIT 1");
    $row = $result->fetch_assoc();
    $mosaic_year = intval($row['year']);
    if ($mosaic_year != $year) {
        echo "\tWrong year, $mosaic_year vs $year. Skipping.\n";
        continue;
    }

    $width = $mosaic->width;
    $height = $mosaic->height;

    // get the counts
    $obs_count = 0;
    if ($expert) {
        $obs_count += intval($mosaic->results->expert_count);
    }
    if ($citizen) {
        $obs_count += intval($mosaic->results->citizen_count);
    }
    if ($matched) {
        $obs_count += intval($mosaic->results->matched_count);
    }

    if ($obs_count < 100) {
        echo "\tNot enough observations ($obs_count). Skipping.\n";
        continue;
    }

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

    $obs_arrays = array();
    if ($expert) {
        $obs_arrays[] = $mosaic->results->expert_observations;
    }
    if ($citizen) {
        $obs_arrays[] = $mosaic->results->citizen_observations;
    }
    if ($matched) {
        $obs_arrays[] = array("0" => $mosaic->results->matched_observations);
    }

    foreach ($obs_arrays as &$obs_array) {
        foreach ($obs_array as $user_id => &$observations) {
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
        if (!exportRegion($obs['x'], $obs['y'], $size, $imagick, $mosaic_idx, $shifted)) {
            fwrite(STDERR, "$mosaic_filename (${obs['x']}, ${obs['y']}) is all black\n");
        }
        $white = $obs['species_id'] == 2 ? 1 : 0;
        $blue  = $obs['species_id'] == 1000000 ? 1 : 0;
        fwrite($counts_idx, pack('CC', $white, $blue));
    }

    foreach ($mosaic['bg_train_arr'] as &$bg) {
        exportRegion($bg['x'], $bg['y'], $size, $imagick, $mosaic_idx, $shifted);
        fwrite($counts_idx, pack('CC', 0, 0));
    }
    echo "\t\tDone.\n";

    // testing data is optional
    if ($testing_percent > 0) {
        echo "\tExporting testing data...\n";
        foreach ($mosaic['obs_test_arr'] as &$obs) {
            if (!exportRegion($obs['x'], $obs['y'], $size, $imagick, $mosaic_testing_idx, $shifted)) {
                fwrite(STDERR, "$mosaic_filename (${obs['x']}, ${obs['y']}) is all black\n");
            }
            $white = $obs['species_id'] == 2 ? 1 : 0;
            $blue  = $obs['species_id'] == 1000000 ? 1 : 0;
            fwrite($counts_testing_idx, pack('CC', $white, $blue));
        }

        foreach ($mosaic['bg_test_arr'] as &$bg) {
            exportRegion($bg['x'], $bg['y'], $size, $imagick, $mosaic_testing_idx, $shifted);
            fwrite($counts_testing_idx, pack('CC', 0, 0));
        }
        echo "\t\tDone.\n";
    }

    // free memory from the mosaic image
    $imagick->clear();
}

cleanup();

?>
