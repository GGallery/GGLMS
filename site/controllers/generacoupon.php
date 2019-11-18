<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

//require_once JPATH_COMPONENT . '/models/contenuto.php';
require_once JPATH_COMPONENT . '/models/generacoupon.php';

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerGeneraCoupon extends JControllerLegacy
{

    private $_user;
    private $_japp;
    public $_params;
    public $_db;
    public $generaCoupon;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();

        $this->_user = JFactory::getUser();
        $this->_db = &JFactory::getDbo();


        $this->generaCoupon = new gglmsModelGeneraCoupon();
        $this->lista_corsi = $this->generaCoupon->lista_corsi;
        $this->societa_venditrici = $this->generaCoupon->societa_venditrici;

    }

    public function generacoupon()
    {
        try {

            $data = JRequest::get($_POST);
            $this->generaCoupon->insert_coupon($data);


        } catch (Exception $e) {

            DEBUGG::error($e, 'generaCoupon');
        }
        $this->_japp->close();
    }


    public function check_username()
    {

        // usato in form genera coupon frontend
        $japp = JFactory::getApplication();
        $piva = JRequest::getVar('username');

        $query = $this->_db->getQuery(true)
            ->select('u.id, u.username, u.email, c.cb_ateco, u.name')
            ->from('#__users as u')
            ->join('inner', '#__comprofiler AS c ON c.user_id = u.id')
            ->where("u.username= '" . $piva . "'");

        $this->_db->setQuery($query);
        $result = $this->_db->loadAssoc();

        if ($result) {
            // prendo anche la piattaforma
            $model_user = new gglmsModelUsers();
            $id_piattaforma = $model_user->get_user_piattaforme($result["id"]);

            $result["id_piattaforma"] = $id_piattaforma[0]->value;

            }

        echo isset($result) ? json_encode($result) : null;
        $japp->close();

    }


}
