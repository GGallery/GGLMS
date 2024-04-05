<?php
/**
 * Created by IntelliJ IDEA.
 * User: Salma
 * Date: 05/12/2022
 * Time: 15:26
 */

// commentato per consentire l'accesso anche agli utenti non loggati
//defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.application.component.helper');

require_once JPATH_COMPONENT . '/controllers/paypal.php';
require_once JPATH_COMPONENT . '/models/users.php';

class gglmsViewRegistrazioneSinpe extends JViewLegacy {

    protected $client_id;
    protected $user_id;
    protected $in_error;
    protected $action;
    protected $hide_pp;
    protected $payment_form;
    protected $payment_extra_form;
    protected $show_view;
    protected $dp_lang;
    protected $_ret;
    protected $rf_registrazione;
    protected $request_obj;
    protected $ultimo_anno_pagato;
    protected $call_result;

    function display($tpl = null)
    {
        try {

            $bootstrap_dp = "";
            $this->dp_lang = "EN";
            $lang = JFactory::getLanguage();
            $lang_locale_arr = $lang->getLocale();
            if (isset($lang_locale_arr[4])
                && $lang_locale_arr[4] != ""
                && $lang_locale_arr[4] != "en") {
                $bootstrap_dp = 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.' . $lang_locale_arr[4] . '.min.js';
                $this->dp_lang = strtolower($lang_locale_arr[4]);
            }

            //JHtml::_('stylesheet', 'components/com_gglms/libraries/css/bootstrap.min.css');
            JHtml::_('stylesheet', "https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/css/bootstrap.min.css");
            JHtml::_('stylesheet', 'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css');
            JHtml::_('stylesheet', 'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css');
            JHtml::_('stylesheet', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');

            //JHtml::_('script', 'components/com_gglms/libraries/js/bootstrap.min.js');
            JHtml::_('script', "https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js");
            JHtml::_('script', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');

            if ($bootstrap_dp != "")
                JHtml::_('script', $bootstrap_dp);

            JHtml::_('script', 'https://kit.fontawesome.com/dee2e7c711.js');
            JHtml::_('script', 'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js');



            // se loggato impedisco la vista
            $_current_user = JFactory::getUser();
            if (isset($_current_user->id) && $_current_user->id > 0) throw new Exception("Non è possibile accedere alla pagina richiesta", E_USER_ERROR);

            // campi encoded dalla chiamata
            $this->action = JRequest::getVar('action', null);

            $pp = JRequest::getVar('pp', null);
            //$token = utilityHelper::build_randon_token();
            //$this->rf_registrazione = utilityHelper::build_encoded_link($token, 'registrazionesinpe', 'user_registration_request');

            $this->hide_pp = true;
            $this->in_error = 0;
            $_payment_form = null;
            $dt = new DateTime();

            if (is_null($this->action)) {

                // se l'utente non è loggato quindi o fa login oppure si deve registrare come un utente minimale soltanto per visionare il corso
                $_payment_form = outputHelper::get_user_registration_form_sinpe();
                if (!is_array($_payment_form)) throw new Exception($_payment_form, E_USER_ERROR);

                $this->payment_form = $_payment_form['success'];

            }
            else if ($this->action == 'bb_buy_request') {

                $decryptedToken = utilityHelper::decrypt_random_token($pp);
                if (is_null($decryptedToken) || !is_numeric($decryptedToken)) throw new Exception("I parametri non sono conformi, impossibile continuare", E_USER_ERROR);

                $this->user_id = $decryptedToken;

                $userModel = new gglmsModelUsers();
                $_user_details = $userModel->get_user_details_cb($this->user_id);
                if (!is_array($_user_details)) throw new Exception($_user_details, E_USER_ERROR);
                
                $totale_sinpe = JRequest::getVar('totale_sinpe');
                $totale_espen = JRequest::getVar('totale_espen');

                $_insert_quote = $userModel->insert_user_quote_stato_bonifico(
                    $this->user_id,
                    $dt->format('Y'),
                    $dt->format('Y-m-d H:i:s'),
                    $totale_sinpe,
                    $totale_espen);

                if (!is_array($_insert_quote)) throw new Exception($_insert_quote, E_USER_ERROR);
                else $this->payment_form = outputHelper::get_payment_form_error("La richiesta di pagamento tramite bonifico è stata registrata correttamente. La tua iscrizione sarà confermata successivamente al completamento della transazione.");

                $_params = utilityHelper::get_params_from_plugin();
                $email_default = utilityHelper::get_params_from_object($_params, "email_default");
                $selectedUser = $userModel->get_user_joomla($this->user_id);
                if (isset($selectedUser->email) && $selectedUser->email != '') {
                    utilityHelper::send_sinpe_email_pp($email_default,
                                                    date('Y-m-d'),
                                                    "",
                                                    "",
                                                    $_user_details,
                                                    0,
                                                    0,
                                                    'richiesta_bonifico_sinpe',
                                                    $selectedUser->email);
                }

            }
            else if ($this->action == 'user_registration_payment') {

                if (is_null($pp)
                    || !isset($pp)
                    || $pp == "")
                    throw new Exception("Nessun parametro definito", E_USER_ERROR);

                $decryptedToken = utilityHelper::decrypt_random_token($pp);
                if (is_null($decryptedToken) || !is_numeric($decryptedToken)) throw new Exception("I parametri non sono conformi, impossibile continuare", E_USER_ERROR);

                $this->hide_pp = false;

                $userModel = new gglmsModelUsers();
                $this->user_id = $decryptedToken;
                $_user_details = $userModel->get_user_details_cb($this->user_id);
                if (!is_array($_user_details)) throw new Exception($_user_details, E_USER_ERROR);

                $_config = new gglmsModelConfig();
                $this->client_id = $_config->getConfigValue('paypal_client_id');
                if (is_null($this->client_id) || $this->client_id == "") throw new Exception("Client ID di PayPal non valorizzato!", E_USER_ERROR);

                $_anno_corrente = $dt->format('Y');
                // se ultimo anno non è valorizzato richiedo il pagamento dell'anno corrente
                //$this->ultimo_anno_pagato = $_user_details['ultimo_anno_pagato'] > 0 ? $_user_details['ultimo_anno_pagato'] : ($_anno_corrente-1);  
                $this->ultimo_anno_pagato = $_anno_corrente-1;

                // devo verificare se è un biologo, in quel caso non deve vedere direttamente il pagamento
                if (strpos(strtolower($_user_details['tipo_laurea']), 'altra') !== false) {

                    $userGroupId = utilityHelper::check_usergroups_by_name("Preiscritto");
                    if (is_null($userGroupId)) throw new Exception("Non è stato trovato nessun usergroup valido", E_USER_ERROR);

                    $insert_ug = $userModel->insert_user_into_usergroup($this->user_id, $userGroupId);
                    if (is_null($insert_ug)) throw new Exception("Inserimento utente in gruppo corso fallito: " . $userGroupId . ", " . $userGroupId, E_USER_ERROR);

                    $this->payment_form = outputHelper::get_payment_form_error("La tua richiesta di adesione a SINPE è stata presa in carico.");
                    $this->in_error = 1;
                }
                else {
                    $_payment_form = outputHelper::get_payment_form_from_year($this->user_id,
                        $this->ultimo_anno_pagato,
                        $_anno_corrente,
                        $_user_details);

                    if (!is_array($_payment_form)) throw new Exception($_payment_form);
                    $this->payment_form = $_payment_form['success'];

                    // verifico se esiste l'indicazione per il metodo di pagamento alternativi
                    $_extra_pay = utilityHelper::get_params_from_plugin();
                    $this->payment_extra_form = outputHelper::get_payment_extra($_extra_pay, 'registrazionesinpe', $pp);

                    $this->call_result = 'tuttook';
                }

            }
            

        } catch (Exception $e){

            DEBUGG::log($e->getMessage() , 'registrazionesinpe', 0, 1, 0);

            //$this->payment_form = outputHelper::get_payment_form_error($e->getMessage());
            ?>
            <script>
                alert('<?php echo $e->getMessage();?>');
                history.back();
            </script>
            <?php
            $this->in_error = 1;

        }

        parent::display($tpl);

    }


}

