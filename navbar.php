<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname($cwd[__FILE__]);

require_once($cwd[__FILE__] . "/user.php");

require_once $cwd[__FILE__] . '/../mustache.php/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();

function print_navbar($active_title, $project_name = "Citizen Science Grid") {
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
                                  'url' => 'http://volunteer.cs.und.edu/dna'),
                            array('dropdown_title' => 'SubsetSum@Home',
                                  'url' => 'http://volunteer.cs.und.edu/subset_sum'),
                            array('dropdown_title' => 'Wildlife@Home',
                            'url' => 'http://volunteer.cs.und.edu/wildlife')
                        ),

                       'left_headers' => array(
                            array(
                                'dropdown_title' => 'Information',
                                'classes' => '',
                                'dropdowns' => array(
                                    array('dropdown_title' => 'Instructions, Rules & Policies',
                                          'url' => '../csg/instructions.php'),
                                    array('dropdown_title' => 'Languages',
                                          'url' => '../csg/language_select.php'),
                                    array('dropdown_title' => 'Profiles',
                                          'url' => '../csg/profile_menu.php'),
                                    array('dropdown_title' => 'Publications',
                                          'url' => '../csg/publications.php'),
                                    array('dropdown_title' => 'Server Status',
                                          'url' => '../csg/server_status.php'),
                                    array('dropdown_title' => 'User Search',
                                          'url' => '../csg/user_search.php')
                                 )
                            ),

                            array(
                                'dropdown_title' => 'Top Lists',
                                'classes' => '',
                                'dropdowns' => array(
                                    array('dropdown_title' => 'Combined Top Users',
                                          'url' => '../csg/combinder_top_users.php'),
                                    array('dropdown_title' => 'Combined Top Hosts',
                                          'url' => '../csg/combinder_top_hosts.php'),
                                    array('dropdown_title' => 'Combined Top Teams',
                                          'url' => '../csg/combinder_top_teams.php'),
                                    array('dropdown_title' => 'More Statistics',
                                          'url' => '../csg/stats.php'),
                                    array('dropdown_title' => 'DNA@Home',
                                          'divider' => true,
                                          'url' => 'javascript:;'),
                                    array('dropdown_title' => 'Top Users',
                                          'url' => 'http://volunteer.cs.und.edu/dna/top_users.php'),
                                    array('dropdown_title' => 'Top Hosts',
                                          'url' => 'http://volunteer.cs.und.edu/dna/top_hosts.php'),
                                    array('dropdown_title' => 'Top Teams',
                                          'url' => 'http://volunteer.cs.und.edu/dna/top_teams.php'),
                                    array('dropdown_title' => 'SubsetSum@Home',
                                          'divider' => true,
                                          'url' => 'javascript:;'),
                                    array('dropdown_title' => 'Top Users',
                                          'url' => 'http://volunteer.cs.und.edu/subset_sum/top_users.php'),
                                    array('dropdown_title' => 'Top Hosts',
                                          'url' => 'http://volunteer.cs.und.edu/subset_sum/top_hosts.php'),
                                    array('dropdown_title' => 'Top Teams',
                                          'url' => 'http://volunteer.cs.und.edu/subset_sum/top_teams.php'),
                                    array('dropdown_title' => 'Wildlife@Home',
                                          'divider' => true,
                                          'url' => 'javascript:;'),
                                    array('dropdown_title' => 'Top Users',
                                          'url' => 'http://volunteer.cs.und.edu/wildlife/top_users.php'),
                                    array('dropdown_title' => 'Top Hosts',
                                          'url' => 'http://volunteer.cs.und.edu/wildlife/top_hosts.php'),
                                    array('dropdown_title' => 'Top Teams',
                                          'url' => 'http://volunteer.cs.und.edu/wildlife/top_teams.php'),
                                    array('dropdown_title' => 'Top Bird Watchers',
                                          'url' => 'http://volunteer.cs.und.edu/wildlife/top_bossa_users.php'),
                                    array('dropdown_title' => 'Top Bird Watching Teams',
                                          'url' => 'http://volunteer.cs.und.edu/wildlife/top_bossa_teams.php')
                               )
                            ),

                            array(
                                'title' => 'Message Boards',
                                'url' => '../csg/forum_index.php',
                                'classes' => ''
                            )

                       ),

                       'right_headers' => array()
                    );

    if ($project_name == "DNA@Home") {
        $user = dna_get_user(false);
        $navbar_info['project_name'] = "DNA@Home";
    } else if ($project_name == "SubsetSum@Home") {
        $user = sss_get_user(false);
        $navbar_info['project_name'] = "SubsetSum@Home";
    } else if ($project_name == "Wildlife@Home") {
        $user = csg_get_user(false);
        $navbar_info['project_name'] = "Wildlife@Home";
    } else {
        $user = csg_get_user(false);
        $navbar_info['project_name'] = "Citizen Science Grid";
    }

    if ($project_name == "Wildlife@Home") {
        $navbar_info['right_headers'][] =
            array(
                'dropdown_title' => 'Wildlife Video',
                'url' => '#',
                'classes' => '',
                'dropdowns' => array(
                    array('dropdown_title' => 'Site and Species Descriptions',
                    'url' => '../wildlife/video_selector.php'),
                    array('dropdown_title' => 'Review Videos',
                    'url' => '../wildlife/review_videos.php'),
                    array('dropdown_title' => 'Sharp-Tailed Grouse',
                    'divider' => true,
                    'url' => 'javascript:;'),
                    array('dropdown_title' => 'Belden, ND',
                    'url' => '../wildlife/watch.php?location=1&species=1'),
                    array('dropdown_title' => 'Blaisdell, ND',
                    'url' => '../wildlife/watch.php?location=2&species=1'),
                    array('dropdown_title' => 'Lostwood Wildlife Refuge, ND',
                    'url' => '../wildlife/watch.php?location=3&species=1'),
                    array('dropdown_title' => 'Interior Least Tern',
                    'divider' => true,
                    'url' => 'javascript:;'),
                    array('dropdown_title' => 'Missouri River, ND',
                    'url' => '../wildlife/watch.php?location=4&species=2'),
                    array('dropdown_title' => 'Piping Plover',
                    'divider' => true,
                    'url' => 'javascript:;'),
                    array('dropdown_title' => 'Missouri River, ND',
                    'url' => '../wildlife/watch.php?location=4&species=3')
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
                    'url' => '../wildlife/sharptailed_grouse_info.php'),
                    array('dropdown_title' => 'Training Videos',
                    'url' => '../wildlife/sharptailed_grouse_training.php'),

                    array('dropdown_title' => 'Interior Least Tern',
                    'divider' => true,
                    'url' => 'javascript:;'),
                    array('dropdown_title' => 'Ecology and Information',
                    'url' => '../wildlife/least_tern_info.php'),
                    array('dropdown_title' => 'Training Videos (Coming Soon)',
                    'url' => 'javascript:;'),

                    array('dropdown_title' => 'Piping Plover',
                    'divider' => true,
                    'url' => 'javascript:;'),
                    array('dropdown_title' => 'Ecology and Information',
                    'url' => '../wildlife/piping_plover_info.php'),
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
                                          'url' => 'apps.php'),
                                    array('dropdown_title' => 'Certificate',
                                          'url' => 'cert1.php'),
                                    array('dropdown_title' => 'Link Accounts',
                                          'url' => 'link_accounts.php'),
                                    array('dropdown_title' => 'Teams',
                                          'url' => 'team.php'),
                                    array('dropdown_title' => 'Your Account',
                                          'url' => 'home.php'),
                                    array('dropdown_title' => '',
                                          'divider' => true,
                                          'url' => 'javascript:;'),
                                    array('dropdown_title' => 'Log Out',
                                          'url' => "logout.php?$url_tokens"),
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
                                          'url' => '../csg/login_form.php'),
                                    array('dropdown_title' => 'Create Account',
                                          'url' => '../csg/create_account_form.php?next_url=')
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
