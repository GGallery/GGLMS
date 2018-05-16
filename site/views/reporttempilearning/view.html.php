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
require_once JPATH_COMPONENT . '/controllers/reporttempilearning.php';

jimport('joomla.application.component.view');

class gglmsViewReportTempiLearning extends JViewLegacy {

    protected $params;

    function display($tpl = null)
    {

        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/bootstrap.min.css');
        JHtml::_('stylesheet', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-bootgrid/1.3.1/jquery.bootgrid.min.css');
        JHtml::_('stylesheet','https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css');
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();
        $this->_filterparam = new stdClass();
        $this->_filterparam->user_id = JRequest::getVar('user_id');

        $obj=new gglmsControllerReportTempiLearning();


        $this->utente=$obj->get_user_name();
        $this->tempi=$obj->get_tempi();

        parent::display($tpl);

    }


}
    