<?php
/**
 * Created by IntelliJ IDEA.
 * User: Luca
 * Date: 31/03/2021
 * Time: 18:20
 */
defined('_JEXEC') or die('Restricted access');
?>

<div class="container-fluid">

    <?php
            echo $this->_html;
            if ($this->in_error)
                echo outputHelper::get_no_corsi_error(JText::_('COM_GGLMS_BOXES_SCHEDA_PRENOTAZIONE_WRONG'));
    ?>

</div>

<script type="text/javascript">

    function customAlertifyAlertSimple(pMsg) {
        alertify.alert()
            .setting({
                'title': 'Attenzione!',
                'label':'OK',
                'message': pMsg
            }).show();
    }

    function eseguiPrenotazione(pUserId, pGruppoCorso, pUrl) {

        window.location.href = pUrl + '&uid=' + pUserId + '&ug=' + pGruppoCorso;

    }

    jQuery(function () {

        <?php if (!is_null($this->box_id)) { ?>
            jQuery('#g-slideshow').hide();
            jQuery('#g-header').hide();
            jQuery('#g-content').hide();
        <?php } else if (!is_null($this->unit_id)) { ?>
            jQuery('#g-slideshow').hide();
        <?php } ?>

        jQuery('#btn-prenota-corso').on('click', function (event) {
            event.preventDefault();

            var pUserId = jQuery(this).attr("data-user");
            var pGruppoCorso = jQuery(this).attr("data-ug");
            var pUrl = '<?php echo $this->prenota_url;?>';

            if (pUserId == 0
                || pUserId == ""
                || pUserId == undefined) {
                customAlertifyAlertSimple('<?php echo JText::_('COM_GGLMS_BOXES_SCHEDA_POSTI_LOGIN_ERROR')?>');
                return;
            }

            if (pGruppoCorso == 0
                || pGruppoCorso == ""
                || pUserId == undefined) {
                customAlertifyAlertSimple('<?php echo JText::_('COM_GGLMS_BOXES_SCHEDA_POSTI_GRUPPO_ERROR')?>');
                return;
            }


            alertify.confirm(
                '<?php echo JText::_('COM_GGLMS_BOXES_SCHEDA_PRENOTA_TITOLO');?>',
                '<?php echo JText::_('COM_GGLMS_BOXES_SCHEDA_PRENOTA_CONFIRM');?>',
                function(){
                    window.location.href = pUrl + '&uid=' + pUserId + '&ug=' + pGruppoCorso;
                    return;
                },
                function(){
                    console.log("cancel..");
                    return;
                }
            );

        })
    })

</script>


