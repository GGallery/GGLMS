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

class gglmsViewunita extends JViewLegacy {

    public function display($tpl = null) {
        $form = $this->get('Form');
        $item = $this->get('Item');


        $this->form = $form;
        $this->item = $item;

        $this->addToolBar();

        jimport('joomla.environment.uri');
        $host = JURI::root();

        $document =  JFactory::getDocument();

        JHtml::_('jquery.framework', true);
        JHtml::_('bootstrap.framework', true);
        JHtml::_('jquery.ui', array('core', 'sortable'));
        
        $document->addStyleSheet($host . 'administrator/components/com_gglms/jupload/css/jquery.fileupload.css');
        $document->addStyleSheet($host . 'administrator/components/com_gglms/jupload/css/jquery.fileupload-ui.css');
        $document->addScript($host . 'administrator/components/com_gglms/jupload/js/jquery.fileupload.js');
        $document->addScript($host . 'administrator/components/com_gglms/jupload/js/procedure.js');


        // Display the template
        parent::display($tpl);

        // Set the document
    }

    protected function addToolBar() {
         
        JFactory::getApplication()->input->get('hidemainmenu', true);//RS JRequest::setVar('hidemainmenu', true);
        $isNew = ($this->item->id == 0);
        JToolBarHelper::title($isNew ? JText::_('COM_GGLMS_UNITA_NEW') : JText::_('COM_GGLMS_UNITA_EDIT'), 'unita');
        JToolBarHelper::save('unita.save');
        JToolBarHelper::apply('unita.apply');
        JToolBarHelper::cancel('unita.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */

}
