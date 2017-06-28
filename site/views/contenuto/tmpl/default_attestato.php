<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

if($this->contenuto->_params->get('abilita_breadcrumbs',1))
    echo $this->loadTemplate('breadcrumb');

?>

 
<div class="g-grid">
    <div class="g-block size-50">
        <h2> Congratulazioni! </h2>
        Ora puoi scaricare l'attestato del corso cliccando sull'icona qui a fianco.<br>
    </div>
    <div class="g-block size-50">
        <a href="index.php?option=com_gglms&task=pdf.generateAttestato&content=<?php echo $this->contenuto->id; ?>"><img src="components/com_gglms/libraries/images/icona_pdf.png"></a>
    </div>

</div>