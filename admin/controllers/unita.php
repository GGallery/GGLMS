<?php

/**
 * @package        Joomla.Tutorials
 * @subpackage    Component
 * @copyright    Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license        License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');
require_once JPATH_COMPONENT . '/models/report.php';
require_once JPATH_COMPONENT . '/models/unitas.php';

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

    public function delete()
    {
        try {
            $app = JFactory::getApplication();
            $unitaModel = $this->getModel('unita');
            $result = $unitaModel->deleteUnita($app->input->get('cid')[0]);
            $app->redirect(JRoute::_('index.php?option=com_gglms&view=unitas', false), $app->enqueueMessage($result));
        } catch (exception $exception) {

            $app->redirect(JRoute::_('index.php?option=com_gglms&view=unitas', false), $app->enqueueMessage($exception->getMessage(), 'Error'));
        }
    }

    public function creaGruppoCorso()
    {

        try {
            $app = JFactory::getApplication();
            $input = JFactory::getApplication()->input;

            $id = $input->get('id', null, 'string');


            $formData = new JRegistry($input->get('jform', '', 'array'));
            $is_corso = $formData->get('is_corso', null);
            $titolo = $formData->get('titolo', '');


            if ($is_corso == 0 || $is_corso == null) {
                $app->redirect(JRoute::_('index.php?option=com_gglms&view=unita&layout=edit&id=' . $id, false), $app->enqueueMessage('Non hai selezionato un unità  corso!', 'Warning'));
                return null;

            } else {
//
                $unitaModel = $this->getModel('unita');
               $res = $unitaModel->creaGruppoCorso($id,$titolo);

               if($res != null){

                   $app->redirect(JRoute::_('index.php?option=com_gglms&view=unita&layout=edit&id=' . $id, false), $app->enqueueMessage('Gruppo creato con successo!', 'Success'));
                   return null;
               }
               else{

                   $app->redirect(JRoute::_('index.php?option=com_gglms&view=unita&layout=edit&id=' . $id, false), $app->enqueueMessage('Esiste già un gruppo legato a questo corso', 'Warning'));
                   return null;

               }
            }
        } catch (exception $ex) {
            $app->redirect(JRoute::_('index.php?option=com_gglms&view=unita&layout=edit&id=' . $id, false), $app->enqueueMessage($ex->getMessage(), 'Error'));
        }

    }


    public function setUnitOrdinamento(){
        $unitasModel=new gglmsModelunitas();
        $unit_id=JRequest::getVar('unit_id');
        $pos=JRequest::getVar('pos');
        $result=$unitasModel->updateOrderValue($unit_id,$pos);


    }

}
