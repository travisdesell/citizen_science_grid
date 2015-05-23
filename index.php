<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname($cwd[__FILE__]);

require_once($cwd[__FILE__] . "/header.php");
require_once($cwd[__FILE__] . "/navbar.php");
require_once($cwd[__FILE__] . "/news.php");
require_once($cwd[__FILE__] . "/footer.php");
require_once($cwd[__FILE__] . "/my_query.php");
require_once($cwd[__FILE__] . "/csg_uotd.php");

print_header("The Citizen Science Grid");
print_navbar("Citizen Science Grid");

echo "
    <div class='container'>
      <div class='row'>

        <div class='col-sm-8'>";


$projects_info['projects'][] = array(
    'project_image' => 'http://solarviews.com/raw/earth/bluemarblewest.jpg',
    'project_name' => 'Climate Tweets', 
    'project_url' => './climate', 
    'project_text' => "The Climate Tweets project is focused on personal opinions about climate change or global warming. The goal is to sort tweets and view the different views in various countries, how the discussion has changed over time, and how opinions change with political orientation. Classifying tweets allows us to discover patterns and coorelations in people's opinions about our world. It also helps us understand what people know about climate change. Please note that the tweets are unfiltered and may contain profanity or controversial views, and these are not the views of the Citizen Science Grid, any of our team, or funding agencies.  Because of this the project is <font color='red'>18+</font>.");

$projects_info['projects'][] = array(
    'project_image' => './dna/images/DNA_Double_Helix.png',
    'project_name' => 'DNA@Home', 
    'project_url' => './dna', 
    'project_text' => "The goal of DNA@Home is to discover what regulates the genes in DNA. Ever notice that skin cells are different from a muscle cells, which are different from a bone cells, even though all these cells have every gene in your genome? That's because not all genes are \"on\" all the time. Depending on the cell type and what the cell is trying to do at any given moment, only a subset of the genes are used, and the remainder are shut off. DNA@home uses statistical algorithms to unlock the key to this differential regulation, using your volunteered computers.");


$projects_info['projects'][] = array(
    'project_image' => 'images/spiral.jpg',
    'project_name' => 'SubsetSum@Home', 
    'project_url' => './subset_sum', 
    'project_text' => "The Subset Sum problem is described as follows: given a set of positive integers S and a target sum t, is there a subset of S whose sum is t? It is one of the well-know, so-called \"hard\" problems in computing. It's actually a very simple problem computationally, and the computer program to solve it is not extremely complicated. What's hard about it is the running time – all known exact algorithms have running time that is proportional to an exponential function of the number of elements in the set (for worst-case instances of the problem).");


$projects_info['projects'][] = array(
    'project_image' => './wildlife/images/hatch.png',
    'project_name' => 'Wildlife@Home', 
    'project_url' => './wildlife', 
    'project_text' => "Wildlife@Home is <i>citizen science project</i> aimed at analyzing video gathered from various cameras recording wildlife.  Currently the project is looking at video of <a href='http://csgrid.org/csg/wildlife/sharptailed_grouse_info.php'>sharp-tailed grouse</a>, <i>Tympanuchus phasianellus</i>, and two federally protected species, <a href='http://csgrid.org/csg/wildlife/least_tern_info.php'>interior least terns</a>, <i>Sternula antillarum</i>, and <a href='http://csgrid.org/csg/wildlife/piping_plover_info.php'>piping plovers</a>, <i>Charadruis melodus</i> to examine their nesting habits and ecology.");

shuffle($projects_info['projects']);

$projects_template = file_get_contents($cwd[__FILE__] . "/templates/index_info_template.html");

$m = new Mustache_Engine;
echo $m->render($projects_template, $projects_info);

echo "
        </div> <!-- col-sm-8 -->

        <div class='col-sm-4'>";

/*
echo "
    <div class='well'>
    <h3>Notice for DNA@Home and SubsetSum@Home Users</h3>
    <p>DNA@Home, SubsetSum@Home and Wildlife@Home are now sub-projects of <a href='../csg/'>Citizen Science Grid</a>. All workunits for these sub-projects will be sent out from the Citizen Science Grid project.  You can link your old DNA@Home and SubsetSum@home accounts to your account on Citizen Science Grid by visiting the <a href='../csg/link_accounts.php'>link accounts</a> webpage. This will copy the credit over from the old projects to your account here. You'll need to detach your BOINC client from these old projects and attach to Citizen Science Grid.</p>
    </div>
    ";
 */

show_uotd(3, 9, "", true);
csg_show_news();

echo "
        </div> <!-- col-sm-4 -->

        </div> <!-- row -->
    </div> <!-- /container -->";



print_footer('Travis Desell, the DNA@Home, SubsetSum@Home and Wildlife@Home Teams', 'Travis Desell, Archana Dhasarathy, Susan Ellis-Felege, Tom O\'Neil, Sergei Nechaev');

echo "</body></html>";

?>
