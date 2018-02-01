<?php

$id_elemento = $_GET['SCOInstanceID'] ;
$scorm_path = $_GET['pathscorm'];
$id_utente= $_GET['id_utente'];
$cache= $_GET['cache'];

?>

<style type="text/css">
    #frameapi{
        display:  none;
    }

</style>

<script type="application/javascript">

    function onMyFrameLoad() {
        console.log("API_LOADED");
        document.getElementById('framecourse').src="<?php echo $scorm_path;?>"
    };
</script>

<html>
<head>
    <title>TEST</title>
</head>


<iframe src="" name="course" id="framecourse" noresize="" width="100%" height="100%" style="framewrap: 0 !important;"></iframe>
<iframe src="api.php?SCOInstanceID=<?php echo $id_elemento; ?>&UserID=<?php echo $id_utente;?>" name="API" id="frameapi" noresize  width="200" height="200" onload="onMyFrameLoad(this)"></iframe>


</html>






