<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$id_contenuto=$this->getContenuto();

echo "<h1>Vista standard contenuto</h1>";
?>
<script type="text/javascript">

    jQuery(document).ready(function ($) {

        <?php if(JFactory::getApplication()->getParams()->get('log_utente')==1) echo 'UserLog('.$this->id_utente.','.$this->contenuto->id.', null);' ?>

        //Aggiorno il bookmark quando chiudo la pagina
        jQuery(window).on('beforeunload', function () {

            <?php
            // aggiornamento della temporizzazione dei contenuti - solo un update in onunload con scrittura della sessione
            echo <<<HTML
            getUpdateSessionStorage("{$this->id_utente}", "{$this->contenuto->id}");
HTML;
?>

            return null;
        });


    });


</script>
