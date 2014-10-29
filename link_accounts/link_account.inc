<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname($cwd[__FILE__]);

require_once($cwd[__FILE__] . "/../user.php");
require_once($cwd[__FILE__] . "/../my_query.php");


function link_account($user, $project) {
    $userid = $user['id'];
    $teamid = $user['teamid'];
    $dna_linked = $user['dna_linked'];
    $sss_linked = $user['subset_sum_linked'];
    $dna_total_credit = $user['dna_total_credit'];
    $sss_total_credit = $user['sss_total_credit'];
    $email_addr = $user['email_addr'];
    $username = $user['name'];

    error_log("userid: $userid, teamid: $teamid, dna credit: $dna_total_credit, sss credit: $sss_total_credit");

    $other_project_credit = 0;
    $response = array();
    if ($project == "dna") {
        $dna_result = query_dna_db("SELECT total_credit FROM user WHERE email_addr = '$email_addr'");
        $dna_row = $dna_result->fetch_assoc();

        if (!$dna_row) {
            $response['status'] = "error";
            $response['error_msg'] = "Could not found account on DNA@Home with email: '" . $email_addr. "'";
            return $response;
        } else {
            $other_project_credit = $dna_row['total_credit'];
        }

    } else if ($project == "subset_sum") {
        $sss_result = query_subset_sum_db("SELECT total_credit FROM user WHERE email_addr = '$email_addr'");
        $sss_row = $sss_result->fetch_assoc();

        if (!$sss_row) {
            $response['status'] = "error";
            $response['error_msg'] = "Could not found account on SubsetSum@Home with email: '" . $email_addr. "'";
            return $response;
        } else {
            $other_project_credit = $sss_row['total_credit'];
        }

    } else {
        $response['status'] = "error";
        $response['error_msg'] = "Unknown project to link to: '" . $project . "'";
        return $response;
    }

    error_log("other project credit is: $other_project_credit");

    $credit_to_add = $other_project_credit;
    if ($project == "dna") $credit_to_add += $dna_total_credit;
    if ($project == "subset_sum") $credit_to_add += $sss_total_credit;

    error_log("credit to add to 'credit_user' table: " . $credit_to_add);

    $appid = -1;
    if ($project == "dna") $appid = 13;
    else if ($project == "subset_sum") $appid = 15;

    //need to update:
    //  credit_team
    //  team total_credit
    //  credit_user
    //  user total_credit
    if ($teamid > 0) {
        error_log("[$username - link accounts] had teamid: $teamid");

        $tu_result = query_boinc_db("SELECT total FROM credit_team WHERE teamid = $teamid AND appid = $appid");
        $tu_row = $tu_result->fetch_assoc();

        if ($tu_row) {
            error_log("[$username - link accounts] found credit_team row");
            query_boinc_db("UPDATE credit_team SET total = total + $credit_to_add WHERE teamid = $teamid AND appid = $appid");
        } else {
            error_log("[$username - link accounts] did not find credit_team row");
            query_boinc_db("INSERT INTO credit_team SET total = $credit_to_add, teamid = $teamid, appid = $appid, njobs = 0, expavg = 0, credit_type = 0");
        }

        query_boinc_db("UPDATE team SET total_credit = total_credit + $credit_to_add WHERE id = $teamid");
    } else {
        error_log("[$username - link accounts] Did not find team");

    }

    $cu_result = query_boinc_db("SELECT total FROM credit_user WHERE userid = $userid AND appid = $appid");
    $cu_row = $cu_result->fetch_assoc();

    if ($cu_row) {
        error_log("[$username - link accounts] found credit_user row");
        query_boinc_db("UPDATE credit_user SET total = total + $credit_to_add WHERE userid = $userid AND appid = $appid");
    } else {
        error_log("[$username - link accounts] did not find credit_user row");
        query_boinc_db("INSERT INTO credit_user SET total = $credit_to_add, userid = $userid, appid = $appid, njobs = 0, expavg = 0, credit_type = 0");
    }
    query_boinc_db("UPDATE user SET total_credit = total_credit + $credit_to_add, " . $project . "_linked = 1 WHERE id = $userid");

    $response['status'] = 'success';

    return $response;
}

/*
$user = csg_get_user_from_id($argv[1]);
echo json_encode( link_account($user, $argv[2]) );
 */

?>
