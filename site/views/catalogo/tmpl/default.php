<?php
// no direct access

defined('_JEXEC') or die('Restricted access');

echo '<div class="main">'; ?>
<h1> <?php echo$this->catalogo[0]->description ?> </h1>
<?php
foreach ($this->catalogo as $item) { ?>

    <div class="item-container">

        <div class="img-container">
            <?php
            if (file_exists('../mediagg/images/unit/' . $item->id . '.jpg')) {
                $img = '../mediagg/images/unit/' . $item->id . '.jpg';
            } else {
                $img = 'components/com_gglms/libraries/images/immagine_non_disponibile.png';
            }
            ?>

            <img class="unit-img" src="<?php echo $img ?>">
        </div>

        <div class="unit-desc">
            <h3> <?php echo $item->titolo ?> </h3>
            <div>
                <?php echo $item->descrizione ?>
            </div>

        </div>


    </div>
    <hr>

<?php }

echo '</div>'
?>


