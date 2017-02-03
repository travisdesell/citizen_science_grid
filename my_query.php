<?php

### Aaron did the following temporarily:
### I commented out lines 51-56, & 109
### on June 25 at 2:29pm


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
$uas_db = null;

// default servers to connect to
$default_servers = array(
    'localhost'
);

function db_connect($server, $user, $passwd, $db) {
    $dbcnx = new mysqli($server, $user, $passwd, $db);

    if ($dbcnx->connect_errno) {
        //echo "Failed to connect to MySQL: (" . $dbcnx->connect_errno . ") " . $dbcnx->connect_error;
        error_log("Failed to connect to MySQL: (" . $dbcnx->connect_errno . ") " . $dbcnx->connect_error);
    }

    return $dbcnx;
}

function db_connect_default(&$server, $user, $passwd, $db) {
    global $default_servers;

    $dbcnx = null;

    foreach ($default_servers as $default_server) {
        $server = $default_server;
        $dbcnx = db_connect($server, $user, $passwd, $db);
        if (!$dbcnx->connect_errno)
            break;
    }

    return $dbcnx;
}

function connect_boinc_db() {
    global $boinc_db, $boinc_user, $boinc_passwd, $boinc_server;

    // don't reconnect
    if (isset($boinc_db))
        return;

    // if we have a defined server, just connect
    // otherwise, try connected to our default servers in order
    if (isset($boinc_server)) {
        $boinc_db = db_connect($boinc_server, $boinc_user, $boinc_passwd, "csg");
    } else {
        $boinc_db = db_connect_default($boinc_server, $boinc_user, $boinc_passwd, "csg");
    }
}

function connect_dna_db() {
    global $dna_db, $dna_user, $dna_passwd, $dna_server;

    if (isset($dna_db))
        return;

    if (isset($dna_server)) {
        $dna_db = db_connect($dna_server, $dna_user, $dna_passwd, "dna");
    } else {
        $dna_db = db_connect_default($dna_server, $dna_user, $dna_passwd, "dna");
    }
}


function connect_subset_sum_db() {
    global $subset_sum_db, $subset_sum_user, $subset_sum_passwd, $subset_sum_server;

    if (isset($subset_sum_db))
        return;

    if (isset($subset_sum_server)) {
        $subset_sum_db = db_connect($subset_sum_server, $subset_sum_user, $subset_sum_passwd, "subset_sum");
    } else {
        $subset_sum_db = db_connect_default($subset_sum_server, $subset_sum_user, $subset_sum_passwd, "subset_sum");
    }
}


function connect_wildlife_db() {
    global $wildlife_db, $wildlife_user, $wildlife_passwd, $wildlife_server; 

    if (isset($wildlife_db))
        return;

    if (isset($wildlife_server)) {
        $wildlife_db = db_connect($wildlife_server, $wildlife_user, $wildlife_passwd, "wildlife_video");
    } else {
        $wildlife_db = db_connect_default($wildlife_server, $wildlife_user, $wildlife_passwd, "wildlife_video");
    }
}

function connect_uas_db() {
    global $uas_db, $wildlife_user, $wildlife_passwd, $uas_server;

    if (isset($uas_db))
        return;

    if (isset($uas_server)) {
        $uas_db = db_connect($uas_server, $wildlife_user, $wildlife_passwd, "uas");
    } else {
        $uas_db = db_connect_default($uas_server, $wildlife_user, $wildlife_passwd, "uas");
    }
}

//connect_boinc_db();
//connect_dna_db();

connect_boinc_db();
//connect_dna_db();
//connect_subset_sum_db();
connect_wildlife_db();
//connect_uas_db();

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

    $wildlife_pdo = new PDO("mysql:host=localhost;dbname=wildlife_video;", $wildlife_user, $wildlife_passwd);

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

function query_uas_db($query) {
    global $uas_db;

    if (!$uas_db or !$uas_db->ping()) connect_uas_db();

    $result = $uas_db->query($query);

    if (!$result) mysqli_error_msg($uas_db, $query);

    return $result;
}

function query_uas_db_prepared($query, $a_bind_params) {
    global $wildlife_user, $wildlife_passwd;

    $wildlife_pdo = new PDO("mysql:host=localhost;dbname=uas;", $wildlife_user, $wildlife_passwd);

    try {
        $stmt = $wildlife_pdo->prepare($query);
        $stmt->execute($a_bind_params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $e->getMessage(), E_USER_ERROR);
        if (!$uas_db or !$uas_db->ping()) connect_uas_db();
        mysqli_error_msg($uas_db, $query);
    }

    return $result;
}

?>
