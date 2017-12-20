<?php
$jsonLisOfPoUrl = "http://sy.fcsystem.com/WsGetJsonFromSql/WsGetJsonFromSql.php?apikey=595c015a-98c4-4097-9d89-c5b83ed28ff1&isonlyresult=TRUE&sqlrfilepath=D2D_PO.sql&num_po=4136783r01";

$servername = "localhost";
$username = "pgweb";
$password = "[14pG47#)";
$dbname = "e-solu";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

jsonToMysql();

$GLOBALS['conn']->close();


function jsonToMysql(){
	$contents = file_get_contents($GLOBALS['jsonLisOfPoUrl']);
	$contents = utf8_encode($contents);
	$json_data = json_decode($contents, true);
	foreach($json_data as $v){
		fillWithPo($v['num_po']);
	}

}

function fillWithPo($whichNumPo){
	fill_usergroups($whichNumPo);
	fill_viewlevels($whichNumPo);
	fill_chatrooms($whichNumPo);
}

function fill_usergroups($whichNumPo){
	$whichNumPo ="PO_".$whichNumPo;

	$sqlr = "CALL add_group(1, '".$whichNumPo."');";
	
	print $sqlr.PHP_EOL;
	if ($GLOBALS['conn']->query($sqlr) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sqlr . "<br>" . $GLOBALS['conn']->error;
	}
	
}

function fill_viewlevels($whichNumPo){
	$whichNumPo ="PO_".$whichNumPo;
	$sqlr = "
	INSERT INTO kgift_viewlevels (title, ordering, rules) 
	SELECT DISTINCT
	'".$whichNumPo."', 
	'0', 
	CONCAT( '[', (
		SELECT id FROM kgift_usergroups WHERE title = '".$whichNumPo."'
	), ']' )
	FROM kgift_viewlevels
	WHERE 0 = (
		SELECT COUNT(*)
		FROM kgift_viewlevels
		WHERE title='".$whichNumPo."'
	)
	;
	 ";
	print $sqlr.PHP_EOL;
	if ($GLOBALS['conn']->query($sqlr) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sqlr . "<br>" . $GLOBALS['conn']->error;
	}
	
}

function fill_chatrooms($whichNumPo){
	$sqlr = "
	INSERT INTO kgift_jchat_rooms (name ,	description ,	checked_out ,	checked_out_time ,	published ,	ordering ,	access	)
	SELECT DISTINCT
	'".$whichNumPo."', 
	'".$whichNumPo."',
	'0',
	'0000-00-00 00:00:00',
	'1',
	'2',
	(
		SELECT id FROM kgift_viewlevels WHERE title = 'PO_".$whichNumPo."'
	)
	FROM kgift_jchat_rooms
	WHERE 0 = (
		SELECT COUNT(*)
		FROM kgift_jchat_rooms
		WHERE name='".$whichNumPo."'
	)
	;
	";
	print $sqlr.PHP_EOL;
	if ($GLOBALS['conn']->query($sqlr) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sqlr . "<br>" . $GLOBALS['conn']->error;
	}

}

?> 

