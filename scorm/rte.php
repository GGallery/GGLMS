<?php
require_once "scormparser.php";

$id_elemento =$_GET['SCOInstanceID'] ;
$scorm_path =$_GET['pathscorm'];
$id_utente=$_GET['id_utente'];
$log_status=$_GET['log_status'];

$index = parse($scorm_path);
//echo $index;
?>
<html>
<head>
    <title>TEST</title>

    <style type="text/css">
        #frameapi{
            display:  none;
        }
        #left{
            position: relative;
            float: left;
            width: auto;
            background-color: #a7b0b4;
        }

        #main{
            position: relative;
            float: left;

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
            console.log("API_LOADED: ");
        }

        function loadResource(res) {
            event.preventDefault();
            console.log("resource: " + res);
            document.getElementById('framecourse').src = res;

            return false;
        }
    </script>

</head>

<div id="left">
    <?php echo $index; ?>
</div>
<div id="main">
    <iframe src="" name="course" id="framecourse" noresize="" width="100%" height="100%" style="framewrap: 0 !important;"></iframe>
    <iframe src="api.php?SCOInstanceID=<?php echo $id_elemento; ?>&UserID=<?php echo $id_utente;?>" name="API" id="frameapi" noresize  width="0" height="0" onload="APILoaded(this)"></iframe>
</div>
</html>






