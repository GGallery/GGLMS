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
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.application.component.helper');

require_once JPATH_COMPONENT . '/controllers/paypal.php';
require_once JPATH_COMPONENT . '/models/users.php';

class gglmsViewPaypal extends JViewLegacy {

    protected $client_id;
    protected $client_secret;
    protected $is_production;
    protected $call_result;
    protected $call_error;
    protected $result_view;
    protected $last_quota;

    function display($tpl = null)
    {
        try {


            JHtml::_('stylesheet', 'components/com_gglms/libraries/css/bootstrap.min.css');
            JHtml::_('script', 'components/com_gglms/libraries/js/bootstrap.min.js');
            JHtml::_('script', 'https://kit.fontawesome.com/dee2e7c711.js');

            // campi encoded dalla chiamata
            $pp = JRequest::getVar('pp');
            $this->call_result = "";
            $new_order = null;

            // chi o cosa mi sta chiamando
            if (!isset($pp)
                || $pp == "")
                throw new Exception("Nessuna azione richiesta", E_USER_ERROR);

            // config model
            $_config = new gglmsModelConfig();
            $this->client_id = $_config->getConfigValue('paypal_client_id');
            $this->client_secret = $_config->getConfigValue('paypal_client_secret');
            $this->is_production = (int) $_config->getConfigValue('paypal_modalita_lavoro');

            // user model
            $_user_quote = new gglmsModelUsers();
            // parametri comuni
            $order_id = JRequest::getVar('order_id');
            $user_id = JRequest::getVar('user_id');

            // parte dedicata al form di pagamento delle quote SINPE
            if ($pp == 'sinpe') {

                $totale_sinpe = JRequest::getVar('totale_sinpe');
                $totale_espen = JRequest::getVar('totale_espen');

                $paypal = new gglmsControllerPaypal($this->client_id, $this->client_secret, $this->is_production);
                $new_order = json_decode($paypal->quote_sinpe_store_payment($order_id, $user_id, $totale_sinpe, $totale_espen), true);

                if (!isset($new_order['success']))
                    throw new Exception($new_order['error'], E_USER_ERROR);

                // inserisco le quote per l'utente selezionato
                $_user_details = $_user_quote->get_user_details_cb($user_id);
                if (!is_array($_user_details))
                    throw new Exception($_user_details, E_USER_ERROR);

                $_insert_quote = $_user_quote->insert_user_quote_anno(
                                                $user_id,
                                                $new_order['anno_quota'],
                                                $new_order['data_creazione'],
                                                $new_order['order_details'],
                                                $new_order['totale'],
                                                $new_order['totale_espen'],
                                                $_user_details,
                                                true);

                if (!is_array($_insert_quote)) $this->call_result = $_insert_quote;
                else $this->call_result = "tuttook";

            }
            // acquisto di servizi extra (ad esempio ESPEN in un secondo momento)
            else if ($pp == 'servizi_extra') {

                $totale = JRequest::getVar('totale');
                $totale_espen = JRequest::getVar('totale_espen');

                $paypal = new gglmsControllerPaypal($this->client_id, $this->client_secret, $this->is_production);
                $new_order = json_decode($paypal->quote_sinpe_store_payment($order_id, $user_id, $totale, $totale_espen), true);

                if (!isset($new_order['success']))
                    throw new Exception($new_order['error'], E_USER_ERROR);

                // inserisco le quote per l'utente selezionato
                $_user_details = $_user_quote->get_user_details_cb($user_id);
                if (!is_array($_user_details))
                    throw new Exception($_user_details, E_USER_ERROR);

                $_insert_servizi_extra = $_user_quote->insert_user_servizi_extra($user_id,
                                                                                $new_order['anno_quota'],
                                                                                $new_order['data_creazione'],
                                                                                $new_order['order_details'],
                                                                                $new_order['totale_espen'],
                                                                                $_user_details,
                                                                                $pp,
                                                                                true);

                if (!is_array($_insert_servizi_extra))
                    $this->call_result = $_insert_servizi_extra;
                else
                    $this->call_result = "tuttook";

            }
            else if ($pp == 'registrazioneasand') {

                $token = JRequest::getVar('token');

                $decryptedToken = utilityHelper::decrypt_random_token($token);
                $parsedToken = explode("|==|", $decryptedToken);

                $userId = $parsedToken[0];
                $requestedQuota = $parsedToken[1];
                $totaleQuota = $parsedToken[2];
                $ppCommissione = $parsedToken[3];

                $checkUser = $_user_quote->get_user_joomla($userId);
                if (is_null($checkUser)
                    || !isset($checkUser->id)) {
                    throw new Exception("Nessun utente trovato", E_USER_ERROR);
                }

                $dt = new DateTime();
                $checkToken = $_user_quote->get_quota_per_user_token($userId, $token, $dt->format('Y'));

                if (!is_null($checkToken)
                    && isset($checkToken['stato'])
                    && $checkToken['stato'] == 1)
                    throw new Exception("Il pagamento è già stato concluso, impossibile continuare", E_USER_ERROR);

                $paypal = new gglmsControllerPaypal($this->client_id, $this->client_secret, $this->is_production);
                $new_order = json_decode($paypal->quota_asand_anno_store_payment($order_id, $userId, $totaleQuota), true);

                if (!isset($new_order['success']))
                    throw new Exception($new_order['error'], E_USER_ERROR);

                $dettagliUtente = $_user_quote->get_user_full_details_cb($userId);
                $_params = utilityHelper::get_params_from_plugin('cb.checksociasand');

                // l'integrazione dei campi extra al momento è soltanto per community builder
                $_config = new gglmsModelConfig();
                $dettagliUtente['nome_utente'] = $dettagliUtente[$_config->getConfigValue('campo_community_builder_nome')];
                $dettagliUtente['cognome_utente'] = $dettagliUtente[$_config->getConfigValue('campo_community_builder_cognome')];
                $dettagliUtente['codice_fiscale'] = $dettagliUtente[$_config->getConfigValue('campo_community_builder_controllo_cf')];
                $dettagliUtente['email'] = $checkUser->email;
                $dettagliUtente['mail_from'] = utilityHelper::get_ug_from_object($_params, 'email_from');
                //$dettagliUtente['testo_pagamento_bonifico'] = utilityHelper::get_ug_from_object($_params, 'testo_pagamento_bonifico');

                $userGroupId = utilityHelper::check_usergroups_by_name($requestedQuota);
                if (is_null($userGroupId))
                    throw new Exception("Non è stato trovato nessun usergroup valido", E_USER_ERROR);

                $_insert_evento = $_user_quote->insert_user_servizi_extra($userId,
                                                                            $new_order['anno_quota'],
                                                                            $new_order['data_creazione'],
                                                                            $new_order['order_details'],
                                                                            $totaleQuota+$ppCommissione,
                                                                            $dettagliUtente,
                                                                            $pp,
                                                                            true,
                                                                            null,
                                                                            $userGroupId
                                                                        );

                if (!is_array($_insert_evento))
                    throw new Exception($_insert_evento, E_USER_ERROR);

                $insert_ug = $_user_quote->insert_user_into_usergroup($userId, $userGroupId);
                if (is_null($insert_ug))
                    throw new Exception("Inserimento utente in gruppo corso fallito: " . $userId . ", " . $userGroupId, E_USER_ERROR);

                // aggiorno ultimo anno pagato
                $_ultimo_anno = $_user_quote->update_ultimo_anno_pagato($userId, $new_order['anno_quota']);
                if (!is_array($_ultimo_anno))
                    throw new Exception($_ultimo_anno, E_USER_ERROR);

                // sblocco l'utente
                $updateUser = $_user_quote->update_user_column($userId, "block", 0);

                if (is_null($updateUser))
                    throw new Exception("Errore durante l'aggiornamento dell'utente", E_USER_ERROR);

                $this->call_result = "tuttook";
                $this->last_quota = $_insert_evento['last_quota'];

            }
            else if ($pp == 'acquistaevento') {

                $token = JRequest::getVar('token');

                // decodifica dell'attributo token
                $decode_pp = UtilityHelper::encrypt_decrypt('decrypt', $token, 'GGallery00!', 'GGallery00!');
                $decode_arr = explode('|==|', $decode_pp);

                // controllo i valori del token
                // controllo se ci sono tutti gli elementi
                if (count($decode_arr) < 5)
                    throw new Exception("La richiesta effettuta non può essere evasa in quanto incompleta", 1);

                if (!isset($decode_arr[0])
                    || $decode_arr[0] == "")
                    throw new Exception("Prezzo non disponibile", E_USER_ERROR);

                if (!isset($decode_arr[1])
                    || $decode_arr[1] == ""
                    || filter_var($decode_arr[1], FILTER_VALIDATE_INT) === false)
                    throw new Exception("Nessun identificativo evento", E_USER_ERROR);

                if (!isset($decode_arr[2])
                    || $decode_arr[2] == ""
                    || filter_var($decode_arr[2], FILTER_VALIDATE_INT) === false)
                    throw new Exception("Nessun utente specificato", E_USER_ERROR);

                /*
                if (!isset($decode_arr[3])
                    || $decode_arr[3] == ""
                    || filter_var($decode_arr[3], FILTER_VALIDATE_INT) === false)
                    throw new Exception("Missing sconto data", 1);

                if (!isset($decode_arr[4])
                    || $decode_arr[4] == ""
                    || filter_var($decode_arr[4], FILTER_VALIDATE_INT) === false)
                    throw new Exception("Missing in groups", 1);
                */

                $unit_prezzo = $decode_arr[0];
                $unit_id = $decode_arr[1];
                $user_id = $decode_arr[2];
                //$sconto_data = $decode_arr[3];
                //$in_groups = $decode_arr[4];

                $paypal = new gglmsControllerPaypal($this->client_id, $this->client_secret, $this->is_production);
                $new_order = json_decode($paypal->acquisto_evento_store_payment($order_id, $user_id, $unit_prezzo), true);

                if (!isset($new_order['success']))
                    throw new Exception($new_order['error'], E_USER_ERROR);

                $unit_model = new gglmsModelUnita();
                $unit_gruppo = $unit_model->get_id_gruppo_unit($unit_id);

                $_insert_evento = $_user_quote->insert_user_servizi_extra($user_id,
                                                                            $new_order['anno_quota'],
                                                                            $new_order['data_creazione'],
                                                                            $new_order['order_details'],
                                                                            $new_order['totale'],
                                                                            array(),
                                                                            $pp,
                                                                            true,
                                                                            $unit_id,
                                                                            $unit_gruppo);

                if (!is_array($_insert_evento))
                    $this->call_result = $_insert_evento;
                else
                    $this->call_result = "tuttook";

            }

            // nessuna delle opzioni richieste elaborata
            if ($this->call_result == "")
                throw new Exception("Non è stata eseguita nessuna operazione valida", E_USER_ERROR);

            $this->result_view = outputHelper::get_result_view($pp, $this->call_result, null, $this->last_quota);

            parent::display($tpl);


        } catch (Exception $e){
            DEBUGG::log($e->getMessage(), 'paypal', 0, 1, 0);
            die("Access denied: " . $e->getMessage());
        }

    }


}
