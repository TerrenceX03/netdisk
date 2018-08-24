<?php
include 'common/common.php';
include 'function/folderOperator.php'; 
/*
Function:Get information from function/folderOperator.php and retrun.
*/
header('Content-type:text/json');
$user = DB_USER;
$pass = DB_PWD;
$ip = DB_IP;
$port = DB_PORT;
$connection = ssh2_connect($ip, $port);
ssh2_auth_password($connection, $user, $pass);

if($_GET['myaction'] == "CREATE_FOLDER") { // Create folder
	echo json_encode(createFolder($connection, FS_MOUNT_POINT . $_POST['folderpath'], $_POST['foldername']));
}
?>