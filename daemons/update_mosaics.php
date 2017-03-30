#!/usr/bin/env php

<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname(dirname($cwd[__FILE__]));

require_once($cwd[__FILE__] . "/my_query.php");
require_once($cwd[__FILE__] . "/user.php");

$mosaic_list = query_wildlife_video_db("SELECT id, filename, project_id, year FROM mosaic_images ORDER BY id ASC");
$imagick = new Imagick();
while (($mosaic = $mosaic_list->fetch_assoc()) != null) {
    $filename = $mosaic['filename'];
    $id = $mosaic['id'];
    $project_id = $mosaic['project_id'];
    $year = $mosaic['year'];
    try {
        //$imagick = new Imagick($imgfile);
        $imagick->pingImage($filename);
        $imagick->setFirstIterator();
    } catch (ImagickException $e) {
        echo "\nexception opening $filename: $e\n";
        continue;
    }

    $width = $imagick->getImageWidth();
    $height = $imagick->getImageHeight();

    // free memory from the mosaic image
    $imagick->clear();

    echo "\n[$id] :: $filename\n";
    echo "$width x $height \n";

    if (!query_wildlife_video_db("UPDATE mosaic_images SET width = $width, height = $height WHERE id = $id")) {
        echo "\tFailure!\n";
    } else {
        echo "\tSuccess!\n";
    }

    // set the correct project and year for each individual image
    echo "\tUpdating individual images...\n";
    $image_list = query_wildlife_video_db("SELECT image_id FROM mosaic_split_images WHERE mosaic_image_id=$id");
    while (($image = $image_list->fetch_assoc()) != null) {
        $image_id = $image['image_id'];
        if (!query_wildlife_video_db("UPDATE images SET project_id=$project_id, year=$year WHERE id=$image_id")) {
            echo "\t\t$image_id ERROR\n";
        }
    } 
    echo "\tDone.\n";
}
?>
