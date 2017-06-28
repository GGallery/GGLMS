<?php

$dbhost = '127.0.0.1';
$dbuser = 'academydb';
$dbpass = 'tevalab';
$dbname = 'farmacademy_db';
$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die('Error connecting to mysql');
mysql_select_db($dbname);

?>


