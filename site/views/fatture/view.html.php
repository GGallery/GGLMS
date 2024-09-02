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


class gglmsViewFatture extends JViewLegacy {

    protected $params;
    protected $current_lang;
    protected $dp_lang;
    protected $id_utente;

    function display($tpl = null)
    {
        $bootstrap_dp = "";
        $this->dp_lang = "EN";
        $lang = JFactory::getLanguage();
        $this->current_lang = $lang->getTag();



        if ($bootstrap_dp != "")
            JHtml::_('script', $bootstrap_dp);

        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/bootstrap.min.css');
        JHtml::_('stylesheet', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-bootgrid/1.3.1/jquery.bootgrid.min.css');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/container-fluid.css');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/bootstrap.min.css');
        JHtml::_('stylesheet', 'https://unpkg.com/bootstrap-table@1.18.2/dist/bootstrap-table.min.css');
        JHtml::_('stylesheet', 'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css');
        JHtml::_('stylesheet', 'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css');


        JHtml::_('script', 'https://kit.fontawesome.com/dee2e7c711.js');
        JHtml::_('script', 'components/com_gglms/libraries/js/bootstrap.min.js');
        JHtml::_('script', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js');
        JHtml::_('script', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.14/jspdf.plugin.autotable.min.js');
        JHtml::_('script', 'https://unpkg.com/bootstrap-table@1.18.2/dist/bootstrap-table.min.js');
        JHtml::_('script', 'https://unpkg.com/bootstrap-table@1.18.2/dist/locale/bootstrap-table-' . $this->current_lang . '.min.js');



        //GRAFICO TORTA
        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/Chart.bundle.min.js');


        JFactory::getDocument()->addScript(JURI::root(true) . '/components/com_gglms/libraries/js/jquery.bootgrid.min.js');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/loader.css');

        $lang = JFactory::getLanguage();
        $this->current_lang = $lang->getTag();
        $user = JFactory::getUser();
        $this->id_utente = $user->get('id');


        parent::display($tpl);
    }




}
