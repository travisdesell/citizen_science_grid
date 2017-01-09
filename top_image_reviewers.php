<?php

$cwd[__FILE__] = __FILE__;
while (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname($cwd[__FILE__]);

require_once($cwd[__FILE__] . "/header.php");
require_once($cwd[__FILE__] . "/navbar.php");
require_once($cwd[__FILE__] . "/footer.php");
require_once($cwd[__FILE__] . "/my_query.php");
require_once($cwd[__FILE__] . "/display_badges.php");
require_once($cwd[__FILE__] . "/user.php");

require_once $cwd[__FILE__] . '/../mustache.php/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();

print_header("Top Image Reviewers");
print_navbar("Citizen Science Grid");

$min = 0;
if (array_key_exists('min', $_GET)) {
    $min = $boinc_db->real_escape_string($_GET['min']);
    if ($min == '') $min = 0;
}

$sort = 'total';
if (array_key_exists('sort', $_GET)) {
    $sort = $boinc_db->real_escape_string($_GET['sort']);
    $sort_by = "image_credit DESC";
}

$where = "";
$count = 0;
if ($sort == 'matched') {
    $sort_by = "matched_image_observations DESC";
    $where = "matched_image_observations > 0";

} else if ($sort == 'total') {
    $sort_by = "image_credit DESC";
    $where = "image_credit > 0";
}

$result = query_boinc_db("SELECT COUNT(*) FROM user WHERE $where");
$count = $result->fetch_array();
$count = $count[0];

$prev_min = $min - 20;
if ($prev_min < 0) $prev_min = 0;
$next_min = $min + 20;

echo "
    <div class='container'>
        <div class='row' style='margin-bottom:10px;'>
            <div class='col-sm-12'>";

if ($min > 0) {
    echo "<a type='button' class='btn btn-default pull-left' href='./top_image_reviewers.php?sort=$sort&min=$prev_min'>
                    <span class='glyphicon glyphicon-chevron-left'></span> Previous 
                </a>";
}

if ($next_min <= $count) {
echo "
                <a type='button' class='btn btn-default pull-right' href='./top_image_reviewers.php?sort=$sort&min=$next_min'>
                    Next <span class='glyphicon glyphicon-chevron-right'></span>
                </a>";
}

echo "
             </div> <!-- col-sm-12 -->
        </div> <!-- row -->";

echo "
        <div class='row'>
            <div class='col-sm-12'>";

$result = query_boinc_db("SELECT id, name, country, create_time, image_credit, matched_image_observations FROM user WHERE $where ORDER BY $sort_by LIMIT $min, 20");

$users['user'] = array();
$i = $min + 1;
while ($row = $result->fetch_assoc()) {
    $row['rank'] = $i;
    $i++;

    $user = csg_get_user_from_id($row['id']);
    if (!isset($user)) continue;

    $user = json_decode(json_encode($user), FALSE);
    if (!isset($user) || !isset($user->id)) continue;

    $row['badges'] = get_badges($user);

    $row['create_time'] = date("F j, Y, g:i a", $row['create_time']);
    $row['image_credit'] = intval($row['image_credit']);

    $users['user'][] = $row;
}
$users['min'] = $min;

//echo json_encode($users);

$navbar_template = file_get_contents($cwd[__FILE__] . "/templates/top_image_reviewers.html");

$m = new Mustache_Engine;
echo $m->render($navbar_template, $users);


echo "
            </div> <!-- col-sm-12 -->
        </div> <!-- row -->
    </div> <!-- /container -->";



print_footer('Travis Desell, the DNA@Home, SubsetSum@Home and Wildlife@Home Teams', 'Travis Desell, Archana Dhasarathy, Susan Ellis-Felege, Tom O\'Neil, Sergei Nechaev');

echo "</body></html>";

?>

