#!/usr/bin/env php

<?php


$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname(dirname($cwd[__FILE__]));

require_once($cwd[__FILE__] . "/my_query.php");
require_once($cwd[__FILE__] . "/display_badges.php");
require_once($cwd[__FILE__] . "/user.php");

echo "running export_badges.php at " . date('Y/m/d h:i:s a') . "\n";

$result = query_boinc_db("SELECT id FROM user WHERE total_credit > 0 OR bossa_total_credit > 0 OR image_credit > 0 OR valid_tweets > 0", $boinc_db);

// LIVE
$filename = "/projects/csg/download/badges.xml";

// TESTING
//$filename = $cwd[__FILE__] . "/badges.xml";
//
$file = fopen($filename, "w");

echo "saving to $filename\n";

fwrite($file, "<users>\n");
while ( ($row = $result->fetch_assoc()) != null) {
    $user = csg_get_user_from_id($row['id']);
    $cpid = md5($user['cross_project_id'] . $user['email_addr']);

    fwrite($file, "<user>\n");
    fwrite($file, "\t<id>" . $user['id'] . "</id>\n");
    fwrite($file, "\t<cpid>" . $cpid . "</cpid>\n");
    fwrite($file, "\t<bossa_credit>" . $user['bossa_total_credit'] . "</bossa_credit>\n");

    $wildlife_credit_badge = get_wildlife_credit_badge_str($user);
    $dna_credit_badge = get_dna_credit_badge_str($user);
    $sss_credit_badge = get_sss_credit_badge_str($user);
    $video_badge = get_bossa_badge_str($user);
    $event_badge = get_event_badge_str($user);
    $tweets_badge = get_tweets_badge_str($user);
    $image_badge = get_image_badge_str($user);

    if ($wildlife_credit_badge != "") {
        fwrite($file, "\t<wildlife_credit_badge>" . $wildlife_credit_badge . "</wildlife_credit_badge>\n");
    }

    if ($dna_credit_badge != "") {
        fwrite($file, "\t<dna_credit_badge>" . $dna_credit_badge . "</dna_credit_badge>\n");
    }

    if ($sss_credit_badge != "") {
        fwrite($file, "\t<sss_credit_badge>" . $sss_credit_badge . "</sss_credit_badge>\n");
    }


    if ($video_badge != "") {
        fwrite($file, "\t<video_badge>" . $video_badge . "</video_badge>\n");
    }

    if ($event_badge != "") {
        fwrite($file, "\t<event_badge>" . $event_badge . "</event_badge>\n");
    }

    if ($tweets_badge != "") {
        fwrite($file, "\t<tweets_badge>" . $tweets_badge . "</tweets_badge>\n");
    }

    if ($image_badge != "") {
        fwrite($file, "\t<image_badge>" . $image_badge . "</image_badge>\n");
    }

    fwrite($file, "</user>\n");
}

fwrite($file, "</users>\n");

?>
