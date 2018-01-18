<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$id_contenuto=$this->getContenuto();

echo "<h1>Vista standard contenuto</h1>";
?>
<script type="text/javascript">
    <?php if(JFactory::getApplication()->getParams()->get('log_utente')==1) echo 'UserLog('.$this->id_utente.','.$this->contenuto->id.', null);' ?>


</script>
