#!/usr/bin/env php

<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname(dirname($cwd[__FILE__]));

require_once($cwd[__FILE__] . "/my_query.php");
require_once($cwd[__FILE__] . "/display_badges.php");
require_once($cwd[__FILE__] . "/user.php");

echo "running update_images.php at " . date('Y/m/d h:i:s a') . "\n";

// get a list of all the users who have submitted
$user_list = query_wildlife_video_db("SELECT DISTINCT user_id FROM image_observations ORDER BY user_id ASC");
if (!$user_list) {
    exit;
}

// for each user, update their info
while (($current_user = $user_list->fetch_row()) != null) {
    $user_id = $current_user[0];

    $total_reviewed = query_wildlife_video_db("SELECT COUNT(*) FROM image_observations WHERE user_id=$user_id");
    $total_observations = query_wildlife_video_db("SELECT COUNT(*) FROM image_observations AS io INNER JOIN image_observation_boxes AS iob ON iob.image_observation_id = io.id WHERE io.user_id=$user_id");

    if (!$total_reviewed) {
        $total_reviewed = 0;
    } else {
        $total_reviewed = $total_reviewed->fetch_row();
        $total_reviewed = $total_reviewed[0];
    }

    if (!$total_observations) {
        $total_observations = 0;
    } else {
        $total_observations = $total_observations->fetch_row();
        $total_observations = $total_observations[0];
    }

    // matched / unmatched will come later
    $matched_observations = 0;
    $unmatched_observations = $total_observations - $matched_observations;

    // total credit
    $image_credit = (
        $total_reviewed +
        2 * $total_observations +
        4 * $matched_observations
    );

    // update the csg database
    $result = query_boinc_db("UPDATE user SET matched_image_observations=$matched_observations, unmatched_image_observations=$unmatched_observations, total_image_observations=$total_observations, total_image_reviews=$total_reviewed, image_credit=$image_credit WHERE id=$user_id");
}

?>
