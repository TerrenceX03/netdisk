<?php
include 'common/common.php';
header('Content-type:text/json');
$result = array();
$result['msg'] = 1;
$user = DB_USER;
$pass = DB_PWD;
$ip = DB_IP;
$port = DB_PORT;
$folder=$_POST['folder'];
$connection = ssh2_connect($ip, $port);
ssh2_auth_password($connection, $user, $pass);
//Create a folder in specific folder.
$cmd_create="mkdir /demofs/";
$cmd_create.=$folder;
$cmd_create.="/his";
$exe_create = ssh2_exec($connection, $cmd_create);
echo json_encode($result);
?>