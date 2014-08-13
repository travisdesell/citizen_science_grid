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

    if ($project == "subset_sum") {
        $sss_result = query_subset_sum_db("SELECT total_credit, expavg_credit, expavg_time FROM user WHERE id = $userid");
        $sss_row = $sss_result->fetch_assoc();

        query_boinc_db("UPDATE user SET sss_total_credit = sss_total_credit + " . $sss_row['total_credit'] . ", sss_expavg_credit = sss_expavg_credit + " . $sss_row['expavg_credit'] . ", sss_expavg_time = " . $sss_row['expavg_time'] . " WHERE id = " . $user['id']);
    } else if ($project == "dna") {
        $dna_result = query_dna_db("SELECT total_credit, expavg_credit, expavg_time FROM user WHERE id = $userid");
        $dna_row = $dna_result->fetch_assoc();

        query_boinc_db("UPDATE user SET dna_total_credit = dna_total_credit + " . $dna_row['total_credit'] . ", dna_expavg_credit = dna_expavg_credit + " . $dna_row['expavg_credit'] . ", dna_expavg_time = " . $dna_row['expavg_time'] . " WHERE id = " . $user['id']);

    }
}

echo json_encode($response_array);

?>
