<?php
/*
Function:Connect to the database
$host：localhost
$user：username of database
$pwd：userpassword of database
$dbName：database name
example:$link = dbConnect('localhost','root','','person');
*/
function dbConnect($host, $user, $pwd, $dbName, $charset = 'utf8') {
    $link = mysqli_connect($host, $user, $pwd);
    if (!$link) {
        exit('error(' . mysqli_connect_errno() . '):' . mysqli_connect_error());
        mysqli_close($link);
    }
    $db = mysqli_select_db($link, $dbName);
    if (!$db) {
        echo 'error(' . mysqli_errno($link) . '):' . mysqli_error($link);
        mysqli_close($link);
    }
    mysqli_set_charset($link, $charset);
    return $link;
}
/*
Function:Insert the information to database
$link：Connect the database and receive the return
$table：The name of table
$data：The data you want to insert
example:dbInsert($link,'user',$arr);$arr = ['id'=>1120,'name'=>'xiaom','password'=>'11123'];
*/
function dbInsert($link, $table, $data) {
    $fields = join(',', array_keys($data));
    $values = "'" . join("','", array_values($data)) . "'";
    $sql = "insert into {$table}($fields) values({$values})";
    $result = mysqli_query($link, $sql);
    if ($result) {
        return true;
    }
    return false;
}
/*
Function:Modify the database
$link：Connect the database and receive the return
$table：The name of table
$set：The data you want to modify
$where：Modified field
example:$arr = ['name'=>'xiaofang1','password'=>md5('123')];dbUpdate($link,'user',$arr,'id=4');
*/
function dbUpdate($link, $table, $set, $where) {
    $sql1 = "UPDATE $table SET tier='$set'
WHERE id='$where'";
    $result = mysqli_query($link, $sql1);
}
/*
Function:Select the information of table based on ID
$link：Connect the database and receive the return
$table：The name of table
$where：The ID you want to get information
*/
function dbSelect($link, $table, $where) {
    $sql = "SELECT * FROM $table WHERE id='$where'";
    $result = $link->query($sql);
    $row = $result->fetch_assoc();
    if ($row > 0) {
        return $row;
    }
}
/*
Function:Return all the information from table
$link：Connect the database and receive the return
$table：The name of table
*/
function Select_all_filesets() {
  $connection=ssh2_connect(DB_IP,DB_PORT);
  ssh2_auth_password($connection,DB_USER,DB_PWD);
  $cmd_template = "curl -k -u GUI_USER:GUI_PWD -XGET -H content-type:application/json 'https://GUI_IP:GUI_PORT/scalemgmt/v2/filesystems/FILESYSTEM/filesets'";
<<<<<<< HEAD
  $cmd = str_replace(['GUI_USER','GUI_PWD','GUI_IP','GUI_PORT','FILESYSTEM'], [GUI_USER,GUI_PWD,GUI_IP,GUI_PORT,FS_MOUNT_POINT], $cmd_template);
=======
<<<<<<< HEAD
  $cmd = str_replace(['GUI_USER','GUI_PWD','GUI_IP','GUI_PORT','FILESYSTEM'], [GUI_USER,GUI_PWD,GUI_IP,GUI_PORT,FS_MOUNT_POINT], $cmd_template);
=======
  $cmd = str_replace(['GUI_USER','GUI_PWD','GUI_IP','GUI_PORT','FILESYSTEM'], [GUI_USER,GUI_PWD,DB_IP,GUI_PORT,FS_MOUNT_POINT], $cmd_template);
>>>>>>> 426f18014c68676cbd35a292bcbc8da0609aa72b
>>>>>>> 65b2930c4176c70b9b843570534e230069cde29a
  $ret=ssh2_exec($connection, $cmd);
  stream_set_blocking($ret, true);
  $ans=stream_get_contents($ret);
  $ans_=json_decode($ans,true);
  $result=array();
  foreach ($ans_['filesets'] as $key => $value) {
    if ($value['filesetName'] != "root") {
        array_push($result, $value['filesetName']);
    }
  }
  return $result;
}
/*
Function:Migration judgement
$link：Connect the database and receive the return
$table：The name of table
$source_tier：original tier
$target_tier:new tier
$max:Set the maximum value of the triggered migration
$min:Set the minum value of the triggered migration
*/
function dbmove_judge($link, $table, $source_tier, $target_tier, $max, $min) {
    $sql = "SELECT * from $table";
    $result = mysqli_query($link, $sql);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $gpfs_size = 0;
    $rows_gpfs = array();
    for ($j = 0; $j < count($rows); $j++) {
        if ($rows[$j]['tier'] == $source_tier) {
            array_push($rows_gpfs, $rows[$j]);
            $filesize = $rows[$j]['filesize'];
            $filesize = (float)$filesize;
            $gpfs_size = $gpfs_size + $filesize;
        }
    }
    $i = 0;
    if ($gpfs_size > $max) {
        while ($gpfs_size > $min) {
            dbUpdate($link, "filedata", $target_tier, $rows_gpfs[$i]['id']);
            $gpfs_size = $gpfs_size - $rows_gpfs[$i]['filesize'];
            $i = $i + 1;
        }
    }
}

// function dbUpdate_path($link,$table,$set,$where)
// {
// 	// ['name'=>'xiaofang','password'=>'123']
// 	//echo $where;
// 	$sql1 = "UPDATE $table SET filepath='$set'
// WHERE id='$where'";
// 	$result = mysqli_query($link,$sql1);
// 	if ($result)
// 		return true;
// 	else
// 		return false;
// }
// function dbDelete($link,$table,$where)
// {
// 	if (is_string($where)) {
// 		// 'id = 1'
// 		$sql = "delete from $table where $where";
// 	} else if (is_array($where)) {
// 		// ['id'=>1,'name'='xiaoming']   id=1 and name='xiaoming'
// 		$where = addDeng($where);
// 		$value = join(' and ',array_values($where));
// 		$sql = "delete from $table where $value";
// 	} else {
// 		return false;
// 	}
// 	$result = mysqli_query($link,$sql);
// 	if ($result && mysqli_affected_rows($link)) {
// 		return true;
// 	}
// 	return false;
// }
//$arr = ['id'=>1120,'name'=>'xiaom'];
//echo dbDelete($link,'user',$arr);
?>


