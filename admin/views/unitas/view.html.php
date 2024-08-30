<?php

/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
use Joomla\CMS\Document\Document;

defined('_JEXEC') or die;

jimport('joomla.application.component.view');
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/models/unita.php');

class gglmsViewunitas extends JViewLegacy {

    function display($tpl = null) {

//        $document =  JFactory::getDocument();
//        JHtml::_('bootstrap.framework'); //RS
//        JHtml::_('jquery.framework'); //RS
//        JHtml::_('jquery.ui', array('core', 'sortable'));//RS
//        $document->addStyleSheet('http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css');

        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');

//      Following variables used more than once
//      $this->sortColumn = $this->state->get('list.ordering');
//      $this->sortDirection = $this->state->get('list.direction');
        $this->searchterms = $this->state->get('filter.search');


        $this->sidebar = JHtmlSidebar::render();
        // Set the toolbar
        $this->addToolBar();

        // Display the template
        parent::display($tpl);
        $document = JFactory::getDocument();
        // Set the document
        $this->setDocument($document);
    }

    protected function addToolBar() {

        JToolBarHelper::title(JText::_('COM_GGLMS_MANAGER_UNITAS'), 'unitas');
        JToolBarHelper::deleteList(JText::_('COM_GGLMS_FILES_SICUROELIMINARE'), 'unita.delete');
        JToolBarHelper::editList('unita.edit');
        JToolBarHelper::addNew('unita.clonaCorso','duplica corso');
        JToolBarHelper::addNew('unita.add');
    }

    public function setDocument(Document $document): void
    {

        $document->setTitle(JText::_('COM_GGLMS_ADMINISTRATION'));
        //$document->addStyleSheet("/unico/media/jui/css/jquery.searchtools.css?748d04e1ef9639d6e79290609751cf67");
    }

}
