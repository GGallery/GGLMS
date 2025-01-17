<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$id_contenuto=$this->getContenuto();

echo "<h1>Vista standard contenuto</h1>";
?>
<script type="text/javascript">

    jQuery(document).ready(function ($) {

        const video = document.querySelector("video");
        const allowedPlaybackRate = 1.0;

        video.addEventListener('ratechange', function() {
            if (video.playbackRate !== allowedPlaybackRate) {
                console.warn(`Playback rate changed to ${video.playbackRate}. Resetting to ${allowedPlaybackRate}.`);
                video.playbackRate = allowedPlaybackRate;
            }
        });

        setInterval(() => {
            if (video.playbackRate !== allowedPlaybackRate) {
                video.playbackRate = allowedPlaybackRate;
            }
        }, 100);

        <?php if ($this->disabilita_mouse == 1) { ?>

        document.addEventListener("contextmenu", function (e) {
            alert('Funzione disabilitata');
            e.preventDefault();
        }, true);

        <?php } ?>


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


