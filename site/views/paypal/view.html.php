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

    function display($tpl = null)
    {
        try {


            JHtml::_('stylesheet', 'components/com_gglms/libraries/css/bootstrap.min.css');
            JHtml::_('script', 'components/com_gglms/libraries/js/bootstrap.min.js');
            JHtml::_('script', 'https://kit.fontawesome.com/dee2e7c711.js');

            $input = JFactory::getApplication()->input;
            // campi encoded dalla chiamata
            $pp = $input->get('pp');
            $this->call_result = "";
            $new_order = null;

            // chi o cosa mi sta chiamando
            if (!isset($pp)
                || $pp == "")
                throw new Exception("Nessuna azione richiesta", 1);

            // config model
            $_config = new gglmsModelConfig();
            $this->client_id = $_config->getConfigValue('paypal_client_id');
            $this->client_secret = $_config->getConfigValue('paypal_client_secret');
            $this->is_production = (int) $_config->getConfigValue('paypal_modalita_lavoro');

            // user model
            $_user_quote = new gglmsModelUsers();
            // parametri comuni
            $order_id = $input->get('order_id');
            $user_id = $input->get('user_id');

            // parte dedicata al form di pagamento delle quote SINPE
            if ($pp == 'sinpe') {

                $totale_sinpe = $input->get('totale_sinpe');
                $totale_espen = $input->get('totale_espen');

                $paypal = new gglmsControllerPaypal($this->client_id, $this->client_secret, $this->is_production);
                $new_order = json_decode($paypal->quote_sinpe_store_payment($order_id, $user_id, $totale_sinpe, $totale_espen), true);

                if (!isset($new_order['success']))
                    throw new Exception($new_order['error'], 1);

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

                if (!is_array($_insert_quote))
                    $this->call_result = $_insert_quote;
                else
                    $this->call_result = "tuttook";

            }
            // acquisto di servizi extra (ad esempio ESPEN in un secondo momento)
            else if ($pp == 'servizi_extra') {

                $totale = $input->get('totale');
                $totale_espen = $input->get('totale_espen');

                $paypal = new gglmsControllerPaypal($this->client_id, $this->client_secret, $this->is_production);
                $new_order = json_decode($paypal->quote_sinpe_store_payment($order_id, $user_id, $totale, $totale_espen), true);

                if (!isset($new_order['success']))
                    throw new Exception($new_order['error'], 1);

                // inserisco le quote per l'utente selezionato
                $_user_details = $_user_quote->get_user_details_cb($user_id);
                if (!is_array($_user_details))
                    throw new Exception($_user_details, 1);

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
            else if ($pp == 'acquistaevento') {

                $token = $input->get('token');

                // decodifica dell'attributo token
                $decode_pp = UtilityHelper::encrypt_decrypt('decrypt', $token, 'GGallery00!', 'GGallery00!');
                $decode_arr = explode('|==|', $decode_pp);

                // controllo i valori del token
                // controllo se ci sono tutti gli elementi
                if (count($decode_arr) < 5)
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
                    throw new Exception($new_order['error'], 1);

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
                throw new Exception("Non è stata eseguita nessuna operazione valida", 1);

            $this->result_view = OutputHelper::get_result_view($pp, $this->call_result);

            parent::display($tpl);


        } catch (Exception $e){
            DEBUGG::log($e->getMessage(), 'paypal', 0, 1, 0);
            die("Access denied: " . $e->getMessage());
        }

    }


}
