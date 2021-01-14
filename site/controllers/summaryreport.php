getData<?php
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


        $this->model_user = new gglmsModelUsers();
        $this->lista_piattaforme = $this->get_piattaforma_filter();
        $this->lista_aziende = $this->get_azienda_filter();

    }

    public function get_piattaforma_filter()
    {

        $piattaforme = $this->model_user->get_user_piattaforme($this->_user->id);
        $p_list = array();
        foreach ($piattaforme as $p) {

            array_push($p_list, $p->value);
        }

        return $p_list;
    }

    public function get_azienda_filter()
    {

        $a_list = array();
        $tutor_az = $this->model_user->is_tutor_aziendale($this->_user->id);
        if ($tutor_az) {
            // se è tutor aziendale sggiungo filtro per società
            $azienda = $this->model_user->get_user_societa($this->_user->id, true);


            foreach ($azienda as $a) {

                array_push($a_list, $a->id);
            }

            return $a_list;

        };
    }


    public function getData()
    {
        $params = JRequest::get($_POST);

        $page = $params["page"];
        $take = $params["take"];
        $filter = $params["filter"];
        $sort = $params["sort"];


        ////////////////////// filter piattaforma //////////////

        $this->_db->setQuery('SET SQL_BIG_SELECTS=1');
        $this->_db->execute();


        $total_count_query = $this->_db->getQuery(true)
            ->select('count(*)')
            ->from('#__view_report')
            ->where("id_piattaforma  in (" . implode(', ', $this->lista_piattaforme) . ")");


        ////////////////////////// filter aziendale ///////////////////////


        $tutor_az = $this->model_user->is_tutor_aziendale($this->_user->id);
        if ($tutor_az) {
            // se è tutor aziendale aggiungo filtro per società
            $total_count_query->where("id_azienda  in (" . implode(', ', $this->lista_aziende) . ")");
        }


        //////////////////////////////////////////////////

        $total_count_query = $this->_filter_query($total_count_query, $filter);
        $this->_db->setQuery($total_count_query);
        $count = $this->_db->loadResult();

        if ($count > 0) {

            $query = $this->_db->getQuery(true)
                ->select('*')
                ->from('#__view_report')
                ->where("id_piattaforma  in (" . implode(', ', $this->lista_piattaforme) . ")");


            if ($tutor_az) {
                $query->where("id_azienda  in (" . implode(', ', $this->lista_aziende) . ")");
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

    public function get_tracklog_details()
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
        foreach ($columns as $c) {
            if (!array_key_exists($c, $data1)) {

                $obj = new stdClass();
                $obj->id_contenuto = -1;
                $obj->last_visit = null;
                $obj->permanenza = 0;
                $obj->visualizzazioni = 0;
                $obj->titolo_contenuto = $c;
                array_push($data, $obj);
            }
        }


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


    public function get_user_actions()
    {

        $res["delete"] = $this->can_delete_coupon();
        $res["reset"] = $this->can_reset_coupon();
        $res["tutor_az"] = $this->is_tutor_az();

        echo(json_encode($res));
        $this->_japp->close();
    }

    public function is_tutor_az()
    {

        $model = new gglmsModelUsers();
        $res = ($model->is_tutor_aziendale($this->_user->id)) ? true : false;

        return $res;
    }

    public function can_delete_coupon()
    {

        // possono cancellare i coupon solo superadmin e tutor piattaforma (e solo coupon liberi)
        $model = new gglmsModelUsers();
        $res = ($model->is_tutor_piattaforma($this->_user->id) || $model->is_user_superadmin($this->_user->id)) ? true : false;

        return $res;
    }

    public function can_reset_coupon()
    {

        // possono resettare i coupon solo superadmin(e solo coupon occupati)
        $model = new gglmsModelUsers();
        $res = ($model->is_user_superadmin($this->_user->id)) ? true : false;

        return $res;

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


    public function do_delete_coupon()
    {

        // possono resettare i coupon solo superadmin(e solo coupon occupati)

        $params = JRequest::get($_POST);
        $coupon = $params["coupon"];

        $query_check = $this->_db->getQuery(true)
            ->select('id_utente')
            ->from('#__gg_coupon')
            ->where('coupon = "' . $coupon . '"');

        $this->_db->setQuery($query_check);
        $coupon_user = $this->_db->loadResult();

        if ($coupon_user == null) {

            $query = $this->_db->getQuery(true);
            $conditions = array(
                $this->_db->quoteName('coupon') . ' = "' . $coupon . '"',
            );

            $query->delete($this->_db->quoteName('#__gg_coupon'));
            $query->where($conditions);
            $this->_db->setQuery($query);
            $result_ = $this->_db->execute();


            if ($result_) {
                $result['success'] = 1;
                $result['message'] = 'Il coupon é stato eliminato con successo';
            } else {
                $result['success'] = -1;
                $result['message'] = 'Oops si è verificato un errore';
            }


        } else {
            $result['success'] = -1;
            $result['message'] = 'Il coupon non è libero';

        }

        echo(json_encode($result));

        $this->_japp->close();


    }


    public function do_reset_coupon()
    {

        // possono resettare i coupon solo superadmin(e solo coupon occupati)

        $params = JRequest::get($_POST);
        $coupon = $params["coupon"];

        try {


            // check coupon
            $query_check = $this->_db->getQuery(true)
                ->select('id_utente , id_gruppi')
                ->from('#__gg_coupon')
                ->where('coupon = "' . $coupon . '"');

            $this->_db->setQuery($query_check);
            $coupon_data = $this->_db->loadAssoc();

            if ($coupon_data['id_utente'] !== null) {


                // 1) update coupon --> set data utilizzo  e id utente  null
                $query = $this->_db->getQuery(true);

                // Fields to update.
                $fields = array(
                    $this->_db->quoteName('id_utente') . ' = null',
                    $this->_db->quoteName('data_utilizzo') . ' = null'
                );

                // Conditions for which records should be updated.
                $conditions = array(
                    $this->_db->quoteName('coupon') . ' = "' . $coupon . '"',
                );

                $query->update($this->_db->quoteName('#__gg_coupon'))->set($fields)->where($conditions);

                $this->_db->setQuery($query);
                $result_reset = $this->_db->execute();


                // 2) disiscrivi utente da gruppo corso

                $query_delete = $this->_db->getQuery(true);
                $conditions = array(
                    $this->_db->quoteName('user_id') . ' = "' . $coupon_data['id_utente'] . '"',
                    $this->_db->quoteName('group_id') . ' = "' . $coupon_data['id_gruppi'] . '"',
                );
//
                $query_delete->delete($this->_db->quoteName('#__user_usergroup_map'));
                $query_delete->where($conditions);
                $this->_db->setQuery($query_delete);
                $result_delete = $this->_db->execute();


                if ($result_reset && $result_delete) {
                    $result['success'] = 1;
                    $result['message'] = 'Il coupon é stato resettato con successo';
                } else {
                    $result['success'] = -1;
                    $result['message'] = 'Oops si è verificato un errore';
                }


            } else {
                $result['success'] = -1;
                $result['message'] = 'Il coupon  è libero, non puoi resettarlo';

            }

            echo(json_encode($result));

            $this->_japp->close();
        }
        catch(Exception $e){

            $result['success'] = -1;
            $result['message'] = 'Oops si è verificato un errore';
            echo(json_encode($result));
            $this->_japp->close();
        }


    }





}

