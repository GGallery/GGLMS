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

//require_once JPATH_COMPONENT . '/controllers/generacoupon.php';


class gglmsViewsummaryreport extends JViewLegacy
{

    protected $params;
    public $societa;
    public $lista_corsi;
    public $hide_columns = null;
    public $hide_columns_var = "[]";


    function display($tpl = null)
    {


        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/bootstrap.min.css');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/container-fluid.css');

        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/jquery-3.2.1.min.js');
        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/kendo/kendo.all.min.js');
        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/kendo/jszip.min.js');
        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/kendo/kendo.messages.it-IT.min.js');
        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/kendo/kendo.culture.it-IT.min.js');
        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/kendo/kendo.helper.js');




        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/kendo/kendo.common.min.css');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/kendo/kendo.bootstrap.min.css');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/summaryreport.css');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/loader.css');

        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/summaryreport.js');


        // esclusione colonne
        $_config = new gglmsModelConfig();
        $this->hide_columns = $_config->getConfigValue('summary_report_nascondi_colonne');
        if (isset($this->hide_columns)
            && !is_null($this->hide_columns)
            && $this->hide_columns != "")
            $this->hide_columns_var = json_encode(explode(",", $this->hide_columns));

        // leggo parametro summary_report_label_coupon
        // se impostato sovrascrivo la label associata a Coupon
        $this->label_coupon = utilityHelper::get_only_extra_label('COM_GGLMS_REPORT_COUPON', 'summary_report_label_coupon');
        // se impostato sovrascrivo la label associata a Nome
        $this->label_nome = utilityHelper::get_only_extra_label('COM_GGLMS_EXTRA_SUMMARY_REPORT_NOME', 'summary_report_label_nome');
        // se impostato sovrascrivo la label associata a Cognome
        $this->label_cognome = utilityHelper::get_only_extra_label('COM_GGLMS_EXTRA_SUMMARY_REPORT_COGNOME', 'summary_report_label_cognome');
        // se impostato sovrascrivo la label associata a Codice fiscale
        $this->label_codice_fiscale = utilityHelper::get_only_extra_label('COM_GGLMS_EXTRA_SUMMARY_REPORT_CODICE_FISCALE', 'summary_report_label_cf');
        // se impostato sovrascrivo la label associata a Corso
        $this->label_corso = utilityHelper::get_only_extra_label('COM_GGLMS_EXTRA_SUMMARY_REPORT_CORSO', 'summary_report_label_corso');
        // se impostato sovrascrivo la label associata a Azienda
        $this->label_azienda = utilityHelper::get_only_extra_label('COM_GGLMS_EXTRA_SUMMARY_REPORT_AZIENDA', 'summary_report_label_azienda');
        // se impostato sovrascrivo la label associata a Stato
        $this->label_stato = utilityHelper::get_only_extra_label('COM_GGLMS_EXTRA_SUMMARY_REPORT_STATO', 'summary_report_label_stato');
        // se impostato sovrascrivo la label associata a Attestato
        $this->label_attestato = utilityHelper::get_only_extra_label('COM_GGLMS_EXTRA_SUMMARY_REPORT_ATTESTATO', 'summary_report_label_attestato');
        // se impostato sovrascrivo la label associata a Venditore
        $this->label_venditore = utilityHelper::get_only_extra_label('COM_GGLMS_EXTRA_SUMMARY_REPORT_VENDITORE', 'summary_report_label_venditore');
        
        // Display the view
        parent::display($tpl);

    }




}
