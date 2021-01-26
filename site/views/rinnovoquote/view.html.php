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

    function display($tpl = null)
    {
        try {


            JHtml::_('stylesheet', '/components/com_gglms/libraries/css/bootstrap.min.css');
            JHtml::_('script', '/components/com_gglms/libraries/js/bootstrap.min.js');

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

            $dt = new DateTime();

            // funzionialità diverse a seconda del servizio invocato
            if ($this->nome_servizio == "sinpe") {

                if (!isset($_arr_decr[3])
                    || $_arr_decr[3] == "")
                    throw new Exception("Ultimo anno di pagamento non definito", 1);

                $_anno_corrente = $dt->format('Y');
                // se ultimo anno non è valorizzato richiedo il pagamento dell'anno corrente
                $this->ultimo_anno_pagato = $_arr_decr[3] > 0 ? $_arr_decr[3] : $_anno_corrente;

                // controllo esistenza quote
                $this->user_id = $_check_user['success'];
                $_user_quote = $_user->get_user_quote($this->user_id);

                if (!is_array($_user_quote))
                    throw new Exception($_user_quote, 1);

                // dettagli utente
                $_user_details = $_user->get_user_details_cb($this->user_id);
                if (!is_array($_user_details))
                    throw new Exception($_user_details, 1);

                //$this->ultimo_anno_pagato = UtilityHelper::get_ultimo_anno_quota($_user_quote);
                $_payment_form = outputHelper::get_payment_form_from_year($this->user_id,
                    $_username,
                    $this->ultimo_anno_pagato,
                    $_anno_corrente,
                    $_user_details);
                if (!is_array($_payment_form))
                    throw new Exception($_payment_form);

                $this->payment_form = $_payment_form['success'];

                // verifico se esiste l'indicazione per il metodo di pagamento alternativi
                $_extra_pay = utilityHelper::get_params_from_plugin();
                $this->payment_extra_form = outputHelper::get_payment_extra($_extra_pay);
            }

            parent::display($tpl);


        } catch (Exception $e){
            die("Access denied: " . $e->getMessage());
        }

    }


}
