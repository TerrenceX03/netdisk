<?php
/*
Function: Get fileset name in the folder that belongs to the specified storage tier and return it

$result['id']:An array,it is used to hold the acquired file IDs

*/
include 'common/common.php';
header('Content-type:text/json');
$user = DB_USER;
$pass = DB_PWD;
$ip = DB_IP;
$port = DB_PORT;
$connection = ssh2_connect($ip, $port);
ssh2_auth_password($connection, $user, $pass);

$result = array();
$result['msg'] = 1;
$result['id'] = array();
$id = $_POST['id_'];

$num = count($id);
for ($i = 0; $i < $num; $i++) {
    $fileset = explode('.', $id[$i])[1];
    $cmd_tier_info = "mmlsattr -L ";
    $cmd_tier_info.= " ";
    $cmd_tier_info.= FS_MOUNT_POINT;
    $cmd_tier_info.= "/"
    $cmd_tier_info.= $fileset;
    $cmd_tier_info.= "/";
    $cmd_tier_info.= $id[$i];
    $cmd_tier_info.= "|sed s/[[:space:]]//g|cut -d: -f2|awk 'NR==7'";
    $exe_tier_info = ssh2_exec($connection, $cmd_tier_info);
    stream_set_blocking($exe_tier_info, true);
    $stream_tier_info = stream_get_contents($exe_tier_info);
    $stream_tier_info_ = str_replace(array("\r\n", "\r", "\n"), "", $stream_tier_info);

    if ($stream_tier_info_ == $_POST['storage_']) {
        array_push($result['id'], $id[$i]);
    }
}
echo json_encode($result);
?>
