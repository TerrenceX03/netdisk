<?php
include 'common/common.php';
include 'function/policyOperator.php'; 

header('Content-type:text/json');
$user = DB_USER;
$pass = DB_PWD;
$ip = DB_IP;
$port = DB_PORT;
$connection = ssh2_connect($ip, $port);
ssh2_auth_password($connection, $user, $pass);

if ($_GET["myaction"] == "APPLY") { // List a folder 
	//$policy = "RULE 'default' SET POOL 'system'\nRULE 'test-m1' MIGRATE FROM POOL 'silver' TO POOL 'system' FOR FILESET ('2010') WHERE FILE_SIZE >= 0";

	$policy = "RULE 'tmp' MIGRATE TO POOL 'system' FOR FILESET ('2010') WHERE (lower(NAME) LIKE '%.zip' OR lower(NAME) LIKE '%.log')";

	// (DAYS(CURRENT_TIMESTAMP) – DAYS(ACCESS_TIME) > 30) AND 
	echo json_encode(applyPolicy($connection, $policy));
}

?>