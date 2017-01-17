#!/usr/bin/env php

<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname(dirname($cwd[__FILE__]));

require_once($cwd[__FILE__] . "/my_query.php");
require_once($cwd[__FILE__] . "/display_badges.php");
require_once($cwd[__FILE__] . "/user.php");

//echo "running match_images.php at " . date('Y/m/d h:i:s a') . "\n";

function get_observations(int $image_id, array &$ret_array, bool $is_expert, int $x_offset, int $y_offset) {
    // get all the observations
    $count = 0;
    $io_list = query_wildlife_video_db("SELECT id, user_id FROM image_observations WHERE image_id=$image_id AND is_expert=" . (int)$is_expert);
    while (($io = $io_list->fetch_assoc()) != null) {
        $io_id = $io['id'];
        $user_id = $io['user_id'];

        // get a list of all the observation boxes for the specific observation
        $iob_list = query_wildlife_video_db("SELECT * FROM image_observation_boxes WHERE image_observation_id=$io_id");
        $iob_boxes = array();
        while (($iob = $iob_list->fetch_assoc()) != null) {
            $iob_boxes[] = array(
                'user_id' => $user_id,
                'image_observation_id' => $io_id,
                'image_observation_box_id' => $iob['id'],
                'species_id' => $iob['species_id'],
                'x' => ($iob['x'] + $x_offset),
                'y' => ($iob['y'] + $y_offset),
                'width' => $iob['width'],
                'height' => $iob['height'],
                'on_nest' => $iob['on_nest']
            );

            $count++; 
        }

        // only add the boxes if we have any at all
        if ($iob_boxes) {
            if (!isset($ret_array[$user_id]))
                $ret_array[$user_id] = array();
            $ret_array[$user_id] = $iob_boxes;
        }
    }

    return $count;
}

$results = array();

// get the list of mosaics
$mosaic_list = query_wildlife_video_db("SELECT DISTINCT mosaic_image_id, filename, mi.width, mi.height FROM mosaic_images AS mi INNER JOIN mosaic_split_images AS msi on msi.mosaic_image_id = mi.id INNER JOIN images AS i on msi.image_id = i.id WHERE i.views > 0 OR i.expert_views > 0");
while (($mosaic = $mosaic_list->fetch_assoc()) != null) {
    $mosaic_id = $mosaic['mosaic_image_id'];
    $mosaic_filename = $mosaic['filename'];
    $mosaic_width = $mosaic['width'];
    $mosaic_height = $mosaic['height'];
    $mosaic_results = array(
        'expert_count' => 0,
        'expert_views' => 0,
        'expert_observations' => array(),

        'citizen_count' => 0,
        'citizen_views' => 0,
        'citizen_observations' => array(),

        'matched_count' => 0,
        'matched_views' => 0,
        'matched_observations' => array()
    );

    // go through each image in the mosaic
    $image_list = query_wildlife_video_db("SELECT image_id, x, y FROM mosaic_split_images WHERE mosaic_image_id=$mosaic_id");
    while (($image = $image_list->fetch_assoc()) != null) {
        $image_id = $image['image_id'];
        $x_offset = $image['x'];
        $y_offset = $image['y'];

        // get all the expert observations
        $expert_observations = array();
        $mosaic_results['expert_count'] += get_observations($image_id, $expert_observations, true, $x_offset, $y_offset);

        // get all the non-expert observations
        $observations = array();
        $mosaic_results['citizen_count'] += get_observations($image_id, $observations, false, $x_offset, $y_offset);

        // no need to add this if there are no true observations
        foreach ($expert_observations as &$val)
            $mosaic_results['expert_observations'][] = $val;

        foreach ($observations as &$val)
            $mosaic_results['citizen_observations'][] = $val;

        // match up the observations
    }

    if ($mosaic_results['expert_count'] || $mosaic_results['citizen_count']) {
        $mosaic_results['expert_views'] = count($mosaic_results['expert_observations']);
        $mosaic_results['citizen_views'] = count($mosaic_results['citizen_observations']);
        $results[$mosaic_id] = array(
            "filename" => $mosaic_filename,
            "width" => $mosaic_width,
            "height" => $mosaic_height,
            "results" => $mosaic_results
        );
    }
}

echo json_encode($results);

?>
