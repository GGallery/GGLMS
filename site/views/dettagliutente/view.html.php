<?php

/**
 * @version        1
 * @package        webtv
 * @author        antonio
 * @author mail    tony@bslt.it
 * @link
 * @copyright    Copyright (C) 2011 antonio - All rights reserved.
 * @license        GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

jimport('joomla.application.component.helper');

require_once JPATH_COMPONENT . '/models/users.php';


class gglmsViewdettagliutente extends JViewLegacy
{

    protected $params;
    protected $_quote_iscrizione;
    protected $_soci;
    protected $_html;
    protected $current_lang;
    protected $online;
    protected $moroso;
    protected $decaduto;
    protected $preiscritto;
    protected $conferma_acquisto;
    protected $in_error;
    protected $client_id;
    protected $user_id;
    protected $payment_extra_form;

    function display($tpl = null)
    {

        try {
            JHtml::_('stylesheet', 'components/com_gglms/libraries/css/bootstrap.min.css');
            JHtml::_('stylesheet', 'https://unpkg.com/bootstrap-table@1.18.2/dist/bootstrap-table.min.css');
            JHtml::_('stylesheet', 'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css');
            JHtml::_('stylesheet', 'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css');

            JHtml::_('script', 'components/com_gglms/libraries/js/bootstrap.min.js');
            JHtml::_('script', 'https://kit.fontawesome.com/dee2e7c711.js');
            JHtml::_('script', 'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js');



            $lang = JFactory::getLanguage();
            $this->current_lang = $lang->getTag();

            JHtml::_('script', 'https://unpkg.com/tableexport.jquery.plugin/tableExport.min.js');
            JHtml::_('script', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js');
            JHtml::_('script', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.14/jspdf.plugin.autotable.min.js');
            JHtml::_('script', 'https://unpkg.com/bootstrap-table@1.18.2/dist/bootstrap-table.min.js');
            JHtml::_('script', 'https://unpkg.com/bootstrap-table@1.18.2/dist/locale/bootstrap-table-' . $this->current_lang . '.min.js');


            $layout = JRequest::getWord('template', '');
            $this->setLayout($layout);

            $_user = new gglmsModelUsers();
            $_current_user = JFactory::getUser();

            // parametri dal plugin di gestione soci
            $_params = utilityHelper::get_params_from_plugin();
            $this->online = utilityHelper::get_ug_from_object($_params, "ug_online");
            $this->moroso = utilityHelper::get_ug_from_object($_params, "ug_moroso");
            $this->decaduto = utilityHelper::get_ug_from_object($_params, "ug_decaduto");
            $_params_2 = utilityHelper::get_params_from_plugin("cb.cbsetgroup");
            $this->preiscritto = utilityHelper::get_ug_from_object($_params_2, "ug_destinazione");

            // parametri dal plugin di gestione acquisto corsi
            $_params_module = UtilityHelper::get_params_from_module();
            $this->conferma_acquisto = UtilityHelper::get_ug_from_object($_params_module, "ug_conferma_acquisto", true);

            if ($layout == 'quota_sinpe_anno') {

                // per admin vedo tutto
                $_user_id = ($_current_user->authorise('core.admin')) ? null : $_current_user->id;
                $this->user_id = $_user_id;

            }
            else if ($layout == 'gestione_soci_sinpe') {
                // nothing to do at this moment..
            }
            else if ($layout == 'pagamenti_servizi_extra') {

                // pp client_id
                $_config = new gglmsModelConfig();
                $this->client_id = $_config->getConfigValue('paypal_client_id');
                if (is_null($this->client_id)
                    || $this->client_id == "")
                    throw new Exception("Client ID di PayPal non valorizzato!", 1);

                // controllo se l'utente è in regola (quindi online)
                $_check_online = utilityHelper::check_user_into_ug($_current_user->id, explode(",", $this->online));
                // se non online
                if (!$_check_online)
                    throw new Exception(JText::_('COM_GGLMS_DETTAGLI_UTENTE_PAGAMENTI_EXTRA_STR3'), 1);

                $this->_html = outputHelper::get_pagamenti_servizi_extra($_current_user->id);
                // nessun servizio extra
                if ($this->_html == "")
                    throw new Exception(JText::_('COM_GGLMS_DETTAGLI_UTENTE_PAGAMENTI_EXTRA_STR2'), 1);

                // verifico se esiste l'indicazione per il metodo di pagamento alternativi
                $_extra_pay = utilityHelper::get_params_from_plugin();
                $this->payment_extra_form = outputHelper::get_payment_extra($_extra_pay);
            }
        }
        catch (Exception $e){
            $this->_html = outputHelper::get_payment_form_error($e->getMessage());
            $this->in_error = true;
        }

        // Display the view
        parent::display($tpl);
    }
}
