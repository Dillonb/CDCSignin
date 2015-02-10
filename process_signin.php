<?php
require_once("UVMLdap.php");
define(ROOT_PATH, dirname(__FILE__));
function endsWith($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) == $needle;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{

    $tos_email_headers = "MIME-Version: 1.0\r\n";
    $tos_email_headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $tos_email_headers .= "From:(helpline@uvm.edu)";
    $tos_email_subject = "Thank you for visiting the UVM Tech Team";
    $tos_email_body = <<<EOT
<p>
    For your records, here is a copy of our Customer Terms of Service. You have not been added to an email list, this is a one time communication. Your provided email address will only be used to contact you in regards to the status of your machine while it is in the Computer Carry-in Clinic or Hardware center for repair
</p>
<hr>
<p>
    <strong>
        Tech Team and Client Services Hardware terms of service agreement:
    </strong>
</p>
<p>
    While Client Services and the Tech Team Computer Carry-in Clinic take data backup and loss-prevention very seriously, neither entity is held responsible for any data loss that may occur once a machine is checked in for service.  Reasonable effort will be made to minimize the chance of loss and, if possible, the Customer will be given the option to back up their data.
</p>
<p>
    The Tech Team and Client Services are not responsible for any other hardware failure that may occur while the machine is in our possession during normal use and repair.  The Tech Team and Client Services do their best to identify failed components, but other parts may fail or manifest as failed during the repair process. The Tech Team and/or Client Services will notify the Customer of their options, but cannot be held responsible for repairing or replacing failed components at the University’s expense.
</p>
<p>
    The Tech Team and Client Services takes precautionary steps to protect any computer equipment left in our care from loss or damage, even during transit between repair locations.  The Customer agrees not to hold the Tech Team or Client Services liable for loss, theft, or damage to the computer or any of the equipment/accessories left with the computer.
</p>
<p>
    The Tech Team and Client Services requires all equipment be picked up from our repair center within 30 days of notification of repair completion.  The Tech Team or Client Services will use Customer-supplied contact information to make repeated attempts to contact the Customer, roughly once per week.  Any equipment left past 30 days, without prior written agreement, will incur storage charges ($5 per business day) that will be added to the bill and payable on machine pickup.  If the Customer cannot be reached or fails to pick up the equipment in question within 45 total days after the initial notification for pickup, the equipment will be considered abandoned.  Once equipment is considered abandoned, it becomes the property of Client Services, and the University, to be used as spare parts or electronic waste for disposal/recycling.
</p>
EOT;

    //file_put_contents("signins.txt","Data: ".print_r($_POST,true)."\n",FILE_APPEND);
    $netid = htmlentities($_POST['netid'],ENT_QUOTES);
    $firstname = htmlentities($_POST['firstname'],ENT_QUOTES);
    $lastname = htmlentities($_POST['lastname'],ENT_QUOTES);
    $phone = htmlentities($_POST['phone'],ENT_QUOTES);
    $email = htmlentities($_POST['email'],ENT_QUOTES);
    $description = htmlentities($_POST['description'],ENT_QUOTES);

    // Send email - different address for different situations
    if (endsWith(ROOT_PATH, "-dev"))
    {
        $debugging = true;
        $to = "cdcdebug@dillonbeliveau.com";
    }
    else
    {
        $debugging = false;
        $to = "cdclinic@uvm.edu";
    }
    $subject = $firstname." ".$lastname.": ".$description;
    $message  = "<html><head><title>Confirmation</title></head><body><p>Sign-in form filled out: ";
    $message .= date("D, M jS, Y g:i:s A")."</p>";
    $message .= "<p><strong>Client has accepted terms of service.</strong></p>";
    $message .= "<p>Netid: ".$netid."</p>";
    $message .= "<p>First Name: ".$firstname."</p>";
    $message .= "<p>Last Name: ".$lastname."</p>";
    $message .= "<p>Phone Number: ".$phone."</p>";
    $message .= "<p>Email Address: ".$email."</p>";
    $message .= "<p>Description: ".$description."</p>";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    if (!empty($netid))
    {
        // We have a netid, we can look them up in LDAP to get their first.last email address.
        $ld = new UVMLdap;
        $results = array();

        // Authenticate to the ldap server
        // Check if webauth has the user logged in
        $bind = @ldap_bind($ld->ldap, $ld->dn, $ld->password);
        if ($bind) {
            $sanitized_netid = str_replace(array("(",")","*","|","="),"",$netid);
            $filter = $ld->makeFilter("uid","=",$sanitized_netid);
            $result = @ldap_search($ld->ldap, $ld->ldap_base, $filter);
            if ($result) {
                $entries = ldap_get_entries($ld->ldap, $result);
                if ($debugging)
                {
                    $message .= "<p><pre>".print_r($entries,1)."</pre></p>";
                }
                if (count($entries) == 0 || (array_key_exists("count", $entries) && $entries["count"] == 0))
                {
                    $message .= "<p>No result returned for ".$sanitized_netid." in directory.</p>";
                    $headers .= "From:(".$email.")\r\n";
                }
                else
                {
                    foreach ($entries as $key => $entry)
                    {
                        if ($key === "count")
                            continue;
                        $results[] = $entry;
                    }
                    $headers .= "From:".$results[0]["mail"]["0"]."\r\n";
                }
            }
            else
            {
                $message .= "<p>Invalid result returned from LDAP.</p>";
                $headers .= "From:(".$email.")\r\n";
            }
            // Data stored in $results.
            //$message .= "<p><pre>".print_r($results,1)."</pre></p>";
        } else { // ldap_bind failed
            $headers .= "From:(".$email.")\r\n";
            $message .= "<p>ldap_bind failed, unable to look up user in the directory.</p>";
        }
    }
    else
    {
        $headers .= "From:(".$email.")\r\n";
    }

    $message .= " </body> </html>";
    // And finally send it.
    $blnMail=mail($to,$subject,$message,$headers);
    // Send the TOS email as well
    $tosMail = mail($email, $tos_email_subject, $tos_email_body, $tos_email_headers);
    //if ($blnMail)
    //{
        //file_put_contents("signins.txt","MESSAGE ACCEPTED FOR DELIVERY",FILE_APPEND);
    //}
    //else
    //{
        //file_put_contents("signins.txt","Error sending email.",FILE_APPEND);
    //}
}
else
{
    print("Access denied!");
}
?>
