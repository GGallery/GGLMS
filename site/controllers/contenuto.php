<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/models/report.php';

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerContenuto extends JControllerLegacy
{

    public function updateTrack() {

        $japp = JFactory::getApplication();


        $secondi = JRequest::getVar('secondi');
        $stato = JRequest::getVar('stato');
        $id_elemento = JRequest::getVar('id_elemento');
        $id_utente = JRequest::getVar('id_utente');
        // per aggiornamento gg_log
        $uniquid = JRequest::getVar('uniquid');

        $user =  JFactory::getUser();
        $user_id = $user->get('id');

        if (is_null($user_id)
            || $user_id == ""
            || (int) $user_id == 0)
            $user_id = $id_utente;

        $modelstato = new gglmsModelStatoContenuto();
        $tmp = new stdClass();

        $tmp->scoid = $id_elemento;
        $tmp->userid = $user_id;

        $log_arr = array(
                        'user_id' => $user_id,
                        'secondi' => $secondi,
                        'stato' => $stato,
                        'id_elemento' => $id_elemento,
                        'uniquid' => $uniquid,
                    );

        //utilityHelper::make_debug_log(__FUNCTION__, print_r($log_arr, true), __FUNCTION__);

        try {
            if($stato == 1){
                $tmp->varName = 'cmi.core.lesson_status';
                $tmp->varValue = 'completed';
                $modelstato->setStato($tmp);
            }

            $tmp->varName = 'cmi.core.last_visit_date';
            $tmp->varValue = date('Y-m-d');
            $modelstato->setStato($tmp);


            $tmp->varName = 'cmi.core.total_time';
            $tmp->varValue = $secondi;
            $modelstato->setStato($tmp);

            // passando uniquid forzo l'esecuzione di updateUserLog
            if ($uniquid != ""
                && !is_null($uniquid)
                && !empty($uniquid)
                && $uniquid != "undefined"
                && $uniquid != "null") {

                $report = new gglmsModelReport();
                if (!$report->updateUserLog($uniquid))
                    utilityHelper::make_debug_log(__FUNCTION__, "uniquid non aggiornato -> " . $uniquid, __FUNCTION__);

            }

            echo 1;
        } catch (Exception $e) {
            //DEBUGG::log(json_encode($e->getMessage()), __FUNCTION__, 0, 1, 0 );
            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__);
            echo 0;
        }
        $japp->close();
    }

    public function updateBookmark() {

        $japp = JFactory::getApplication();


        $time = JRequest::getVar('time');
        $id_elemento = JRequest::getVar('id_elemento');
        $id_utente = JRequest::getVar('id_utente');

        $user =  JFactory::getUser();
        $user_id = $user->get('id');

        if (is_null($user_id)
            || $user_id == ""
            || (int) $user_id == 0)
            $user_id = $id_utente;

        //utilityHelper::make_debug_log(__FUNCTION__, print_r($_REQUEST, true), __FUNCTION__);


        $modelstato = new gglmsModelStatoContenuto();
        $tmp = new stdClass();

        $tmp->scoid = $id_elemento;
        $tmp->userid = $user_id;

        $log_arr = array(
            'user_id' => $user_id,
            'time' => $time,
            'id_elemento' => $id_elemento
        );

        //utilityHelper::make_debug_log(__FUNCTION__, print_r($log_arr, true), __FUNCTION__);

        try{
            $tmp->varName = 'bookmark';
            $tmp->varValue = $time;
            $modelstato->setStato($tmp);
        } catch (Exception $e) {
            //DEBUGG::log($e);
//            DEBUGG::log(json_encode($e->getMessage()), __FUNCTION__, 0, 1, 0 );
            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__);
       }
        $japp->close();
    }

    public function get_quiz_per_corso() {

        $japp = JFactory::getApplication();
        $_ret = array();
        $enable_json = false;

        try {

            $params = JRequest::get($_GET);
            $id_corso = $params["id_corso"];

            if (isset($params['json'])
                &&  (int) $params['json'] == 1)
                $enable_json = true;

            if (!isset($id_corso)
                || $id_corso == "")
                throw new Exception("Missing corso id", 1);

            $model_content = new gglmsModelContenuto();
            $quiz = $model_content->get_quiz_per_unit($id_corso);

            if (!$quiz
                || !is_array($quiz)
                || count($quiz) == 0)
                throw new Exception("Nessun quiz disponibile per il corso selezionato", E_USER_ERROR);

            if (!$enable_json)
                return $quiz;
            else
                $_ret['success'] = $quiz;

        }
        catch (Exception $e) {
            if (!$enable_json)
                return $e->getMessage();

            $_ret['error'] = $e->getMessage();
        }

        if ($enable_json)
            echo json_encode($_ret);

        $japp->close();
    }


}
