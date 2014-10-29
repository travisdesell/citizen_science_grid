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

print_header("The University of North Dakota Citizen Science Grid");
print_navbar("Citizen Science Grid");

echo "
    <div class='container'>
      <div class='row'>

        <div class='col-sm-8'>";

include $cwd[__FILE__] . "/templates/index_info.html";

echo "
        </div> <!-- col-sm-8 -->

        <div class='col-sm-4'>";

echo "
    <div class='well'>
    <h3>Notice for DNA@Home and SubsetSum@Home Users</h3>
    <p>DNA@Home and SubsetSum@Home are transitioning to sub-projects of <a href='../csg/'>Citizen Science Grid</a>. All workunits for these sub-projects will be sent out from the Citizen Science Grid project.  You can link your old DNA@Home and SubsetSum@home accounts to your account on Citizen Science Grid by visiting the <a href='../csg/link_accounts.php'>link accounts</a> webpage. This will copy the credit over from the old projects to your account here.</p>
    </div>
    ";


show_uotd(3, 9);
csg_show_news();

echo "
        </div> <!-- col-sm-4 -->

        </div> <!-- row -->
    </div> <!-- /container -->";



print_footer('Travis Desell, the DNA@Home, SubsetSum@Home and Wildlife@Home Teams', 'Travis Desell, Archana Dhasarathy, Susan Ellis-Felege, Tom O\'Neil, Sergei Nechaev');

echo "</body></html>";

?>
