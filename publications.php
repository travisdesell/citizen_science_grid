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

print_header("Citizen Science Grid Publications");
print_navbar("Citizen Science Grid");

echo"
   <div class='container'>
    <div class='row'>
        <div class='col-sm-12'>
<div class='well'>
    <h2>DNA@Home Publications</h2>
    <hr class='news-hr'>

    <h3>Conference Proceedings</h3>

    <ul>
        <li>Travis Desell, Lee A. Newberg, Malik Magdon-Ismail, Boleslaw K. Szymanski and William Thompson. <b>Finding Protein Binding Sites Using Volunteer Computing Grids</b>. <i>In the 2011 2nd International Congress on Computer Applications and Computational Science (CACS 2011)</i>. Bali, Indonesia. November 15-17, 2011. <a href='http://people.aero.und.edu/~tdesell/papers/2011_cacs.pdf'>[pdf]</a></li>
    </ul>

    <h3>Oral Presentations</h3>

    <ul>
        <li>Travis Desell. <b>DNA@Home: Using Volunteered Computers for Finding Transcription Factor Binding Sites</b>. <i>UND Epigenetics and Epigenomics Symposium</i>. University of North Dakota, Grand Forks, ND. November 15, 2012. <a href='http://people.aero.und.edu/~tdesell/talks/2012_nov_15_und_epigenetics.ppt'>[ppt]</a> <a href='http://people.aero.und.edu/~tdesell/talks/2012_nov_15_und_epigenetics.key'>[keynote]</a> </li>

        <li>Travis Desell. <b>Finding Protein Binding Sites using Volunteer Computing Grids</b>. <i>The 2011 2nd International Congress on Computer Applications and Computational Science (CACS 2011)</i>. Bali, Indonesia. November 15-17, 2011. <a href='http://people.aero.und.edu/~tdesell/talks/2011_nov_15_cacs.ppt'>[ppt]</a> <a href='http://people.aero.und.edu/~tdesell/talks/2011_nov_15_cacs.key'>[keynote]</a></li>
    </ul>
</div>
<div class='well'>
    <h2>SubsetSum@Home Publications</h2>
    <hr class='news-hr'>

    We are currently preparing our first SubsetSum@Home publication.
</div>
<div class='well'>
    <h2>Wildlife@Home Publications</h2>
    <hr class='news-hr'>

    <h3>Press</h3>

    <ul>
        <li><b>Watching Wildlife at Home</b>. <i>UND Arts and Sciences Feature</i>. <a href='http://arts-sciences.und.edu/features/2014/01/watching-wildlife.cfm'>[html]</a></li>
    </ul>

    <h3>Journal Articles</h3>

    <ul>
        <li>Susan N. Ellis-Felege, Travis Desell, Christopher J. Felege. <b>A Bird's Eye View of... Birds: Combining Technology and Citizen Science for Conservation</b>. <i>Wildlife Professional 8: 27-30</i>. Spring 2014. <a href='./publications/birds_eye_view.pdf'>[pdf]</a> courtsey of <a href='http://www.wildlife.org/publications/twp'>The Wildlife Professional.</a></li>
    </ul>

    <h3>Conference Proceedings</h3>

    <ul>
        <li>Travis Desell, Kyle Goehner, Alicia Andes, Rebecca Eckroad, and Susan Ellis-Felege. <b>On the Effectiveness of Crowd Sourcing Avian Nesting Video Analysis at Wildlife@Home</b>. <i>In the 15th International Conference on Computational Science</i>. Reykjavík, Iceland. June 1-3, 2015. <a href='http://volunteer.cs.und.edu/csg/wildlife/publications/2015_iccs_wildlife.pdf'>[pdf]</a>. <b>To Appear.</b> </li>

        <li>Travis Desell, Robert Bergman, Kyle Goehner, Ronald Marsh, Rebecca VanderClute, and Susan Ellis-Felege. <b>Wildlife@Home: Combining Crowd Sourcing and Volunteer Computing to Analyze Avian Nesting Video</b>. <i>In the 2013 IEEE 9th International Conference on e-Science</i>. Beijing, China. October 23-25, 2013. <a href='http://people.cs.und.edu/~tdesell/papers/2013_escience_wildlife.pdf'>[pdf]</a></li>
    </ul>

    <h3>Oral Presentations</h3>

    <ul>
        <li>Kyle Goehner and Travis Desell. <b>Computer Vision to Aid in WIldlife Surveillance</b>. <i>The 2014 ND EPSCoR/IDeA State Conference</i>. 29 April 2014, Grand Forks, ND. <a href='publications/2014_nd_epscor_presentation.pdf'>[pdf]</a> </li>

        <li>Susan N. Ellis-Felege, Travis Desell, and Christopher J. Felege. <b>Wildlife@Home: Conservation Outreach Using Nest Cameras, Citizen Science and Computer vision</b>. <i>The North Dakota Chapter of the Wildlife Society Conference</i>. 12-14 February 2014, Mandan, ND. <a href='publications/felege_conservation_outreach_talk.pdf'>[pdf]</a> </li>

        <li>Travis Desell, Robert Bergman, Kyle Goehner, Ronald Marsh, Rebecca VanderClute, and Susan Ellis-Felege. <b>Wildlife@Home: Combining Crowd Sourcing and Volunteer Computing to Analyze Avian Nesting Video</b>. <i>The 9th International Conference on E-Science (e-Science 2013)</i>. Beijing, China. October 23, 2013. <a href='http://people.cs.und.edu/~tdesell/talks/2013_october_23_escience/index.html'>[html]</a> </li>

        <li>Travis Desell and Susan N. Ellis-Felege. <b>Wildlife@Home</b>. <i>The 8th International BOINC Workshop</i>. University of Westminster, London, UK. September 27, 2012. <a href='http://people.cs.und.edu/~tdesell/talks/2012_boinc_workshop.ppt.zip'>[ppt]</a> <a href='http://people.cs.und.edu/~tdesell/talks/2012_boinc_workshop.key'>[keynote]</a> </li>

        <li>Travis Desell and Susan N. Ellis-Felege. <b>Wildlife@Home</b>. <i>The UND Digital Media Showcase</i>. Fire Hall Theatre, Grand Forks, North Dakota, USA. April 11, 2012. <a href='http://people.cs.und.edu/~tdesell/talks/2012_und_digital_media_showcase.ppt.zip'>[ppt]</a> <a href='http://people.cs.und.edu/~tdesell/talks/2012_und_digital_media_showcase.key'>[keynote]</a> </li>


    </ul>

    <h3>Poster Presentations</h3>
    <ul>
        <li>J. P. Johnson, Rebecca A. Eckroad,  Aaron C. Robinson, and Susan N. Ellis-Felege.  <b>Nest attendance patterns in sharp-tailed grouse in western North Dakota</b>.  <i>The North Dakota Chapter of the Wildlife Society Conference</i>. 12-14 February 2014, Mandan, ND. <a href='publications/RecessPoster_NDCTWS2014_Finalpdf.pdf'>[pdf]</a> </li>

        <li>Rebecca A. Eckroad, Paul C. Burr, Aaron C. Robinson, and Susan N. Ellis-Felege. <b>Impact of camera installation on nesting sharp-tailed grouse (Tympanuchus phasianellus) behavior in western North Dakota</b>.  <i>The North Dakota Chapter of the Wildlife Society Conference</i>. 12-14 February 2014, Mandan, ND. <a href='publications/Becca_ND_TWS2014_small.pdf'>[pdf]</a> </li>

        <li>Alicia K. Andes, Susan N. Ellis-Felege, Terry L. Shaffer, and Mark H. Sherfy.  <b>A video camera technique to monitor piping plover and least tern nests on the Missouri River in North Dakota</b>. <i>The North Dakota Chapter of the Wildlife Society Conference</i>. 12-14 February 2014, Mandan, ND. <b>Won most outstanding student poster award</b>. <a href='publications/Andes_ND_TWS_Poster_2014_small.pdf'>[pdf]</a> </li>

        <li>Leila Mohsenian, Alicia K. Andes, and Susan N. Ellis-Felege. <b>The mysterious life of piping plovers: nesting behaviors of a threatened species</b>. <i>The North Dakota Chapter of the Wildlife Society Conference</i>. 12-14 February 2014, Mandan, ND. <a href='publications/mysterious_life_poster.pdf'>[pdf]</a></li>

        <li>Julia P. Johnson, Rebecca A. Eckroad, Aaron C. Robinson, and Susan N. Ellis-Felege. <b>Nest attendance patterns in sharp-tailed grouse in western North Dakota</b>. <i>The Wildlife Society’s 20th Annual Conference</i>. 4 – 10 October 2013, Milwaukee, Wisconsin (Student- In- Progress Poster Presentation; won 2nd place in undergraduate presentation category). <a href='http://volunteer.cs.und.edu/wildlife/alpha/publications/2013_10_poster_nest_attendence_patterns.pdf'>[pdf]</a></li>
    </ul>

</div>

        </div> <!-- container -->
    </div> <!-- row -->
</div> <!-- col-sm-12 -->
";

print_footer('Travis Desell, the DNA@Home, SubsetSum@Home and Wildlife@Home Teams', 'Travis Desell, Archana Dhasarathy, Susan Ellis-Felege, Tom O\'Neil, Sergei Nechaev');

echo "
</body>
</html>
";


?>
