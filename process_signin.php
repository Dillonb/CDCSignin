<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $netid = htmlentities($_POST['netid'],ENT_QUOTES);
    $firstname = htmlentities($_POST['firstname'],ENT_QUOTES);
    $lastname = htmlentities($_POST['lastname'],ENT_QUOTES);
    $phone = htmlentities($_POST['phone'],ENT_QUOTES);
    $email = htmlentities($_POST['email'],ENT_QUOTES);
    $description = htmlentities($_POST['description'],ENT_QUOTES);

    // Send email
    //$to = "cdcdebug@dillonbeliveau.com";
    $to = "cdclinic@uvm.edu";
    $subject = $firstname." ".$lastname.": ".$description;
    $message  = "<html><head><title>Confirmation</title></head><body><p>Sign-in form filled out: ";
    $message .= date("D, M jS, Y g:i:s A")."</p>";
    $message .= "<p>Netid: ".$netid."</p>";
    $message .= "<p>First Name: ".$firstname."</p>";
    $message .= "<p>Last Name: ".$lastname."</p>";
    $message .= "<p>Phone Number: ".$phone."</p>";
    $message .= "<p>Email Address: ".$email."</p>";
    $message .= "<p>Description: ".$description."</p>";
    $message .= " </body> </html>";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
    $headers .= "From:(".$email.")\r\n";

    // And finally send it.
    $blnMail=mail($to,$subject,$message,$headers);
}
else
{
    print("Access denied!");
}
?>
