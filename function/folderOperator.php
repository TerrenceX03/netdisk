<?php
/* $folderpath:Current path of the folder.
   $foldername:the foldername you type in.
*/
function createFolder($connection,$folderpath,$foldername){
	$result = array();
	$result['msg'] = 1;
	$cmd_create="mkdir ";
	$cmd_create.=$folderpath;
	$cmd_create.=$foldername;
	$exe_create = ssh2_exec($connection, $cmd_create);
	return $result;
}

?>