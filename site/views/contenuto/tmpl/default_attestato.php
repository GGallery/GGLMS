<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

if ($this->contenuto->_params->get('abilita_breadcrumbs', 1))
    echo $this->loadTemplate('breadcrumb');

?>

<style>
    a.disabled.attestato  {

        cursor: default;
        pointer-events: none;
    }

    img.disabled.attestato {
        opacity: 0.2;

    }
</style>

<div class="g-grid">
    <div class="g-block size-50">

        <?php echo $this->att_scaricabile == 1 ?  JText::_('COM_GGLMS_ATTESTATO_INVITO') : JText::_('COM_GGLMS_ATTESTATO_DISABILITATO')  ?>

    </div>
    <div class="g-block size-50">
        <a class=" <?php echo !$this->att_scaricabile ? 'attestato disabled' : 'attestato' ?> " href="<?php echo $this->att_scaricabile ? "index.php?option=com_gglms&task=pdf.generateAttestato&content="
            . trim($this->contenuto->id) . (!is_null($this->id_unita) ? '&id_corso=' . $this->id_unita : '') : ''; ?>
        ">
        <img class="<?php echo !$this->att_scaricabile ? ' attestato disabled' : 'attestato' ?>"
             src="components/com_gglms/libraries/images/icona_pdf.png">
        </a>
    </div>

</div>
