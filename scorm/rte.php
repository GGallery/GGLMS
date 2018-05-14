<?php

$id_elemento =$_GET['SCOInstanceID'] ;
$scorm_path =$_GET['pathscorm'];
$id_utente=$_GET['id_utente'];
$log_status=$_GET['log_status'];

?>
<html>
<head>
    <title>TEST</title>

    <style type="text/css">
        #frameapi{
            display:  none;
        }
    </style>
    <script type="application/javascript" src="js/jquery-3.2.1.min.js"></script>
    <script type="application/javascript" src="js/userlog.js"></script>
    <script type="application/javascript">

        <?php if($log_status==1) {
            echo 'UserLog(' . $id_utente . ',' . $id_elemento . ', null)';
        }
        ?>

        function APILoaded() {
            console.log("API_LOADED!");
            document.getElementById('framecourse').src="<?php echo $scorm_path;?>"
        };
    </script>

</head>

<iframe src="" name="course" id="framecourse" noresize="" width="100%" height="100%" style="framewrap: 0 !important;"></iframe>
<iframe src="api.php?SCOInstanceID=<?php echo $id_elemento; ?>&UserID=<?php echo $id_utente;?>" name="API" id="frameapi" noresize  width="0" height="0" onload="APILoaded(this)"></iframe>

</html>






