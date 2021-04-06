<?php

/**
 * @version		1
 * @package		webtv
 * @author 		antonio
 * @author mail	tony@bslt.it
 * @link
 * @copyright	Copyright (C) 2011 antonio - All rights reserved.
 * @license		GNU/GPL
 */

// commentato per consentire l'accesso anche agli utenti non loggati
//defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.application.component.helper');

require_once JPATH_COMPONENT . '/controllers/paypal.php';
require_once JPATH_COMPONENT . '/models/users.php';

class gglmsViewAcquistaEvento extends JViewLegacy {

    protected $client_id;
    protected $user_id;
    protected $unit_prezzo;
    protected $unit_id;
    protected $sconto_data;
    protected $sconto_custom;
    protected $in_groups;
    protected $payment_form;
    protected $in_error;
    protected $action;
    protected $hide_pp;
    protected $show_view;
    protected $dp_lang;
    protected $_ret;

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

            JHtml::_('stylesheet', 'components/com_gglms/libraries/css/bootstrap.min.css');
            JHtml::_('stylesheet', 'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css');
            JHtml::_('stylesheet', 'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css');
            JHtml::_('stylesheet', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');

            JHtml::_('script', 'components/com_gglms/libraries/js/bootstrap.min.js');
            JHtml::_('script', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');

            if ($bootstrap_dp != "")
                JHtml::_('script', $bootstrap_dp);

            JHtml::_('script', 'https://kit.fontawesome.com/dee2e7c711.js');
            JHtml::_('script', 'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js');



            // campi encoded dalla chiamata
            $this->action = JRequest::getVar('action');
            $pp = JRequest::getVar('pp');

            // chi o cosa mi sta chiamando
            if (!isset($this->action)
                || $this->action == "")
                throw new Exception("Nessuna azione richiesta", 1);

            if (!isset($pp)
                || $pp == "")
                throw new Exception("Nessun parametro definito", 1);

            $dt = new DateTime();
            // semaforo per la visualizzazione degli errori al lancio di eccezioni
            $this->show_view = true;

            // decodifica dell'attributo pp
            $decode_pp = UtilityHelper::encrypt_decrypt('decrypt', $pp, 'GGallery00!', 'GGallery00!');
            $decode_arr = explode('|==|', $decode_pp);

            // precarico i params del modulo
            $_params = UtilityHelper::get_params_from_module();

            // controllo se ci sono tutti gli elementi
            if (count($decode_arr) < 6)
                throw new Exception("La richiesta effettuta non può essere evasa in quanto incompleta", 1);

            if (!isset($decode_arr[0])
                || $decode_arr[0] == "")
                throw new Exception("Prezzo non disponibile", 1);

            if (!isset($decode_arr[1])
                || $decode_arr[1] == ""
                || filter_var($decode_arr[1], FILTER_VALIDATE_INT) === false)
                throw new Exception("Nessun identificativo evento", 1);

            if (!isset($decode_arr[2])
                || $decode_arr[2] == ""
                || filter_var($decode_arr[2], FILTER_VALIDATE_INT) === false)
                throw new Exception("Nessun utente specificato", 1);

            if (!isset($decode_arr[3])
                || $decode_arr[3] == "")
                throw new Exception("Nessuno sconto indicato", 1);

            if (!isset($decode_arr[4]))
                throw new Exception("Nessuno sconto custom indicato", 1);

            if (!isset($decode_arr[5])
                || $decode_arr[5] == ""
                || filter_var($decode_arr[5], FILTER_VALIDATE_INT) === false)
                throw new Exception("Missing in groups", 1);


            $this->unit_prezzo = $decode_arr[0];
            $this->unit_id = $decode_arr[1];
            $this->user_id = $decode_arr[2];
            $this->sconto_data = $decode_arr[3];
            $this->sconto_custom = $decode_arr[4];
            $this->in_groups = $decode_arr[5];

            $_config = new gglmsModelConfig();

            // provengo dal modulo cliccando sul pulsante Acquista
            if ($this->action == 'buy') {
                $this->hide_pp = false;

                // se l'utente non è loggato quindi o fa login oppure si deve registrare come un utente minimale soltanto per visionare il corso
                if ($this->user_id == 0) {

                    $this->action == 'user_action_request';
                    $this->hide_pp = true;

                    $_payment_form = outputHelper::get_user_action_request_form_acquisto_evento($this->unit_prezzo,
                                                                                                $this->unit_id,
                                                                                                $this->user_id,
                                                                                                $this->sconto_data,
                                                                                                $this->sconto_custom,
                                                                                                $this->in_groups,
                                                                                                $_params);

                    if (!is_array($_payment_form))
                        throw new Exception($_payment_form);

                    $this->payment_form = $_payment_form['success'];
                    $this->in_error = 0;

                } else { // lo invio al modulo di acquisto paypal / bonifico

                    $_current_user = JFactory::getUser();
                    if ($this->user_id != $_current_user->id)
                        throw new Exception("L'utente corrente è diverso da quello che ha richiesto la transazione", 1);

                    $this->client_id = $_config->getConfigValue('paypal_client_id');
                    if (is_null($this->client_id)
                        || $this->client_id == "")
                        throw new Exception("Client ID di PayPal non valorizzato!", 1);

                    // devo controlla se l'utente ha già richiesto l'evento
                    // mi servono informazioni sull'unita
                    $unit_model = new gglmsModelUnita();
                    $_unit = $unit_model->getUnita($this->unit_id);
                    $unit_gruppo = $unit_model->get_id_gruppo_unit($this->unit_id);
                    $_already_request = utilityHelper::get_acquisto_evento_richiesto($this->user_id, $unit_gruppo);
                    if ($_already_request)
                        throw new Exception("Evento acquistato oppure in attesa del bonifico", 1);

                    $_payment_form = outputHelper::get_payment_form_acquisto_evento($this->unit_prezzo,
                                                                                    $this->unit_id,
                                                                                    $this->user_id,
                                                                                    $this->sconto_data,
                                                                                    $this->sconto_custom,
                                                                                    $this->in_groups,
                                                                                    $_params);

                    if (!is_array($_payment_form))
                        throw new Exception($_payment_form);

                    $this->payment_form = $_payment_form['success'];
                    $this->in_error = 0;
                }
            }
            else if ($this->action == 'bb_buy_request') { // l'utente vuole pagare con bonifico

                // mi servono informazioni sull'unita
                $unit_model = new gglmsModelUnita();
                $_unit = $unit_model->getUnita($this->unit_id);
                $unit_gruppo = $unit_model->get_id_gruppo_unit($this->unit_id);


                // user model
                $_user_quote = new gglmsModelUsers();
                $_insert_servizi_extra = $_user_quote->insert_user_servizi_extra($this->user_id,
                                                                                $dt->format('Y'),
                                                                                $dt->format('Y-m-d H:i:s'),
                                                                                "",
                                                                                $this->unit_prezzo,
                                                                                array(),
                                                                                $this->action,
                                                                                true,
                                                                                $this->unit_id,
                                                                                $unit_gruppo);

                if (!is_array($_insert_servizi_extra))
                    throw new Exception($_insert_servizi_extra, 1);

                $_payment_form = outputHelper::get_payment_form_acquisto_evento_bonifico($this->user_id,
                                                                                        $_unit->titolo,
                                                                                        $this->unit_prezzo,
                                                                                        $_params);

                if (!is_array($_payment_form))
                    throw new Exception($_payment_form, 1);

                $this->payment_form = $_payment_form['success'];
                $this->in_error = 0;

            }
            else if ($this->action == 'new_user_request') { // registrazione di un nuovo utente solo per visione evento

                $this->hide_pp = true;
                $_payment_form = outputHelper::get_user_registration_form_acquisto_evento($this->unit_prezzo,
                                                                                            $this->unit_id,
                                                                                            $this->user_id,
                                                                                            $this->sconto_data,
                                                                                            $this->sconto_custom,
                                                                                            $this->in_groups,
                                                                                            $_params);

                if (!is_array($_payment_form))
                    throw new Exception($_payment_form, 1);

                $this->payment_form = $_payment_form['success'];
                $this->in_error = 0;

            }
            else if ($this->action == 'user_registration_request') {

                $this->_ret = array();
                $this->show_view = false;

                $request_obj = JRequest::getVar('request_obj');
                if (!isset($request_obj)
                    || !is_array($request_obj)
                    || count($request_obj) == 0) {
                    throw new Exception("Nessun oggetto valido per elaborare i dati di registrazione", 1);
                }


                // i campi necessari per l'inserimento nella tabella users
                $_new_user = array();
                $_new_user_cp = array();
                $nome_utente = null;
                $cognome_utente = null;
                $email_utente = null;
                $password_utente = null;
                $cf_utente = null;

                foreach ($request_obj as $sub_key => $sub_arr) {

                    if (isset($request_obj[$sub_key]['cb'])
                        && $request_obj[$sub_key]['cb'] == 'cb_nome') {
                        $nome_utente = preg_replace("/[^a-zA-Z]/", "", $request_obj[$sub_key]['value']);
                    }
                    else if (isset($request_obj[$sub_key]['cb'])
                        && $request_obj[$sub_key]['cb'] == 'cb_cognome') {
                        $cognome_utente = preg_replace("/[^a-zA-Z]/", "", $request_obj[$sub_key]['value']);
                    }
                    else if (isset($request_obj[$sub_key]['cb'])
                            && $request_obj[$sub_key]['cb'] == 'cb_codicefiscale') {
                        $cf_utente = $request_obj[$sub_key]['value'];
                    }
                    else if (isset($request_obj[$sub_key]['campo'])
                        && $request_obj[$sub_key]['campo'] == 'email_utente') {
                        $email_utente = $request_obj[$sub_key]['value'];
                    }
                    else if (isset($request_obj[$sub_key]['campo'])
                        && $request_obj[$sub_key]['campo'] == 'password_utente') {
                        $password_utente = $request_obj[$sub_key]['value'];
                    }
                    else if (isset($request_obj[$sub_key]['campo'])
                        && $request_obj[$sub_key]['campo'] == 'data_nascita_utente') {
                        // format date artigianale
                        $_tmp_date = date("Y-m-d", strtotime(str_replace('/', '-', trim($request_obj[$sub_key]['value']))));
                        $request_obj[$sub_key]['value'] = $_tmp_date;
                    }

                    // campi cb

                    if (isset($request_obj[$sub_key]['cb'])
                        && $request_obj[$sub_key]['cb'] != ''
                        && isset($request_obj[$sub_key]['value'])) {

                        $cb_value = $request_obj[$sub_key]['value'];

                        // campi select
                        if (isset($request_obj[$sub_key]['is_id'])
                            && $request_obj[$sub_key]['is_id'] != '') {
                            $row_arr = utilityHelper::get_cb_fieldtitle_values($request_obj[$sub_key]['is_id'], $cb_value);
                            if (isset($row_arr['fieldtitle']))
                                $cb_value = $row_arr['fieldtitle'];
                        }

                        $_new_user_cp[$request_obj[$sub_key]['cb']] = addslashes($cb_value);

                    }

                }

                // name e username prima lettera nome + cognome
                $_new_user['name'] = strtoupper(substr($nome_utente, 0, 1) . $cognome_utente);
                $_new_user['username'] = $_new_user['name'];
                $_new_user['email'] = trim($email_utente);
                $_new_user['password'] = $_user_value = JUserHelper::hashPassword($password_utente);

                // controllo il codice fiscale
                $_cf_check = UtilityHelper::conformita_cf($cf_utente);
                if (!isset($_cf_check['valido'])
                    || $_cf_check['valido'] != 1) {

                    $_err = "Problemi con il Codice fiscale";
                    if (isset($_cf_check['msg'])
                        && $_cf_check['msg'] != "")
                        $_err .= " " . $_cf_check['msg'];

                    throw new Exception($_err, 1);
                }

                // controllo validità email
                if (!filter_var($_new_user['email'], FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("EMAIL NON VALIDA: " . $_new_user['email'], 1);
                }

                // verifico l'esistenza delle colonne minimali per l'inserimento utente
                $_test_users_fields = UtilityHelper::check_new_user_array($_new_user);
                if ($_test_users_fields != "") {
                    throw new Exception("Mancano dei campi nencessari alla creazione dell'utente: " . $_test_users_fields, 1);
                }

                // controllo esistenza utente su username
                if (UtilityHelper::check_user_by_username($_new_user['username'])) {
                    //throw new Exception("USERNAME ESISTENTE: ". $_new_user['username'], 1);
                    // aggiungo dei numeri randomici
                    $_new_user['username'] = $_new_user['username'] . rand(1, 999);
                }

                // controllo esistenza email utente
                if (UtilityHelper::check_user_by_column('email', $_new_user['email'])) {
                    throw new Exception("EMAIL ESISTENTE: ". $_new_user['email'], 1);
                }

                // inserimento utente
                $_user_insert_query = UtilityHelper::get_insert_query("users", $_new_user);
                $_user_insert_query_result = UtilityHelper::insert_new_with_query($_user_insert_query);

                if (!is_array($_user_insert_query_result)) {
                    throw new Exception("Inserimento utente fallito: " . $_user_insert_query_result, 1);
                }

                $_new_user_id = $_user_insert_query_result['success'];

                // associo utente a gruppi
                $ug_destinazione = UtilityHelper::get_ug_from_object($_params, "ug_nonsocio", true);
                $ug_destinazione = !is_array($ug_destinazione) ? (array) $ug_destinazione : $ug_destinazione;
                JUserHelper::setUserGroups($_new_user_id, $ug_destinazione);

                // riferimento id per CP
                $_new_user_cp['id'] = $_new_user_id;
                $_new_user_cp['user_id'] = $_new_user_id;

                // inserimento utente in CP
                $_cp_insert_query = UtilityHelper::get_insert_query("comprofiler", $_new_user_cp);
                $_cp_insert_query_result = UtilityHelper::insert_new_with_query($_cp_insert_query);
                if (!is_array($_cp_insert_query_result))
                    throw new Exception(print_r($_new_user_cp, true) . " errore durante inserimento", 1);

                // invio email registrazione
                $_email_from = UtilityHelper::get_params_from_object($_params, 'email_from');
                $_email_to  = UtilityHelper::get_params_from_object($_params, 'email_default');
                $_event_title = "";

                // mi servono informazioni sull'unita
                if (isset($this->unit_id)
                    && $this->unit_id != ""
                    && $this->unit_id > 0) {
                    $unit_model = new gglmsModelUnita();
                    $_unit = $unit_model->getUnita($this->unit_id);
                    $_event_title = $_unit->titolo;
                }

                $_send_email = UtilityHelper::send_acquisto_evento_email_new_user($_email_to,
                                                                                $_event_title,
                                                                                $_new_user['name'],
                                                                                $_new_user['username'],
                                                                                $_new_user['email'],
                                                                                $password_utente,
                                                                                $_email_from);

                $this->_ret['success'] = "tuttook";
                echo json_encode($this->_ret);
                die();
            }
            else if ($this->action == 'user_registration_confirm') { // la registrazione dell'utente è andata a buon fine

                $this->hide_pp = true;

                $_payment_form = outputHelper::get_user_registration_confirm_acquisto_evento($this->unit_prezzo,
                                                                                            $this->unit_id,
                                                                                            $this->user_id,
                                                                                            $this->sconto_data,
                                                                                            $this->in_groups,
                                                                                            $_params);

                if (!is_array($_payment_form))
                    throw new Exception($_payment_form);

                $this->payment_form = $_payment_form['success'];
                $this->in_error = 0;

            }
            else if ($this->action == 'user_insert_confirm_group_sponsor_evento') {

                $this->hide_pp = true;

                $unit_model = new gglmsModelUnita();
                $_unit = $unit_model->getUnita($this->unit_id);
                $_event_title = $_unit->titolo;

                $_payment_form = OutputHelper::get_user_insert_confirm_group_sponsor_evento($_event_title);
                if (!is_array($_payment_form))
                    throw new Exception($_payment_form);

                $this->payment_form = $_payment_form['success'];
                $this->in_error = 0;
            }
            else if ($this->action == 'user_insert_group_sponsor_evento') {

                $this->hide_pp = true;
                $this->show_view = false;

                $request_obj = JRequest::getVar('request_obj');
                if (!isset($request_obj)
                    || !is_array($request_obj)
                    || count($request_obj) == 0
                    || !isset($request_obj[0]['username'])
                    || !isset($request_obj[0]['password_utente'])) {
                    throw new Exception("Nessun oggetto valido per elaborare i dati di registrazione", 1);
                }

                $user_model  = new gglmsModelUsers();
                $_check_user = $user_model->check_user(trim($request_obj[0]['username']), trim($request_obj[0]['password_utente']));

                if (!is_array($_check_user)
                    || !isset($_check_user['success']))
                    throw new Exception($_check_user, 1);

                $this->user_id = $_check_user['success'];

                $unit_model = new gglmsModelUnita();
                $_unit = $unit_model->getUnita($this->unit_id);

                // controllo esistenza evento
                $_check_evento = $_unit->find_corso_pubblicato($this->unit_id);
                if (!is_array($_check_evento))
                    throw new Exception($_check_evento, 1);

                $unit_gruppo = $unit_model->get_id_gruppo_unit($this->unit_id);

                // controllo se l'utente è già nel gruppo corso
                $_already_request = utilityHelper::check_user_into_ug($this->user_id, (array) $unit_gruppo);
                if ($_already_request)
                    throw new Exception("Sei già registrato all'evento selezionato", 1);

                $_insert_ug = UtilityHelper::set_usergroup_generic($this->user_id, $unit_gruppo);

                if (!is_array($_insert_ug))
                    throw new Exception($_insert_ug, 1);

                $this->_ret['success'] = "tuttook";
                echo json_encode($this->_ret);
                die();
            }
            else if ($this->action == 'user_login_form_sponsor_evento') {

                $this->hide_pp = true;

                // controllo esistenza evento
                $_unit = new gglmsModelUnita();
                $_check_evento = $_unit->find_corso_pubblicato($this->unit_id);
                if (!is_array($_check_evento))
                    throw new Exception($_check_evento, 1);

                $_payment_form = OutputHelper::get_user_login_form_sponsor_evento($this->unit_id);
                if (!is_array($_payment_form))
                    throw new Exception($_payment_form);

                $this->payment_form = $_payment_form['success'];
                $this->in_error = 0;

            }
            // form di registrazione utente a evento per sponsor
            else if ($this->action == 'user_registration_form_sponsor_evento') {

                $this->hide_pp = true;

                $_payment_form = OutputHelper::get_user_registration_form_sponsor_evento($_params,
                                                                                        $this->unit_id);
                if (!is_array($_payment_form))
                    throw new Exception($_payment_form);

                $this->payment_form = $_payment_form['success'];
                $this->in_error = 0;

            }
            // registrazione utente a evento per sponsor
            else if ($this->action == 'user_registration_sponsor_request') {

                $this->_ret = array();
                $this->show_view = false;

                $request_obj = JRequest::getVar('request_obj');
                if (!isset($request_obj)
                    || !is_array($request_obj)
                    || count($request_obj) == 0) {
                    throw new Exception("Nessun oggetto valido per elaborare i dati di registrazione", 1);
                }

                // i campi necessari per l'inserimento nella tabella users
                $_new_user = array();
                $_new_user_cp = array();
                $nome_utente = null;
                $cognome_utente = null;
                $username = null;
                $email_utente = null;
                $password_utente = null;
                $cf_utente = null;
                $id_evento = null;
                $_event_title = "";

                foreach ($request_obj as $sub_key => $sub_arr) {

                    if (isset($request_obj[$sub_key]['cb'])
                        && $request_obj[$sub_key]['cb'] == 'cb_nome') {
                        $nome_utente = preg_replace("/[^a-zA-Z]/", "", $request_obj[$sub_key]['value']);
                    }
                    else if (isset($request_obj[$sub_key]['cb'])
                        && $request_obj[$sub_key]['cb'] == 'cb_cognome') {
                        $cognome_utente = preg_replace("/[^a-zA-Z]/", "", $request_obj[$sub_key]['value']);
                    }
                    else if (isset($request_obj[$sub_key]['cb'])
                        && $request_obj[$sub_key]['cb'] == 'cb_codicefiscale') {
                        $cf_utente = $request_obj[$sub_key]['value'];
                    }
                    else if (isset($request_obj[$sub_key]['campo'])
                        && $request_obj[$sub_key]['campo'] == 'username') {
                        $username = preg_replace("/[^A-Za-z0-9 ]/", "", $request_obj[$sub_key]['value']);
                    }
                    else if (isset($request_obj[$sub_key]['campo'])
                        && $request_obj[$sub_key]['campo'] == 'email_utente') {
                        $email_utente = $request_obj[$sub_key]['value'];
                    }
                    else if (isset($request_obj[$sub_key]['campo'])
                        && $request_obj[$sub_key]['campo'] == 'id_evento') {
                        $id_evento = (int) $request_obj[$sub_key]['value'];
                    }
                    else if (isset($request_obj[$sub_key]['campo'])
                        && $request_obj[$sub_key]['campo'] == 'password_utente') {
                        $password_utente = $request_obj[$sub_key]['value'];
                    }
                    else if (isset($request_obj[$sub_key]['campo'])
                        && $request_obj[$sub_key]['campo'] == 'data_nascita_utente') {
                        // format date artigianale
                        $_tmp_date = date("Y-m-d", strtotime(str_replace('/', '-', trim($request_obj[$sub_key]['value']))));
                        $request_obj[$sub_key]['value'] = $_tmp_date;
                    }

                    // campi cb

                    if (isset($request_obj[$sub_key]['cb'])
                        && $request_obj[$sub_key]['cb'] != ''
                        && isset($request_obj[$sub_key]['value'])) {

                        $cb_value = $request_obj[$sub_key]['value'];

                        // campi select
                        if (isset($request_obj[$sub_key]['is_id'])
                            && $request_obj[$sub_key]['is_id'] != '') {
                            $row_arr = utilityHelper::get_cb_fieldtitle_values($request_obj[$sub_key]['is_id'], $cb_value);
                            if (isset($row_arr['fieldtitle']))
                                $cb_value = $row_arr['fieldtitle'];
                        }

                        $_new_user_cp[$request_obj[$sub_key]['cb']] = addslashes($cb_value);

                    }

                }

                // name e username prima lettera nome + cognome
                $_new_user['name'] = strtoupper(substr($nome_utente, 0, 1) . $cognome_utente);
                $_new_user['username'] = trim($username);
                $_new_user['email'] = trim($email_utente);
                $_new_user['password'] = $_user_value = JUserHelper::hashPassword($password_utente);

                // controllo il codice fiscale
                $_cf_check = UtilityHelper::conformita_cf($cf_utente);
                if (!isset($_cf_check['valido'])
                    || $_cf_check['valido'] != 1) {

                    $_err = "Problemi con il Codice fiscale";
                    if (isset($_cf_check['msg'])
                        && $_cf_check['msg'] != "")
                        $_err .= " " . $_cf_check['msg'];

                    throw new Exception($_err, 1);
                }

                // controllo validità email
                if (!filter_var($_new_user['email'], FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("EMAIL NON VALIDA: " . $_new_user['email'], 1);
                }

                // verifico l'esistenza delle colonne minimali per l'inserimento utente
                $_test_users_fields = UtilityHelper::check_new_user_array($_new_user);
                if ($_test_users_fields != "") {
                    throw new Exception("Mancano dei campi nencessari alla creazione dell'utente: " . $_test_users_fields, 1);
                }

                // controllo esistenza utente su username
                if (UtilityHelper::check_user_by_username($_new_user['username'])) {
                    //throw new Exception("USERNAME ESISTENTE: ". $_new_user['username'], 1);
                    // aggiungo dei numeri randomici
                    $_new_user['username'] = $_new_user['username'] . rand(1, 999);
                }

                // controllo esistenza email utente
                if (UtilityHelper::check_user_by_column('email', $_new_user['email'])) {
                    throw new Exception("EMAIL ESISTENTE: ". $_new_user['email'], 1);
                }

                // controllo se l'ug dell'evento è valido
                if ($id_evento == ""
                    || filter_var($id_evento, FILTER_VALIDATE_INT) === false)
                    throw new Exception("RIFERIMENTO EVENTO NON VALIDO", 1);

                // inserimento utente
                $_user_insert_query = UtilityHelper::get_insert_query("users", $_new_user);
                $_user_insert_query_result = UtilityHelper::insert_new_with_query($_user_insert_query);

                if (!is_array($_user_insert_query_result)) {
                    throw new Exception("Inserimento utente fallito: " . $_user_insert_query_result, 1);
                }

                $_new_user_id = $_user_insert_query_result['success'];

                // mi servono informazioni sull'unita
                if (isset($this->unit_id)
                    && $this->unit_id != ""
                    && $this->unit_id > 0) {
                    $unit_model = new gglmsModelUnita();
                    $_unit = $unit_model->getUnita($this->unit_id);
                    $_event_title = $_unit->titolo;
                    $ug_evento = $unit_model->get_id_gruppo_unit($this->unit_id);
                }

                // associo utente a gruppi solo eventi e gruppo evento
                $ug_arr = array();
                $ug_destinazione = UtilityHelper::get_ug_from_object($_params, "ug_nonsocio", true);
                if (!is_array($ug_destinazione))
                    $ug_arr[] = $ug_destinazione;
                else
                    $ug_arr = array_merge($ug_arr, $ug_destinazione);

                $ug_arr[] = $ug_evento;

                JUserHelper::setUserGroups($_new_user_id, $ug_arr);

                // riferimento id per CP
                $_new_user_cp['id'] = $_new_user_id;
                $_new_user_cp['user_id'] = $_new_user_id;

                // inserimento utente in CP
                $_cp_insert_query = UtilityHelper::get_insert_query("comprofiler", $_new_user_cp);
                $_cp_insert_query_result = UtilityHelper::insert_new_with_query($_cp_insert_query);
                if (!is_array($_cp_insert_query_result))
                    throw new Exception(print_r($_new_user_cp, true) . " errore durante inserimento", 1);

                // invio email registrazione
                $_email_from = UtilityHelper::get_params_from_object($_params, 'email_from');
                $_email_to  = UtilityHelper::get_params_from_object($_params, 'email_default');

                $_send_email = UtilityHelper::send_acquisto_evento_email_new_user($_email_to,
                                                                                    $_event_title,
                                                                                    $_new_user['name'],
                                                                                    $_new_user['username'],
                                                                                    $_new_user['email'],
                                                                                    $password_utente,
                                                                                    $_email_from,
                                                                                    true);

                $this->_ret['success'] = "tuttook";
                echo json_encode($this->_ret);
                die();


            }
            else if ($this->action == 'user_registration_sponsor_request_confirm') { // la registrazione dell'utente sponsor è andata a buon fine

                $this->hide_pp = true;

                $_payment_form = outputHelper::get_user_registration_confirm_acquisto_evento($this->unit_prezzo,
                                                                                            $this->unit_id,
                                                                                            $this->user_id,
                                                                                            $this->sconto_data,
                                                                                            $this->in_groups,
                                                                                            $_params);

                if (!is_array($_payment_form))
                    throw new Exception($_payment_form);

                $this->payment_form = $_payment_form['success'];
                $this->in_error = 0;

            }

        } catch (Exception $e){

            DEBUGG::log($e->getMessage() . ' -> ' . print_r($decode_arr, true), 'acquistaevento', 0, 1, 0);

            // echo senza mostrare la vista
            if (!$this->show_view) {
                $this->_ret['error'] = $e->getMessage();
                echo json_encode($this->_ret);
                die();
            }

            $this->payment_form = outputHelper::get_payment_form_error($e->getMessage());
            $this->in_error = 1;
        }

        parent::display($tpl);

    }


}
