<?php

include 'common/common.php';
header('Content-type:text/json');
$result = array();
$result['msg'] = 1;
$result['filename'] = $_POST['filename'];
$user="root";//远程用户名
  $pass="root";//远程密码
  $connection=ssh2_connect('9.181.159.95',22);
  ssh2_auth_password($connection,$user,$pass);
  $fileset=explode('.', $result['filename'])[1];
  $cmd_path_info="mmlsattr -L ";
  $cmd_path_info.="/demofs/";
  $cmd_path_info.=$fileset;
  $cmd_path_info.="/";
  $cmd_path_info.=$result['filename'];
  $cmd_path_info.="|sed s/[[:space:]]//g|cut -d: -f2|awk 'NR==1'";
  $exe_path_info=ssh2_exec($connection, $cmd_path_info);
  stream_set_blocking($exe_path_info, true);
  $stream_path_info=stream_get_contents($exe_path_info);
  $result['filepath']=$stream_path_info;
 //echo($result['filepath']);

  $cmd_tier_info="mmlsattr -L ";
  $cmd_tier_info.="/demofs/";
  $cmd_tier_info.=$fileset;
  $cmd_tier_info.="/";
  $cmd_tier_info.=$result['filename'];
  $cmd_tier_info.="|sed s/[[:space:]]//g|cut -d: -f2|awk 'NR==7'";
  $exe_tier_info=ssh2_exec($connection, $cmd_tier_info);
  stream_set_blocking($exe_tier_info, true);
  $stream_tier_info=stream_get_contents($exe_tier_info);
  $result['tier']=$stream_tier_info;
  //echo($result['tier']);

  $cmd_crtime_info="mmlsattr -L ";
  $cmd_crtime_info.="/demofs/";
  $cmd_crtime_info.=$fileset;
  $cmd_crtime_info.="/";
  $cmd_crtime_info.=$result['filename'];
  $cmd_crtime_info.="|awk '$0~\"creation time\"'|cut -d: -f 2-4|awk '{sub(/^[ \\t]+/,\"\");print $0}'";
  $exe_crtime_info=ssh2_exec($connection, $cmd_crtime_info);
  stream_set_blocking($exe_crtime_info, true);
  $stream_crtime_info=stream_get_contents($exe_crtime_info);
  $result['crtime']=$stream_crtime_info;
  //echo($result['crtime']);
  echo json_encode($result);
?> 