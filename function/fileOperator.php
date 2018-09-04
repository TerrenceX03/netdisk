<?php
include 'function/basic.php';
/* 
  Get fileinfo of a file and return to files.php.
  
  $filepath:Absolute filepath. 
*/
function getFile($connection, $filepath) {
    $mmlsattr_cmd = "mmlsattr -L '" . $filepath . "'";
    $stat_cmd = "stat -c \"%g,%u,%n,%o,%s,%x,%y,%z,%F\"  '" . $filepath . "'";
    $filetype_cmd = "file '" . $filepath .  "'";

    $file = array();

    //get file information from mmlsattr command
    $response1 = basic_exec($connection, $mmlsattr_cmd);
    $lines = explode(",", str_replace(array("\r\n", "\n"), ",", $response1["output"]));

    //get fileype from file command
    $response2 = basic_exec($connection, $filetype_cmd);
    $file['filetype'] =  trim(explode(':', $response2["output"])[1]);

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
                $file['file_path'] = trim($tmpArray[1]);
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
                $file['metadata_replication'] = trim($tmpArray[1]);
            } elseif ($tmpArray[0] == 'data replication') {
                $file['data_replication'] = trim($tmpArray[1]);
            } elseif ($tmpArray[0] == 'storage pool name') {
                $file['storage_pool_name'] = trim($tmpArray[1]);
            } elseif ($tmpArray[0] == 'snapshot name') {
                $file['snapshot_name'] =trim($tmpArray[1]);
            }  elseif ($tmpArray[0] == 'Misc attributes') {
                $file['Misc_attributes'] = trim($tmpArray[1]);
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
    $response3 = basic_exec($connection, $stat_cmd);
    $props_stat = explode(",", $response3["output"]);
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
    $file['type'] = trim($items_stat[8]);
    $file["action"] = "GET";

    return $file;
}

/* 
   Get files information in specific dirpath.
   
   $dirpath:Absolute path.
*/
function listFiles($connection, $dirpath) {
    $response = basic_exec($connection, "ls " . $dirpath);
    $tmp = explode("\n", $response["output"]);

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
    $tmpfile["action"] = "POST";
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

$files：An array,it contains filepath of the files to be migrated

$target:  storage pool where files will be migrated to.
*/
function migrate($connection, $files, $target){
    $result = array();

    foreach($files as $key => $filepath) {
        $response = basic_exec($connection, "mmchattr -P " . $target . " " . $filepath);
        $file = array();

        if (trim($response["error"]) == "") {
            $file["result"] = 1;    
        } else {
            $file["result"] = 0;
            $file["error"] = trim($response["error"]);
        }
        
        array_push($result, $file);
    }

    return $result;
}

/* 
Delete files with specfic filepaith

$filepath:Absolute filepath without FS_MOUNT_POINT.
*/
function deleteFiles($connection, $filepaths){
    $result = array();
    
    foreach($filepaths as $key => $filepath) {
        $response = basic_exec($connection, "rm -f '" . $filepath . "'");
        $file = array();

        if (trim($response["error"]) == "") {
            $file["result"] = 1;    
        } else {
            $file["result"] = 0;
            $file["error"] = trim($response["error"]);
        }
        
        array_push($result, $file);
    }

    return $result;
}

?>