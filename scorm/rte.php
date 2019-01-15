<?php

$id_elemento =$_GET['SCOInstanceID'] ;
$scorm_path =$_GET['pathscorm'];
$id_utente=$_GET['id_utente'];
$log_status=$_GET['log_status'];

?>
<html>
<head>
    <title>TEST</title>

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
    <meta http-equiv="Pragma" content="no-cache"/>
    <meta http-equiv="Expires" content="0"/>

    <style type="text/css">
        #frameapi{
            display:  none;
        }

        .closebar{
            width: 100%;
            background-color: #0095AD;
            text-align: right;
            font-size: 15px;
            padding: 2px;
        }

        .closebar a{
            color: white;
            text-decoration: none;
        }
    </style>
    <script type="application/javascript" src="js/jquery-3.2.1.min.js"></script>
    <script type="application/javascript" src="js/userlog.js"></script>
    <script type="application/javascript">


        <?php if($log_status==1) { ?>
        var uniqid=Math.floor(Math.random()*100000);
        StartLog(<?php echo $id_utente; ?> , <?php echo $id_elemento; ?>, null,uniqid);

        <?php } ?>



        var API_isActive = false;
        function APILoaded() {
            API_isActive = true;
            console.log("API_LOADED!");
            document.getElementById('framecourse').src="<?php echo $scorm_path;?>"
        };



        var unloaded = false;
        function unloadHandler(){

            console.log(API);

            if(!unloaded && API_isActive){
                API.LMSSetValue("cmi.exit", "suspend"); //Set exit to whatever is needed
                API.LMSCommit(""); //save all data that has already been sent
                API.LMSFinish(""); //close the SCORM API connection properly
                EndLog(uniqid);
                unloaded = true;
                window.close();
            }
        }
        window.onbeforeunload = unloadHandler;
        window.onunload = unloadHandler;

    </script>

</head>
<div class="closebar"><a href="#"  onclick="unloadHandler()">CHIUDI <img width="20px" src="chiudi.png"></a></div>
<iframe src="" name="course" id="framecourse" noresize="" width="100%" height="100%" style="framewrap: 0 !important;"></iframe>
<iframe src="api.php?SCOInstanceID=<?php echo $id_elemento; ?>&UserID=<?php echo $id_utente;?>" name="API" id="frameapi" noresize  width="0" height="0" onload="APILoaded(this)"></iframe>

</html>






