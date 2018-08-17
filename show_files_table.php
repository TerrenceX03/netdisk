<?php
/*
Function:Return the html of files which in specific filesets.
*/
include 'common/common.php';
header('Content-type:text/json');
$user = DB_USER;
$pass = DB_PWD;
$ip = DB_IP;
$port = DB_PORT;
$connection = ssh2_connect($ip, $port);
ssh2_auth_password($connection, $user, $pass);
//query the exist filesets.
$cmd_ls_files1 = 'cd ..';
$cmd_ls_files2 = 'cd demofs/';
$cmd_ls_files2.= $_POST['Name'];
$cmd_ls_files3 = 'ls';
$ret_ls_fileset = ssh2_exec($connection, "$cmd_ls_files1;$cmd_ls_files2;$cmd_ls_files3");
stream_set_blocking($ret_ls_fileset, true);
$ans_ls_fileset = stream_get_contents($ret_ls_fileset);
// var_dump(strlen($ans_ls_fileset));
$tmp = explode("\n", $ans_ls_fileset);
$result = array();
$result['msg'] = 1;
$result['filename'] = array();
$result['id'] = array();
for ($i = 0; $i < count($tmp); $i++) {
    if ($tmp[$i] != '') {
        array_push($result['filename'], $tmp[$i]);
        array_push($result['id'], $tmp[$i]);
    }
}
$str = '';
for ($j = 0; $j < count($result['filename']); $j++) {
    /*
    Add the html of checkbox which is not display in default
    Add the label of files' name
    */
    $tmp = show_files_table($result['filename'][$j], $result['id'][$j], 'display1()');
    $str.= $tmp;
}
$str.= "<input type='button' value=批量选取 name=selects onclick=showche()><br>";
$result['html'] = $str;
echo json_encode($result);
?> 
             
                    
