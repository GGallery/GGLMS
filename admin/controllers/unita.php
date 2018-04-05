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
require_once JPATH_COMPONENT . '/models/report.php';

class gglmsControllerUnita extends JControllerForm
{

    public function clonaCorso()
    {

        try {

            $app = JFactory::getApplication();
            $unitaModel = $this->getModel('unita');
            $jinput = $app->input;
            $id = $jinput->get('cid')[0];
            if ($id == null) {

                $app->redirect(JRoute::_('index.php?option=com_gglms&view=unitas', false), $app->enqueueMessage('non hai selezionato alcun corso', 'Warning'));
                return null;
            }
            $result = $unitaModel->clonaCorso($id);
            $app->redirect(JRoute::_('index.php?option=com_gglms&view=unitas', false), $app->enqueueMessage($result));
        } catch (exception $exception) {

            $app->redirect(JRoute::_('index.php?option=com_gglms&view=unitas', false), $app->enqueueMessage($exception->getMessage(), 'Error'));
        }

    }

    public function delete(){
        try {
            $app = JFactory::getApplication();
            $unitaModel = $this->getModel('unita');
            $result=$unitaModel->deleteUnita($app->input->get('cid')[0]);
            $app->redirect(JRoute::_('index.php?option=com_gglms&view=unitas', false), $app->enqueueMessage($result));
        }catch (exception $exception) {

            $app->redirect(JRoute::_('index.php?option=com_gglms&view=unitas', false), $app->enqueueMessage($exception->getMessage(), 'Error'));
        }
    }

}
