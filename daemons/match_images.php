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

            ++$count; 
        }

        // only add the boxes if we have any at all
        if ($iob_boxes) {
            if (!isset($ret_array[$user_id]))
                $ret_array[$user_id] = array();
            $ret_array[$user_id][] = $iob_boxes;
        }
    }

    return $count;
}

function pythag(int $x1, int $y1, int $x2, int $y2)
{
    $x = ($x1 - $x2);
    $y = ($y1 - $y2);

    return sqrt($x*$x + $y*$y);
}

/** Says if two entries are matched using the corner-pixel method. */
function is_matched(array &$a1, array &$a2, int $dist = 10)
{
    $x11 = $a1['x']; $x12 = $a1['x'] + $a1['width'];
    $x21 = $a2['x']; $x22 = $a2['x'] + $a2['width'];
    $y11 = $a1['y']; $y12 = $a1['y'] + $a1['height'];
    $y21 = $a2['y']; $y22 = $a2['y'] + $a2['height'];

    return (
        ($a1['species_id'] == $a2['species_id'])    &&
        (pythag($x11, $y11, $x21, $y21) <= $dist)   &&
        (pythag($x12, $y11, $x22, $y21) <= $dist)   &&
        (pythag($x12, $y12, $x22, $y22) <= $dist)   &&
        (pythag($x11, $y12, $x21, $y22) <= $dist)
    );
}

function matched_observation(array &$matches)
{
    $ids = array(
        $matches[0]['user_id'] => array(
            'image_observation_id' => $matches[0]['image_observation_id'],
            'image_observation_box_id' => $matches[0]['image_observation_box_id']
        )
    );

    $species_id = $matches[0]['species_id'];
    $x1 = $matches[0]['x'];
    $x2 = $x1 + $matches[0]['width'];
    $y1 = $matches[0]['y'];
    $y2 = $y1 + $matches[0]['height'];
    $on_nest = $matches[0]['on_nest'];

    for ($i = 1; $i < count($matches); ++$i) {
        $obs = $matches[$i];
        if ($obs['x'] > $x1)
            $x1 = $obs['x'];
        if (($obs['x'] + $obs['width']) < $x2)
            $x2 = $obs['x'] + $obs['width'];
        if ($obs['y'] > $y1)
            $y1 = $obs['y'];
        if (($obs['y'] + $obs['height']) < $y2)
            $y2 = $obs['y'] + $obs['height'];

        $on_nest += $obs['on_nest'];
        $ids[$obs['user_id']] = array(
            'image_observation_id' => $obs['image_observation_id'],
            'image_observation_box_id' => $obs['image_observation_box_id']
        );
    }

    $on_nest /= count($matches);
    return array(
        'count'         => count($matches),
        'ids'           => $ids,
        'species_id'    => $species_id,
        'x'             => $x1,
        'y'             => $y1,
        'width'         => $x2 - $x1,
        'height'        => $y2 - $y1,
        'on_nest'       => $on_nest
    );
}

$results = array();

$mosaic_list = query_wildlife_video_db("SELECT DISTINCT filename FROM mosaic_images");
while (($mosaic_row = $mosaic_list->fetch_row()) != null) {
    $mosaic_filename = $mosaic_row[0];
    $mosaic_width = 0;
    $mosaic_height = 0;
    $mosaic_ids = array();
    $mosaic_id_list = query_wildlife_video_db("SELECT DISTINCT mosaic_image_id, mi.width, mi.height FROM mosaic_images AS mi INNER JOIN mosaic_split_images AS msi on msi.mosaic_image_id = mi.id INNER JOIN images AS i on msi.image_id = i.id WHERE mi.filename = '$mosaic_filename' AND (i.views > 0 OR i.expert_views > 0)");

    $mosaic_results = array(
        'expert_count' => 0,
        'expert_views' => 0,
        'expert_observations' => array(),

        'citizen_count' => 0,
        'citizen_views' => 0,
        'citizen_observations' => array(),

        'matched_count' => 0,
        'matched_observations' => array(),

        'expert_matched_count' => 0,
        'expert_matched_observations' => array(),

        'expert_composite_matched_count' => 0,
        'expert_composite_matched_observations' => array()
    );

    while (($mosaic = $mosaic_id_list->fetch_assoc()) != null) {
        $mosaic_id = $mosaic['mosaic_image_id'];
        $mosaic_width = $mosaic['width'];
        $mosaic_height = $mosaic['height'];

        $mosaic_ids[] = $mosaic_id;

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
            foreach ($expert_observations as $id => &$obs) {
                if (!isset($mosaic_results['expert_observations'][$id]))
                    $mosaic_results['expert_observations'][$id] = array();

                foreach ($obs as &$ob) {
                    foreach ($ob as &$o) {
                        $mosaic_results['expert_observations'][$id][] = $o;
                    }
                }
            }

            // no need to add this if there are no true observations
            foreach ($observations as $id => &$obs) {
                if (!isset($mosaic_results['citizen_observations'][$id]))
                    $mosaic_results['citizen_observations'][$id] = array();

                foreach ($obs as &$ob) {
                    foreach ($ob as &$o) {
                        $mosaic_results['citizen_observations'][$id][] = $o;
                    }
                }
            }
        }
    }

    $citizen_matches = array();
    $citizen_is_matched = array();
    foreach ($mosaic_results['citizen_observations'] as $id1 => &$c1) {
        $id1 = intval($id1);

        foreach ($c1 as &$obs1) {
            if (in_array($obs1['image_observation_box_id'], $citizen_is_matched))
                continue;

            $matches = array();

            // go through the rest of the array to find matches
            foreach ($mosaic_results['citizen_observations'] as $id2 => &$c2) {
                $id2 = intval($id2);
                if ($id1 == $id2)
                    continue;

                foreach ($c2 as &$obs2) {
                    if (is_matched($obs1, $obs2)) {
                        $matches[] = $obs2;
                        if (!in_array($obs2['image_observation_box_id'], $citizen_is_matched))
                            $citizen_is_matched[] = $obs2['image_observation_box_id'];
                    }
                }
            }

            // get the agreed upon bounding area
            if ($matches) {
                $citizen_is_matched[] = $obs1;
                $matches[] = $obs1;
                $citizen_matches[] = matched_observation($matches);
            }
        }
    }

    if ($citizen_matches) {
        $mosaic_results['matched_count'] = count($citizen_matches);
        $mosaic_results['matched_observations'] = $citizen_matches;
    }

    // match up the expert observations with citizens
    // and citizen matched observations
    $expert_matches = array();
    $expert_composite_matches = array();
    $expert_self_matches = array();
    $expert_self_matches_count = array();
    $expert_self_matches_found = array();
    foreach ($mosaic_results['expert_observations'] as $id => &$e) {
        $id = intval($id);

        foreach ($e as &$eobs) {

            // match up individual citizens
            foreach ($mosaic_results['citizen_observations'] as $id2 => &$c) {
                foreach ($c as &$cobs) {
                    if (is_matched($eobs, $cobs)) {
                        $expert_matches[] = array(
                            'expert'    => $eobs,
                            'citizen'   => $cobs
                        );
                    }
                }
            }

            // match up composites
            foreach ($mosaic_results['matched_observations'] as &$cobs) {
                if (is_matched($eobs, $cobs)) {
                    $expert_composite_matches[] = array(
                        'expert'    => $eobs,
                        'composite' => $cobs
                    );
                }
            }

            // match up experts to get a true count
            if (!in_array($eobs['image_observation_box_id'], $expert_self_matches_found)) {
                $matches = array();

                foreach ($mosaic_results['expert_observations'] as $id2 => &$e2) {
                    $id2 = intval($id2);
                    if ($id2 == $id) {
                        continue;
                    }

                    foreach ($e2 as &$eobs2) {
                        if (in_array($eobs2['image_observation_box_id'], $expert_self_matches_found) || !is_matched($eobs, $eobs2)) {
                            continue;
                        }

                        $matches[] = $eobs2;
                        $expert_self_matches_found[] = $eobs2['image_observation_box_id'];
                    }
                }

                if (count($matches)) {
                    $expert_self_matches_found[] = $eobs['image_observation_box_id'];
                    $matches[] = $eobs;
                    $expert_self_matches[] = matched_observation($matches);

                    $count = count($matches);
                    if (!isset($expert_self_matches_count[$count]))
                        $expert_self_matches_count[$count] = 0;
                    $expert_self_matches_count[$count] += 1;
                }
            }
        }
    }

    $expert_potential_matches = $mosaic_results['expert_count'];
    foreach ($expert_self_matches_count as $count => &$num) {
        $expert_potential_matches += $num * ($count - 1);
    }

    if ($expert_matches) {
        $mosaic_results['expert_matched_count'] = count($expert_matches);
        $mosaic_results['expert_matched_observations'] = $expert_matches;
        $mosaic_results['expert_potential_matches'] = $expert_potential_matches;
        $mosaic_results['expert_self_matches'] = $expert_self_matches;
    }

    if ($expert_composite_matches) {
        $mosaic_results['expert_composite_matched_count'] = count($expert_composite_matches);
        $mosaic_results['expert_composite_matched_observations'] = $expert_composite_matches;
    }

    if ($mosaic_results['expert_count'] || $mosaic_results['citizen_count']) {
        $mosaic_results['expert_views'] = count($mosaic_results['expert_observations']);
        $mosaic_results['citizen_views'] = count($mosaic_results['citizen_observations']);
        $results[$mosaic_filename] = array(
            "width" => $mosaic_width,
            "height" => $mosaic_height,
            "ids" => $mosaic_ids,
            "results" => $mosaic_results
        );
    }
}

echo json_encode($results);

?>
