<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

if($this->contenuto->_params->get('abilita_breadcrumbs',1))
    echo $this->loadTemplate('breadcrumb');

?>


<div class="g-grid">
    <div class="g-block size-50 center">

        <?php
        if($this->contenuto->descrizione == '' && $this->contenuto->abstract == '')
            echo $this->contenuto->_params->get('testo_invito_scaricare_pdfsingolo');
        else
        {
            echo $this->contenuto->descrizione;
            echo "<br>";
            echo $this->contenuto->abstract;
        }
        ?>

    </div>
    <div class="g-block size-50 center">
        <a target="_blank" href="<?php echo PATH_CONTENUTI.'/'.$this->contenuto->id. '/'.$this->contenuto->id.'.pdf'; ?>">
            <img src="components/com_gglms/libraries/images/icona_pdf.png">
        </a>
    </div>


</div>