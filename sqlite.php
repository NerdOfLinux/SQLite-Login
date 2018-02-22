<?php
include_once("info.php");
//Create functions to user
//Caching
function checkUserCache($email){
     $dir="userCache";
     $cacheFile="$dir/$email";
     $output="";
     if(is_file($cacheFile)){
          $output=json_decode(file_get_contents($cacheFile), true);
     }
     return $output;
}
function createUserCache($email, $info){
     $dir="userCache";
     $cacheFile="$dir/$email";
     file_put_contents("$cacheFile", json_encode($info));
}
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
function checkCreds($email, $password){
     if($caching=="yes"){
          $cached=checkUserCache($email);
          if(!empty($cached)){
               return $cached;
          }
     }
     $query="SELECT * FROM users WHERE email=\"$email\"";
     $db=openDB();
     $result=$db->query($query);
     $result=$result->fetchArray();
     if($caching=="yes"){
          createUserCache($email, $result);
     }
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
//Check code
function checkCode($code){
     $query="SELECT * FROM pending WHERE code=\"$code\"";
     $db=openDB();
     $result=$db->query($query);
     $result=$result->fetchArray();
     return $result;
}
?>
