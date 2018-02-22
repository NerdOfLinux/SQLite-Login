<?php
include_once("info.php");
?>
<html>
<head>
     <title> Simple Login </title>
</head>
<body>
<?php
$action=$_GET['action'];
if($action=="login"){
     include("login.php");
}else if($action=="signup"){
     include("signup.php");
}else{
     echo "<p> No action given. You can: <ul> <li><a href='?action=login'> Log in </a> </li> <li><a href='?action=signup'> Sign Up </a></li></ul>";
}
?>
</body>
</html>
