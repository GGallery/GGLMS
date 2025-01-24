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


require_once JPATH_COMPONENT . '/models/unita.php';

class gglmsViewReport extends JViewLegacy {

    protected $params;
    protected $current_lang;
    protected $dp_lang;

    function display($tpl = null)
    {
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

        $model = new gglmsModelReport();
        $this->state = $this->get('State');


        $this->usergroups = utilityHelper::getSocietaByUser();

        $this->corsi = $model->getCorsi(true);

//        var_dump($this->corsi);
//        die();

        $this->summarize = $this->get('SummarizeCourse');

        if ($bootstrap_dp != "")
            JHtml::_('script', $bootstrap_dp);

        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/bootstrap.min.css');
        JHtml::_('stylesheet', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-bootgrid/1.3.1/jquery.bootgrid.min.css');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/container-fluid.css');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/bootstrap.min.css');
        JHtml::_('stylesheet', 'https://unpkg.com/bootstrap-table@1.18.2/dist/bootstrap-table.min.css');
        JHtml::_('stylesheet', 'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css');
        JHtml::_('stylesheet', 'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css');
        JHtml::_('stylesheet', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');

        JHtml::_('stylesheet', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
        JHtml::_('script', 'components/com_gglms/libraries/js/bootstrap.min.js');
        JHtml::_('script', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');
        JHtml::_('script', 'https://unpkg.com/tableexport.jquery.plugin/tableExport.min.js');
        JHtml::_('script', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js');
        JHtml::_('script', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.14/jspdf.plugin.autotable.min.js');
        JHtml::_('script', 'https://unpkg.com/bootstrap-table@1.18.2/dist/bootstrap-table.min.js');
        JHtml::_('script', 'https://unpkg.com/bootstrap-table@1.18.2/dist/locale/bootstrap-table-' . $this->current_lang . '.min.js');



        //GRAFICO TORTA
        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/Chart.bundle.min.js');

        //JFactory::getDocument()->addScript(JURI::root(true) . '/components/com_gglms/libraries/js/jquery.bootgrid.fa.min.js');
        JFactory::getDocument()->addScript(JURI::root(true) . '/components/com_gglms/libraries/js/jquery.bootgrid.min.js');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/loader.css');

        $lang = JFactory::getLanguage();
        $this->current_lang = $lang->getTag();
        $modelReport  = $this->getModel('report');
        $this->header  = $modelReport->getSottoUnita($this->state->get('id_corso'));

        parent::display($tpl);
    }




}
