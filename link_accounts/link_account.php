<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname($cwd[__FILE__]);

require_once($cwd[__FILE__] . "/../user.php");
require_once($cwd[__FILE__] . "/../my_query.php");
require_once($cwd[__FILE__] . "/link_account.inc");

$project = $boinc_db->real_escape_string($_POST["project"]);
$username = $boinc_db->real_escape_string($_POST["username"]);
$userid = $boinc_db->real_escape_string($_POST["userid"]);
$email = $boinc_db->real_escape_string($_POST["project"]);


$user = csg_get_user(true);

$response_array = link_account($user, $project);

echo json_encode($response_array);

?>
