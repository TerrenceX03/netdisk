<?php
include_once 'function/basic.php';

function listFileSet($connection){
    $response = basic_exec($connection, "mmlsfileset " . FS_NAME);
    $tmp = explode("\n", trim($response["output"]));
    $filesets = array();
    $fileset = array();

    for ($i = 2; $i < count($tmp); $i++) {
        if (trim($tmp[$i]) != '') {
            $isMatched = preg_match('#(\w+)\s+(\w+)\s+(\S+)#i', trim($tmp[$i]), $matches);

            if ($isMatched == 1) {
                $fileset["name"] = $matches[1];
                $fileset["status"] = $matches[2];
                $fileset["path"] = $matches[3];
                array_push($filesets, $fileset);
            }
        }
    }

    return $filesets;
}

?>
