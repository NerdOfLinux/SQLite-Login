<?php
include_once("info.php");
?>
<html>
<head>
     <title> Verify Email </title>
</head>
<body>
<?php
include("$DB_type.php");
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
?>
</body>
</html>
