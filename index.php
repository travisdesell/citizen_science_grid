<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname($cwd[__FILE__]);

require_once($cwd[__FILE__] . "/header.php");
require_once($cwd[__FILE__] . "/navbar.php");
require_once($cwd[__FILE__] . "/news.php");
require_once($cwd[__FILE__] . "/footer.php");
require_once($cwd[__FILE__] . "/my_query.php");
require_once($cwd[__FILE__] . "/uotd.php");

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

show_uotd(3, 9);
show_news();

echo "
        </div> <!-- col-sm-4 -->

        </div> <!-- row -->
    </div> <!-- /container -->";



print_footer('Travis Desell, the DNA@Home, SubsetSum@Home and Wildlife@Home Teams', 'Travis Desell, Archana Dhasarathy, Susan Ellis-Felege, Tom O\'Neil, Sergei Nechaev');

echo "</body></html>";

?>
