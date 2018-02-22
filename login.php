<?php
include_once("info.php");
?>
<form name="login" action="" method="post">
<pre>
Email:    <input type="email" name="email" id="email" placeholder="bob@example.com" required>
Password: <input type="password" name="password" id="password" required>
<button type="submit" name="loginButton"> Sign Up </button>
</pre>
</form>
<?php
//All the magic happens here
//Only do stuff if the sign up button is pressed
if(!isset($_POST['loginButton'])){
     exit();
}
//Set variables
$email=$_POST['email'];
$password=$_POST['password'];
//Check against DB
include("sqlite.php");
$creds=checkCreds($email, $password);
$passwordHash=$creds['password'];
$username=$creds['username'];
if(password_verify($password, $passwordHash)){
     echo "Congrats! Your user name is: $username";
}else{
     echo "Drat! Wrong password or email.";
}
?>
