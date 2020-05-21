<?php
// no direct access

defined('_JEXEC') or die('Restricted access');
$data_decode = json_decode(base64_decode($this->data));

?>

<h3 class="alert alert-error"><?php echo  JText::_('COM_GGLMS_CF_INVALID_1_1') ?>
    <?php echo $data_decode->nome . ' ' . $data_decode->cognome ?><?php echo  JText::_('COM_GGLMS_CF_INVALID_2') ?> !
    <?php echo  JText::_('COM_GGLMS_CF_INVALID_3_1') ?>   <a href=mailto:<?php echo strtoupper($data_decode->email); ?> > <?php echo  JText::_('COM_GGLMS_CF_INVALID_3_2') ?>  </a>
</h3>

<!--<div class="g-grid">-->
<!---->
<!--</div>-->
