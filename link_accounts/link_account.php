<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname($cwd[__FILE__]);

require_once($cwd[__FILE__] . "/../user.php");
require_once($cwd[__FILE__] . "/../my_query.php");

$project = mysql_real_escape_string($_POST["project"]);
$username = mysql_real_escape_string($_POST["username"]);
$userid = mysql_real_escape_string($_POST["userid"]);
$email = mysql_real_escape_string($_POST["project"]);

error_log("project: '$project' username: '$username' userid: '$userid' email: '$email'");

$user = csg_get_user(true);

$linked_result = query_boinc_db("SELECT " . $project . "_linked FROM user WHERE id = " . $user['id']);
$linked_row = $linked_result->fetch_assoc();

error_log("linked? " . $linked_row[$project . "_linked"]);

$response_array = array();
if ($linked_row[$project . "_linked"] == 1) {
    $response_array['status'] = 'error';
    $response_array['error_msg'] = 'Accounts already linked!';
} else {
    query_boinc_db("UPDATE user SET " . $project . "_linked = 1, " . $project . "_username = '$username', " . $project . "_userid = $userid WHERE id = " . $user['id']);
    $response_array['status'] = 'success';

    //dna_app is 13
    //wildlife_app is 7, 9, 12
    //subset_sum is 15

    if ($project == "subset_sum") {
        $sss_result = query_subset_sum_db("SELECT total_credit, expavg_credit, expavg_time FROM user WHERE id = $userid");
        $sss_row = $sss_result->fetch_assoc();

        $per_app_result = query_boinc_db("SELECT * FROM credit_user WHERE userid = $userid AND appid = 15");
        $per_app_row = $per_app_result->fetch_assoc();
        if ($per_app_row) {
            query_boinc_db("UPDATE credit_user SET total = total + " . $sss_row['total_credit'] . " WHERE userid = $userid AND appid = 15");
        } else {
            query_boinc_db("INSERT INTO credit_user SET userid = $userid, appid = 15, njobs = 0, total=" . $sss_row['total_credit'] . ", expavg=" . $sss_row['expavg_credit'] . ", credit_type = 0");
        }

        if ($user['teamid'] > 0) {
            $team_result = query_boinc_db("SELECT * FROM credit_team WHERE teamid = " . $user['teamid'] . " AND appid = 15");
            $team_row = $team_result->fetch_assoc();

            if ($team_row) {
                query_boinc_db("UPDATE credit_team SET total = total + " . $sss_row['total_credit'] . " WHERE teamid = " . $user['teamid'] . " AND appid = 15");
            } else {
                query_boinc_db("INSERT INTO credit_team SET teamid = " . $user['teamid'] . ", appid = 15, njobs = 0, total=" . $sss_row['total_credit'] . ", expavg=" . $sss_row['expavg_credit'] . ", credit_type = 0");
            }
        }

    } else if ($project == "dna") {
        $dna_result = query_dna_db("SELECT total_credit, expavg_credit, expavg_time FROM user WHERE id = $userid");
        $dna_row = $dna_result->fetch_assoc();

        $per_app_result = query_boinc_db("SELECT * FROM credit_user WHERE userid = $userid AND appid = 13");
        $per_app_row = $per_app_result->fetch_assoc();
        if ($per_app_row) {
            query_boinc_db("UPDATE credit_user SET total = total + " . $dna_row['total_credit'] . " WHERE userid = $userid AND appid = 13");
        } else {
            query_boinc_db("INSERT INTO credit_user SET userid = $userid, appid = 13, njobs = 0, total=" . $dna_row['total_credit'] . ", expavg=" . $dna_row['expavg_credit'] . ", credit_type = 0");
        }

        if ($user['teamid'] > 0) {
            $team_result = query_boinc_db("SELECT * FROM credit_team WHERE teamid = " . $user['teamid'] . " AND appid = 13");
            $team_row = $team_result->fetch_assoc();

            if ($team_row) {
                query_boinc_db("UPDATE credit_team SET total = total + " . $dna_row['total_credit'] . " WHERE teamid = " . $user['teamid'] . " AND appid = 13");
            } else {
                query_boinc_db("INSERT INTO credit_team SET teamid = " . $user['teamid'] . ", appid = 13, njobs = 0, total=" . $dna_row['total_credit'] . ", expavg=" . $dna_row['expavg_credit'] . ", credit_type = 0");
            }
        }

    }
}

echo json_encode($response_array);

?>
