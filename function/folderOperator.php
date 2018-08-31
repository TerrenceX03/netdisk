<?php
include 'function/fileOperator.php';

/* $folderpath:Current path of the folder.
   $foldername:the foldername you type in.
*/
function postFolder($connection,$folderpath,$foldername){
	$success = false;
	$result = array();

    if ($folderpath && $foldername) {
    	$cmd_create="mkdir " . $folderpath . $foldername;
		$ret_mkdir = ssh2_exec($connection, $cmd_create);
		stream_set_blocking($ret_mkdir, true);
        $ans_mkdir = stream_get_contents($ret_mkdir);
        
        if (trim($ans_mkdir) == "") {
            $result["result"] = 1;
            $serverSideFile = getFile($connection, $folderpath . $foldername);
            $serverSideFile["filename"] = $foldername;
        	$result["files"] = $serverSideFile;
        } else {
            $result["result"] = 0;
            $result["error"] = "Failed to create new folder: " . $folderpath . $foldername . " - " . trim($ans_mkdir);
        }
    } else {
    	$result["result"] = 0;
        $result["error"] = "Please check your folder path and folder name.";
    }

    return $result;
}

?>