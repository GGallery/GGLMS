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

class gglmsViewGeneracoupon extends JViewLegacy {

    public function display($tpl = null) {
//        $form = $this->get('Form');

//        $this->form = $form;

        $this->addToolBar();

        jimport('joomla.environment.uri');

        $this->sidebar = JHtmlSidebar::render();
        $this->setDocument();


        // Display the template
        parent::display($tpl);
        // Set the document
    }

    protected function addToolBar() {
        JFactory::getApplication()->input->get('hidemainmenu', true);//RS JRequest::setVar('hidemainmenu', true);
        JToolBarHelper::title("Generazione di fenomeni", 'generacoupon');
        JToolBarHelper::save('generacoupon.save','Genera coupon');
        JToolBarHelper::cancel('generacoupon.cancel',  'JTOOLBAR_CANCEL' );
       
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument() {
        $document = JFactory::getDocument();
        $document->setTitle("Generazione di fenomeni");
    }

}
