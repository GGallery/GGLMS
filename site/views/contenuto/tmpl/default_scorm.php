<?php
defined('_JEXEC') or die('Restricted access');

if($this->contenuto->_params->get('abilita_breadcrumbs', 1))
    echo $this->loadTemplate('breadcrumb');

if($this->contenuto->path)
    $pathscorm = PATH_CONTENUTI.'/'.$this->contenuto->id.'/'.$this->contenuto->path;
else
    $pathscorm = PATH_CONTENUTI.'/'.$this->contenuto->id.'/index_lms_html5.html';

?>

<p style="text-align:center; margin: 100px;">
    <button id="start">
        <img   src="components/com_gglms/libraries/images/avviatest.jpg">
    </button>
</p>

<script type="text/javascript">
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

        var pathscorm = '<?php echo $pathscorm; ?>';

        var id_utente = '<?php echo $this->contenuto->_userid; ?>';

        var url = '../../../../scorm/rte.php?SCOInstanceID=' + SCOInstanceID + '&pathscorm=' + pathscorm + '&id_utente=' + id_utente;

        window.open(url, "", stile);
    });
</script>