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
        $fileset = $_POST['foldername'];

        $mmlsattr_cmd_prefix = str_replace(['FILESYSTEM','FILESET','FILENAME'], [FS_MOUNT_POINT,$fileset, $tmp[$i]], "mmlsattr -L 'FILESYSTEM/FILESET/FILENAME'");
        $stat_cmd_prefix = str_replace(['FILESYSTEM','FILESET','FILENAME'], [FS_MOUNT_POINT,$fileset, $tmp[$i]], "stat -c \"%g,%u,%n,%o,%s,%x,%y,%z \" 'FILESYSTEM/FILESET/FILENAME'");

        //get file information from mmls command
        $exe_mmls_info = ssh2_exec($connection,$mmlsattr_cmd_prefix);
        stream_set_blocking($exe_mmls_info, true);
        $stream_mmls_info = stream_get_contents($exe_mmls_info);
        $stream_mmls_info=str_replace(array("\r\n", "\n"), ",", $stream_mmls_info);
        $props = explode(",", $stream_mmls_info);
        $items_mmls = array();
        foreach ($props as $prop) {
            $tmpArray = explode(":", $prop);
            if (isset($tmpArray[0]) && isset($tmpArray[1])) {
                if ($tmpArray[0]=='creation time') {
                    $tmp_str='';
                    $tmp_str.=trim($tmpArray[1]);
                    $tmp_str.=':';
                    $tmp_str.=trim($tmpArray[2]);
                    $tmp_str.=':';
                    $tmp_str.=trim($tmpArray[3]);
                    $items_mmls[trim($tmpArray[0])] = $tmp_str;
                }
                else{
                    $items_mmls[trim($tmpArray[0])] = trim($tmpArray[1]);
                }
            }
        }
        foreach ($items_mmls as $key => $value) {
            if ($key=='file name') {
                $file['file_path'] = $value;
            }
            if ($key=='metadata replication') {
                $file['metadata_replication'] = $value;
            }
            if ($key=='data replication') {
                $file['data_replication'] = $value;
            }
            if ($key=='immutable') {
                $file['immutable'] = $value;
            }
            if ($key=='appendOnly') {
                $file['appendOnly'] = $value;
            }
            if ($key=='flags') {
                $file['flags'] = $value;
            }
            if ($key=='storage pool name') {
                $file['storage_pool_name'] = $value;
            }
            if ($key=='snapshot name') {
                $file['snapshot_name'] = $value;
            }
            if ($key=='creation time') {
                $file['creation_time'] = $value;
            }
            if ($key=='Misc attributes') {
                $file['Misc_attributes'] = $value;
            }
            if ($key=='Encrypted') {
                $file['Encrypted'] = $value;
            }
        }
        /*
            get file information from stat command
            command:stat -c "%g,%u,%n,%o,%s,%x,%y,%z" s3-dg.pdf
            %F--file type
            %g--File owner's group ID
            %G--File owner's group name
            %i--inode number
            %n--file name
            %o--System format block size
            %s--file size(bytes)
            %t--Main equipment type (hexadecimal)
            %T--Secondary equipment type (hexadecimal)
            %u--Owner's user ID
            %U--Owner's user name
            %x--Last visit time
            %X--Time of the last visit (Epoch Times)
            %y--%Y--Last modified content time
            %z--%z--Finally change the time (file attributes, permission owners, etc., format Epoch Times)
        */
        $exe_stat_info = ssh2_exec($connection,$stat_cmd_prefix);
        stream_set_blocking($exe_stat_info, true);
        $stream_stat_info = stream_get_contents($exe_stat_info);
        $props_stat = explode(",", $stream_stat_info);
        $items_stat = array();
        foreach ($props_stat as $key => $value) {
            $items_stat[$key]=$value;
        }
        $file['file_group_id'] = $items_stat[0];
        $file['user_id'] = $items_stat[1];
        $file['block_size'] = $items_stat[3];
        $file['file_size'] = $items_stat[4];
        $file['L_vist_time'] = $items_stat[5];
        $file['L_mod_time'] = $items_stat[6];
        $file['F_chan_time'] = $items_stat[7];

        array_push($result['data'], $file);
    }
}
echo json_encode($result);
?> 