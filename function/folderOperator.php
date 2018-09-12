<?php
include 'function/fileOperator.php';

/* $folderpath:Current path of the folder.
   $foldername:the foldername you type in.
*/
function postFolder($connection,$folderpath,$foldername){
	$success = false;
	$result = array();

    if ($folderpath && $foldername) {
        $response = basic_exec($connection, "mkdir " . $folderpath . $foldername);

        if (trim($response["error"]) == "") {
            $result["result"] = 1;
            $serverSideFile = getFile($connection, $folderpath . $foldername, false);
            $serverSideFile["filename"] = $foldername;
        	$result["files"] = $serverSideFile;
        } else {
            $result["result"] = 0;
            $result["error"] = "Failed to create new folder: " . $folderpath . $foldername . " - " . trim($response["error"]);
        }
    } else {
    	$result["result"] = 0;
        $result["error"] = "Please check your folder path and folder name.";
    }

    return $result;
}

?>
