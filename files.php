<?php
include 'common/common.php';
include 'function/fileOperator.php'; 
/*
Function:Get information from function/fileOperator.php and retrun.
*/
header('Content-type:text/json');
$user = DB_USER;
$pass = DB_PWD;
$ip = DB_IP;
$port = DB_PORT;
$connection = ssh2_connect($ip, $port);
ssh2_auth_password($connection, $user, $pass);

if ($_GET["myaction"] == "LIST") { // List a folder 
	echo json_encode(listFiles($connection, $_GET['foldername']));
} elseif ($_GET['myaction'] == "GET") { // Get a file information
	echo json_encode(getFile($connection, $_GET['filepath']));
} elseif($_GET['myaction'] == "POST") { // Upload a new file
	echo json_encode(postFile($connection, $_FILES["newFile"], $_GET['parent']));
} elseif($_GET['myaction'] == "DELETE") { // Delete file
	echo json_encode(deleteFiles($connection, json_decode($_POST['files'])));
} elseif($_GET['myaction'] == "MIGRATE") { // Migration
	echo json_encode(migrate($connection, json_decode($_POST['files']), json_decode($_POST['externalfiles']),$_POST['target'], $_POST['targetpooltype']));
} 
?> 
