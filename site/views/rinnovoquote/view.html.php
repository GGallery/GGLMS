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

//require_once JPATH_COMPONENT . '/controllers/users.php';
require_once JPATH_COMPONENT . '/models/users.php';

class gglmsViewRinnovoQuote extends JViewLegacy {

    protected $params;
    protected $user_id;
    protected $nome_servizio;
    protected $ultimo_anno_pagato;
    protected $gruppi_online;
    protected $gruppi_moroso;
    protected $gruppi_decaduto;
    protected $payment_form;
    protected $payment_extra_form;
    protected $in_error;
    protected $client_id;
    protected $action;
    protected $call_result;
    protected $last_quota;

    function display($tpl = null)
    {
        try {


            JHtml::_('stylesheet', 'components/com_gglms/libraries/css/bootstrap.min.css');
            JHtml::_('script', 'components/com_gglms/libraries/js/bootstrap.min.js');

            /*
            // campi encoded dalla chiamata
            $pp = JRequest::getVar('pp');
            $_decripted_params = UtilityHelper::encrypt_decrypt('decrypt', $pp, 'GGallery00!', 'GGallery00!');
            if (strpos($_decripted_params, "|==|") == false)
                throw new Exception("Forbidden", 1);

            $_arr_decr = explode("|==|", $_decripted_params);
            $_username = $_arr_decr[0];
            $_password = UtilityHelper::encrypt_decrypt('decrypt', $_arr_decr[1], 'GGallery00!', 'GGallery00!');

            if (!isset($_arr_decr[2])
                || $_arr_decr[2] == "")
                throw new Exception("Nessun servizio definito", 1);

            $this->nome_servizio = $_arr_decr[2];

            // controllo esistenza utente
            $_user = new gglmsModelUsers();
            $_check_user = $_user->check_user($_username, $_password);

            if (!is_array($_check_user))
                throw new Exception($_check_user, 1);
            */

            $_current_user = JFactory::getUser();
            $this->user_id = $_current_user->id;
            $this->call_result = "";

            // pp client_id
            $_config = new gglmsModelConfig();
            $_user_quote = new gglmsModelUsers();
            $this->client_id = $_config->getConfigValue('paypal_client_id');
            if (is_null($this->client_id) || $this->client_id == "") throw new Exception("Client ID di PayPal non valorizzato!", E_USER_ERROR);

            // dettagli utente
            $_user = new gglmsModelUsers();
            $_user_details = $_user->get_user_details_cb($this->user_id);

            $this->action = JRequest::getVar('action');
            $pp = JRequest::getVar('pp');

            // chi o cosa mi sta chiamando


            if (!is_array($_user_details)) throw new Exception($_user_details, E_USER_ERROR);

            $dt = new DateTime();

            // funzionialità diverse a seconda del servizio invocato
            //if ($this->nome_servizio == "sinpe") {

            if (!isset($_user_details['ultimo_anno_pagato']) || $_user_details['ultimo_anno_pagato'] == "") throw new Exception("Ultimo anno di pagamento non definito", E_USER_ERROR);

            $_anno_corrente = $dt->format('Y');
            // se ultimo anno non è valorizzato richiedo il pagamento dell'anno corrente
            $this->ultimo_anno_pagato = $_user_details['ultimo_anno_pagato'] > 0 ? $_user_details['ultimo_anno_pagato'] : ($_anno_corrente-1);

            /*
            // controllo esistenza quote
            $this->user_id = $_check_user['success'];
            $_user_quote = $_user->get_user_quote($this->user_id);

            if (!is_array($_user_quote))
                throw new Exception($_user_quote, 1);
            */

            //$this->ultimo_anno_pagato = UtilityHelper::get_ultimo_anno_quota($_user_quote);
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
            //}

            if ($this->action == 'bb_buy_request') { // l'utente vuole pagare con bonifico

                // mi servono informazioni sull'unita

                $totale_sinpe = JRequest::getVar('totale_sinpe');
                $totale_espen = JRequest::getVar('totale_espen');

                $_user_details = $_user_quote->get_user_details_cb($this->user_id);
                if (!is_array($_user_details)) throw new Exception($_user_details, E_USER_ERROR);


                $_insert_quote = $_user_quote->insert_user_quote_stato_bonifico(
                    $this->user_id,
                    $dt->format('Y'),
                    $dt->format('Y-m-d H:i:s'),
                    $totale_sinpe,
                    $totale_espen);

                if (!is_array($_insert_quote)) $this->call_result = $_insert_quote;
                else $this->call_result = "tuttook";

                // nessuna delle opzioni richieste elaborata
                if ($this->call_result == "") throw new Exception("Non è stata eseguita nessuna operazione valida", E_USER_ERROR);

                $email_default = utilityHelper::get_params_from_object($_extra_pay, "email_default");
                $selectedUser = $_user->get_user_joomla($this->user_id);
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

                if($this->call_result == 'tuttook') {
                    $_html = <<<HTML
            <script type="text/javascript">
                
                history.back();
            </script>
HTML;

                }
            }



        } catch (Exception $e){
            $this->payment_form = outputHelper::get_payment_form_error($e->getMessage());
            $this->in_error = 1;
        }

        parent::display($tpl);
    }


}
