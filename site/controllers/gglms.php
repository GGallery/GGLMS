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

    protected $_db;
    private $_app;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_app = JFactory::getApplication();
        $this->_db = JFactory::getDbo();


    }

    public function updatecf()
    {
        try {
            $jinput = JFactory::getApplication()->input;
            $getdata = $jinput->post;

            $cfcorretto = $getdata->get('cfcorretto');
            $data = json_decode(base64_decode($getdata->get('data')));

            $conformita = utilityHelper::conformita_cf($cfcorretto);
            if ($conformita['valido']) {
                $this->_db = JFactory::getDbo();

                switch ($data->integration) {
                    case "eb":
                        $this->update_cf_eb($cfcorretto, $data);
                        break;
                    case "cb":
                        $this->update_cf_cb($cfcorretto, $data);
                        break;
                    default:
                        die('integrazione non gestita');
                        break;
                }

                $this->_app->enqueueMessage('Modifica effettuata correttamente');
                $url = JRoute::_('index.php?option=com_gglms&view=contenuto&alias=' . $data->return, false);
                $this->_app->redirect($url, 'Modifica effettuata correttamente');

            } else {
                $this->_app->enqueueMessage('Anche il nuovo codice fiscale (' . $cfcorretto . ') sembra avere problemi! ', 'error');
                $this->_app->redirect(JRoute::_('index.php?option=com_gglms&view=gglms&layout=mcf&data=' . $getdata->get('data')));
            }
        }
        catch (Exception $e){
            DEBUGG::error($e, 'Error updatecf controller GGLMS ', 1);
        }
    }

    private function update_cf_eb($cfcorretto, $data)
    {
        try {
            $query = "UPDATE `#__eb_field_values` as fv 
                  INNER JOIN #__eb_fields as f on f.id = fv.field_id
                  SET field_value ='" . $cfcorretto . "'
                  WHERE f.name = '" . $data->field_id . "'
                  AND registrant_id = " . $data->registrant_id . "
                  ";

            $this->_db->setQuery($query);
            return $this->_db->execute();
        }
        catch (Exception $e){
            DEBUGG::error($e, 'Error update_cf_eb controller GGLMS', 1);
        }
    }

    private function update_cf_cb($cfcorretto, $data)
    {
        try {
            $query = "UPDATE `#__comprofiler` as c 
                  SET " . $data->field_id . " = '" . $cfcorretto . "'
                  WHERE  user_id = " . $data->registrant_id . "
                  ";

            $this->_db->setQuery($query);
            return $this->_db->execute();
        }
        catch (Exception $e){
            DEBUGG::error($e, 'Error update_cf_cb controller GGLMS', 1);
        }
    }


}
