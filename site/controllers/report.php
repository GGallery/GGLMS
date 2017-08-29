<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/models/contenuto.php';
require_once JPATH_COMPONENT . '/models/unita.php';
require_once JPATH_COMPONENT . '/models/users.php';

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerReport extends JControllerLegacy
{
    protected $_db;
    private $_app;
    private $params;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_app = JFactory::getApplication();
        $this->params = $this->_app->getParams();
        $this->_db = JFactory::getDbo();

        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/debugg.css');

    }

    //INGRESSO
    public function  sync(){

        if($this->sync_report_users()){
            echo "sync_report_users completato <br>";
            if($this->sync_report()) {
                echo "sync_report completato <br> ";
                $this->updateconfig();
            }
        }

        $this->_app->close();

    }


    private function updateconfig(){

        try{
            $query = $this->_db->getQuery(true)
                ->update('#__gg_configs	')
                ->set('config_value = now()')
                ->where('config_key = "data_sync"');

            $this->_db->setQuery($query);
            $this->_db->execute();

            utilityHelper::setComponentParam('data_sync', date('Y-m-d G:i:s'));

            return "1";
        }
        catch (Exception $e){
            DEBUGG::log($e, 'updateconfig',1);
        }
    }

    //REPORT TRACCIAMENTO
    public function sync_report(){

        $scormvar_list		= $this->_getScormvarsVariation();
        $quizdeluxe_list 	= $this->_getQuizDeluxeVariation();

        $list = array_merge($scormvar_list, $quizdeluxe_list);

        foreach ($list as $item){
            $data = new Stdclass();


            $data->id_utente = $item->id_utente;

            $data->id_contenuto = $item->id_contenuto;

            $modelcontenuto = new gglmsModelContenuto();
            $contenuto = $modelcontenuto->getContenuto($item->id_contenuto);

            $stato = $contenuto->getStato($data->id_utente);

            $data->data = $stato->data;
            $data->stato = $stato->completato;
            $data->visualizzazioni = $stato->visualizzazioni;

            $data->id_unita = $contenuto->getUnitPadre();

            $modelunita = new gglmsModelUnita();
            $unita = $modelunita->getUnita($data->id_unita);
            $corso = $unita->find_corso($data->id_unita);

            $data->id_corso = $corso->id;
            $data->id_event_booking = $corso->id_event_booking;

            $data->id_anagrafica = $this->_getAnagraficaid($data->id_utente, $data->id_event_booking);

            $this->store_report($data);

            unset($modelcontenuto);
            unset($contenuto);
            unset($modelunita);
            unset($unita);
            unset($data);
        }


        return true;
    }

    private function _getScormvarsVariation(){

        try {
            $query = $this->_db->getQuery(true)
                ->select('DISTINCT s.scoid as id_contenuto, s.userid as id_utente')
                ->from('#__gg_scormvars as s');

              if($this->params->get('data_sync'))
                $query->where('timestamp > "' . $this->params->get('data_sync').'"');

            $this->_db->setQuery($query);
            $data = $this->_db->loadObjectList();


            return $data;
        }
        catch (Exception $e) {
            DEBUGG::query($query, '_getScormvarsVariation', 1);
        }
    }

    private function _getQuizDeluxeVariation(){

        try {
            $query = $this->_db->getQuery(true)
                ->select('DISTINCT  c.id as id_contenuto, q.c_student_id as id_utente')
                ->from('#__quiz_r_student_quiz as q')
                ->join('inner','#__gg_contenuti as c on q.c_quiz_id = c.id_quizdeluxe');

                 if($this->params->get('data_sync'))
                   $query->where('c_date_time > "' . $this->params->get('data_sync').'"');

            $this->_db->setQuery($query);
            $data = $this->_db->loadObjectList();


            return $data;
        }
        catch (Exception $e) {
            DEBUGG::query($query, '_getScormvarsVariation', 1);
        }
    }

    private function _getAnagraficaid($user_id, $event_id){

        try{
            $query = $this->_db->getQuery(true);
            $query->select('id');
            $query->from('#__gg_report_users as ru');
            $query->where('ru.id_user = '. $user_id);
            $query->where('ru.id_event_booking = '. $event_id);
            $query->limit('1');

            $this->_db->setQuery($query);
            $res = $this->_db->loadResult();

            return $res;

        }catch (Exception $e){
            DEBUGG::error($e, 'error get Anagrafica',1);
        }

    }

    private function store_report($data){

        try {

            $query = "
    INSERT INTO #__gg_report (id_corso, id_event_booking,id_unita, id_contenuto,  id_utente , id_anagrafica, stato, visualizzazioni, data ) 
    VALUES ($data->id_corso, $data->id_event_booking, $data->id_unita,$data->id_contenuto,$data->id_utente,$data->id_anagrafica,$data->stato, $data->visualizzazioni, '$data->data')";
            $query .= "ON DUPLICATE KEY UPDATE stato = $data->stato , visualizzazioni= $data->visualizzazioni, data='$data->data'  ";

            $this->_db->setQuery($query);
            $this->_db->execute();

        }catch (Exception $e){
            DEBUGG::error($e, 'error store report', 1);
        }
    }

    //REPORT UTENTI
    public function sync_report_users() {

        try {
            $users = $this->get_users_id($this->params->get('data_sync'));

            foreach ($users as $user) {

                $modelUser = new gglmsModelUsers();
                if (!$user->event_id)
                    $user->event_id = 0;

                $tmpuser = $modelUser->get_user($user->id, $user->event_id);

                $tmp = new stdClass();
                $tmp->id = $user->id;
                $tmp->id_event_booking = $user->event_id;
                $tmp->id_user = $user->id;
                $tmp->nome = $this->_db->quote($tmpuser->nome);
                $tmp->cognome = $this->_db->quote($tmpuser->cognome);
                $tmp->fields = $this->_db->quote(json_encode($tmpuser));

                $this->store_report_users($tmp);
            }

            return true;
        }catch (Exception $e){
            DEBUGG::log($e, 'error sync_report_users', 1);
        }
    }

    public function get_users_id($from_date = null )
    {


        switch ($this->params->get('integrazione')) {
            case 'cb':
                $data =  $this->get_users_cb($from_date);
                break;

            case 'eb':
                $data =  $this->get_users_eb($from_date);
                break;

            default:
                $data =  $this->get_users_joomla($from_date);
                break;
        }
        return $data;
    }

    private function get_users_cb($from_date){

        try {
            $query = $this->_db->getQuery(true)
                ->select('u.id, 0 as event_id')
                ->from('#__comprofiler as r')
                ->join('inner', '#__users as u on u.id = r.id');



            if ($from_date) {
                $query->where('u.registerDate > ' . $this->_db->quote($from_date).
                    ' OR '.' r.lastupdatedate > ' . $this->_db->quote($from_date));
            }

            $this->_db->setQuery($query);
            $registrants = $this->_db->loadObjectList();


            return $registrants;
        }catch (Exception $e){

            DEBUGG::error($e, 'error get user cb', 1);
        }

    }

    private function get_users_eb($from_date){
        try {
            $query = $this->_db->getQuery(true)
                ->select('distinct user_id as id, event_id')
                ->from('#__eb_registrants as r');


            if($from_date) {
                $query->Where('r.register_date > ' . $this->_db->quote($from_date));
            }

            $this->_db->setQuery($query);
            $registrants = $this->_db->loadObjectList();

            return $registrants;
        }catch (Exception $e){
            DEBUGG::query($query, 'query error in get_users_eb');
            DEBUGG::error($e, 'error in get user eb', 1);

        }
    }

    private function get_users_joomla($from_date){
        try {
            $query = $this->_db->getQuery(true)
                ->select('u.id, 0 as event_id')
                ->from('#__users as u');

            if($from_date) {
                $query->Where('u.registerDate > ' . $from_date);
            }

            $this->_db->setQuery($query);
            $registrants = $this->_db->loadObjectList();

            return $registrants;
        }catch (Exception $e){
            DEBUGG::query($query, 'query error in get_users_joomla');
            DEBUGG::error($e, 'error in get_users_joomla', 1);

        }
    }

    private function store_report_users($data){

        try {
            $query = "
            INSERT INTO #__gg_report_users (id_event_booking,id_user, nome, cognome, fields) 
            VALUES ($data->id_event_booking, $data->id_user, $data->nome,$data->cognome,$data->fields)";
            $query .= " ON DUPLICATE KEY UPDATE fields = $data->fields";

            $this->_db->setQuery($query);
            $this->_db->execute();

        }catch (Exception $e){
            DEBUGG::error($e, 'error store report', 1);
        }


    }






}
