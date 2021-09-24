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


class gglmsViewMonitora extends JViewLegacy
{

    protected $params;
    public $societa;
    public $lista_corsi;
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


        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/bootstrap.min.css');
        JHtml::_('stylesheet', 'https://unpkg.com/bootstrap-table@1.18.2/dist/bootstrap-table.min.css');
        JHtml::_('stylesheet', 'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css');
        JHtml::_('stylesheet', 'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css');
        JHtml::_('stylesheet', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');

        JHtml::_('script', 'components/com_gglms/libraries/js/bootstrap.min.js');
        JHtml::_('script', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');

        if ($bootstrap_dp != "")
            JHtml::_('script', $bootstrap_dp);


        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/monitoraCoupon.js');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/monitoraCoupon.css');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/loader.css');

        JHtml::_('script', 'https://unpkg.com/tableexport.jquery.plugin/tableExport.min.js');
        JHtml::_('script', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js');
        JHtml::_('script', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.14/jspdf.plugin.autotable.min.js');
        JHtml::_('script', 'https://unpkg.com/bootstrap-table@1.18.2/dist/bootstrap-table.min.js');
        JHtml::_('script', 'https://unpkg.com/bootstrap-table@1.18.2/dist/locale/bootstrap-table-' . $this->current_lang . '.min.js');

        JFactory::getDocument()->addScript(JURI::root(true) . '/components/com_gglms/libraries/js/jquery.bootgrid.min.js');

        $lang = JFactory::getLanguage();
        $this->current_lang = $lang->getTag();
        $this->societa = utilityHelper::getSocietaByUser();
        $this->lista_corsi = utilityHelper::getGruppiCorsi();


        // Display the view
        parent::display($tpl);

    }
}
