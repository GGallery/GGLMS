<?php
// no direct access

defined('_JEXEC') or die('Restricted access');

?>

<?php if ($this->eventSinpeCookie): ?>
    <div class="d-flex flex-row">

        <video id="video" width="640" height="360" preload="none" style="max-width: 100%" controls
                controlsList="nodownload"
                preload="auto" class="img-thumbnail">
            <source type="video/mp4" src="<?php echo $this->eventVideoLocation; ?>"/>

        </video>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            const player = new MediaElementPlayer('video', {
                autoplay: true,
                enableKeyboard: false,
            });
        });
    </script>

<?php else: ?>

<style>
    .btn-request , .btn-registrazione, .btn-bonifico {
        min-width: 50%;
        min-height: 50px;
        border-radius: 10px;
        font-size: 1.2em;
        background-color: rgba(85, 99, 143, 1);
        color: #FFFFFF
    }

    .form-check-input {
        appearance: auto !important;
    }
</style>

    <?php echo $this->_html; ?>

    <script>

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

        // form di registrazione
        if (jQuery('.btn-registrazione').length > 0)
            jQuery('.btn-registrazione').on('click', async function (e) {

                e.preventDefault();

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

                const tts = jQuery('#tts').val();
                const cc = jQuery('#cc').val();

                fetch('index.php?option=com_gglms&task=api.sinpeWatchWebinar', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        'cb_nome': pNomeUtente,
                        'cb_cognome': pCognomeUtente,
                        'email_utente': pEmail,
                        'cb_ordine': pOrdine,
                        'cb_numeroiscrizione': pNumeroIscrizione,
                        'tts': tts,
                    })
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
                    
                    window.location.href = "index.php?option=com_gglms&view=dettagliutente&layout=dettagliutente&template=eventshowing&cc=" + cc + "&pp=" + pToken;
                })
                .catch((error) => {
                    console.error('Errore:', error);
                    customAlertifyAlertSimple(error);
                });
                
            });
    </script>
        
<?php endif; ?>


