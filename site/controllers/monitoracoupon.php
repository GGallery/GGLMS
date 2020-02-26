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
        $this->_db = JFactory::getDbo();


    }

    public function getcouponlist()
    {

        $filter_params = JRequest::get($_POST);
        $data = $this->get_filterd_coupon_list($filter_params);

        echo json_encode($data);
        $this->_japp->close();


    }

    public function get_filterd_coupon_list($filter_params)
    {

        try {


            $offset = $filter_params["offset"];
            $limit = $filter_params["limit"];


            // count totale senza limit per ottenere il numero di righe totali
            $total_count_query = $this->_db->getQuery(true)
                ->select('count(*)')
                ->from('#__gg_coupon AS c');

            $total_count_query = $this->_filter_query($total_count_query, $filter_params);
            $this->_db->setQuery($total_count_query);
            $count = $this->_db->loadResult();


            if ($count > 0) {

                $query = $this->_db->getQuery(true)
                    ->select("c.*, coalesce(CONCAT(cm.cb_nome, ' ', cm.cb_cognome), cm.id) as user , u.titolo as corso, case when DATE_ADD(c.data_utilizzo,INTERVAL c.durata DAY) < NOW() then 1 else 0 END as scaduto")
                    ->from('#__gg_coupon AS c');


                $query = $this->_filter_query($query, $filter_params);

                               $query->setlimit($offset, $limit);

                $this->_db->setQuery($query);
                $results = $this->_db->loadAssocList();


            } else {

                $results = array();

            }


            $results['rowCount'] = $count;
            $results['query'] = ((string)$query);
            return $results;


        } catch (Exception $e) {
            DEBUGG::log($e->getMessage(), 'error get_filterd_coupon_list', 0, 0, 0);

        }


    }

    public function _filter_query($query, $params)
    {
        $id_gruppo_corso = $params['id_gruppo_corso'];
        $id_gruppo_societa = $params['id_gruppo_azienda'];
        $stato = $params['stato'];
        $coupon = $params['coupon'];
        $venditore = $params["venditore"];
        $utente = $params["utente"];


        // info corso
        $query = $query->join('inner', '#__gg_usergroup_map AS gm ON c.id_gruppi = gm.idgruppo');
        $query = $query->join('inner', '#__gg_unit AS u ON u.id = gm.idunita');

        if ($id_gruppo_corso != -1) {
            $query = $query->where('c.id_gruppi = ' . $id_gruppo_corso);
        }

        if ($id_gruppo_societa != -1) {
            $query = $query->where('c.id_societa = ' . $id_gruppo_societa);
        }

        if ($coupon != '') {
            $query = $query->where("c.coupon like '%" . $coupon . "%'");
        }

        if ($venditore != '') {
            $query = $query->where("c.venditore like '%" . $venditore . "%'");
        }

        if ($utente != '') {

            $query = $query->where("(cm.cb_nome like '%" . $utente . "%'  OR cm.cb_cognome like '%" . $utente . "%')", "AND");

        }

        switch ($stato) {
            case -1:
                // qualsiasi
                $query = $query->join('left', '#__comprofiler as cm on cm.id = c.id_utente');
                break;

            case 0:
                // non assegnati ad utente
                $query = $query->join('left', '#__comprofiler as cm on cm.id = c.id_utente');
                $query = $query->where('c.id_utente is null');

                break;


            case 1:
                // assegnati ad un utente
                $query = $query->join('inner', '#__comprofiler as cm on cm.id = c.id_utente');

                break;

            case 2:
                // scaduti = assegnati ad un utente e  data_utilizzo + durata < oggi
                $query = $query->join('inner', '#__comprofiler as cm on cm.id = c.id_utente');
                $query = $query->where('DATE_ADD(c.data_utilizzo,INTERVAL c.durata DAY) < NOW() ');

                break;

        }


        $query = $query->order('c.data_utilizzo DESC');
        return $query;
    }

    public function exportCsv()
    {
        $filter_params = JRequest::get($_POST);
        $data = $this->get_filterd_coupon_list($filter_params);
        $this->createCSV($data, $filter_params["columns"]);
    }

    public function createCSV($rows, $csv_columns)
    {


        // unset del totale delle righe perchè è al livello superiore rispetto all'array dei dati e non mi interessa esportatlo
        unset($rows["rowCount"]);
        // colonne da esportare
        $csv_columns_list = explode(',', $csv_columns);

        utilityHelper::_export_data_csv('monitora_coupon', $rows, $csv_columns_list);
        $this->_japp->close();
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
