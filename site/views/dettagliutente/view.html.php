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
require_once JPATH_COMPONENT . '/models/helpdesk.php';


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
    protected $dp_lang;
    protected $id_evento_sponsor;
    protected $corsi;
    protected $box_corsi;
    protected $box_id;
    protected $unit_id;
    protected $unita;
    protected $gruppo_corso;
    protected $posti_disponibili;
    protected $prenota_url;

    function display($tpl = null)
    {

        try {

            $bootstrap_dp = "";
            $this->dp_lang = "EN";
            $lang = JFactory::getLanguage();
            $this->current_lang = $lang->getTag();
            $lang_locale_arr = $lang->getLocale();

            if (isset($lang_locale_arr[4])
                && $lang_locale_arr[4] != ""
                && $lang_locale_arr[4] != "en") {
                $bootstrap_dp = 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.' . $lang_locale_arr[4] . '.min.js';
                $this->dp_lang = strtolower($lang_locale_arr[4]);
            }


            JHtml::_('stylesheet', 'components/com_gglms/libraries/css/bootstrap.min.css');
            JHtml::_('stylesheet', 'https://unpkg.com/bootstrap-table@1.18.2/dist/bootstrap-table.min.css');
            JHtml::_('stylesheet', 'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css');
            JHtml::_('stylesheet', 'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css');
            JHtml::_('stylesheet', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');

            JHtml::_('script', 'components/com_gglms/libraries/js/bootstrap.min.js');
            JHtml::_('script', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');

            if ($bootstrap_dp != "")
                JHtml::_('script', $bootstrap_dp);

            JHtml::_('script', 'https://kit.fontawesome.com/dee2e7c711.js');
            JHtml::_('script', 'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js');


            JHtml::_('script', 'https://unpkg.com/tableexport.jquery.plugin/tableExport.min.js');
            JHtml::_('script', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js');
            JHtml::_('script', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.14/jspdf.plugin.autotable.min.js');
            JHtml::_('script', 'https://unpkg.com/bootstrap-table@1.18.2/dist/bootstrap-table.min.js');
            JHtml::_('script', 'https://unpkg.com/bootstrap-table@1.18.2/dist/locale/bootstrap-table-' . $this->current_lang . '.min.js');


            $layout = JRequest::getWord('template', '');
            $this->setLayout($layout);


            $_user = new gglmsModelUsers();
            $_unit = new gglmsModelUnita();
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
            else if ($layout == 'registrazione_utenti_sponsor') {

                $this->id_evento_sponsor = JRequest::getVar('ev');
                if (!isset($this->id_evento_sponsor)
                    || $this->id_evento_sponsor == "")
                    throw new Exception("Nessun evento valido specificato", 1);

                // controllo esistenza evento
                $_check_evento = $_unit->find_corso_pubblicato($this->id_evento_sponsor);
                if (!is_array($_check_evento))
                    throw new Exception($_check_evento, 1);

                // precarico i params del modulo
                $_form_registrazione = OutputHelper::get_user_action_registration_form_sponsor_evento(0,
                                                                                                    $this->id_evento_sponsor,
                                                                                                    $_current_user->id,
                                                                                                    $_unit);
                if (!is_array($_form_registrazione)
                    || !isset($_form_registrazione['success']))
                    throw new Exception($_form_registrazione, 1);

                $this->_html = $_form_registrazione['success'];

            }
            else if ($layout == 'report_quiz_per_utente') {

                $model_report = new gglmsModelReport();
                $this->corsi = $model_report->getCorsi(true);

            }
            else if ($layout == 'prenotazione_corsi_box') {

                $this->box_id = JRequest::getVar('box_id', null);
                $this->unit_id = JRequest::getVar('unit_id', null);

                $uri = JUri::getInstance();
                $dominio = null;
                $model_catalogo = new gglmsModelCatalogo();
                $model_helpdesk = new gglmsModelHelpDesk();
                $helpdesk_info = $model_helpdesk->getPiattaformaHelpDeskInfo();

                if (is_null($this->unit_id)) {

                    // elenco corsi associati ad un box_details
                    if (!is_null($this->box_id)) {
                        $dominio = $helpdesk_info->dominio;

                        utilityHelper::_set_cookie_by_name("prenota_corso_box", $uri->toString(), time() + 3600);
                        $this->box_corsi = $model_catalogo->get_box_categorie_corso($this->box_id, $dominio);
                        $this->_html = outputHelper::get_courses_list($this->box_corsi, $_current_user->id);

                    }
                    // visualizzazione dei box per categorie definite in box_details
                    else {

                        utilityHelper::_set_cookie_by_name("catalogo_corsi_list", $uri->toString(), time() + 3600);
                        $this->box_corsi = $model_catalogo->get_box_categorie_corso();
                        $this->_html = outputHelper::get_box_details($this->box_corsi);

                    }

                }
                else {
                    $this->unita = $_unit->getUnita($this->unit_id);
                    // gruppo corso unita
                    $this->gruppo_corso = $_unit->get_id_gruppo_unit($this->unit_id);

                    if (is_null($this->gruppo_corso)
                        || $this->gruppo_corso == "")
                        throw new Exception("Nessun gruppo corso definito per unita id: " . $this->unit_id, E_USER_ERROR);

                    $prenota = JRequest::getVar('prenota', null);

                    // se non devo prenotare..
                    if (is_null($prenota)) {
                        utilityHelper::_set_cookie_by_name("prenota_corso_unita", $uri->toString(), time() + 3600);

                        // il numero di utenti iscritti ad un gruppo corso
                        $utenti_per_gruppo = $_user->get_users_per_gruppo($this->gruppo_corso);
                        $utenti_iscritti = (is_array($utenti_per_gruppo)) ? count($utenti_per_gruppo) : 0;
                        //$this->prenota_url = utilityHelper::remove_param($uri->toString(), "unit_id") . '?prenota_id=' . $this->unit_id;
                        $this->prenota_url = $uri->toString() . '&prenota=1';

                        $this->posti_disponibili = ($this->unita->posti_disponibili - $utenti_iscritti);
                        $this->posti_disponibili = $this->posti_disponibili < 0 ? 0 : $this->posti_disponibili;
                        $this->_html = outputHelper::get_scheda_unita($this->unita,
                            $this->posti_disponibili,
                            $this->gruppo_corso,
                            $_current_user->id);
                    }
                    // prenotazione corso
                    else {

                        $_error = "";
                        // mi aspetto user_id e id_gruppo a cui associare l'utente
                        $uid = JRequest::getVar('uid', null);
                        $ug = JRequest::getVar('ug', null);

                        if ($uid != $_current_user->id)
                            throw new Exception(JText::_('COM_GGLMS_BOXES_SCHEDA_ERROR_USER_MISMATCH'), E_USER_ERROR);

                        if ($ug != $this->gruppo_corso)
                            throw new Exception(JText::_('COM_GGLMS_BOXES_SCHEDA_ERROR_GROUP_MISMATCH'), E_USER_ERROR);

                        // controllo se l'utente è già iscritto
                        $check_user_into_ug = utilityHelper::check_user_into_ug($_current_user->id, (array) $this->gruppo_corso);
                        if ($check_user_into_ug)
                            throw new Exception(JText::_('COM_GGLMS_BOXES_SCHEDA_ISCRITTO'), E_USER_ERROR);

                        // associo l'utente al gruppo
                        $_add_ug = utilityHelper::set_usergroup_generic($_current_user->id, $this->gruppo_corso);
                        if (!is_array($_add_ug))
                            throw new Exception($_add_ug, E_USER_ERROR);

                        // invio email di conferma
                        $_params = utilityHelper::get_params_from_module('mod_farmacie');
                        $email_from = utilityHelper::get_ug_from_object($_params, "email_from");

                        $email_oggetto = JText::_('COM_GGLMS_BOXES_SCHEDA_PRENOTAZIONE_MAIL_SUBJECT') . ' ' . $this->unita->titolo;
                        $data_inizio = (!is_null($this->unita->data_inizio) && $this->unita->data_inizio != "") ? utilityHelper::convert_dt_in_format($this->unita->data_inizio, 'd/m/Y') : "-";
                        $data_fine = (!is_null($this->unita->data_fine) && $this->unita->data_fine != "") ? utilityHelper::convert_dt_in_format($this->unita->data_fine, 'd/m/Y') : "-";
                        $email_body = <<<HTML
                        <br /><br />
                        <p>
                            Gentile {$_current_user->name},<br />
                            ti confermiamo l'iscrizione al corso {$this->unita->titolo}, che si svolgerà dal {$data_inizio} al {$data_fine}.
                            Grazie per l'adesione.
                        </p>
                        <p>
                            Cordiali saluti<br />
                            Lo staff di {$dominio}
                        </p>
                        <p>Questa mail è generata automaticamente, si prega di non rispondere.</p>
HTML;
                        $confirm_email = utilityHelper::send_email($email_oggetto, $email_body, (array) $_current_user->email, true, false, $email_from);
                        if (!$confirm_email)
                            throw new Exception(JText::_('COM_GGLMS_BOXES_SCHEDA_PRENOTAZIONE_MAIL_ERROR'), E_USER_ERROR);

                        $this->_html = outputHelper::get_success_subscription($this->unita->titolo);

                    }
                }

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
