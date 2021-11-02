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

        <?php if (JFactory::getApplication()->getParams()->get('log_utente') == 1) echo 'UserLog(' . $this->id_utente . ',' . $this->contenuto->id . ', null);' ?>


        jQuery('#video').bind('contextmenu', function () {
            console.log('prevent download');
            return false;
        });

        jQuery('#file-download').click(function() {
            var pHref = jQuery(this).attr("data-href");
            //window.location = pHref;
            window.open(pHref, '_blank');
        });

        var hasPlayed = false;
        var player;
        var old_tempo;
        var bookmark =<?php echo $stato->bookmark; ?>;

        var id_elemento = <?php echo $this->contenuto->id; ?>;
        var stato = <?php echo $stato->completato; ?>;
        var features = null;

        if (stato) {
            features = ['playpause', 'current', 'progress', 'duration', 'volume', 'fullscreen', 'tracks'];
        } else {
            features = ['playpause', 'current', 'duration', 'volume', 'fullscreen', 'tracks'];
        }

        // abilito tutte le features a prescindere dallo stato perchè controllerò il seeking
        //features = ['playpause', 'current', 'progress', 'duration', 'volume', 'fullscreen', 'tracks'];

        var jumper_attuale = null;
        var jumper = [];

        <?php
        $i = 0;
        foreach ($this->jumper as $val) {
        ?>
        jumper[<?php echo $i++; ?>] = {
            'tstart': <?php echo $val['tstart']; ?>,
            'titolo': "<?php echo $val['titolo']; ?>"
        };
        <?php
        }
        ?>

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
                    sliding(tview);

                }, false);

                mediaElement.addEventListener('loadedmetadata', function (e) {
                    console.log("setcurrentetime" + bookmark);
                    mediaElement.setCurrentTime(bookmark);
                });

                if (!stato) {
                    mediaElement.addEventListener('ended', function (e) {
                        stato = 1;
                        /*
                        jQuery.get("index.php?option=com_gglms&task=contenuto.updateTrack", {
                            secondi: mediaElement.duration.toFixed(0),
                            stato: 1,
                            id_elemento: id_elemento
                        });
                        */

                        var data_sync = null;
                        var pAsync = get_async_call();
                        data_sync = {async: pAsync};
                        var id_utente = '<?php echo $this->id_utente;?>';


                        jQuery.ajax({
                            url: "index.php?option=com_gglms&task=contenuto.updateTrack",
                            data: {
                                "secondi": mediaElement.duration.toFixed(0),
                                "stato": 1,
                                "id_elemento": id_elemento,
                                "id_utente" : id_utente
                            },
                            async: data_sync.async,
                            success: function () {
                                console.log("ended success");
                            }
                        });
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
        jQuery('.jumper.enabled').click(function () {
            var rel = jQuery(this).attr('rel');
            player.setCurrentTime(rel);
            //sliding(time);
            sliding(rel);
        });

        jQuery('.jumper.disabled').click(function () {
            alert("E' necessario guardare tutto il video prima di poter cliccare sui jumper");
        });

        //Aggiorno il bookmark quando chiudo la pagina
        jQuery(window).on('beforeunload', function () {
            console.log("bookmark->" + tview);

            updateBookmark(id_utente, tview, id_elemento);
            <?php
            // aggiornamento della temporizzazione dei contenuti - solo un update in onunload con scrittura della sessione
            echo <<<HTML
            getUpdateSessionStorage("{$this->id_utente}", "{$this->contenuto->id}");
HTML;
?>
            return null;
        });

        function sliding(tempo) {
            if (old_tempo != tempo && typeof (jumper.length) != 'undefined') {

                old_tempo = tempo;
                var currTime = parseInt(tempo);
                var i = 0;
                var past_jumper_selector = [];
                while (i < jumper.length && currTime >= parseInt(jumper[i]['tstart'])) {
                    past_jumper_selector[i] = '#' + i;
                    i++;
                }
                i--; // col ciclo while vado avanti di 1
                if (i < jumper.length && i != jumper_attuale) { // se cambio jumper

                    console.log("cambio slide -> AJAX per set position");

                    jumper_attuale = i;
                    // cancello eventuali jumper azzurri
                    jQuery('.jumper').css('background-color', '#fff');

                    // jumper attuale è azzurro
                    jQuery('#' + i).css('background-color', '#E4E4E4');
                }
            }
        }


//INIZIO COMPARSA JUMPER
        jQuery("#jumper").click(function () {

            if (jQuery("#panel_jumper").hasClass('show')) {

                $("#sidepanel").removeClass('show').addClass('sidepanelhide');
                $(".sidepanel").removeClass('show').addClass('hide');
                $(".container-video").removeClass("container-videosidepanelshow").addClass("container-videosidepanelhide");
                $(".pulsante").removeClass("container-videosidepanelshow").addClass("container-videosidepanelhide");
                $("#moduli").removeClass("container-videosidepanelshow").addClass("container-videosidepanelhide");
            } else {
                $(".sidepanel").removeClass('show').addClass('hide');
                $("#sidepanel").removeClass('sidepanelhide').addClass('show');
                $("#panel_jumper").removeClass("hide").addClass('show');
                $(".container-video").removeClass("container-videosidepanelhide").addClass("container-videosidepanelshow");
                $(".pulsante").removeClass("container-videosidepanelhide").addClass("container-videosidepanelshow");
                $("#moduli").removeClass("container-videosidepanelhide").addClass("container-videosidepanelshow");
            }
        });
// FINE COMPARSA JUMPER

    });

</script>


<div class="container-videosidepanelhide g-grid span12">

    <div id="boxvideo" class="g-block size-50 span6">


        <video id="video" controlsList="nodownload" style="width:100%; height:100%; " height="100%" controls="controls"
               preload="auto" class="img-thumbnail">
            <source type="video/mp4"
                    src="<?php echo PATH_CONTENUTI . '/' . $this->contenuto->id . '/' . $this->contenuto->id . '.mp4'; ?>"/>
            <?php /*
            <source type="video/webm"
                    src="<?php echo PATH_CONTENUTI . '/' . $this->contenuto->id . '/' . $this->contenuto->id . '.webm'; ?>"/>
            <source type="video/ogg"
                    src="<?php echo PATH_CONTENUTI . '/' . $this->contenuto->id . '/' . $this->contenuto->id . '.ogv'; ?>"/>
            */?>
            <track
                    kind="slides"
                    src="<?php echo PATH_CONTENUTI . '/' . $this->contenuto->id . '/'; ?>vtt_slide.vtt"
                    label="slides"
                    srclang="" />

        </video>


        <!--    mobile-->
        <div id="panel_jumper" class="sidepanel layout-sm ">
            <?php
                echo outputHelper::buildPanelJumperBox($this->jumper, $this->contenuto);
            ?>
        </div>


    </div>

    <div id="boxslide" class="g-block size-50 span6 ">
        <div class="mejs-slides-player-slides img-thumbnail"></div>

        <!-- ICONE - PULSANTI-->
        <?php if (!is_null($this->slide_pdf)) { ?>
            <div class="g-block size-100 span12 text-right slide-download">
                <button id="file-download" name="file-download" title="<?php echo JText::_('COM_GGLMS_ELEMENTO_STR10'); ?>" class="tooltip-button" data-href="<?php echo $this->slide_pdf; ?>"></button>
            </div>
        <?php } ?>
    </div>

    <!--desktop-->
    <div id="panel_jumper" class="sidepanel layout-desktop">
        <?php
            echo outputHelper::buildPanelJumperBox($this->jumper, $this->contenuto);
        ?>
    </div>



</div>





<!--
<div class="g-grid">
<div class="g-block size-50">


   <div id="jumper" class="pulsante"><img  width="30px" src="components/com_gglms/libraries/images/tab_navigazione.png"/></div>
    </div>


</div>
-->

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



