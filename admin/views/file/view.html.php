<?php

/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
use Joomla\CMS\Document\Document;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class gglmsViewFile extends JViewLegacy {

    public function display($tpl = null) {
        $form = $this->get('Form');
        $item = $this->get('Item');

        $this->form = $form;
        $this->item = $item;

        $this->addToolBar();

        jimport('joomla.environment.uri');

        $host = JURI::root();

        JHtml::_('jquery.framework', true);
        JHtml::_('bootstrap.framework', true);
        HTMLHelper::_('script', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', array('version' => 'auto', 'relative' => false));
        HTMLHelper::_('stylesheet', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css', array('version' => 'auto', 'relative' => false));

        $document = JFactory::getDocument();
        $document->addStyleSheet($host . 'administrator/components/com_gglms/jupload/css/jquery.fileupload.css');
        $document->addStyleSheet($host . 'administrator/components/com_gglms/jupload/css/jquery.fileupload-ui.css');
        $document->addScript($host . 'administrator/components/com_gglms/jupload/js/jquery.fileupload.js');
        $document->addScript($host . 'administrator/components/com_gglms/jupload/js/procedure.js');

        // Display the template
        $document = JFactory::getDocument();
        $this->setDocument($document);
        parent::display($tpl);



    }

    protected function addToolBar() {

        JFactory::getApplication()->input->get('hidemainmenu', true);//RS JRequest::setVar('hidemainmenu', true);
        $isNew = ($this->item->id == 0);
        JToolBarHelper::title(JText::_('COM_GGLMS_FILE_MANAGER') , 'GGLMS');
        JToolBarHelper::save('file.save');
        JToolBarHelper::apply('file.apply');
        JToolBarHelper::cancel('file.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }

    /**
     * Method to set up the document properties
     *
     * @param Document $document
     * @return void
     */
    public function setDocument(Document $document): void
    {
        $isNew = ($this->item->id < 1);
        $document->setTitle($isNew ? JText::_('COM_GGLMS_FILE_NEW') : JText::_('COM_GGLMS_FILE_EDITING'));
    }

}
