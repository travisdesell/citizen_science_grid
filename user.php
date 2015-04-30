<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = dirname(readlink($cwd[__FILE__]));
else $cwd[__FILE__] = dirname($cwd[__FILE__]);

require_once($cwd[__FILE__] . '/my_query.php');

$csg_g_logged_in_user = null;
$csg_got_logged_in_user = false;

$dna_g_logged_in_user = null;
$dna_got_logged_in_user = false;

$sss_g_logged_in_user = null;
$sss_got_logged_in_user = false;


function get_next_url() {
    $next_url = '';
    if (array_key_exists('REQUEST_URI', $_SERVER)) {
        $next_url = $_SERVER['REQUEST_URI'];
        $n = strpos($next_url, "/", 1);
        if ($n) {
            $next_url = substr($next_url, $n+1);
        }
    }
    error_log("request uri: " .$_SERVER['REQUEST_URI']);
    error_log("next url: " .$next_url);

    $next_url = urlencode($next_url);
    return $next_url;
}


function csg_url_tokens($auth) {
    $now = time();
    $ttok = md5((string)$now.$auth);
    return "&amp;tnow=$now&amp;ttok=$ttok";
}

function csg_get_user_from_id($id) {
    $result = query_boinc_db("SELECT * FROM user WHERE id = '$id'");
    return $result->fetch_assoc();
}

function sss_get_user($must_be_logged_in = true) {
    global $sss_g_logged_in_user, $sss_got_logged_in_user;

    if ($sss_got_logged_in_user) return $sss_g_logged_in_user;

    $authenticator = null;
    if (isset($_COOKIE['auth'])) {
        $authenticator = $_COOKIE['auth'];
        //error_log("authenticator set: '" . $authenticator . "'");
    }

    $authenticator = mysql_real_escape_string($authenticator);
    if ($authenticator) {
        $result = query_subset_sum_db("SELECT * FROM user WHERE authenticator = '$authenticator'");
        $sss_g_logged_in_user = $result->fetch_assoc();
    }

    if ($must_be_logged_in && !$sss_g_logged_in_user) {
        $next_url = get_next_url();
        Header("Location: http://volunteer.cs.und.edu/csg/login_form.php?next_url=$next_url");
        exit;
    }

    $sss_got_logged_in_user = true;
    return $sss_g_logged_in_user;
}


function dna_get_user($must_be_logged_in = true) {
    global $dna_g_logged_in_user, $dna_got_logged_in_user;

    if ($dna_got_logged_in_user) return $dna_g_logged_in_user;

    $authenticator = null;
    if (isset($_COOKIE['auth'])) {
        $authenticator = $_COOKIE['auth'];
        //error_log("authenticator set: '" . $authenticator . "'");
    }

    $authenticator = mysql_real_escape_string($authenticator);
    if ($authenticator) {
        $result = query_dna_db("SELECT * FROM user WHERE authenticator = '$authenticator'");
        $dna_g_logged_in_user = $result->fetch_assoc();
    }

    if ($must_be_logged_in && !$dna_g_logged_in_user) {
        $next_url = get_next_url();
        Header("Location: http://volunteer.cs.und.edu/csg/login_form.php?next_url=$next_url");
        exit;
    }

    $dna_got_logged_in_user = true;
    return $dna_g_logged_in_user;
}

function csg_get_user($must_be_logged_in = true) {
    global $csg_g_logged_in_user, $csg_got_logged_in_user;

    if ($csg_got_logged_in_user) return $csg_g_logged_in_user;

    $authenticator = null;
    if (isset($_COOKIE['auth'])) {
        $authenticator = $_COOKIE['auth'];
        //error_log("authenticator set: '" . $authenticator . "'");
    }

    $authenticator = mysql_real_escape_string($authenticator);
    if ($authenticator) {
        $result = query_boinc_db("SELECT * FROM user WHERE authenticator = '$authenticator'");
        $csg_g_logged_in_user = $result->fetch_assoc();
    }

    if ($must_be_logged_in && !$csg_g_logged_in_user) {
        $next_url = get_next_url();
        Header("Location: http://volunteer.cs.und.edu/csg/login_form.php?next_url=$next_url");
        exit;
    }

    $csg_got_logged_in_user = true;
    return $csg_g_logged_in_user;
}


function csg_is_special_user($user = null, $must_be_logged_in = true) {
    if ($user == null) {
        if ($must_be_logged_in) {
            $user = csg_get_user($must_be_logged_in);
            if ($user == null) return 0;
        } else {
            return 0;
        }
    }

    $query = "SELECT special_user FROM forum_preferences WHERE userid=" . $user['id'];
    $result = query_boinc_db($query);
    $row = $result->fetch_assoc();
    $special_user = $row['special_user'];

    if ($special_user == null) {
        return 0;
    } else if (strlen($special_user) <= 6) {
        return 0;
    } else if ($special_user{6} == 1) {
        return 1;
    } else {
        return 0;
    }
}

?>
