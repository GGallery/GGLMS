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

class gglmsViewConfigs extends JViewLegacy {

    public function display($tpl = null) {


        $form = $this->get('Form');
        $item = $this->get('Item');

        $this->form = $form;
        $this->item = $item;
        
        $this->addToolBar();

        jimport('joomla.environment.uri');

        $this->sidebar = JHtmlSidebar::render();

        // Display the template
        parent::display($tpl);
        // Set the document
        $this->setDocument();
    }

    protected function addToolBar() {
        JFactory::getApplication()->input->get('hidemainmenu', true);//RS JRequest::setVar('hidemainmenu', true);
        JToolBarHelper::title("Configurazione GGLMS", 'gglms');
        JToolBarHelper::save('configs.save');
        JToolBarHelper::cancel('configs.cancel',  'JTOOLBAR_CANCEL' );
       
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument() {
        $isNew = ($this->item->id < 1);
        $document = JFactory::getDocument();
        $document->setTitle($isNew ? "COM_GGLMS_CONTENT_NUOVOCONTENUTO" : "COM_GGLMS_CONTENT_MODIFICACONTENUTO");
    }

}
