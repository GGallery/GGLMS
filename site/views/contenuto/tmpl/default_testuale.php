<?php
defined('_JEXEC') or die('Restricted access');

if($this->contenuto->_params->get('abilita_breadcrumbs', 1))
    echo $this->loadTemplate('breadcrumb');
$files= $this->contenuto->getFiles();
echo "<h1>".$this->contenuto->titolo."</h1>";
?>

<script type="text/javascript">

    jQuery(document).ready(function ($) {

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

<?php if(!empty($files)): ?>
    <div id="files_sottotitolo" class="g-grid ">
        <ul>
            <?php
            foreach($files as $file){
                echo "<li>";
                echo '<a target="_blank" href="/mediagg/files/'.$file->id.'/'.$file->filename.'">'.$file->name.'</a>';
                echo "</li>";
            }
            ?>
        </ul>
    </div>
<?php endif; ?>


<div class="contenuto_testuale">
    <?php echo $this->contenuto->descrizione; ?>
</div>
