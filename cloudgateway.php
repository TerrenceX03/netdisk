<?php
include 'common/common.php';
include 'function/cloudgatewayOperator.php'; 

header('Content-type:text/json');
$user = DB_USER;
$pass = DB_PWD;
$ip = DB_IP;
$port = DB_PORT;
$connection = ssh2_connect($ip, $port);
ssh2_auth_password($connection, $user, $pass);

if ($_GET["myaction"] == "LIST") { // List a folder 
	echo json_encode(listContainerPairSet($connection));
} else if ($_GET["myaction"] == "GET" && $_GET["key"] == "FILE") { // List a folder 
	echo json_encode(getFileCloudInfo($connection, $_GET["filepath"]));
}

?>
