<?php
include 'common/common.php';
include 'function/fileOperator.php'; 

/*
Function:Return the html of files which in specific filesets.
*/
header('Content-type:text/json');
$user = DB_USER;
$pass = DB_PWD;
$ip = DB_IP;
$port = DB_PORT;
$connection = ssh2_connect($ip, $port);
ssh2_auth_password($connection, $user, $pass);

if ($_GET["myaction"] == "LIST") { // List a folder 
	echo json_encode(listFiles($connection, FS_MOUNT_POINT . "/" . $_POST['foldername']));
} elseif ($_GET['myaction'] == "GET") { // Get a file information
	echo json_encode(getFile($connection, $_GET['filepath']));
} elseif($_GET['myaction'] == "POST") { // Upload a new file
	echo json_encode(postFile($connection, $_FILES["newFile"], FS_MOUNT_POINT . "/" . $_GET["parent"]));
}

?> 