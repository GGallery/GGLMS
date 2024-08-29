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
    public $_content_dictionary;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();

        $this->_user = JFactory::getUser();
        $this->_db = &JFactory::getDbo();

        $this->_content_dictionary = array();


    }

    public function getData()
    {
        $filter_params = $this->_japp->input->getArray($_POST);
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
            $id_corso = $filter_params['id_corso'];
            $stato = $filter_params['stato'];
            $utente = $filter_params['utente'];



            $query = $this->_db->getQuery(true)
                ->select("u.id_user,  coalesce(CONCAT(u.nome, ' ', u.cognome), u.id_user) as user ,c.titolo as corso,c.id as id_corso ,report.stato, report.data_inizio, report.data_fine")
                ->from('#__gg_view_stato_user_corso as report')
                ->join('inner', '#__gg_unit as c on report.id_corso = c.id')
                ->join('inner', '#__gg_report_users as u on report.id_anagrafica = u.id')
                ->join('inner', '#__user_usergroup_map as um  on um.user_id = u.id_user')
                ->where('um.group_id =' . $id_gruppo_societa)
                ->where('c.id =' . $id_corso);


            if ($stato != -1) {
                $query = $query->where('report.stato =' . $stato);
            }

            if ($utente != '') {
                $query = $query->where("(u.nome like '%" . $utente . "%'  OR u.cognome like '%" . $utente . "%')", "AND");
            }

            $query->setlimit($offset, $limit);
            $this->_db->setQuery($query);
            $data = $this->_db->loadAssocList();

            $results["data"] = $data;
            $results['rowCount'] = count($results);
            $results['query'] = ((string)$query);
            return $results;


        } catch (Exception $e) {
            DEBUGG::log($e->getMessage(), 'error get_tracklog_main_data', 0, 0, 0);

        }


    }

    public function getDetails()
    {
        $filter_params = $this->_japp->input->getArray($_POST);
        $id_gruppo_societa = $filter_params['id_gruppo_azienda'];
        $id_corso = $filter_params['id_corso'];
        $id_user = $filter_params['id_user'];


        $query = $this->_db->getQuery(true)
            ->select('r.id_contenuto, r.data as last_visit ,r.permanenza_tot as permanenza ,r.visualizzazioni as visualizzazioni,contenuti.titolo as titolo_contenuto')
            ->from('#__gg_view_stato_user_corso as report')
            ->join('inner', '#__gg_unit AS c ON report.id_corso = c.id')
            ->join('inner', '#__gg_report_users AS u ON report.id_anagrafica = u.id')
            ->join('inner', '#__user_usergroup_map AS um ON um.user_id = u.id_user ')
            ->join('inner', '#__gg_report as r on r.id_corso = c.id and r.id_utente = u.id_user')
            ->join('inner', '#__gg_contenuti as contenuti on contenuti.id = r.id_contenuto')
            ->join('inner', '#__gg_unit_map as umap on umap.idcontenuto = contenuti.id')
            ->where('um.group_id =' . $id_gruppo_societa)
            ->where('c.id =' . $id_corso)
            ->where('u.id_user =' . $id_user);


        $this->_db->setQuery($query);
        $data = $this->_db->loadAssocList();

        echo json_encode($data);
        $this->_japp->close();
    }

    public function createCSV($rows, $csv_columns)
    {

        $csv_columns_list = explode(',', $csv_columns);

        utilityHelper::_export_data_csv('monitora_coupon', $rows, $csv_columns_list);
        $this->_japp->close();
    }


    public function exportCsv()
    {


        $filter_params = $this->_japp->input->getArray($_POST);
        $id_gruppo_societa = $filter_params['id_gruppo_azienda'];
        $id_corso = $filter_params['id_corso'];
        $stato = $filter_params['stato'];
        $utente = $filter_params['utente'];


        $query = $this->_db->getQuery(true)
            ->select("coalesce(CONCAT(u.nome, '', u.cognome), u.id_user) AS user, r.data as last_visit ,r.permanenza_tot as permanenza ,r.visualizzazioni as visualizzazioni,contenuti.titolo as titolo_contenuto")
            ->from('#__gg_view_stato_user_corso as report')
            ->join('inner', '#__gg_unit AS c ON report.id_corso = c.id')
            ->join('inner', '#__gg_report_users AS u ON report.id_anagrafica = u.id')
            ->join('inner', '#__user_usergroup_map AS um ON um.user_id = u.id_user ')
            ->join('inner', '#__gg_report as r on r.id_corso = c.id and r.id_utente = u.id_user')
            ->join('inner', '#__gg_contenuti as contenuti on contenuti.id = r.id_contenuto')
            ->join('inner', '#__gg_unit_map as umap on umap.idcontenuto = contenuti.id')
            ->where('um.group_id =' . $id_gruppo_societa)
            ->where('c.id =' . $id_corso);


        if ($stato != -1) {
            $query = $query->where('report.stato =' . $stato);
        }

        if ($utente != '') {
            $query = $query->where("(u.nome like '%" . $utente . "%'  OR u.cognome like '%" . $utente . "%')", "AND");
        }


        $this->_db->setQuery($query);
        $data = $this->_db->loadAssocList();

        $this->createCSV($data, ['user', 'last_visit', 'permanenza', 'visualizzazioni', 'titolo_contenuto']);


    }

}
