<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllergglms extends JControllerLegacy
{

    public function updatecf(){

        $app = JFactory::getApplication();
        $jinput = JFactory::getApplication()->input;
        $getdata= $jinput->post;

        $cfcorretto = $getdata->get('cfcorretto');
        $data= json_decode(base64_decode($getdata->get('data')));

        $conformita = utilityHelper::conformita_cf($cfcorretto);
        if($conformita['valido']) {
            $db = JFactory::getDbo();
            $query = "UPDATE `#__eb_field_values` as fv 
                  INNER JOIN #__eb_fields as f on f.id = fv.field_id
                  SET field_value ='" . $cfcorretto . "'
                  WHERE f.name = '" . $data->field_id . "'
                  AND registrant_id = " . $data->registrant_id . "
                  ";

            $db->setQuery($query);
            $db->execute();


            $app->enqueueMessage('Modifica effettuata correttamente');

            $url = JRoute::_('index.php?option=com_gglms&view=contenuto&alias='.$data->return,false);
            $app->redirect($url, 'Modifica effettuata correttamente');


        }
        else{
            $app->enqueueMessage('Anche il nuovo codice fiscale ('.$cfcorretto.') sembra avere problemi! ', 'error');
            $app->redirect(JRoute::_('index.php?option=com_gglms&view=gglms&layout=mcf&data='.$getdata->get('data')));
        }

    }
}
