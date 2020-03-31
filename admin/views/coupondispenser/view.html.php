<?php

/**
 * @package        Joomla.Tutorials
 * @subpackage    Component
 * @copyright    Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license        License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class gglmsViewCoupondispenser extends JViewLegacy
{

    public function display($tpl = null)
    {
        $app = JFactory::getApplication();
        $form = $this->get('Form');
        $item = $this->get('Item');

        $this->form = $form;
        $this->item = $item;

        $this->addToolBar();

        jimport('joomla.environment.uri');
        $host = JURI::root();

        $document = JFactory::getDocument();

        JHtml::_('jquery.framework', true);
        JHtml::_('bootstrap.framework', true);
        JHtml::_('jquery.ui', array('core', 'sortable'));


        // Display the template
        parent::display($tpl);

        // Set the document
    }

    protected function addToolBar()
    {

        JFactory::getApplication()->input->get('hidemainmenu', true);//RS JRequest::setVar('hidemainmenu', true);
        $isNew = ($this->item->id == 0);
        JToolBarHelper::title($isNew ? JText::_('COM_GGLMS_COUPONDISPENSER_NEW') : JText::_('COM_GGLMS_COUPONDISPENSER_EDIT'));
        JToolBarHelper::save('coupondispenser.save');
        JToolBarHelper::apply('coupondispenser.apply');
        JToolBarHelper::cancel('coupondispenser.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');

    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */

}
