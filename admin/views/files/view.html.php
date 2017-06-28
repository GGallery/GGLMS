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

class gglmsViewFiles extends JViewLegacy {

    function display($tpl = null) {

        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');


//      Following variables used more than once
//      $this->sortColumn = $this->state->get('list.ordering');
//      $this->sortDirection = $this->state->get('list.direction');
//        $this->searchterms = $this->state->get('filter.search');
        // Set the toolbar
        $this->addToolBar();

        $this->sidebar = JHtmlSidebar::render();


        // Set the document
        $this->setDocument();

         // Display the template
        parent::display($tpl);
        

    }

    protected function addToolBar() {

        JToolBarHelper::title(JText::_('COM_GGLMS_MANAGER_FILES'), 'GGLMS');// RS JToolBarHelper::title(JText::_('COM_webtv_MANAGER_FILES'), 'webtv');
        JToolBarHelper::deleteList(JText::_('COM_GGLMS__FILES_SICUROELIMINARE'),'files.delete');
        JToolBarHelper::editList('file.edit');
        JToolBarHelper::addNew('file.add');
        
    }

    protected function setDocument() {
        
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_GGLMS_ADMINISTRATION'));
    }

}
