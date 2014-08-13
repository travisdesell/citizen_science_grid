<?php

require_once("./my_query.php");


$dna_result = query_boinc_db("SELECT id, name, dna_userid FROM user WHERE dna_linked");

while ($dna_row = $dna_result->fetch_assoc()) {
    $user_id = $dna_row['id'];
    $user_name = $dna_row['name'];
    $dna_userid = $dna_row['dna_userid'];


    $credit_result = query_dna_db("SELECT total_credit, expavg_credit, expavg_time FROM user WHERE id = $dna_userid");
    if ($credit_result->num_rows > 1) {
        die ("CREDIT RESULT HAD MORE THAN 1 ROW!");
    }
    $credit_row = $credit_result->fetch_assoc();


    $dna_total_credit = $credit_row['total_credit'];
    $dna_expavg_credit = $credit_row['expavg_credit'];
    $dna_expavg_time  = $credit_row['expavg_time'];

    echo "CSG ID: $user_id, CSG NAME: $user_name, DNA ID: $dna_userid, DNA CREDIT: $dna_total_credit, DNA EXPAVG CREDIT: $dna_expavg_credit, DNA EXPAVG TIME: $dna_expavg_time\n";

    $update_result = query_boinc_db("UPDATE user SET dna_total_credit = $dna_total_credit, dna_expavg_credit = $dna_expavg_credit, dna_expavg_time = $dna_expavg_time WHERE id = $user_id");
}

$sss_result = query_boinc_db("SELECT id, name, subset_sum_userid FROM user WHERE subset_sum_linked");

while ($sss_row = $sss_result->fetch_assoc()) {
    $user_id = $sss_row['id'];
    $user_name = $sss_row['name'];
    $sss_userid = $sss_row['subset_sum_userid'];


    $credit_result = query_subset_sum_db("SELECT total_credit, expavg_credit, expavg_time FROM user WHERE id = $sss_userid");
    if ($credit_result->num_rows > 1) {
        die ("CREDIT RESULT HAD MORE THAN 1 ROW!");
    }
    $credit_row = $credit_result->fetch_assoc();


    $sss_total_credit = $credit_row['total_credit'];
    $sss_expavg_credit = $credit_row['expavg_credit'];
    $sss_expavg_time  = $credit_row['expavg_time'];

    echo "CSG ID: $user_id, CSG NAME: $user_name, DNA ID: $sss_userid, DNA CREDIT: $sss_total_credit, DNA EXPAVG CREDIT: $sss_expavg_credit, DNA EXPAVG TIME: $sss_expavg_time\n";

    $update_result = query_boinc_db("UPDATE user SET sss_total_credit = $sss_total_credit, sss_expavg_credit = $sss_expavg_credit, sss_expavg_time = $sss_expavg_time WHERE id = $user_id");
}


?>
