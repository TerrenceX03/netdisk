<?php
include_once 'function/basic.php';
include 'function/cloudgatewayOperator.php';


/*
    $dirpath - MUST not be end with "/", and white space in the path must be wrapped by "\""
*/
function listFiles($connection, $dirpath) {
    $files = array();
    $files["data"] = _getFiles($connection, $dirpath, "*");

    return $files;
}

/* 
  Get fileinfo of a file and return to files.php.
  
  $filepath:Absolute filepath. 
*/
function getFile($connection, $filepath) {
    $tmp = explode("/", trim($filepath));
    $filename = array_pop($tmp);
    $dirpath = implode("/", $tmp);

    $files = _getFiles($connection, $dirpath, $filename);

    return $files[0];
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

$files：An array,it contains filepath of the files which are located in internal pool now, to be migrated

$externalFiles: An array, it contains filepath of the files which are located in external pool now, to be migrated

$target:  storage pool where files will be migrated to.

$targetPoolType: internal or external pool
*/
function migrate($connection, $files, $externalFiles, $target, $targetPoolType){
    $result = array();
    $response = null;
    $data= null;

    foreach($files as $key => $filepath) {
        if ($targetPoolType == "internal") {
            $response = basic_exec($connection, "mmchattr -P " . $target . " '" . $filepath . "'");
        } else if ($targetPoolType == "external") {
            $response = basic_exec($connection, "mmcloudgateway files migrate '" . $filepath . "'");
            $data = getFileCloudInfo($connection, $filepath);
        }
        
        $file = array();
        if (trim($response["error"]) == "") {
            $file["result"] = 1;
            $file["file"] = $data;
        } else {
            $file["result"] = 0;
            $file["error"] = trim($response["error"]);
        }
        
        array_push($result, $file);
    }

    foreach($externalFiles as $key => $filepath) {
        $file = array();

        if ($targetPoolType == "internal") {
            $response = basic_exec($connection, "mmcloudgateway files recall '" . $filepath . "'");
            
            if (trim($response["error"]) == "") {
                $data = getFileCloudInfo($connection, $filepath);
                $response = basic_exec($connection, "mmchattr -P " . $target . " '" . $filepath . "'");
                if (trim($response["error"]) == "") {
                    $file["result"] = 1; 
                    $file["file"] = $data;   
                } else {
                    $file["result"] = 0;
                    $file["error"] = trim($response["error"]);
                }    
            } else {
                $file["result"] = 0;
                $file["error"] = trim($response["error"]);
            }
        
        } else {
            $file["result"] = 0;
            $file["error"] = "Cannot migrate a file from external pool to another external pool.";
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

/*
get files information
        
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

$dirpath - MUST not be end with "/", and white space in the path must be wrapped by "\"
$filename - white space in the path must be wrapped by "\"

*/

function _getFiles($connection, $dirpath, $filename) {
    $files = array();
    $map = array();
    $file = array();

    $response = basic_exec($connection, "mmlsattr -L " . $dirpath . "/" . $filename);

    if (trim($response["output"]) != "" && trim($response["error"]) == "") {
        $lines = explode(",", str_replace(array("\r\n", "\n"), ",", trim($response["output"])));
        foreach ($lines as $line) {
            if (trim($line) == "") {
                $file['folder_name'] = $dirpath;
                $map[$file['file_name']] = $file;
                $file = array();
            } else {
                $isMatched = preg_match('#(\w+.[^:]*):\s*(.*)\s*#i', $line, $matches);
                if ($isMatched == 1) {
                    $file[str_replace(" ", "_", strtolower(trim($matches[1])))] = $matches[2];
                }
            }
        }

        $map[$file['file_name']] = $file;

        $response = basic_exec($connection, "stat -c \"%n|%g|%u|%o|%s|%x|%y|%z|%F\"  " . $dirpath . "/" . $filename);
        $lines = explode(",", str_replace(array("\r\n", "\n"), ",", trim($response["output"])));
        foreach ($lines as $line) {
            $props = explode("|", $line);
            $filename = $props[0];
            $map[$filename]['file_group_id'] = $props[1];
            $map[$filename]['user_id'] = $props[2];
            $map[$filename]['block_size'] = $props[3];
            $map[$filename]['file_size'] = round($props[4]/1024,1);
            $map[$filename]['l_vist_time'] = date("Y-m-d H:i:s", strtotime($props[5]));
            $map[$filename]['l_mod_time'] = date("Y-m-d H:i:s", strtotime(explode(".",$props[6])[0]));
            $map[$filename]['f_chan_time'] = date("Y-m-d H:i:s", strtotime($props[7]));
            $map[$filename]['type'] = trim($props[8]);
            $map[$filename]["action"] = "GET";
        }

        foreach ($map as $file) {
            $file['file_path'] = $file['file_name'];
            $file['file_name'] = substr($file['file_name'], strlen($dirpath) + 1);
            array_push($files, $file);
        }
    }
    
    return $files;
}

?>
