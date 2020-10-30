<?php
/**
 * Created by IntelliJ IDEA.
 * User: Francesca
 * Date: 28/10/2020
 * Time: 17:57
 */

defined('_JEXEC') or die;

?>


<div id="contenitoreprincipale" style="width: 100%">

    <?php
    // nessun corso
    if (count($this->corsi) == 0
            || is_null($this->dettaglio_corsi)
            || !is_array($this->dettaglio_corsi)
            || count($this->dettaglio_corsi) == 0) : ?>

        <div class="jumbotron jumbotron-fluid">
            <div class="container">
                <p class="lead"><strong><?php echo JText::_('COM_GGLMS_CRUSCOTTO_ORE_STR1'); ?></strong></p>
            </div>
        </div>


    <?php else : ?>

        <?php echo utilityHelper::buildRowsDettaglioCorsi($this->corsi, $this->dettaglio_corsi); ?>

        <!-- se esistenti esplodo i collapse -->
        <script type="text/javascript">

            if (jQuery('.collapse').length)
                jQuery('.collapse').collapse();

        </script>

    <?php endif; ?>

</div>


