<form name="signup" action="" method="post">
<pre>
Username: <input type="text" name="username" id="username" placeholder="unicorns101" required value="bob">
Email:    <input type="email" name="email" id="email" placeholder="example@example.com" required value="bob@example.com">
Password: <input type="password" name="password" id="password" required value="abc123">
Verify:   <input type="password" name="verify" id="password" required value="abc123">
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
include("sqlite.php");
//Unless the user already exists
if(checkPending($email) || checkUsers($email)){
     echo "<br> <h3> Sorry, the user already exists </h3>";
     exit();
}
$passwordHash=password_hash("$password", PASSWORD_DEFAULT);
$randcode=addPending($username, $email, $passwordHash);
if(isset($randcode)){
     $url="/verify.php?code=$randcode";
     echo "$url";
}else{
     echo "<br> <h3> Sorry, an error occured, please try again later </h3>";
}
?>
