<?php
include 'function/basic.php';

/*
	List all pools under particular filesystem
	$fsname - filesystem name
	$connection - ssh2 connection
*/
function listPools($connection, $fsname) {
    $response = basic_exec($connection, "mmlspool " . $fsname);
    $tmp = explode(";", str_replace(array("\r\n", "\n"), ";", $response["output"]));

	$pools = array();
	$pool = array();

	for ($i = 0; $i < count($tmp); $i++) {
    	if ($tmp[$i] != '') {
    		$isMatched = preg_match('#(\w+)\s+(\d+)\s+(\d+)\s{1}KB\s+(\w+)\s+(\w+)\s+(\d+)\s+(\d+)\s{1}\(\s{0,2}(\d+)%\)\s+(\d+)\s+(\d+)\s{1}\(\s{0,2}(\d+)%\)#i', $tmp[$i], $matches);
    		if ($isMatched == 1) {
    			$pool["name"] = $matches[1];
    			$pool["id"] = $matches[2];
    			$pool["blocksize"] = $matches[3];
    			$pool["isDataPool"] = $matches[4];
    			$pool["isMetaPool"] = $matches[5];
    			$pool["totaldatasize"] = $matches[6];
    			$pool["freedatasize"] = $matches[7];
    			$pool["freedatapercentage"] = $matches[8];
    			$pool["totalmetasize"] = $matches[9];
    			$pool["freemetasize"] = $matches[10];
    			$pool["freemetapercentage"] = $matches[11];
                $pool["type"] = "internal";
    			array_push($pools, $pool);
    		}
    	}
	}

    $externalPool = array();
    $response = basic_exec($connection, "mmlsconfig tctEnable");
    $tmp = explode(" ", trim($response["output"]));
    if ($tmp[1] == "yes") {
        $response = basic_exec($connection, "mmcloudgateway account list -Y");
        $tmp = explode(";", str_replace(array("\r\n", "\n"), ";", trim($response["output"])));
        
        for ($i = 1; $i < count($tmp); $i++) {
            $tmp2 = explode(":", $tmp[$i]);
            $externalPool["name"] = $tmp2[7];
            $externalPool["cloudtype"] = $tmp2[8];
            $externalPool["username"] = $tmp2[9];
            $externalPool["type"] = "external";
            array_push($pools, $externalPool);
        }
    }

	return $pools;
}
?>
