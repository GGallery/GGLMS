<?php

$id_elemento = $_GET['SCOInstanceID'] ;
$scorm_path = $_GET['pathscorm'];
$id_utente= $_GET['id_utente'];
$log_status = $_GET['log_status'];

?>

<style type="text/css">
    #frameapi{
        display:  block;
    }
</style>

<html>

<head>
    <title>TEST</title>
</head>

<iframe src="<?php echo $scorm_path;?>" name="course" noresize="" width="100%" height="100%" style="framewrap: 0 !important;"></iframe>
<iframe src="api.php?SCOInstanceID=<?php echo $id_elemento; ?>&UserID=<?php echo $id_utente; ?>" name="API" id="frameapi" noresize  width="100" height="100"></iframe>
<iframe src="userlog.php?SCOInstanceID=<?php echo $id_elemento; ?>&id_utente=<?php echo $id_utente; ?>&log_status=<?php echo $log_status; ?>"></iframe>

</html>






