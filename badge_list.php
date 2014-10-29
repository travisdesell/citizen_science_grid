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
require_once($cwd[__FILE__] . "/display_badges.php");

print_header("Citizen Science Grid: Badges");
print_navbar("Citizen Science Grid");

echo "<div class='container'>";
echo "<div class='row'>";
echo "<div class='col-sm-12' style='margin-left:0px'>";
echo "<div class='well' style='padding-top:10px; padding-bottom:10px;'>";

echo "<p style='margin-top:5px; margin-bottom:5px;'>You can earn different badges for watching video or volunteering your computer to crunch workunits.  This page describes the different badges and how you can unlock them.</p>";
echo "</div>";

print_badge_table();

echo "</div>";
echo "</div>";
echo "</div>";

print_footer('Travis Desell, the DNA@Home, SubsetSum@Home and Wildlife@Home Teams', 'Travis Desell, Archana Dhasarathy, Susan Ellis-Felege, Tom O\'Neil, Sergei Nechaev');

echo "
</body>
</html>
";

?>
