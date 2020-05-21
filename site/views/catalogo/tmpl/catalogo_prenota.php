<?php
// no direct access

defined('_JEXEC') or die('Restricted access');

echo '<div class="main">'; ?>
<?php echo  JText::_('COM_GGLMS_CATALOGO_TITOLO') ?>
<?php
foreach ($this->catalogo as $item) { ?>


    <div class="item-container">

        <div>
            <h6> <?php echo $item->titolo ?> </h6>
            <a style="font-weight: bold" href="<?php echo JURI::root() . 'index.php?option=com_gglms&view=prenota&id_corso=' . $item->id_corso . '&id_piattaforma=' . $this->id_piattaforma ?>">
                <?php echo  JText::_('COM_GGLMS_CATALOGO_INFO') ?>
            </a>
        </div>


    </div>
    <hr>

<?php }

echo '</div>'
?>


