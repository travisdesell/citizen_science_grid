#!/usr/bin/env php

<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname(dirname($cwd[__FILE__]));

require_once($cwd[__FILE__] . "/my_query.php");
require_once($cwd[__FILE__] . "/display_badges.php");
require_once($cwd[__FILE__] . "/user.php");

// all of the possible object.idx types
$types = array(
    'expert' => 'only expert observations',
    'citizen' => 'only citizen scientist observations',
    'both' => 'both expert and citizen scientist observations',
    'matched' => 'matched observations from citizen scientists'
);

/** Prints out the usage for the program and exits. */
function usage($what = null) {
    global $types;

    if ($what)
        echo "\n$what\n";

    echo "\nUsage: php generate_test_images.php -f file.json [--mosaic_size INT] [--type STRING] [-n] [--no_mosaic]\n";
    echo "\tfile.json        - the JSON file created by match_images.php\n";
    echo "\t-n / --no_mosaic - don't slice the mosaics into an idx file\n";
    echo "\t--mosaic_size    - size of the mosiacs to be generate (DEFAULT: 256)\n";
    echo "\t--type           - the type of object.idx file to generate (DEFAULT: both)\n";

    // print out the type options
    foreach ($types as $type => &$desc) {
        echo "\t\t$type\t- $desc\n";
    }

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

/** Gets the counts for a single user within bounds. */
function get_count_within_bounds(int $x, int $y, int $width, int $height, array &$boxes, array &$ret) {
    foreach ($boxes as &$box) {
        if ( $box->x >= $x && $box->y >= $y &&
            ($box->x + $box->width) <= ($x + $width) &&
            ($box->y + $box->height) <= ($y + $height))
        {
            $species_id = $box->species_id;
            if (isset($ret[$species_id]))
                $ret[$species_id]++;
            else
                $ret[$species_id] = 1;
        }
    }
}

/** Appends the main array with new counts. */
function append_counts(array &$counts, array &$newcounts) {
    foreach ($newcounts as $species => &$num) {
        if (isset($counts[$species]))
            $counts[$species] += $num;
        else
            $counts[$species] = $num;
    }
}

/** Gets the counts for all users within bounds. */
function get_users_count_within_bounds(int $x, int $y, int $width, int $height, array &$users, &$ret) {
    foreach ($users as &$user) {
        $count = array();
        get_count_within_bounds($x, $y, $width, $height, $user, $count);
        append_counts($ret, $count);
    }
}

$shortops = "f:n";

$longops = array(
    "mosaic_size:",
    "type:",
    "no_mosaic"
);

$options = getopt($shortops, $longops);
if (!$options || !isset($options['f'])) {
    usage();
}

$infile = $options['f'];

$mosaic_size = 256;
if (isset($options['mosaic_size'])) {
    $mosaic_size = (int)$options['mosaic_size'];
}
if ($mosaic_size < 32 || $mosaic_size > 2048) {
    usage("Mosaic size must be between 32 and 2048"); 
}

$type = 'both';
if (isset($options['type'])) {
    $type = strtolower((string)$options['type']);
}
if (!array_key_exists($type, $types)) {
    usage("Unknown type: '$type'");
}

// open our counts idx
$datetime = date('Ymdhis');
$counts_idx_filename = "${type}_${datetime}.idx";
$counts_idx = fopen($counts_idx_filename, 'wb');
if (!$counts_idx) {
    echo "Fatal error: Failed to open $counts_idx_filename!\n";
    exit();
}

$no_mosaic = false;
if (isset($options['n']) || isset($options['no_mosaic'])) {
    $no_mosaic = true;
}

$mosaics_json = json_decode(file_get_contents($infile)) or die('Unable to parse JSON file');

echo "running generate_test_images.php at " . date('Y/m/d h:i:s a') . "\n";
echo "\tJSON file: $infile\n";
echo "\tType: $type\n";
echo "\tMosaic size: $mosaic_size\n";
if ($no_mosaic) {
   echo "\tNo mosaic idx output.\n";
} else {
    $mosaic_idx_filename = "mosaic_$datetime.idx";
    $mosaic_idx = fopen($mosaic_idx_filename, 'wb');
    if ($mosaic_idx) {
        echo "\tMosaic IDX: $mosaic_idx_filename\n";
    } else {
        echo "\tUnable to open mosaic IDX: $mosaic_idx_filename\n";
        $no_mosaic = true;
    }
}

echo "\n";

$total_images = 0;
$mosaics = array();

foreach ($mosaics_json as $mosaic_id => &$mosaic) {
    // go through our pixels based on the mosaic size
    $width = $mosaic->width;
    $height = $mosaic->height;
 
    $last_col_width = $mosaic_size;
    $cols = (int)($width / $mosaic_size);
    if (($cols * $mosaic_size) < $width) {
        $last_col_width = $width - ($cols * $mosaic_size);
        $cols++;
    }

    $last_row_height = $mosaic_size;
    $rows = (int)($height / $mosaic_size);
    if (($rows * $mosaic_size) < $height) {
        $last_row_height = $height - ($rows * $mosaic_size);
        $rows++;
    }

    $mosaics[$mosaic_id] = array(
        'filename' => $mosaic->filename,
        'width' => $width,
        'height' => $height,
        'cols' => $cols,
        'rows' => $rows,
        'last_col_width' => $last_col_width,
        'last_row_height' => $last_row_height,
        'results' => $mosaic->results
    );

    $total_images += $cols * $rows;
}

// save the mosaic header 
if (!$no_mosaic) {
    write_idx_header(
        $mosaic_idx,
        4,
        array(
            $total_images,
            $mosaic_size,
            $mosaic_size,
            3
        )
    );
}

// save the counts header
write_idx_header(
    $counts_idx,
    2,
    array(
        $total_images,
        2
    )
);


echo "Total images: $total_images\n";

foreach ($mosaics as $mosaic_id => &$mosaic) {
    $imgfile = $mosaic['filename'];
    echo "$mosaic_id => $imgfile\n";

    // open our imgfile and determine all the x, y bounds for the split
    echo "\n\tOpening image... ";
    try {
        // only load into memory if we will be saving the image
        if ($no_mosaic) {
            $imagick = new Imagick();
            $imagick->pingImage($imgfile);
        } else {
            $imagick = new Imagick($imgfile);
        }

        $imagick->setFirstIterator();
        echo "Success!";
    } catch (ImagickException $e) {
        echo "Failure:\n\t\t$e";
        continue;
    }
    echo "\n";

    $width = $imagick->getImageWidth();
    $height = $imagick->getImageHeight();
    if ($width != $mosaic['width'] || $height != $mosaic['height']) {
        echo "\tERROR: Dimensions mismatch: (${mosaic['width']} x ${mosaic['height']}) vs ($width, $height)\n";
        $imagick->clear();
        continue;
    }
    
    $cols = $mosaic['cols'];
    $rows = $mosaic['rows'];
    $last_col_width = $mosaic['last_col_width'];
    $last_row_height = $mosaic['last_row_height'];

    echo "\t\tWidth x Height: $width x $height\n";
    echo "\t\tCols x Rows: $cols x $rows\n";
    echo "\t\tLast Col Size: $last_col_width\n";
    echo "\t\tLast Row Size: $last_row_height\n\n";

    $total_height = 0;
    $total_width = 0;
    for ($row = 0; $row < $rows; ++$row) {
        $y = $row * $mosaic_size;
        $row_height = $mosaic_size;
        if (($row + 1) == $rows)
            $row_height = $last_row_height;

        $total_width = 0;
        for ($col = 0; $col < $cols; ++$col) {
            $x = $col * $mosaic_size;
            $col_width = $mosaic_size;
            if (($col + 1) == $cols)
                $col_width = $last_col_width;

            // save the pixels in the mosaic.idx, unless told not to
            if (!$no_mosaic) {
                $areaIterator = $imagick->getPixelRegionIterator($x, $y, $col_width, $row_height);
                foreach ($areaIterator as $rowIterator) {
                    foreach ($rowIterator as $pixel) { 
                        // save the row pixel information
                        $color = $pixel->getColor();
                        fwrite($mosaic_idx, pack('CCC', $color['r'], $color['g'], $color['b']));
                    }

                    // fill the leftover with black
                    $leftover = $mosaic_size - $col_width;
                    for ($i = 0; $i < $leftover; ++$i) {
                        fwrite($mosaic_idx, pack('CCC', 0, 0, 0));
                    }
                }

                // clear the memory for the iterator
                $areaIterator->clear();

                // fill the leftover with black
                $leftover = $mosaic_size - $row_height;
                for ($i = 0; $i < $leftover; ++$i) {
                    for ($j = 0; $j < $mosaic_size; ++$j) {
                        fwrite($mosaic_idx, pack('CCC', 0, 0, 0));
                    }
                }
            }

            // get the counts within the bounds
            $counts = array();

            if ($type == 'expert' || $type == 'both') {
                get_users_count_within_bounds($x, $y, $col_width, $row_height, $mosaic['results']->expert_observations, $counts);
            }

            if ($type == 'citizen' || $type == 'both') {
                get_users_count_within_bounds($x, $y, $col_width, $row_height, $mosaic['results']->citizen_observations, $counts);
            }

            if ($type == 'matched') {
                echo "Matched supported coming soon.\n";
            }

            // write out the counts
            // white = 2, blue = 1000000
            $white = isset($counts['2']) ? $counts['2'] : 0;
            $blue = isset($counts['1000000']) ? $counts['1000000'] : 0;
            fwrite($counts_idx, pack('CC', $white, $blue));

            $total_width += $col_width;
        }

        $total_height += $row_height;
    }

    if ($total_width != $width || $total_height != $height)
        echo "\n\tWARNING: Size mismatch - ($total_width, $total_height) vs ($width, $height)\n";

    // free memory from the mosaic image
    $imagick->clear();
}

// close the mosaic file
if (!$no_mosaic) {
    fclose($mosaic_idx);
}

fclose($counts_idx);

?>
