<?php

defined('_JEXEC') or die('Restricted access');
?>

<script>
    jQuery(function () {
        jQuery.ajaxSetup({cache: false});
        jQuery("button").click(function (e) {

            e.preventDefault();
            jQuery("#button_conferma_codice").hide();
            jQuery("#waiting_verifica_codice").show();

            jQuery.get("index.php?option=com_gglms&task=api.check_codice_votazione", {codice: jQuery("#box_codice_field").val(),user_id: '<?php echo $this->user_id ;?>'},
                function (data) {
                    if (data.valido) {
                        console.log("OK!");
                        window.location.href = "index.php?option=com_gglms&view=dettagliutente&layout=dettagliutente&template=votazione_candidati_sinpe"
                    } else {
                        jQuery("#button_conferma_codice_vot").show();
                        jQuery("#waiting_verifica_codice").hide();
                    }
                    jQuery("#report").fadeIn(function () {
                        jQuery("#report").html(data.report);
                    });
                }, 'json');

        });
    });
</script>


<div id="box_codice_container">
    <div id="box_codice">
        <h4>Inserisci qui il tuo codice per la Votazione</h4>
        <br>
        <p>
            <input class="field rounded" id="box_codice_field" type="text" name="nome"/>
            <br>
            <br>
            <button id="button_conferma_codice_vot" class="btn btn-primary btn-lg" style=" font-size: large !important;">Conferma codice</button>
        </p>
        <div id="waiting_verifica_codice" class="hide">
            <h3>Verifica codice in corso..</h3>
        </div>
    </div>

    <div id="report"></div>
</div>

