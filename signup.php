<?php
include_once("info.php");
?>
<form name="signup" action="" method="post">
<pre>
Username: <input type="text" name="username" id="username" placeholder="unicorns101" required>
Email:    <input type="email" name="email" id="email" placeholder="example@example.com" required>
Password: <input type="password" name="password" id="password" required>
Verify:   <input type="password" name="verify" id="password" required>
<button type="submit" name="signupButton"> Sign Up </button>
</pre>
</form>
<?php
//All the magic happens here
//Only do stuff if the sign up button is pressed
if(!isset($_POST['signupButton'])){
     exit();
}
//Set variables
$username=$_POST['username'];
$email=$_POST['email'];
$password=$_POST['password'];
$verify=$_POST['verify'];
//Ensure that passwords match
if("$password" != "$verify"){
     echo "<br> <h3> Passwords do not match, please try again</h3>";
}
//Insert into pending DB
include("$DB_type.php");
//Unless the user already exists
if(checkPending($email) || checkUsers($email)){
     echo "<br> <h3> Sorry, the user already exists </h3>";
     exit();
}
$passwordHash=password_hash("$password", PASSWORD_DEFAULT);
$randcode=addPending($username, $email, $passwordHash);
if(isset($randcode)){
     if(!empty($accountDir)){
          $url="http://$domain/$accountDir/verify.php?code=$randcode";
     else{
          $url="http://$domain/verify.php?code=$randcode";
     }
     $verify_link="<a href=\"$url\"> verify your email.</a>";
     $styles="font-family: Arial;font-size: 14px;";
     $message="<html><body><span style=\"$styles\"><h1>Welcome to $domain!</h1><p>Your account is almost set-up, but there is one last step you need to complete! Simply $verify_link <p> Link not working? No prboblem, just copy and paste: $url<br><p>Sincerely,<br>The people over at $domain</span></body></html>";
     $headers="From: $from_email\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";
     mail("$email", "Verify your email($domain)", $message, $headers);
     echo "An email has been sent to $email";
}else{
     echo "<br> <h3> Sorry, an error occured, please try again later </h3>";
}
?>
