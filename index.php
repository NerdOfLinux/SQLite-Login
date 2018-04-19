<?php
session_start();
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
//Fill out the following:
$domain="example.com";
$from_email="no-reply@$domain";
$DB_location=".ht.users.db";
?>
<html>
<head>
	<title> <?php echo "$domain"; ?> </title>
</head>
<body>
<?php
//Create functions
//Create functions to user
//Define db location
$location=$DB_location;
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
//Allow for email links
function addPending($userName, $email, $password){
     //Generate a random code
     $x="";
     for($i=0;$i<5;$i++){
	         $a=mt_rand(999,999999999);
	            $x .= $a;
     }
     $randcode=base64_encode($x);
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
//Get action
$action=$_GET['action'];
//If the GET parameter is signup, display the signup form
if($action=="signup"){
	$form='
<form name="signup" action="" method="post">
<pre>
Username: <input type="text" name="username" id="username" placeholder="unicorns101" required>
Email:    <input type="email" name="email" id="email" placeholder="example@example.com" required>
Password: <input type="password" name="password" id="password" required>
Verify:   <input type="password" name="verify" id="password" required>
<button type="submit" name="signupButton"> Sign Up </button>
</pre>
</form>
';
	echo $form;
	echo "<a href='?action=login'> Log in </a><br>";
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
	//Insert into pending
	//Unless the user already exists
	if(checkPending($email) || checkUsers($email)){
	     echo "<br> <h3> Sorry, the email is already in use</h3>";
	     exit();
	}
	if(checkPending2($username) || checkUsers2($username)){
		echo "<br> <h3> Sorry, the username is already in use</h3>";
	}
	$passwordHash=password_hash("$password", PASSWORD_DEFAULT);
	$randcode=addPending($username, $email, $passwordHash);
	if(isset($randcode)){
	          $url="http://$domain/?action=verify&code=$randcode";
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
//Else if the action is to logout
}else if($action=="logout"){
	unset($_SESSION['loggedIn']);
	unset($_SESSION['userName']);
	echo "You're now logged out!";
//If the request is for the dashboard
}else if($action=="dashboard"){
	if(!$_SESSION['loggedIn']){
		exit();
	}
	$username=$_SESSION['userName'];
	$id=$_SESSION['id'];
	echo "Your username: $username<br>";
	echo "Your ID: $id<br>";
	echo "<a href='?action=logout'> Log out </a><br>";
	//Use some JS to only display the form when a link is clicked
	echo "<a onclick='showUpdatePass()' href='#'> Update Password</a>";
	echo "<script>
function showUpdatePass(){
	document.getElementById('showUpdatePassForm').style.display = 'unset';
}
</script>";
	echo "<br><span style='display: none;' id='showUpdatePassForm'><br>";
	$form='
<form action="" method="post" name="updatePass">
<pre>
Current: <input type="password" name="oldPass" required>
New:     <input type="password" name="newPass1" required>
Verify:  <input type="password" name="newPass2" required>
</pre>
<input type="submit" value="Update" name="updatePassButton"></input>
</form>
</span>
';
	echo $form;
	//Only do stuff if the button is pressed
	if(isset($_POST['updatePassButton'])){
		if($_POST['newPass1'] != $_POST['newPass2']){
			echo "New passwords don't match.";
			exit();
		}
		$oldPass=$_POST['oldPass'];
		$newPass=$_POST['newPass1'];
		$id=$_SESSION['id'];
		if(updatePass($oldPass, $newPass, $id)){
			echo "Password updated";
		}else{
			echo "Current password incorrect(or we encountered an error).";
		}
	}
	//Again, JS to only display form upon link press
	echo "<a onclick='showUpdateUsername()' href='#'> Update Username</a>";
     echo "<script>
function showUpdateUsername(){
     document.getElementById('showUpdateUsername').style.display = 'unset';
}
</script>";
     echo "<br><span style='display: none;' id='showUpdateUsername'><br>";
	$form='
<form action="" method="post" name="updateUsername">
<pre>
New username: <input type="text" name="newUsername" required> </input>
</pre>
<input type="submit" value="Update" name="newUsernameButton"></input>
</form>
</span>
';
	echo $form;
	if(isset($_POST['newUsernameButton'])){
		if(updateUsername($_POST['newUsername'], $_SESSION['id'])){
			echo "Username updated.";
		}else{
			echo "Username in use.";
		}
	}
//If an unknown or no parameter is given, assume login
}else{
	$form='
<form name="login" action="" method="post">
<pre>
Email:    <input type="email" name="email" id="email" placeholder="bob@example.com" required>
Password: <input type="password" name="password" id="password" required>
<button type="submit" name="loginButton"> Log In</button>
</pre>
</form>
';
	if($_SESSION['loggedIn']){
		$username=$_SESSION['userName'];
		echo "You're already logged in, $username<br>";
		echo "<a href='?action=dashboard'> Dashboard</a><br>";
		echo "<a href='?action=logout'> Log out </a>";
		echo "</body></html>";
		exit;
	}
	echo $form;
	echo "<a href='?action=signup'> Create an account </a><br>";
	//Only do stuff if the sign up button is pressed
	if(!isset($_POST['loginButton'])){
	     exit();
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
	}else{
	     echo "Drat! Wrong password or email.";
	}
}
?>
</body>
</html>
