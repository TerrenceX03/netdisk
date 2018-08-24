<?php
include 'common/common.php';
include 'function/poolOperator.php'; 

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

echo json_encode(listPools($connection, FS_NAME));

?>