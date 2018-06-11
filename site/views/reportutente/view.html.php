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
require_once JPATH_COMPONENT . '/controllers/reportutente.php';
jimport('joomla.application.component.view');

class gglmsViewReportUtente extends JViewLegacy {

    protected $params;

    function display($tpl = null)
    {


        JHtml::_('stylesheet','https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css');
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();

        $report=new gglmsControllerReportUtente();
        $this->data=$report->get_report_utente();

        $this->utente=$report->get_user();

        $this->_filterparam = new stdClass();
        $this->_filterparam->user_id = JRequest::getVar('user_id');


        parent::display($tpl);

    }


}
    