<?php
/* 
function:Get all the pools' information and return the size value
*/
include 'common/common.php';
header('Content-type:text/json');
$result = array();
$tier = $_POST['tier'];
$tier_size = $_POST['tier_size'];
$user = DB_USER;
$pass = DB_PWD;
$ip = DB_IP;
$port = DB_PORT;
$connection = ssh2_connect($ip, $port);
ssh2_auth_password($connection, $user, $pass);
//specified poolsize
$cmd_poolsize = "mmlspool ";
$cmd_poolsize.= FS_NAME;
$cmd_poolsize.= "|awk '$0~ ";
$cmd_poolsize.= "\"";
$cmd_poolsize.= $tier;
$cmd_poolsize.= "\"";
$cmd_poolsize.= "'|awk '$0~\"KB\"'|awk '{print $7}'";
$exe_poolsize = ssh2_exec($connection, $cmd_poolsize);
stream_set_blocking($exe_poolsize, true);
$stream_poolsize = stream_get_contents($exe_poolsize);
$stream_poolsize = (int) $stream_poolsize / 1024;
//freesize 
$cmd_freesize = "mmlspool ";
$cmd_freesize.= FS_NAME;
$cmd_freesize.= "|awk '$0~ ";
$cmd_freesize.= "\"";
$cmd_freesize.= $tier;
$cmd_freesize.= "\"";
$cmd_freesize.= "'|awk '$0~\"KB\"'|awk '{print $8}'";
$exe_freesize = ssh2_exec($connection, $cmd_freesize);
stream_set_blocking($exe_freesize, true);
$stream_freesize = stream_get_contents($exe_freesize);
$stream_freesize = (int) $stream_freesize / 1024;

$used_size = $stream_poolsize - $stream_freesize;
$per = $used_size / $tier_size;
$per = $per * 100;
$per = round($per);
$result['perc'] = $per;
$result['size'] = $used_size;
echo json_encode($result);
?>