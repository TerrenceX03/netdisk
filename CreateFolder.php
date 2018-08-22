<?php
include 'common/common.php'; 
header('Content-type:text/json');
$result = array();
$result['msg'] = 1;
$user = DB_USER;
$pass = DB_PWD;
$ip = DB_IP;
$port = DB_PORT;
$connection = ssh2_connect($ip, $port);
ssh2_auth_password($connection, $user, $pass);
//Create a folder in specific folder.
$cmd_create="mkdir ";
$cmd_create.=$_POST['folderpath'];
$cmd_create.=$_POST['foldername'];
$exe_create = ssh2_exec($connection, $cmd_create);
echo json_encode($result);
?>