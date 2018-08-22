<?php

/*
	List all pools under particular filesystem
	$fsname - filesystem name
	$connection - ssh2 connection
*/
function listPools($connection, $fsname) {
	$cmd_ls_pools = "mmlspool " . $fsname;
	$ret_ls_pools = ssh2_exec($connection, $cmd_ls_pools);
	stream_set_blocking($ret_ls_pools, true);
	$ans_ls_pools = stream_get_contents($ret_ls_pools);
	$ans_ls_pools = str_replace(array("\r\n", "\n"), ";", $ans_ls_pools);
	$tmp = explode(";", $ans_ls_pools);

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
    			array_push($pools, $pool);
    		}
    	}
	}

	return $pools;
}
?>