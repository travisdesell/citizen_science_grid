<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname($cwd[__FILE__]);

require_once($cwd[__FILE__] . "/my_query.php");

$status_ids = array(
    'video_conversion' => 1
);

function is_online($id) {
    $result = query_wildlife_video_db("SELECT online FROM status WHERE id=$id");
    return $result && (($result->fetch_row())[0] != 0);
}

function video_conversion_online() {
    global $status_ids;
    return is_online($status_ids['video_conversion']);
}

?>
