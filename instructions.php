<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname($cwd[__FILE__]);

require_once($cwd[__FILE__] . '/header.php');
require_once($cwd[__FILE__] . '/navbar.php');
require_once($cwd[__FILE__] . '/footer.php');

print_header("Citizen Science Grid: Volunteer Computing (BOINC) Instructions");
print_navbar("CSG Information");

echo "
    <div class='container'>

    <div class='well'>
        <h3>The Citizen Science Grid and BOINC</h3>
        <p>The Citizen Science Grid uses the <a href='http://boinc.berkeley.edu'>Berkeley Open Infrastructure for Network Computing (BOINC)</a> for volunteer computing. You can download and install BOINC, attach to our project, and volunteer your computer to aid us in using computer vision algorithms to find out what is happening in the video gathered by our field biologists. Eventually, we will use these volunteered computers to filter this video, so that the video we show to our users contains mostly interesting events.
        </p>
    </div>

    <div class='well'>
        <h3>Volunteer Computing (BOINC) Instructions</h3>
        <ul>
        <li>  If you're already running BOINC, select Attach to Project. If not, <a target='_new' href='http://boinc.berkeley.edu/download.php'>download, install and run BOINC</a>. </li>
        <li> When prompted, enter <b>http://csgrid.org/csg/</b></li>
        <li> If you're running a command-line or pre-5.0 version of BOINC, <a href='create_account_form.php'>create an account</a> first. </li>
        <li> If you have any problems, <a href='http://boinc.berkeley.edu/help.php'>get help here</a>. </li>
        </ul>
    </div>

    <div class='well'>
        <h3>Linking DNA@Home and SubsetSum@Home Accounts</h3>
        <p>If you had an account on DNA@Home or SubsetSum@Home before those projects were unified under Citizen Science Grid, you can link your Citizen Science Grid account to those accounts <a href='link_accounts.php'>here</a>. Wildlife@Home users are already within Citizen Science Grid and do not have to do this. When your account is linked, you will continue to earn credit for that account on DNA@Home and/or SubsetSum@Home, when you successfully complete workunits for those applications on Citizen Science Grid.
        </p>
    </div>


    <div class='well'>
        <h3>Rules and Policies</h3>

        <h4>Eligability</h4>
        <p><!-- In order to participate in Citizen Science Grid, you must be at least 13 years of age. !--> Persons younger than 18 must obtain permission from a parent or legal guardian to participate in Citizen Science Grid. Your participation in Citizen Science Grid signifies that you have read, are familiar with, and agree to be bound by these rules and policies.

        <h4>Run Citizen Science Grid only on authorized computers</h4>
            <p>Citizen Science Grid requires the use of a computer. Run Citizen Science Grid only on computers that you own, or for which you have obtained the owner's permission. Some companies and schools have policies that prohibit using their computers for projects such as Citizen Science Grid.</p>

        <h4>How Citizen Science Grid will use your computer</h4>
            <p>When you run Citizen Science Grid on your computer, it will use part of the computer's CPU power, disk space, and network bandwidth. You can control how much of your resources are used by Citizen Science Grid, and when it uses them. Part of the project (observing wildlife videos for Wildlife@Home) simply requires you to stream information via a web browser.</p>
            <p>The work done by your computer contributes to the goals of Citizen Science Grid, as described on its web site. The application programs may change from time to time.</p>

        <h4>Privacy policy</h4>
            <p>Your account on Citizen Science Grid is identified by a name that you choose. This name may be shown on the Citizen Science Grid web site, along with a summary of the work your computer has done for Citizen Science Grid. If you want to be anonymous, choose a name that doesn't reveal your identity.</p>

            <p>If you participate in Citizen Science Grid, information about your computer (such as its processor type, amount of memory, etc.) will be recorded by Citizen Science Grid and used to determine compatibility or to decide what type of work to assign to your computer. This information will also be shown on Citizen Science Grid's web site. Nothing that reveals your computer's location (e.g. its domain name or network address) will be shown.</p>

            <p>To participate in Citizen Science Grid, you must give an address where you receive email. This address will not be shown on the Citizen Science Grid web site or shared with organizations. Citizen Science Grid may send you periodic newsletters; however, you can opt out at any time.</p>

            <p>Private messages sent on the Citizen Science Grid web site are visible only to the sender and recipient.  Citizen Science Grid does not examine or police the content of private messages.  If you receive unwanted private messages from another Citizen Science Grid user, you may add them to your <a href='edit_forum_preferences_form.php'>message filter</a>.  This will prevent you from seeing any public or private messages from that user. </p>

            <p>If you use our web site forums you must follow the <a href=moderation.php>posting guidelines</a>.  Messages posted to the Citizen Science Grid forums are visible to everyone, including non-members.  By posting to the forums, you are granting irrevocable license for anyone to view and copy your posts. </p>

        <h4>Is it safe to run Citizen Science Grid?</h4></p>
            <p>Any time you download a program through the Internet you are taking a chance: the program might have dangerous errors, or the download server might have been hacked. Citizen Science Grid has made efforts to minimize these risks. We have tested our applications carefully. Our servers are behind a firewall and are configured for high security. To ensure the integrity of program downloads, all executable files are digitally signed on a secure computer not connected to the Internet.</p>
            <p>The applications run by Citizen Science Grid may cause some computers to overheat. If this happens, stop running Citizen Science Grid or use a <a href='download_network.php'>utility program</a> that limits CPU usage.</p>

        <h4>Credits</h4>
            <p>Wildlife@Home and SubsetSum@Home are being developed by the Citizen Science Grid teams at the <a href='http://und.edu'>University of North Dakota</a>. DNA@Home was originally developed at <a href='http://rpi.edu'>Rensselaer Polytechnic Insitute</a> and development has now moved to the University of North Dakota. <a href='http://boinc.berkeley.edu'>BOINC</a> was developed at the <a href='http://berkeley.edu/index.html'> University of California, Berkeley</a>.</p>

        <h4>Liability</h4>
            <p>Citizen Science Grid and the Citizen Science Grid team assume no liability for, and you hereby release them from any and all claims for or arising out of, damage to your computer, loss of data, or any other event or condition that may occur as a result of participating in Citizen Science Grid.</p>

            <p>Your participation is voluntary, no employment relationship of any kind is intended nor should be inferred, and you are not entitled to any compensation or recognition of any kind.  Citizen Science Grid reserves the right, in its sole discretion, to acknowledge the participation and contributions of volunteers in future publications, productions, statements, press releases, announcements or any other communications, regardless of medium.</p>

            <p>Any and all disputes arising under your participation in Citizen Science Grid shall be subject to North Dakota law and any lawsuits shall be filed in the Northeast Central Judicial District Court of North Dakota.</p>

        <h4>Copyrighted materials and limited license</h4>
            <p>Citizen Science Grid content and viewing platforms are subject to one or more copyrights.  With respect to Citizen Science Grid content, you are granted a limited license by virtue of, and for the sole purpose of, your participation.  This license includes the right to privately view but not to publicly distribute or share Citizen Science Grid videos and data.  <b>No research publications based on Citizen Science Grid content or data should be made without written consent from Dr. Desell and the other project leaders.</b> You are solely responsible for any unauthorized use or distribution by you, or under your direction, of copyrighted materials.</p>

        <h4>Other BOINC projects</h4>
            <p>Other projects use the same platform, BOINC, as Citizen Science Grid. You may want to consider participating in one or more of these projects. By doing so, your computer will do useful work even when Citizen Science Grid has no work available for it.</p>
            <p>These other projects are not associated with Citizen Science Grid, and we cannot vouch for their security practices or the nature of their research. Join them at your own risk.</p>
    </div>
    </div>  <!--container-->
";


print_footer('Travis Desell, the DNA@Home, SubsetSum@Home and Wildlife@Home Teams', 'Travis Desell, Archana Dhasarathy, Susan Ellis-Felege, Tom O\'Neil, Sergei Nechaev');

echo "
</body>
</html>
";

?>
