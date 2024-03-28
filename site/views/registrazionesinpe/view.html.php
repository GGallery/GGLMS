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


            // campi encoded dalla chiamata
            $this->action = JRequest::getVar('action', null);

            $pp = JRequest::getVar('pp', null);
            //$token = utilityHelper::build_randon_token();
            //$this->rf_registrazione = utilityHelper::build_encoded_link($token, 'registrazionesinpe', 'user_registration_request');

            $_params = utilityHelper::get_params_from_plugin();

            $this->hide_pp = true;
            $this->show_view = false;
            $_payment_form = null;

            if (is_null($this->action)) {

                // se l'utente non è loggato quindi o fa login oppure si deve registrare come un utente minimale soltanto per visionare il corso
                $_payment_form = outputHelper::get_user_registration_form_sinpe();

                if (!is_array($_payment_form)) throw new Exception($_payment_form, E_USER_ERROR);

                $this->in_error = 0;

            }
            else if ($this->action == 'user_registration_request') {
               
                /*
                $userGroupId = utilityHelper::check_usergroups_by_name("Registered");
                if (is_null($userGroupId))
                    throw new Exception("Non è stato trovato nessun usergroup valido", E_USER_ERROR);

                $insert_ug = $userModel->insert_user_into_usergroup($_new_user_id, $userGroupId);
                    if (is_null($insert_ug))
                        throw new Exception("Inserimento utente in gruppo corso fallito: " . $_new_user_id . ", " . $userGroupId, E_USER_ERROR);

                $this->_ret['success'] = "tuttook";
                $this->_ret['token'] = utilityHelper::build_randon_token($_new_user_id . "|==|" . $quota_associativa);

                echo json_encode($this->_ret);
                die();
                */

            }
            else if ($this->action == 'user_registration_payment') {

                if (is_null($pp)
                    || !isset($pp)
                    || $pp == "")
                    throw new Exception("Nessun parametro definito", E_USER_ERROR);

                $decryptedToken = utilityHelper::decrypt_random_token($pp);
                if (is_null($decryptedToken) || !is_numeric($decryptedToken)) throw new Exception("I parametri non sono conformi, impossibile continuare", E_USER_ERROR);


                $userModel = new gglmsModelUsers();
                $this->user_id = $decryptedToken;
                $_user_details = $userModel->get_user_details_cb($this->user_id);
                if (!is_array($_user_details)) throw new Exception($_user_details, E_USER_ERROR);

                $_config = new gglmsModelConfig();
                $this->client_id = $_config->getConfigValue('paypal_client_id');
                if (is_null($this->client_id)
                    || $this->client_id == "") throw new Exception("Client ID di PayPal non valorizzato!", E_USER_ERROR);

                $dt = new DateTime();
                $_anno_corrente = $dt->format('Y');
                // se ultimo anno non è valorizzato richiedo il pagamento dell'anno corrente
                $this->ultimo_anno_pagato = $_user_details['ultimo_anno_pagato'] > 0 ? $_user_details['ultimo_anno_pagato'] : ($_anno_corrente-1);  
                
                $_payment_form = outputHelper::get_payment_form_from_year($this->user_id,
                    $this->ultimo_anno_pagato,
                    $_anno_corrente,
                    $_user_details);

                if (!is_array($_payment_form)) throw new Exception($_payment_form);

                $this->payment_form = $_payment_form['success'];
                $this->in_error = 0;

                // verifico se esiste l'indicazione per il metodo di pagamento alternativi
                $_extra_pay = utilityHelper::get_params_from_plugin();
                $this->payment_extra_form = outputHelper::get_payment_extra($_extra_pay);

            }
            else if ($this->action == 'bb_buy_request') {

                if (is_null($pp)
                    || !isset($pp)
                    || $pp == "")
                    throw new Exception("Nessun parametro definito", E_USER_ERROR);

                $decryptedToken = utilityHelper::decrypt_random_token($pp);
                $parsedToken = explode("|==|", $decryptedToken);

                if (!is_array($parsedToken)) {
                    throw new Exception("I parametri non sono conformi, impossibile continuare", E_USER_ERROR);
                }

                $userId = $parsedToken[0];
                $requestedQuota = $parsedToken[1];
                $totaleQuota = $parsedToken[2];
                $dt = new DateTime();

                $userModel = new gglmsModelUsers();
                // controllo utente
                $checkUser = $userModel->get_user_joomla($userId);
                if (is_null($checkUser)
                    || !isset($checkUser->id)) {
                    $this->show_view = true;
                    throw new Exception("Nessun utente trovato", E_USER_ERROR);
                }

                // controllo il pagamento per l'utente e l'anno corrente
                $checkPagamento = $userModel->get_quota_user_anno($userId);
                if (!is_null($checkPagamento)) {
                    $this->show_view = true;
                    throw new Exception("Il pagamento della quota è già stato effettuato oppure è in attesa di conferma", E_USER_ERROR);
                }

                $dettagliUtente = $userModel->get_user_full_details_cb($userId);

                // l'integrazione dei campi extra al momento è soltanto per community builder
                $_config = new gglmsModelConfig();
                $dettagliUtente['nome_utente'] = $dettagliUtente[$_config->getConfigValue('campo_community_builder_nome')];
                $dettagliUtente['cognome_utente'] = $dettagliUtente[$_config->getConfigValue('campo_community_builder_cognome')];
                $dettagliUtente['codice_fiscale'] = $dettagliUtente[$_config->getConfigValue('campo_community_builder_controllo_cf')];
                $dettagliUtente['email'] = $checkUser->email;
                $dettagliUtente['mail_from'] = utilityHelper::get_ug_from_object($_params, 'email_from');
                $dettagliUtente['testo_pagamento_bonifico'] = utilityHelper::get_ug_from_object($_params, 'testo_pagamento_bonifico');

                $userGroupId = utilityHelper::check_usergroups_by_name($requestedQuota);
                if (is_null($userGroupId))
                    throw new Exception("Non è stato trovato nessun usergroup valido", E_USER_ERROR);

                $_insert_servizi_extra = $userModel->insert_user_servizi_extra($userId,
                                                                                $dt->format('Y'),
                                                                                $dt->format('Y-m-d H:i:s'),
                                                                                "",
                                                                                $totaleQuota,
                                                                                $dettagliUtente,
                                                                                'bb_buy_quota_asand',
                                                                                true,
                                                                                null,
                                                                                $userGroupId);
                if (!is_array($_insert_servizi_extra)) {
                    $this->show_view = true;
                    throw new Exception($_insert_servizi_extra, E_USER_ERROR);
                }

                $_payment_form = outputHelper::get_payment_form_acquisto_evento_bonifico($userId, "", $totaleQuota, $_params, true);

                if (!is_array($_payment_form)) {
                    $this->show_view = true;
                    throw new Exception($_payment_form, E_USER_ERROR);
                }

                $this->hide_pp = true;
                $this->in_error = 0;

            }

            $this->payment_form = $_payment_form['success'];


        } catch (Exception $e){

            DEBUGG::log($e->getMessage() , 'registrazionesinpe', 0, 1, 0);

            // echo senza mostrare la vista
            if (!$this->show_view) {
                $this->_ret['error'] = $e->getMessage();
                echo json_encode($this->_ret);
                die();
            }
            else {
                echo $e->getMessage();
                die();
            }

            //$this->in_error = 1;
        }

        parent::display($tpl);

    }


}

