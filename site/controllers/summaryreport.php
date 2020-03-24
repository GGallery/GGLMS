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
require_once JPATH_COMPONENT . '/models/report.php';

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
        $params = JRequest::get($_POST);

        $page = $params["page"];
        $take = $params["take"];
        $filter = $params["filter"];
        $sort = $params["sort"];


        $model_user = new gglmsModelUsers();
        $piattaforme = $model_user->get_user_piattaforme($this->_user->id);
        $p_list = array();
        foreach ($piattaforme as $p) {

            array_push($p_list, $p->value);
        }


        $total_count_query = $this->_db->getQuery(true)
            ->select('count(*)')
            ->from('#__view_report')
            ->where("id_piattaforma  in (" . implode(', ', $p_list) . ")");


        ////////////////////////// gestione tutor aziendale ///////////////////////
//        aggiungo filtro per azienda se tutor aziendale
        $a_list = array();
        $tutor_az = $model_user->is_tutor_aziendale($this->_user->id);
        if ($tutor_az) {
            // se è tutor aziendale sggiungo filtro per società
            $azienda = $model_user->get_user_societa($this->_user->id, true);


            foreach ($azienda as $a) {

                array_push($a_list, $a->id);
            }
            $total_count_query->where("id_azienda  in (" . implode(', ', $a_list) . ")");
        }

        //////////////////////////////////////////////////
        ///
        $total_count_query = $this->_filter_query($total_count_query, $filter);
        $this->_db->setQuery($total_count_query);
        $count = $this->_db->loadResult();

        if ($count > 0) {

            $query = $this->_db->getQuery(true)
                ->select('*')
                ->from('#__view_report')
                ->where("id_piattaforma  in (" . implode(', ', $p_list) . ")");


            if ($tutor_az) {
                $query->where("id_azienda  in (" . implode(', ', $a_list) . ")");
            }


            $query = $this->_filter_query($query, $filter);


            if ($page == 1) {
                $query->setLimit($take);
            } else {
                $query->setLimit($take, ($page - 1) * $take);
            }


            if ($sort) {
                $query->order($sort[0]['field'] . ' ' . $sort[0]['dir']);
            }

            $this->_db->setQuery($query);
            $data = $this->_db->loadAssocList();


            $result['data'] = $data;
            $result['total'] = $count;
            $result['sort'] = $sort[0]['field'] . ' ' . $sort[0]['dir'];

            $result['query'] = (string)$query;
            $result['filter'] = $filter;
            $result["page"] = $params["page"];

        } else {

            $result['data'] = array();
            $result['query'] = '';
            $result['filter'] = $filter;
            $result['total'] = 0;
        }


        echo json_encode($result);
        $this->_japp->close();

    }


    public function _filter_query($query, $filter)
    {
        foreach ($filter["filters"] as $f) {


            switch ($f["operator"]) {
                case "eq":
                    $query = $query->where($f['field'] . " = '" . $f['value'] . "'");
                    break;
                case "like":
                    $query = $query->where($f['field'] . " like '%" . $f['value'] . "%'");
                    break;
                case "lte":
                    // lowerthan or equal --> campo data
                    $query = $query->where($f['field'] . " <= '" . $f['value'] . "'");
                    break;
                case "gte":
                    // greater than or equal --> campo data
                    $query = $query->where($f['field'] . " >= '" . $f['value'] . "'");
                    break;
                case "isnull":
                    $query = $query->where($f['field'] . " is null");
                    // campo data
                    break;
                case "isnotnull":
                    $query = $query->where($f['field'] . " is not null");
                    break;

            }


        }
        return $query;
    }


    public function is_tutor_aziendale()
    {

        $model = new gglmsModelUsers();
        $is_tutor_az = $model->is_tutor_aziendale($this->_user->id);

        echo(json_encode($is_tutor_az));
        $this->_japp->close();

        // is_tutor_aziendale

    }

    public function getDetails()
    {
        $filter_params = JRequest::get($_POST);
        $id_gruppo_societa = $filter_params['id_gruppo_azienda'];
        $id_corso = $filter_params['id_corso'];
        $id_user = $filter_params['id_user'];

        $columns = $this->buildColumnsforContenutiView($id_corso);


        // questa query tira fuori solo i titoli dei contenuti visitati, a me servono tutti
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
        $data1 = $this->_db->loadAssocList('titolo_contenuto');

        // contenuti del corso mai visualizzati dall'utente,nella query non li becco perchè in __gg_report ci sono solo quelli dove l'utente ha fatto almeno un accesso
        foreach ($columns as $c){
            if(!array_key_exists( $c,$data1)){

                $obj = new stdClass();
                $obj->id_contenuto = -1;
                $obj->last_visit = null;
                $obj->permanenza = 0;
                $obj->visualizzazioni = 0;
                $obj->titolo_contenuto = $c;
                array_push($data, $obj);
            }
        }

//        $res['data'] = $data;
//        $res['data1'] = $data1;
//        echo json_encode($res);

        echo json_encode($data);

        $this->_japp->close();
    }

    public function get_user_detail()
    {

        $params = JRequest::get($_POST);
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


    private function buildColumnsforContenutiView($id_corso)
    {

        $reportObj = new gglmsModelReport();
        $contenuti = $reportObj->getContenutiArrayList($id_corso);

        $columns = [];
        foreach ($contenuti as $contenuto) {
            array_push($columns, $contenuto['titolo']);
        }
        return $columns;
    }

}

