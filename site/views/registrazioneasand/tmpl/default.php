<?php
/**
 * Created by IntelliJ IDEA.
 * User: Salma
 * Date: 05/12/2022
 * Time: 15:26
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

<?php

    if ($this->in_error == 1
        || $this->action == 'bb_buy_request'
        || $this->hide_pp) {

        echo $this->payment_form;

    } ?>

    <script type="text/javascript">


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



            jQuery('#titolo_studio').on('change', function (e) {

                var pChecked = jQuery(this).attr("value");
                if (pChecked == '530') {
                    jQuery('#campi_studente').show();
                    jQuery('#campi_professione').hide();
                }else {
                    jQuery('#campi_studente').hide();
                    jQuery('#campi_professione').show();
                    jQuery("input.campi_studente:text").val("");
                }


            });

            jQuery('#posizione_lavorativa').on('change', function (e) {

                var pChecked = jQuery(this).attr("value");
                if (pChecked == '534') {
                    jQuery('#campi_dipendente').show();
                    jQuery('#campi_libero').hide();

                }else {
                    jQuery('#campi_dipendente').hide();
                    jQuery('#campi_libero').show();
                    jQuery("input.campi_dipendente:text").val("");
                }


            });

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

                    var pTitoloStudio = jQuery('#titolo_studio').val();
                    if (pTitoloStudio.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR35'), '#titolo_studio');
                        return;
                    }

                    var pUniversita = jQuery('#università').val();
                    if (pUniversita.trim() == "" && pTitoloStudio == '530') {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR32'), '#niversità');
                        return;
                    }

                    var pAnnoFrequenza = jQuery('#anno_di_frequenza').val();
                    if (pAnnoFrequenza.trim() == "" && pTitoloStudio == '530') {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR33'), '#anno_di_frequenza');
                        return;
                    }

                    var pMatricola = jQuery('#matricola').val();
                    if (pMatricola.trim() == "" && pTitoloStudio == '530') {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR34'), '#matricola');
                        return;
                    }

                    var pPosizioneLavorativa = jQuery('#posizione_lavorativa').val();
                    if (pPosizioneLavorativa.trim() == "" && pTitoloStudio != '530') {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR36'), '#posizione_lavorativa');
                        return;
                    }

                    var pAzienda = jQuery('#azienda').val();
                    if (pAzienda.trim() == "" && pPosizioneLavorativa == '534') {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR40'), '#azienda');
                        return;
                    }

                    var pIndirizzoAzienda = jQuery('#indirizzo_azienda').val();
                    if (pIndirizzoAzienda.trim() == "" && pPosizioneLavorativa == '534') {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR41'), '#indirizzo_azienda');
                        return;
                    }

                    var pCittaAzienda = jQuery('#citta_azienda').val();
                    if (pCittaAzienda.trim() == "" && pPosizioneLavorativa == '534') {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR42'), '#citta_azienda');
                        return;
                    }

                    var pIndirizzoStudio = jQuery('#indirizzo_studio').val();
                    if (pIndirizzoStudio.trim() == "" && pPosizioneLavorativa == '535') {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR43'), '#indirizzo_studio');
                        return;
                    }

                    var pCittaStudio = jQuery('#citta_studio').val();
                    if (pCittaStudio.trim() == "" && pPosizioneLavorativa == '535') {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR44'), '#citta_studio');
                        return;
                    }

                    var pIva = jQuery('#piva').val();
                    if (pIva.trim() == "" && pPosizioneLavorativa == '535') {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR25'), '#piva');
                        return;
                    }

                    var pAreaPratica = jQuery('#area_pratica').val();
                    if (pAreaPratica.trim() == "" && pTitoloStudio != '530') {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR37'), '#area_pratica');
                        return;
                    }

                    var pNumeroAlbo = jQuery('#numero_albo').val();
                    if (pNumeroAlbo.trim() == "" && pTitoloStudio != '530') {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR38'), '#numero_albo');
                        return;
                    }

                    var pPvAlbo = jQuery('#pv_albo').val();
                    if (pPvAlbo.trim() == "" && pTitoloStudio != '530') {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR39'), '#pv_albo');
                        return;
                    }

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

                    var pTitoloStudioID = jQuery('#titolo_studio').attr("id");
                    var pTitoloStudioCB = jQuery('#titolo_studio').attr("data-campo");
                    var pTitoloStudioIDRef = jQuery('#titolo_studio').attr("data-id-ref");
                    pPropArr.push({
                        campo: pTitoloStudioID,
                        cb: pTitoloStudioCB,
                        value: pTitoloStudio,
                        is_id: pTitoloStudioIDRef
                    });

                    var pUniversitaID = jQuery('#università').attr("id");
                    var pUniversitaCB = jQuery('#università').attr("data-campo");
                    pPropArr.push({campo: pUniversitaID, cb: pUniversitaCB, value: pUniversita});

                    var pAnnoFrequenzaID = jQuery('#anno_di_frequenza').attr("id");
                    var pAnnoFrequenzaCB = jQuery('#anno_di_frequenza').attr("data-campo");
                    var pAnnoFrequenzaIDRef = jQuery('#anno_di_frequenza').attr("data-id-ref");
                    pPropArr.push({campo: pAnnoFrequenzaID, cb: pAnnoFrequenzaCB, value: pAnnoFrequenza, is_id: pAnnoFrequenzaIDRef});

                    var pMatricolaID = jQuery('#matricola').attr("id");
                    var pMatricolaCB = jQuery('#matricola').attr("data-campo");
                    pPropArr.push({campo: pMatricolaID, cb: pMatricolaCB, value: pMatricola});

                    var pPasswordID = jQuery('#password_utente').attr("id");
                    pPropArr.push({campo: pPasswordID, cb: null, value: pPassword});

                    var pPosizioneLavorativaID = jQuery('#posizione_lavorativa').attr("id");
                    var pPosizioneLavorativaCB = jQuery('#posizione_lavorativa').attr("data-campo");
                    var pPosizioneLavorativaIDRef = jQuery('#posizione_lavorativa').attr("data-id-ref");
                    pPropArr.push({campo: pPosizioneLavorativaID, cb: pPosizioneLavorativaCB, value: pPosizioneLavorativa, is_id: pPosizioneLavorativaIDRef});

                    var pAziendaID = jQuery('#azienda').attr("id");
                    var pAziendaCB = jQuery('#azienda').attr("data-campo");
                    pPropArr.push({campo: pAziendaID, cb: pAziendaCB, value: pAzienda});

                    var pIndirizzoAziendaID = jQuery('#indirizzo_azienda').attr("id");
                    var pIndirizzoAziendaCB = jQuery('#indirizzo_azienda').attr("data-campo");
                    pPropArr.push({campo: pIndirizzoAziendaID, cb: pIndirizzoAziendaCB, value: pIndirizzoAzienda});

                    var pCittaAziendaID = jQuery('#citta_azienda').attr("id");
                    var pCittaAziendaCB = jQuery('#citta_azienda').attr("data-campo");
                    pPropArr.push({campo: pCittaAziendaID, cb: pCittaAziendaCB, value: pCittaAzienda});

                    var pIndirizzoStudioID = jQuery('#indirizzo_studio').attr("id");
                    var pIndirizzoStudioCB = jQuery('#indirizzo_studio').attr("data-campo");
                    pPropArr.push({campo: pIndirizzoStudioID, cb: pIndirizzoStudioCB, value: pIndirizzoStudio});

                    var pCittaStudioID = jQuery('#citta_studio').attr("id");
                    var pCittaStudioCB = jQuery('#citta_studio').attr("data-campo");
                    pPropArr.push({campo: pCittaStudioID, cb: pCittaStudioCB, value: pCittaStudio});

                    var pIvaID = jQuery('#piva').attr("id");
                    var pIvaCB = jQuery('#piva').attr("data-campo");
                    pPropArr.push({campo: pIvaID, cb: pIvaCB, value: pIva});

                    var pAreaPraticaID = jQuery('#area_pratica').attr("id");
                    var pAreaPraticaCB = jQuery('#area_pratica').attr("data-campo");
                    pPropArr.push({campo: pAreaPraticaID, cb: pAreaPraticaCB, value: pAreaPratica});

                    var pNumeroAlboID = jQuery('#numero_albo').attr("id");
                    var pNumeroAlboCB = jQuery('#numero_albo').attr("data-campo");
                    pPropArr.push({campo: pNumeroAlboID, cb: pNumeroAlboCB, value: pNumeroAlbo});

                    var pPvAlboID = jQuery('#pv_albo').attr("id");
                    var pPvAlboCB = jQuery('#pv_albo').attr("data-campo");
                    var pPvAlboIDRef = jQuery('#pv_albo').attr("data-id-ref");
                    pPropArr.push({campo: pPvAlboID, cb: pPvAlboCB, value: pPvAlbo, is_id: pPvAlboIDRef});


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
                                window.location.href = "index.php?option=com_gglms&view=registrazioneasand&action=user_registration_confirm&pp=" + pToken;
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

