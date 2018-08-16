<?php 
//Function:Uploading the files and creating the filesets
include 'common/common.php';
$user = DB_USER;
$pass = DB_PWD;
$ip = DB_IP;
$port = DB_PORT;
$connection = ssh2_connect($ip, $port);
//query the exist filesets.
$cmd_ls_fileset = "curl -k -u admin:passw0rd -XGET -H content-type:application/json 'https://172.16.12.95:443/scalemgmt/v2/filesystems/demofs/filesets'";
$ret_ls_fileset = ssh2_exec($connection, $cmd_ls_fileset);
stream_set_blocking($ret_ls_fileset, true);
$ans_ls_fileset = stream_get_contents($ret_ls_fileset);
$ans_ls_fileset_json = json_decode($ans_ls_fileset, true);
echo $ans_ls_fileset;
$folder = array();
foreach($ans_ls_fileset_json['filesets'] as $key => $value) {
    array_push($folder, $value['filesetName']);
}
//creating the fileset 
$result = array();
foreach($_FILES as $k) {
    $array = [];
    $count = count($k['name']);
    for ($i = 0; $i < $count; $i++) {
        $tmp_path = $k['tmp_name'][$i];
        $tmp_name = $k['name'][$i];
        $tmp_type = explode('.', $k['name'][$i])[1];
        $sign = 0;
        foreach($folder as $key => $value) {
            if ($value == $tmp_type) {
                $sign = $sign + 1;
            }
        }
        if ($sign == 0) {
            $cmd_cr_fileset = "mmcrfileset demofs ";
            $cmd_cr_fileset.= $tmp_type;
            $cmd_lk_fileset = "mmlinkfileset demofs ";
            $cmd_lk_fileset.= $tmp_type;
            $cmd_lk_fileset.= " -J /demofs/";
            $cmd_lk_fileset.= $tmp_type;
            ssh2_exec($connection, $cmd_cr_fileset);
            ssh2_exec($connection, $cmd_lk_fileset);
        }
        $tmp_target_path = '/demofs/';
        $tmp_target_path.= $tmp_type;
        $tmp_target_path.= '/';
        $tmp_target_path.= $tmp_name;
        // echo $tmp_target_path;
        ssh2_scp_send($connection, $tmp_path, $tmp_target_path);
    }
}
?>