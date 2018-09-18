<?php
include_once 'function/basic.php';

/* $folderpath:Current path of the folder.
   $foldername:the foldername you type in.
*/
function applyPolicy($connection, $policy){
    $response = basic_exec($connection, "echo -e \"" . $policy . "\" > /tmp/policy/tmp.policy");

    if (trim($response["error"]) == "") {
        $response = basic_exec($connection, "mmapplypolicy " . FS_NAME . " -P /tmp/policy/tmp.policy -I yes -s '/tmp' --choice-algorithm 'exact' -a 2 -n 24 -m 24 -B 100");
        $output = trim($response["output"]);
        $error = trim($response["error"]);

        if ($output == "" || startWith($error, "[W") || startWith($error, "[E")) {
            $result["result"] = 0;
            $result["error"] = $response["error"];
        } else {
            $result["result"] = 1;
            $result["msg"] = $response["output"];
        }
    } else {
        $result["result"] = 0;
        $result["error"] = $response["error"];
    }

    return $result;
}

?>