<?php
// no direct access

defined('_JEXEC') or die('Restricted access');
?>


<div id="box_coupon_container">
    <div id="box_coupon">
        <p><?php echo $this->dispenser->descrizione; ?></p>

        <p>
            <input class="field" id="box_email_field" type="email" name="email" required/>
            <input class="field" id="id_iscrizione" type="hidden" name="id_iscrizione"
                   value="<?php echo $this->dispenser->id_iscrizione; ?>"/>
            <input class="field" id="id_dispenser" type="hidden" name="id_dispenser"
                   value="<?php echo $this->dispenser->id; ?>"/>

            <br>
            <button id="button_conferma_codice" class="btn btn-primary btn-lg"><?php echo  JText::_('COM_GGLMS_GLOBAL_CONFERMA') ?></button>
        </p>
        <div id="waiting_verifica_codice" class="hide">
            <h3><?php echo  JText::_('COM_GGLMS_COUPON_DISPENSER_SENDING') ?></h3>
        </div>
    </div>

    <div id="report"></div>
</div>


<script>
    jQuery(function () {
        jQuery.ajaxSetup({cache: false});
        jQuery("button").click(function (e) {
            e.preventDefault();
            jQuery("#button_conferma_codice").hide();
            jQuery("#waiting_verifica_codice").show();

            jQuery.get("index.php?option=com_gglms&task=coupondispenser.check",
                {
                    email: jQuery("#box_email_field").val(),
                    id_dispenser: jQuery("#id_dispenser").val(),
                    id_iscrizione: jQuery("#id_iscrizione").val()
                },
                function (data) {
                    if (data.valido) {
                        jQuery("#box_coupon").hide();
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
