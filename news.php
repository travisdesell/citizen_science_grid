<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname($cwd[__FILE__]);

require_once($cwd[__FILE__] . "/my_query.php");

require_once("/projects/csg/html/inc/text_transform.inc");

function csg_show_news($project_name = "", $limit = 5) {
    echo "
                <div class='well'>
                    <h3><a href='http://csgrid.org/csg/forum_forum.php?id=1'>News</a> <img href='rss_main.php' src='http://csgrid.org/csg/img/rss_icon.gif' alt='RSS'> </h3>";


    $thread_result = query_boinc_db("SELECT id, title, owner, timestamp FROM thread WHERE forum = 1 AND hidden = 0 AND title like '%$project_name%' ORDER BY id desc LIMIT $limit");


    while ($thread_row = $thread_result->fetch_assoc()) {
        $post_result = query_boinc_db("SELECT content, timestamp FROM post WHERE thread = " . $thread_row['id'] . " ORDER BY id LIMIT 1");
        $post_row = $post_result->fetch_assoc();

        $owner = csg_get_user_from_id($thread_row['owner']);

        echo "
                    <hr class='news-hr'>
                    <p><b>" . $thread_row['title'] . "</b></p>
                    <p>" . output_transform($post_row['content']) . "</p>
                    <p style='text-align:right; margin-bottom:0px'><i>" . $owner['name'] . " on " . date("l, F jS", $thread_row['timestamp']) . "</i><br>
                    <a href='http://csgrid.org/csg/forum_thread.php?id=" . $thread_row['id'] . "'>leave a comment</a></p>
                    ";
    }

    echo "
                </div> <!-- well -->";
}

?>
