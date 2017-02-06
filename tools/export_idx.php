#!/usr/bin/env php

<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname($cwd[__FILE__]);

require_once($cwd[__FILE__] . '/idx.php');

if ($argc != 4) {
    echo "Include the filename for the counts and mosaic IDX files\n";
    exit();
}

// open our counts and images files
echo "\nOpening IDX files...\n";
$counts = IDX::fromFile($argv[1]);
$images = IDX::fromFile($argv[2]);

// make sure they are the same length
if ($counts->count() != $images->count()) {
    echo "\tDifferent counts, please verify.\n";
    exit();
}

echo "\tDone.\n";

$outdir = $argv[3];
echo "\nSaving images to: $outdir\n";

// create our class folders
$classes = $counts->dimCount(0);
$classCounts = array();
for ($i = 0; $i <= $classes; ++$i) {
    if (!mkdir("$outdir/$i", 0777, true)) {
        echo "\tError creating directories.\n";
        exit();
    }

    $classCounts[] = 0;
}

$w = $images->dimCount(0);
$h = $images->dimCount(1);

for ($i = 0; $i < $counts->count(); ++$i) {
    $count = $counts[$i];
    $image = $images[$i];

    // create the image
    $img = new Imagick();
    $img->newImage($w, $h, new ImagickPixel('black'));
    $img->setImageFormat('png');
    $imageIterator = $img->getPixelIterator();

    // fill the image pixel-by-pixel
    $j = 0;
    foreach ($imageIterator as $row => $pixels) {
        foreach ($pixels as $column => $pixel) {
            $pixel->setColor("rgba(".$image[$j].", ".$image[$j+1].", ".$image[$j+2].", 0)");
            $j += 3;
        }

        $imageIterator->syncIterator();
    }

    // determine the class
    $class = 0;
    for ($j = 0; $j < count($count); ++$j) {
        if ($count[$j] > 0) {
            $class = $j + 1;
        }
    }

    // save the file
    $img->writeImage("$outdir/$class/".$classCounts[$class].".png");
    $img->clear();

    ++$classCounts[$class];
}

echo "\tDone.";

?>
