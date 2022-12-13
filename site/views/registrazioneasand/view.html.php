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
            $token = UtilityHelper::build_token_url_asand(0, 0, 0, 0, 0, 0);
            $this->rf_registrazione = UtilityHelper::build_encoded_link_asand($token, 'registrazioneasand', 'user_form');

            // chi o cosa mi sta chiamando
            if (!isset($this->action)
                || $this->action == "")
                throw new Exception("Nessuna azione richiesta", E_USER_ERROR);

            if (!isset($pp)
                || $pp == "")
                throw new Exception("Nessun parametro definito", E_USER_ERROR);

            $this->show_view = true;

            $this->hide_pp = false;

            // se l'utente non è loggato quindi o fa login oppure si deve registrare come un utente minimale soltanto per visionare il corso
            if ($this->action == 'buy') {

                $this->hide_pp = true;
                $_payment_form = outputHelper::get_user_registration_form_asand($this->user_id,
                    $this->in_groups);

                if (!is_array($_payment_form))
                    throw new Exception($_payment_form, 1);

                $this->payment_form = $_payment_form['success'];
                $this->in_error = 0;

            }

        } catch (Exception $e){

            DEBUGG::log($e->getMessage() , 'registrazioneasand', 0, 1, 0);

            // echo senza mostrare la vista
            if (!$this->show_view) {
                $this->_ret['error'] = $e->getMessage();
                echo json_encode($this->_ret);
                die();
            }

            $this->in_error = 1;
        }

        parent::display($tpl);

    }


}

