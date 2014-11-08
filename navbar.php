<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname($cwd[__FILE__]);

require_once($cwd[__FILE__] . "/user.php");

require_once $cwd[__FILE__] . '/../mustache.php/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();

function print_navbar($active_title, $project_name = "Citizen Science Grid", $base_dir = ".") {
    global $cwd;

    $cwd[__FILE__] = __FILE__;
    if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
    $cwd[__FILE__] = dirname($cwd[__FILE__]);

    $navbar_info = array('project_name' => 'CSG',
                       'url' => 'http://volunteer.cs.und.edu/csg/',
                       'brand-dropdowns' => array(
                            array('dropdown_title' => 'Citizen Science Grid',
                                  'url' => 'http://volunteer.cs.und.edu/csg'),
                            array('dropdown_title' => 'Projects',
                                  'divider' => true,
                                  'url' => 'javascript:;'),
                            array('dropdown_title' => 'DNA@Home',
                                  'url' => 'http://volunteer.cs.und.edu/csg/dna'),
                            array('dropdown_title' => 'SubsetSum@Home',
                                  'url' => 'http://volunteer.cs.und.edu/csg/subset_sum'),
                            array('dropdown_title' => 'Wildlife@Home',
                                  'url' => 'http://volunteer.cs.und.edu/csg/wildlife'),
                            array('dropdown_title' => 'Affiiliated Projects',
                                  'divider' => true,
                                  'url' => 'javascript:;'),
                            array('dropdown_title' => 'MilkyWay@Home',
                                  'url' => 'http://milkyway.cs.rpi.edu')
                        ),

                       'left_headers' => array(
                            array(
                                'dropdown_title' => 'Information',
                                'classes' => '',
                                'dropdowns' => array(
                                    array('dropdown_title' => 'Instructions, Rules & Policies',
                                          'url' => "$base_dir/instructions.php"),
                                    array('dropdown_title' => 'Languages',
                                          'url' => "$base_dir/language_select.php"),
                                    array('dropdown_title' => 'Profiles',
                                          'url' => "$base_dir/profile_menu.php"),
                                    array('dropdown_title' => 'Publications',
                                          'url' => "$base_dir/publications.php"),
                                    array('dropdown_title' => 'Server Status',
                                          'url' => "$base_dir/server_status.php"),
                                    array('dropdown_title' => 'User Search',
                                          'url' => "$base_dir/user_search.php"),
                                    array('dropdown_title' => 'Badge Descriptions',
                                          'url' => "$base_dir/badge_list.php")
                                 )
                            ),

                            array(
                                'dropdown_title' => 'Top Lists',
                                'classes' => '',
                                'dropdowns' => array(
                                    array('dropdown_title' => 'Top Users',
                                          'url' => "$base_dir/top_users.php"),
                                    array('dropdown_title' => 'Top Hosts',
                                          'url' => "$base_dir/top_hosts.php"),
                                    array('dropdown_title' => 'Top Teams',
                                          'url' => "$base_dir/top_teams.php"),
                                    array('dropdown_title' => 'More Statistics',
                                          'url' => "$base_dir/stats.php"),
                                    array('dropdown_title' => 'Sub-Project Lists',
                                          'divider' => true,
                                          'url' => 'javascript:;'),
                                    array('dropdown_title' => 'Top DNA@Home Users',
                                          'url' => 'http://volunteer.cs.und.edu/csg/per_app_list.php?appid=13&is_team=&is_total=1'),
                                    array('dropdown_title' => 'Top SubsetSum@Home Users',
                                          'url' => 'http://volunteer.cs.und.edu/csg/per_app_list.php?appid=15&is_team=&is_total=1'),
                                    array('dropdown_title' => 'Top Wildlife@Home Users',
                                          'url' => 'http://volunteer.cs.und.edu/csg/per_app_list.php?appid=12&is_team=&is_total=1'),
                                    array('dropdown_title' => 'Wildlife@Home',
                                          'divider' => true,
                                          'url' => 'javascript:;'),
                                    array('dropdown_title' => 'Top Bird Watchers',
                                          'url' => "$base_dir/wildlife/top_bossa_users.php"),
                                    array('dropdown_title' => 'Top Bird Watching Teams',
                                          'url' => "$base_dir/wildlife/top_bossa_teams.php")
                               )
                            ),

                            array(
                                'title' => 'Message Boards',
                                'url' => "$base_dir/forum_index.php",
                                'classes' => ''
                            )

                       ),

                       'right_headers' => array()
                    );

    if ($project_name == "DNA@Home") {
        $navbar_info['project_name'] = "DNA@Home";
    } else if ($project_name == "SubsetSum@Home") {
        $navbar_info['project_name'] = "SubsetSum@Home";
    } else if ($project_name == "Wildlife@Home") {
        $navbar_info['project_name'] = "Wildlife@Home";
    } else {
        $navbar_info['project_name'] = "Citizen Science Grid";
    }
    $user = csg_get_user(false);

    if ($project_name == "DNA@Home") {
        $navbar_info['right_headers'][] =
            array(
                'dropdown_title' => 'Gibbs Sampling',
                'url' => '#',
                'classes' => '',
                'dropdowns' => array(
                    array('dropdown_title' => 'Progress Information',
                    'url' => './overview.php')
                )
            );
    }


    if ($project_name == "Wildlife@Home") {
        $navbar_info['right_headers'][] =
            array(
                'dropdown_title' => 'Wildlife Video',
                'url' => '#',
                'classes' => '',
                'dropdowns' => array(
                    array('dropdown_title' => 'Site and Species Descriptions',
                    'url' => './video_selector.php'),
                    array('dropdown_title' => 'Review Videos',
                    'url' => './review_videos.php'),
                    array('dropdown_title' => 'Sharp-Tailed Grouse',
                    'divider' => true,
                    'url' => 'javascript:;'),
                    array('dropdown_title' => 'Belden, ND',
                    'url' => './watch.php?location=1&species=1'),
                    array('dropdown_title' => 'Blaisdell, ND',
                    'url' => './watch.php?location=2&species=1'),
                    array('dropdown_title' => 'Lostwood Wildlife Refuge, ND',
                    'url' => './watch.php?location=3&species=1'),
                    array('dropdown_title' => 'Interior Least Tern',
                    'divider' => true,
                    'url' => 'javascript:;'),
                    array('dropdown_title' => 'Missouri River, ND',
                    'url' => './watch.php?location=4&species=2'),
                    array('dropdown_title' => 'Piping Plover',
                    'divider' => true,
                    'url' => 'javascript:;'),
                    array('dropdown_title' => 'Missouri River, ND',
                    'url' => './watch.php?location=4&species=3')
                )
            );

        $navbar_info['right_headers'][] =
            array(
                'dropdown_title' => 'About the Wildlife',
                'url' => '#',
                'classes' => '',
                'dropdowns' => array(
                    array('dropdown_title' => 'Sharp-Tailed Grouse',
                    'divider' => true,
                    'url' => 'javascript:;'),
                    array('dropdown_title' => 'Ecology and Information',
                    'url' => './sharptailed_grouse_info.php'),
                    array('dropdown_title' => 'Training Videos',
                    'url' => './sharptailed_grouse_training.php'),

                    array('dropdown_title' => 'Interior Least Tern',
                    'divider' => true,
                    'url' => 'javascript:;'),
                    array('dropdown_title' => 'Ecology and Information',
                    'url' => './least_tern_info.php'),
                    array('dropdown_title' => 'Training Videos (Coming Soon)',
                    'url' => 'javascript:;'),

                    array('dropdown_title' => 'Piping Plover',
                    'divider' => true,
                    'url' => 'javascript:;'),
                    array('dropdown_title' => 'Ecology and Information',
                    'url' => './piping_plover_info.php'),
                    array('dropdown_title' => 'Training Videos (Coming Soon)',
                    'url' => 'javascript:;'),
                )
            );

    }


    if ($user) {    //user is logged in
        $url_tokens = csg_url_tokens($user['authenticator']);
        $navbar_info['right_headers'][] =
                            array(
                                'dropdown_title' => $user['name'],
                                'url' => '#',
                                'classes' => '',
                                'dropdowns' => array(
                                    array('dropdown_title' => 'Applications',
                                          'url' => "$base_dir/apps.php"),
                                    array('dropdown_title' => 'Certificate',
                                          'url' => "$base_dir/cert1.php"),
                                    array('dropdown_title' => 'Link Accounts',
                                          'url' => "$base_dir/link_accounts.php"),
                                    array('dropdown_title' => 'Teams',
                                          'url' => "$base_dir/team.php"),
                                    array('dropdown_title' => 'Your Account',
                                          'url' => "$base_dir/home.php"),
                                    array('dropdown_title' => '',
                                          'divider' => true,
                                          'url' => 'javascript:;'),
                                    array('dropdown_title' => 'Log Out',
                                          'url' => "$base_dir/logout.php?$url_tokens"),
                                )
                             );

    } else {
        $navbar_info['right_headers'][] =
                            array(
                                'dropdown_title' => 'Your Account',
                                'url' => '#',
                                'classes' => '',
                                'dropdowns' => array(
                                    array('dropdown_title' => 'Log In',
                                          'url' => "$base_dir/login_form.php"),
                                    array('dropdown_title' => 'Create Account',
                                          'url' => "$base_dir/create_account_form.php?next_url=")
                               )
                             );
    }

    if ($active_title != "") {
        for ($i = 0; $i < count($navbar_info['left_headers']); $i++) {
    //        error_log("comparing title '" . $navbar_info['left_headers'][$i]['title'] . "' to '$active_title'");
            if ((array_key_exists('title', $navbar_info['left_headers'][$i]) && $navbar_info['left_headers'][$i]['title'] == $active_title) || 
                (array_key_exists('dropdown_title', $navbar_info['left_headers'][$i]) && $navbar_info['left_headers'][$i]['dropdown_title'] == $active_title)) {
                $navbar_info['left_headers'][$i]['classes'] .= " active";
            }
        }

        for ($i = 0; $i < count($navbar_info['right_headers']); $i++) {
    //        error_log("comparing title '" . $navbar_info['right_headers'][$i]['title'] . "' to '$active_title'");
            if ((array_key_exists('title', $navbar_info['right_headers'][$i]) && $navbar_info['right_headers'][$i]['title'] == $active_title) || 
                (array_key_exists('dropdown_title', $navbar_info['right_headers'][$i]) && $navbar_info['right_headers'][$i]['dropdown_title'] == $active_title)) {
                $navbar_info['right_headers'][$i]['classes'] .= " active";
            }
        }
    }

    if ($project_name == "Wildlife@Home" && $user && csg_is_special_user($user)) {

        $result = query_wildlife_video_db("SELECT count(*) FROM timed_observations WHERE report_status = 'REPORTED'");
        $row = $result->fetch_assoc();
        $waiting_review = $row['count(*)'];

        if ($waiting_review == 0) {
            $waiting_review = "";
        } else {
            $waiting_review = " (" . $waiting_review . ")";
        }
        $navbar_info['right_headers'][0]['dropdown_title'] .= $waiting_review;
        $navbar_info['right_headers'][0]['dropdowns'][1]['dropdown_title'] .= $waiting_review;
    }

    $navbar_template = file_get_contents($cwd[__FILE__] . "/templates/navbar_template.html");

    $m = new Mustache_Engine;
    echo $m->render($navbar_template, $navbar_info);
}

?>
