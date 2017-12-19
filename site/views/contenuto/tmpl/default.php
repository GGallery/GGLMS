<?php
// no direct access


defined('_JEXEC') or die('Restricted access');


$id_contenuto=$this->getContenuto();
$user = JFactory::getUser();
$id_utente = $user->get('id');


echo "<h1>Vista standard contenuto</h1>";
?>
<script type="text/javascript">
    <?php if(JFactory::getApplication()->getParams()->get('log_utente')==1) echo 'UserLog('.$id_utente.','.$this->contenuto->id.', null);' ?>


</script>
