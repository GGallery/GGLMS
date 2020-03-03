<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


require_once JPATH_COMPONENT . '/models/helpdesk.php';
require_once JPATH_COMPONENT . '/models/users.php';

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerSummaryReport extends JControllerLegacy
{

    private $_user;
    private $_japp;
    public $_params;
    public $_db;

    public $__piattaforme;


    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();

        $this->_user = JFactory::getUser();
        $this->_db = &JFactory::getDbo();

        $this->_piattaforme = utilityHelper::getPiattaformeByUser(false);

    }

    public function getData()
    {
        $model_user = new gglmsModelUsers();

        $piattaforme = $model_user->get_user_piattaforme($this->_user->id);

        $p_list = array();

        foreach ($piattaforme as $p) {

            array_push($p_list, $p->value);
        }

        $query = $this->_db->getQuery(true)
            ->select('*')
            ->from('#__view_report')
            ->where("id_piattaforma  in (" . implode(', ', $p_list) . ")")
            ->order('id_piattaforma');

        $this->_db->setQuery($query);
        $result = $this->_db->loadAssocList();


        echo json_encode($result);
        $this->_japp->close();
//
    }

    public function is_tutor_aziendale()
    {

        $model = new gglmsModelUsers();
        $is_tutor_az = $model->is_tutor_aziendale($this->_user->id);

        echo(json_encode($is_tutor_az));
        $this->_japp->close();

        // is_tutor_aziendale

    }


}

