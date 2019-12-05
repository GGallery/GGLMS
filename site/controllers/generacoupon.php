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

            $this->_japp->redirect(('index.php?option=com_gglms&view=genera'), $this->_japp->enqueueMessage('Coupon creato/i con successo!', 'Success'));

        } catch (Exception $e) {

            DEBUGG::error($e, 'generaCoupon');
        }
        $this->_japp->close();
    }


    public function api_genera_coupon()
    {
        try {

            $data = JRequest::get($_POST);
            $id_iscrizione = $this->generaCoupon->insert_coupon($data);

            $result = new stdClass();
            $result->id_iscrizione = $id_iscrizione;

            echo json_encode($result);
            $this->_japp->close();

        } catch (Exception $e) {

            DEBUGG::error($e, 'generaCoupon');
        }

    }

    // usato in form genera coupon frontend
    public function check_username()
    {

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

    // usato in form genera coupon frontend
    public function load_matching_venditori_list()
    {

        $japp = JFactory::getApplication();
        $venditore = JRequest::getVar('txt_venditore');
        $id_piattaforma = JRequest::getVar('id_piattaforma');

        // filtro i venditori anche  per piattaforma
        $query = $this->_db->getQuery(true)
            ->select('DISTINCT c.venditore')
            ->from('#__gg_coupon as c')
            ->where("c.venditore like '%" . $venditore . "%'")
            ->where("LEFT(c.id_iscrizione, 2) = '" . $id_piattaforma . "'");

        $this->_db->setQuery($query);
        $list = $this->_db->loadAssocList();

        $result = [];
        foreach ($list as $v) {
            array_push($result, $v["venditore"]);
        }

        echo isset($result) ? json_encode($result) : null;
        $japp->close();


    }

}
