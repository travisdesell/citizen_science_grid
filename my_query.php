<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname($cwd[__FILE__]);

require_once($cwd[__FILE__] . '/../db_info/boinc_db.php');
require_once($cwd[__FILE__] . '/../db_info/dna_db.php');
require_once($cwd[__FILE__] . '/../db_info/subset_sum_db.php');
require_once($cwd[__FILE__] . '/../db_info/wildlife_db.php');

$boinc_db = null;
$dna_db = null;
$subset_sum_db = null;
$wildlife_db = null;

function connect_boinc_db() {
    global $boinc_db, $boinc_user, $boinc_passwd;
    $boinc_db = new mysqli("localhost", $boinc_user, $boinc_passwd, "wildlife");
    if ($boinc_db->connect_errno) {
        echo "Failed to connect to MySQL: (" . $boinc_db->connect_errno . ") " . $boinc_db->connect_error;
    }
}

function connect_dna_db() {
    global $dna_db, $dna_user, $dna_passwd;
    $dna_db = new mysqli("localhost", $dna_user, $dna_passwd, "dna");
    if ($dna_db->connect_errno) {
        echo "Failed to connect to MySQL: (" . $dna_db->connect_errno . ") " . $dna_db->connect_error;
    }
}


function connect_subset_sum_db() {
    global $subset_sum_db, $subset_sum_user, $subset_sum_passwd;
    $subset_sum_db = new mysqli("localhost", $subset_sum_user, $subset_sum_passwd, "subset_sum");
    if ($subset_sum_db->connect_errno) {
        echo "Failed to connect to MySQL: (" . $subset_sum_db->connect_errno . ") " . $subset_sum_db->connect_error;
    }
}


function connect_wildlife_db() {
    global $wildlife_db, $wildlife_user, $wildlife_passwd;

    $wildlife_db = new mysqli("wildlife.und.edu", $wildlife_user, $wildlife_passwd, "wildlife_video");

    if ($wildlife_db->connect_errno) {
        echo "Failed to connect to MySQL: (" . $wildlife_db->connect_errno . ") " . $wildlife_db->connect_error;
        error_log("Failed to connect to MySQL: (" . $wildlife_db->connect_errno . ") " . $wildlife_db->connect_error);
    }
}

connect_boinc_db();
connect_dna_db();
connect_subset_sum_db();
connect_wildlife_db();

function mysqli_error_msg($db, $query) {
    error_log("MYSQL Error (" . $db->errno . "): " . $db->error . ", query: $query");
    die("MYSQL Error (" . $db->errno . "): " . $db->error . ", query: $query");
}


function query_boinc_db($query) {
    global $boinc_db;

    if (!$boinc_db->ping()) connect_boinc_db();

    $result = $boinc_db->query($query);

    if (!$result) mysqli_error_msg($boinc_db, $query);

    return $result;
}

function query_dna_db($query) {
    global $dna_db;

    if (!$dna_db->ping()) connect_dna_db();

    $result = $dna_db->query($query);

    if (!$result) mysqli_error_msg($dna_db, $query);

    return $result;
}

function query_subset_sum_db($query) {
    global $subset_sum_db;

    if (!$subset_sum_db->ping()) connect_subset_sum_db();

    $result = $subset_sum_db->query($query);

    if (!$result) mysqli_error_msg($subset_sum_db, $query);

    return $result;
}


function query_wildlife_video_db($query) {
    global $wildlife_db;

    if (!$wildlife_db->ping()) connect_wildlife_db();

    $result = $wildlife_db->query($query);

    if (!$result) mysqli_error_msg($wildlife_db, $query);

    return $result;
}

function query_wildlife_video_db_prepared($query, $a_bind_params) {
    global $wildlife_user, $wildlife_passwd;

    $wildlife_pdo = new PDO("mysql:host=wildlife.und.edu;dbname=wildlife_video;", $wildlife_user, $wildlife_passwd);

    try {
        $stmt = $wildlife_pdo->prepare($query);
        $stmt->execute($a_bind_params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $e->getMessage(), E_USER_ERROR);
        mysqli_error_msg($wildlife_db, $query);
    }

    return $result;
}


?>
