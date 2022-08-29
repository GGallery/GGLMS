<?php

$id_elemento = $_GET['SCOInstanceID'];
$scorm_path = $_GET['pathscorm'];
$id_utente = $_GET['id_utente'];
$log_status = $_GET['log_status'];

?>
<html>
<head>
    <title>WEBINAR</title>

    <style type="text/css">
        .alert {
            text-align: right;
            color: red;
        }
    </style>

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
    <meta http-equiv="Pragma" content="no-cache"/>
    <meta http-equiv="Expires" content="0"/>

    <script type="application/javascript" src="js/jquery-3.2.1.min.js"></script>
    <script type="application/javascript" src="js/userlog.js?<?php echo date('Ymd');?>"></script>
    <script type="application/javascript">


        <?php if ($log_status == 1) {
            echo 'UserLog(' . $id_utente . ',' . $id_elemento . ', null)';
        }
        ?>

        var unloaded = false;

        function unloadHandler() {
            console.log(API);
            if (!unloaded && API_isActive) {
                EndLog(uniqid);
                unloaded = true;
                window.close();
            }
        }

        // window.onbeforeunload = unloadHandler;
        window.onbeforeunload = function(event) {


            <?php
            // aggiornamento della temporizzazione dei contenuti - solo un update in onunload con scrittura della sessione
            echo 'getUpdateSessionStorage(' . $id_utente . ',' . $id_elemento . ', null)';
            ?>

            unloadHandler();

        };
        window.onunload = unloadHandler;

    </script>

</head>
<p class="alert">NON CHIUDERE QUESTA FINESTRA FINO ALLA FINE DEL WEBINAR</p>
<iframe allowusermedia allow="microphone *; camera *; autoplay;" style="width: 100%; height: 100%;"
        src="<?php echo base64_decode($scorm_path); ?>"></iframe>

</html>






