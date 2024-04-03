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
    if ($this->hide_pp && $this->in_error == 0) :
        echo $this->payment_form;
        ?>

    <script type="text/javascript">

        if (document.getElementById("show_hide_password") != null) {

            document.getElementById("show_hide_password").addEventListener("click", function (e) {

                if (document.getElementById("password_utente").type == 'password') {
                    document.getElementById("show_hide_password_icon").classList.remove('fa-eye-slash');
                    document.getElementById("show_hide_password_icon").classList.add('fa-eye');
                    document.getElementById("password_utente").type = 'text';
                }
                else {
                    document.getElementById("show_hide_password_icon").classList.add('fa-eye-slash');
                    document.getElementById("show_hide_password_icon").classList.remove('fa-eye');
                    document.getElementById("password_utente").type = 'password';
                }

            });

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

            document.getElementById('email_utente').addEventListener('input', function() {
                checkEmail(this.value);
            });
            
            document.getElementById('cb_codicefiscale').addEventListener('input', function() {
                checkCodiceFiscale(this.value);
            });
            
            document.getElementById('username').addEventListener('input', function() {
                checkUsername(this.value);
            });

            jQuery('.datepicker').datepicker({
                language: '<?php echo $this->dp_lang; ?>',
                format: 'dd/mm/yyyy'
            });

            
            // form di registrazione
            if (jQuery('.btn-registrazione').length > 0) {
                jQuery('.btn-registrazione').on('click', async function (e) {

                    e.preventDefault();

                    const pRef = jQuery(this).attr("data-ref");
                    let pPropArr = [];

                    const formData = new FormData();
                    const formObject = {};

                    // validazione dati
                    const pNomeUtente = jQuery('#cb_nome').val();
                    if (pNomeUtente.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR8'), '#cb_nome');
                        return;
                    }

                    const pCognomeUtente = jQuery('#cb_cognome').val();
                    if (pCognomeUtente.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR9'), '#cb_cognome');
                        return;
                    }

                    const pEmail = jQuery('#email_utente').val();
                    if (pEmail.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR13'), '#email_utente');
                        return;
                    }

                    const pUsername = jQuery('#username').val();
                    if (pUsername.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR2'), '#username');
                        return;
                    }

                    const pAltraEmail = jQuery('#cb_altraemail').val();

                    const pTitolo = jQuery('#cb_titolo').val();
                    if (pTitolo.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR46'), '#cb_titolo');
                        return;
                    }

                    const pCfUtente = jQuery('#cb_codicefiscale').val();
                    if (pCfUtente.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR12'), '#cb_codicefiscale');
                        return;
                    }

                    const pDataNascita = jQuery('#cb_datadinascita').val();
                    if (pDataNascita == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR18'), '#cb_datadinascita');
                        return;
                    }

                    const pCittaNascita = jQuery('#cb_luogodinascita').val();
                    if (pCittaNascita.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR8'), '#cb_luogodinascita');
                        return;
                    }

                    const pPvNascitaUtente = jQuery('#cb_provinciadinascita').val();
                    if (pPvNascitaUtente == "") {
                        customAlertifyAlert(Joomla.JText._('COM_GGLMS_ISCRIZIONE_EVENTO_STR9'), '#cb_provinciadinascita');
                        return;
                    }
                    
                    const pNazionalita = jQuery('#cb_nazionalita').val();
                    if (pNazionalita == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR47'), '#cb_nazionalita');
                        return;
                    }

                    const pTelefono = jQuery('#cb_telefono').val();

                    const pIndirizzo = jQuery('#cb_indirizzodiresidenza').val();
                    if (pIndirizzo.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR14'), '#cb_indirizzodiresidenza');
                        return;
                    }

                    const pPvUtente = jQuery('#cb_provdiresidenza').val();
                    if (pPvUtente == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR16'), '#cb_provdiresidenza');
                        return;
                    }

                    const pCittaResidenza = jQuery('#cb_citta').val();
                    if (pCittaResidenza.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR15'), '#cb_citta');
                        return;
                    }

                    const pCap = jQuery('#cb_cap').val();
                    if (pCap.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_REGISTRAZIONE_ASAND_STR6'), '#cb_cap');
                        return;
                    }

                    const pRegione = jQuery('#cb_regione').val();
                    if (pRegione.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR48'), '#cb_regione');
                        return;
                    }

                    const pLaureaIn = jQuery('#cb_laureain').val();
                    if (pLaureaIn.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR21'), '#cb_laureain');
                        return;
                    }

                    const pLaureAnno = jQuery('#cb_laureanno').val();
                    if (pLaureAnno.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR22'), '#cb_laureanno');
                        return;
                    }

                    const pProfessioneDisciplina = jQuery('#cb_professionedisciplina').val();
                    if (pProfessioneDisciplina.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR49'), '#cb_professionedisciplina');
                        return;
                    }

                    const pOrdine = jQuery('#cb_ordine').val();
                    if (pOrdine.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR50'), '#cb_ordine');
                        return;
                    }

                    const pNumeroIscrizione = jQuery('#cb_numeroiscrizione').val();
                    if (pNumeroIscrizione.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR38'), '#cb_numeroiscrizione');
                        return;
                    }

                    /*
                    const pQualifica = jQuery('#cb_qualifica').val();
                    if (pQualifica.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR52'), '#cb_qualifica');
                        return;
                    }
                    */

                    const pAzienda = jQuery('#cb_azienda').val();
                    if (pAzienda.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR53'), '#cb_azienda');
                        return;
                    }

                    const pDipartimento = jQuery('#cb_dipartimento').val();
                    /*
                    if (pDipartimento.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR54'), '#cb_dipartimento');
                        return;
                    }
                    */

                    const pReparto = jQuery('#cb_reparto').val();
                    /*
                    if (pReparto.trim() == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR56'), '#cb_reparto');
                        return;
                    }
                    */

                    const pPassword = jQuery('#cb_privacy').val();
                    if (pPassword == "") {
                        customAlertifyAlert(Joomla.JText._('COM_PAYPAL_ACQUISTA_EVENTO_STR10'), '#password_utente');
                        return;
                    }

                    const pPrivacyCheck = document.getElementById("cb_privacy").checked;
                    if (!pPrivacyCheck) {
                        customAlertifyAlert('Devi accettare l\'informativa sulla privacy', '#cb_privacy');
                        return;
                    }

                    const pDatiImmaginiCheck = document.getElementById("cb_dtai_immagini").checked;

                    const pStatutoCheck = document.getElementById("cb_statuto").checked;
                    if (!pStatutoCheck) {
                        customAlertifyAlert('Devi accettare lo Statuto della società', '#cb_statuto');
                        return;
                    }

                    const pNewsletterCheck = document.getElementById('cb_newsletter').checked;
                    const pAccessoNutritionOnlineCheck = document.getElementById('cb_accessonutritiononline').checked;

                    // oggetto che crea coppie di valori fra i dati inputati e community builder
                    const pNomeUtenteID = jQuery('#cb_nome').attr("id");
                    const pNomeUtenteCB = jQuery('#cb_nome').attr("data-campo");
                    pPropArr.push({campo: pNomeUtenteID, cb: pNomeUtenteCB, value: pNomeUtente.toUpperCase()});

                    const pCognomeUtenteID = jQuery('#cb_cognome').attr("id");
                    const pCognomeUtenteCB = jQuery('#cb_cognome').attr("data-campo");
                    pPropArr.push({campo: pCognomeUtenteID, cb: pCognomeUtenteCB, value: pCognomeUtente.toUpperCase()});

                    pPropArr.push({campo: 'firstname', cb: 'firstname', value: pNomeUtente.toUpperCase()});
                    pPropArr.push({campo: 'lastname', cb: 'lastname', value: pCognomeUtente.toUpperCase()});

                    const pEmailID = jQuery('#email_utente').attr("id");
                    pPropArr.push({campo: pEmailID, cb: null, value: pEmail});

                    const pUsernameID = jQuery('#username').attr("id");
                    pPropArr.push({
                        campo: pUsernameID,
                        cb: null,
                        value: pUsername
                    });

                    const pPasswordID = jQuery('#password_utente').attr("id");
                    pPropArr.push({campo: pPasswordID, cb: null, value: pPassword});

                    const pAltraEmailID = jQuery('#cb_altraemail').attr("id");
                    pPropArr.push({campo: pAltraEmailID, cb: null, value: pAltraEmail});

                    const pTitoloID = jQuery('#cb_titolo').attr("id");
                    const pTitoloCB = jQuery('#cb_titolo').attr("data-campo");
                    const pTitoloIDRef = jQuery('#cb_titolo').attr("data-id-ref");
                    pPropArr.push({campo: pTitoloID, cb: pTitoloCB, value: pTitolo, is_id: pTitoloIDRef});

                    const pCfUtenteID = jQuery('#cb_codicefiscale').attr("id");
                    const pCfUtenteCB = jQuery('#cb_codicefiscale').attr("data-campo");
                    pPropArr.push({campo: pCfUtenteID, cb: pCfUtenteCB, value: pCfUtente.toUpperCase()});

                    const pDataNascitaID = jQuery('#cb_datadinascita').attr("id");
                    const pDataNascitaCB = jQuery('#cb_datadinascita').attr("data-campo");
                    pPropArr.push({campo: pDataNascitaID, cb: pDataNascitaCB, value: pDataNascita});

                    const pCittaNascitaID = jQuery('#cb_luogodinascita').attr("id");
                    const pCittaNascitaCB = jQuery('#cb_luogodinascita').attr("data-campo");
                    pPropArr.push({campo: pCittaNascitaID, cb: pCittaNascitaCB, value: pCittaNascita.toUpperCase()});

                    const pPvNascitaUtenteID = jQuery('#cb_provinciadinascita').attr("id");
                    const pPvNascitaUtenteCB = jQuery('#cb_provinciadinascita').attr("data-campo");
                    const pPvNascitaUtenteIDRef = jQuery('#cb_provinciadinascita').attr("data-id-ref");
                    pPropArr.push({campo: pPvNascitaUtenteID, cb: pPvNascitaUtenteCB, value: pPvNascitaUtente, is_id: pPvNascitaUtenteIDRef});

                    const pNazionalitaID = jQuery('#cb_nazionalita').attr("id");
                    const pNazionalitaCB = jQuery('#cb_nazionalita').attr("data-campo");
                    pPropArr.push({campo: pNazionalitaID, cb: pNazionalitaCB, value: pNazionalita.toUpperCase()});

                    const pTelefonoID = jQuery('#cb_telefono').attr("id");
                    const pTelefonoCB = jQuery('#cb_telefono').attr("data-campo");
                    pPropArr.push({campo: pTelefonoID, cb: pTelefonoCB, value: pTelefono});

                    const pIndirizzoID = jQuery('#cb_indirizzodiresidenza').attr("id");
                    const pIndirizzoCB = jQuery('#cb_indirizzodiresidenza').attr("data-campo");
                    pPropArr.push({campo: pIndirizzoID, cb: pIndirizzoCB, value: pIndirizzo});

                    const pPvUtenteID = jQuery('#cb_provdiresidenza').attr("id");
                    const pPvUtenteCB = jQuery('#cb_provdiresidenza').attr("data-campo");
                    const pPvUtenteIDRef = jQuery('#cb_provdiresidenza').attr("data-id-ref");
                    pPropArr.push({campo: pPvUtenteID, cb: pPvUtenteCB, value: pPvUtente, is_id: pPvUtenteIDRef});

                    const pCittaResidenzaID = jQuery('#cb_citta').attr("id");
                    const pCittaResidenzaCB = jQuery('#cb_citta').attr("data-campo");
                    pPropArr.push({campo: pCittaResidenzaID, cb: pCittaResidenzaCB, value: pCittaResidenza.toUpperCase()});

                    const pCapID = jQuery('#cb_cap').attr("id");
                    const pCapCB = jQuery('#cb_cap').attr("data-campo");
                    pPropArr.push({campo: pCapID, cb: pCapCB, value: pCap});

                    const pRegioneID = jQuery('#cb_regione').attr("id");
                    const pRegioneCB = jQuery('#cb_regione').attr("data-campo");
                    const pRegioneIDRef = jQuery('#cb_regione').attr("data-id-ref");
                    pPropArr.push({campo: pRegioneID, cb: pRegioneCB, value: pRegione, is_id: pRegioneIDRef});

                    const pLaureaInID = jQuery('#cb_laureain').attr("id");
                    const pLaureaInCB = jQuery('#cb_laureain').attr("data-campo");
                    const pLaureaInIDRef = jQuery('#cb_laureain').attr("data-id-ref");
                    pPropArr.push({campo: pLaureaInID, cb: pLaureaInCB, value: pLaureaIn, is_id: pLaureaInIDRef});

                    const pLaureAnnoID = jQuery('#cb_laureanno').attr("id");
                    const pLaureAnnoCB = jQuery('#cb_laureanno').attr("data-campo");
                    pPropArr.push({campo: pLaureAnnoID, cb: pLaureAnnoCB, value: pLaureAnno});

                    const pProfessioneDisciplinaID = jQuery('#cb_professionedisciplina').attr("id");
                    const pProfessioneDisciplinaCB = jQuery('#cb_professionedisciplina').attr("data-campo");
                    const pProfessioneDisciplinaIDRef = jQuery('#cb_professionedisciplina').attr("data-id-ref");
                    pPropArr.push({campo: pProfessioneDisciplinaID, cb: pProfessioneDisciplinaCB, value: pProfessioneDisciplina, is_id: pProfessioneDisciplinaIDRef});

                    const pOrdineID = jQuery('#cb_ordine').attr("id");
                    const pOrdineCB = jQuery('#cb_ordine').attr("data-campo");
                    const pOrdineIDRef = jQuery('#cb_ordine').attr("data-id-ref");
                    pPropArr.push({campo: pOrdineID, cb: pOrdineCB, value: pOrdine, is_id: pOrdineIDRef});

                    const pNumeroIscrizioneID = jQuery('#cb_numeroiscrizione').attr("id");
                    const pNumeroIscrizioneCB = jQuery('#cb_numeroiscrizione').attr("data-campo");
                    pPropArr.push({campo: pNumeroIscrizioneID, cb: pNumeroIscrizioneCB, value: pNumeroIscrizione});

                    /*
                    const pQualificaID = jQuery('#cb_qualifica').attr("id");
                    const pQualificaCB = jQuery('#cb_qualifica').attr("data-campo");
                    const pQualificaIDRef = jQuery('#cb_qualifica').attr("data-id-ref");
                    pPropArr.push({campo: pQualificaID, cb: pQualificaCB, value: pQualifica, is_id: pQualificaIDRef});
                    */

                    const pAziendaID = jQuery('#cb_azienda').attr("id");
                    const pAziendaCB = jQuery('#cb_azienda').attr("data-campo");
                    pPropArr.push({campo: pAziendaID, cb: pAziendaCB, value: pAzienda});

                    const pDipartimentoID = jQuery('#cb_dipartimento').attr("id");
                    const pDipartimentoCB = jQuery('#cb_dipartimento').attr("data-campo");
                    pPropArr.push({campo: pDipartimentoID, cb: pDipartimentoCB, value: pDipartimento});

                    const pRepartoID = jQuery('#cb_reparto').attr("id");
                    const pRepartoCB = jQuery('#cb_reparto').attr("data-campo");
                    pPropArr.push({campo: pRepartoID, cb: pRepartoCB, value: pReparto});

                    const pPrivacyCheckID = jQuery('#cb_privacy').attr("id");
                    const pPrivacyCheckCB = jQuery('#cb_privacy').attr("data-campo");
                    pPropArr.push({campo: pPrivacyCheckID, cb: pPrivacyCheckCB, value: pPrivacyCheck});

                    const pDatiImmaginiCheckID = jQuery('#cb_dtai_immagini').attr("id");
                    const pDatiImmaginiCheckCB = jQuery('#cb_dtai_immagini').attr("data-campo");
                    pPropArr.push({campo: pDatiImmaginiCheckID, cb: pDatiImmaginiCheckCB, value: pDatiImmaginiCheck});
                    
                    const pStatutoCheckID = jQuery('#cb_statuto').attr("id");
                    const pStatutoCheckCB = jQuery('#cb_statuto').attr("data-campo");
                    pPropArr.push({campo: pStatutoCheckID, cb: pStatutoCheckCB, value: pStatutoCheck});
                    
                    const pNewsletterCheckID = jQuery('#cb_newsletter').attr("id");
                    const pNewsletterCheckCB = jQuery('#cb_newsletter').attr("data-campo");
                    pPropArr.push({campo: pNewsletterCheckID, cb: pNewsletterCheckCB, value: pNewsletterCheck});

                    const pAccessoNutritionOnlineCheckID = jQuery('#cb_accessonutritiononline').attr("id");
                    const pAccessoNutritionOnlineCheckCB = jQuery('#cb_accessonutritiononline').attr("data-campo");
                    pPropArr.push({campo: pAccessoNutritionOnlineCheckID, cb: pAccessoNutritionOnlineCheckCB, value: pAccessoNutritionOnlineCheck});

                    pPropArr.push({campo: 'cb_professione_sinpe', cb: 'cb_professione_sinpe', value: Math.floor(Date.now() / 1000)});

                    const fileInput = document.querySelector('input[type="file"]');
                    if (fileInput.files.length > 0) {
                        const file = fileInput.files[0];
                        const base64String = await convertFileToBase64(file);
                        formObject['userImage'] = base64String;
                        // Append the file directly to a FormData object
                        //formData.append("cb_cv", file, file.name);
                    }

                    formObject['request_obj'] = pPropArr;

                    fetch('index.php?option=com_gglms&task=api.sinpeRegistrationAction', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify(formObject),
                    })
                    .then(response => response.json())
                    .then(data => {
                        
                        if (data.error) {
                            customAlertifyAlertSimple(data.error);
                            return;
                        }
                    
                        if (data.token == undefined || data.token == '') {
                            customAlertifyAlertSimple('Risposta del server malformata!');
                            return;
                        }

                        const pToken = data.token;
                        //console.log(pToken);
                        
                        window.location.href = "index.php?option=com_gglms&view=registrazionesinpe&action=user_registration_payment&pp=" + pToken;
                    })
                    .catch((error) => {
                        console.error('Errore:', error);
                        customAlertifyAlertSimple(error);
                    });



                });
            }



        });

        function checkEmail(email) {

            const emailFeedback = document.getElementById('emailFeedback');
            const emailFeedbackMsg = document.getElementById('emailFeedbackMsg');

            emailFeedback.classList.add('sr-only');
            emailFeedbackMsg.innerHTML = '';

            fetch('index.php?option=com_gglms&task=api.sinpeCheckEmail', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'email=' + encodeURIComponent(email)
            })
            .then(response => response.json())
            .then(data => {
                
                if (data.error) {
                    emailFeedback.classList.remove('sr-only');
                    emailFeedbackMsg.innerHTML = data.error;
                    return;
                }
            
            })
            .catch((error) => {
                customAlertifyAlertSimple(error);
            });

        }
        
        function checkUsername(username) {

            const usernameFeedback = document.getElementById('usernameFeedback');
            const usernameFeedbackMsg = document.getElementById('usernameFeedbackMsg');

            usernameFeedback.classList.add('sr-only');
            usernameFeedbackMsg.innerHTML = '';

            fetch('index.php?option=com_gglms&task=api.sinpeCheckUserName', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'username=' + encodeURIComponent(username)
            })
            .then(response => response.json())
            .then(data => {
                
                if (data.error) {
                    usernameFeedback.classList.remove('sr-only');
                    usernameFeedbackMsg.innerHTML = data.error;
                    return;
                }
            
            })
            .catch((error) => {
                customAlertifyAlertSimple(error);
            });

        }

        function checkCodiceFiscale(cf) {

            const codiceFiscaleFeedback = document.getElementById('codiceFiscaleFeedback');
            const codiceFiscaleFeedbackMsg = document.getElementById('codiceFiscaleFeedbackMsg');

            codiceFiscaleFeedback.classList.add('sr-only');
            codiceFiscaleFeedbackMsg.innerHTML = '';

            fetch('index.php?option=com_gglms&task=api.sinpeCheckCodiceFiscale', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'cf=' + encodeURIComponent(cf)
            })
            .then(response => response.json())
            .then(data => {
                
                if (data.error) {
                    codiceFiscaleFeedback.classList.remove('sr-only');
                    codiceFiscaleFeedbackMsg.innerHTML = data.error;
                    return;
                }
            
            })
            .catch((error) => {
                customAlertifyAlertSimple(error);
            });

        }
        function convertFileToBase64(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onloadend = () => {
                resolve(reader.result);
                };
                reader.onerror = reject;
                reader.readAsDataURL(file);
            });
        }

    </script>

    <?php elseif ($this->in_error == 1): 
        echo $this->payment_form;
        ?>
        
    <?php else : ?>

    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $this->client_id; ?>&currency=EUR" data-sdk-integration-source="button-factory"></script>

    <div class="container">

        <div class="row">
            <div class="col-12">
                <h4><span style="color: black; font-weight: bold"><?php echo JText::_('COM_PAYPAL_SINPE_STR1') ?></span></h4>
            </div>
        </div>

        <div class="row">
            <?php echo $this->payment_form; ?>
            <p id="descriptionError" style="display: none; color: red;">
                <?php echo JText::_('COM_PAYPAL_SINPE_STR2') ?>
            </p>
            <p  id="priceLabelError" style="display: none; color: red;">
                <?php echo JText::_('COM_PAYPAL_SINPE_STR3') ?>
            </p>
        </div>

        <?php
        // informazioni relative al bonifico ulteriori indicazioni
        echo $this->payment_extra_form;
        if($this->call_result == 'tuttook') {
            echo <<<HTML
            <script type="text/javascript">

              document.getElementById("myForm").style.display = "block";

            </script>
HTML;
        }
        ?>

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

            // pagamento con bonifico conferma
            jQuery('#btn-bonifico').on('click', function (e) {

                // disabilito bottone per evitare click multipli
                document.getElementById('btn-bonifico').disabled = true;

                var pHref = jQuery(this).attr("data-ref");
                window.location.href = pHref+ '&totale_sinpe=' + jQuery('#amount').val()
                    + '&totale_espen=' + jQuery('#amount_espen').val();
            });

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
                                            + '&pp=sinpe'
                                            + '&order_id=' + details.id
                                            + '&user_id=' + jQuery('#user_id').val()
                                            //+ '&gruppi_online=' + jQuery('#gruppi_online').val()
                                            //+ '&gruppi_moroso=' + jQuery('#gruppi_moroso').val()
                                            //+ '&gruppi_decaduto=' + jQuery('#gruppi_decaduto').val()
                                            + '&totale_sinpe=' + jQuery('#amount').val()
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

            jQuery('#anni_da_pagare').on('change', function (){
                jQuery(this).attr("checked", true);
            });

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

    <?php endif; ?>

</div>

