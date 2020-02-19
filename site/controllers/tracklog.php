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
class gglmsControllerTracklog extends JControllerLegacy
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

    public function getData()
    {
        $filter_params = JRequest::get($_POST);
        $data = $this->get_tracklog_main_data($filter_params);

        echo json_encode($data);
        $this->_japp->close();
    }


    public function get_tracklog_main_data($filter_params)
    {

        try {


            $offset = $filter_params["offset"];
            $limit = $filter_params["limit"];
            $id_gruppo_societa = $filter_params['id_gruppo_azienda'];
            $stato = $filter_params['stato'];


            // count totale senza limit per ottenere il numero di righe totali
            $query = $this->_db->getQuery(true)
//                ->select('count(*)')
                ->select("u.id_user,  coalesce(CONCAT(u.nome, ' ', u.cognome), u.id_user) as user ,c.titolo as corso, report.stato, report.data_inizio, report.data_fine")
                ->from('#__gg_view_stato_user_corso as report')
                ->join('inner', '#__gg_unit as c on report.id_corso = c.id')
                ->join('inner', '#__gg_report_users as u on report.id_anagrafica = u.id')
                ->join('inner', '#__user_usergroup_map as um  on um.user_id = u.id_user')
                ->where('um.group_id =' . $id_gruppo_societa);

            if ($stato != -1) {
                $query = $query->where('report.stato =' . $stato);
            }


            $query->setlimit($offset, $limit);
            $this->_db->setQuery($query);
            $data = $this->_db->loadAssocList();

            $results["data"] = $data;
            $results['rowCount'] =  count($results);
            $results['query'] = ((string)$query);
            return $results;


        } catch (Exception $e) {
            DEBUGG::log($e->getMessage(), 'error get_filterd_coupon_list', 0, 0, 0);

        }


    }

//
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
//
//    public function exportCsv()
//    {
//        $filter_params = JRequest::get($_POST);
//        $data = $this->get_filterd_coupon_list($filter_params);
//        $this->createCSV($data, $filter_params["columns"]);
//    }
//
//    public function createCSV($rows, $csv_columns)
//    {
//
//
//        // unset del totale delle righe perchè è al livello superiore rispetto all'array dei dati e non mi interessa esportatlo
//        unset($rows["rowCount"]);
//        // colonne da esportare
//        $csv_columns_list = explode(',', $csv_columns);
//
//        utilityHelper::_export_data_csv('monitora_coupon', $rows, $csv_columns_list);
//        $this->_japp->close();
//    }
//
//    public function is_tutor_aziendale()
//    {
//
//        $model = new gglmsModelUsers();
//        $is_tutor_az = $model->is_tutor_aziendale($this->_user->id);
//
//        echo(json_encode($is_tutor_az));
//        $this->_japp->close();
//
//        // is_tutor_aziendale
//
//    }


}
