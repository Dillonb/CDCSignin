<?php
require_once("UVMLdap.php");
define(ROOT_PATH, dirname(__FILE__));
function endsWith($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) == $needle;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
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
        $to = "cdcdebug@dillonbeliveau.com";
    }
    else
    {
        $to = "cdclinic@uvm.edu";
    }
    $subject = $firstname." ".$lastname.": ".$description;
    $message  = "<html><head><title>Confirmation</title></head><body><p>Sign-in form filled out: ";
    $message .= date("D, M jS, Y g:i:s A")."</p>";
    $message .= "<p>Netid: ".$netid."</p>";
    $message .= "<p>First Name: ".$firstname."</p>";
    $message .= "<p>Last Name: ".$lastname."</p>";
    $message .= "<p>Phone Number: ".$phone."</p>";
    $message .= "<p>Email Address: ".$email."</p>";
    $message .= "<p>Description: ".$description."</p>";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
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
                if (count($entries) != 0)
                {
                    foreach ($entries as $key => $entry)
                    {
                        if ($key === "count")
                            continue;
                        $results[] = $entry;
                    }
                    $headers .= "From:".$results[0]["mail"]["0"]."\r\n";
                }
                else
                {
                    $message .= "<p>No result returned for ".$sanitized_netid." in directory.</p>";
                    $headers .= "From:(".$email.")\r\n";
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
