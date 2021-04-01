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
        ?>

    </div>


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

            // form di registrazione
            jQuery('.btn-registrazione').on('click', function (e) {

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
                if (pCap.trim()  == "") {
                    customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR12'), '#cap_utente');
                    return;
                }

                var pPvUtente = jQuery('#pv_utente').val();
                if (pPvUtente  == "") {
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
                pPropArr.push({ campo: pUsernameID, cb: null, value: pUsername });

                var pPasswordID = jQuery('#password_utente').attr("id");
                pPropArr.push({ campo: pPasswordID, cb: null, value: pPassword });

                var pEmailID = jQuery('#email_utente').attr("id");
                pPropArr.push({ campo: pEmailID, cb: null, value: pEmail });

                var pNomeUtenteID = jQuery('#nome_utente').attr("id");
                var pNomeUtenteCB = jQuery('#nome_utente').attr("data-campo");
                pPropArr.push({ campo: pNomeUtenteID, cb: pNomeUtenteCB, value: pNomeUtente });

                var pCognomeUtenteID = jQuery('#cognome_utente').attr("id");
                var pCognomeUtenteCB = jQuery('#cognome_utente').attr("data-campo");
                pPropArr.push({ campo: pCognomeUtenteID, cb: pCognomeUtenteCB, value: pCognomeUtente });

                var pCfUtenteID = jQuery('#cf_utente').attr("id");
                var pCfUtenteCB = jQuery('#cf_utente').attr("data-campo");
                pPropArr.push({ campo: pCfUtenteID, cb: pCfUtenteCB, value: pCfUtente });

                var pDataNascitaID = jQuery('#data_nascita_utente').attr("id");
                var pDataNascitaCB = jQuery('#data_nascita_utente').attr("data-campo");
                pPropArr.push({ campo: pDataNascitaID, cb: pDataNascitaCB, value: pDataNascita });

                var pCittaNascitaID = jQuery('#citta_nascita_utente').attr("id");
                var pCittaNascitaCB = jQuery('#citta_nascita_utente').attr("data-campo");
                pPropArr.push({ campo: pCittaNascitaID, cb: pCittaNascitaCB, value: pCittaNascita });

                var pPvNascitaID = jQuery('#pv_nascita_utente').attr("id");
                var pPvNascitaCB = jQuery('#pv_nascita_utente').attr("data-campo");
                var pPvNascitaIDRef = jQuery('#pv_nascita_utente').attr("data-id-ref");
                pPropArr.push({ campo: pPvNascitaID, cb: pPvNascitaCB, value: pPvNascita, is_id: pPvNascitaIDRef });

                var pIndirizzoID = jQuery('#indirizzo_utente').attr("id");
                var pIndirizzoCB = jQuery('#indirizzo_utente').attr("data-campo");
                pPropArr.push({ campo: pIndirizzoID, cb: pIndirizzoCB, value: pIndirizzo });

                var pCittaResidenzaID = jQuery('#citta_utente').attr("id");
                var pCittaResidenzaCB = jQuery('#citta_utente').attr("data-campo");
                pPropArr.push({ campo: pCittaResidenzaID, cb: pCittaResidenzaCB, value: pCittaResidenza });

                var pCapID = jQuery('#cap_utente').attr("id");
                var pCapCB = jQuery('#cap_utente').attr("data-campo");
                pPropArr.push({ campo: pCapID, cb: pCapCB, value: pCap });

                var pPvUtenteID = jQuery('#pv_utente').attr("id");
                var pPvUtenteCB = jQuery('#pv_utente').attr("data-campo");
                var pPvUtenteIDRef = jQuery('#pv_utente').attr("data-id-ref");
                pPropArr.push({ campo: pPvUtenteID, cb: pPvUtenteCB, value: pPvUtente, is_id: pPvUtenteIDRef });

                var pTelefonoID = jQuery('#telefono_utente').attr("id");
                var pTelefonoCB = jQuery('#telefono_utente').attr("data-campo");
                pPropArr.push({ campo: pTelefonoID, cb: pTelefonoCB, value: pTelefono });

                var pProfessioneUtenteID = jQuery('#professione_utente').attr("id");
                var pProfessioneUtenteCB = jQuery('#professione_utente').attr("data-campo");
                var pProfessioneUtenteIDRef = jQuery('#professione_utente').attr("data-id-ref");
                pPropArr.push({ campo: pProfessioneUtenteID, cb: pProfessioneUtenteCB, value: pProfessioneUtente, is_id: pProfessioneUtenteIDRef });

                var pRuoloID = jQuery('#ruolo_utente').attr("id");
                var pRuoloCB = jQuery('#ruolo_utente').attr("data-campo");
                var pRuoloIDRef = jQuery('#ruolo_utente').attr("data-id-ref");
                pPropArr.push({ campo: pRuoloID, cb: pRuoloCB, value: pRuolo, is_id: pRuoloIDRef });

                var pOrdineID = jQuery('#ordine_utente').attr("id");
                var pOrdineCB = jQuery('#ordine_utente').attr("data-campo");
                var pOrdineIDRef = jQuery('#ordine_utente').attr("data-id-ref");
                pPropArr.push({ campo: pOrdineID, cb: pOrdineCB, value: pOrdine, is_id: pOrdineIDRef });

                var pIscrizioneAlboID = jQuery('#iscrizione_albo_utente').attr("id");
                var pIscrizioneAlboCB = jQuery('#iscrizione_albo_utente').attr("data-campo");
                pPropArr.push({ campo: pIscrizioneAlboID, cb: pIscrizioneAlboCB, value: pIscrizioneAlbo });

                var pReclutamentoID = jQuery('#reclutamento_utente').attr("id");
                var pReclutamentoCB = jQuery('#reclutamento_utente').attr("data-campo");
                var pReclutamentoIDRef = jQuery('#reclutamento_utente').attr("data-id-ref");
                pPropArr.push({ campo: pReclutamentoID, cb: pReclutamentoCB, value: pReclutamento, is_id: pReclutamentoIDRef });

                pPropArr.push({ campo: "id_evento", cb: null, value: pIdEvento });

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
                        }
                        else if (typeof data.error != "undefined") {
                            customAlertifyAlertSimple(data.error);
                            return;
                        }
                        else {
                            console.log("OK!");
                            window.location.href = "index.php?option=com_gglms&view=acquistaevento&action=user_registration_sponsor_request_confirm&pp=" + pToken;
                        }
                    },
                    error: function (er) {
                        customAlertifyAlertSimple(er);
                    }
                });


            });

        });

    </script>
