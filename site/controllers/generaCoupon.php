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


    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();

        $this->_user = JFactory::getUser();
        $this->_db = &JFactory::getDbo();


    }

    //
    public function generaCoupon()
    {

        try {
            $db = JFactory::getDbo();
            $postData = $this->_japp->input->get;
            $data = JRequest::get($_POST);

//
//            $piva = $data['username'];
//            $ragioneSociale = $data['ragioneSociale'];
//            $email = $data['email'];
//            $ateco = $data['ateco'];
//            $qty = $data['qty'];
//            $id_gruppo_piattaforma = $data['vendor'];
//            $attestato = $data['attestato'] == 'on' ? true : false;
//            $stampatracciato = $data['stampatracciato'] == 'on' ? true : false;



            $coupon = new gglmsModelGeneraCoupon();
            $coupon->insert_coupon($data);




        } catch (Exception $e) {

//            DEBUGG::log($e, 'Exception in generaCoupon ', 1);
        }
        $this->_japp->close();
    }


    ///////////////////////////////////////////////////////////

    public function getGruppiCorsi()
    {

        // carico i gruppi dei corsi
        $query_config = $this->_db->getQuery(true)
            ->select('config_value')
            ->from('#__gg_configs')
            ->where("config_key='id_gruppo_corsi'");

        $this->_db->setQuery($query_config);
        $id_gruppo_accesso_corsi = $this->_db->loadResult();


        $query = $this->_db->getQuery(true)
            ->select('g.id as value, g.title as text')
            ->from('#__usergroups as g')
            ->where(" g.parent_id =" . $id_gruppo_accesso_corsi);

        $this->_db->setQuery($query);
        $corsi = $this->_db->loadObjectList();


        return $corsi;
    }

    public function getVenditrici()
    {

        $user = JFactory::getUser();
        $gruppi_appartenenza_utente = JAccess::getGroupsByUser($user->id);
        $società_venditrici = array();


        $query_config = $this->_db->getQuery(true)
            ->select('config_value')
            ->from('#__gg_configs')
            ->where("config_key='id_gruppo_venditori'");

        $this->_db->setQuery($query_config);
        $id_gruppo_venditori = $this->_db->loadResult();


        if (in_array($id_gruppo_venditori, $gruppi_appartenenza_utente)) {
//            echo 'sei un venditore';
            // filtro i gruppi a cui appartiene l'utente per ricavare le piattaforme a cui è associato

            $query_config = $this->_db->getQuery(true)
                ->select('config_value')
                ->from('#__gg_configs')
                ->where("config_key='id_gruppo_piattaforme'");

            $this->_db->setQuery($query_config);
            $id_gruppo_piattaforme = $this->_db->loadResult();


            // ricavo tra i gruppi di appartenenza dell'utente quelli che corrispondono a delle piattaforme
            $query = $this->_db->getQuery(true)
                ->select('g.id as value, d.alias as text')
                ->from('cis19_usergroups as g')
                ->join('inner', 'cis19_user_usergroup_map as m ON  g.id = m.group_id')
                ->join('inner', 'cis19_usergroups_details as d ON g.id = d.group_id')
                ->where("g.parent_id=" . $id_gruppo_piattaforme)
                ->where("m.user_id=" . $user->id);


            $this->_db->setQuery($query);
            $società_venditrici = $this->_db->loadObjectList();

        } else {
            echo "l'utente loggato non appartiene al gruppo venditore, non può generare coupon";
        }

        return $società_venditrici;

    }

}
