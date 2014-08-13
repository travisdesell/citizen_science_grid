<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname($cwd[__FILE__]);

require_once($cwd[__FILE__] . "/user.php");

require_once $cwd[__FILE__] . '/mustache.php/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();

function print_navbar($active_title, $project_name = "Citizen Science Grid") {
    global $cwd;

    $cwd[__FILE__] = __FILE__;
    if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
    $cwd[__FILE__] = dirname($cwd[__FILE__]);

    $navbar_info = array('project_name' => 'Citizen Science Grid',
                       'url' => 'http://volunteer.cs.und.edu/csg/',
                       'left_headers' => array(
                            array(
                                'dropdown_title' => 'Projects',
                                'classes' => '',
                                'dropdowns' => array(
                                    array('dropdown_title' => 'DNA@Home',
                                          'url' => 'http://volunteer.cs.und.edu/dna'),
                                    array('dropdown_title' => 'SubsetSum@Home',
                                          'url' => 'http://volunteer.cs.und.edu/subset_sum'),
                                    array('dropdown_title' => 'Wildlife@Home',
                                          'url' => 'http://volunteer.cs.und.edu/wildlife')
                                )
                            ),

                            array(
                                'dropdown_title' => 'CSG Information',
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
                                'title' => 'Message Boards',
                                'url' => '../csg/forum_index.php',
                                'classes' => ''
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
                                'title' => 'Contact',
                                'url' => 'mailto:tdesell@cs.und.edu',
                                'classes' => ''
                           )
                       ),

                       'right_headers' => array()
                    );

    if ($project_name == "DNA@Home") {
        $user = dna_get_user(false);
        $navbar_info['left_headers'][0]['dropdown_title'] .= ": DNA@Home";
    } else if ($project_name == "SubsetSum@Home") {
        $user = sss_get_user(false);
        $navbar_info['left_headers'][0]['dropdown_title'] .= ": SubsetSum@Home";
    } else {
        $user = csg_get_user(false);
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
                                    array('dropdown_title' => 'Your Preferences',
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
            if ($navbar_info['left_headers'][$i]['title'] == $active_title || $navbar_info['left_headers'][$i]['dropdown_title'] == $active_title) {
                $navbar_info['left_headers'][$i]['classes'] .= " active";
            }
        }

        for ($i = 0; $i < count($navbar_info['right_headers']); $i++) {
    //        error_log("comparing title '" . $navbar_info['right_headers'][$i]['title'] . "' to '$active_title'");
            if ($navbar_info['right_headers'][$i]['title'] == $active_title || $navbar_info['right_headers'][$i]['dropdown_title'] == $active_title) {
                $navbar_info['right_headers'][$i]['classes'] .= " active";
            }
        }
    }

    $navbar_template = file_get_contents($cwd[__FILE__] . "/templates/navbar_template.html");

    $m = new Mustache_Engine;
    echo $m->render($navbar_template, $navbar_info);
}

?>
