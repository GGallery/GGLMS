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

class gglmsViewContents extends JViewLegacy {

    function display($tpl = null) {


        $app = JFactory::getApplication();


        $this->state = $this->get('State');

// Initial state for sorting and ordering
        $this->filter_order = 'm.ordinamento';
        $this->filter_order_Dir = $app->getUserStateFromRequest('filter_order_Dir', 'filter_order_Dir', 'desc', 'cmd');





        $form = $this->get('Form');
        $this->form = $form;

        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->sidebar = JHtmlSidebar::render();


//      Following variables used more than once
//      $this->sortColumn = $this->state->get('list.ordering');
//      $this->sortDirection = $this->state->get('list.direction');
        $this->searchterms = $this->state->get('filter.search');

        // Set the toolbar
        $this->addToolBar();
        $this->setDocument();

        parent::display($tpl);
    }

    protected function addToolBar() {
        JToolBarHelper::title(JText::_('COM_GGLMS_MANAGER_CONTENTS'), 'GGLMS');
        JToolBarHelper::deleteList(JText::_('COM_GGLMS__FILES_SICUROELIMINARE'), 'contents.delete');
        JToolBarHelper::editList('content.edit');
        JToolBarHelper::addNew('content.clonaContenuto','duplica contenuto');
        JToolBarHelper::addNew('content.add');
    }

    protected function setDocument() {
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_GGLMS_ADMINISTRATION'));
        $document->addStyleSheet("/unico/media/jui/css/jquery.searchtools.css?748d04e1ef9639d6e79290609751cf67");
    }

}
