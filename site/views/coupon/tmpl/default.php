<?php
// no direct access

defined('_JEXEC') or die('Restricted access');
?>

<script>
    jQuery(function () {
        jQuery.ajaxSetup({cache: false});
        //jQuery("button").click(function (e) {
        jQuery("#button_conferma_codice").click(function (e) {
            e.preventDefault();
            jQuery("#button_conferma_codice").hide();
            jQuery("#waiting_verifica_codice").show();

            jQuery.get("index.php?option=com_gglms&task=coupon.check_coupon", {coupon: jQuery("#box_coupon_field").val()},
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


<div id="box_coupon_container" <?php echo $this->hideCouponGeneration ? 'style="height: inherit !important;"' : ''; ?>>
    <div id="box_coupon">
        <?php if (!$this->hideCouponGeneration) : ?>
        <h3><?php echo JText::_('COM_GGLMS_COUPON_INSERT'); ?></h3>
        <p><?php echo JText::_('COM_GGLMS_COUPON_DESCRIZIONE'); ?></p>
        <p>
            <input class="field" id="box_coupon_field" type="text" name="nome"/>
            <br>
            <button id="button_conferma_codice" class="btn btn-primary btn-lg"><?php echo JText::_('COM_GGLMS_COUPON_CONFIRM'); ?></button>
        </p>
        <div id="waiting_verifica_codice" class="hide">
            <h3><?php echo JText::_('COM_GGLMS_COUPON_VERIFICA'); ?></h3>
        </div>
        <? else: ?>
        <h3><b>La piattaforma si rinnova!</b></h3>
        <p>La versione attuale della piattaforma resterà disponibile ancora per alcuni mesi come archivio dei corsi già conclusi, ma non sarà più possibile attivare nuovi corsi.</p>
        <p>Il tuo account è già stato trasferito alla nuova piattaforma.</p>
        <h4><b>Come accedere?</b></h4>
        <ol>
            <li>Clicca sul pulsante qui sotto e utilizza il box <b>ACCEDI</b>.  Non creare un nuovo account!</li>
            <li>Inserisci l'indirizzo e-mail associato al tuo account sia nel campo <b>USERNAME</b> che nel campo <b>PASSWORD</b></li>
            <li>Riceverai una e-mail con un link per riattivare il tuo account e impostare una nuova password
                <br /><i>Per motivi di privacy, la tua password attuale non è stata trasferita. Potrai scegliere se impostarne una nuova oppure utilizzare la stessa che avevi in precedenza.</i>
            </li>
            <li>Una volta effettuato l'accesso, vai sulla scheda <b>CATALOGO CORSI</b> e troverai il box dedicato all'inserimento del coupon per sbloccare l'iscrizione al corso e iniziare la fruizione</li>
        </ol>
        <h4><b>E per i successivi accessi?</b></h4>
        <p>Trovi il link della nuova piattaforma in home page!</p>
        <h4><b>Hai bisogno di aiuto?</b></h4>
        <p>Utilizza il pulsante rosso di assistenza che trovi nell’angolo in basso a destra della nuova piattaforma.</p>
        <p style="text-align: center">
            <button 
                class="btn btn-primary btn-lg"
                style="height: 50px !important; width: 200px; border-radius: 7px !important;"
                onclick="javascript:event.preventDefault();window.open('<?php echo $this->newPlatformUrl; ?>', '_blank')"
                >
              <b>ACCEDI</b>
            </button>
        </p>
        <? endif; ?>
    </div>

    <div id="report"></div>
</div>
