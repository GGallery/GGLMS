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
        $this->state = $this->get('State');

        //GRAFICO TORTA
        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/Chart.bundle.min.js');

        //TABELLA DINAMICA
        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/jquery.bootgrid.min.js');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/jquery.bootgrid.min.css');

//        $modelReport  = $this->getModel('report');
//        $this->header  = $modelReport->getSottoUnita($this->state->get('id_corso'));
//
//        $this->userReport = $this->get('User');

//        DEBUGG::log($header, 'header');
//        DEBUGG::log($this->userReport, 'userReport');

        parent::display($tpl);
    }




}
    