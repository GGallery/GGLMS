<?php

/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class gglmsControllerContent extends JControllerForm {


    public function clonaContenuto()
    {

        try {

            $app = JFactory::getApplication();
            $contentModel = $this->getModel('content');
            $jinput = $app->input;
            $id = $jinput->get('cid')[0];
            if ($id == null) {

                $app->redirect(JRoute::_('index.php?option=com_gglms&view=contents', false), $app->enqueueMessage('non hai selezionato alcun corso', 'Warning'));
                return null;
            }
            $result = $contentModel->clonaContenuto($id);
            $app->redirect(JRoute::_('index.php?option=com_gglms&view=contents', false), $app->enqueueMessage($result));
        } catch (exception $exception) {

            $app->redirect(JRoute::_('index.php?option=com_gglms&view=contents', false), $app->enqueueMessage($exception->getMessage(), 'Error'));
        }

    }


}
