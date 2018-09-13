<?php
include_once 'function/basic.php'; 

function listContainerPairSet($connection) {
    $response = basic_exec($connection, "mmcloudgateway containerPairSet list -Y");
    $tmp = explode("\n", trim($response["output"]));
    $pairs = array();
    $pair = array();

    $cloudservices = array();
    $accounts = array();

    for ($i = 1; $i < count($tmp); $i++) {
        if (trim($tmp[$i]) != '') {
            $params = explode(":", trim($tmp[$i]));
            $pair["name"] = $params[7];

            $cloudserviceName = $params[8];
            $pair["cloudservice"] = $cloudserviceName;
            if (!isset($cloudservices[$cloudserviceName])) {
                $cloudservices[$cloudserviceName] = getCloudService($connection, $cloudserviceName);
            }
            $pair["cloudservicetype"] = $cloudservices[$cloudserviceName]["type"];
            
            $accountName = $cloudservices[$cloudserviceName]["account"];
            $pair["account"] = $accountName;
            if (!isset($accounts[$accountName])) {
                $accounts[$accountName] = getAccount($connection, $accountName);
            }
            $pair["accounttype"] = $accounts[$accountName]["type"];
            $pair["accountusername"] = $accounts[$accountName]["username"];

            $pair["scopeto"] = $params[9];
            $pair["path"] = $params[10];
            $pair["datacontainer"] = $params[11];
            $pair["metacontainer"] = $params[12];
            $pair["cloudpath"] = $params[13];
            $pair["fileset"] = $params[20];
            $pair["filesystem"] = $params[21];
            array_push($pairs, $pair);
        }
    }

    return $pairs;
}

function getCloudService($connection, $name) {
    $cloudservice = array();
    $response = basic_exec($connection, "mmcloudgateway cloudService list --cloud-service-name " . $name . " -Y");
    $tmp = explode("\n", trim($response["output"]));

    if (isset($tmp[1]) && trim($tmp[1]) != '') {
        $params = explode(":", trim($tmp[1]));
        $cloudservice["name"] = $name;
        $cloudservice["account"] = $params[8];
        $cloudservice["type"] = $params[9];
    }

    return $cloudservice;
}

function getAccount($connection, $name) {
    $account = array();
    $response = basic_exec($connection, "mmcloudgateway account list --account-name " . $name . " -Y");
    $tmp = explode("\n", trim($response["output"]));

    if (isset($tmp[1]) && trim($tmp[1]) != '') {
        $params = explode(":", trim($tmp[1]));
        $account["name"] = $name;
        $account["type"] = $params[8];
        $account["username"] = $params[9];
    }

    return $account;
}

function getFileCloudInfo($connection, $filepath) {
    $response = basic_exec($connection, "mmcloudgateway files list '" . $filepath . "'");
    $lines = explode(",", str_replace(array("\r\n", "\n"), ",", trim($response["output"])));
    $file = array();

    foreach ($lines as $line) {
        $tmpArray = explode(":", $line);
        if (trim($tmpArray[0]) == "On-line size") {
            $file["online_size"] = trim($tmpArray[1]);
        } else if (trim($tmpArray[0]) == "Used blocks") {
            $file["used_blocks"] = trim($tmpArray[1]);
        } else if (trim($tmpArray[0]) == "Data Version") {
            $file["data_version"] = trim($tmpArray[1]);
        } else if (trim($tmpArray[0]) == "Meta Version") {
            $file["meta_version"] = trim($tmpArray[1]);
        } else if (trim($tmpArray[0]) == "State") {
            $file["state"] = trim($tmpArray[1]);
        } else if (trim($tmpArray[0]) == "Container Index") {
            $file["container_index"] = trim($tmpArray[1]);
        } else if (trim($tmpArray[0]) == "Base Name") {
            $file["base_name"] = trim($tmpArray[1]);
        }
    }

    return $file;
}

function isTctTeringEnabled($connection, $dirpath) {
    $containerPairSets = listContainerPairSet($connection);
    $result = array();
    $result["enabled"] = false;

    foreach ($containerPairSets as $pair) {
        if (($pair["scopeto"] == "filesystem") || ($pair["scopeto"] == "fileset" && startWith(trim($dirpath), $pair["path"]))) {
            $result["cloudservice"] = $pair["cloudservice"];
            $result["enabled"] = true;

            $cloudservice = getCloudService($connection, $pair["cloudservice"]);
            $result["account"] = $cloudservice["account"];

            return $result;
        } 
    }

    return $result;
}

?>