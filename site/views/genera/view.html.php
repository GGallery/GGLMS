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

require_once JPATH_COMPONENT . '/controllers/generacoupon.php';


class gglmsViewGenera extends JViewLegacy
{

    protected $params;
    public $lista_corsi;
    public $societa_venditrici;
    public $check_coupon_attestato;
    public $specifica_durata;
    public $specifica_abilitazione;
    public $is_durata_standard;
    public $show_trial = 0;
    public $label_partita_iva;
    public $label_partita_iva_missing;
    public $label_ragione_sociale;
    public $label_email_tutor_aziendale;
    public $genera_coupon_visualizza_venditore = 1;
    public $genera_coupon_visualizza_ateco = 1;
    public $genera_coupon_visualizza_stampa_tracciato = 1;
    public $genera_coupon_visualizza_tipo_coupon = 0;
    public $genera_coupon_tipi_coupon;


    function display($tpl = null)
    {
        // scripts per input text venditori  filtrato
        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/typeahead.js');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/typeahead.css');

        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/generaCoupon.js');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/generaCoupon.css');

        $couponCtrl = new gglmsControllerGeneraCoupon();
        $this->lista_corsi = $couponCtrl->generaCoupon->lista_corsi;
        $this->societa_venditrici = $couponCtrl->generaCoupon->societa_venditrici;

        // leggo parametro config.check_coupon_attestato
        // se == 1 mostra la checkbox
        // se == 0 nascondi la checkbox e creali tutti abilitati
        $_config = new gglmsModelConfig();
        $this->check_coupon_attestato = $_config->getConfigValue('check_coupon_attestato');

        // leggo parametro config.specifica_durata_coupon
        // se == 1 si prende la durata standard
        // se == 0 specificare la durata per ogni coupon
        $this->specifica_durata = $_config->getConfigValue('specifica_durata_coupon');

        // leggo parametro config.specifica_durata_coupon
        // se == 1 mostro la checkbox nel form
        // se == 0 coupon generati abilitati di default
        $this->specifica_abilitazione = $_config->getConfigValue('coupon_active_default');

        // leggo parametro genera_coupon_label_partita_iva
        // se impostato sovrascrivo la label associata a Partita iva
        $this->label_partita_iva = utilityHelper::get_label_from_configuration('COM_GGLMS_GENERA_COUPON_PIVA', 'genera_coupon_label_partita_iva');

        // leggo parametro genera_coupon_label_partita_iva_missing
        // se impostato sovrascrivo la label associata a Partita iva non specificata
        $this->label_partita_iva_missing = utilityHelper::get_label_from_configuration('COM_GGLMS_GENERA_COUPON_PIVA_MISSING', 'genera_coupon_label_partita_iva_missing');


        // leggo parametro genera_coupon_label_ragione_sociale
        // se impostato sovrascrivo la label associata a Ragione sociale
        $this->label_ragione_sociale = utilityHelper::get_label_from_configuration('COM_GGLMS_GENERA_COUPON_COMPANYNAME', 'genera_coupon_label_ragione_sociale');

        // leggo parametro genera_coupon_label_email_tutor_aziendale
        // se impostato sovrascrivo la label associate a Email Tutor Aziendale
        $this->label_email_tutor_aziendale = utilityHelper::get_label_from_configuration('COM_GGLMS_GENERA_COUPON_EMAIL_TUTOR_AZ', 'genera_coupon_label_email_tutor_aziendale');

        // leggo parametro genera_coupon_visualizza_venditore
        // se 1 visualizzo il campo se 0 lo nascondo
        $this->genera_coupon_visualizza_venditore = utilityHelper::get_display_from_configuration($this->genera_coupon_visualizza_venditore, 'genera_coupon_visualizza_venditore');

        // leggo parametro genera_coupon_visualizza_ateco
        // se 1 visualizzo il campo se 0 lo nascondo
        $this->genera_coupon_visualizza_ateco = utilityHelper::get_display_from_configuration($this->genera_coupon_visualizza_ateco, 'genera_coupon_visualizza_ateco');

        // leggo parametro genera_coupon_visualizza_stampa_tracciato
        // se 1 visualizzo il campo se 0 lo nascondo
        $this->genera_coupon_visualizza_stampa_tracciato = utilityHelper::get_display_from_configuration($this->genera_coupon_visualizza_stampa_tracciato, 'genera_coupon_visualizza_stampa_tracciato');

        // leggo parametro genera_coupon_visualizza_tipo_coupon
        // se 1 visualizzo il campo se 0 lo nascondo
        $this->genera_coupon_visualizza_tipo_coupon = utilityHelper::get_display_from_configuration($this->genera_coupon_visualizza_tipo_coupon, 'genera_coupon_visualizza_stampa_tracciato');

        // leggo parametro genera_coupon_tipi_coupon
        $this->genera_coupon_tipi_coupon = $this->genera_coupon_visualizza_tipo_coupon == 1 ? utilityHelper::get_lista_tipo_coupon('genera_coupon_tipi_coupon', 'disabled required class="form-control cpn_opt"') : "";

        // checkbox trial la mostro solo se l'utente super user perchè sblocca tutto il corso.
        $user = JFactory::getUser();
        $user_model = new gglmsModelUsers();
        $is_super_admin = $user_model->is_user_superadmin($user->id);
        if ($is_super_admin == 1) {
            $this->show_trial = 1;
        }

        // Display the view
        parent::display($tpl);
    }



}
