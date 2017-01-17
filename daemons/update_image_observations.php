#!/usr/bin/env php

<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname(dirname($cwd[__FILE__]));

require_once($cwd[__FILE__] . "/my_query.php");
require_once($cwd[__FILE__] . "/user.php");

// get a list of all the users who have submitted
$user_list = query_wildlife_video_db("SELECT DISTINCT user_id FROM image_observation_experts ORDER BY user_id ASC");
if (!$user_list) {
    exit;
}

// for each user, update their info
while (($row = $user_list->fetch_row()) != null) {
    $user = $row[0];
    echo "Updating user #$user images... ";
    if (query_wildlife_video_db("UPDATE image_observations SET is_expert = 1 WHERE user_id=$user"))
        echo "Success";
    else
        echo "Failure";

    echo "\n";
}

// update the counts
$result = query_wildlife_video_db("SELECT DISTINCT image_id FROM image_observations");
while (($row = $result->fetch_row()) != null) {
    $image_id = $row[0];
    $expert_result = query_wildlife_video_db("SELECT COUNT(*) FROM image_observations WHERE image_id=$image_id AND is_expert=1");
    $expert_count = 0;
    if ($expert_result) { 
        $expert_count = ($expert_result->fetch_row())[0];
    }

    $nonexpert_result = query_wildlife_video_db("SELECT COUNT(*) FROM image_observations WHERE image_id=$image_id AND is_expert=0");
    $nonexpert_count = 0;
    if ($nonexpert_result) { 
        $nonexpert_count = ($nonexpert_result->fetch_row())[0];
    }

    echo "Updating image #$image_id with $nonexpert_count non-experts and $expert_count experts... ";
    if (query_wildlife_video_db("UPDATE images SET views = $nonexpert_count, expert_views = $expert_count WHERE id=$image_id"))
        echo "Success";
    else
        echo "Failure";

    echo "\n";
}

?>
