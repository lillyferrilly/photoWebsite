<?php

$debug = false; // NOTE: to pass w3c html validation this needs to be set to false

$firstName = "";
$email = "";
$gender = "Male";
$findme = false;
$photographer = false;
$photography = false;
$camera = false;

$yourURL = "https://lthomps4.w3.uvm.edu/cs142/assignment7/contact.php";

//initialize flags for errors, one for each item
$firstNameERROR = false;
$emailERROR = false;

// if form has been submitted, validate the information
if (isset($_POST["butSubmit"])){

/*is the referring web page the one we want or is someone trying to hack in. this is not 100% reliable*/
    /*$fromPage = getenv("http_referer"); 

    if ($debug) print "<p>From: " . $fromPage . " should match yourUrl: " . $yourURL;

    if($fromPage != $yourURL){
        die("<p>Sorry you cannot access this page. Security breach detected and reported</p>");
    } */
    
    /*
        this function just converts all input to html entities to remove any potentially
        malicious coding
    */
    function clean($elem)
    {
        if(!is_array($elem))
            $elem = htmlentities($elem,ENT_QUOTES,"UTF-8");
        else
            foreach ($elem as $key => $value)
                $elem[$key] = clean($value);
        return $elem;
     }

     // be sure to clean out any code that was submitted
     if(isset($_GET)) $_CLEAN['GET'] = clean($_GET);
     if(isset($_POST)) $_CLEAN['POST'] = clean($_POST); 

     /* now we refer to the $_CLEAN arrays instead of the get or post
      * ex: $to = $_CLEAN['GET']['txtEmail'];
      * or: $to = $_CLEAN['POST']['txtEmail'];
      */
     
     //check for errors
     include ("validation_functions.php");
     $errorMsg=array();
     
     // begin testing each form element 
     
     // Test first name for empty and valid characters
     $firstName=$_CLEAN['POST']['txtFname'];
     if(empty($firstName)){
        $errorMsg[]="Please enter your First Name";
        $firstNameERROR = true;
     } else {
        $valid = verifyAlphaNum ($firstName);
        if (!$valid){ 
            $errorMsg[]="First Name must be letters and numbers, spaces, dashes and single quotes only.";
            $firstNameERROR = true;
        }
     }
     
      // test email for empty and valid format
     $email=$_CLEAN['POST']['txtEmail'];
     if(empty($email)){
        $errorMsg[]="Please enter your email address.";
        $emailERROR = true;
     } else {
        $valid = verifyEmail ($email);
        if (!$valid){
            $errorMsg[]="You must enter a valid email address.";
            $emailERROR = true;
        }
     }
     
      // make the form sticky
     if(isset($_CLEAN['POST']["radGender"])){
            $gender = $_POST["radGender"];
     }
     if(isset($_CLEAN['POST']["chkPhotographer"])){
            $photographer = true;
     }
     if(isset($_CLEAN['POST']["chkPhotography"])){
            $photography = true;
     }
     if(isset($_CLEAN['POST']["chkCamera"])){
            $camera = true;
     }
     if(isset($_CLEAN['POST']["radFindMe"])){
            $findme = $_POST["radFindMe"];
     }
     
      // our form data is valid so we can mail it
    if(!$errorMsg){    
        if ($debug) print "<p>Form is valid</p>";
     //now i can mail it
        $to = $email;

        // just sets these variable to the current date and time
        $todaysDate=strftime("%x");
        $currentTime=strftime("%X");

        /* subject line for the email message */
        $subject = "Web Order: " . $todaysDate ;

        // be sure to change Your Site and yoursite to something meaningful
        $mailFrom = "Lilly Marie Photography <noreply@lillymariephotography.com>";

        $cc = "";  // if you needed to Carbon Copy someone (person who fills out form will see this)
        $bcc = "lthomps4@uvm.edu"; // if you need to Blind Carbon Copy (person who fills out form will NOT see this)


        //build your message here.
        $message  = '<p>This is your confirmation on your order placed on ' . $todaysDate;
        $message .= '. Please print and keep a copy for your records.</p>';
        
        /* message */
        $messageTop  = '<html><head><title>' . $subject . '</title></head><body>';

        // $$$$$$$$$$$$ build message Here
        /* here you can customize the message if you need to */

        /* ########################################################################### */
        // This block simply adds the items filled in on the form to the email message
        
        if(isset($_CLEAN['POST'])) {
            foreach ($_CLEAN['POST'] as $key => $value){
                    $message .= "<p>" . $key . " = " . $value . "</p>";
            }
        }
        
        /* ########################################################################### */

        /* To send HTML mail, you can set the Content-type header. */
        $headers .= "MIME-Version: 1.0\r\n"; 
        $headers .= "Content-type: text/html; charset=utf-8\r\n";

        /* additional headers */
        $headers .= "From: " . $mailFrom . "\r\n";

        if ($cc!="") $headers .= "CC: " . $cc . "\r\n";
        if ($bcc!="") $headers .= "Bcc: " . $bcc . "\r\n";

        $mailMessage = $messageTop . $message;

        /* this line actually sends the email */
        if(!empty($_CLEAN['POST']['txtEmail'])) { 
             $blnMail=mail($to, $subject, $mailMessage, $headers);
        }
        
    } // no errors our form is valid
    
    
     // rest of code goes ABOVE this line
     
} // ends isset($_POST["butSubmit"]
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Lilly Marie Photography</title>
<meta charset="utf-8">
<meta name="author" content="Lilly Thompson">
<meta name="description" content="Lilly Thompson is a photographer living in Vermont.">

<!--[if lt IE 9]>
	<script src="//html5shim.googlecode.com/sin/trunk/html5.js"></script>
<![endif]-->
	
<link rel="stylesheet"
href="style.css"
type="text/css"
media="screen">

<link rel="stylesheet"
href="print.css"
type="text/css"
media="print">

<link href='https://fonts.googleapis.com/css?family=Parisienne' rel='stylesheet' type='text/css'>

<link href='https://fonts.googleapis.com/css?family=Duru+Sans' rel='stylesheet' type='text/css'>

<link rel="shortcut icon" href="http://www.prodraw.net/favicon/download.php?file=1im09wez_4.ico">
	
</head>

<body class="contact">
<?php 
if(isset($_POST["butSubmit"]) AND empty($errorMsg)){

    print "<h2>Your Request has ";

    if (!$blnMail) {
        echo "not ";
    }
    
    echo "been processed</h2>";

    print "<p>A copy of this message has ";
    if (!$blnMail) {
        echo "NOT ";
    }
    print "been sent</p>";
    print "<p>To: " . $to . "</p>";
    print "<p>Subject: " . $subject . "</p>";
    print "<p>Mail Message:</p>";
    echo $message;
}

?>
<div id="errors">
<?php
if($errorMsg){
    echo "<ol>\n";
    foreach($errorMsg as $err){
        echo "<li>" . $err . "</li>\n";
    }
    echo "</ol>\n";
} 
?>
</div>
<header>
    <h1>Lilly Marie Photography</h1>
</header>
<nav>
    <table class="buttons">
		<tr>
		    <td><a href="index.php">Home</a></td>
			<td><a href="color.php">Color</a></td>
			<td><a href="black.php">Black & White</a></td>
			<td><a href="prints.php">Prints</a></td>
			<td id="active"><a href="contact.php">Contact Me</a></td>
		</tr>
	</table>
</nav><br>
<div class="all">
<!-- ###########   form   ################## -->
<article class="contact">
	<h2>Contact Me</h2>
<p>Have questions? Comments? Complaints? Want to inquire about prints? Take a second to fill out this brief form and send me a message! I will e-mail you back as soon as I can.</p><br>

<form action="<?php print $_SERVER['PHP_SELF']; ?>"
	method="post" 
	id="frmRegister"
	enctype="multipart/form-data">
	
<fieldset> 
   <legend>Tell Me About You</legend>
   <table>
		<tr>              
   <td><label for="txtFname" class="required">First Name:</label>
   <input type="text" id="txtFname" name="txtFname" placeholder="Enter your first name"  value="<?php echo $firstName; ?>" tabindex="261"
            size="25" maxlength="45"  <?php if($firstNameERROR) echo 'class="mistake"' ?> autofocus onfocus="this.select()" /></td>
   <td><label for="txtEmail" class="required">Email Address:</label>
   <input type="text" id="txtEmail" name="txtEmail" placeholder="Enter your email" value="<?php echo $email; ?>" tabindex="263"
            size="25" maxlength="45"  <?php if($emailERROR) echo 'class="mistake"' ?> onfocus="this.select()" /></td></tr>
            
       <tr><td><p>I am (check all that apply):<p>
   <label><input type="checkbox" id="chkPhotographer" name="chkPhotographer" value="Photographer" 
                   tabindex="280"  <?php if($photographer) echo ' checked="checked" ';?>/> A Photographer</label><br>
   <label><input type="checkbox" id="chkPhotography" name="chkPhotography" value="Photography"
                   tabindex="281" <?php if($photography) echo ' checked="checked" ';?>/> Interested in Photography</label><br>
   <label><input type="checkbox" id="chCamera" name="chkCamera" value="Camera"
                   tabindex="282" <?php if($camera) echo ' checked="checked" ';?>/> A Camera Owner</label></td>
   <td><p>What is your gender?</p>
   <label><input type="radio" id="radGenderMale" name="radGender" value="Male" 
                   tabindex="270"  <?php if($gender=="Male") echo ' checked="checked" ';?>/>Male</label>
   <label><input type="radio" id="radGenderFemale" name="radGender" value="Female" 
                   tabindex="271" <?php if($gender=="Female") echo ' checked="checked" ';?>/>Female</label>
   <label><input type="radio" id="radGenderTransgender" name="radGender"           value="Transgender" 
                   tabindex="272" <?php if($gender=="Transgender") echo ' checked="checked" ';?>/>Transgender</label></td></tr>
   
   <tr><td><label for="lstFindMe" class="required">How did you hear about me?</label><br>
   <select id="lstFindMe" name="lstFindMe" tabindex="300" size="1">
      <option value="Facebook" <?php if($findme=="Facebook") echo ' selected="selected" ';?>>Facebook</option>
      <option value="Tumblr" <?php if($findme=="Tumblr") echo ' selected="selected" ';?>>Tumblr</option>
      <option value="A Friend" <?php if($findme=="A Friend") echo ' selected="selected" ';?>>A Friend</option>
      <option value="A Client of Mine" <?php if($findme=="A Client of Mine") echo ' selected="selected" ';?>>A Client of Yours</option>
   </select></td></tr>
</table>
</fieldset><br>
<fieldset>  
   <legend>Send Me A Message</legend>             
   <label for="txtComments" class="required"></label>
   <textarea id="txtComments" name="txtComments" tabindex="271" 
               cols="150" rows="15" onfocus="this.select()">
   </textarea>
</fieldset><br>

<fieldset>            
   <input type="submit" id="butSubmit" name="butSubmit" value="Send" 
            tabindex="330" class="button"/>
   <input type="reset" id="butReset" name="butReset" value="Reset Form" 
            tabindex="331" class="button" />
</fieldset> 
</form>
</article>
</div>
<footer>
        <p><a href="index.php">Home</a> ~ <a href="bio.php">Bio</a> ~ <a href="color.php">Color</a> ~ <a href="black.php">Black & White</a> ~ <a href="prints.php">Order Prints</a> ~ <a href="contact.php">Contact Me</a></p>
	<p>Web Design by Lilly Thompson</p>
</footer>

</body>
</html>
