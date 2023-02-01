<?php
defined('_JEXEC') or die('Restricted access');

//BREADCRUMBS
if ($this->contenuto->_params->get('abilita_breadcrumbs', 1))
    echo $this->loadTemplate('breadcrumb');

// dicitura link per consentire all'utente di visualizzare il contenuto in una nuova scheda del browser
$visualizza_link_semplice = "";
if ($this->contenuto->_params->get('visualizza_link_semplice', 0))
    $visualizza_link_semplice = outputHelper::visualizza_link_semplice_contenuto($this->contenuto->path);

//BOOTSTRAP SCORM FILE
$pathscorm = $this->contenuto->path;

//$pathscorm = 'C:/WAMP64/www/www.carigelearning.it/mediagg/contenuti/'.$this->contenuto->id.'/';


?>
<!--<script type="text/javascript">-->
<!--    --><?php //if(JFactory::getApplication()->getParams()->get('log_utente')==1) echo ' '.$this->id_utente.','.$this->contenuto->id.', null);' ?>
<!--</script>-->

<p style="text-align:center; margin: 100px;">
    <button id="start">
        <img src="components/com_gglms/libraries/images/avviatest.jpg">
    </button>
</p>

<?php echo $visualizza_link_semplice; ?>

<script type="text/javascript">


    <?php if ($this->disabilita_mouse == 1) { ?>

    document.addEventListener("contextmenu", function (e) {
        alert('Funzione disabilitata');
        e.preventDefault();
    }, true);

    <?php } ?>

    jQuery('#start').click(function () {

//        var w = 1120;
//        var h = 700;
//        var left = (screen.width / 2) - (w / 2);
//        var top = (screen.height / 2) - (h / 2);

        var w = screen.width;
        var h = screen.height;
        var left = 0;
        var top = 0;


        var stile = "top=" + top + ", left=" + left + ", width=" + w + ", height=" + h + ", status=no, menubar=no, toolbar=no, scrollbars=yes";

        var SCOInstanceID = '<?php echo $this->contenuto->id; ?>';

        var pathscorm = '<?php echo base64_encode($pathscorm); ?>';

        var id_utente = '<?php echo $this->contenuto->_userid; ?>';

        var log_status = '<?php echo JFactory::getApplication()->getParams()->get("log_utente")?>';

        var url = '../../../../scorm/webinar.php?SCOInstanceID=' + SCOInstanceID + '&pathscorm=' + pathscorm + '&id_utente=' + id_utente + '&log_status=' + log_status;

        window.open(url, "", stile);
    });
</script>
