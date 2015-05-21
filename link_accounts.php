<?php

$cwd[__FILE__] = __FILE__;
if (is_link($cwd[__FILE__])) $cwd[__FILE__] = readlink($cwd[__FILE__]);
$cwd[__FILE__] = dirname($cwd[__FILE__]);

require_once($cwd[__FILE__] . "/header.php");
require_once($cwd[__FILE__] . "/navbar.php");
require_once($cwd[__FILE__] . "/footer.php");
require_once($cwd[__FILE__] . "/my_query.php");
require_once($cwd[__FILE__] . "/user.php");

print_header("Citizen Science Grid: Link Accounts", "<script src='./js/link_accounts.js'></script>");
print_navbar("Citizen Science Grid");

echo "
    <div class='container'>
        <div class='row'>";

$user = csg_get_user(true);

$csg_username = $user['name'];
$csg_email = $user['email_addr'];


//if account already linked, show what it's linked to
//if not linked, show what it would be linked to (if there is a matching email)
//if not linked and no potential link, report message about changing other email address

//dna_linked, dna_userid, dna_username, subset_sum_linked, subset_sum_userid, subset_sum_username
$dna_linked = $user['dna_linked'];
$dna_can_link = 0;
$dna_username = "";
$dna_userid = "";
if ($dna_linked == 0) {
    $other_result = query_dna_db("SELECT id, name, email_addr FROM user WHERE email_addr = '$csg_email'");
    $other_row = $other_result->fetch_assoc();

    if ($other_row) {
        $dna_username = $other_row['name'];
        $dna_userid = $other_row['id'];
        $dna_email = $other_row['email_addr'];
        $dna_can_link = 1;

    } else {
        $dna_can_link = 0;
    }
} else {
    $dna_username = $user['dna_username'];
    $dna_userid = $user['dna_userid'];
}

$sss_linked = $user['subset_sum_linked'];
$sss_can_link = 0;
$sss_username = "";
$sss_userid = "";
if ($sss_linked == 0) {
    $other_result = query_subset_sum_db("SELECT id, name, email_addr FROM user WHERE email_addr = '$csg_email'");
    $other_row = $other_result->fetch_assoc();

    if ($other_row) {
        $sss_username = $other_row['name'];
        $sss_userid = $other_row['id'];
        $sss_email = $other_row['email_addr'];
        $sss_can_link = 1;

    } else {
        $sss_can_link = 0;
    }
} else {
    $sss_username = $user['subset_sum_username'];
    $sss_userid = $user['subset_sum_userid'];
}


function print_link_well($linked, $can_link, $project_name, $project, $userid, $username, $email) {
    if ($linked == 1) {
        echo "
            <div class='well'>
            <p>Your Citizen Science Grid account has been successfully linked to the following $project_name account.</p>
                <form>
                    <div class='row'>
                    <input type='hidden' id='InputUserid' value='$userid'>
                        <div class='col-xs-4'>
                            <div class='input-group col-xs-12'>
                                <span class='input-group-addon'>Username</span>
                                <input type='text' class='form-control disabled' readonly value='$username'>
                            </div>
                        </div>
                        <div class='col-xs-4'>
                            <div class='input-group col-xs-12'>
                                <span class='input-group-addon'>Email</span>
                                <input type='text' class='form-control disabled' readonly value='$email'>
                            </div>
                        </div>
                        <div class='col-xs-4'>
                            <div class='input-group'>
                                <button type='button' project='$project' class='btn btn-primary link-accounts-button disabled'>Account Linked</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>";

    } else {
        if ($can_link == 1) {
            echo "
                <div class='well'>
                    <p>The following account on $project_name was found with a matching email. You can link to this account such that its credit is linked to your Citizen Scienc Grid account.  All credit you earn on Citizen Science Grid for the $project_name application will be added to this account. Note that you can only do this once and it is not reversible.</p>
                    <form>
                        <div class='row'>
                        <input type='hidden' id='" . $project . "InputUserid' value='$userid'>
                            <div class='col-xs-4'>
                                <div class='input-group col-xs-12'>
                                    <span class='input-group-addon'>Username</span>
                                    <input type='text' id='" . $project . "InputUsername' class='form-control'  readonly value='$username'>
                                </div>
                            </div>
                            <div class='col-xs-4'>
                                <div class='input-group col-xs-12'>
                                    <span class='input-group-addon'>Email</span>
                                    <input type='text' id='" . $project . "InputEmail' class='form-control'  readonly value='$email'>
                                </div>
                            </div>
                            <div class='col-xs-4'>
                                <div class='input-group'>
                                    <button type='button' project='$project' class='btn btn-primary link-accounts-button'>Link Account</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>";

        } else {
            echo "
                <div class='well'>
                    <p>No account on $project_name was found with a matching email. You can only link accounts with matching emails.  If you want to change your email on $project_name, you can do so on the <a href='http://csgrid.org/$project/home.php'>Your Account</a> page at $project_name.</p>
                </div>";
        }
    }
}

print_link_well($dna_linked, $dna_can_link, "DNA@Home", "dna", $dna_userid, $dna_username, $csg_email);

print_link_well($sss_linked, $sss_can_link, "SubsetSum@Home", "subset_sum", $sss_userid, $sss_username, $csg_email);

echo "  </div> <!-- row -->
    </div> <!-- /container -->";



print_footer('Travis Desell, the DNA@Home, SubsetSum@Home and Wildlife@Home Teams', 'Travis Desell, Archana Dhasarathy, Susan Ellis-Felege, Tom O\'Neil, Sergei Nechaev');

echo "</body></html>";

?>
