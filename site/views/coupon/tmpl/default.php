<?php
// no direct access

defined('_JEXEC') or die('Restricted access');
?>

<script>
    jQuery(function() {
        jQuery.ajaxSetup({cache:false});
        jQuery("button").click(function(e){
            e.preventDefault();
            jQuery("#button_conferma_codice").hide();
            jQuery("#waiting_verifica_codice").show();

            jQuery.get("index.php?option=com_gglms&task=coupon.check_coupon", {coupon:jQuery("#box_coupon_field").val()},
                function(data){
                    if(data.valido){
                        jQuery("#box_coupon").hide();
                    }else
                    {
                        jQuery("#button_conferma_codice").show();
                        jQuery("#waiting_verifica_codice").hide();
                    }
                    jQuery("#report").fadeIn(function(){
                        jQuery("#report").html(data.report);
                    });
                },'json');

        });
    });
</script>


<div id="box_coupon_container">
    <div id="box_coupon">
        <h3 >Inserisci qui il tuo codice Coupon: </h3>
        <p >
            <input class="field" id="box_coupon_field" type="text" name="nome" />
            <br>
            <button id="button_conferma_codice" class="">Conferma codice</button>
        </p>
        <div id="waiting_verifica_codice" class="hide">
            <h3>Verifica codice in corso...</h3>
        </div>
    </div>

    <div id="report" ></div>
</div>
