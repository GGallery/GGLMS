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
    public $_config;


    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();

        $this->_user = JFactory::getUser();
        $this->_db = JFactory::getDbo();
        $this->_config = new gglmsModelConfig();


    }

    public function getcouponlist()
    {

        //$filter_params = JRequest::get($_POST);
        $filter_params = JRequest::get($_GET);
        $data = $this->get_filterd_coupon_list($filter_params);

        echo json_encode($data);
        $this->_japp->close();

    }

    public function get_filterd_coupon_list($_call_params)
    {

        try {

            $_rows = array();
            $_ret = array();

            $_offset = (isset($_call_params['offset']) && $_call_params['offset'] != "") ? $_call_params['offset'] : 0;
            $_limit = (isset($_call_params['limit']) && $_call_params['limit'] != "") ? $_call_params['limit'] : 10;
            $_sort = (isset($_call_params['sort']) && $_call_params['sort'] != "") ? $_call_params['sort'] : null;
            $_order = (isset($_call_params['order']) && $_call_params['order'] != "") ? $_call_params['order'] : null;

            // count totale senza limit per ottenere il numero di righe totali
            $total_count_query = $this->_db->getQuery(true)
                ->select('count(*)')
                ->from('#__gg_coupon AS c');

            $total_count_query = $this->_filter_query($total_count_query, $_call_params);
            $this->_db->setQuery($total_count_query);
            $count = $this->_db->loadResult();

            //controllo se ho dati
            if ($count > 0) {

                $query = $this->_db->getQuery(true)
                    ->select("c.*, coalesce(CONCAT(cm.cb_nome, ' ', cm.cb_cognome), cm.id) as user , u.titolo as corso, case when DATE_ADD(c.data_utilizzo,INTERVAL c.durata DAY) < NOW() then 1 else 0 END as scaduto")
                    ->from('#__gg_coupon AS c');


                $query = $this->_filter_query($query, $_call_params);

                // ordinamento per colonna - di default per id utente
                if (!is_null($_sort)
                    && !is_null($_order)) {
                    $query = $query->order($_sort . ' ' . $_order);
                }
                else
                    $query = $query->order('cm.id DESC');

                $this->_db->setQuery($query,$_offset,$_limit);
                $results = $this->_db->loadAssocList();

            } else {
                // se nessun risultato restituisco un array vuoto
                $results = array();

            }

            $_rows['rowCount'] = $count;
            //controllo i coupon scaduti da colorare
            if (isset($results)) {
                foreach ($results as $_key_coupon => $_coupon) {

                    $color_cell = ($_coupon['scaduto'] == 1) ? 'color:red;' : '';
                    foreach ($_coupon as $key => $value) {


                        $_ret[$_key_coupon][$key] = <<<HTML
                            <span style="{$color_cell}" >{$value}</span>
HTML;

                    }
                }

            }

        } catch (Exception $e) {
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            $_ret['error'] = $e->getMessage();
        }

        $_rows['rows'] = $_ret;
        return $_rows;
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


       // $query = $query->order('c.data_utilizzo DESC');
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

    public function get_var_monitora_coupon() {

        $_ret = array();

        $model = new gglmsModelUsers();
        $is_tutor_az = $model->is_tutor_aziendale($this->_user->id);

        $_ret['is_tutor_aziendale'] = $is_tutor_az;

        $show_disattiva_coupon = $this->_config->getConfigValue('monitora_coupon_disattiva_coupon');
        $_ret['show_disattiva_coupon'] = $show_disattiva_coupon;

        echo json_encode($_ret);
        $this->_japp->close();

    }

    public function disattivazione_coupon() {

        $_ret = array();
        $filter_params = JRequest::get($_GET);

        try {

            if (!isset($filter_params["codice_coupon"])
                || $filter_params["codice_coupon"] == "")
                throw new Exception("Nessun codice coupon specificato!", 1);

            $coupon = new gglmsModelcoupon();
            $_disattiva = $coupon->disattiva_coupon($filter_params["codice_coupon"]);

            if (!is_array($_disattiva))
                throw new Exception($_disattiva, 1);

            $_ret['success'] = "tuttook";

        }
        catch (Exception $e) {
            $_msg = __FUNCTION__ . " errore: " . $e->getMessage();
            $_ret['error'] = $_msg;
            DEBUGG::log($e->getMessage(), __FUNCTION__, 0, 1, 0);
        }

        echo json_encode($_ret);
        $this->_japp->close();

    }


}
