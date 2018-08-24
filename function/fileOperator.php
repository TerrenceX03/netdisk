<?php 
/* 
  Get fileinfo of a file and return to files.php.
  
  $filepath:Absolute filepath. 
*/
function getFile($connection, $filepath) {
    $mmlsattr_cmd = "mmlsattr -L '" . $filepath . "'";
    $stat_cmd = "stat -c \"%g,%u,%n,%o,%s,%x,%y,%z\"  '" . $filepath . "'";
    //get file information from mmls command
    $exe_mmls_info = ssh2_exec($connection,$mmlsattr_cmd);
    stream_set_blocking($exe_mmls_info, true);
    $stream_mmls_info = stream_get_contents($exe_mmls_info);
    $stream_mmls_info = str_replace(array("\r\n", "\n"), ",", $stream_mmls_info);
    $lines = explode(",", $stream_mmls_info);
    $file = array();
    //get fileype from file command
    $filetype_cmd = "file '" . $filepath .  "'";
    $exe_filetype = ssh2_exec($connection,$filetype_cmd);
    stream_set_blocking($exe_filetype, true);
    $stream_filetype = stream_get_contents($exe_filetype);
    $filetype = trim(explode(':', $stream_filetype)[1]);
    $file['filetype'] =  $filetype;

    foreach ($lines as $line) {
        $tmpArray = explode(":", $line);
        if (isset($tmpArray[0]) && isset($tmpArray[1])) {
            if ($tmpArray[0] == 'creation time') {
                $tmp_str = '';
                $tmp_str .= trim($tmpArray[1]);
                $tmp_str .= ':';
                $tmp_str .= trim($tmpArray[2]);
                $tmp_str .= ':';
                $tmp_str .= trim($tmpArray[3]);
                $file['creation_time'] = date("Y-m-d H:i:s", strtotime($tmp_str));
            } elseif ($tmpArray[0] == 'file name') {
                $file['file_path'] = $tmpArray[1];
                $tmp_folder_path = str_replace("/", ",", trim($tmpArray[1]));
                $tmp_folder_path = explode(',', $tmp_folder_path);
                array_pop($tmp_folder_path);
                $tmp_path_str = '';
                foreach ($tmp_folder_path as $key => $value) {
                    if ($value != '') {
                        $tmp_path_str .= $value;
                        $tmp_path_str .= "/";
                    }
                }
                $file['folder_path'] = $tmp_path_str;
            } elseif ($tmpArray[0] == 'metadata replication') {
                $file['metadata_replication'] = $tmpArray[1];
            } elseif ($tmpArray[0] == 'data replication') {
                $file['data_replication'] = $tmpArray[1];
            } elseif ($tmpArray[0] == 'storage pool name') {
                $file['storage_pool_name'] = $tmpArray[1];
            } elseif ($tmpArray[0] == 'snapshot name') {
                $file['snapshot_name'] = $tmpArray[1];
            }  elseif ($tmpArray[0] == 'Misc attributes') {
                $file['Misc_attributes'] = $tmpArray[1];
            } else {
                $props[trim($tmpArray[0])] = trim($tmpArray[1]);
            }
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
    $exe_stat_info = ssh2_exec($connection,$stat_cmd);
    stream_set_blocking($exe_stat_info, true);
    $stream_stat_info = stream_get_contents($exe_stat_info);
    $props_stat = explode(",", $stream_stat_info);
    $items_stat = array();
    foreach ($props_stat as $key => $value) {
        $items_stat[$key] = $value;
    }
    $file['file_group_id'] = $items_stat[0];
    $file['user_id'] = $items_stat[1];
    $file['block_size'] = $items_stat[3];
    $file['file_size'] = round($items_stat[4]/1024,1);
    $file['L_vist_time'] = date("Y-m-d H:i:s", strtotime($items_stat[5]));
    $file['L_mod_time'] = date("Y-m-d H:i:s", strtotime(explode(".",$items_stat[6])[0]));
    $file['F_chan_time'] = date("Y-m-d H:i:s", strtotime($items_stat[7]));

    return $file;
}

/* 
   Get files information in specific dirpath.
   
   $dirpath:Absolute path.
*/
function listFiles($connection, $dirpath) {
    //query the exist filesets.
    $cmd_ls_files = "ls " . $dirpath;
    $ret_ls_fileset = ssh2_exec($connection, $cmd_ls_files);
    stream_set_blocking($ret_ls_fileset, true);
    $ans_ls_fileset = stream_get_contents($ret_ls_fileset);
    $tmp = explode("\n", $ans_ls_fileset);

    $files = array();
    $files['data'] = array();
    $file = array();

    for ($i = 0; $i < count($tmp); $i++) {
        if ($tmp[$i] != '') {
            $file = getFile($connection, $dirpath . "/" . $tmp[$i]);
            $file['filename'] = $tmp[$i];

            array_push($files['data'], $file);
        }
    }
    return $files;
}

/*
   Upload file to dirpath.
*/
function postFile($connection, $file, $dirpath) {
    $success = false;
    $filepath = $dirpath . "/" . $file["name"];

    if ($file && $file["tmp_name"] && $dirpath) {
        $success = ssh2_scp_send($connection,  $file["tmp_name"], $filepath);
    }

    $result = array();
    $result["files"] = array();
    $tmpfile = array();
    $tmpfile["name"] = $file["name"];
    $tmpfile["size"] = $file["size"];
    
    if ($success) {
        $tmpfile["type"] = $file["type"];
        $tmpfile["url"] = "";
        $tmpfile["deleteUrl"] = "";
        $tmpfile["deleteType"] = "DELETE";

        $serverSideFile = getFile($connection, $filepath);
        $tmpfile = array_merge($tmpfile, $serverSideFile);
        $tmpfile["size"] = $tmpfile["file_size"];
    } else {
        $tmpfile["error"] = "Failed to upload!";
    }

    array_push($result["files"], $tmpfile);

    return $result;
}

/* 
Modify the storage tier of the file with the specified ID

$id：An array,it contains IDs of the files to be modified

$tier：The storage tier where files will be moved in.
*/
function movePool($connection,$id,$tier,$folder){
    $result = array();
    $result['msg'] = 1;
    foreach($id as $key => $value) {
        $filename = $value;
        $filename = preg_replace('/ /','\ ',$filename);
        $fileset = $folder[0];
        $cmd_chpool = "mmchattr -P " . $tier . " " . FS_MOUNT_POINT . "/" . $fileset . "/" . $filename;
        $exe_chpool = ssh2_exec($connection, $cmd_chpool);
    }
    return $result;
}

/* 
Delete files with specfic filepaith

$filepath:Absolute filepath without FS_MOUNT_POINT.
*/
function deleteFiles($connection,$filepath){
    $result = array();
    $result['msg'] = 1;
    //Delete cammand
    $cmd_delete = "rm -rf \"".FS_MOUNT_POINT."/" . $filepath . "\"";

    $exe_delete = ssh2_exec($connection, $cmd_delete);
    return $result;
}

?>