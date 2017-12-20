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

$whichUserName = $_GET["whichUserName"];
$whichNumPo = $_GET["whichNumPo"];

//$userName = 'pgWeb';
//$whichNumPo = '4000213g01';

attachUserNameWithPoUserGroup();

$GLOBALS['conn']->close();

function attachUserNameWithPoUserGroup(){
	$whichUserName = $GLOBALS['whichUserName'];
	$whichNumPo = $GLOBALS['whichNumPo'];

	$sqlr = "
	CALL attachChatroomPOToUserName('".$whichUserName."',  '".$whichNumPo."');
	";


	print "<pre>".$sqlr."</pre>".PHP_EOL;
	if ($GLOBALS['conn']->query($sqlr) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sqlr . "<br>" . $GLOBALS['conn']->error;
	}


	
}

?> 

