<?php

/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
require_once (JPATH_COMPONENT . '/models/logs.php');

class gglmsViewlogs extends JViewLegacy {

    function display($tpl = null) {

//        $document =  JFactory::getDocument();
//        JHtml::_('bootstrap.framework'); //RS
//        JHtml::_('jquery.framework'); //RS
//        JHtml::_('jquery.ui', array('core', 'sortable'));//RS
//        $document->addStyleSheet('http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css');
        JHtml::_('stylesheet','https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css');
        $this->_japp = JFactory::getApplication();
        $modelLogs=new gglmsModellogs();
        $this->items = $modelLogs->getCorsi(JFactory::getUser()->id);



        // Display the template
        parent::display($tpl);


    }

}
