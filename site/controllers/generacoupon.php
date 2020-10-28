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

            $this->_japp->redirect(('index.php?option=com_gglms&view=genera'), $this->_japp->enqueueMessage(JText::_('COM_GGLMS_GENERA_COUPON_SUCCESS'), 'Success'));

        } catch (Exception $e) {

            DEBUGG::error($e, 'generaCoupon');
        }
        $this->_japp->close();
    }


    /*api genera coupon per sviluppatori e comerce*/
    public function api_genera_coupon()
    {
        try {

            $data = JRequest::get($_POST);

//            todo add $data check

            DEBUGG::log(json_encode($data), 'api_genera_coupon', 0, 1, 0 );

            $id_iscrizione = $this->generaCoupon->insert_coupon($data);

            $result = new stdClass();
            $result->id_iscrizione = $id_iscrizione;

            echo json_encode($result);
            $this->_japp->close();

        } catch (Exception $e) {

            DEBUGG::error($e, 'generaCoupon');
        }

    }

    /* usato in form genera coupon frontend*/
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

    /* usato in form genera coupon frontend*/
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


    /* corsi per piattaforma per sviluppatori e comerce*/
    public function api_get_corsi()
    {

        $japp = JFactory::getApplication();
        $id_piattaforma = JRequest::getVar('id_piattaforma');
        $result = utilityHelper::getGruppiCorsi($id_piattaforma);

        echo isset($result) ? json_encode($result) : null;
        $japp->close();

    }


    public function get_lista_corsi_by_piattaforma()
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


    function get_lista_piva($ret_json = true)
    {

        try {
            $japp = JFactory::getApplication();
//            $txt_azienda = JRequest::getVar('txt_azienda');

            $user_id = $this->_user->id;
            $_config = new gglmsModelConfig();
            $id_gruppo_tutor_aziendale = $_config->getConfigValue('id_gruppo_tutor_aziendale');

            $model_user = new gglmsModelUsers();
            $id_piattaforma = $model_user->get_user_piattaforme($user_id);

            $id_piattaforma_array = array();
            foreach ($id_piattaforma as $p) {
                array_push($id_piattaforma_array, $p->value);
            }


            $db = JFactory::getDbo();
            // estratto anche l'id dell'azienda
            $query = $db->getQuery(true)
                ->select(' distinct u.name as azienda , u.username as piva, u.id as id_azienda, piattaforme.id as id_gruppo ')
                ->from('#__users as u')
                ->join('inner', '#__user_usergroup_map as map on map.user_id = u.id')
                ->join('inner', '#__usergroups as ug on ug.id = map.group_id')
                ->join('inner', '#__usergroups as  piattaforme on piattaforme.title = u.name')
                ->where(" ug.id=" . $id_gruppo_tutor_aziendale)
                ->where('piattaforme.parent_id IN (' . implode(", ", $id_piattaforma_array) . ')')
                ->order('u.name asc');

            $db->setQuery($query);

            // se richiamati da ajax restituisco un json
            if ($ret_json) {
                $piva_list = $db->loadObjectList();
                echo json_encode($piva_list);
                $japp->close();
            }
            // altrimenti restituisco un array in modo "silente"
            else {
                $piva_list = $db->loadAssocList();
                return $piva_list;
            }

        } catch (Exception $e) {
            DEBUGG::error($e, 'getListaPiva');
        }


    }



}
