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


        <?php if ($this->disabilita_mouse == 1) { ?>

        document.addEventListener("contextmenu", function (e) {
            alert('Funzione disabilitata');
            e.preventDefault();
        }, true);

        <?php } ?>



        <?php if (JFactory::getApplication()->getParams()->get('log_utente') == 1) echo 'UserLog(' . $this->id_utente . ',' . $this->contenuto->id . ', null);'?>

        //let player;
        let duration = 0;
        let tview = 0;
        let old_tempo;
        let bookmark = <?php echo $stato->bookmark; ?>;


        let stato = <?php echo $this->contenuto->getStato()->completato; ?>;
        let features = null;
        let pSeeking = false;

        // if (stato) {
        //     features = ['playpause', 'current', 'progress', 'duration', 'volume', 'fullscreen', 'tracks'];
        // } else {
        //     features = ['playpause', 'current', 'duration', 'volume', 'fullscreen', 'tracks'];
        // }

        if (stato)
            pSeeking = true;

        //stoppo il video quando cambia focus
        <?php if ($this->attiva_blocco_video_focus == 1) { ?>

        jQuery(window).on('blur',function() {
            console.log('blur');
            player.pause();
        });
        <?php } ?>

        // declare object for video
        const player = amp('video', { /* Options */
            "nativeControlsForTouch": false,
            autoplay: true,
            controls: true,
            width: "640",
            height: "400",
            // seeking: false,
        }, function() {

            player.src([{
                src: "<?php echo $this->azureStreamUrl; ?>",
                type: "application/vnd.ms-sstr+xml"
            }]);

            //if (bookmark > 0) player.currentTime(5);

            this.play();

            console.log('Good to go!');

            if (stato) document.querySelector('.vjs-progress-control').classList.remove('hidden');
            else document.querySelector('.vjs-progress-control').classList.add('hidden');

            this.addEventListener('play', function (e) {

                // if (bookmark > 0 && player.currentTime() < bookmark) {
                //      console.log("setcurrentetime " + bookmark);
                //      player.currentTime(bookmark);
                // }

                duration = player.duration();

            });

            this.addEventListener('timeupdate', function (e) {
                tview = player.currentTime().toFixed(0);
                if (!stato) {
                    if (duration && duration - tview < 20)
                        finish(tview);
                }
            });

            this.addEventListener('loadedmetadata', function (e) {
                //console.log("setcurrentetime " + bookmark);
                //player.currentTime(bookmark);
                //duration = player.duration();
            });

            this.addEventListener('loadeddata', function (e) {
                //console.log("setcurrentetime " + bookmark);
                //player.currentTime(bookmark);
                //duration = player.duration();
                jQuery('#buffer').trigger('click');
            });

            if (!stato) {

                this.addEventListener('ended', function (e) {
                    finish(player.duration().toFixed(0));
                }, false);
            }

        });

        //player.play();

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

        jQuery('#buffer').on('click', function (e) {
            e.preventDefault();
            if (bookmark > 0) {
                player.currentTime(bookmark);
            }
        });

        function finish(tempo) {
            stato = 1;

            /*
            jQuery.get("index.php?option=com_gglms&task=contenuto.updateTrack", {
                secondi: tempo,
                stato: 1,
                id_elemento: id_elemento
            });
            */

            let data_sync = null;
            let pAsync = get_async_call();
            data_sync = {async: pAsync};
            let id_utente = '<?php echo $this->id_utente;?>';
            let id_elemento = '<?php echo $this->contenuto->id; ?>';

            // passando uniquid forzo l'esecuzione di updateUserLog
            let globalUniq = (typeof gUniqid != 'undefined' ?  gUniqid : "");

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


    });

</script>


<div class="container-videosidepanelhide row">
    <div class="span3"></div>

    <div id="boxvideo" class="span6 center-block" style="min-height: 500px; text-align: center;">

        <video id="video"
            class="azuremediaplayer amp-default-skin amp-stream-skin amp-big-play-centered" tabindex="0">
            <?php /*
            <source type="application/vnd.ms-sstr+xml"
                src="<?php echo $this->azureStreamUrl; ?>"
            />
            */?>
            <p class="amp-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that supports HTML5 video</p>
        </video>

    </div>

</div>

<div class="row hidden">
    <p>
        <button id="buffer">Buffering overload</button>
    </p>
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



