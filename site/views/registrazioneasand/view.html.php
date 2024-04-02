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

class gglmsViewRegistrazioneAsand extends JViewLegacy {

    protected $client_id;
    protected $user_id;
    protected $unit_prezzo;
    protected $unit_id;
    protected $sconto_data;
    protected $sconto_custom;
    protected $in_groups;
    protected $sconto_particolare;
    protected $acquisto_webinar;
    protected $perc_webinar;
    protected $payment_form;
    protected $in_error;
    protected $action;
    protected $hide_pp;
    protected $show_view;
    protected $dp_lang;
    protected $_ret;
    protected $rf_registrazione;
    protected $quota_standard;
    protected $quota_studente;
    protected $request_obj;
    protected $percentuale_pp;
    protected $fisso_pp;
    protected $incremento_pp;
    protected $requested_quota;

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
            $this->request_obj = JRequest::getVar('request_obj', null);

            $pp = JRequest::getVar('pp', null);
            $voucherCode = JRequest::getVar('vv', null);
            $token = utilityHelper::build_randon_token();
            $this->rf_registrazione = utilityHelper::build_encoded_link($token, 'registrazioneasand', 'user_registration_request');

            $_params = utilityHelper::get_params_from_plugin('cb.checksociasand');

            $this->hide_pp = true;
            $this->show_view = false;
            $_payment_form = null;

            if (is_null($this->action)) {

                $this->quota_standard = utilityHelper::get_ug_from_object($_params, "quota_standard");
                $this->quota_studente = utilityHelper::get_ug_from_object($_params, "quota_studente");

                // se l'utente non è loggato quindi o fa login oppure si deve registrare come un utente minimale soltanto per visionare il corso
                $_payment_form = outputHelper::get_user_registration_form_asand($this->rf_registrazione, $this->quota_standard, $this->quota_studente);

                if (!is_array($_payment_form)) throw new Exception($_payment_form, E_USER_ERROR);

                $this->in_error = 0;

            }
            else if ($this->action == 'user_registration_request') {

                if (is_null($pp)
                    || !isset($pp)
                    || $pp == "")
                    throw new Exception("Nessun parametro definito", E_USER_ERROR);

                if (is_null($this->request_obj)
                    || !is_array($this->request_obj)
                    || count($this->request_obj) == 0) {
                    throw new Exception("Nessun oggetto valido per elaborare i dati di registrazione", E_USER_ERROR);
                }

                $dt = new DateTime();
                $_new_user = array();
                $_new_user_cp = array();

                $nome_utente = $cognome_utente = $cf_utente = $username = $email_utente = $password_utente = $quota_associativa = "";

                foreach ($this->request_obj as $sub_key => $sub_arr) {

                    if (isset($this->request_obj[$sub_key]['cb'])
                        && $this->request_obj[$sub_key]['cb'] == 'cb_nome') {
                        $nome_utente = preg_replace("/[^a-zA-Z]/", "", $this->request_obj[$sub_key]['value']);
                    }
                    else if (isset($this->request_obj[$sub_key]['cb'])
                        && $this->request_obj[$sub_key]['cb'] == 'cb_cognome') {
                        $cognome_utente = preg_replace("/[^a-zA-Z]/", "", $this->request_obj[$sub_key]['value']);
                    }
                    else if (isset($this->request_obj[$sub_key]['cb'])
                            && $this->request_obj[$sub_key]['cb'] == 'cb_codicefiscale') {
                        $cf_utente = strtoupper($this->request_obj[$sub_key]['value']);
                    }
                    else if (isset($this->request_obj[$sub_key]['campo'])
                        && $this->request_obj[$sub_key]['campo'] == 'username') {
                        $username = $this->request_obj[$sub_key]['value'];
                    }
                    else if (isset($this->request_obj[$sub_key]['campo'])
                        && $this->request_obj[$sub_key]['campo'] == 'email_utente') {
                        $email_utente = $this->request_obj[$sub_key]['value'];
                    }
                    else if (isset($this->request_obj[$sub_key]['campo'])
                        && $this->request_obj[$sub_key]['campo'] == 'password_utente') {
                        $password_utente = $this->request_obj[$sub_key]['value'];
                    }
                    else if (isset($this->request_obj[$sub_key]['campo'])
                        && $this->request_obj[$sub_key]['campo'] == 'data_nascita_utente') {
                        // format date artigianale
                        $_tmp_date = date("Y-m-d", strtotime(str_replace('/', '-', trim($this->request_obj[$sub_key]['value']))));
                        $this->request_obj[$sub_key]['value'] = $_tmp_date;
                    }
                    else if (isset($this->request_obj[$sub_key]['campo'])
                        && $this->request_obj[$sub_key]['campo'] == 'quota_associativa') {
                        $quota_associativa = $this->request_obj[$sub_key]['value'];
                    }

                    // campi cb

                    if (isset($this->request_obj[$sub_key]['cb'])
                        && $this->request_obj[$sub_key]['cb'] != ''
                        && isset($this->request_obj[$sub_key]['value'])) {

                        $cb_value = $this->request_obj[$sub_key]['value'];

                        // campi select
                        if (isset($this->request_obj[$sub_key]['is_id'])
                            && $this->request_obj[$sub_key]['is_id'] != '') {
                            $row_arr = utilityHelper::get_cb_fieldtitle_values($this->request_obj[$sub_key]['is_id'], $cb_value);
                            if (isset($row_arr['fieldtitle']))
                                $cb_value = $row_arr['fieldtitle'];
                        }

                        $_new_user_cp[$this->request_obj[$sub_key]['cb']] = addslashes($cb_value);

                    }

                }

                // controllo eventuali disparità fra titolo di studio e quota associativa
                if (
                    ($quota_associativa == "quota_studente" && strpos(strtolower($_new_user_cp['cb_titolo_studio']), "studente") === false)
                    ||
                    ($quota_associativa == "quota_standard" && strpos(strtolower($_new_user_cp['cb_titolo_studio']), "laurea") === false)
                    )
                    throw new Exception("La Quota associativa scelta non è conforme al Titolo di studio", E_USER_ERROR);

                // name e username prima lettera nome + cognome
                $_new_user['name'] = strtoupper(substr($nome_utente, 0, 1) . $cognome_utente);
                $_new_user['username'] = trim($username);
                $_new_user['email'] = trim($email_utente);
                $_new_user['password'] = $_user_value = JUserHelper::hashPassword($password_utente);
                $_new_user['block'] = 1;
                $_new_user['registerDate'] = $dt->format('Y-m-d H:i:s');
                //$_new_user['requireReset'] = 1;

                // controllo il codice fiscale
                $_cf_check = utilityHelper::conformita_cf($cf_utente);
                if (!isset($_cf_check['valido'])
                    || $_cf_check['valido'] != 1) {

                    $_err = "Problemi con il Codice fiscale";
                    if (isset($_cf_check['msg'])
                        && $_cf_check['msg'] != "")
                        $_err .= " " . $_cf_check['msg'];

                    throw new Exception($_err, E_USER_ERROR);
                }

                // controllo validità email
                if (!filter_var($_new_user['email'], FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("EMAIL NON VALIDA: " . $_new_user['email'], E_USER_ERROR);
                }

                // verifico l'esistenza delle colonne minimali per l'inserimento utente
                $_test_users_fields = utilityHelper::check_new_user_array($_new_user);
                if ($_test_users_fields != "") {
                    throw new Exception("Mancano dei campi nencessari alla creazione dell'utente: " . $_test_users_fields, E_USER_ERROR);
                }

                // controllo esistenza utente su username
                if (utilityHelper::check_user_by_username($_new_user['username'])) {
                    // aggiungo dei numeri randomici
                    $_new_user['username'] = $_new_user['username'] . rand(1, 999);
                }

                // controllo esistenza email utente
                $userModel = new gglmsModelUsers();
                //if (utilityHelper::check_user_by_column('email', $_new_user['email'])) {
                if ($emailCheck = utilityHelper::check_user_by_column_row('email', $_new_user['email'])) {

                    // controllo se l'utente è ancora bloccato
                    if ($emailCheck['block'] == 1) {
                        // la casistica si riferisce ad un utente che non ha completato il procedimento di registrazione
                        // gli permetto di completarlo
                        $annoRef = $dt->format('Y');

                        // prima controllo se ha un pagamento di tipo bonifico in sospeso
                        $checkQuota = $userModel->get_quota_per_id($emailCheck['id'], 'user_id', $annoRef);
                        if (isset($checkQuota['tipo_pagamento']))
                            throw New Exception('L\'utente con EMAIL ' . $emailCheck['email'] . ' ha un pagamento con bonifico in sospeso per l\'anno ' . $annoRef);

                        $checkToken = $userModel->get_registration_request($emailCheck['id']);
                        $siteMainUrl = utilityHelper::getSiteMainUrl(['asand', 'home']);

                        if (!is_null($checkToken)) {
                            throw new Exception('L\'utente con EMAIL ' . $emailCheck['email'] . ' &egrave; gi&agrave; registrato ma non &egrave; stato completato il pagamento della quota annuale ' . $annoRef . '. Puoi terminare il procedimento cliccando <a href=\''. $siteMainUrl . '/index.php?option=com_gglms&view=registrazioneasand&action=user_registration_payment&pp=' . $checkToken . '\'>QUI</a>', E_USER_ERROR);
                        }
                    }
                    else
                        throw new Exception("L'utente con EMAIL ". $_new_user['email'] . " &egrave; gi&agrave; attivo", E_USER_ERROR);

                    throw new Exception("EMAIL ESISTENTE: ". $_new_user['email'], E_USER_ERROR);
                }

                // inserimento utente
                $_user_insert_query = utilityHelper::get_insert_query("users", $_new_user);
                $_user_insert_query_result = utilityHelper::insert_new_with_query($_user_insert_query);

                if (!is_array($_user_insert_query_result)) {
                    throw new Exception("Inserimento utente fallito: " . $_user_insert_query_result, E_USER_ERROR);
                }

                $_new_user_id = $_user_insert_query_result['success'];
                // riferimento id per CP
                $_new_user_cp['id'] = $_new_user_id;
                $_new_user_cp['user_id'] = $_new_user_id;

                // inserimento utente in CP
                $_cp_insert_query = utilityHelper::get_insert_query("comprofiler", $_new_user_cp);
                $_cp_insert_query_result = utilityHelper::insert_new_with_query($_cp_insert_query);
                if (!is_array($_cp_insert_query_result)) throw new Exception(print_r($_new_user_cp, true) . " errore durante inserimento", E_USER_ERROR);

                $userGroupId = utilityHelper::check_usergroups_by_name("Registered");
                if (is_null($userGroupId)) throw new Exception("Non è stato trovato nessun usergroup valido", E_USER_ERROR);

                $insert_ug = $userModel->insert_user_into_usergroup($_new_user_id, $userGroupId);
                if (is_null($insert_ug)) throw new Exception("Inserimento utente in gruppo corso fallito: " . $_new_user_id . ", " . $userGroupId, E_USER_ERROR);

                $this->_ret['success'] = "tuttook";
                $this->_ret['token'] = utilityHelper::build_randon_token($_new_user_id . "|==|" . $quota_associativa);

                echo json_encode($this->_ret);
                die();

            }
            else if ($this->action == 'user_registration_payment') {

                if (is_null($pp)
                    || !isset($pp)
                    || $pp == "")
                    throw new Exception("Nessun parametro definito", E_USER_ERROR);

                $decryptedToken = utilityHelper::decrypt_random_token($pp);
                $parsedToken = explode("|==|", $decryptedToken);

                if (!is_array($parsedToken)) throw new Exception("I parametri non sono conformi, impossibile continuare", E_USER_ERROR);

                $_config = new gglmsModelConfig();
                $this->client_id = $_config->getConfigValue('paypal_client_id');
                if (is_null($this->client_id)
                    || $this->client_id == "")
                    throw new Exception("Client ID di PayPal non valorizzato!", E_USER_ERROR);

                $userId = $parsedToken[0];
                $requestedQuota = $parsedToken[1];

                if (!is_numeric($userId)) throw new Exception("Id utente non numerico!", E_USER_ERROR);

                $userModel = new gglmsModelUsers();
                // controllo utente
                $checkUser = $userModel->get_user_joomla($userId);
                if (is_null($checkUser)
                    || !isset($checkUser->id))
                    throw new Exception("Nessun utente trovato", E_USER_ERROR);

                $dt = new DateTime();
                $this->requested_quota = utilityHelper::get_ug_from_object($_params, $requestedQuota);
                $this->percentuale_pp = utilityHelper::get_ug_from_object($_params, 'percentuale_pp');
                $this->fisso_pp = utilityHelper::get_ug_from_object($_params, 'fisso_pp');
                // incremento PayPal
                $ppAmount = utilityHelper::percentageFromNumber($this->percentuale_pp, $this->requested_quota);
                $this->incremento_pp = $ppAmount+$this->fisso_pp;

                // user id, quota richiesta, quota richiesta in euro, incremento spese paypal
                $newToken = utilityHelper::build_randon_token($userId . "|==|" . $parsedToken[1] . "|==|" . $this->requested_quota . "|==|" . $this->incremento_pp);

                // inserimento della richiesta di pagamento a database
                $newReq['user_id'] = $userId;
                $newReq['token'] = $newToken;
                $annoCorrente = $dt->format('Y');
                //$newReq['date'] = $dt->format('Y-m-d H:i:s');

                // verifico se esiste già un richiesta di pagamento con questo token
                $checkToken = $userModel->get_quota_per_user_token($userId, $newToken, $annoCorrente);
                if (!is_null($checkToken)) throw new Exception("Esiste già una richiesta di pagamento, impossibile continuare", E_USER_ERROR);

                // se il token non esiste memorizzo la richiesta
                //$checkToken = $userModel->get_registration_request($userId, $newToken);

                //if (is_null($checkToken)) {
                $reqPagamentoQuery = utilityHelper::get_insert_query("gg_registration_request", $newReq);
                $reqPagamentoInsertResult = utilityHelper::insert_new_with_query($reqPagamentoQuery);

                if (!is_array($reqPagamentoInsertResult)) throw new Exception("Inserimento riferimento token pagamento fallito: " . $reqPagamentoInsertResult, E_USER_ERROR);
                //}

                $_payment_form = outputHelper::get_payment_form_quota_asand($userId, $this->requested_quota, $this->incremento_pp, $parsedToken[1], $_params, $newToken);

                if (!is_array($_payment_form)) throw new Exception($_payment_form, E_USER_ERROR);

                $this->hide_pp = false;
                $this->in_error = 0;

            }
            else if ($this->action == 'voucher_buy_request') {

                if (is_null($pp)
                    || !isset($pp)
                    || $pp == "")
                    throw new Exception("Nessun parametro definito", E_USER_ERROR);

                if (is_null($voucherCode)
                    || !isset($voucherCode)
                    || $voucherCode == "")
                    throw new Exception("Nessun voucher definito", E_USER_ERROR);

                $decryptedToken = utilityHelper::decrypt_random_token($pp);
                $parsedToken = explode("|==|", $decryptedToken);

                if (!is_array($parsedToken)) throw new Exception("I parametri non sono conformi, impossibile continuare", E_USER_ERROR);

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

                $annoCorrente = $dt->format('Y');
                $dateTimeCorrente = $dt->format('Y-m-d H:i:s');
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
                if (is_null($userGroupId)) throw new Exception("Non è stato trovato nessun usergroup valido", E_USER_ERROR);

                $_insert_servizi_extra = $userModel->insert_user_servizi_extra($userId,
                                            $annoCorrente,
                                            $dateTimeCorrente,
                                            "",
                                            $totaleQuota,
                                            $dettagliUtente,
                                            'voucher_buy_quota_asand',
                                            true,
                                            null,
                                            $userGroupId);

                if (!is_array($_insert_servizi_extra)) {
                    $this->show_view = true;
                    throw new Exception($_insert_servizi_extra, E_USER_ERROR);
                }

                $insert_ug = $userModel->insert_user_into_usergroup($userId, $userGroupId);
                if (is_null($insert_ug))throw new Exception("Inserimento utente in gruppo corso fallito: " . $userId . ", " . $userGroupId, E_USER_ERROR);


                // aggiorno ultimo anno pagato
                $_ultimo_anno = $userModel->update_ultimo_anno_pagato($userId, $annoCorrente);
                if (!is_array($_ultimo_anno))
                    throw new Exception($_ultimo_anno, E_USER_ERROR);

                // sblocco l'utente
                $updateUser = $userModel->update_user_column($userId, "block", 0);

                if (is_null($updateUser))
                    throw new Exception("Errore durante l'aggiornamento dell'utente", E_USER_ERROR);

                // aggiorno il voucher
                $updateVoucher = $userModel->update_voucher_utilizzato($voucherCode, $userId, $dateTimeCorrente);
                if (!is_array($updateVoucher))
                    throw new Exception($updateVoucher, E_USER_ERROR);

                $_payment_form = outputHelper::get_result_view($this->action, "tuttook", null, $_insert_servizi_extra['last_quota'], true);

                $this->hide_pp = true;
                $this->in_error = 0;

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
                if (is_null($userGroupId)) throw new Exception("Non è stato trovato nessun usergroup valido", E_USER_ERROR);

                // per sicurezza rimuovo dal gruppo della quota
                $ug_quota = !is_array($userGroupId) ? (array) $userGroupId : $userGroupId;
                utilityHelper::remove_user_from_usergroup($userId, $ug_quota);

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

            DEBUGG::log($e->getMessage() , 'registrazioneasand', 0, 1, 0);

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

