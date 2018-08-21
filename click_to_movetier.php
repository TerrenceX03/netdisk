<?php
/* 
Modify the storage tier of the file with the specified ID

$id：An array,it contains IDs of the files to be modified

$tier：The storage tier where files will be moved in.
*/
include 'common/common.php';
header('Content-type:text/json');
$result = array();
$result['msg'] = 1;
foreach($_POST['id'] as $key => $value) {
    $filename = $value;
    $tier = $_POST['tier'];
    $fileset = $_POST['folder'][0];
    $user = DB_USER;
    $pass = DB_PWD;
    $ip = DB_IP;
    $port = DB_PORT;
    $connection = ssh2_connect($ip, $port);
    ssh2_auth_password($connection, $user, $pass);
    $cmd_chpool = "mmchattr -P ";
    $cmd_chpool.= $tier;
    $cmd_chpool.= " ";
    $cmd_chpool.= FS_MOUNT_POINT;
    $cmd_chpool.= "/"
    $cmd_chpool.= $fileset;
    $cmd_chpool.= "/";
    $cmd_chpool.= $filename;
    $exe_chpool = ssh2_exec($connection, $cmd_chpool);
}
echo json_encode($result);
?>
