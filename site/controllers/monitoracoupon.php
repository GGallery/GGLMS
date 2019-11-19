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

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerMonitoracoupon extends JControllerLegacy
{

    private $_user;
    private $_japp;
    public $_params;
    public $_db;
    public $model;


    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();

        $this->_user = JFactory::getUser();
        $this->_db = &JFactory::getDbo();

    }


    public function getcouponlist()
    {

        $data = JRequest::get($_POST);
        var_dump($data);



    }


}
