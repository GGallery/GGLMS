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
        <?php echo $this->contenuto->att_scaricabile ? $this->contenuto->_params->get('testo_invito_scaricare_attestato') : $this->contenuto->_params->get('testo_attestato_disabilitato'); ?>

    </div>
    <div class="g-block size-50">
        <a class=" <?php echo !$this->contenuto->att_scaricabile ? 'attestato disabled' : 'attestato' ?> href="<?php echo $this->contenuto->att_scaricabile ? "index.php?option=com_gglms&task=pdf.generateAttestato&content=" . $this->contenuto->id : ''; ?>
        ">
        <img class="<?php echo !$this->contenuto->att_scaricabile ? ' attestato disabled' : 'attestato' ?>"
             src="components/com_gglms/libraries/images/icona_pdf.png">
        </a>
    </div>

</div>
