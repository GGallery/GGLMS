<?php
// no direct access

defined('_JEXEC') or die('Restricted access');

echo '<div class="container-fluid">';
//echo '<h2  style="text-align: center" >Lista Corsi</h2>';
foreach ($this->catalogo as $item) { ?>

    <div class="row">
        <h3  style="text-align: center"> <?php echo $item->titolo ?> </h3>
        <div class="col-xs-2">
            <?php
            if (file_exists('../mediagg/images/unit/' . $item->id . '.jpg')) {
                $img = '../mediagg/images/unit/' . $item->id . '.jpg';
            } else {
                $img = 'components/com_gglms/libraries/images/immagine_non_disponibile.png';
            }
            ?>
            <img width="100px" style="text-align: center" src="<?php echo $img ?>">
        </div>
        <div class="col-xs-10">
            <div> <?php echo $item->descrizione ?>
                <a style="font-size: larger; margin-left: 5px; font-weight: bold" href="<?php echo JRoute::_('index.php?option=com_gglms&view=unita&alias=' . $item->alias) ?>"> Vai al corso</a>
            </div>

        </div>
    </div>

<?php }

echo '</div>'
?>


