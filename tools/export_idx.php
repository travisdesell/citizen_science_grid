#!/usr/bin/env php

<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname($cwd[__FILE__]);

require_once($cwd[__FILE__] . '/idx.php');

if ($argc < 3) {
    echo "Please include the output filename and the filename(s) of the IDX files.\n";
    exit();
}

$imgfile = $argv[1];

// open the idx file
$args = array_slice($argv, 2);
$idxarr = array();
$ixcount = count($args);
foreach ($args as $idx_filename) {
    echo "\n";
    $idxarr[] = new IDXReader($idx_filename);
}

// get the total area needed for the image (assume all the same size for now)
$ecount = $idxarr[0]->count();
$ewidth = $idxarr[0]->getElementWidth();
$eheight = $idxarr[0]->getElementHeight();
foreach (array_slice($idxarr, 1) as &$idx) {
    if ($ewidth != $idx->getElementWidth() || $eheight != $idx->getElementHeight()) {
        "\tDifferent dimensions. Skipping.\n";
        continue;
    }

    $ecount += $idx->count();
}

$width = $ecount * $ewidth;
$height = $ecount * $eheight;

// create our image
echo "\nCreating new image: $imgfile\n";
echo "\tCount: $ecount\n";
echo "\tDims: $width x $height\n";

$img = new Imagick();
$img->newImage($width, $height, new ImagickPixel('black'));
$img->setImageFormat('png');

$count = 0;
$col = 0;
$lastrow = -1;

echo "\nIterating through IDX files...\n";
foreach ($idxarr as &$idx) {
    foreach ($idx as $objs) {
        // determine the new width / height
        $row = intval(floor($count / $ecount));
        if ($row != $lastrow) {
            $col = 0;
            $lastrow = $row;
            echo "\tMoving to new row: $row\n";
        } else {
            $col++;
        }

        $i = 0;
        $areaIterator = $img->getPixelRegionIterator($col, $row, $ewidth, $eheight);
        foreach ($areaIterator as $rowIterator) {
            foreach ($rowIterator as $pixel) {
                $pixel->setColor("rgba(${objs[$i]}, ${objs[$i+1]}, ${objs[$i+2]}, 0)");
                $i += 3;
            }

            $areaIterator->syncIterator();
        }

        $count++;
    }
}

echo "\nWriting file to: $imgfile\n";
$img->writeImage($imgfile);

?>
