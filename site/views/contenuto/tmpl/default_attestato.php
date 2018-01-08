<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

if($this->contenuto->_params->get('abilita_breadcrumbs',1))
    echo $this->loadTemplate('breadcrumb');

?>


<div class="g-grid">
    <div class="g-block size-50">
        <?php echo $this->contenuto->_params->get('testo_invito_scaricare_attestato');?>
    </div>
    <div class="g-block size-50">
        <a href="index.php?option=com_gglms&task=pdf.generateAttestato&content=<?php echo $this->contenuto->id; ?>"><img src="components/com_gglms/libraries/images/icona_pdf.png"></a>
    </div>

</div>