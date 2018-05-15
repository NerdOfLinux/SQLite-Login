<?php
session_start();
$close="
</body>
</html>
";
/*
    SQLite Web Login- A single file to manage user accounts with a SQLite DB for easy set up and minimal maintainance
    Copyright (C) 2018  NerdOfLinux

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along
    with this program; if not, write to the Free Software Foundation, Inc.,
    51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.

    You can also find the full copy of the license at https://goo.gl/Pkfs1S
*/
//Set info
//Please use a wrapper script :-)
//Only used if they are not set(so you can include this in another file with the settings there)
if(!isset($domain)){
	$domain="example.com";
}
if(!isset($from_email)){
	$from_email="no-reply@$domain";
}
if(!isset($DB_location)){
	$DB_location=".ht.users.db";
}
if(!isset($accountFile)){
	$accountFile="index.php";
}
if(!isset($siteName)){
	$siteName=$domain;
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>
	<?php
	//Use title + action as the title
	$action=$_GET['action'];
	if($action == "verify"){
		$action="Verification";
	}else if($action == "updateEmail"){
		$action="Verification";
	}else{
		$action=ucfirst($action);
	}
	if(isset($customTitle)){
		echo "$customTitle $action";
	}else{
		echo "$siteName $action";
	}

	?> </title>
	<style>
	<?php
	//Inline external CSS to prevent caching(after removing all spaces)
	if(isset($customCSS) && is_file($customCSS)){
		echo preg_replace("*\s*", "", file_get_contents($customCSS));
	}else{
		echo preg_replace("*\s*", "","
		a{
			color: blue;
		}
		button[type=submit],input[type=submit]{
			width: 200px;
			height: 25px;
			padding: 3px;
			margin: 5px;
			background-color: lightsteelblue;
			border-radius: 5px;
		}
		button[type=submit]:hover,input[type=submit]:hover{
			border-style: inset;
		}
		button[type=submit]:focus,input[type=submit]:focus{
			background-color: lightgray;
		}
		input{
			height: 25px;
			padding: 3px;
			margin: 3px;
			border-radius: 3px;
		}
		#hideOnClickUsername,#hideOnClickPassword,#hideOnClickEmail{
			padding: 3px;
			margin: 3px;
			height: 25px;
			width: 200px;
			border-radius: 5px;
		}
		#hideOnClickUsername:hover,#hideOnClickPassword:hover,#hideOnClickEmail:hover{
			background-color: lightgray;
		}
		.center{
			text-align: center;
		}");
		}?>
	</style>
	<meta name="viewport" content="initial-scale=1.0">
	<?php
	if(isset($customLogo)){
	?>
	<link rel="shortcut icon" href="<?php echo $customLogo;?>">
	<?php
	}
	?>
</head>
<body>
<?php
//Create functions
//Create functions to user
//Define db location
$location=$DB_location;
//Semi-installer functionality
//If the DB file does not exist, then create it:
if(!is_file($location)){
	$db=openDB();
	$query="BEGIN;CREATE TABLE pending (code TEXT UNIQUE NOT NULL,username TEXT UNIQUE NOT NULL, email TEXT UNIQUE NOT NULL, password TEXT NOT NULL);CREATE TABLE users (username TEXT UNIQUE NOT NULL, email TEXT UNIQUE NOT NULL, password TEXT NOT NULL);CREATE TABLE newEmail (code TEXT UNIQUE NOT NULL, newEmail TEXT UNIQUE NOT NULL, id TEXT UNIQUE NOT NULL);COMMIT;";
	$status=$db->exec($query);
	if($status){
		echo "Database file created.";
		echo $close;
		exit();
	}
}
//Function to connect to DB
function openDB(){
     global $location;
     $db = new SQLite3($location);
     return $db;
}
//Function to close connection
function closeDB($db){
     $db->close();
     return TRUE;
}
//Add user
function addUser($userName, $email, $password){
     $query="INSERT INTO users (username, email, password) VALUES(\"$userName\", \"$email\", \"$password\")";
     $db=openDB();
     $status=$db->exec($query);
     closeDB($db);
     return $status;
}
//Check user
function checkCreds($email){
     $query="SELECT rowid,* FROM users WHERE email=\"$email\"";
     $db=openDB();
     $result=$db->query($query);
     $result=$result->fetchArray();
     closeDB($db);
     return $result;
}
//Generate a random code
function randCode(){
	//Generate a random code
     $x="";
     for($i=0;$i<5;$i++){
              $a=mt_rand(999,999999999);
                 $x .= $a;
     }
     $randcode=base64_encode($x);
	return $randcode;
}
//Allow for email links
function addPending($userName, $email, $password){
     //Generate a random code
     $randcode=randCode();
     $query="INSERT INTO pending (code, username, email, password) VALUES(\"$randcode\", \"$userName\", \"$email\", \"$password\")";
     $db=openDB();
     if(!$db->exec($query)){
          unset($randcode);
     }
     closeDB($db);
     return urlencode($randcode);
}
//Delete a user from the pending table
function removePending($code){
     $query="DELETE FROM pending WHERE code=\"$code\"";
     $db=openDB();
     $status=$db->exec($query);
     closeDB($db);
     return $status;
}
//Check if user or pending exists
function checkPending($email){
     $query="SELECT * FROM pending WHERE email=\"$email\"";
     $db=openDB();
     $result=$db->query($query);
     $result=$result->fetchArray();
     $status=TRUE;
     if(empty($result)){
          $status=FALSE;
     }else{
          $status=TRUE;
     }
     closeDB($db);
     return $status;
}
function checkEmailPending($email){
     $query="SELECT * FROM newEmail WHERE newEmail=\"$email\"";
     $db=openDB();
     $result=$db->query($query);
     $result=$result->fetchArray();
     $status=TRUE;
     if(empty($result)){
          $status=FALSE;
     }else{
          $status=TRUE;
     }
     closeDB($db);
     return $status;
}
//Check pending table for username
function checkPending2($username){
     $query="SELECT * FROM pending WHERE username=\"$username\"";
     $db=openDB();
     $result=$db->query($query);
     $result=$result->fetchArray();
     $status=TRUE;
     if(empty($result)){
          $status=FALSE;
     }else{
          $status=TRUE;
     }
     closeDB($db);
     return $status;
}
//Check users table for email
function checkUsers($email){
     $query="SELECT * FROM users WHERE email=\"$email\"";
     $db=openDB();
     $result=$db->query($query);
     $result=$result->fetchArray();
     $status=TRUE;
     if(empty($result)){
          $status=FALSE;
     }else{
          $status=TRUE;
     }
     closeDB($db);
     return $status;
}
//Check users table for username
function checkUsers2($username){
     $query="SELECT * FROM users WHERE username=\"$username\"";
     $db=openDB();
     $result=$db->query($query);
     $result=$result->fetchArray();
     $status=TRUE;
     if(empty($result)){
          $status=FALSE;
     }else{
          $status=TRUE;
     }
     closeDB($db);
     return $status;
}
//Check code
function checkCode($code){
     $query="SELECT * FROM pending WHERE code=\"$code\"";
     $db=openDB();
     $result=$db->query($query);
     $result=$result->fetchArray();
     return $result;
}
//Update the password
function updatePass($oldPass, $newPass, $id){
	$query1="SELECT password FROM users WHERE rowid=$id";
	$db=openDB();
	$result=$db->query($query1);
	$result=$result->fetchArray();
	if(password_verify($oldPass, $result['password'])){
		$password=password_hash($newPass, PASSWORD_DEFAULT);
		$query2="UPDATE users SET password='$password' WHERE rowid=$id";
		$status=$db->exec($query2);
	}else{
		$status=false;
	}
	closeDB($db);
	return $status;
}
//Update the username
function updateUsername($newUsername, $id){
	$query="UPDATE users SET username='$newUsername' WHERE rowid=$id";
	$db=openDB();
	$status=$db->exec($query);
	closeDB($db);
	return $status;
}
//Update the email
function insertNewEmail($randcode, $newEmail, $id){
	$query="INSERT INTO newEmail (code, newEmail, id) VALUES(\"$randcode\", \"$newEmail\", \"$id\")";
	$db=openDB();
	$status=$db->exec($query);
	return $status;
}
function checkEmailCode($code){
	$query="SELECT * FROM newEmail WHERE code = \"$code\"";
	$db=openDB();
     $result=$db->query($query);
     $result=$result->fetchArray();
	closeDB($db);
     return $result;
}
function updateEmail($code){
     $query="SELECT * FROM newEmail WHERE code = \"$code\"";
     $db=openDB();
     $result=$db->query($query);
     $result=$result->fetchArray();
	$email=$result['newEmail'];
	$id=$result['id'];
	$query="UPDATE users SET email=\"$email\" WHERE rowid=\"$id\";DELETE FROM newEmail WHERE code=\"$code\"";
	$status=$db->exec($query);
	closeDB($db);
     return $status;
}
//Get action
$action=$_GET['action'];
//If the GET parameter is signup, display the signup form
if($action=="signup"){
?>
<h2 class="center"> <?php echo $siteName;?> signup</h2>
<hr>
<form name="signup" action="" method="post">
<pre>
Username: <input type="text" name="username" id="username" placeholder="unicorns101" required>
Email:    <input type="email" name="email" id="email" placeholder="example@example.com" required>
Password: <input type="password" name="password" id="password" required>
Verify:   <input type="password" name="verify" id="password" required>
<button type="submit" name="signupButton"> Sign Up </button>
</pre>
</form>
<a href='?action=login'> Log in </a><br>
<?php
	//Only do stuff if the sign up button is pressed
	if(!isset($_POST['signupButton'])){
		echo $close;
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
	//Insert into pending
	//Unless the user already exists
	if(checkPending($email) || checkUsers($email)){
	     echo "<br> <h3> Sorry, the email is already in use</h3>";
		echo $close;
	     exit();
	}
	if(checkPending2($username) || checkUsers2($username)){
		echo "<br> <h3> Sorry, the username is already in use</h3>";
	}
	$passwordHash=password_hash("$password", PASSWORD_DEFAULT);
	$randcode=addPending($username, $email, $passwordHash);
	if(isset($randcode)){
	     $url="http://$domain/$accountFile?action=verify&code=$randcode";
	     $verify_link="<a href=\"$url\"> verify your email.</a>";
	     $styles="font-family: Arial;font-size: 14px;";
	     $message="<html><body><span style=\"$styles\"><h1>Welcome to $domain!</h1><p>Your account is almost set-up, but there is one last step you need to complete! Simply $verify_link <p> Link not working? No prboblem, just copy and paste: $url<br><p>Sincerely,<br>The people over at $domain</span></body></html>";
	     $headers="From: $from_email\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";
	     mail("$email", "Verify your email($domain)", $message, $headers);
	     echo "<br> An email has been sent to $email";
	}else{
		echo "<br> <h3> Sorry, an error occured, please try again later </h3>";
	}
}else if($action=="verify"){
	$code=urldecode($_GET['code']);
	$check=checkCode($code);
	if(empty($check)){
	     echo "<br> <h3> Sorry, that code does not appear to exist</h3>";
		echo $close;
	     exit();
	}
	$username=$check['username'];
	$password=$check['password'];
	$email=$check['email'];
	//Add the user
	if(addUser($username, $email, $password) && removePending($code)){
	     echo "<br> <h3> Your account has been created </h3>";
	}else{
	     echo "<br> <h3> Sorry, there was an error creating the account. Please try again later.";
	}
}else if($action=="updateEmail"){
	$code=urldecode($_GET['code']);
	$check=checkEmailCode($code);
	if(empty($check)){
		echo "<br> <h3> Sorry, that code does not appear to exist</h3>";
		echo $close;
		exit();
	}
	if(updateEmail($code)){
		echo "Email updated.";
	}else{
		echo "Please refresh this page to try again.";
	}
//Else if the action is to logout
}else if($action=="logout"){
	unset($_SESSION['loggedIn']);
	unset($_SESSION['userName']);
	echo "You're now logged out!";
	header("Location: ?action=login");
//If the request is for the dashboard
}else if($action=="dashboard"){
	if(!$_SESSION['loggedIn']){
		header("Location: ?action=login");
		exit();
	}
	$username=$_SESSION['userName'];
	$id=$_SESSION['id'];
	$email=$_SESSION['email'];
?>
<h2 class="center"> <?php echo $username;?>'s Dashboard </h2>
<hr>
Your email: <?php echo $email; ?><br>
Your username: <?php echo $username;?><br>
Your ID: <?php echo $id?><br>
<a href='?action=logout'> Log out </a><br>
Available Options:<br>
<!--Use some JS to only display the form when a link is clicked-->
<button onclick='showUpdatePass()' href='#' id="hideOnClickPassword"> Update Password</button><br>
<script>
function showUpdatePass(){
	document.getElementById('showUpdatePassForm').style.display = 'unset';
	document.getElementById('hideOnClickPassword').style.display = 'none';
}
</script>
<span style='display: none;' id='showUpdatePassForm'>
<h3> Password Update: </h3>
<form action="" method="post" name="updatePass">
<pre>
Current: <input type="password" name="oldPass" required>
New:     <input type="password" name="newPass1" required>
Verify:  <input type="password" name="newPass2" required>
</pre>
<input type="submit" value="Update" name="updatePassButton"></input>
</form>
</span>
<?php
	//Only do stuff if the button is pressed
	if(isset($_POST['updatePassButton'])){
		if($_POST['newPass1'] != $_POST['newPass2']){
			echo "New passwords don't match.";
			echo $close;
			exit();
		}
		$oldPass=$_POST['oldPass'];
		$newPass=$_POST['newPass1'];
		$id=$_SESSION['id'];
		if(updatePass($oldPass, $newPass, $id)){
			echo "Password updated!<br>";
		}else{
			echo "Current password incorrect(or we encountered an error).<br>";
		}
	}
?>
<!--Again, JS to only display form upon link press-->
<button onclick='showUpdateUsername()' href='#' id="hideOnClickUsername"> Update Username</button>
<script>
function showUpdateUsername(){
     document.getElementById('showUpdateUsername').style.display = 'unset';
	document.getElementById('hideOnClickUsername').style.display = 'none';
}
</script>
<br><span style='display: none;' id='showUpdateUsername'><br>
<h3> Username Update: </h3>
<form action="" method="post" name="updateUsername">
<pre>
New username: <input type="text" name="newUsername" required> </input>
</pre>
<input type="submit" value="Update" name="newUsernameButton"></input>
</form>
</span>
<?php
	if(isset($_POST['newUsernameButton'])){
		if(updateUsername($_POST['newUsername'], $_SESSION['id'])){
			echo "Username updated!<br>";
		}else{
			echo "Username in use!<br>";
		}
	}
?>
<!--Again, JS to only display form upon link press-->
<button onclick='showUpdateEmail()' href='#' id="hideOnClickEmail"> Update Email </button>
<script>
function showUpdateEmail(){
     document.getElementById('showUpdateEmail').style.display = 'unset';
     document.getElementById('hideOnClickEmail').style.display = 'none';
}
</script>
<br><span style='display: none;' id='showUpdateEmail'><br>
<h3> Email Update: </h3>
<form action="" method="post" name="updateUsername">
<pre>
New email: <input type="email" name="newEmail" required> </input>
</pre>
<input type="submit" value="Update" name="newEmailButton"></input>
</form>
</span>
<?php
     if(isset($_POST['newEmailButton'])){
		$email=$_POST['newEmail'];
		if(checkPending($email) || checkUsers($email)){
          	echo "<br> <h3> Sorry, that email is already in use</h3>";
          	echo $close;
          	exit();
     	}
		//Doesn't work for some reason if placed above
		if(checkEmailPending($email)){
			echo "<br> <h3> Sorry, that email is already pending</h3>";
               echo $close;
               exit();
		}
		$randcode=randCode();
		$url="http://$domain/$accountFile?action=updateEmail&code=$randcode";
		$verify_link="<a href=\"$url\"> verify your email.</a>";
		$styles="font-family: Arial;font-size: 14px;";
		$message="<html><body><span style=\"$styles\"><h1>Welcome to $domain!</h1><p>You have recently requested an email change. In order to do this, simply $verify_link <p> Link not working? No prboblem, just copy and paste: $url<br><p>Sincerely,<br>The people over at $domain<br><br><p> Didn't request an email change? You can safely ignore this email, although changing your password is recommended.</span></body></html>";
		$headers="From: $from_email\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";
		mail("$email", "Verify your email($domain)", $message, $headers);
		insertNewEmail($randcode, $email, $_SESSION['id']);
		echo "Email verification sent.";
     }
//If an unknown or no parameter is given, assume login
}else{
     if($_SESSION['loggedIn']){
          $username=$_SESSION['userName'];
		header("Location: ?action=dashboard");
          echo $close;
          exit();
     }
?>
<h2 class="center"> <?php echo $siteName;?> login</h2>
<hr>
<form name="login" action="" method="post">
<pre>
Email:    <input type="email" name="email" id="email" placeholder="bob@example.com" required>
Password: <input type="password" name="password" id="password" required>
<button type="submit" name="loginButton"> Log In</button>
</pre>
</form>
<a href='?action=signup'> Create an account </a><br>
<?php
	//Only do stuff if the sign up button is pressed
	if(!isset($_POST['loginButton'])){
	     exit();
		echo $close;
	}
	//Set variables
	$email=$_POST['email'];
	$password=$_POST['password'];
	//Check against DB
	$creds=checkCreds($email, $password);
	$passwordHash=$creds['password'];
	$username=$creds['username'];
	$id=$creds['rowid'];
	if(password_verify($password, $passwordHash)){
	     echo "Congrats! Your user name is: $username";
	     $_SESSION['loggedIn']=true;
		$_SESSION['userName']=$username;
		$_SESSION['id']=$id;
		$_SESSION['email']=$email;
		header("Location: ?action=dashboard");
	}else{
	     echo "Drat! Wrong password or email.";
	}
}
?>
</body>
</html>
