<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


require_once JPATH_COMPONENT . '/models/users.php';

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerReportKendo extends JControllerLegacy
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


    function get_filter_data()
    {

        $params = $this->_japp->input->getArray($_POST);

        $model = new gglmsModelReport();
        $result["usergroups"] = utilityHelper::getSocietaByUser();
        $result["corsi"] = $model->getCorsi(true);

        echo json_encode($result);
        $this->_japp->close();


    }

    public function get_user_detail()
    {

        $params = $this->_japp->input->getArray($_POST);
        $user_id = $params["user_id"];


        $query = $this->_db->getQuery(true)
            ->select('fields')
            ->from('#__gg_report_users')
            ->where("id_user =" . $user_id);

        $this->_db->setQuery($query);
        $u = $this->_db->loadAssoc();


        echo json_encode($u);
        $this->_japp->close();


    }


}

