<?php
// no direct access

defined('_JEXEC') or die('Restricted access');
$data_decode = json_decode(base64_decode($this->data));
 
?>

<h3 class="alert alert-error"><?php echo  JText::_('COM_GGLMS_CF_INVALID_1') ?> (<b><?php echo strtoupper($data_decode->codicefiscale); ?></b>) <?php echo  JText::_('COM_GGLMS_CF_INVALID_2') ?></h3>
<p><?php echo  JText::_('COM_GGLMS_CF_INVALID_3') ?></p>

<div class="g-grid">
    <div class="g-block size-20">


        <form action="index.php" method="post">

            <input type="text" name="cfcorretto" id="cf" value="" size="16">
            <input type="hidden" name="data" value="<?php echo $this->data; ?>">
            <input type="hidden" name="option" value="com_gglms">
            <input type="hidden" name="task" value="gglms.updatecf">

            <button type="submit" ><?php echo  JText::_('COM_GGLMS_GLOBAL_CONFERMA') ?></button>

        </form>
    </div>

</div>
