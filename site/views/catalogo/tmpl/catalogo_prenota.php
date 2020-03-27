<?php
// no direct access

defined('_JEXEC') or die('Restricted access');

echo '<div class="main">'; ?>
<h1> I nostri corsi </h1>
<?php
foreach ($this->catalogo as $item) { ?>


    <div class="item-container">

        <!--        Non sono sicura che serva id piattaforma-->
        <!--echo  $this->id_piattaforma -->
        <div>
            <h6> <?php echo $item->titolo ?> </h6>
            <a style="font-weight: bold" href="<?php echo JURI::root() . 'index.php?option=com_gglms&view=prenota&id_corso=' . $item->id_corso ?>">
                PREZZI E ACQUISTO >>
            </a>
        </div>


    </div>
    <hr>

<?php }

echo '</div>'
?>


