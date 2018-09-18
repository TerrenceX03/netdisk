<?php
include_once 'function/basic.php';
include 'function/fileOperator.php';

/* $folderpath:Current path of the folder.
   $foldername:the foldername you type in.
*/
function postFolder($connection, $folderpath, $foldername){
	$result = array();
    $fullpath = $folderpath . "/" . $foldername;

    if ($folderpath && $foldername) {
        $response = basic_exec($connection, "mkdir " . $fullpath);

        if (trim($response["error"]) == "") {
            $result["result"] = 1;
            $serverSideFile = getFile($connection, $fullpath);
            $serverSideFile["filename"] = $foldername;
        	$result["files"] = $serverSideFile;
        } else {
            $result["result"] = 0;
            $result["error"] = "Failed to create new folder: " . $fullpath . " - " . trim($response["error"]);
        }
    } else {
    	$result["result"] = 0;
        $result["error"] = "Please check your folder path and folder name.";
    }

    return $result;
}

?>
