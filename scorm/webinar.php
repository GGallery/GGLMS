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

<iframe allowusermedia allow = "microphone *; camera *; autoplay;"  style = "width: 100%; height: 100%;" src = "https://go.skymeeting.net/live/webinar@ggallery.it/embed1"></iframe>

</html>






