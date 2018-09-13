<?php

function basic_exec($connection, $cmd) {
	$result = array();

	$stream = ssh2_exec($connection, $cmd);
    $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

    stream_set_blocking($stream, true);
    stream_set_blocking($errorStream, true);

    $result["output"] = stream_get_contents($stream);
    $result["error"] = stream_get_contents($errorStream);

    fclose($stream);
    fclose($errorStream);

    return $result;
}

function startWith($str, $needle) {
    return strpos($str, $needle) === 0;
}

 function endWith($str, $needle) {   
    $length = strlen($needle);  
    if($length == 0) {    
        return true;  
    }  
    
    return (substr($str, -$length) === $needle);
 }
