<?php
/**
 * Created by PhpStorm.
 * User: Tony
 * Date: 18/01/2018
 * Time: 10:51
 */

$id_elemento = $_GET['SCOInstanceID'] ;
$id_utente= $_GET['id_utente'];
$log_status = $_GET['log_status'];


?>
<script type="application/javascript" src="js/jquery-3.2.1.min.js"></script>
<script type="application/javascript" src="js/userlog.js"></script>
<script type="text/javascript">

<?php if($log_status) echo 'UserLog('.$id_utente.','.$id_elemento.', null);' ?>

</script>
