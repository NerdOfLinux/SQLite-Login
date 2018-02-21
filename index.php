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
     echo "<br> <h3> No action given </h3>";
}
?>
</body>
</html>
