<?php
include 'common/common.php'; 
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

//query the exist filesets.
$cmd_ls_files = str_replace(['FILESYSTEM','FILESET'], [FS_MOUNT_POINT,$_POST['foldername']], 'ls FILESYSTEM/FILESET');
$ret_ls_fileset = ssh2_exec($connection, $cmd_ls_files);
stream_set_blocking($ret_ls_fileset, true);
$ans_ls_fileset = stream_get_contents($ret_ls_fileset);
$tmp = explode("\n", $ans_ls_fileset);

$result = array();
$result['data'] = array();
$file = array();

for ($i = 0; $i < count($tmp); $i++) {
    if ($tmp[$i] != '') {
        $file['filename'] = $tmp[$i];

        // $fileset = explode('.', $tmp[$i])[1];
        $fileset = $_POST['foldername'];

        $mmlsattr_cmd_prefix = str_replace(['FILESYSTEM','FILESET','FILENAME'], [FS_MOUNT_POINT,$fileset, $tmp[$i]], "mmlsattr -L 'FILESYSTEM/FILESET/FILENAME'");

        $stat_cmd_prefix = str_replace(['FILESYSTEM','FILESET','FILENAME'], [FS_MOUNT_POINT,$fileset, $tmp[$i]], "stat 'FILESYSTEM/FILESET/FILENAME'");

        //path information
        $path_info = $mmlsattr_cmd_prefix;
        $path_info .= "|sed s/[[:space:]]//g|cut -d: -f2|awk 'NR==1'";
        $exe_path_info = ssh2_exec($connection,$path_info);
        stream_set_blocking($exe_path_info, true);
        $stream_path_info = stream_get_contents($exe_path_info);
        $stream_path_info=str_replace(array("\r\n", "\r", "\n"), "", $stream_path_info);
        $file['filepath'] = $stream_path_info;

        //pool information
        $pool_info = $mmlsattr_cmd_prefix;
        $pool_info .= "|sed s/[[:space:]]//g|cut -d: -f2|awk 'NR==7'";
        $exe_tier_info = ssh2_exec($connection, $pool_info);
        stream_set_blocking($exe_tier_info, true);
        $stream_tier_info = stream_get_contents($exe_tier_info);
        $stream_tier_info=str_replace(array("\r\n", "\r", "\n"), "", $stream_tier_info);
        $file['tier'] = $stream_tier_info;

        // created time
        $crtime_info = $mmlsattr_cmd_prefix;
        $crtime_info .= "|awk '$0~\"creation time\"'|cut -d: -f 2-4|awk '{sub(/^[ \\t]+/,\"\");print $0}'";
        $exe_crtime_info = ssh2_exec($connection, $crtime_info);
        stream_set_blocking($exe_crtime_info, true);
        $stream_crtime_info = stream_get_contents($exe_crtime_info);
        $stream_crtime_info=str_replace(array("\r\n", "\r", "\n"), "", $stream_crtime_info);
        $file['crtime'] = $stream_crtime_info;

        //modified time
        $modtime_info = $stat_cmd_prefix;
        $modtime_info .= "|awk 'NR==6'|cut -d: -f 2-3";
        $exe_modtime = ssh2_exec($connection, $modtime_info);
        stream_set_blocking($exe_modtime, true);
        $stream_modtime = stream_get_contents($exe_modtime);
        $stream_modtime=str_replace(array("\r\n", "\r", "\n"), "", $stream_modtime);
        $file['modtime'] = $stream_modtime;

        //file size
        $filesize_info = $stat_cmd_prefix;
        $filesize_info .= "|awk 'NR==2'|awk '{print $2}'";
        $exe_filesize = ssh2_exec($connection, $filesize_info);
        stream_set_blocking($exe_filesize, true);
        $stream_filesize = stream_get_contents($exe_filesize);
        $stream_filesize=str_replace(array("\r\n", "\r", "\n"), "", $stream_filesize);
        $file['filesize'] = $stream_filesize;
        array_push($result['data'], $file);
    }
}
echo json_encode($result);
?> 