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
    protected $dp_lang;
    protected $id_evento_sponsor;
    protected $corsi;
    protected $quota_standard;
    protected $quota_studente;
    protected $forceIndexRedirect;

    function display($tpl = null)
    {

        try {

            $bootstrap_dp = "";
            $this->dp_lang = "EN";
            $lang = JFactory::getLanguage();
            $this->current_lang = $lang->getTag();
            $lang_locale_arr = $lang->getLocale();
            $this->forceIndexRedirect = false;

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
            JHtml::_('stylesheet', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
            
            JHtml::_('script', 'components/com_gglms/libraries/js/bootstrap.min.js');
            JHtml::_('script', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');

            if ($bootstrap_dp != "")
                JHtml::_('script', $bootstrap_dp);

            JHtml::_('script', 'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js');

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
            else if ($layout == 'registrazione_utenti_sponsor') {

                $this->id_evento_sponsor = JRequest::getVar('ev');
                if (!isset($this->id_evento_sponsor)
                    || $this->id_evento_sponsor == "")
                    throw new Exception("Nessun evento valido specificato", E_USER_ERROR);

                // controllo esistenza evento
                $_unit = new gglmsModelUnita();
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
            else if ($layout == 'resend_info_corso') {

                require_once JPATH_COMPONENT . '/controllers/users.php';
                $user_controller = new gglmsControllerUsers();
                $this->_html = $user_controller->get_utenti_per_societa();

            }
            else if ($layout == 'gestione_anagrafica_centri_sinpe') {
                // nothing to do at this moment..
            }
            else if ($layout == 'gestione_quote_asand') {

                $userGroupIdStandard = utilityHelper::check_usergroups_by_name("quota_standard");
                $userGroupIdStudente = utilityHelper::check_usergroups_by_name("quota_studente");

                if (is_null($userGroupIdStandard))
                    throw new Exception("Non è stato trovato nessun usergroup valido per la quota standard", E_USER_ERROR);

                if (is_null($userGroupIdStudente))
                    throw new Exception("Non è stato trovato nessun usergroup valido per la quota studente", E_USER_ERROR);

                $this->quota_standard = $userGroupIdStandard;
                $this->quota_studente = $userGroupIdStudente;
            }
            else if ($layout == 'rinnovo_quota_asand') {

                if ($_current_user->guest) {
                    $this->forceIndexRedirect = true;
                    throw new Exception("Questa pagina non è accessibile", E_USER_ERROR);
                }

                $userDetails = $_user->get_user_full_details_cb($_current_user->id);
                if (is_null($userDetails)) {
                    $this->forceIndexRedirect = true;
                    throw new Exception("Nessun riferimento comprofiler trovato per l'utente corrente!", E_USER_ERROR);
                }

                $dt = new DateTime();
                $titoloStudio = $userDetails['cb_titolo_studio'];
                $ultimoAnnoQuota = $userDetails['cb_ultimoannoinregola'];
                $annoCorrente = $dt->format('Y');
                $quotaAssociativa = "quota_standard";

                if ($annoCorrente == $ultimoAnnoQuota) {
                    $this->forceIndexRedirect = true;
                    throw new Exception("Risulti essere in regola con la quota di iscrizione per l'anno " . $annoCorrente, E_USER_ERROR);
                }

                if ($titoloStudio == ""
                    || is_null($titoloStudio)) {
                    $this->forceIndexRedirect = true;
                    throw new Exception ("Nessun riferimento al titolo di studio, si prega di completare il campo nel proofilo utente", E_USER_ERROR);
                }

                if (strpos($titoloStudio, "Studente") !== false) {
                    $quotaAssociativa = "quota_studente";
                }

                $paymentToken = utilityHelper::build_randon_token($_current_user->id . "|==|" . $quotaAssociativa);

                // index.php?option=com_gglms&view=registrazioneasand&action=user_registration_payment&pp=" + pToken
                $this->_html = <<<HTML
                <script>
                    window.location.href = "index.php?option=com_gglms&view=registrazioneasand&action=user_registration_payment&pp={$paymentToken}";
                </script>
HTML;

            }else if ($layout == 'gestione_accesso_utenti_aic') {
                // nothing to do at this moment..
            }

        }
        catch (Exception $e){
            $this->_html = outputHelper::get_payment_form_error($e->getMessage(), null, $this->forceIndexRedirect);
            $this->in_error = true;
        }

        // Display the view
        parent::display($tpl);
    }
}
