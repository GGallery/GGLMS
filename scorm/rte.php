<?php

$id_elemento = $_GET['SCOInstanceID'] ;
$scorm_path = $_GET['pathscorm'];
$id_utente= $_GET['id_utente'];

?>

<style type="text/css">
	#frameapi{
		display:  none;
	}

</style>

<html>
<head>
	<title>TEST</title>
</head>

<iframe src="<?php echo $scorm_path;?>" name="course" noresize="" width="100%" height="100%" style="framewrap: 0 !important;"></iframe>
<iframe src="api.php?SCOInstanceID=<?php echo $id_elemento; ?>&UserID=<?php echo $id_utente; ?>" name="API" id="frameapi" noresize  width="0" height="0"></iframe>


</html>






