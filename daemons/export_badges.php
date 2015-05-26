#!/usr/bin/env php

<?php


$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname(dirname($cwd[__FILE__]));

require_once($cwd[__FILE__] . "/my_query.php");
require_once($cwd[__FILE__] . "/display_badges.php");

echo "running export_badges.php at " . date('Y/m/d h:i:s a') . "\n";

$result = query_boinc_db("SELECT id, name, email_addr, total_credit, bossa_total_credit, cross_project_id FROM user WHERE total_credit > 0 OR bossa_total_credit > 0", $boinc_db);

$file = fopen("/projects/csg/download/badges.xml", "w");

fwrite($file, "<users>\n");
while ( ($row = $result->fetch_assoc()) != null) {
    $user['id'] = $row['id'];
    $user['bossa_total_credit'] = $row['bossa_total_credit'];
    $user['cross_project_id'] = $row['cross_project_id'];
    $user['email_addr'] = $row['email_addr'];

    $cpid = md5($user['cross_project_id'] . $user['email_addr']);

    fwrite($file, "<user>\n");
    fwrite($file, "\t<id>" . $user['id'] . "</id>\n");
    fwrite($file, "\t<cpid>" . $cpid . "</cpid>\n");
    fwrite($file, "\t<bossa_credit>" . $user['bossa_total_credit'] . "</bossa_credit>\n");

    $wildlife_credit_badge = get_wildlife_credit_badge_str($user);
    $dna_credit_badge = get_dna_credit_badge_str($user);
    $sss_credit_badge = get_sss_credit_badge_str($user);
    $video_badge = get_bossa_badge_str($user);

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

    fwrite($file, "</user>\n");
}

fwrite($file, "</users>\n");

?>
