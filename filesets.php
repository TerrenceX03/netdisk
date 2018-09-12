<?php
include 'common/common.php';
include 'function/filesetOperator.php'; 

header('Content-type:text/json');
$user = DB_USER;
$pass = DB_PWD;
$ip = DB_IP;
$port = DB_PORT;
$connection = ssh2_connect($ip, $port);
ssh2_auth_password($connection, $user, $pass);

if ($_GET["myaction"] == "LIST") { // List a folder 
	echo json_encode(listFileSet($connection));
}

?>