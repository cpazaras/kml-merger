<?
if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false){
	//local
	$dbName		= 'supermarket';
	$dbPass		= 'My$q7';
	$dbServer	= 'localhost';
	$dbUser		= 'root';
} else {
	//live
	$dbName		= 'supermarket';
	$dbPass		= '24xpePvncK2uGxjU';
	$dbServer	= 'localhost';
	$dbUser		= 'xaris_supermarket';
}//end if

//$dbconn = mysqli_connect($dbServer, $dbUser, $dbPass, $dbName);

global $dbconn;

$dbconn = new mysqli($dbServer, $dbUser, $dbPass, $dbName);

if($dbconn->connect_errno){
	//db conn failed
}//end if

$dbconn->query('SET CHARACTER SET UTF8') or die($dbconn->error);
$dbconn->query('SET NAMES utf8') or die($dbconn->error);


?>
