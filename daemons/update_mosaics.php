#!/usr/bin/env php

<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname(dirname($cwd[__FILE__]));

require_once($cwd[__FILE__] . "/my_query.php");
require_once($cwd[__FILE__] . "/user.php");

$mosaic_list = query_wildlife_video_db("SELECT id, filename FROM mosaic_images ORDER BY id ASC");
$imagick = new Imagick();
while (($mosaic = $mosaic_list->fetch_assoc()) != null) {
    $filename = $mosaic['filename'];
    $id = $mosaic['id'];
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

    echo "\n[$id] :: $filename\n";
    echo "$width x $height \n";

    if (!query_wildlife_video_db("UPDATE mosaic_images SET width = $width, height = $height WHERE id = $id")) {
        echo "\tFailure!\n";
    } else {
        echo "\tSuccess!\n";
    }
    
    // free memory from the mosaic image
    $imagick->clear();
}
?>
