<?php
// no direct access

defined('_JEXEC') or die('Restricted access');
?>

<script>
    jQuery(function () {
        jQuery.ajaxSetup({cache: false});
        jQuery("button").click(function (e) {
            e.preventDefault();
            jQuery("#button_conferma_codice").hide();
            jQuery("#waiting_verifica_codice").show();
            jQuery("#report").hide();

            jQuery.get("index.php?option=com_gglms&task=coupon.check_coupon_rinnovo", {coupon: jQuery("#box_coupon_field").val(), durata: jQuery("#box_durata_rinnovo").val()},
                function (data) {

                    console.log(data);
                    if (data.valido == 1) {
                        jQuery("#button_conferma_codice").show();
                        jQuery("#box_coupon_field").val("");
                        jQuery("#box_durata_rinnovo").val("");

                    } else {
                        jQuery("#button_conferma_codice").show();
                        jQuery("#waiting_verifica_codice").hide();
                    }
                    jQuery("#report").fadeIn(function () {
                        jQuery("#report").html(data.report);
                    });
                }, 'json');

        });
    });
</script>


<div id="box_coupon_container">
    <div id="box_coupon">
        <h3><?php echo JText::_('COM_GGLMS_COUPON_RENEW_COUPON_TITOLO')  ?></h3>
        <?php if ($this->visualizza_durata_rinnovo_coupon == 1) { ?>
        <p><?php echo JText::_('COM_GGLMS_COUPON_RENEW_COUPON_DESCRIZIONE_CON_DURATA_PERSONALIZZATA')  ?></p>
        <?php }else { ?>
        <p><?php echo JText::_('COM_GGLMS_COUPON_RENEW_COUPON_DESCRIZIONE')  ?></p>
        <?php } ?>
        <p>
            <input class="field" id="box_coupon_field" type="text" name="nome"/>
            <br>
            <?php if ($this->visualizza_durata_rinnovo_coupon == 1) { ?>
            <p>Inserisci la durata del rinnovo coupon in giorni.</p>
            <input class="field" id="box_durata_rinnovo" type="text" name="nome"/>
            <br>
            <?php } ?>
            <button id="button_conferma_codice" class="btn btn-primary btn-lg"><?php echo JText::_('COM_GGLMS_COUPON_CONFIRM')  ?></button>
        </p>
        <div id="waiting_verifica_codice" class="hide">
            <h3><?php echo JText::_('COM_GGLMS_COUPON_VERIFICA')  ?></h3>
        </div>
    </div>

    <div id="report"></div>
</div>
