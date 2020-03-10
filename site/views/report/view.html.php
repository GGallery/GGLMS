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

    function display($tpl = null)
    {

        $model = new gglmsModelReport();
        $this->state = $this->get('State');


        $this->usergroups = utilityHelper::getSocietaByUser();

        $this->corsi = $model->getCorsi(true);

//        var_dump($this->corsi);
//        die();

        $this->summarize = $this->get('SummarizeCourse');

        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/bootstrap.min.css');
        JHtml::_('stylesheet', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-bootgrid/1.3.1/jquery.bootgrid.min.css');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/container-fluid.css');

        //GRAFICO TORTA
        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/Chart.bundle.min.js');

        //JFactory::getDocument()->addScript(JURI::root(true) . '/components/com_gglms/libraries/js/jquery.bootgrid.fa.min.js');
        JFactory::getDocument()->addScript(JURI::root(true) . '/components/com_gglms/libraries/js/jquery.bootgrid.min.js');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/loader.css');


        $modelReport  = $this->getModel('report');
        $this->header  = $modelReport->getSottoUnita($this->state->get('id_corso'));

        parent::display($tpl);
    }




}
