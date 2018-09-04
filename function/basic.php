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