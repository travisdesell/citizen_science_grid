<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname($cwd[__FILE__]);

require_once($cwd[__FILE__] . "/my_query.php");


// Returns a string containing as many words
// (being collections of characters separated by the character $delimiter)
// as possible such that the total string length is <= $chars characters long.
// If $ellipsis is true, then an ellipsis is added to any sentence which
// is cut short.

function sub_sentence($sentence, $delimiter, $max_chars, $ellipsis=false) {
    $words = explode($delimiter, $sentence);
    $total_chars = 0;
    $trunc = false;
    $result = ""; 

    foreach ($words as $word) {
        if (strlen($result) + strlen($word) > $max_chars) {
            $trunc = true;
            break;
        }   
        if ($result) {
            $result .= " $word";
        } else {
            $result = $word;
        }   
    }   

    if ($ellipsis && $trunc) {
        $result .= "...";
    }   

    return $result;
}

function show_uotd($col1, $col2, $style="") {
$uotd_result = query_boinc_db("SELECT * FROM profile ORDER BY uotd_time DESC LIMIT 1");

if ($uotd_result->num_rows == 1) {
    $uotd_row = $uotd_result->fetch_assoc();
    $uotd = csg_get_user_from_id($uotd_row['userid']);

    echo "
                <div class='well' $style>
                    <h3>User of the Day</h3>
                    <div class='row'>
";

    $uotd_text = "";
    if ($uotd['has_profile']) {
        $img_url = "../csg/img/head_20.png";
        $uotd_text .= " <a href='../csg/view_profile.php?userid='" . $uotd['id'] . "'><img title='View the profile of " . $uotd['name'] . "' src='" . $img_url . "' alt='Profile'></img></a>";
    }    
    $uotd_text .= " <a href='../csg/show_uotd.php?userid=" . $uotd['id'] . "'>" . $uotd['name'] . "</a><br>";
    $response = output_transform($uotd_row['response1']);
    $response = strip_tags($response);
    $response = sub_sentence($response, ' ', 150, true);
    $uotd_text .= $response;

    if ($uotd_row['has_picture']) {
        $uotd_picture = "<a href='../csg/view_profile.php?userid=" . $uotd['id'] . "'><img border=0 vspace=4 hspace=8 align=left src='../csg/user_profile/images/" . $uotd['id'] . "_sm.jpg' alt='User profile'></img></a>";
    }

    if ($uotd_picture) {
        echo "          <div class='col-sm-$col1'>$uotd_picture</div>";
    }
    echo "
                        <div class='col-sm-$col2'>$uotd_text</div>
                    </div> <!-- row -->
                </div> <!--well -->";
}

}

?>
