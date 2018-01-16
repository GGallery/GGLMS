<?php
/**
 * Created by PhpStorm.
 * User: Antonio
 * Date: 18/12/2017
 * Time: 12:59
 */


/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');
require_once JPATH_COMPONENT . '/models/report.php';

class gglmsControllerReport extends JControllerAdmin
{
    public function empty_tables()
    {

        $model = new gglmsModelReport();

        if ($model->empty_tables()==true){


            $this->setRedirect('index.php?option=com_gglms&view=configs&extension=com_gglms', JText::_('tabelle svuotate correttamente'));

        }else{

            $this->setRedirect('index.php?option=com_gglms&view=configs&extension=com_gglms',JFactory::getApplication()->enqueueMessage(JText::_('SOME_ERROR_OCCURRED'), 'error'));
        }
    }

    public function allinea_tabella(){

        try {

            $tabella = JRequest::getVar('tabella');
            $modalita = JRequest::getVar('modalita');
            $model = new gglmsModelReport();
            echo json_encode($model->allinea_tabella($tabella, $modalita));
            JFactory::getApplication()->close();
        }catch (exceptions $ex){

            DEBUGG::log('ERRORE DA ALLINEA TABELLA'.$tabella.' in '.$modalita,'ERRORE DA ALLINEA TABELLA'.$tabella.' in '.$modalita,0,1);
        }
    }
}

