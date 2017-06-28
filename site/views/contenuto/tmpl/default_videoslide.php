<?php
defined('_JEXEC') or die('Restricted access');

if($this->contenuto->_params->get('abilita_breadcrumbs', 1))
    echo $this->loadTemplate('breadcrumb');

echo "<h1>".$this->contenuto->titolo."</h1>";
?>



<script type="text/javascript">

    jQuery(document).ready(function ($) {
        var hasPlayed = false;
        var player;
        var old_tempo;

        var id_elemento = <?php echo $this->contenuto->id; ?>;
        var stato = <?php echo $this->contenuto->getStato()->completato; ?>;
        var features = null;

        if (stato) {
            features = ['playpause', 'current', 'progress', 'duration', 'volume', 'fullscreen', 'tracks'];
        }
        else {
            features = ['playpause', 'current', 'duration', 'volume', 'fullscreen', 'tracks'];
        }

        var jumper_attuale = null;
        var jumper = new Array();

        <?php
        $i = 0;
        foreach ($this->jumper as $val) {
        ?>
        jumper[<?php echo $i++; ?>] = {
            'tstart': <?php echo $val['tstart']; ?>,
            'titolo': "<?php echo $val['titolo']; ?>"
        }
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
                    time = mediaElement.currentTime.toFixed(0);
                    sliding(time);

                }, false);


                if(!stato){
                    mediaElement.addEventListener('loadedmetadata', function(e){
                        if (!hasPlayed){
                            mediaElement.setCurrentTime(tview);
                            hasPlayed = true;
                        }
                    });

                    mediaElement.addEventListener('ended', function(e) {
                        stato = 1;
                        jQuery.get("index.php?option=com_gglms&task=contenuto.updateTrack", {
                            secondi:mediaElement.duration.toFixed(0),
                            stato:1,
                            id_elemento:id_elemento
                        });
                    }, false);
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
            sliding(time);
        });

        jQuery('.jumper.disabled').click(function () {
            alert("E' necessario guardare tutto il video prima di poter cliccare sui jumper");
        });


        function sliding(tempo) {
            if (old_tempo != tempo && typeof (jumper.length) != 'undefined') {

                old_tempo = tempo;
                var currTime = parseInt(tempo);
                var i = 0;
                var past_jumper_selector = new Array();
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

// //////////////////////

// DA MIGLIORARE !!!!! //

// //////////////////////

//INIZIO COMPARSA JUMPER
        jQuery("#jumper").click(function () {

            if (jQuery("#panel_jumper").hasClass('show')) {

                $("#sidepanel").removeClass('show').addClass('sidepanelhide');
                $(".sidepanel").removeClass('show').addClass('hide');
                $(".container-video").removeClass("container-videosidepanelshow").addClass("container-videosidepanelhide");
                $(".pulsante").removeClass("container-videosidepanelshow").addClass("container-videosidepanelhide");
                $("#moduli").removeClass("container-videosidepanelshow").addClass("container-videosidepanelhide");
            }
            else {
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



<div class="container-videosidepanelhide g-grid">
    <div id="boxvideo" class="g-block size-50">


        <video  style="width:100%; height:100%; " height="100%" controls="controls" preload="auto" class="img-thumbnail">
            <source type="video/mp4" src="<?php echo PATH_CONTENUTI.'/'.$this->contenuto->id. '/'.$this->contenuto->id.'.mp4'; ?>" />
            <source type="video/webm" src="<?php echo PATH_CONTENUTI.'/'.$this->contenuto->id. '/'.$this->contenuto->id.'.webm'; ?>" />
            <source type="video/ogg" src="<?php echo PATH_CONTENUTI.'/'.$this->contenuto->id. '/'.$this->contenuto->id.'.ogv'; ?>" />
            <track kind="slides" src="<?php echo PATH_CONTENUTI.'/'.$this->contenuto->id. '/'; ?>vtt_slide.vtt" />
        </video>

        <div id= "panel_jumper" class="sidepanel  ">
            <?php
            $i = 0;
            foreach ($this->jumper as $var) {
                $_titolo = $var['titolo'];
                $_tstart = $var['tstart'];

                //Genero il minutaggio del Jumper
                $h = floor($_tstart / 3600);
                $m = floor(($_tstart % 3600) / 60);
                $s = ($_tstart % 3600) % 60;
                $_durata = sprintf('%02d:%02d:%02d', $h, $m, $s);

                //DIV ID del jumper che serve poi impostare il colore di background
                $_jumper_div_id = $i;

                //Anteprima Jumper
                $_id_contenuto = JRequest::getInt('id', 0);

                $_img_contenuto = $this->contenuto->_path . "images/normal/Slide" . ($i + 1) . ".jpg";
                $_background = "background-image: url('" . $_img_contenuto . "'); background-size: 60px 50px; background-position: center;  width: 60px; height: 50px;";
//            $class = ($this->elemento['track']['cmi.core.lesson_status'] == 'completed') ? 'enabled' : 'disabled';
                $class = ($this->contenuto->getStato()->completato)  ? 'enabled' : 'disabled';

                $jumper = '<div class="jumper ' . $class . '" id="' . $_jumper_div_id . '" rel="' . $_tstart . '">';
                // $jumper.='<div class="anteprima_jumper" style="' . $_background . '"></div>';
                $jumper.=$_durata . "<br>" . $_titolo;
                $jumper.='</div>';
                echo $jumper;
                $i++;
            }
            ?>
        </div>


    </div>

    <div id="boxslide" class="g-block size-50">
        <div class="mejs-slides-player-slides img-thumbnail"></div>
    </div>
</div>



<div class="g-grid">
    <div class="g-block size-50">


        <!--        <div id="jumper" class="pulsante"><img  width="30px" src="components/com_gglms/libraries/images/tab_navigazione.png"/></div>-->
    </div>


</div>




