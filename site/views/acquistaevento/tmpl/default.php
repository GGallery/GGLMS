<?php
/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

?>

<style>

    .btn-request , .btn-registrazione {
        min-width: 50%;
        min-height: 50px;
        border-radius: 10px;
        font-size: 1.2em;
    }

</style>

<div class="container-fluid">

<?php

    if ($this->in_error == 1
        || $this->action == 'bb_buy_request'
        || $this->hide_pp) {

        echo $this->payment_form;

    } else if ($this->action == 'buy'
        && !$this->hide_pp) { ?>
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $this->client_id; ?>&currency=EUR" data-sdk-integration-source="button-factory"></script>

        <div class="row">
            <?php echo $this->payment_form; ?>
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


<?php } ?>

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

                    if (amount.value.length < 1) {
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
                            + '&pp=acquistaevento'
                            + '&order_id=' + details.id
                            + '&token=' + jQuery('#token').val()
                            + '&evvasnd=<?php echo $this->is_asand ? 100 : 99;?>'

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

        function customAlertifyAlert(pCampoNome, pRefCampo) {
            alertify.alert()
                .setting({
                    'title': 'Attenzione!',
                    'label':'OK',
                    'message': 'Compila il campo ' + pCampoNome + '!' ,
                    'onok': function() {
                        jQuery([document.documentElement, document.body]).animate({
                            scrollTop: jQuery(pRefCampo).offset().top
                        }, 1000);
                    }
                }).show();
        }

        function customAlertifyAlertSimple(pMsg) {
            alertify.alert()
                .setting({
                    'title': 'Attenzione!',
                    'label':'OK',
                    'message': pMsg
                }).show();
        }

        jQuery(function() {


            jQuery('.datepicker').datepicker({
                language: '<?php echo $this->dp_lang; ?>',
                format: 'dd/mm/yyyy'
            });

            // inizializza form PayPal
            if (jQuery('#paypal-button-container').length)
                initPayPalButton();

            // checkbox pagamento
            jQuery('#evento_da_pagare').on('change', function (e){
                jQuery(this).attr("checked", true);
            });

            // pagamento con bonifico conferma
            jQuery('#btn-bonifico').on('click', function (e) {
                var pHref = jQuery(this).attr("data-ref");
                window.location.href = pHref;
            });

            // richiesta di login oppure registrazione
            jQuery('.btn-request').on('click', function (e) {
                var pHref = jQuery(this).attr("data-ref");
                window.location.href = pHref;
            });

            // richiesta fattura
            jQuery('#check_richiesta_fattura').on('change', function (e) {

                var pChecked = jQuery(this).is(":checked");
                if (pChecked)
                    jQuery('#campi_fattura').show();
                else {
                    jQuery('#campi_fattura').hide();
                    jQuery("input.campi_fattura:text").val("");
                }

            });

            <?php if ($this->is_asand) : ?>

                /*
                jQuery('#titolo_studio').on('change', function (e) {

                    document.getElementById("rowGiornoStd").classList.add("hidden");

                    if (e.target.options[e.target.selectedIndex].text.toLowerCase() == 'studente')
                        document.getElementById("rowGiornoStd").classList.remove("hidden");
                    else
                        document.getElementById('informazioniextra').value = '';

                });
                */

                if (jQuery('#btn-informazioniextra_pay').length) {
                    jQuery('#btn-informazioniextra_pay').on('click', function (e) {
                        e.preventDefault();

                        var pRefDt = jQuery('#informazioniextra_pay').val();
                        var pRefId = event.target.getAttribute("data-ref-id");

                        jQuery.ajax({
                            type: "POST",
                            url: "index.php?option=com_gglms&task=api.storeReqEventDt",
                            // You are expected to receive the generated JSON (json_encode($data))
                            data: {"ref_id": pRefId, "dts": pRefDt},
                            dataType: "json",
                            success: function (data) {

                                // controllo errore
                                if (typeof data != "object") {
                                    customAlertifyAlertSimple(data);
                                    return;
                                } else if (typeof data.error != "undefined") {
                                    customAlertifyAlertSimple(data.error);
                                    return;
                                } else {
                                    customAlertifyAlertSimple(data.success);
                                }
                            },
                            error: function (er) {
                                customAlertifyAlertSimple(er);
                            }
                        });

                    });
                }


            <?php endif; ?>

            // form di registrazione
            if (jQuery('.btn-registrazione').length > 0) {
                jQuery('.btn-registrazione').on('click', function (e) {

                    e.preventDefault();

                    var pRef = jQuery(this).attr("data-ref");
                    var pToken = jQuery('#token').val();
                    var pPropArr = [];

                    // validazione dati

                    if (pToken == "") {
                        customAlertifyAlertSimple('Nessun token di sicurezza definito. Impossibile continuare');
                        return;
                    }

                    var pNomeUtente = jQuery('#nome_utente').val();
                    if (pNomeUtente.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR8'), '#nome_utente');
                        return;
                    }

                    var pCognomeUtente = jQuery('#cognome_utente').val();
                    if (pCognomeUtente.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR9'), '#cognome_utente');
                        return;
                    }

                    var pCfUtente = jQuery('#cf_utente').val();
                    if (pCfUtente.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR12'), '#cf_utente');
                        return;
                    }

                    var pEmail = jQuery('#email_utente').val();
                    if (pEmail.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR13'), '#email_utente');
                        return;
                    }

                    var pIndirizzo = jQuery('#indirizzo_utente').val();
                    if (pIndirizzo.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR14'), '#indirizzo_utente');
                        return;
                    }

                    var pCittaResidenza = jQuery('#citta_utente').val();
                    if (pCittaResidenza.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR15'), '#citta_utente');
                        return;
                    }

                    var pPvUtente = jQuery('#pv_utente').val();
                    if (pPvUtente == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR16'), '#pv_utente');
                        return;
                    }

                    var pCap = jQuery('#cap_utente').val();
                    if (pCap.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR17'), '#cap_utente');
                        return;
                    }

                    var pDataNascita = jQuery('#data_nascita_utente').val();
                    if (pDataNascita == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR18'), '#data_nascita_utente');
                        return;
                    }

                    var pTelefono = jQuery('#telefono_utente').val();
                    if (pTelefono.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR19'), '#telefono_utente');
                        return;
                    }

                    var pProfessioneUtente = jQuery('#professione_utente').val();
                    if (pProfessioneUtente.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR20'), '#professione_utente');
                        return;
                    }

                    <?php if (!$this->is_asand) : ?>

                    var pLaureaIn = jQuery('#laureain_utente').val();
                    if (pLaureaIn == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR21'), '#laureain_utente');
                        return;
                    }

                    var pAnnoLaurea = jQuery('#anno_laurea_utente').val();
                    if (pAnnoLaurea.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR22'), '#anno_laurea_utente');
                        return;
                    }

                    <?php else: ?>

                    var pUsername = jQuery('#username').val();
                    if (pUsername.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR2'), '#username');
                        return;
                    }

                    var pCittaNascita = jQuery('#citta_nascita_utente').val();
                    if (pCittaNascita == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR8'), '#citta_nascita_utente');
                        return;
                    }

                    var pPvNascita = jQuery('#pv_nascita_utente').val();
                    if (pPvNascita == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR9'), '#pv_nascita_utente');
                        return;
                    }

                    var pTitoloStudio = jQuery('#titolo_studio').val();
                    if (pTitoloStudio == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR35'), '#titolo_studio');
                        return;
                    }

                    /*
                    var pInformazioniExtra = jQuery('#informazioniextra').val();
                    if (pInformazioniExtra == "" && jQuery( "#titolo_studio option:selected" ).text().toLowerCase() == 'studente') {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR45'), '#informazioniextra');
                        return;
                    }
                    */

                    <?php endif; ?>

                    var pPassword = jQuery('#password_utente').val();
                    if (pPassword == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR10'), '#password_utente');
                        return;
                    }

                    var pRipetiPassword = jQuery('#ripeti_password_utente').val();
                    if (pRipetiPassword == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR11'), '#ripeti_password_utente');
                        return;
                    }

                    if (pPassword != pRipetiPassword) {
                        customAlertifyAlertSimple('Le password non corrispondono');
                        return;
                    }

                    var pRagioneSociale = jQuery('#ragione_sociale').val();
                    var pPiva = jQuery('#partita_iva').val();
                    var pCodiceDestinatario = jQuery('#codice_destinatario').val();

                    // oggetto che crea coppie di valori fra i dati inputati e community builder
                    var pNomeUtenteID = jQuery('#nome_utente').attr("id");
                    var pNomeUtenteCB = jQuery('#nome_utente').attr("data-campo");
                    pPropArr.push({campo: pNomeUtenteID, cb: pNomeUtenteCB, value: pNomeUtente});

                    var pCognomeUtenteID = jQuery('#cognome_utente').attr("id");
                    var pCognomeUtenteCB = jQuery('#cognome_utente').attr("data-campo");
                    pPropArr.push({campo: pCognomeUtenteID, cb: pCognomeUtenteCB, value: pCognomeUtente});

                    var pCfUtenteID = jQuery('#cf_utente').attr("id");
                    var pCfUtenteCB = jQuery('#cf_utente').attr("data-campo");
                    pPropArr.push({campo: pCfUtenteID, cb: pCfUtenteCB, value: pCfUtente});

                    var pIndirizzoID = jQuery('#indirizzo_utente').attr("id");
                    var pIndirizzoCB = jQuery('#indirizzo_utente').attr("data-campo");
                    pPropArr.push({campo: pIndirizzoID, cb: pIndirizzoCB, value: pIndirizzo});

                    var pCittaResidenzaID = jQuery('#citta_utente').attr("id");
                    var pCittaResidenzaCB = jQuery('#citta_utente').attr("data-campo");
                    pPropArr.push({campo: pCittaResidenzaID, cb: pCittaResidenzaCB, value: pCittaResidenza});

                    var pPvUtenteID = jQuery('#pv_utente').attr("id");
                    var pPvUtenteCB = jQuery('#pv_utente').attr("data-campo");
                    var pPvUtenteIDRef = jQuery('#pv_utente').attr("data-id-ref");
                    pPropArr.push({campo: pPvUtenteID, cb: pPvUtenteCB, value: pPvUtente, is_id: pPvUtenteIDRef});

                    var pCapID = jQuery('#cap_utente').attr("id");
                    var pCapCB = jQuery('#cap_utente').attr("data-campo");
                    pPropArr.push({campo: pCapID, cb: pCapCB, value: pCap});

                    var pDataNascitaID = jQuery('#data_nascita_utente').attr("id");
                    var pDataNascitaCB = jQuery('#data_nascita_utente').attr("data-campo");
                    pPropArr.push({campo: pDataNascitaID, cb: pDataNascitaCB, value: pDataNascita});

                    var pEmailID = jQuery('#email_utente').attr("id");
                    pPropArr.push({campo: pEmailID, cb: null, value: pEmail});

                    var pTelefonoID = jQuery('#telefono_utente').attr("id");
                    var pTelefonoCB = jQuery('#telefono_utente').attr("data-campo");
                    pPropArr.push({campo: pTelefonoID, cb: pTelefonoCB, value: pTelefono});

                    var pProfessioneUtenteID = jQuery('#professione_utente').attr("id");
                    var pProfessioneUtenteCB = jQuery('#professione_utente').attr("data-campo");
                    var pProfessioneUtenteIDRef = jQuery('#professione_utente').attr("data-id-ref");
                    pPropArr.push({
                        campo: pProfessioneUtenteID,
                        cb: pProfessioneUtenteCB,
                        value: pProfessioneUtente,
                        is_id: pProfessioneUtenteIDRef
                    });

                    <?php if (!$this->is_asand) : ?>

                    var pLaureaInID = jQuery('#laureain_utente').attr("id");
                    var pLaureaInCB = jQuery('#laureain_utente').attr("data-campo");
                    var pLaureaInIDRef = jQuery('#laureain_utente').attr("data-id-ref");
                    pPropArr.push({campo: pLaureaInID, cb: pLaureaInCB, value: pLaureaIn, is_id: pLaureaInIDRef});

                    var pAnnoLaureaID = jQuery('#anno_laurea_utente').attr("id");
                    var pAnnoLaureaCB = jQuery('#anno_laurea_utente').attr("data-campo");
                    pPropArr.push({campo: pAnnoLaureaID, cb: pAnnoLaureaCB, value: pAnnoLaurea});

                    <?php else: ?>

                    var pUsernameID = jQuery('#username').attr("id");
                    pPropArr.push({campo: pUsernameID, cb: null, value: pUsername});

                    var pCittaNascitaID = jQuery('#citta_nascita_utente').attr("id");
                    var pCittaNascitaCB = jQuery('#citta_nascita_utente').attr("data-campo");
                    pPropArr.push({campo: pCittaNascitaID, cb: pCittaNascitaCB, value: pCittaNascita});

                    var pTitoloStudioID = jQuery('#titolo_studio').attr("id");
                    var pTitoloStudioCB = jQuery('#titolo_studio').attr("data-campo");
                    var pTitoloStudioIDRef = jQuery('#titolo_studio').attr("data-id-ref");
                    pPropArr.push({campo: pTitoloStudioID, cb: pTitoloStudioCB, value: pTitoloStudio, is_id: pTitoloStudioIDRef});

                    /*
                    var pInformazioniExtraID = jQuery('#informazioniextra').attr("id");
                    var pInformazioniExtraCB = jQuery('#informazioniextra').attr("data-campo");
                    var pInformazioniExtraRef = jQuery('#informazioniextra').attr("data-id-ref");
                    pPropArr.push({campo: pInformazioniExtraID, cb: pInformazioniExtraCB, value: pInformazioniExtra, is_id: pInformazioniExtraRef});
                    */

                    var pPvNascitaID = jQuery('#pv_nascita_utente').attr("id");
                    var pPvNascitaCB = jQuery('#pv_nascita_utente').attr("data-campo");
                    var pPvNascitaIDRef = jQuery('#pv_nascita_utente').attr("data-id-ref");
                    pPropArr.push({campo: pPvNascitaID, cb: pPvNascitaCB, value: pPvNascita, is_id: pPvNascitaIDRef});

                    <?php endif; ?>

                    var pPasswordID = jQuery('#password_utente').attr("id");
                    pPropArr.push({campo: pPasswordID, cb: null, value: pPassword});

                    var pRagioneSocialeID = jQuery('#ragione_sociale').attr("id");
                    var pRagioneSocialeCB = jQuery('#ragione_sociale').attr("data-campo");
                    pPropArr.push({campo: pRagioneSocialeID, cb: pRagioneSocialeCB, value: pRagioneSociale});

                    var pPivaID = jQuery('#partita_iva').attr("id");
                    var pPivaCB = jQuery('#partita_iva').attr("data-campo");
                    pPropArr.push({campo: pPivaID, cb: pPivaCB, value: pPiva});

                    var pCodiceDestinatarioID = jQuery('#codice_destinatario').attr("id");
                    var pCodiceDestinatarioCB = jQuery('#codice_destinatario').attr("data-campo");
                    pPropArr.push({campo: pCodiceDestinatarioID, cb: pCodiceDestinatarioCB, value: pCodiceDestinatario});

                    jQuery.ajax({
                        type: "GET",
                        url: pRef,
                        // You are expected to receive the generated JSON (json_encode($data))
                        data: {"request_obj": pPropArr},
                        dataType: "json",
                        success: function (data) {

                            // controllo errore
                            if (typeof data != "object") {
                                customAlertifyAlertSimple(data);
                                return;
                            } else if (typeof data.error != "undefined") {
                                customAlertifyAlertSimple(data.error);
                                return;
                            } else {
                                console.log("OK!");
                                window.location.href = "index.php?option=com_gglms&view=acquistaevento&action=user_registration_confirm&pp=" + pToken;
                            }
                        },
                        error: function (er) {
                            customAlertifyAlertSimple(er);
                        }
                    });


                });
            }

            // form di registrazione ad evento sponsor
            if (jQuery('.btn-registrazione-sponsor').length > 0) {
                jQuery('.btn-registrazione-sponsor').on('click', function (e) {

                    e.preventDefault();

                    var pRef = jQuery(this).attr("data-ref");
                    var pToken = jQuery('#token').val();
                    var pPropArr = [];

                    var pUsername = jQuery('#username').val();
                    if (pUsername.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR2'), '#username');
                        return;
                    }

                    var pPassword = jQuery('#password_utente').val();
                    if (pPassword.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR3'), '#password_utente');
                        return;
                    }

                    var pEmail = jQuery('#email_utente').val();
                    if (pEmail.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR20'), '#email_utente');
                        return;
                    }

                    var pNomeUtente = jQuery('#nome_utente').val();
                    if (pNomeUtente.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR4'), '#nome_utente');
                        return;
                    }

                    var pCognomeUtente = jQuery('#cognome_utente').val();
                    if (pCognomeUtente.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR5'), '#cognome_utente');
                        return;
                    }

                    var pCfUtente = jQuery('#cf_utente').val();
                    if (pCfUtente.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR6'), '#cf_utente');
                        return;
                    }

                    var pDataNascita = jQuery('#data_nascita_utente').val();
                    if (pDataNascita == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR7'), '#data_nascita_utente');
                        return;
                    }

                    var pCittaNascita = jQuery('#citta_nascita_utente').val();
                    if (pCittaNascita == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR8'), '#citta_nascita_utente');
                        return;
                    }

                    var pPvNascita = jQuery('#pv_nascita_utente').val();
                    if (pPvNascita == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR9'), '#pv_nascita_utente');
                        return;
                    }

                    var pIndirizzo = jQuery('#indirizzo_utente').val();
                    if (pIndirizzo.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR10'), '#indirizzo_utente');
                        return;
                    }

                    var pCittaResidenza = jQuery('#citta_utente').val();
                    if (pCittaResidenza.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR11'), '#citta_utente');
                        return;
                    }

                    var pCap = jQuery('#cap_utente').val();
                    if (pCap.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR12'), '#cap_utente');
                        return;
                    }

                    var pPvUtente = jQuery('#pv_utente').val();
                    if (pPvUtente == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR13'), '#pv_utente');
                        return;
                    }

                    var pTelefono = jQuery('#telefono_utente').val();
                    if (pTelefono.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR14'), '#telefono_utente');
                        return;
                    }

                    var pProfessioneUtente = jQuery('#professione_utente').val();
                    if (pProfessioneUtente.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR15'), '#professione_utente');
                        return;
                    }

                    var pRuolo = jQuery('#ruolo_utente').val();
                    if (pRuolo.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR16'), '#ruolo_utente');
                        return;
                    }

                    var pOrdine = jQuery('#ordine_utente').val();
                    if (pOrdine.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR17'), '#ordine_utente');
                        return;
                    }

                    var pIscrizioneAlbo = jQuery('#iscrizione_albo_utente').val();
                    if (pIscrizioneAlbo.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR18'), '#iscrizione_albo_utente');
                        return;
                    }

                    var pReclutamento = jQuery('#reclutamento_utente').val();
                    if (pReclutamento.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR19'), '#reclutamento_utente');
                        return;
                    }

                    var pIdEvento = jQuery('#id_evento').val();
                    if (pIdEvento.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR21'), '#id_evento');
                        return;
                    }

                    // oggetto che crea coppie di valori fra i dati inputati e community builder
                    var pUsernameID = jQuery('#username').attr("id");
                    pPropArr.push({campo: pUsernameID, cb: null, value: pUsername});

                    var pPasswordID = jQuery('#password_utente').attr("id");
                    pPropArr.push({campo: pPasswordID, cb: null, value: pPassword});

                    var pEmailID = jQuery('#email_utente').attr("id");
                    pPropArr.push({campo: pEmailID, cb: null, value: pEmail});

                    var pNomeUtenteID = jQuery('#nome_utente').attr("id");
                    var pNomeUtenteCB = jQuery('#nome_utente').attr("data-campo");
                    pPropArr.push({campo: pNomeUtenteID, cb: pNomeUtenteCB, value: pNomeUtente});

                    var pCognomeUtenteID = jQuery('#cognome_utente').attr("id");
                    var pCognomeUtenteCB = jQuery('#cognome_utente').attr("data-campo");
                    pPropArr.push({campo: pCognomeUtenteID, cb: pCognomeUtenteCB, value: pCognomeUtente});

                    var pCfUtenteID = jQuery('#cf_utente').attr("id");
                    var pCfUtenteCB = jQuery('#cf_utente').attr("data-campo");
                    pPropArr.push({campo: pCfUtenteID, cb: pCfUtenteCB, value: pCfUtente});

                    var pDataNascitaID = jQuery('#data_nascita_utente').attr("id");
                    var pDataNascitaCB = jQuery('#data_nascita_utente').attr("data-campo");
                    pPropArr.push({campo: pDataNascitaID, cb: pDataNascitaCB, value: pDataNascita});

                    var pCittaNascitaID = jQuery('#citta_nascita_utente').attr("id");
                    var pCittaNascitaCB = jQuery('#citta_nascita_utente').attr("data-campo");
                    pPropArr.push({campo: pCittaNascitaID, cb: pCittaNascitaCB, value: pCittaNascita});

                    var pPvNascitaID = jQuery('#pv_nascita_utente').attr("id");
                    var pPvNascitaCB = jQuery('#pv_nascita_utente').attr("data-campo");
                    var pPvNascitaIDRef = jQuery('#pv_nascita_utente').attr("data-id-ref");
                    pPropArr.push({campo: pPvNascitaID, cb: pPvNascitaCB, value: pPvNascita, is_id: pPvNascitaIDRef});

                    var pIndirizzoID = jQuery('#indirizzo_utente').attr("id");
                    var pIndirizzoCB = jQuery('#indirizzo_utente').attr("data-campo");
                    pPropArr.push({campo: pIndirizzoID, cb: pIndirizzoCB, value: pIndirizzo});

                    var pCittaResidenzaID = jQuery('#citta_utente').attr("id");
                    var pCittaResidenzaCB = jQuery('#citta_utente').attr("data-campo");
                    pPropArr.push({campo: pCittaResidenzaID, cb: pCittaResidenzaCB, value: pCittaResidenza});

                    var pCapID = jQuery('#cap_utente').attr("id");
                    var pCapCB = jQuery('#cap_utente').attr("data-campo");
                    pPropArr.push({campo: pCapID, cb: pCapCB, value: pCap});

                    var pPvUtenteID = jQuery('#pv_utente').attr("id");
                    var pPvUtenteCB = jQuery('#pv_utente').attr("data-campo");
                    var pPvUtenteIDRef = jQuery('#pv_utente').attr("data-id-ref");
                    pPropArr.push({campo: pPvUtenteID, cb: pPvUtenteCB, value: pPvUtente, is_id: pPvUtenteIDRef});

                    var pTelefonoID = jQuery('#telefono_utente').attr("id");
                    var pTelefonoCB = jQuery('#telefono_utente').attr("data-campo");
                    pPropArr.push({campo: pTelefonoID, cb: pTelefonoCB, value: pTelefono});

                    var pProfessioneUtenteID = jQuery('#professione_utente').attr("id");
                    var pProfessioneUtenteCB = jQuery('#professione_utente').attr("data-campo");
                    var pProfessioneUtenteIDRef = jQuery('#professione_utente').attr("data-id-ref");
                    pPropArr.push({
                        campo: pProfessioneUtenteID,
                        cb: pProfessioneUtenteCB,
                        value: pProfessioneUtente,
                        is_id: pProfessioneUtenteIDRef
                    });

                    var pRuoloID = jQuery('#ruolo_utente').attr("id");
                    var pRuoloCB = jQuery('#ruolo_utente').attr("data-campo");
                    var pRuoloIDRef = jQuery('#ruolo_utente').attr("data-id-ref");
                    pPropArr.push({campo: pRuoloID, cb: pRuoloCB, value: pRuolo, is_id: pRuoloIDRef});

                    var pOrdineID = jQuery('#ordine_utente').attr("id");
                    var pOrdineCB = jQuery('#ordine_utente').attr("data-campo");
                    var pOrdineIDRef = jQuery('#ordine_utente').attr("data-id-ref");
                    pPropArr.push({campo: pOrdineID, cb: pOrdineCB, value: pOrdine, is_id: pOrdineIDRef});

                    var pIscrizioneAlboID = jQuery('#iscrizione_albo_utente').attr("id");
                    var pIscrizioneAlboCB = jQuery('#iscrizione_albo_utente').attr("data-campo");
                    pPropArr.push({campo: pIscrizioneAlboID, cb: pIscrizioneAlboCB, value: pIscrizioneAlbo});

                    var pReclutamentoID = jQuery('#reclutamento_utente').attr("id");
                    var pReclutamentoCB = jQuery('#reclutamento_utente').attr("data-campo");
                    var pReclutamentoIDRef = jQuery('#reclutamento_utente').attr("data-id-ref");
                    pPropArr.push({
                        campo: pReclutamentoID,
                        cb: pReclutamentoCB,
                        value: pReclutamento,
                        is_id: pReclutamentoIDRef
                    });

                    pPropArr.push({campo: "id_evento", cb: null, value: pIdEvento});

                    jQuery.ajax({
                        type: "GET",
                        url: pRef,
                        // You are expected to receive the generated JSON (json_encode($data))
                        data: {"request_obj": pPropArr},
                        dataType: "json",
                        success: function (data) {

                            // controllo errore
                            if (typeof data != "object") {
                                customAlertifyAlertSimple(data);
                                return;
                            } else if (typeof data.error != "undefined") {
                                customAlertifyAlertSimple(data.error);
                                return;
                            } else {
                                console.log("OK!");
                                window.location.href = "index.php?option=com_gglms&view=acquistaevento&action=user_registration_sponsor_request_confirm&pp=" + pToken;
                            }
                        },
                        error: function (er) {
                            customAlertifyAlertSimple(er);
                        }
                    });


                });
            }

            // form di login per registrazione ad evento sponsor
            if (jQuery('.btn-accedi-sponsor').length) {

                jQuery('.btn-accedi-sponsor').on('click', function (e) {

                    e.preventDefault();

                    var pRef = jQuery(this).attr("data-ref");
                    var pToken = jQuery('#token').val();
                    var pPropArr = [];

                    var pUsername = jQuery('#username').val();
                    if (pUsername.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR2'), '#username');
                        return;
                    }

                    var pPassword = jQuery('#password_utente').val();
                    if (pPassword.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR3'), '#password_utente');
                        return;
                    }

                    pPropArr.push({username: pUsername, password_utente: pPassword});

                    jQuery.ajax({
                        type: "GET",
                        url: pRef,
                        // You are expected to receive the generated JSON (json_encode($data))
                        data: {"request_obj": pPropArr},
                        dataType: "json",
                        success: function (data) {

                            // controllo errore
                            if (typeof data != "object") {
                                customAlertifyAlertSimple(data);
                                return;
                            } else if (typeof data.error != "undefined") {
                                customAlertifyAlertSimple(data.error);
                                return;
                            } else {
                                console.log("OK!");
                                window.location.href = "index.php?option=com_gglms&view=acquistaevento&action=user_insert_confirm_group_sponsor_evento&pp=" + pToken;
                            }
                        },
                        error: function (er) {
                            customAlertifyAlertSimple(er);
                        }
                    });

                });

            }

        });

    </script>

</div>
