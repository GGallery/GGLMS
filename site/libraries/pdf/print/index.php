<?php
$path = $_SERVER['DOCUMENT_ROOT'];
$path .= "/db_connection.php";
include_once($path);

$id_elemento=urldecode($_REQUEST['id_elemento']);
$id_utente=urldecode($_REQUEST['id_utente']);

$query ="
replace INTO jos_tt_track( id_elemento, id_utente, stato, data) 
VALUES ( ".$id_elemento.", ".$id_utente.", 1, CURRENT_TIMESTAMP())
";


/*
$query="Replace INTO
jos_lms_n_scorm_scoes_track (jos_lms_n_scorm_scoes_track.userid, jos_lms_n_scorm_scoes_track.scoid, jos_lms_n_scorm_scoes_track.attempt, jos_lms_n_scorm_scoes_track.element, jos_lms_n_scorm_scoes_track.value, jos_lms_n_scorm_scoes_track.timemodified)
values( '".$id_utente."', '".$id_quiz."',  '1', 'cmi.core.lesson_status', 'completed',  '".time()."')";
*/

$db_query = mysql_query($query) or die (mysql_error());

$url=substr("http://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'], 0,-9 )."main.html" ;



header('Location: '.$url);

?>