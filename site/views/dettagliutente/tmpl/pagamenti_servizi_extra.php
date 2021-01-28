<?php
/**
 * Created by IntelliJ IDEA.
 * User: Francesca
 * Date: 28/01/2021
 * Time: 10:23
 */

defined('_JEXEC') or die('Restricted access');

?>

<div class="container-fluid">

<?php if (!$this->in_error) : ?>

    <div class="page-header">
        <h3><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_PAGAMENTI_EXTRA_STR1'); ?></h3>
    </div>

    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $this->client_id; ?>&currency=EUR" data-sdk-integration-source="button-factory"></script>


    <div class="row">
        <?php echo $this->_html; ?>
        <p id="descriptionError" style="display: none; color: red;">
            <?php echo JText::_('COM_PAYPAL_SINPE_STR2') ?>
        </p>
        <p  id="priceLabelError" style="display: none; color: red;">
            <?php echo JText::_('COM_PAYPAL_SINPE_STR3') ?>
        </p>
    </div>

    <div class="alert alert-danger" role="alert" id="paymentError" style="display: none;">
        <?php echo JText::_('COM_PAYPAL_SINPE_STR4') ?> <br />
        <p>
        <pre id="paymentErrorDetails"></pre>
        </p>
    </div>

    <div class="alert alert-success" role="alert" id="paymentSuccess" style="display: none;">
        <?php echo JText::_('COM_PAYPAL_SINPE_STR5') ?> <br />
        <p>
            <textarea id="paymentSuccessDetails" class="form-control col-sm-6"></textarea>
        </p>
    </div>

    <script type="text/javascript">

    function initPayPalButton() {
        var description = document.querySelector('#description');
        var amount = document.querySelector('#amount');
        var priceError = document.querySelector('#priceLabelError');
        var paymentError = document.querySelector('#paymentError');
        var paymentErrorDetails = document.querySelector('#paymentErrorDetails');
        var paymentSuccess = document.querySelector('#paymentSuccess');
        var paymentSuccessDetails = document.querySelector('#paymentSuccessDetails');

        var elArr = [description, amount];

        var purchase_units = [];
        purchase_units[0] = {};
        purchase_units[0].amount = {};

        function validate(event) {
            return event.value.length > 0;
        }

        paypal.Buttons({
            style: {
                color: 'gold',
                shape: 'pill',
                label: 'pay',
                layout: 'horizontal',

            },

            onClick: function () {

                if (description.value.length < 1) {
                    descriptionError.style.display = "block";
                } else {
                    descriptionError.style.display = "none";
                }

                if (amount.value.length < 1
                    || amount.value == 0
                    || amount.value == ""
                    || amount.value == "0") {
                    priceError.style.display = "block";
                } else {
                    priceError.style.display = "none";
                }

                paymentError.style.display = "none";
                paymentSuccess.style.display = "none";
                paymentErrorDetails.textContent = "";
                paymentSuccessDetails.value = "";


                purchase_units[0].description = description.value;
                purchase_units[0].amount.value = amount.value;

            },

            createOrder: function (data, actions) {
                return actions.order.create({
                    purchase_units: purchase_units,
                });
            },

            onApprove: function (data, actions) {
                return actions.order.capture().then(function (details) {

                    window.location.href = 'index.php?option=com_gglms&view=paypal'
                        + '&pp=servizi_extra'
                        + '&order_id=' + details.id
                        + '&user_id=' + jQuery('#user_id').val()
                        + '&totale=' + jQuery('#amount').val()
                        + '&totale_espen=' + jQuery('#amount_espen').val();

                });
            },

            onError: function (err) {
                //console.log(err);
                paymentError.style.display = 'block';
                paymentSuccess.style.display = 'hidden';
                paymentErrorDetails.textContent = err;
            }
        }).render('#paypal-button-container');
    }

    jQuery(function() {


        initPayPalButton();

        jQuery('#anni_da_pagare_espen').on('change', function (){

            if (!jQuery('#amount_espen').length)
                return false;

            var amount = parseFloat(jQuery('#amount').val());
            var amountEspen = parseFloat(jQuery('#tariffa_espen').val());
            var description = jQuery('#description').val();

            var nuovoTotale = 0;
            var totaleEspen = 0;
            var nuovaDescription = "";

            if (jQuery(this).attr("checked")) {
                nuovoTotale = amount + amountEspen;
                totaleEspen = amountEspen;
                nuovaDescription = description + jQuery.trim(jQuery('#anni_da_pagare_espen').attr("data-descr"));
            }
            else {
                nuovoTotale = amount - amountEspen;
                nuovaDescription = description.replace(jQuery.trim(jQuery('#anni_da_pagare_espen').attr("data-descr")), "");
            }

            jQuery('#amount').val(nuovoTotale);
            jQuery('#amount_espen').val(totaleEspen);
            jQuery('#amount_span').html(nuovoTotale);
            jQuery('#description').val(nuovaDescription);

        });

    });

    </script>

    <?php else:
        echo $this->_html;
        ?>

    <?php endif; ?>

</div>
