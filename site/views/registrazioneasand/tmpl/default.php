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

    .btn-request , .btn-registrazione, .btn-bonifico {
        min-width: 50%;
        min-height: 50px;
        border-radius: 10px;
        font-size: 1.2em;
        background-color: rgba(98, 161, 156, 0.82);
        color: #FFFFFF
    }

    .form-check-input {
        appearance: auto !important;
    }

</style>

<?php
    if ($this->hide_pp) :
        echo $this->payment_form;
        ?>

    <script type="text/javascript">

        document.getElementById("show_hide_password").addEventListener("click", function (e) {

            if (document.getElementById("password_utente").type == 'password') {
                document.getElementById("show_hide_password_icon").classList.remove('fa-eye-slash');
                document.getElementById("show_hide_password_icon").classList.add('fa-eye');
                document.getElementById("password_utente").type = 'text';
                document.getElementById("ripeti_password_utente").type = 'text';
            }
            else {
                document.getElementById("show_hide_password_icon").classList.add('fa-eye-slash');
                document.getElementById("show_hide_password_icon").classList.remove('fa-eye');
                document.getElementById("password_utente").type = 'password';
                document.getElementById("ripeti_password_utente").type = 'password';
            }

        });

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
                let pCurrentId = jQuery(this).attr("id");
                let pLabelSelected = jQuery("#" + pCurrentId + " option:selected" ).text();

                jQuery("input.campi_professione:text").val("");
                jQuery(".campi_professione").val("");

                if (pLabelSelected.includes("Studente")) {
                    jQuery('#campi_studente').show();
                    jQuery('#campi_professione').hide();
                    jQuery("input.campi_dipendente:text").val("");
                    jQuery(".campi_dipendente").val("");
                }else {
                    jQuery('#campi_studente').hide();
                    jQuery('#campi_professione').show();
                    jQuery("input.campi_studente:text").val("");
                    jQuery(".campi_studente").val("");
                }

                jQuery('#campi_area').show();

            });

            jQuery('#posizione_lavorativa').on('change', function (e) {

                var pChecked = jQuery(this).attr("value");
                let pCurrentId = jQuery(this).attr("id");
                let pLabelSelected = jQuery("#" + pCurrentId + " option:selected" ).text();
                jQuery('#campi_dipendente').show();
                if (pLabelSelected.includes("Dipendente")) {
                    //jQuery('#campi_dipendente').show();

                    jQuery('#dipendente').show();
                    jQuery('#campi_libero').hide();

                } else {
                    //jQuery('#campi_dipendente').hide();
                    jQuery('#dipendente').hide();

                    if (!pLabelSelected.includes("occupato")) {
                        jQuery('#campi_libero').show();
                        jQuery('#area_pratica_professione_row').show();
                    }
                    else {
                        jQuery('#campi_libero').hide();
                        jQuery('#area_pratica_professione_row').hide();
                    }

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

                    if (!document.getElementById("check_quota_associativa1").checked
                        && !document.getElementById("check_quota_associativa2").checked) {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR29'), '#check_quota_associativa1');
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

                    var pEmail = jQuery('#email_utente').val();
                    if (pEmail.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR13'), '#email_utente');
                        return;
                    }

                    var pUsername = jQuery('#username').val();
                    if (pUsername.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR2'), '#username');
                        return;
                    }

                    var pCfUtente = jQuery('#cf_utente').val();
                    if (pCfUtente.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR12'), '#cf_utente');
                        return;
                    }

                    var pCittaNascita = jQuery('#citta_nascita_utente').val();
                    if (pCittaNascita.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR8'), '#citta_nascita_utente');
                        return;
                    }

                    var pPvNascitaUtente = jQuery('#pv_nascita').val();
                    if (pPvNascitaUtente == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR9'), '#pv_nascita');
                        return;
                    }

                    var pDataNascita = jQuery('#data_nascita_utente').val();
                    if (pDataNascita == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR18'), '#data_nascita_utente');
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
                        customAlertifyAlert(Joomla.JText._('COM_REGISTRAZIONE_ASAND_STR6'), '#cap_utente');
                        return;
                    }

                    var pTelefono = jQuery('#telefono_utente').val();
                    if (pTelefono.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR19'), '#telefono_utente');
                        return;
                    }

                    var pTitoloStudio = jQuery('#titolo_studio').val();
                    let pTitoloStudioText = jQuery( "#titolo_studio option:selected" ).text();
                    if (pTitoloStudio.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR35'), '#titolo_studio');
                        return;
                    }

                    // studente - inizio
                    var pUniversita = jQuery('#universita').val();
                    if (pUniversita.trim() == "" && pTitoloStudioText.includes("Studente")) {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR32'), '#universita');
                        return;
                    }

                    var pAnnoFrequenza = jQuery('#anno_di_frequenza').val();
                    if (pAnnoFrequenza.trim() == "" && pTitoloStudioText.includes("Studente")) {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR33'), '#anno_di_frequenza');
                        return;
                    }

                    var pMatricola = jQuery('#matricola').val();
                    if (pMatricola.trim() == "" && pTitoloStudioText.includes("Studente")) {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR34'), '#matricola');
                        return;
                    }

                    // var pAreaPraticaStudente = jQuery('#area_pratica_studente').val();
                    // if (pAreaPraticaStudente.trim() == "" && pTitoloStudioText.includes("Studente")) {
                    //     customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR37'), '#area_pratica_studente');
                    //     return;
                    // }
                    // studente - fine

                    var pPosizioneLavorativa = jQuery('#posizione_lavorativa').val();
                    let pPosizioneLavorativaText = jQuery( "#posizione_lavorativa option:selected" ).text();
                    if (pPosizioneLavorativa.trim() == "" && !pTitoloStudioText.includes("Studente")) {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR36'), '#posizione_lavorativa');
                        return;
                    }

                    // dipendente - inizio
                    var pAzienda = jQuery('#azienda').val();
                    let pAziendaText = jQuery( "#azienda option:selected" ).text();
                    if (pAzienda.trim() == "" && pPosizioneLavorativaText.includes("Dipendente")) {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR40'), '#azienda');
                        return;
                    }

                    var pIndirizzoAzienda = jQuery('#indirizzo_azienda').val();
                    if (pIndirizzoAzienda.trim() == "" && pPosizioneLavorativaText.includes("Dipendente")) {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR41'), '#indirizzo_azienda');
                        return;
                    }

                    var pCittaAzienda = jQuery('#citta_azienda').val();
                    if (pCittaAzienda.trim() == "" && pPosizioneLavorativaText.includes("Dipendente")) {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR42'), '#citta_azienda');
                        return;
                    }

                    // dipendente - fine

                    // piva - inizio
                    var pIndirizzoStudio = jQuery('#indirizzo_studio').val();
                    if (pIndirizzoStudio.trim() == "" && pPosizioneLavorativaText.includes("professionista")) {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR43'), '#indirizzo_studio');
                        return;
                    }

                    var pCittaStudio = jQuery('#citta_studio').val();
                    if (pCittaStudio.trim() == "" && pPosizioneLavorativaText.includes("professionista")) {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR44'), '#citta_studio');
                        return;
                    }

                    var pIva = jQuery('#piva').val();
                    if (pIva.trim() == "" && pPosizioneLavorativaText.includes("professionista")) {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR25'), '#piva');
                        return;
                    }
                    // piva - fine

                    var pAreaPratica = jQuery('#area_pratica_professione').val();
                    if (pAreaPratica.trim() == ""
                        && !pTitoloStudioText.includes("Studente")
                        && !pPosizioneLavorativaText.includes("occupato")) {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR37'), '#area_pratica_professione');
                        return;
                    }

                    var pNumeroAlbo = jQuery('#numero_albo').val();
                    if (pNumeroAlbo.trim() == "" && !pTitoloStudioText.includes("Studente")) {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR38'), '#numero_albo');
                        return;
                    }

                    var pPvAlbo = jQuery('#pv_albo').val();
                    if (pPvAlbo.trim() == "" && !pTitoloStudioText.includes("Studente")) {
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

                    if (!document.getElementById("privacy_check").checked) {
                        customAlertifyAlert('Devi accettare le condizioni generali', '#privacy_check');
                        return;
                    }

                    if (!pTitoloStudioText.includes("Studente") && document.getElementById("check_quota_associativa2").checked) {
                        customAlertifyAlertSimple('La quota associativa selezionata non è confacente con il titolo di studio e la professione');
                        return;
                    }

                    // oggetto che crea coppie di valori fra i dati inputati e community builder
                    var pNomeUtenteID = jQuery('#nome_utente').attr("id");
                    var pNomeUtenteCB = jQuery('#nome_utente').attr("data-campo");
                    pPropArr.push({campo: pNomeUtenteID, cb: pNomeUtenteCB, value: pNomeUtente.toUpperCase()});

                    var pCognomeUtenteID = jQuery('#cognome_utente').attr("id");
                    var pCognomeUtenteCB = jQuery('#cognome_utente').attr("data-campo");
                    pPropArr.push({campo: pCognomeUtenteID, cb: pCognomeUtenteCB, value: pCognomeUtente.toUpperCase()});

                    var pCfUtenteID = jQuery('#cf_utente').attr("id");
                    var pCfUtenteCB = jQuery('#cf_utente').attr("data-campo");
                    pPropArr.push({campo: pCfUtenteID, cb: pCfUtenteCB, value: pCfUtente.toUpperCase()});

                    var pCittaNascitaID = jQuery('#citta_nascita_utente').attr("id");
                    var pCittaNascitaCB = jQuery('#citta_nascita_utente').attr("data-campo");
                    pPropArr.push({campo: pCittaNascitaID, cb: pCittaNascitaCB, value: pCittaNascita.toUpperCase()});

                    var pPvNascitaUtenteID = jQuery('#pv_nascita').attr("id");
                    var pPvNascitaUtenteCB = jQuery('#pv_nascita').attr("data-campo");
                    var pPvNascitaUtenteIDRef = jQuery('#pv_nascita').attr("data-id-ref");
                    pPropArr.push({campo: pPvNascitaUtenteID, cb: pPvNascitaUtenteCB, value: pPvNascitaUtente, is_id: pPvNascitaUtenteIDRef});

                    var pIndirizzoID = jQuery('#indirizzo_utente').attr("id");
                    var pIndirizzoCB = jQuery('#indirizzo_utente').attr("data-campo");
                    pPropArr.push({campo: pIndirizzoID, cb: pIndirizzoCB, value: pIndirizzo});

                    var pCittaResidenzaID = jQuery('#citta_utente').attr("id");
                    var pCittaResidenzaCB = jQuery('#citta_utente').attr("data-campo");
                    pPropArr.push({campo: pCittaResidenzaID, cb: pCittaResidenzaCB, value: pCittaResidenza.toUpperCase()});

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

                    var pUsernameID = jQuery('#username').attr("id");
                    pPropArr.push({
                        campo: pUsernameID,
                        cb: null,
                        value: pUsername
                    });

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

                    var pUniversitaID = jQuery('#universita').attr("id");
                    var pUniversitaCB = jQuery('#universita').attr("data-campo");
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
                    pPropArr.push({campo: pIndirizzoAziendaID, cb: pIndirizzoAziendaCB, value: pIndirizzoAzienda.toUpperCase()});

                    var pCittaAziendaID = jQuery('#citta_azienda').attr("id");
                    var pCittaAziendaCB = jQuery('#citta_azienda').attr("data-campo");
                    pPropArr.push({campo: pCittaAziendaID, cb: pCittaAziendaCB, value: pCittaAzienda.toUpperCase()});

                    var pIndirizzoStudioID = jQuery('#indirizzo_studio').attr("id");
                    var pIndirizzoStudioCB = jQuery('#indirizzo_studio').attr("data-campo");
                    pPropArr.push({campo: pIndirizzoStudioID, cb: pIndirizzoStudioCB, value: pIndirizzoStudio.toUpperCase()});

                    var pCittaStudioID = jQuery('#citta_studio').attr("id");
                    var pCittaStudioCB = jQuery('#citta_studio').attr("data-campo");
                    pPropArr.push({campo: pCittaStudioID, cb: pCittaStudioCB, value: pCittaStudio.toUpperCase()});

                    var pIvaID = jQuery('#piva').attr("id");
                    var pIvaCB = jQuery('#piva').attr("data-campo");
                    pPropArr.push({campo: pIvaID, cb: pIvaCB, value: pIva.toUpperCase()});

                    var pAreaPraticaID = "";
                    var pAreaPraticaCB = "";
                    // if (pTitoloStudioText.includes("Studente")) {
                    //     pAreaPraticaID = jQuery('#area_pratica_studente').attr("id");
                    //     pAreaPraticaCB = jQuery('#area_pratica_studente').attr("data-campo");
                    // }
                    if (!pPosizioneLavorativaText.includes("occupato")) {
                        pAreaPraticaID = jQuery('#area_pratica_professione').attr("id");
                        pAreaPraticaCB = jQuery('#area_pratica_professione').attr("data-campo");
                        pPropArr.push({campo: pAreaPraticaID, cb: pAreaPraticaCB, value: pAreaPratica});
                    }

                    var pNumeroAlboID = jQuery('#numero_albo').attr("id");
                    var pNumeroAlboCB = jQuery('#numero_albo').attr("data-campo");
                    pPropArr.push({campo: pNumeroAlboID, cb: pNumeroAlboCB, value: pNumeroAlbo});

                    var pPvAlboID = jQuery('#pv_albo').attr("id");
                    var pPvAlboCB = jQuery('#pv_albo').attr("data-campo");
                    var pPvAlboIDRef = jQuery('#pv_albo').attr("data-id-ref");
                    pPropArr.push({campo: pPvAlboID, cb: pPvAlboCB, value: pPvAlbo, is_id: pPvAlboIDRef});

                    var pQuotaAssociativa = document.querySelector('input[name="check_quota_associativa"]:checked').value;
                    pPropArr.push({campo: "quota_associativa", cb: null, value: pQuotaAssociativa });


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
                                if (data.token == undefined || data.token == '') {
                                    customAlertifyAlertSimple('Risposta del server malformata!');
                                    return;
                                }
                                let pToken = data.token;
                                window.location.href = "index.php?option=com_gglms&view=registrazioneasand&action=user_registration_payment&pp=" + pToken;
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

    <?php else : ?>

    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $this->client_id; ?>&currency=EUR" data-sdk-integration-source="button-factory"></script>

    <div class="container">

        <?php echo $this->payment_form; ?>

        <div class="row">
            <div class="col-md-6 offset-md-3">
                <p id="descriptionError" style="display: none; color: red;">
                    <?php echo JText::_('COM_PAYPAL_SINPE_STR2') ?>
                </p>
                <p  id="priceLabelError" style="display: none; color: red;">
                    <?php echo JText::_('COM_PAYPAL_SINPE_STR3') ?>
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="alert alert-danger" role="alert" id="paymentError" style="display: none;">
                    <?php echo JText::_('COM_PAYPAL_SINPE_STR4') ?> <br />
                    <p>
                    <pre id="paymentErrorDetails"></pre>
                    </p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="alert alert-success" role="alert" id="paymentSuccess" style="display: none;">
                    <?php echo JText::_('COM_PAYPAL_SINPE_STR5') ?> <br />
                    <p>
                        <textarea id="paymentSuccessDetails" class="form-control col-sm-6"></textarea>
                    </p>
                </div>
            </div>
        </div>

    </div>


    <script type="text/javascript">

        jQuery(function() {
            if (jQuery('#paypal-button-container').length)
                initPayPalButton();
        });

        // pagamento con bonifico conferma
        jQuery('#btn-bonifico').on('click', function (e) {
            let pHref = jQuery(this).attr("data-ref");
            window.location.href = pHref;
        });

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
                            + '&pp=registrazioneasand'
                            + '&order_id=' + details.id
                            + '&token=' + jQuery('#token').val()

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

    </script>

    <?php endif; ?>

</div>

