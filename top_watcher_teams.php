<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname($cwd[__FILE__]);

require_once($cwd[__FILE__] . "/header.php");
require_once($cwd[__FILE__] . "/navbar.php");
require_once($cwd[__FILE__] . "/footer.php");
require_once($cwd[__FILE__] . "/my_query.php");
require_once($cwd[__FILE__] . "/display_badges.php");

require_once $cwd[__FILE__] . '/../mustache.php/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();

print_header("Top Watching Teams");
print_navbar("Citizen Science Grid");

$min = 0;
if (array_key_exists('min', $_GET)) {
    $min = $boinc_db->real_escape_string($_GET['min']);
    if ($min == '') $min = 0;
}

$sort = 'events';
if (array_key_exists('sort', $_GET)) {
    $sort = $boinc_db->real_escape_string($_GET['sort']);
    $sort_by = "valid_events DESC";
}

if ($sort == 'events') {
    $sort_by = "valid_events DESC";
} else if ($sort == 'seconds') {
    $sort_by = "bossa_total_credit DESC";
}


$prev_min = $min - 20;
if ($prev_min < 0) $prev_min = 0;
$next_min = $min + 20;

echo "
    <div class='container'>
        <div class='row' style='margin-bottom:10px;'>
            <div class='col-sm-12'>";

if ($min > 0) {
    echo "<a type='button' class='btn btn-default pull-left' href='./top_watcher_teams.php?sort=$sort&min=$prev_min'>
                    <span class='glyphicon glyphicon-chevron-left'></span> Previous 
                </a>";
}

echo "
                <a type='button' class='btn btn-default pull-right' href='./top_watcher_teams.php?sort=$sort&min=$next_min'>
                    Next <span class='glyphicon glyphicon-chevron-right'></span>
                </a>

             </div> <!-- col-sm-12 -->
        </div> <!-- row -->";


echo "
        <div class='row'>
            <div class='col-sm-12'>";

$result = query_boinc_db("SELECT id, name, valid_events, bossa_total_credit, country, create_time FROM team ORDER BY $sort_by LIMIT $min, 20");

$teams['team'] = array();
$i = $min + 1;
while ($row = $result->fetch_assoc()) {
    $row['rank'] = $i;
    $i++;

    $row['create_time'] = date("F j, Y, g:i a", $row['create_time']);
    $row['bossa_total_credit'] = intval($row['bossa_total_credit']);

    $teams['team'][] = $row;
}
$teams['min'] = $min;

//echo json_encode($teams);

$navbar_template = file_get_contents($cwd[__FILE__] . "/templates/top_watcher_teams.html");

$m = new Mustache_Engine;
echo $m->render($navbar_template, $teams);


echo "
            </div> <!-- col-sm-12 -->
        </div> <!-- row -->
    </div> <!-- /container -->";



print_footer('Travis Desell, the DNA@Home, SubsetSum@Home and Wildlife@Home Teams', 'Travis Desell, Archana Dhasarathy, Susan Ellis-Felege, Tom O\'Neil, Sergei Nechaev');

echo "</body></html>";

?>

