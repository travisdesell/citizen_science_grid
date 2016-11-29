<?php

require_once("../my_query.php");

$results = query_boinc_db("SELECT id, name, email_addr FROM user where total_credit = 0 AND total_events = 0 AND total_tweets = 0 AND total_observations = 0 AND teamid = 0 AND total_observations = 0");

$delete_count = 0;
while ($row = $results->fetch_assoc()) {
    $user_id = $row['id'];
    $user_name = $row['name'];
    $email_addr = $row['email_addr'];


    $host_result = query_boinc_db("SELECT count(*) FROM host where userid = $user_id");
    $host_row = $host_result->fetch_assoc();
    $host_count = $host_row['count(*)'];


    if ($host_count == 0) {
        echo "DELETING: $user_id - $user_name - $email_addr : $host_count\n";
        $delete_count++;

        query_boinc_db("DELETE FROM user WHERE id = $user_id");
    }
}

echo "deleted $delete_count spammers.\n";

?>
