<?php
include 'common/common.php';
include 'function/folderOperator.php'; 

header('Content-type:text/json');
$user = DB_USER;
$pass = DB_PWD;
$ip = DB_IP;
$port = DB_PORT;
$connection = ssh2_connect($ip, $port);
ssh2_auth_password($connection, $user, $pass);

if($_GET['myaction'] == "CREATE_FOLDER") { // Create folder
	echo json_encode(createFolder($connection, $_POST['folderpath'], $_POST['foldername']));
}
?>