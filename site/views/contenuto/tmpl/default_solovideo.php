<?php
defined('_JEXEC') or die('Restricted access');

if ($this->contenuto->_params->get('abilita_breadcrumbs', 1))
    echo $this->loadTemplate('breadcrumb');

$files = $this->contenuto->getFiles();
$stato = $this->contenuto->getStato();

echo "<h1>" . $this->contenuto->titolo . "</h1>";

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

        <?php if (JFactory::getApplication()->getParams()->get('log_utente') == 1) echo 'UserLog(' . $this->id_utente . ',' . $this->contenuto->id . ', null);'?>

        var hasPlayed = false;
        var player;
        var duration = 0;
        var tview = 0;
        var old_tempo;
        var bookmark =<?php echo $stato->bookmark; ?>;


        var stato = <?php echo $this->contenuto->getStato()->completato; ?>;
        var features = null;

        if (stato) {
            features = ['playpause', 'current', 'progress', 'duration', 'volume', 'fullscreen', 'tracks'];
        } else {
            features = ['playpause', 'current', 'duration', 'volume', 'fullscreen', 'tracks'];
        }

        // abilito tutte le features a prescindere dallo stato perchè controllerò il seeking
        //features = ['playpause', 'current', 'progress', 'duration', 'volume', 'fullscreen', 'tracks'];

        // var jumper_attuale = null;
        // var jumper = [];

        <?php
        /* $i = 0;
        foreach ($this->jumper as $val) {
        ?>
        jumper[<?php echo $i++; ?>] = {
             'tstart': <?php echo $val['tstart']; ?>,
             'titolo': "<?php echo $val['titolo']; ?>"
         };
        <?php
        }
        */
        ?>

        //stoppo il video quando cambia focus
        <?php if ($this->attiva_blocco_video_focus == 1) { ?>

        jQuery(window).on('blur',function() {
            console.log('blur');
            player.pause();

        });
        <?php } ?>

        // declare object for video
        player = new MediaElementPlayer('video', {
            features: features,
            slidesSelector: '.mejs-slides-player-slides',
            autoplay: true,
            enableKeyboard: false,
            success: function (mediaElement, domObject) {
                old_tempo = null;

                mediaElement.addEventListener('timeupdate', function (e) {
                    tview = mediaElement.currentTime.toFixed(0);
                    if (!stato) {
                        if (duration && duration - tview < 20)
                            finish(tview);
                    }
                    // sliding(time);
                }, false);

                mediaElement.addEventListener('loadedmetadata', function (e) {
                    console.log("setcurrentetime" + bookmark);
                    mediaElement.setCurrentTime(bookmark);
                    duration = mediaElement.duration;
                });

                if (!stato) {
                    mediaElement.addEventListener('ended', function (e) {
                        finish(mediaElement.duration.toFixed(0));
                    }, false);

                    /*

                    // così facendo il forward seek è disabilitato, posso andare soltanto indietro nel video ma mai avanti
                    mediaElement.addEventListener("seeking", function(event) {
                        if (tview < mediaElement.getCurrentTime()) {
                            console.log("controllo seeking...");
                            mediaElement.setCurrentTime(tview);
                        }
                    });

                    mediaElement.addEventListener("seeked", function(event) {
                        if (tview < mediaElement.getCurrentTime()) {
                            console.log("controllo seeking...");
                            mediaElement.setCurrentTime(tview);
                        }
                    });

                    */

                }

            },
            error: function () {
                console.log('Errore');
            }
        });


        player.play();
        // jQuery('.jumper.enabled').on('click',function () {
        //     var rel = jQuery(this).attr('rel');
        //     player.setCurrentTime(rel);
        //     sliding(time);
        // });

        // jQuery('.jumper.disabled').on('click',function () {
        //     alert("E' necessario guardare tutto il video prima di poter cliccare sui jumper");
        // });

        //Aggiorno il bookmark quando chiudo la pagina
        jQuery(window).on('beforeunload', function () {
            console.log("bookmark->" + tview);
            var id_utente = '<?php echo $this->id_utente;?>';
            var id_elemento = '<?php echo $this->contenuto->id; ?>';

            updateBookmark(tview, id_elemento, id_utente);
            <?php
            // aggiornamento della temporizzazione dei contenuti - solo un update in onunload con scrittura della sessione
            echo <<<HTML
            getUpdateSessionStorage("{$this->id_utente}", "{$this->contenuto->id}");
HTML;
?>

            return null;
        });

        // prevent context menu (così non possono salvarsi il video)
        jQuery('#video').bind('contextmenu', function () {
            console.log('prevent download solo_video');
            return false;
        });

        // function sliding(tempo) {
        //     if (old_tempo != tempo && typeof (jumper.length) != 'undefined') {

        //         old_tempo = tempo;
        //         var currTime = parseInt(tempo);
        //         var i = 0;
        //         var past_jumper_selector = [];
        //         while (i < jumper.length && currTime >= parseInt(jumper[i]['tstart'])) {
        //             past_jumper_selector[i] = '#' + i;
        //             i++;
        //         }
        //         i--; // col ciclo while vado avanti di 1
        //         if (i < jumper.length && i != jumper_attuale) { // se cambio jumper

        //             console.log("cambio slide -> AJAX per set position");

        //             jumper_attuale = i;
        //             // cancello eventuali jumper azzurri
        //             jQuery('.jumper').css('background-color', '#fff');

        //             // jumper attuale è azzurro
        //             jQuery('#' + i).css('background-color', '#E4E4E4');
        //         }
        //     }
        // }


        function finish(tempo) {
            stato = 1;

            /*
            jQuery.get("index.php?option=com_gglms&task=contenuto.updateTrack", {
                secondi: tempo,
                stato: 1,
                id_elemento: id_elemento
            });
            */

            var data_sync = null;
            var pAsync = get_async_call();
            data_sync = {async: pAsync};
            var id_utente = '<?php echo $this->id_utente;?>';
            var id_elemento = '<?php echo $this->contenuto->id; ?>';

            // passando uniquid forzo l'esecuzione di updateUserLog
            var globalUniq = (typeof gUniqid != 'undefined' ?  gUniqid : "");

            jQuery.ajax({
                url: "index.php?option=com_gglms&task=contenuto.updateTrack",
                data: {
                    "secondi": tempo,
                    "stato": 1,
                    "id_elemento": id_elemento,
                    "id_utente" : id_utente,
                    "uniquid" : globalUniq
                },
                async: data_sync.async,
                success: function () {
                    console.log("finish success");
                }
            });

        }



//INIZIO COMPARSA JUMPER
        // jQuery("#jumper").click(function () {

        //     if (jQuery("#panel_jumper").hasClass('show')) {

        //         $("#sidepanel").removeClass('show').addClass('sidepanelhide');
        //         $(".sidepanel").removeClass('show').addClass('hide');
        //         $(".container-video").removeClass("container-videosidepanelshow").addClass("container-videosidepanelhide");
        //         $(".pulsante").removeClass("container-videosidepanelshow").addClass("container-videosidepanelhide");
        //         $("#moduli").removeClass("container-videosidepanelshow").addClass("container-videosidepanelhide");
        //     } else {
        //         $(".sidepanel").removeClass('show').addClass('hide');
        //         $("#sidepanel").removeClass('sidepanelhide').addClass('show');
        //         $("#panel_jumper").removeClass("hide").addClass('show');
        //         $(".container-video").removeClass("container-videosidepanelhide").addClass("container-videosidepanelshow");
        //         $(".pulsante").removeClass("container-videosidepanelhide").addClass("container-videosidepanelshow");
        //         $("#moduli").removeClass("container-videosidepanelhide").addClass("container-videosidepanelshow");
        //     }
        // });
// FINE COMPARSA JUMPER


    });

</script>


<div class="container-videosidepanelhide row">
    <div class="span3"></div>

    <div id="boxvideo" class="span6 center-block" style="min-height: 500px; text-align: center;">

        <video id="video" style="width:100%; height:100%; display: inline-block" height="100%" controls
               controlsList="nodownload"
               preload="auto" class="img-thumbnail">
            <source type="video/mp4"
                    src="<?php echo PATH_CONTENUTI . '/' . $this->contenuto->id . '/' . $this->contenuto->id . '.mp4'; ?>"/>

            <?php
                // se presenti nella directory del contenuto vengono caricati i file dei sottotitoli che devono essere nel formato srt
                // e con questi nominativi subtitle_english_en.srt, subtitle_french_fr.srt, subtitle_german_de.srt, ecc
                echo OutputHelper::check_subtitles_solovideo($_SERVER['DOCUMENT_ROOT'] . '/mediagg/contenuti/' . $this->contenuto->id, PATH_CONTENUTI . '/' . $this->contenuto->id);
            ?>
        </video>

    </div>

</div>


<div class="g-grid">
    <div class="g-block size-50">

        <!--        <div id="jumper" class="pulsante"><img  width="30px" src="components/com_gglms/libraries/images/tab_navigazione.png"/></div>-->
    </div>


</div>

<?php if (!empty($files)): ?>
    <div id="files" class="g-grid ">
        <hr>
        <ul>
            <?php
            foreach ($files as $file) {
                echo "<li>";
                echo '<a target="_blank" href="/mediagg/files/' . $file->id . '/' . $file->filename . '">' . $file->name . '</a>';
                echo "</li>";
            }
            ?>
        </ul>
        <hr>
    </div>
<?php endif; ?>


