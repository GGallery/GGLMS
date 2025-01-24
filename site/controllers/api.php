<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use phpDocumentor\Reflection\DocBlock\Tags\Example;

defined('_JEXEC') or die;
require_once JPATH_COMPONENT . '/models/report.php';
require_once JPATH_COMPONENT . '/models/unita.php';
require_once JPATH_COMPONENT . '/models/config.php';
require_once JPATH_COMPONENT . '/models/generacoupon.php';
require_once JPATH_COMPONENT . '/models/syncdatareport.php';
require_once JPATH_COMPONENT . '/models/syncviewstatouser.php';
require_once JPATH_COMPONENT . '/controllers/zoom.php';
require_once JPATH_COMPONENT . '/controllers/users.php';
require_once JPATH_COMPONENT . '/helpers/utility.php';

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerApi extends JControllerLegacy
{
    private $_japp;
    public $_params;
    protected $_db;
    private $_config;
    private $_filterparam;
    public $mail_debug;


//https://api.joomla.org/cms-3/classes/Joomla.Utilities.ArrayHelper.html

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();
        $this->_db = JFactory::getDbo();
        $this->_config = new gglmsModelConfig();

        $this->_filterparam = new stdClass();

        $this->_filterparam->corso_id = JRequest::getVar('corso_id');
        $this->_filterparam->gruppo_id = JRequest::getVar('gruppo_id');
        $this->_filterparam->current = JRequest::getVar('current');
        $this->_filterparam->rowCount = JRequest::getVar('rowCount');
        $this->_filterparam->startdate = JRequest::getVar('startdate');
        $this->_filterparam->finishdate = JRequest::getVar('finishdate');
        $this->_filterparam->filterstato = JRequest::getVar('filterstato');
        $this->_filterparam->usergroups = JRequest::getVar('usergroups');
        $this->_filterparam->sort = JRequest::getVar('sort');
        $this->_filterparam->order = JRequest::getVar('order');
        $this->_filterparam->searchPhrase = JRequest::getVar('searchPhrase');
        $this->_filterparam->csvlimit = JRequest::getVar('csvlimit');
        $this->_filterparam->csvoffset = JRequest::getVar('csvoffset');
        $this->_filterparam->id_chiamata = JRequest::getVar('id_chiamata');
        $this->_filterparam->to = JRequest::getVar('to');
        $this->_filterparam->oggettomail = JRequest::getVar('oggettomail');
        $this->_filterparam->testomail = JRequest::getVar('testomail');
        $this->_filterparam->tipo_report = JRequest::getVar('tipo_report');
        $this->_filterparam->limit = JRequest::getVar('limit');
        $this->_filterparam->offset = JRequest::getVar('offset');
        $this->_filterparam->cid = JRequest::getVar('cid');
        $this->_filterparam->user_id = JRequest::getVar('user_id');
        $this->_filterparam->all_users = JRequest::getVar('all_users');
        $this->_filterparam->zoom_user = JRequest::getVar('zoom_user');
        $this->_filterparam->zoom_tipo = JRequest::getVar('zoom_tipo');
        $this->_filterparam->zoom_mese = JRequest::getVar('zoom_mese');
        $this->_filterparam->zoom_event_id = JRequest::getVar('zoom_event_id');
        $this->_filterparam->zoom_event_label = JRequest::getVar('zoom_label');
        $this->_filterparam->zoom_event_type = JRequest::getVar('zoom_event_type');
        $this->_filterparam->id_piattaforma = JRequest::getVar('id_piattaforma');
        $this->_filterparam->tipologia_svolgimento = JRequest::getVar('tipologia_svolgimento');
        $this->_filterparam->id_quiz = JRequest::getVar('id_quiz');
        $this->_filterparam->id_user = JRequest::getVar('user_id');
        $this->_filterparam->cToken = JRequest::getVar('cToken');
        // email di debug
        $this->mail_debug = $this->_config->getConfigValue('mail_debug');
        $this->mail_debug = ($this->mail_debug == "" || is_null($this->mail_debug)) ? "luca.gallo@gallerygroup.it" : $this->mail_debug;

    }

    // dati id_utente e id_corso rimuove tutti i riferimenti di quell'utente dalle tabelle dei coupon, report e quiz
    public function reset_corso(){

       try {

           $id_user = $this->_filterparam->user_id;
           $id_corso = $this->_filterparam->corso_id;

         if(!isset($id_corso)
            || !isset($id_user)
            || !is_numeric($id_corso)
            || !is_numeric($id_user))
             throw new Exception("id_corso e id_user devono essere di tipo numerico", 1);

            // cerco il gruppo del corso
            // usiamo il modello unita
            $model_unita = new gglmsModelUnita();
            $gruppo = $model_unita->get_id_gruppo_unit($id_corso);

             if (isset($gruppo) && is_numeric($gruppo)) {

                 $this->_db->transactionStart();

                 //controllo il coupon
                 $query_coupon = $this->_db->getQuery(true)
                     ->select('count(*)')
                     ->from('#__gg_coupon')
                     ->where('id_utente = ' . $id_user . ' and id_gruppi = ' . $this->_db->quote($gruppo));

                 $this->_db->setQuery($query_coupon);
                 $count_coupon = $this->_db->loadResult();


                 if (isset($count_coupon) && $count_coupon > 0) {

//                     //libero user dal coupon
//                     $update_coupon = 'UPDATE #__gg_coupon SET id_utente = NULL '
//                         . ' WHERE id_utente = ' . $id_user . ' AND id_gruppi = ' . $this->_db->quote($gruppo);
//                     $this->_db->setQuery($update_coupon);
//
//                     if (!$this->_db->execute()) throw new Exception("update coupon query ko -> " . $update_coupon, 1);
//
//                     //cancello da user_usergroup corso per utente
//                     $delete_usergroup = "DELETE FROM #__user_usergroup_map"
//                         . " WHERE group_id = " . $this->_db->quote($gruppo) . " AND user_id = " . $id_user;
//                     $this->_db->setQuery($delete_usergroup);
//
//                     if (!$this->_db->execute()) throw new Exception("Delete usergroup da user query ko -> " . $delete_usergroup, 1);


                     //seleziono tutti i contenuti del corso
                     $model_report = new gglmsModelReport();
                     $contents = $model_report->getContenutiArrayList($id_corso);


                     if(!isset($contents) || !is_array($contents)) throw new Exception("Nessun contenuto in questo corso" . $id_corso, 1);

                     foreach ($contents as $content) {
                         if (isset($content['id'])) {

                             //controllo tabella di log
                             $countquery = $this->_db->getQuery(true)
                                 ->select('count(*)')
                                 ->from('#__gg_log')
                                 ->where('id_contenuto =' . $this->_db->quote($content['id']) . 'and id_utente =' . $id_user);

                             $this->_db->setQuery($countquery);
                             $count = $this->_db->loadResult();


                             if (isset($count) && $count > 0) {

                                 //cancello utente per corso dal log
                                $delete_log = "DELETE FROM #__gg_log"
                                            . " WHERE id_contenuto = " . $this->_db->quote($content['id']) . " AND id_utente = " . $id_user ;
                                $this->_db->setQuery($delete_log);

                                if (!$this->_db->execute())
                                    throw new Exception("Delete log query ko -> " . $delete_log, 1);

                             }

                             $query_report = $this->_db->getQuery(true)
                                 ->select('id_anagrafica')
                                 ->from('#__gg_report')
                                 ->where('id_contenuto = ' . $this->_db->quote($content['id']) . ' and id_utente = ' . $id_user);

                             $this->_db->setQuery($query_report);
                             $anagrafica_id = $this->_db->loadResult();


                             if (isset($anagrafica_id) && is_numeric($anagrafica_id) && $anagrafica_id > 0) {


                                 $delete_stato_corso = "DELETE FROM #__gg_view_stato_user_corso"
                                     . " WHERE id_anagrafica = " . $this->_db->quote($anagrafica_id) . " AND id_corso = " . $id_corso ;
                                 $this->_db->setQuery($delete_stato_corso);

                                 if (!$this->_db->execute())
                                     throw new Exception("Delete stato corso query ko -> " . $delete_stato_corso, 1);


                                 $delete_stato_unita = "DELETE FROM #__gg_view_stato_user_unita"
                                     . " WHERE id_anagrafica = " . $this->_db->quote($anagrafica_id) . " AND id_corso = " . $id_corso ;
                                 $this->_db->setQuery($delete_stato_unita);

                                 if (!$this->_db->execute())
                                     throw new Exception("Delete stato unita query ko -> " . $delete_stato_unita, 1);


                                 $delete_report = "DELETE FROM #__gg_report"
                                     . " WHERE id_contenuto = " . $this->_db->quote($content['id']) . " AND id_utente = " . $id_user ;
                                 $this->_db->setQuery($delete_report);

                                 if (!$this->_db->execute())
                                     throw new Exception("Delete report query ko -> " . $delete_report, 1);

                             }


                             //cerco tipologia di ogni contenuto
                             $query_tipologia = $this->_db->getQuery(true)
                                 ->select('tipologia as tipologia_contenuto, id_quizdeluxe')
                                 ->from('#__gg_contenuti')
                                 ->where('id = ' . $this->_db->quote($content['id']));

                             $this->_db->setQuery($query_tipologia);
                             $tipologie = $this->_db->loadAssocList();

                             if(!isset($tipologie) || !is_array($tipologie))
                                 throw new Exception("Nessun tipologia per il contenuto " . $content['id'], 1);

                             foreach ($tipologie as $tipologia) {
                                 if (isset($tipologia)) {

                                     if ($tipologia['tipologia_contenuto'] == 7 ) {

                                         //conto se c sono dati in quiz
                                         $countquery = $this->_db->getQuery(true)
                                             ->select("count(*)")
                                             ->from("#__quiz_r_student_quiz")
                                             ->where('c_quiz_id = ' . $this->_db->quote($tipologia['id_quizdeluxe']) . ' and c_student_id = ' . $id_user);


                                         $this->_db->setQuery($countquery);
                                         $count = $this->_db->loadResult();

                                         if (isset($count) && $count > 0) {

                                             $delete_quiz = "DELETE FROM #__quiz_r_student_quiz"
                                                 . " WHERE c_quiz_id = " . $this->_db->quote($tipologia['id_quizdeluxe']) . " AND c_student_id = " . $id_user ;
                                             $this->_db->setQuery($delete_quiz);

                                             if (!$this->_db->execute())
                                                 throw new Exception("Delete quiz query ko ->" . $delete_quiz, 1);
                                         }

                                     } else {

                                         //conto se c sono dati in scormvars
                                         $countquery = $this->_db->getQuery(true)
                                             ->select("count(*)")
                                             ->from("#__gg_scormvars")
                                             ->where('scoid = ' . $this->_db->quote($content['id']) . ' and userid =' . $id_user);


                                         $this->_db->setQuery($countquery);
                                         $count = $this->_db->loadResult();

                                         if (isset($count) && $count > 0) {

                                             $delete_scormvars = "DELETE FROM #__gg_scormvars"
                                                 . " WHERE scoid = " . $this->_db->quote($content['id']) . " AND userid = " . $id_user ;
                                             $this->_db->setQuery($delete_scormvars);

                                             if (!$this->_db->execute())
                                                 throw new Exception("Delete scormvars query ko ->" . $delete_scormvars, 1);

                                         }


                                     }

                                 }

                             }


                         }
                     }


                 }

                 $this->_db->transactionCommit();
             } elseif (!isset($gruppo) || !is_numeric($gruppo)){

                 throw new Exception("corso non ha un gruppo su usergroup ->" .$gruppo, 1);
             }


           echo "Operazione di cancellazione del corso terminata: " . date('d/m/Y H:i:s');

       }catch (Exception $e) {
           $this->_db->transactionRollback();
           echo __FUNCTION__ . " error: " . $e->getMessage();
       }

        $this->_japp->close();

    }

    public function reset_corso_no_coupon(){

        try {

            $id_user = $this->_filterparam->user_id;
            $id_corso = $this->_filterparam->corso_id;

            if(!isset($id_corso)
                || !isset($id_user)
                || !is_numeric($id_corso)
                || !is_numeric($id_user))
                throw new Exception("id_corso e id_user devono essere di tipo numerico", 1);

            // cerco il gruppo del corso
            // usiamo il modello unita
            $model_unita = new gglmsModelUnita();
            $gruppo = $model_unita->get_id_gruppo_unit($id_corso);

            if (isset($gruppo) && is_numeric($gruppo)) {

                $this->_db->transactionStart();

                //controllo il coupon
                $query_coupon = $this->_db->getQuery(true)
                    ->select('count(*)')
                    ->from('#__gg_coupon')
                    ->where('id_utente = ' . $id_user . ' and id_gruppi = ' . $this->_db->quote($gruppo));

                $this->_db->setQuery($query_coupon);
                $count_coupon = $this->_db->loadResult();


                if (isset($count_coupon) && $count_coupon > 0) {

                    //seleziono tutti i contenuti del corso
                    $model_report = new gglmsModelReport();
                    $contents = $model_report->getContenutiArrayList($id_corso);


                    if(!isset($contents) || !is_array($contents)) throw new Exception("Nessun contenuto in questo corso" . $id_corso, 1);

                    foreach ($contents as $content) {
                        if (isset($content['id'])) {

                            //controllo tabella di log
                            $countquery = $this->_db->getQuery(true)
                                ->select('count(*)')
                                ->from('#__gg_log')
                                ->where('id_contenuto =' . $this->_db->quote($content['id']) . 'and id_utente =' . $id_user);

                            $this->_db->setQuery($countquery);
                            $count = $this->_db->loadResult();


                            if (isset($count) && $count > 0) {

                                //cancello utente per corso dal log
                                $delete_log = "DELETE FROM #__gg_log"
                                    . " WHERE id_contenuto = " . $this->_db->quote($content['id']) . " AND id_utente = " . $id_user ;
                                $this->_db->setQuery($delete_log);

                                if (!$this->_db->execute())
                                    throw new Exception("Delete log query ko -> " . $delete_log, 1);

                            }

                            $query_report = $this->_db->getQuery(true)
                                ->select('id_anagrafica')
                                ->from('#__gg_report')
                                ->where('id_contenuto = ' . $this->_db->quote($content['id']) . ' and id_utente = ' . $id_user);

                            $this->_db->setQuery($query_report);
                            $anagrafica_id = $this->_db->loadResult();


                            if (isset($anagrafica_id) && is_numeric($anagrafica_id) && $anagrafica_id > 0) {


                                $delete_stato_corso = "DELETE FROM #__gg_view_stato_user_corso"
                                    . " WHERE id_anagrafica = " . $this->_db->quote($anagrafica_id) . " AND id_corso = " . $id_corso ;
                                $this->_db->setQuery($delete_stato_corso);

                                if (!$this->_db->execute())
                                    throw new Exception("Delete stato corso query ko -> " . $delete_stato_corso, 1);


                                $delete_stato_unita = "DELETE FROM #__gg_view_stato_user_unita"
                                    . " WHERE id_anagrafica = " . $this->_db->quote($anagrafica_id) . " AND id_corso = " . $id_corso ;
                                $this->_db->setQuery($delete_stato_unita);

                                if (!$this->_db->execute())
                                    throw new Exception("Delete stato unita query ko -> " . $delete_stato_unita, 1);


                                $delete_report = "DELETE FROM #__gg_report"
                                    . " WHERE id_contenuto = " . $this->_db->quote($content['id']) . " AND id_utente = " . $id_user ;
                                $this->_db->setQuery($delete_report);

                                if (!$this->_db->execute())
                                    throw new Exception("Delete report query ko -> " . $delete_report, 1);

                            }


                            //cerco tipologia di ogni contenuto
                            $query_tipologia = $this->_db->getQuery(true)
                                ->select('tipologia as tipologia_contenuto, id_quizdeluxe')
                                ->from('#__gg_contenuti')
                                ->where('id = ' . $this->_db->quote($content['id']));

                            $this->_db->setQuery($query_tipologia);
                            $tipologie = $this->_db->loadAssocList();

                            if(!isset($tipologie) || !is_array($tipologie))
                                throw new Exception("Nessun tipologia per il contenuto " . $content['id'], 1);

                            foreach ($tipologie as $tipologia) {
                                if (isset($tipologia)) {

                                    if ($tipologia['tipologia_contenuto'] == 7 ) {

                                        //conto se c sono dati in quiz
                                        $countquery = $this->_db->getQuery(true)
                                            ->select("count(*)")
                                            ->from("#__quiz_r_student_quiz")
                                            ->where('c_quiz_id = ' . $this->_db->quote($tipologia['id_quizdeluxe']) . ' and c_student_id = ' . $id_user);


                                        $this->_db->setQuery($countquery);
                                        $count = $this->_db->loadResult();

                                        if (isset($count) && $count > 0) {

                                            $delete_quiz = "DELETE FROM #__quiz_r_student_quiz"
                                                . " WHERE c_quiz_id = " . $this->_db->quote($tipologia['id_quizdeluxe']) . " AND c_student_id = " . $id_user ;
                                            $this->_db->setQuery($delete_quiz);

                                            if (!$this->_db->execute())
                                                throw new Exception("Delete quiz query ko ->" . $delete_quiz, 1);
                                        }

                                    } else {

                                        //conto se c sono dati in scormvars
                                        $countquery = $this->_db->getQuery(true)
                                            ->select("count(*)")
                                            ->from("#__gg_scormvars")
                                            ->where('scoid = ' . $this->_db->quote($content['id']) . ' and userid =' . $id_user);


                                        $this->_db->setQuery($countquery);
                                        $count = $this->_db->loadResult();

                                        if (isset($count) && $count > 0) {

                                            $delete_scormvars = "DELETE FROM #__gg_scormvars"
                                                . " WHERE scoid = " . $this->_db->quote($content['id']) . " AND userid = " . $id_user ;
                                            $this->_db->setQuery($delete_scormvars);

                                            if (!$this->_db->execute())
                                                throw new Exception("Delete scormvars query ko ->" . $delete_scormvars, 1);

                                        }


                                    }

                                }

                            }


                        }
                    }


                }

                $this->_db->transactionCommit();
            } elseif (!isset($gruppo) || !is_numeric($gruppo)){

                throw new Exception("corso non ha un gruppo su usergroup ->" .$gruppo, 1);
            }


            echo "Operazione di cancellazione del corso terminata: " . date('d/m/Y H:i:s');

        }catch (Exception $e) {
            $this->_db->transactionRollback();
            echo __FUNCTION__ . " error: " . $e->getMessage();
        }

        $this->_japp->close();

    }

    public function get_report()
    {

        $data = $this->new_get_data();

        echo json_encode($data);
        $this->_japp->close();
    }

    private function new_get_data($offsetforcsv = null)
    {
        $_ret = array();


        //$this->_filterparam->task = JRequest::getVar('task');
        //FILTERSTATO: 2=TUTTI 1=COMPLETATI 0=SOLO NON COMPLETATI 3=IN SCADENZA
        if(strpos($this->_filterparam->corso_id,'|')){

            $id_corso = explode('|', $this->_filterparam->corso_id)[0];
            $id_contenuto = explode('|', $this->_filterparam->corso_id)[1];
        }else{
            $id_corso = explode('|', $this->_filterparam->corso_id)[0];
        }

        $alert_days_before = $this->_params->get('alert_days_before');
        $tipo_report = $this->_filterparam->tipo_report;
        $offset = (isset($this->_filterparam->offset) && $this->_filterparam->offset != "") ? $this->_filterparam->offset : 0;
       $limit = (isset($this->_filterparam->limit) && $this->_filterparam->limit != "") ? $this->_filterparam->limit : 5;
       $_sort = (isset($this->_filterparam->sort) && $this->_filterparam->sort != "") ? $this->_filterparam->sort : null;
       $_order = (isset($this->_filterparam->order) && $this->_filterparam->order != "") ? $this->_filterparam->order : null;

        $filters = array(
                        'startdate' => $this->_filterparam->startdate,
                        'finishdate' => $this->_filterparam->finishdate,
                        'filterstato' => $this->_filterparam->filterstato,
                        'searchPhrase' => $this->_filterparam->searchPhrase,
                        'usergroups' => $this->_filterparam->usergroups);

        try {


            $columns = array();
            $rows = array();
            switch ($tipo_report) {

                case 0: //PER CORSO


                    $att_id_string = $this->getAttestati($id_corso);

                    if(strpos($att_id_string,'|')){

                        $attestati = explode('|',$att_id_string);
                        $attestato = $attestati[1];
                        $attestato_hidden = $attestati[0];
                    }else{
                        $attestato_hidden = $att_id_string;
                    }

                    $query = $this->_db->getQuery(true);
                    $query->select('vista.id_anagrafica as id_anagrafica,"'. $attestato .'" as Attestato,"' . $attestato_hidden . '" as attestati_hidden, vista.stato as stato, vista.data_inizio as data_inizio, vista.data_fine as data_fine , IF(date(now())>DATE_ADD((select data_fine from #__gg_unit where id=' . $id_corso . '), INTERVAL -' . $alert_days_before . ' DAY), IF(stato=0,1,0),0) as scadenza');
                    $query->from('#__gg_view_stato_user_corso  as vista');
                    $query->where('id_corso=' . $id_corso);
                    switch ($filters['filterstato']) {

                        case 0: //qualsiasi stato

                            $arrayresult = $this->buildGeneralDataCubeUtentiInCorso($id_corso, $_order, $_sort, $limit, $offset, $filters['searchPhrase'], $filters['usergroups']);
                            $users = $arrayresult[0];
                            $count = $arrayresult[1];
                            $queryGeneralCube = $arrayresult[2];
                            $queryGeneralCubeCount = $arrayresult[3];
//                            $result['secondaryCubeQuery'] = (string)$query;
                            $datas = $this->buildPrimaryDataCube($query);
                            $users = $this->addColumn($users, $datas, "id_anagrafica", null, "stato", 'outer');
                            $users = $this->addColumn($users, $datas, "id_anagrafica", null, "data_inizio", 'outer');
                            $users = $this->addColumn($users, $datas, "id_anagrafica", null, "data_fine", 'outer');
                            $users = $this->addColumn($users, $datas, "id_anagrafica", null, "scadenza", 'outer');
                            $users = $this->addColumn($users, $datas, "id_anagrafica", null, "attestati_hidden", 'outer');
                            $users = $this->addColumn($users, $datas, "id_anagrafica", null, "Attestato", 'outer');
                            $columns = array('id_anagrafica', 'cognome', 'nome', 'stato', 'data_inizio', 'data_fine', 'scadenza', 'fields','attestati_hidden','Attestato');

                            $rows = $users;


                            break;

                        case 1:
                        case 2:
                        case 3:
                            if ($filters['filterstato'] == 1)
                                $query->where("vista.stato = 1");
                            if ($filters['filterstato'] == 2)
                                $query->where("vista.stato = 0");
                            if ($filters['filterstato'] == 3)
                                $query->where("vista.stato = 0 and IF(date(now())>DATE_ADD((select data_fine from #__gg_unit where id=" . $id_corso . "), INTERVAL -" . $alert_days_before . " DAY), IF(stato=0,1,0),0)=1");


                            if ($filters['startdate'] != null)
                                $query->where("vista.data_fine>'" . $filters['startdate'] . "'");
                            if ($filters['finishdate'] != null)
                                $query->where("vista.data_fine<'" . $filters['finishdate'] . "'");

                            if ($filters['searchPhrase'] != null) {
                                $query->where('id_anagrafica in (select anagrafica.id from #__gg_report_users as anagrafica where anagrafica.cognome LIKE \'%' . $filters['searchPhrase'] . '%\')');

                            }
                           // $result['secondaryCubeQuery'] = (string)$query;




//                            $count = $this->countPrimaryDataCube($query);!!! BUG il count che viene su da qui non è filtrato per azienda!!
//                            $datas = $this->buildPrimaryDataCube($query,$offset, $limit); !!! BUG offset e limit erano invertiti!!
                            $datas = $this->buildPrimaryDataCube($query,null,null);




                            $arrayresult = $this->buildGeneralDataCubeUtentiInCorso($id_corso, $_order, $_sort, $limit, $offset, $filters['searchPhrase'], $filters['usergroups'], implode(",", (array_column($datas, "id_anagrafica"))));
                            $users = $arrayresult[0];

                            // in sostituzione del count commentato sopra --> il count era già presente come parametro di ritorno ma non veniva usato
                            $count = $arrayresult[1];

                            $queryGeneralCube = $arrayresult[2];
                            $queryGeneralCubeCount = $arrayresult[3];
                            $datas = $this->addColumn($datas, $users, "id_anagrafica", null, "nome", 'inner');
                            $datas = $this->addColumn($datas, $users, "id_anagrafica", null, "cognome", 'inner');
                            $datas = $this->addColumn($datas, $users, "id_anagrafica", null, "fields", 'inner');
                            $rows = $datas;

                            $columns = array('id_anagrafica', 'cognome', 'nome', 'stato', 'data_inizio', 'data_fine', 'scadenza', 'fields', 'attestati_hidden','Attestato');

                            break;


                    }

                    break;


                case 1: //PER UNITA'
                    $arrayresult = $this->buildGeneralDataCubeUtentiInCorso($id_corso, $_order, $_sort, $limit, $offset, $filters['searchPhrase'], $filters['usergroups']);
                    $users = $arrayresult[0];
                    $count = $arrayresult[1];
                    $queryGeneralCube = $arrayresult[2];
                    $queryGeneralCubeCount = $arrayresult[3];
                    $query = $this->_db->getQuery(true);
                    $query->select('vista.id_anagrafica as id_anagrafica, u.id as id_unita,u.titolo as titolo_unita, vista.stato as stato, vista.data_inizio as data_inizio, vista.data_fine as data_fine');
                    $query->from('#__gg_view_stato_user_unita  as vista');
                    $query->join('inner', '#__gg_unit as u on vista.id_unita=u.id');
                    $query->where('id_corso=' . $id_corso);
//                    $result['secondaryCubeQuery'] = (string)$query;
                    $datas = $this->buildPrimaryDataCube($query);
                    $users = $this->addColumn($users, $datas, "id_anagrafica", "titolo_unita", "stato", 'outer');
                    $columns = $this->buildColumnsforUnitaView($id_corso);
                    $rows = $users;
                    break;
                case 2://PER CONTENUTO
                    $arrayresult = $this->buildGeneralDataCubeUtentiInCorso($id_corso, $_order, $_sort, $limit, $offset, $filters['searchPhrase'], $filters['usergroups']);
                    $users = $arrayresult[0];
                    $count = $arrayresult[1];
                    $queryGeneralCube = $arrayresult[2];
                    $queryGeneralCubeCount = $arrayresult[3];

                    $query = $this->_db->getQuery(true);
                    $query->select('vista.id_anagrafica as id_anagrafica, c.id as id_contenuto,c.titolo as titolo_contenuto, vista.stato as stato, vista.permanenza_tot as permanenza, vista.data as last_visit');
                    $query->from('#__gg_report  as vista ');
                    $query->join('inner', '#__gg_contenuti as c on vista.id_contenuto=c.id');
                    $query->where('id_corso=' . $id_corso);
//                    $result['secondaryCubeQuery'] = (string)$query;
                    $datas = $this->buildPrimaryDataCube($query);
                    $users = $this->addColumn($users, $datas, "id_anagrafica", "titolo_contenuto", "stato", 'outer');
//                    $users = $this->addColumn($users, $datas, "id_anagrafica", "titolo_contenuto", "last_visit", 'outer'); // mostro ultima data di visita al posto di spunta verde in report per contenuto
                    $columns = $this->buildColumnsforContenutiView($id_corso);
                    $rows = $users;
                    break;


            }

            $fields = explode(',', $this->_params->get('campicustom_report'));
            $columns = array_merge($columns, $fields);

            $rows = $this->buildPivot($rows, $columns, "");


        } catch (Exception $e) {

            DEBUGG::log('ERRORE DA GETDATA:' . json_encode($e->getMessage()), 'ERRORE DA GET DATA', 1, 1);
            //DEBUGG::error($e, 'error', 1);
        }

        if (isset($rows)) {
            foreach ($rows as $_key_row => $_row) {

                 $disable = '0';
                 //rows da colorare
                if($_row['stato'] == 1){
                 $color_cell = 'color:green';
                 $disable = '1';
                }else if($_row['scadenza'] == 1) {
                    $color_cell = 'color:red';
                }else{
                    $color_cell = '';
                }

                    foreach ($_row as $key => $value) {
                        $user_id1 = $_row['fields']->user_id;

                        //salto i fields perche un object
                        if($key == 'fields')continue;

                            $_ret[$_key_row][$key] = <<<HTML
                            <span style="{$color_cell}" >{$value}</span>
HTML;
                        if($value == '1'){
                            $_ret[$_key_row][$key] = <<<HTML
                            <i title="completato" class="fas fa-check-square fa-2x" style="color:green;"></i>
HTML;
                        }else if($value == '0'){
                            $_ret[$_key_row][$key] = <<<HTML
                            <i title="iniziato" class="fas fa-sign-in-alt fa-2x" style="color:#4169e1;"></i>
HTML;
                        }else if($value == '0000-00-00'){
                            $_ret[$_key_row][$key] = <<<HTML
                            <span ></span>
HTML;
                        }else if(($disable == '1')&&($key == 'attestati_hidden')){ //aggiungo buttone per scaricare l'attestato
                            $content_id = explode('#',$_row['attestati_hidden'])[0];
                            $url = 'index.php?option=com_gglms&task=reportutente.generateAttestato&content_id='.$content_id.'&user_id='.$user_id1.'&id_corso='.$id_corso;
                            $_ret[$_key_row][$key] = <<<HTML
                            <i class="far fa-file-pdf fa-2x" onclick="javascript:window.open('{$url}')" style="cursor: pointer;color: red"></i>
HTML;
                        }else if(($disable == '1')&&($key == 'Attestato')&&($attestato != '')){
                            $content_id = explode('#',$_row['Attestato'])[0];
                            $url = 'index.php?option=com_gglms&task=reportutente.generateAttestato&content_id='.$content_id.'&user_id='.$user_id1.'&id_corso='.$id_corso;
                            $_ret[$_key_row][$key] = <<<HTML
                            <i class="far fa-file-pdf fa-2x" onclick="javascript:window.open('{$url}')" style="cursor: pointer;color: red"></i>
HTML;
                        }else if(($disable == '0')&&($key == 'attestati_hidden')){
                            $_ret[$_key_row][$key] = <<<HTML
                            <span></span>
HTML;
                        }else if(($disable == '0')&&($key == 'attestato')){
                            $_ret[$_key_row][$key] = <<<HTML
                            <span></span>
HTML;
                        }


                    }
                    //aggiungo i fields
                $_ret[$_key_row]['fields'] = $_row['fields'];

            }

        }



//       $result['queryGeneralCube'] = (string)$queryGeneralCube;

//        $result['datas'] = $datas;
//        $result['buildGeneralDataCubeUtentiInCorso'] = $arrayresult[0];
        //$result['current']=$this->_filterparam->current;
//        $result['columns'] = $columns;
        $result['rowCount'] = $count;
        $result['rows'] = $_ret;
        //$result['total']=$total;
//       $result['totalquery'] = (string)$queryGeneralCubeCount;
        //echo json_encode($result);
        return $result;
    }

    /*
     * Eliminazione dei quiz in riferimento ad un utente
     */
    public function del_user_quiz() {

        $_ret = array();

        try {
            $cid = $this->_filterparam->cid;

            $query = "SELECT c_id FROM #__quiz_r_student_question"
                . "\n WHERE c_stu_quiz_id = '". $cid ."'";
            $this->_db->SetQuery($query);
            $stu_q_id = $this->_db->loadColumn();

            if ((!is_array($stu_q_id)) || empty($stu_q_id))
                $stu_q_id = array(0);

            $stu_cids = implode( ',', $stu_q_id );
            utilityHelper::joomla_quiz_delete_items($stu_cids, 'remove/', 'removeResults');

            $query = "DELETE FROM #__quiz_r_student_question"
                . "\n WHERE c_stu_quiz_id = '". $cid . "'";
            $this->_db->setQuery($query);

            if (!$this->_db->execute())
                $_ret['messages'][] = $this->_db->getErrorMsg();

            $query = "DELETE FROM #__quiz_r_student_quiz"
                . "\n WHERE c_id = '". $cid . "'";
            $this->_db->setQuery($query);

            if (!$this->_db->execute())
                $_ret['messages'][] = $this->_db->getErrorMsg();

        }
        catch (Exception $e) {
            $_ret['errors'] = $e->getMessage();
            DEBUGG::log(json_encode($_ret['errors']), 'ERRORE DA ' . __FUNCTION__, 1, 1);
        }

        echo json_encode($_ret);
        $this->_japp->close();
    }

    /*
     * utenti che hanno svolto quiz per azienda e corso
     * */
    public function get_user_quiz_per_azienda_corso() {

        $data = $this->new_get_user_quiz_per_azienda_corso();

        echo json_encode($data);
        $this->_japp->close();

    }

    private function new_get_user_quiz_per_azienda_corso() {

        try {

            $id_corso = $this->_filterparam->corso_id;
            $limit = $this->_filterparam->limit;
            $offset = $this->_filterparam->offset;
            $filters = array(
                'startdate' => $this->_filterparam->startdate,
                'finishdate' => $this->_filterparam->finishdate,
                'searchPhrase' => $this->_filterparam->searchPhrase,
                'usergroups' => $this->_filterparam->usergroups
            );

            $columns = array();

            $query = $this->_db->getQuery(true);
            $sub_query1 = $this->_db->getQuery(true);

            $sub_query1->select('user_id');
            $sub_query1->from('#__user_usergroup_map');
            $sub_query1->where('group_id = ' . $filters['usergroups']);

            $query->from('#__gg_contenuti C');
            $query->join('inner', '#__quiz_r_student_quiz SQ ON C.id_quizdeluxe = SQ.c_quiz_id');
            $query->join('inner', '#__quiz_t_quiz TQ ON SQ.c_quiz_id = TQ.c_id');
            $query->join('inner', '#__gg_unit_map UM ON C.id = UM.idcontenuto');
            $query->join('inner', '(' . $sub_query1 . ') AS SUB1 ON SQ.c_student_id = SUB1.user_id');
            $query->join('inner', '#__comprofiler CP ON SQ.c_student_id = CP.user_id');
            $query->where('UM.idunita = ' . $id_corso);
            $query->where('TQ.c_passing_score > 0');

            // filtri su date dei corsi
            if ($filters['startdate'] != null)
                $query->where("SQ.c_date_time > '" . $filters['startdate'] . "'");
            if ($filters['finishdate'] != null)
                $query->where("SQ.c_date_time < '" . $filters['finishdate'] . "'");

            if ($filters['searchPhrase'] != null) {
                $query->where('(CP.cb_nome LIKE \'%' . $filters['searchPhrase'] . '%\'
                                    OR CP.cb_cognome LIKE \'%' . $filters['searchPhrase'] . '%\'
                                    OR CP.cb_codicefiscale LIKE \'%' . $filters['searchPhrase'] . '%\'
                                    OR TQ.c_title LIKE \'%' . $filters['searchPhrase'] . '%\'
                                    ) ');
            }

            // clono la query originale per il conteggio
            $countquery = clone $query;

            $query->select('CP.cb_nome AS nome, CP.cb_cognome AS cognome, UPPER(CP.cb_codicefiscale) AS codice_fiscale,
                            TQ.c_title AS titolo,
			                DATE_FORMAT(SQ.c_date_time, \'%d/%m/%Y %H:%i\') AS data_completamento,
			                SQ.c_passed AS esito,
			                SQ.c_quiz_id AS quiz_ref,
			                SQ.c_id AS quiz_id,
			                SQ.c_student_id AS student_id,
			                SQ.c_total_score AS punteggio');

            $countquery->select("COUNT(*) AS num_rows");

            $query->order('SQ.c_date_time', 'desc');
            $query->order('SQ.c_student_id', 'asc');


            $this->_db->setQuery($query, $offset, $limit);
            $rows = $this->_db->loadAssocList();

            $this->_db->setQuery($countquery);
            $count = $this->_db->loadResult();

            $columns = utilityHelper::get_nomi_colonne_da_query_results($count, $rows);

            $result['columns'] = $columns;
            $result['rowCount'] = $count;
            $result['rows'] = $rows;

            return $result;
        }
        catch (Exception $e) {
            DEBUGG::log(json_encode($e->getMessage()), 'ERRORE DA ' . __FUNCTION__, 1, 1);
        }

    }

    /*
     * report che estrae il numero di ore per corso degli utenti iscritti
     * */
    public function get_report_ore_corso() {

        $data = $this->new_get_ore_corso();

        echo json_encode($data);
        $this->_japp->close();

    }

    public function get_date_per_contenuto() {

        $_ret = array();
        $arr_date_descrizione = $this->get_descrizione_contenuto();
        if (count($arr_date_descrizione) == 0)
            return $_ret;

        //$normalizza_contenuto = UtilityHelper::normalizza_contenuto_array($arr_date_descrizione);
        $_ret = UtilityHelper::elabora_array_date_id_contenuto($arr_date_descrizione);

        return $_ret;

    }

    // se presente la descrizione in HTML che definisce le date in cui il corso si sviluppa
    private function get_descrizione_contenuto() {

        try {

            $query = $this->_db->getQuery(true);
            $query->select('id AS id_contenuto, descrizione')
                    ->from('#__gg_contenuti')
                    ->where('descrizione IS NOT NULL')
                    ->where('descrizione != ""')
                    ->where('pubblicato = 1')
                    ->where('durata > 0');

            $this->_db->setQuery($query);
            $rows = $this->_db->loadAssocList();

            return $rows;

        }
        catch (Exception $e) {
            DEBUGG::log(json_encode($e->getMessage()), 'ERRORE DA ' . __FUNCTION__, 1, 1);
        }
    }

    private function new_get_ore_corso() {

        $id_corso = explode('|', $this->_filterparam->corso_id)[0];
        $limit = $this->_filterparam->limit;
        $offset = $this->_filterparam->offset;
        $filters = array(
                        'startdate' => $this->_filterparam->startdate,
                        'finishdate' => $this->_filterparam->finishdate,
                        //'filterstato' => $this->_filterparam->filterstato,
                        'searchPhrase' => $this->_filterparam->searchPhrase,
                        'usergroups' => $this->_filterparam->usergroups
                    );
        $columns = array();

        $con_orari = false;
        $arr_date_descrizione = $this->get_date_per_contenuto();
        if (count($arr_date_descrizione) > 0)
            $con_orari = true;

        try {

            $query = $this->_db->getQuery(true);
            $countquery = $this->_db->getQuery(true);
            $sub_query1 = $this->_db->getQuery(true);
            $sub_query2 = $this->_db->getQuery(true);

            $query->select('IFNULL(CP.cb_nome, "") AS nome, IFNULL(CP.cb_cognome, "") AS cognome,
                            IFNULL(UPPER(CP.cb_codicefiscale), "") AS codice_fiscale,
                            CN.id AS id_contenuto, CN.titolo AS titolo_evento');

            if (!$con_orari)
                $query->select('SEC_TO_TIME(CN.durata) AS durata_evento, SEC_TO_TIME(SUM(LG.permanenza)) AS tempo_visualizzato');
            else
                $query->select('DATE_FORMAT(LG.data_accesso, \'%Y-%m-%d\') AS data_accesso, SEC_TO_TIME(CN.durata) AS durata_evento, SUM(LG.permanenza) AS tempo_visualizzato');

            $countquery->select("COUNT(*) AS per_contenuto, CN.id AS id_contenuto, DATE_FORMAT(LG.data_accesso, '%Y-%m-%d') AS data_accesso");

            $query->from('#__comprofiler CP');
            $query->join('inner', '#__gg_log LG ON CP.user_id = LG.id_utente');
            $query->join('inner', '#__gg_contenuti CN ON LG.id_contenuto = CN.id');

            $countquery->from('#__comprofiler CP');
            $countquery->join('inner', '#__gg_log LG ON CP.user_id = LG.id_utente');
            $countquery->join('inner', '#__gg_contenuti CN ON LG.id_contenuto = CN.id');

            // join in subquery
            $sub_query1->select('MAP.idcontenuto');
            $sub_query1->from('#__gg_unit_map MAP');
            $sub_query1->join('inner', '#__gg_unit U ON MAP.idunita = U.id');
            //$sub_query1->where('MAP.idunita = ' . $id_corso);
            $sub_query1->where(' (MAP.idunita = ' . $id_corso . ' OR U.unitapadre = ' . $id_corso . ')');
            $sub_query1->where('U.pubblicato = 1');
            //$sub_query1->where('U.is_corso = 1');

            $query->join('inner', '(' . $sub_query1 . ') AS SUB1 ON CN.id = SUB1.idcontenuto');
            $countquery->join('inner', '(' . $sub_query1 . ') AS SUB1 ON CN.id = SUB1.idcontenuto');

            // filtro su azienda
            if ($filters['usergroups'] != null) {
                $sub_query2->select('user_id')
                            ->from('#__user_usergroup_map')
                            ->where('group_id = ' . $filters['usergroups']);

                $query->join('inner', '(' . $sub_query2 . ') AS SUB2 ON LG.id_utente = SUB2.user_id');
                $countquery->join('inner', '(' . $sub_query2 . ') AS SUB2 ON LG.id_utente = SUB2.user_id');
            }

            // filtri su date dei corsi
            if ($filters['startdate'] != null)
                $sub_query1->where("U.data_fine > '" . $filters['startdate'] . "'");
            if ($filters['finishdate'] != null)
                $sub_query1->where("U.data_fine < '" . $filters['finishdate'] . "'");

            $query->where('CN.pubblicato = 1');
            $countquery->where('CN.pubblicato = 1');

            // il limit va impostato nel setQuery
            //$query->setlimit($offset, $limit);

            if ($filters['searchPhrase'] != null) {
                $query->where('(CP.cb_nome LIKE \'%' . $filters['searchPhrase'] . '%\'
                                    OR CP.cb_cognome LIKE \'%' . $filters['searchPhrase'] . '%\'
                                    OR CP.cb_codicefiscale LIKE \'%' . $filters['searchPhrase'] . '%\') ');
                $countquery->where('(CP.cb_nome LIKE \'%' . $filters['searchPhrase'] . '%\'
                                    OR CP.cb_cognome LIKE \'%' . $filters['searchPhrase'] . '%\'
                                    OR CP.cb_codicefiscale LIKE \'%' . $filters['searchPhrase'] . '%\') ');
            }

            $query->group($this->_db->quoteName('LG.id_utente'));
            $query->group($this->_db->quoteName('LG.id_contenuto'));

            $countquery->group($this->_db->quoteName('LG.id_utente'));
            $countquery->group($this->_db->quoteName('LG.id_contenuto'));

            if ($con_orari) {
                $query->group('DATE_FORMAT(LG.data_accesso, \'%Y-%m-%d\')');
                $query->order('DATE_FORMAT(LG.data_accesso, \'%Y-%m-%d\')');

                $countquery->group('DATE_FORMAT(LG.data_accesso, \'%Y-%m-%d\')');
                $countquery->order('DATE_FORMAT(LG.data_accesso, \'%Y-%m-%d\')');
            }

            $this->_db->setQuery($query, $offset, $limit);
            $rows = $this->_db->loadAssocList();

            $this->_db->setQuery($countquery);
            $counts = $this->_db->loadAssocList();

            // elaborazione dell'array in base alle date evento (se previsto)
            if ($con_orari) {
                $_tmp_arr = array();
                $_tmp_count_arr = array();
                foreach ($rows as $rr => $row) {
                    if (isset($arr_date_descrizione[$row['id_contenuto']][$row['data_accesso']])) {
                        $durata_evento = $arr_date_descrizione[$row['id_contenuto']][$row['data_accesso']];
                        //$row['durata_evento'] = gmdate("H:i:s", $durata_evento);
                        $row['durata_evento'] = UtilityHelper::sec_to_hr($durata_evento);
                        $tempo_assenza = ($durata_evento-$row['tempo_visualizzato']);
                        $tempo_assenza = ($tempo_assenza < 0) ? 0 : $tempo_assenza;
                        //$row['tempo_assenza'] = gmdate("H:i:s", $tempo_assenza);
                        $row['tempo_assenza'] = UtilityHelper::sec_to_hr($tempo_assenza);
                        //$row['tempo_visualizzato'] = ($row['tempo_visualizzato'] > $durata_evento) ? gmdate("H:i:s", $durata_evento) : gmdate("H:i:s", $row['tempo_visualizzato']);
                        $row['tempo_visualizzato'] = ($row['tempo_visualizzato'] > $durata_evento) ? UtilityHelper::sec_to_hr($durata_evento) : UtilityHelper::sec_to_hr($row['tempo_visualizzato']);
                        $row['data_accesso'] = date("d/m/Y", strtotime($row['data_accesso']));
                        $_tmp_arr[] = $row;
                    }
                }

                if (count($_tmp_arr) > 0) {
                    $rows = $_tmp_arr;

                    foreach ($counts as $cc => $count) {
                        if (isset($arr_date_descrizione[$count['id_contenuto']][$count['data_accesso']])) {
                            $_tmp_count_arr[] = $count;
                        }
                    }

                    if (count($_tmp_count_arr) > 0) {
                        $counts = $_tmp_count_arr;
                    }
                }
            }

            $num_rows = count($counts);
            $columns = utilityHelper::get_nomi_colonne_da_query_results($num_rows, $rows);

            $result['columns'] = $columns;
            $result['rowCount'] = $num_rows;
            $result['rows'] = $rows;

            return $result;
        }
        catch (Exception $e) {
            DEBUGG::log(json_encode($e->getMessage()), 'ERRORE DA ' . __FUNCTION__, 1, 1);
        }
    }

/*
     metodo copia di new_get_data che ritorna le colonne del report a seconda dei parametri
    serve nel report kendo per la costruzione di griglie dinamiche nel reportkendo*/
    public function get_report_columns()
    {

        //$this->_filterparam->task = JRequest::getVar('task');
        //FILTERSTATO: 2=TUTTI 1=COMPLETATI 0=SOLO NON COMPLETATI 3=IN SCADENZA
        $id_corso = explode('|', $this->_filterparam->corso_id)[0];
        $tipo_report = $this->_filterparam->tipo_report;

        try {


            $columns = array();
            switch ($tipo_report) {

                case 0: //PER CORSO
                    $columns = array('id_anagrafica', 'cognome', 'nome', 'stato', 'data_inizio', 'data_fine', 'scadenza', 'fields', 'attestati_hidden');
                    break;

                case 1: //PER UNITA'

                    $columns = $this->buildColumnsforUnitaView($id_corso);

                    break;
                case 2://PER CONTENUTO
                    $columns = $this->buildColumnsforContenutiView($id_corso);

                    break;


            }

            $fields = explode(',', $this->_params->get('campicustom_report'));
            $columns = array_merge($columns, $fields);


        } catch (Exception $e) {

            DEBUGG::log('ERRORE DA GETDATA:' . json_encode($e->getMessage()), 'ERRORE DA GET DATA', 1, 1);
            //DEBUGG::error($e, 'error', 1);
        }

        $result['columns'] = $columns;
        echo json_encode($result);

        $this->_japp->close();

    }


    public function get_export_columns()
    {

        $elenco_campi_per_csv_da_back_end = explode(',', $this->_params->get('campi_csv'));
        echo (json_encode($elenco_campi_per_csv_da_back_end));
        $this->_japp->close();
    }



    private function buildGeneralDataCubeUtentiInCorso($id_corso, $order, $sort, $limit, $offset, $searchPrase, $gruppo_azienda, $anagrafica_filter = null)
    {
        try {

            $query = $this->_db->getQuery(true);
            $query->select("accesso");
            $query->from("#__gg_unit as u");
            $query->where('u.id=' . $id_corso);
            $this->_db->setQuery($query);
            $accesso = $this->_db->loadResult();

            $query = $this->_db->getQuery(true);
            $countquery = $this->_db->getQuery(true);
            $query->select("anagrafica.id as id_anagrafica, anagrafica.cognome as cognome, anagrafica.nome as nome, anagrafica.fields as fields");
            $countquery->select("count(*)");
            $query->from("#__gg_report_users as anagrafica");
            $countquery->from("#__gg_report_users as anagrafica");

//            $usergroups --> aziende

            //INNER E CONDIZIONI SULLA BASE DELL'ACCESSO
            switch ($accesso) {

                case 'coupon':
                    $query->join('inner', '#__gg_coupon as c on c.id_utente=anagrafica.id_user');
                    $countquery->join('inner', '#__gg_coupon as c on c.id_utente=anagrafica.id_user');
                    $query->where('c.corsi_abilitati like ' . $id_corso);
                    $countquery->where('c.corsi_abilitati like ' . $id_corso);
                    break;

                case "gruppo":
//                  da unit id ad gruppo acesso corso,
                    $model_unita = new gglmsModelUnita();
                    $id_gruppo_corso = $model_unita->get_gruppo_accesso_corso($id_corso);

                    $query->join('inner', '#__gg_coupon as c on c.id_utente=anagrafica.id_user');
                    $countquery->join('inner', '#__gg_coupon as c on c.id_utente=anagrafica.id_user');

                    $query->where('c.id_gruppi =' . $id_gruppo_corso);
                    $countquery->where('c.id_gruppi =' . $id_gruppo_corso);


                    break;

                default:
                    $query->join('inner', '#__gg_unit as u on anagrafica.id_event_booking=u.id_event_booking');
                    $countquery->join('inner', '#__gg_unit as u on anagrafica.id_event_booking=u.id_event_booking');
                    $query->where('u.id=' . $id_corso);
                    $countquery->where('u.id=' . $id_corso);
                    break;
            }

            if ($gruppo_azienda != null) {
                $query->join('inner', '#__user_usergroup_map as um on anagrafica.id_user=um.user_id');
                $countquery->join('inner', '#__user_usergroup_map as um on anagrafica.id_user=um.user_id');
                $query->where('um.group_id=' . $gruppo_azienda);
//
                $countquery->where('um.group_id=' . $gruppo_azienda);

            }
            if ($accesso == "gruppo") {

                // escludo gli utenti tutor aziendali e tutor piattaforma, solo per gruppo (?)
                $_config = new gglmsModelConfig();
                $id_gruppo_tutor_aziendale = $_config->getConfigValue('id_gruppo_tutor_aziendale');
                $id_gruppo_tutor_piattaforma = $_config->getConfigValue('id_gruppo_tutor_piattaforma');
                $query->where('um.group_id not in (' . $id_gruppo_tutor_aziendale . ', ' . $id_gruppo_tutor_piattaforma . ')');
            }


            //$query->where('anagrafica.id=11497');
            if ($searchPrase != null) {
                $query->where('anagrafica.fields LIKE \'%' . $searchPrase . '%\'');
                $countquery->where('anagrafica.fields LIKE \'%' . $searchPrase . '%\'');
            }

            if ($anagrafica_filter != null) {
                $query->where('anagrafica.id in(' . $anagrafica_filter . ')');
                $countquery->where('anagrafica.id in(' . $anagrafica_filter . ')');
            }
//            $query->order('anagrafica.cognome', 'asc');
            // ordinamento per colonna - di default per id utente
            if (!is_null($sort)
                && !is_null($order)) {
                $query = $query->order($sort . ' ' . $order);
            }
            else
                $query = $query->order('anagrafica.cognome', 'asc');



            $this->_db->setQuery($query,$offset,$limit);
            $rows = $this->_db->loadAssocList();

            foreach ($rows as &$row) {//FILTRO PER CAMPI DI FIELDS
                $row['fields'] = json_decode($row['fields']);
                unset($row['fields']->password);
  //              $row['fields'] = json_encode($row['fields']);
            }
            $this->_db->setQuery($countquery);
            $count = $this->_db->loadResult();
            return [$rows, $count, $query, $countquery];
        } catch (Exception $e) {

            DEBUGG::log('ERRORE DA GETDATA:' . json_encode($e->getMessage()), 'ERRORE DA GET DATA', 1, 1);
            //DEBUGG::error($e, 'error', 1);
        }
    }

    private function buildPrimaryDataCube($query, $offset = null, $limit = null)
    {
        try {


            $this->_db->setQuery($query);

            $query->setLimit($limit, $offset);

            $datas = $this->_db->loadAssocList();
            return $datas;

        } catch (Exception $e) {

            DEBUGG::log('ERRORE DA GETDATA:' . json_encode($e->getMessage()), 'ERRORE DA GET DATA', 1, 1);
            //DEBUGG::error($e, 'error', 1);
        }
    }

    private function countPrimaryDataCube($query)
    {
        try {


            $this->_db->setQuery($query);
            $datas = $this->_db->loadAssocList();
            return count($datas);

        } catch (Exception $e) {

            DEBUGG::log('ERRORE DA GETDATA:' . json_encode($e->getMessage()), 'ERRORE DA GET DATA', 1, 1);
            //DEBUGG::error($e, 'error', 1);
        }

    }

    private function buildColumnsforUnitaView($id_corso)
    {

        $reportObj = new gglmsModelReport();
        $unitas = $reportObj->getSottoUnitaArrayList($id_corso);
        $columns = ['id_anagrafica', 'cognome', 'nome', 'fields','attestati_hidden'];
        foreach ($unitas as $unita) {
            array_push($columns, $unita['titolo']);
        }
        return $columns;
    }

    private function buildColumnsforContenutiView($id_corso)
    {

        $reportObj = new gglmsModelReport();
        $contenuti = $reportObj->getContenutiArrayList($id_corso);

        $columns = ['id_anagrafica', 'cognome', 'nome', 'fields','attestati_hidden'];
        foreach ($contenuti as $contenuto) {
            array_push($columns, $contenuto['titolo']);
        }
        return $columns;
    }

    private function buildPivot($basearray, $columns, $nullvalue)
    {
        $fields = explode(',', $this->_params->get('campicustom_report'));

        $table = array();

        foreach ($basearray as $item) {

            foreach ($columns as $column) {


                if (isset($item[$column])) {
                    $row{$column} = $item[$column];
                } else {
                    $row{$column} = $nullvalue;
                }
            }
            //non serve json
//            $userFields = json_decode($row['fields']);
              $userFields = $row['fields'];


            foreach ($fields as $field) {
                if (isset($userFields->$field)) {
                    $row{$field} = $userFields->$field;
                } else {
                    $row{$field} = $nullvalue;
                }
            }

            array_push($table, $row);

        }
        return $table;
    }

    private function addColumn($basearray, $arraytoadd, $key, $newcolumname, $columnvalue, $typeofjoin)
    {

        $innerjoinedarray = array();
        foreach ($arraytoadd as $newitem) {

            foreach ($basearray as &$item) {

                if ($newitem[$key] == $item[$key]) {

                    if ($newcolumname != null) {
                        $item{$newitem[$newcolumname]} = $newitem[$columnvalue];
                        array_push($innerjoinedarray, $item);
                    } else {
                        $item{$columnvalue} = $newitem[$columnvalue];
                        array_push($innerjoinedarray, $item);

                    }
                }
                unset($item);
            }
        }
        if ($typeofjoin == 'inner') {

            return $innerjoinedarray;
        } else {

            return $basearray;

        }
    }


    public function get_csv()
    {
        //ini_set('max_execution_time', 600);
        $this->_japp = JFactory::getApplication();
        //$csvlimit=$this->_filterparam->csvlimit;
        $csvlimit = 1;
        $id_chiamata = $this->_filterparam->id_chiamata;
        $data = $this->new_get_data();
        $this->createCSV($data['rows'], $id_corso = explode('|', $this->_filterparam->corso_id)[0]);


        /*
        if($csvlimit>0) { //COSI' LA PRIMA CHIAMATA, PER IL TOTALE, NON GENERA RECORD
            foreach ($data['rows'] as $row) {

                try {

                    $insertquery = "INSERT INTO #__gg_csv_report VALUES (";
                    $insertquery = $insertquery . $id_chiamata . ",";
                    $insertquery = $insertquery . $row['id_utente'] . ",";
                    $insertquery = $insertquery . "'" . addslashes($row['nome']) . "',";
                    $insertquery = $insertquery . "'" . addslashes($row['cognome']) . "',";
                    $insertquery = $insertquery . "'" . addslashes($row['fields']) . "',";
                    $insertquery = $insertquery . "'" . $row['email'] . "',";
                    $insertquery = $insertquery . $row['stato'] . ",";
                    $insertquery = $insertquery . "'" . $row['hainiziato'] . "',";
                    $insertquery = $insertquery . "'" . $row['hacompletato'] . "',";


                    if($param_colonne_somme) {
                        $insertquery = $insertquery . "'" . $row['tempo_lavorativo'] . "',";
                        $insertquery = $insertquery . "'" . $row['tempo_straordinario'] . "',";
                    }else{
                        $insertquery = $insertquery . "null,";
                        $insertquery = $insertquery . "null,";

                    }
                    $insertquery = $insertquery . $row['alert'] . ")";



                    $this->_db->setQuery($insertquery);
                    $this->_db->execute();
                }catch (Exception $exception){
                    echo $exception->getMessage();
                }

            }

        }

        echo  json_encode($data);
*/
        //$this->_japp->close();
    }

    /*
     * Export CSV per reportistica legata alla visualizzazione di contenuti e relativa durata
     * */
    public function get_csv_report_ore_corso() {

        $this->_japp = JFactory::getApplication();

        $data = $this->new_get_ore_corso();
        $this->createCSV($data['rows'], $id_corso = explode('|', $this->_filterparam->corso_id)[0]);

    }

    public function createCSV($rows, $corso_id)
    {

        $elenco_campi_per_csv_da_back_end = explode(',', $this->_params->get('campi_csv'));
        if ($elenco_campi_per_csv_da_back_end[0] != 'no_column') {
            $added_colums_rows = [];
            //var_dump($elenco_campi_per_csv_da_back_end);
            foreach ($rows as $row) {
                $rowfields = (array)json_decode($row['fields']);

                foreach ($elenco_campi_per_csv_da_back_end as $nuovacolonna => $nuovovalore) {
                    if (array_key_exists($nuovovalore, $rowfields))
                        $row[$nuovovalore] = $rowfields[$nuovovalore];
                }
                array_push($added_colums_rows, $row);
            }
            $rows = $added_colums_rows;
        }

        $csv_row_filters = ['fields'];//CAMPI DA NON MOSTRARE NEL CSV
        foreach ($rows as &$row) {
            foreach ($csv_row_filters as $csv_row_filter) {
                unset($row[$csv_row_filter]);
            }
        }
        try {
            if (!empty($rows)) {
                $comma = ';';
                $quote = '"';
                $CR = "\015\012";
                // Make csv rows for field name
                $i = 0;
                $fields = $rows[0];

                $cnt_fields = count($fields);
                $csv_fields = '';

                foreach ($fields as $name => $val) {
                    $i++;
                    if ($cnt_fields <= $i) $comma = '';
                    $csv_fields .= $quote . $name . $quote . $comma;


                }

                // Make csv rows for data
                $csv_values = '';
                foreach ($rows as $row_) {
                    $i = 0;
                    $comma = ';';
                    foreach ($row_ as $name => $val) {
                        $i++;
                        if ($cnt_fields <= $i) $comma = '';
                        $csv_values .= $quote . $val . $quote . $comma;
                    }
                    $csv_values .= $CR;
                }

                //echo ($csv_values);

                $csv_save = $csv_fields . $CR . $csv_values;
            }
            echo $csv_save;


            $filename = $this->get_CourseName($corso_id);

            $filename = preg_replace('~[^\\pL\d]+~u', '_', $filename);
            $filename = iconv('utf-8', 'us-ascii//TRANSLIT', $filename);
            $filename = strtolower($filename);
            $filename = trim($filename, '_');
            $filename = preg_replace('~[^-\w]+~', '', $filename);
            $filename .= "-" . date("d/m/Y");
            $filename = $filename . ".csv";

            //var_dump($filename);die;


            header("Content-Type: text/plain");
            header("Content-disposition: attachment; filename=$filename");
            header("Content-Transfer-Encoding: binary");
            header("Pragma: no-cache");
            header("Expires: 0");
        } catch (exceptions $exception) {
            echo $exception->getMessage();
        }
        $this->_japp->close();
    }

    private function get_CourseName($corso_id)
    {
        $query = $this->_db->getQuery(true);
        $query->select('titolo');
        $query->from('#__gg_unit as u');
        $query->where('u.id= ' . $corso_id);
        $this->_db->setQuery($query);
        $titolo = $this->_db->loadResult();

        return $titolo;
    }

    public function buildDettaglioCorso()
    {

        $id_utente = (int)$_GET['id_utente'];
        $id_corso = (int)$_GET['id_corso'];
        $query = $this->_db->getQuery(true);
        $query->select('u.titolo as \'titolo unità\',c.titolo as \'titolo contenuto\',
                      IF (r.stato=1, \'completato\',\'non completato\') as \'stato\', r.`data` as \'data\'');
        $query->from('#__gg_report as r');
        $query->join('inner', '#__gg_unit as u on r.id_unita=u.id');
        $query->join('inner', '#__gg_contenuti c on r.id_contenuto=c.id');
        $query->join('inner', '#__gg_unit_map um on c.id=um.idcontenuto');
        $query->where('id_utente=' . $id_utente);
        $query->where('id_corso=' . $id_corso);
        $query = $query . " order by u.ordinamento, u.id, um.ordinamento ,r.`data`";
        $this->_db->setQuery($query);
        $result = $this->_db->loadAssocList();
        echo json_encode($result);
        $this->_japp->close();
    }

    public function sendMail()
    {
        try {
            $to = (string)$this->_filterparam->to;

            //$to="a.petruzzella71@gmail.com";
            $oggettomail = $this->_filterparam->oggettomail;
            $testomail = $this->_filterparam->testomail;
            $recipients = array();
            array_push($recipients, $to);
            $mailer = JFactory::getMailer();
            $config = JFactory::getConfig();
            $sender = array(
                $config->get('mailfrom'),
                $config->get('fromname')
            );

            $mailer->setSender($sender);

            $mailer->addRecipient($recipients);
            $mailer->setSubject($oggettomail);
            $mailer->setBody($testomail);

            $send = $mailer->Send();

            echo json_encode($send);
        } catch (exceptions $exception) {
            echo $exception->getMessage();

        }
        $this->_japp->close();
    }

    public function sendAllMail()
    {
        try {
            $id_corso = explode('|', $this->_filterparam->corso_id)[0];
            $id_contenuto = explode('|', $this->_filterparam->corso_id)[1];
            $group_id = $this->_filterparam->usergroups;
            $alert_days_before = $this->_params->get('alert_days_before');
            $query = $this->_db->getQuery(true);
            $query->select('DISTINCT users.email');
            $query->from('#__gg_report as r');
            $query->join('inner', '#__gg_report_users as anagrafica ON anagrafica.id = r.id_anagrafica');
            $query->join('inner', '#__users as users on r.id_utente=users.id');
            $query->join('inner', '#__gg_unit as un on r.id_corso=un.id');
            $query->join('inner', '#__user_usergroup_map as gruppo  ON gruppo.user_id = r.id_utente');
            $query->where('id_corso=' . $id_corso);
            $query->where('group_id=' . $group_id);
            $query->where('IF(date(now())>DATE_ADD(un.data_fine, INTERVAL -' . $alert_days_before . ' DAY),	IF((select r2.stato from #__gg_report as r2 where r2.id_utente = r.id_utente
                                and id_contenuto=' . $id_contenuto . ' and stato = 1 limit 1),0,1),0)=1');
            $query->where('r.id_utente NOT IN (SELECT r.id_utente FROM #__gg_report as r INNER JOIN  #__user_usergroup_map as gruppo  ON gruppo.user_id = r.id_utente
                               WHERE r.id_corso = ' . $id_corso . ' AND r.stato = 1 and  r.id_contenuto=' . $id_contenuto . ' AND group_id = ' . $group_id . ')');


            $this->_db->setQuery($query);
            $rows = $this->_db->loadAssocList();

            $oggettomail = $this->_filterparam->oggettomail;
            $testomail = $this->_filterparam->testomail;
            $recipients = array_column($rows, 'email');


            $mailer = JFactory::getMailer();
            $config = JFactory::getConfig();
            $sender = array(
                $config->get('mailfrom'),
                $config->get('fromname')
            );

            $mailer->setSender($sender);
#@_
            $mailer->addRecipient($recipients);
            $mailer->setSubject($oggettomail);
            $mailer->setBody($testomail);

            //$send = $mailer->Send();  ATTENZIONE IL VERO INVIO E' DISABILITATO IN PROVA

            //echo $send;
        } catch (exceptions $exception) {
            echo $exception->getMessage();

        }
        $this->_japp->close();

    }

    private function buildSelectColonneTempi($id_corso)
    {

        $orario_inizio = '09:00:00';
        $orario_fine = '17:00:00';
        $select_somma_log_fascia_int = '(select SEC_TO_TIME((select IFNULL(sum(permanenza),0) from #__gg_log as l where l.id_utente=r.id_utente and  l.id_contenuto in (select id_contenuto from #__gg_report where id_utente=l.id_utente and id_corso=' . $id_corso . ') and TIME(l.data_accesso)>\'' . $orario_inizio . '\' and TIME(l.data_accesso)<\'' . $orario_fine . '\')';
        $select_somma_log_fascia_est = '(select SEC_TO_TIME((select IFNULL(sum(permanenza),0) from #__gg_log as l where l.id_utente=r.id_utente and  l.id_contenuto in (select id_contenuto from #__gg_report where id_utente=l.id_utente and id_corso=' . $id_corso . ') and (TIME(l.data_accesso)<\'' . $orario_inizio . '\' or TIME(l.data_accesso)>\'' . $orario_fine . '\')';


        $sub_query = 'select distinct c.id_quizdeluxe from #__gg_contenuti as c inner join #__gg_report as r on c.id=r.id_contenuto where c.tipologia = 7 AND id_corso=' . $id_corso;
        $this->_db->setQuery($sub_query);
        $id_quizzes = $this->_db->loadAssocList();

        $in_quizzes = '';
        if (count($id_quizzes) > 0) {
            foreach ($id_quizzes as $id_quiz) {

                $in_quizzes = $in_quizzes . ',' . $id_quiz['id_quizdeluxe'];
            }
            $in_quizzes = substr($in_quizzes, 1);
            //echo $in_quizzes;
            $select_somma_quiz_fascia_int = '(select ifnull(sum(c_total_time),0) from #__quiz_r_student_quiz where c_quiz_id in (' . $in_quizzes . ') and c_student_id=r.id_utente and TIME(c_date_time) > \'' . $orario_inizio . '\' and TIME(c_date_time) < \'' . $orario_fine . '\')))';
            $select_somma_quiz_fascia_est = '(select ifnull(sum(c_total_time),0) from #__quiz_r_student_quiz where c_quiz_id in (' . $in_quizzes . ') and c_student_id=r.id_utente and (TIME(c_date_time) < \'' . $orario_inizio . '\' or TIME(c_date_time) > \'' . $orario_fine . '\')))))';

            $select_result = $select_somma_log_fascia_int . ' + ' . $select_somma_quiz_fascia_int . 'as tempo_lavorativo, ' . $select_somma_log_fascia_est . '+' . $select_somma_quiz_fascia_est . ' as tempo_straordinario';
        } else {

            $select_result = $select_somma_log_fascia_int . '))as tempo_lavorativo,' . $select_somma_log_fascia_est . '))) as tempo_straordinario';

        }

        return $select_result;


    }

    public function getContenuti()
    {//CONTROLLER CHE SERVE A VEDERSI STAMPARE L'ELENCO DI TUTTI I CONTENUTI DI UNA UNITA E SOTTO UNITA'. NON VIENE USATO IN ALCUNA VISTA

        $id = JRequest::getVar('corso_id');
        $reportModel = new gglmsModelReport();
        $contenuti = $reportModel->getContenutiArrayList($id);


        echo implode('<br>', array_column($contenuti, 'titolo'));
        echo '<br> ' . count($contenuti);
    }

    public function getAttestati($id_corso)
    {
        // prima di eseguire questa istruzione andrebbe caricato il modello unita per id
        // così restituisce NULL
        //$corso_obj = $this->_db->loadObject('gglmsModelUnita');
        $corso_obj = new gglmsModelUnita();
        $corso_obj->setAsCorso($id_corso);

        $all_attestati = $corso_obj->getAllAttestatiByCorso();
        $att_id_array = array();

        //costruiscp array ["id_attestato1#titolo_attestato1","id_attestato2#titolo_attestato2 ]
        // per poterlo splittare nel report e avere sia id che titolo
        foreach ($all_attestati as $att) {
            array_push($att_id_array, $att->id . '#' . $att->titolo);
        }

        $att_id_string = implode('|', $att_id_array);

        return $att_id_string;
    }

    // procedura di emergenza per impostare tutti i form dei corsi per azienda
    public function crea_gruppi_form() {

        try {

            $this->_db->setQuery('SET sql_mode=\'\'');
            $this->_db->execute();

            $query = $this->_db->getQuery(true);
            $query = $query->select('c.id_societa AS id_gruppo_societa,
                                    c.id_gruppi AS gruppo_corsi,
                                    c.gruppo AS id_piattaforma,
                                    ug.title AS nome_societa')
                            ->from('#__gg_coupon c')
                            ->join('inner', '#__usergroups ug ON c.id_societa = ug.id')
                            //->where('c.gruppo != ' . $this->_db->quote(''))
                            ->group('c.id_gruppi')
                            ->order('c.id_societa, c.id_gruppi');

            $this->_db->setQuery($query);
            $results = $this->_db->loadAssocList();

            if (count($results) == 0)
                throw new Exception("Nessuna azienda disponibile, nessun forum verrà creato", 1);

            $_model_coupon = new gglmsModelgeneracoupon();
            $_model_user = new gglmsModelUsers();

            foreach ($results as $key => $company) {

                $this->_db->transactionStart();

                // controllo se esiste il form aziendale
                $parent_id = $_model_coupon->_get_company_forum($company['id_gruppo_societa']);
                // creo il form aziendale se non esiste
                if (null === $parent_id) {

                    // prima di tentare la creazione verifico se l'azienda ha un tutor aziendale, altrimenti non creo nulla perchè la query si spacca
                    $tutor_id = $_model_user->get_tutor_aziendale($company['id_gruppo_societa']);
                    if (is_null($tutor_id)
                        || $tutor_id == "") {
                        DEBUGG::log($company['nome_societa'] . ' non ha tutor aziendale. Impossibile inserire il form azienda', __FUNCTION__, 0, 1, 0);
                        continue;
                    }

                    $_insert_c = $_model_coupon->_create_company_forum($company['id_gruppo_societa'], $company['nome_societa']);
                    // se fallisce per ovvie motivazioni vado avanti
                    if (!$_insert_c) {
                        DEBUGG::log($company['nome_societa'] . ' inserimento form aziendale non riuscito', __FUNCTION__, 0, 1, 0);
                        continue;
                    }
                }

                // mi serve per il modello, controlla il titolo in base al coupon
                $_info_corso = $_model_coupon->get_info_corso($company['gruppo_corsi']);
                if (!isset($_info_corso['titolo'])
                    || $_info_corso['titolo'] == ""
                    || is_null($_info_corso['titolo'])) {
                    DEBUGG::log($company['nome_societa']  . " titolo del corso non impostato per corso id: ". $company['gruppo_corsi'], __FUNCTION__, 0, 1, 0);
                    // salvo almeno l'inserimento del form aziendale se avvenuto
                    $this->_db->transactionCommit();
                    continue;
                }

                $forum_corso = $_model_coupon->_check_corso_forum($company['id_gruppo_societa'], $company['gruppo_corsi']);
                if (empty($forum_corso))
                    $_insert_f = $_model_coupon->_create_corso_forum($company['id_gruppo_societa'],
                                                                    $company['gruppo_corsi'],
                                                                    $company['nome_societa'],
                                                                    $_info_corso);
                if (!$_insert_f)
                    DEBUGG::log($company['nome_societa'] . ' forum non creato per corso ' . $company['gruppo_corsi'], __FUNCTION__, 0, 1, 0);


                $this->_db->transactionCommit();
            }

            echo "Operazione terminata: " . date('d/m/Y H:i:s');

        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            echo __FUNCTION__  . " error: " . $e->getMessage();
        }

        $this->_japp->close();

    }

    // prima versione completamento tutti i contenuti per utente
    public function completa_corsi_per_utente_iscritto() {

        try {

            if (!isset($this->_filterparam->user_id)
                || $this->_filterparam->user_id == "")
                throw new Exception("Nessun utente definito", 1);

            // selezioni tutti i corsi a cui un utente è iscritto
            $select_coupon = $this->_db->getQuery(true)
                                            ->select('id_gruppi')
                                            ->from('#__gg_coupon')
                                            ->where('id_utente = ' . $this->_db->quote($this->_filterparam->user_id));
            $this->_db->setQuery($select_coupon);
            $results_coupon = $this->_db->loadAssocList();

            if (count($results_coupon) == 0)
                throw new Exception("Nessuna corso disponibile per l'utente " . $this->_filterparam->user_id, 1);

            $unit_arr = array();
            $unit_contents = array();

            foreach ($results_coupon as $key_coupon => $coupon) {

                $select_map = $this->_db->getQuery(true)
                                        ->select('um.idcontenuto, um.idunita')
                                        ->from('#__gg_unit_map um')
                                        ->join('inner', '#__gg_usergroup_map ug ON um.idunita = ug.idunita AND ug.idgruppo = ' . $this->_db->quote($coupon['id_gruppi']))
                                        ->order('um.ordinamento');

                $this->_db->setQuery($select_map);
                $results_map = $this->_db->loadAssocList();

                if (count($results_map) == 0) {

                    $select_single = $this->_db->getQuery(true)
                                        ->select('idunita')
                                        ->from('#__gg_usergroup_map')
                                        ->where('idgruppo = ' . $this->_db->quote($coupon['id_gruppi']));

                    $this->_db->setQuery($select_single);
                    $result_single = $this->_db->loadResult();

                    if (!in_array($result_single, $unit_arr)
                        && !is_null($result_single))
                        $unit_arr[] = $result_single;

                }
                else {

                    foreach ($results_map as $key_map => $map) {

                        //$unit_contents[$map['idunita']][] = $map['idcontenuto'];
                        if (!in_array($map['idcontenuto'], $unit_contents))
                            $unit_contents[] = $map['idcontenuto'];

                        if (!in_array($map['idunita'], $unit_arr))
                            $unit_arr[] = $map['idunita'];

                    }

                }

            }

            // in base alle unit recupero i contenuti per le unita figlie delle unita
            foreach ($unit_arr as $unita) {

                $select_figli = $this->_db->getQuery(true)
                                    ->select('id as id_unita')
                                    ->from('#__gg_unit')
                                    ->where('unitapadre = ' . $this->_db->quote($unita));

                $this->_db->setQuery($select_figli);
                $results_figli = $this->_db->loadAssocList();

                if (count($results_figli) == 0)
                    continue;

                foreach ($results_figli as $key_figlio => $figlio) {
                    $select_contenuto_figlio = $this->_db->getQuery(true)
                                                ->select('idcontenuto')
                                                ->from('#__gg_unit_map')
                                                ->where('idunita = ' . $this->_db->quote($figlio['id_unita']))
                                                ->order('ordinamento');

                    $this->_db->setQuery($select_contenuto_figlio);
                    $results_contenuto_figlio = $this->_db->loadAssocList();

                    if (count($results_contenuto_figlio) == 0)
                        continue 2;

                    foreach ($results_contenuto_figlio as $key_figlio_contenuto => $contenuto) {

                        if (!in_array($contenuto['idcontenuto'], $unit_contents))
                            $unit_contents[] = $contenuto['idcontenuto'];

                    }

                }

            }

            // controllo se ci sono contenuti da scrivere
            if (count($unit_contents) == 0)
                throw new Exception("Nessun contenuto da scrivere in scormvars", 1);

            $dt = new DateTime();
            $_date = $dt->format('Y-m-d');
            $ts = $dt->format('Y-m-d H:i:s');

            $this->_db->transactionStart();

            foreach ($unit_contents as $content) {

                $insert_scorm_1 = "INSERT INTO #__gg_scormvars
                                  (scoid, userid, varName, varValue, timestamp)
                                  VALUES (
                                    " . $this->_db->quote($content) . ",
                                    " . $this->_db->quote($this->_filterparam->user_id) . ",
                                    'bookmark',
                                    0,
                                    " . $this->_db->quote($ts) . "
                                    )
                                    ON DUPLICATE KEY UPDATE
                                                varValue = 0,
                                                timestamp = " . $this->_db->quote($ts) . "
                                                ";

                $insert_scorm_2 = "INSERT INTO #__gg_scormvars
                                  (scoid, userid, varName, varValue, timestamp)
                                  VALUES (
                                    " . $this->_db->quote($content) . ",
                                    " . $this->_db->quote($this->_filterparam->user_id) . ",
                                    'cmi.core.count_views',
                                    1,
                                    " . $this->_db->quote($ts) . "
                                    )
                                    ON DUPLICATE KEY UPDATE
                                                varValue = 1,
                                                timestamp = " . $this->_db->quote($ts) . "
                                                ";

                $insert_scorm_3 = "INSERT INTO #__gg_scormvars
                                  (scoid, userid, varName, varValue, timestamp)
                                  VALUES (
                                    " . $this->_db->quote($content) . ",
                                    " . $this->_db->quote($this->_filterparam->user_id) . ",
                                    'cmi.core.last_visit_date',
                                    " . $this->_db->quote($_date) . ",
                                    " . $this->_db->quote($ts) . "
                                    )
                                    ON DUPLICATE KEY UPDATE
                                                varValue = " . $this->_db->quote($_date) . ",
                                                timestamp = " . $this->_db->quote($ts) . "
                                                ";

                $insert_scorm_4 = "INSERT INTO #__gg_scormvars
                                  (scoid, userid, varName, varValue, timestamp)
                                  VALUES (
                                    " . $this->_db->quote($content) . ",
                                    " . $this->_db->quote($this->_filterparam->user_id) . ",
                                    'cmi.core.lesson_status',
                                    'completed',
                                    " . $this->_db->quote($ts) . "
                                    )
                                    ON DUPLICATE KEY UPDATE
                                                timestamp = " . $this->_db->quote($ts) . "
                                                ";

                $insert_scorm_5 = "INSERT INTO #__gg_scormvars
                                  (scoid, userid, varName, varValue, timestamp)
                                  VALUES (
                                    " . $this->_db->quote($content) . ",
                                    " . $this->_db->quote($this->_filterparam->user_id) . ",
                                    'cmi.core.total_time',
                                    9999,
                                    " . $this->_db->quote($ts) . "
                                    )
                                    ON DUPLICATE KEY UPDATE
                                                varValue = 9999,
                                                timestamp = " . $this->_db->quote($ts) . "
                                                ";

                $delete_scorm_1 = "DELETE FROM #__gg_scormvars
                                    WHERE scoid = " . $this->_db->quote($content) . "
                                    AND userid = " . $this->_db->quote($this->_filterparam->user_id) . "
                                    AND varValue = 'cmi.core.lesson_status'
                                    AND varValue = 'init'";

                $this->_db->setQuery($insert_scorm_1);
                if (!$this->_db->execute())
                    throw new Exception("Insert query 1 ko -> " . $insert_scorm_1, 1);

                $this->_db->setQuery($insert_scorm_2);
                if (!$this->_db->execute())
                    throw new Exception("Insert query 2 ko -> " . $insert_scorm_2, 1);

                $this->_db->setQuery($insert_scorm_3);
                if (!$this->_db->execute())
                    throw new Exception("Insert query 3 ko -> " . $insert_scorm_3, 1);

                $this->_db->setQuery($insert_scorm_4);
                if (!$this->_db->execute())
                    throw new Exception("Insert query 4 ko -> " . $insert_scorm_4, 1);

                $this->_db->setQuery($insert_scorm_5);
                if (!$this->_db->execute())
                    throw new Exception("Insert query 5 ko -> " . $insert_scorm_5, 1);

                $this->_db->setQuery($delete_scorm_1);
                if (!$this->_db->execute())
                    throw new Exception("Delete query 1 ko -> " . $delete_scorm_1, 1);

            }

            // seleziono i contenuti init e li aggiorno a completed
            $select_init = $this->_db->getQuery(true)
                            ->select('scoid')
                            ->from('#__gg_scormvars')
                            ->where("varName = 'cmi.core.lesson_status'")
                            ->where("varValue = 'init'")
                            ->where('userid = ' . $this->_db->quote($this->_filterparam->user_id));

            $this->_db->setQuery($select_init);
            $results_init = $this->_db->loadAssocList();

            // se ci sono contenuti init devo convertirli in completed
            if (count($results_init) > 0) {

                foreach ($results_init as $key_init => $init) {

                    $update_init_1 = "UPDATE #__gg_scormvars
                                      SET varValue = 'completed',
                                      timestamp = " . $this->_db->quote($ts) . "
                                        WHERE scoid = " . $this->_db->quote($init['scoid']) . "
                                        AND userid = " . $this->_db->quote($this->_filterparam->user_id) . "
                                        AND varValue = 'init'
                                    ";

                    $update_init_2 = "UPDATE #__gg_scormvars
                                      SET varValue = " . $this->_db->quote($_date) . ",
                                      timestamp = " . $this->_db->quote($ts) . "
                                        WHERE scoid = " . $this->_db->quote($init['scoid']) . "
                                        AND userid = " . $this->_db->quote($this->_filterparam->user_id) . "
                                        AND varValue = 'cmi.core.last_visit_date'
                                    ";

                    $insert_scorm_1 = "INSERT INTO #__gg_scormvars
                                  (scoid, userid, varName, varValue, timestamp)
                                  VALUES (
                                    " . $this->_db->quote($init['scoid']) . ",
                                    " . $this->_db->quote($this->_filterparam->user_id) . ",
                                    'cmi.core.total_time',
                                    9999,
                                    " . $this->_db->quote($ts) . "
                                    )
                                    ON DUPLICATE KEY UPDATE
                                                varValue = 9999,
                                                timestamp = " . $this->_db->quote($ts) . "
                                                ";


                    $this->_db->setQuery($update_init_1);
                    if (!$this->_db->execute())
                        throw new Exception("Update init query 1 ko -> " . $update_init_1, 1);

                    $this->_db->setQuery($update_init_2);
                    if (!$this->_db->execute())
                        throw new Exception("Update init query 2 ko -> " . $update_init_2, 1);

                    $this->_db->setQuery($insert_scorm_1);
                    if (!$this->_db->execute())
                        throw new Exception("Insert init query 2 ko -> " . $insert_scorm_1, 1);


                }

                $this->_db->transactionCommit();

            }


            echo "Operazione terminata: " . date('d/m/Y H:i:s');

        }
        catch(Exception $e) {
            $this->_db->transactionRollback();
            echo __FUNCTION__ . " error: " . $e->getMessage();
        }

        $this->_japp->close();

    }

    // dettagli per quiz ed utente
    public function get_dettagli_quiz() {

        $_ret = array();
        $dettaglio_tot = array();

        try {

            $params = JRequest::get($_GET);
            $quiz_id = $params["quiz_id"];
            $user_id = $params["user_id"];
            $corso_id = $params["corso_id"];
            $all_users = $params["all_users"];

            $model_content = new gglmsModelContenuto();

            if (!isset($all_users)
                || $all_users == "")
                throw new Exception("Missing all users", 1);

            if (!isset($quiz_id)
                || $quiz_id == "")
                throw new Exception("Missing quiz id", 1);

            //nel caso report per tutti utente
            if ($all_users == 1 ) {

                if (!isset($corso_id)
                    || $corso_id == "")
                    throw new Exception("Missing corso id", 1);

                $users_id_arr = UtilityHelper::get_user_iscritti_corso($corso_id);
                if (count($users_id_arr) == 0
                     || !is_array($users_id_arr))
                    throw new Exception("Nessun utente iscritto al corso selezionato", 1);


                $user_model = new gglmsModelUsers();
                $users = $user_model->get_utenti_iscritti_corso($corso_id, $users_id_arr);

                if (is_null($users)
                    || count($users) == 0)
                    throw new Exception("Nessuna anagrafica disponibile per il corso selezionato", 1);


                foreach ($users as $user) {

                    $dettagli_quiz = $model_content->get_dettagli_quiz_per_utente($quiz_id, $user['id_utente']);

                   if (is_null($dettagli_quiz)
                       || !is_array($dettagli_quiz)
                       || count($dettagli_quiz) == 0){

                       //commentata l'exception perche nel caso di tutti utenti si ferma in quello che non ha ancora fatto il quiz
                 //     throw new Exception("Nessun dettaglio disponibile per il quiz e per lo user selezionati", E_USER_ERROR);
                     continue;
                  }

                    $_csv_cols = utilityHelper::get_cols_from_array($dettagli_quiz[0]);

                    $dettagli_quiz = utilityHelper::clean_quiz_array($dettagli_quiz);


                    $nome_user = ["nome" => $user['denominazione_utente']];


                    array_push($dettaglio_tot,$nome_user);

                    array_push($dettaglio_tot,$_csv_cols);

                    foreach ($dettagli_quiz as $dettaglio){
                        array_push($dettaglio_tot,$dettaglio);
                    }

                }


                if(empty($dettaglio_tot)
                    || is_null($dettaglio_tot)){

                    echo "<script type='text/javascript'>
                         if (confirm('Report per utente selezionato non esiste, contattare Help Desk.')){
                                   window.close();
                                }</script>";

                }else {

                    $_export_csv = utilityHelper::_export_csv_dettaglio(time() . '.csv', $dettaglio_tot);

                    // chiusura della finestra dopo generazione del report
                    $_html = <<<HTML
            <script type="text/javascript">
                window.close();
            </script>
HTML;
                }

            } else {

                if (!isset($user_id)
                    || $user_id == "")
                    throw new Exception("Missing user id", 1);


                $dettagli_quiz = $model_content->get_dettagli_quiz_per_utente($quiz_id, $user_id);


                if(empty($dettagli_quiz)
                   || is_null($dettagli_quiz)){

                    echo "<script type='text/javascript'>
                        if (confirm('Report per utente selezionato non esiste, contattare Help Desk.')){
                                 window.close();
                              }</script>";

                }else {

                    $_csv_cols = utilityHelper::get_cols_from_array($dettagli_quiz[0]);
                    $dettagli_quiz = utilityHelper::clean_quiz_array($dettagli_quiz);

                    $_export_csv = utilityHelper::esporta_csv_spout($dettagli_quiz, $_csv_cols, time() . '.csv');


//                 chiusura della finestra dopo generazione del report
                    $_html = <<<HTML
            <script type="text/javascript">
                window.close();
            </script>
HTML;
                }
            }

        }
        catch (Exception $e) {
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'get_dettagli_quiz');
            $_ret['error'] = $e->getMessage();
        }


        $this->_japp->close();
    }

    // interazione con API zoom
    public function get_local_events() {

        $_ret = array();

        try {

            $_zoom_model = new gglmsModelZoom();
            $_events = $_zoom_model->get_local_events($this->_filterparam->zoom_event_id);

            if (is_null($_events)
                || !isset($_events['success']))
                throw new Exception("Non è stato salvato nessun evento", 1);

            // devo salvare il report
            if (isset($this->_filterparam->zoom_event_id)
                && $this->_filterparam->zoom_event_id != "") {

                if (!isset($_events['success'])
                    || !isset($_events['success'][0]['response'])
                    || $_events['success'][0]['response'] == "")
                    throw new Exception("Il servizio non ha prodotto alcuna risposta", 1);

                $_event_json = json_decode($_events['success'][0]['response']);
                $_event_arr = (array) $_event_json;

                $_csv_cols = utilityHelper::get_cols_from_array((array) $_event_arr[0]);
                $_participants = $_event_arr;

                $_export_csv = utilityHelper::esporta_csv_spout($_participants, $_csv_cols, time() . '.csv');
                $this->_japp->close();

            }

            $_ret['success'] = $_events['success'];

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        echo json_encode($_ret);
        $this->_japp->close();

    }

    public function get_event_participants() {

        $_ret = array();

        try {

            $_config = new gglmsModelConfig();
            $api_key = $_config->getConfigValue('zoom_api_key');
            $api_secret = $_config->getConfigValue('zoom_api_secret');
            $api_endpoint = $_config->getConfigValue('zoom_api_endpoint');
            $api_version = $_config->getConfigValue('zoom_api_version');
            $api_scadenza_token = $_config->getConfigValue('zoom_api_scadenza_token');
            $_csv_cols = null;
            $_participants = null;

            $zoom_call = new gglmsControllerZoom($api_key, $api_secret, $api_endpoint, $api_version, $api_scadenza_token, true);

            $event_id = urlencode($this->_filterparam->zoom_event_id);
            $_events = $zoom_call->get_event_participants($event_id, $this->_filterparam->zoom_tipo, $this->_filterparam->zoom_event_type);


            if (isset($_events['error']))
                throw new Exception($_events['error'], 1);

            if (!isset($_events['success'])
                || $_events['success'] == "")
                throw new Exception("Il servizio non ha prodotto alcuna risposta", 1);

            $_response = json_decode($_events['success']);

            if (!isset($_response->participants)
                || count($_response->participants) == 0)
                throw new Exception("Nessun dettaglio disponibile per l'evento selezionato", 1);

            // conversione date nella timezone corretta - zoom registra le date sul meridiano di Greenwich
            $_event_json = UtilityHelper::convert_zoom_response($_response);

            // inserisco l'evento a database se non è già presente
            $_zoom_model = new gglmsModelZoom();
            $_get_event = $_zoom_model->get_event($event_id, $this->_filterparam->zoom_tipo);

            if (is_null($_get_event)
                || !is_array($_get_event)) {
                $_store_event = $_zoom_model->store_events($event_id,
                                                            $this->_filterparam->zoom_tipo,
                                                            $this->_filterparam->zoom_event_label,
                                                            $_event_json->participants);
                if (!is_array($_store_event))
                    throw new Exception($_store_event, 1);

                // ricavo le colonne dall'oggetto
                $_csv_cols = utilityHelper::get_cols_from_array((array) $_event_json->participants[0]);
                $_participants = $_event_json->participants;
            }
            else {
                $_json = $_get_event['success']['response'];
                $_participants = json_decode($_json);
                $_csv_cols = utilityHelper::get_cols_from_array((array) $_participants[0]);

            }

            $_export_csv = utilityHelper::esporta_csv_spout($_participants, $_csv_cols, time() . '.csv');

            // chiusura della finestra dopo generazione del report
            $_html = <<<HTML
            <script type="text/javascript">
                window.close();
            </script>
HTML;

            //echo $_html;
        }
        catch (Exception $e) {
            //$_ret['error'] = $e->getMessage();
            echo $e->getMessage();
        }

        $this->_japp->close();
    }

    public function get_event_list() {

        $_ret = array();

        try {

            $_config = new gglmsModelConfig();
            $api_key = $_config->getConfigValue('zoom_api_key');
            $api_secret = $_config->getConfigValue('zoom_api_secret');
            $api_endpoint = $_config->getConfigValue('zoom_api_endpoint');
            $api_version = $_config->getConfigValue('zoom_api_version');
            $api_scadenza_token = $_config->getConfigValue('zoom_api_scadenza_token');

            $zoom_call = new gglmsControllerZoom($api_key, $api_secret, $api_endpoint, $api_version, $api_scadenza_token, true);
            $_events = $zoom_call->get_events($this->_filterparam->zoom_user, $this->_filterparam->zoom_tipo, $this->_filterparam->zoom_mese);

            if (isset($_events['error']))
                throw new Exception($_events['error'], 1);

            if (!isset($_events['success'])
                || $_events['success'] == "")
                throw new Exception("Il servizio non ha prodotto alcuna risposta", 1);

            $_event_json = json_decode($_events['success']);
            $_event_arr = (array) $_event_json;

            if (!isset($_event_arr[$this->_filterparam->zoom_tipo]))
                throw new Exception("Nessun " . $this->_filterparam->zoom_tipo . " disponbile");

            $_ret['success'] = $_event_arr[$this->_filterparam->zoom_tipo];

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        echo json_encode($_ret);
        $this->_japp->close();

    }

    public function attivazione_coupons_utenti_ep() {

        $id_piattaforma = $this->_filterparam->id_piattaforma;
        echo $this->attivazione_coupons_utenti($id_piattaforma);

        $this->_japp->close();

    }

    public function checkVoucherValidation() {

        $retArr = [];

        try {

            $requestedVoucher = $this->_filterparam->searchPhrase;
            $requestedToken = $this->_filterparam->cToken;

            if (!isset($requestedVoucher)
                || $requestedVoucher == ""
                )
                throw new Exception("Codice voucher non valorizzato", E_USER_ERROR);

            $decryptedToken = utilityHelper::decrypt_random_token($requestedToken);
            $parsedToken = explode("|==|", $decryptedToken);

            if (!isset($parsedToken[0]) || $parsedToken[0] == "" || !is_numeric($parsedToken[0]))
                throw new Exception("Nessun token di riferimento", E_USER_ERROR);

            $userId = $parsedToken[0];
            $dt = new DateTime();
            $annoCorrente = $dt->format('Y');

            // controllo se il codice del voucher esiste e non è già stato speso
            $checkVoucher = "SELECT *
                            FROM #__gg_quote_voucher
                            WHERE code = " . $this->_db->quote(trim(strtoupper($requestedVoucher))) . "
                            AND user_id IS NULL";
            $this->_db->setQuery($checkVoucher);
            $resultVoucher = $this->_db->loadResult();

            if (!$resultVoucher) throw new Exception("Il codice immesso non è stato trovato oppure è già stato utilizzato", E_USER_ERROR);

            // controllo se l'utente ha già utilizzato un voucher per l'anno corrente
            $checkUserForYear = "SELECT *
                                    FROM #__gg_quote_voucher
                                    WHERE user_id = " . $this->_db->quote($userId) . "
                                    AND date LIKE " . $this->_db->quote("%" . $annoCorrente . "%");

            $this->_db->setQuery($checkUserForYear);
            $resultUserForYear = $this->_db->loadResult();

            if (!is_null($resultUserForYear))
                throw new Exception("Esistono voucher per l'utente corrente nell'anno " . $annoCorrente, E_USER_ERROR);


            $retArr['success'] = "Codice voucher utilizzabile";

        }
        catch(Exception $e) {
            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            $retArr['error'] = "Codice voucher non utilizzabile: " . $e->getMessage();
        }

        echo json_encode($retArr);
        $this->_japp->close();

    }

    public function sinpeCheckEmail() {

        try {

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                if (isset($_POST['email']) && $_POST['email']) {
                    $emailCheck = utilityHelper::check_user_by_column_row('email', $_POST['email']);
                    if (!is_null($emailCheck)) throw new Exception("L'Email selezionata è già esistente", E_USER_ERROR);
                }

                if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) throw new Exception("L'Email selezionata non è valida: " . $_POST['email'], E_USER_ERROR);

            }

            $response['success'] = time();

        }
        catch(Exception $e) {
            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage() . " -> ". print_r($_POST, true), __FUNCTION__ . "_error");
            $response['error'] = $e->getMessage();
        }

        echo json_encode($response);

        $this->_japp->close();
    }

    public function sinpeCheckUserName() {

        try {

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                if (isset($_POST['username']) && $_POST['username']) {
                    $usernameCheck = utilityHelper::check_user_by_column_row('username', $_POST['username']);
                    if (!is_null($usernameCheck)) throw new Exception("Lo Username selezionato è già esistente", E_USER_ERROR);
                }

            }

            $response['success'] = time();

        }
        catch(Exception $e) {
            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage() . " -> ". print_r($_POST, true), __FUNCTION__ . "_error");
            $response['error'] = $e->getMessage();
        }

        echo json_encode($response);

        $this->_japp->close();
    }

    public function sinpeCheckCodiceFiscale() {

        $response = [];
        try {

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                if (isset($_POST['cf']) && $_POST['cf']) {
                    $comprofilerCheck = utilityHelper::check_comprofiler_by_column_row('cb_codicefiscale', $_POST['cf']);

                    if ($comprofilerCheck) {

                        // controllo se l'utente è online oppure moroso
                        $_params = utilityHelper::get_params_from_plugin();
                        $gruppi_online = utilityHelper::get_ug_from_object($_params, "ug_online");
                        $gruppi_moroso = utilityHelper::get_ug_from_object($_params, "ug_moroso");
                        $gruppi_decaduto = utilityHelper::get_ug_from_object($_params, "ug_decaduto");
                        $gruppi_solo_evento = utilityHelper::get_ug_from_object($_params,"ug_nonsocio");

                        // online
                        if (utilityHelper::check_user_into_ug($comprofilerCheck['user_id'], explode(",", $gruppi_online)))
                            throw new Exception(JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_ERR_ONLINE'), E_USER_ERROR);

                        // moroso
                        if (utilityHelper::check_user_into_ug($comprofilerCheck['user_id'], explode(",", $gruppi_moroso)))
                            throw new Exception(JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_ERR_MOROSO'), E_USER_ERROR);

                        // decaduto
                        if (utilityHelper::check_user_into_ug($comprofilerCheck['user_id'], explode(",", $gruppi_decaduto)))
                            throw new Exception(JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_ERR_DECADUTO'), E_USER_ERROR);

                        // solo evento
                        if (utilityHelper::check_user_into_ug($comprofilerCheck['user_id'], explode(",", $gruppi_solo_evento))){
                            $response["soloEventi"]=true;
                            throw new Exception(JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_ERR_SOLO_EVENTO'), E_USER_ERROR);
                            }

                        // cf esistente
                        throw new Exception("L'utente con il codice fiscale ". strtoupper($_POST['cf']) . " è esistente" , E_USER_ERROR);
                    }
                }

            }

            $response['success'] = time();

        }
        catch(Exception $e) {
            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage() . " -> ". print_r($_POST, true), __FUNCTION__ . "_error");
            $response['error'] = $e->getMessage();
        }

        echo json_encode($response);

        $this->_japp->close();
    }

    public function sinpeWatchWebinar() {

        $response = [];
        $startTransaction = false;

        try {

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                $jsonContent = file_get_contents('php://input');
                $decoded = json_decode($jsonContent, true);

                //if (!$decoded) throw new Exception("Nessun dato valido per essere elaborato", E_USER_ERROR);

                // validazione campi
                if (!isset($decoded['cb_nome']) || $decoded['cb_nome'] == "") throw new Exception("Compilare il campo Nome!", E_USER_ERROR);
                if (!isset($decoded['cb_cognome']) || $decoded['cb_cognome'] == "") throw new Exception("Compilare il campo Cognome!", E_USER_ERROR);
                if (!isset($decoded['email_utente']) || $decoded['email_utente'] == "") throw new Exception("Compilare il campo Email!", E_USER_ERROR);
                if (!isset($decoded['cb_ordine']) || $decoded['cb_ordine'] == "") throw new Exception("Compilare il campo Ordine!", E_USER_ERROR);
                if (!isset($decoded['cb_numeroiscrizione']) || $decoded['cb_numeroiscrizione'] == "") throw new Exception("Compilare il campo Numero iscrizione!", E_USER_ERROR);
                if (!isset($decoded['cb_professionedisciplina']) || $decoded['cb_professionedisciplina'] == "") throw new Exception("Compilare il campo Professione/Disciplina!", E_USER_ERROR);
                if (!isset($decoded['tts']) || $decoded['tts'] == "") throw new Exception("Nessun riferimento token valorizzato!", E_USER_ERROR);

                if (!filter_var($decoded['email_utente'], FILTER_VALIDATE_EMAIL)) throw new Exception("EMAIL NON VALIDA: " . $decoded['email_utente'], E_USER_ERROR);

                $this->_db->transactionStart();
                $startTransaction = true;

                $query = "INSERT INTO #__gg_watching_video
                             (
                                nome,
                                cognome,
                                email,
                                ordine,
                                numeroiscrizione,
                                professione
                              )
                      VALUES (
                          ". $this->_db->quote($decoded['cb_nome']) .",
                          ". $this->_db->quote($decoded['cb_cognome']) .",
                          ". $this->_db->quote($decoded['email_utente']) .",
                          ". $this->_db->quote($decoded['cb_ordine']) .",
                          ". $this->_db->quote($decoded['cb_numeroiscrizione']) .",
                          ". $this->_db->quote($decoded['cb_professionedisciplina']) ."
                           )";

                $this->_db->setQuery($query);
                $result = $this->_db->execute();

                if (!$result) throw new Exception("Inserimento anagrafica fallito! Si è verificato un errore: " . $result, E_USER_ERROR);

                $this->_db->transactionCommit();

                $response['success'] = "tuttook";
                $response['token'] = utilityHelper::build_randon_token($decoded['tts']);

            }

        }
        catch(Exception $e) {
            if ($startTransaction) $this->_db->transactionRollback();

            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage() . " -> ". print_r($_POST, true), __FUNCTION__ . "_error");
            $response['error'] = $e->getMessage();
        }

        echo json_encode($response);

        $this->_japp->close();

    }

    public function sinpeRegistrationAction() {

        $response = [];
        $startTransaction = false;
        try {

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                $jsonContent = file_get_contents('php://input');
                $decoded = json_decode($jsonContent, true);

                if (!$decoded || !isset($decoded['request_obj'])) throw new Exception("Nessun dato valido per essere elaborato", E_USER_ERROR);

                $_new_user = [];
                $_new_user_cp = [];
                $dt = new DateTime();
                $userModel = new gglmsModelUsers();
                $voucher_code = null;

                foreach ($decoded['request_obj'] as $sub_key => $sub_arr) {

                    if (isset($sub_arr['cb'])
                        && $sub_arr['cb'] == 'cb_nome') {
                        $nome_utente = preg_replace("/[^a-zA-Z]/", "", $decoded['request_obj'] [$sub_key]['value']);
                    }
                    else if (isset($sub_arr['cb'])
                        && $sub_arr['cb'] == 'cb_cognome') {
                        $cognome_utente = preg_replace("/[^a-zA-Z]/", "", $sub_arr['value']);
                    }
                    else if (isset($sub_arr['campo'])
                        && $sub_arr['campo'] == 'email_utente') {
                        $email_utente = $sub_arr['value'];
                    }
                    else if (isset($sub_arr['campo'])
                        && $sub_arr['campo'] == 'username') {
                        $username = $sub_arr['value'];
                    }
                    else if (isset($sub_arr['campo'])
                        && $sub_arr['campo'] == 'password_utente') {
                        $password_utente = $sub_arr['value'];
                    }
                    else if (isset($sub_arr['cb'])
                        && $sub_arr['cb'] == 'cb_codicefiscale') {
                        $cf_utente = strtoupper($sub_arr['value']);
                    }
                    else if (isset($sub_arr['campo'])
                        && $sub_arr['campo'] == 'cb_datadinascita') {
                        // format date artigianale
                        $_tmp_date = date("Y-m-d", strtotime(str_replace('/', '-', trim($sub_arr['value']))));
                        $sub_arr['value'] = $_tmp_date;
                    }
                    else if (isset($sub_arr['cb'])
                        && $sub_arr['cb'] == 'cb_privacy') {
                        $sub_arr['value'] = $sub_arr['value'] == 1 ? 1 : 0;
                    }
                    else if (isset($sub_arr['cb'])
                        && $sub_arr['cb'] == 'cb_dtai_immagini') {
                        $sub_arr['value'] = $sub_arr['value'] == 1 ? 1 : 0;
                    }
                    else if (isset($sub_arr['cb'])
                        && $sub_arr['cb'] == 'cb_statuto') {
                        $sub_arr['value'] = $sub_arr['value'] == 1 ? 1 : 0;
                    }
                    else if (isset($sub_arr['cb'])
                        && $sub_arr['cb'] == 'cb_newsletter') {
                        $sub_arr['value'] = $sub_arr['value'] == 1 ? 1 : 0;
                    }
                    else if (isset($sub_arr['cb'])
                        && $sub_arr['cb'] == 'cb_accessonutritiononline') {
                        $sub_arr['value'] = $sub_arr['value'] == 1 ? 1 : 0;
                    }
                    else if (isset($sub_arr['cb'])
                        && $sub_arr['cb'] == 'cb_privacy'
                        && ($sub_arr['value']) != 1) {
                        throw new Exception("Devi accettare l'informativa sulla privacy");
                    }
                    else if (isset($sub_arr['cb'])
                        && $sub_arr['cb'] == 'cb_statuto'
                        && ($sub_arr['value']) != 1) {
                        throw new Exception("Devi accettare lo Statuto della società");
                    }
                    else if (isset($sub_arr['campo'])
                        && $sub_arr['campo'] == 'voucher_code') {
                        $voucher_code = (!is_null($sub_arr['value']) && $sub_arr['value'] != '')
                            ? $sub_arr['value']
                            : null;
                    }

                    // campi cb
                    if (isset($sub_arr['cb'])
                        && $sub_arr['cb'] != ''
                        && isset($sub_arr['value'])) {

                        $cb_value = $sub_arr['value'];

                        // campi select
                        if (isset($sub_arr['is_id'])
                            && $sub_arr['is_id'] != '') {
                            $row_arr = utilityHelper::get_cb_fieldtitle_values($sub_arr['is_id'], $cb_value);
                            if (isset($row_arr['fieldtitle']))
                                $cb_value = $row_arr['fieldtitle'];
                        }

                        $_new_user_cp[$sub_arr['cb']] = addslashes($cb_value);

                    }

                }

                // controllo esistenza voucher
                if (!is_null($voucher_code)) {

                    $checkVoucher = $userModel->checkVoucherValid($voucher_code);
                    if ($checkVoucher == "error") throw new Exception("Si è verificato un errore durante il controllo del voucher". E_USER_ERROR);

                    if (!$checkVoucher) throw new Exception("Il codice voucher immesso non è stato trovato oppure è già stato utilizzato.", E_USER_ERROR);

                }

                // name e username prima lettera nome + cognome
                $_new_user['name'] = strtoupper(substr($nome_utente, 0, 1) . $cognome_utente);
                $_new_user['username'] = trim($username);
                $_new_user['email'] = trim($email_utente);
                $_new_user['password'] = JUserHelper::hashPassword($password_utente);
                $_new_user['block'] = 0;
                $_new_user['registerDate'] = $dt->format('Y-m-d H:i:s');

                // controllo il codice fiscale
                $_cf_check = utilityHelper::conformita_cf($cf_utente);
                if (!isset($_cf_check['valido'])
                    || $_cf_check['valido'] != 1) {

                    $_err = "Problemi con il Codice fiscale";
                    if (isset($_cf_check['msg'])
                        && $_cf_check['msg'] != "")
                        $_err .= " " . $_cf_check['msg'];

                    throw new Exception($_err, E_USER_ERROR);
                }

                // controllo validità email
                if (!filter_var($_new_user['email'], FILTER_VALIDATE_EMAIL)) throw new Exception("EMAIL NON VALIDA: " . $_new_user['email'], E_USER_ERROR);

                // verifico l'esistenza delle colonne minimali per l'inserimento utente
                $_test_users_fields = utilityHelper::check_new_user_array($_new_user);
                if ($_test_users_fields != "") throw new Exception("Mancano dei campi nencessari alla creazione dell'utente: " . $_test_users_fields, E_USER_ERROR);

                // controllo esistenza utente su username
                if (utilityHelper::check_user_by_username($_new_user['username'])) {
                    // aggiungo dei numeri randomici
                    $_new_user['username'] = $_new_user['username'] . rand(1, 999);
                }

                // controllo esistenza utente per codice fiscale
                $comprofilerCheck = utilityHelper::check_comprofiler_by_column_row('cb_codicefiscale', $cf_utente);
                $isDecaduto = false;
                $isSoloEvento = false;
                $newUserId = null;
                $fileExt = null;
                $cvData = null;
                $userModel = new gglmsModelUsers();

                if ($comprofilerCheck) {

                    // la casistica si riferisce ad un utente che non ha completato il procedimento di registrazione
                    // gli permetto di completarlo
                    $annoRef = $dt->format('Y');

                    // prima controllo se ha un pagamento di tipo bonifico in sospeso
                    $checkQuota = $userModel->get_quota_per_id($comprofilerCheck['user_id'], 'user_id', $annoRef);
                    if (isset($checkQuota['tipo_pagamento'])) throw new Exception('Hai già un pagamento registrato per l\'anno ' . $annoRef);

                    // controllo se l'utente è online oppure moroso - in questo caso blocco il procedimento
                    $_params = utilityHelper::get_params_from_plugin();
                    $gruppi_online = utilityHelper::get_ug_from_object($_params, "ug_online");
                    $gruppi_moroso = utilityHelper::get_ug_from_object($_params, "ug_moroso");
                    $gruppi_decaduto = utilityHelper::get_ug_from_object($_params, "ug_decaduto");
                    $gruppi_solo_eventi = utilityHelper::get_ug_from_object($_params, "ug_nonsocio");

                    // online
                    if (utilityHelper::check_user_into_ug($comprofilerCheck['user_id'], explode(",", $gruppi_online)))
                        throw new Exception(JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_ERR_ONLINE'), E_USER_ERROR);

                    // moroso
                    if (utilityHelper::check_user_into_ug($comprofilerCheck['user_id'], explode(",", $gruppi_moroso)))
                        throw new Exception(JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_ERR_MOROSO'), E_USER_ERROR);

                    // se decaduto
                    if (utilityHelper::check_user_into_ug($comprofilerCheck['user_id'], explode(",", $gruppi_decaduto))) 
                        $isDecaduto = true;

                    // se solo evento
                    if (utilityHelper::check_user_into_ug($comprofilerCheck['user_id'], explode(",", $gruppi_solo_eventi))) 
                        $isSoloEvento = true;

                    //else  throw new Exception("L'utente con il codice fiscale ". strtoupper($cf_utente) . " è esistente" , E_USER_ERROR);
                }

                if (isset($decoded['userImage'])) {

                    $dataExploded = explode(",", $decoded['userImage']);
                    $cvData = base64_decode($dataExploded[1]);

                    preg_match("/data:([a-zA-Z0-9]+\/[a-zA-Z0-9-.+]+).*/", $decoded['userImage'], $mimeMatch);
                    $tipoMIME = $mimeMatch[1] ?? null;
                    $fileExt = utilityHelper::mime_to_extension($tipoMIME);
                    if (is_null($fileExt)) throw new Exception("Il file caricato ha un'estensione non supportata. Si prega di verificare il contenuto", E_USER_ERROR);

                }


                $this->_db->transactionStart();
                $startTransaction = true;

                if ($isDecaduto||$isSoloEvento) {

                    // riferimento id per CP
                    $_new_user_cp['id'] = $comprofilerCheck['user_id'];
                    $_new_user_cp['user_id'] = $comprofilerCheck['user_id'];

                    // aggiornamento utente
                    $userUpdateQuery = utilityHelper::get_update_query('users', $_new_user, " where id = " . $comprofilerCheck['user_id']);
                    $_user_update_query_result = utilityHelper::insert_new_with_query($userUpdateQuery);
                    if (!is_array($_user_update_query_result)) throw new Exception("Aggiornamento anagrafica utente fallito: " . $_user_update_query_result, E_USER_ERROR);

                    // aggiornamento comprofiler
                    $comprofilerUpdateQuery = utilityHelper::get_update_query('comprofiler', $_new_user_cp, " where user_id = " . $comprofilerCheck['user_id']);
                    $_cp_update_query_result = utilityHelper::insert_new_with_query($comprofilerUpdateQuery);
                    if (!is_array($_cp_update_query_result)) throw new Exception(print_r($_new_user_cp, true) . " errore durante l'aggiornamento del profilo utente", E_USER_ERROR);

                    $newUserId = $comprofilerCheck['user_id'];

                    if($isSoloEvento){
                        //lo rimuovo dal gruppo solo evento
                        $userGroupId = utilityHelper::check_usergroups_by_name("Solo_eventi");
                        if (is_null($userGroupId)) throw new Exception("Non è stato trovato nessun usergroup valido", E_USER_ERROR);

                        $insert_ug = $userModel->deleteUserFromUserGroup($newUserId, $userGroupId);
                        if (is_null($insert_ug)) throw new Exception("Eliminazione utente in gruppo corso fallito: " . $userGroupId . ", " . $userGroupId, E_USER_ERROR);
                    }
                }
                else {

                    // inserimento utente
                    $userInsertQuery = utilityHelper::get_insert_query("users", $_new_user);
                    $_user_insert_query_result = utilityHelper::insert_new_with_query($userInsertQuery);

                    if (!is_array($_user_insert_query_result)) throw new Exception("Inserimento anagrafica utente fallito: " . $_user_insert_query_result, E_USER_ERROR);

                    // inserimento utente in CP
                    $_new_user_cp['id'] =  $_user_insert_query_result['success'];
                    $_new_user_cp['user_id'] = $_user_insert_query_result['success'];
                    $newUserId = $_user_insert_query_result['success'];


                    $_cp_insert_query = utilityHelper::get_insert_query("comprofiler", $_new_user_cp);
                    //throw new Exception($_cp_insert_query, E_USER_ERROR);

                    $_cp_insert_query_result = utilityHelper::insert_new_with_query($_cp_insert_query);
                    if (!is_array($_cp_insert_query_result)) throw new Exception(print_r($_new_user_cp, true) . " errore durante l'inserimento del profilo utente", E_USER_ERROR);

                }

                // se è un utente nuovo lo inserisco temporaneamente nei morosi
                if (!$isDecaduto||($isSoloEvento&&is_null($voucher_code))) {
                    $userGroupId = utilityHelper::check_usergroups_by_name("Moroso");
                    if (is_null($userGroupId)) throw new Exception("Non è stato trovato nessun usergroup valido", E_USER_ERROR);

                    $insert_ug = $userModel->insert_user_into_usergroup($newUserId, $userGroupId);
                    if (is_null($insert_ug)) throw new Exception("Inserimento utente in gruppo corso fallito: " . $userGroupId . ", " . $userGroupId, E_USER_ERROR);
                }

                $this->_db->transactionCommit();

                if (!is_null($fileExt)) {

                    $dirPath =  JPATH_ROOT . '/tmp/' . $newUserId;

                    if (!file_exists($dirPath)) $createPath = mkdir($dirPath, 0777, true);

                    $filePath = $dirPath .'/' . $_new_user['name'] . '.' . $fileExt;
                    file_put_contents($filePath, $cvData);
                }


                $response['success'] = "tuttook";
                $response['token'] = utilityHelper::build_randon_token($newUserId . '|==|' . $voucher_code);


            }

        }
        catch(Exception $e) {
            if ($startTransaction) $this->_db->transactionRollback();

            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage() . " -> ". print_r($_POST, true), __FUNCTION__ . "_error");
            $response['error'] = $e->getMessage();
        }

        echo json_encode($response);

        $this->_japp->close();
    }

    // stampa ricevuta per pagamento quota asand
    public function printReceiptAsnd() {

        try {
            if (!isset($_REQUEST['recepit_id']))
                throw new Exception("Identificativo quota non valorizzato", E_USER_ERROR);

            $decryptedQuotaRef = utilityHelper::decrypt_random_token($_REQUEST['recepit_id']);
            if (!is_numeric($decryptedQuotaRef))
                throw new Exception("Identificativo quota non numerico", E_USER_ERROR);

            $query = "SELECT usr.id as user_id, usr.email,
                            cb.cb_codicefiscale, cb.cb_nome, cb.cb_cognome, DATE_FORMAT(cb.cb_datadinascita, '%d/%m/%Y') AS cb_datadinascita, cb_luogodinascita, cb_provinciadinascita,
                            quote.anno, quote.totale, quote.tipo_pagamento, DATE_FORMAT(quote.data_pagamento, '%d/%m/%Y') AS data_pagamento, quote.gruppo_corso
                        FROM #__users usr
                        JOIN #__comprofiler cb ON usr.id = cb.user_id
                        JOIN #__gg_quote_iscrizioni quote ON usr.id = quote.user_id
                        WHERE quote.id = " . $this->_db->quote($decryptedQuotaRef);
            $this->_db->setQuery($query);
            $result = $this->_db->loadAssoc();

            if (is_null($result))
                throw new Exception("Nessuna ricevuta trovata", E_USER_ERROR);

            $check = utilityHelper::getJoomlaMainUrl(['asand', 'home']);
            $siteRefUrl = utilityHelper::getHostname(true) . (!is_null($check) ? '/' . $check : "") . "/tmp/";
            $logoAsand = $siteRefUrl . "logo_asand.png";
            $firma_1 = $siteRefUrl . "firma1.jpg";
            $firma_2 = $siteRefUrl . "firma2.jpg";
            $formattedCf = strtoupper($result['cb_codicefiscale']);
            $formattedQuota = number_format($result['totale'], 2, ',', '');
            $speseCommissioni = 0;
            $formattedCommissioni = 0;
            $quotaStandard = 0;
            $quotaStudente = 0;
            $refQuotaOrig = 0;
            $strCommissioni = '';

            // se paypal devo aggiungere la dicitura delle spese di commissione
            if ($result['tipo_pagamento'] == 'paypal' || $result['tipo_pagamento'] == 'voucher') {
                $_params = utilityHelper::get_params_from_plugin('cb.checksociasand');

                $userGroupStandardId = utilityHelper::check_usergroups_by_name("quota_standard");
                $userGroupStudenteId = utilityHelper::check_usergroups_by_name("quota_studente");

                if ($result['gruppo_corso'] == $userGroupStandardId) {
                    $quotaStandard = utilityHelper::get_ug_from_object($_params, "quota_standard");
                    $refQuotaOrig = $quotaStandard;
                    $speseCommissioni = $result['totale']-$quotaStandard;
                }
                else if ($result['gruppo_corso'] == $userGroupStudenteId) {
                    $quotaStudente = utilityHelper::get_ug_from_object($_params, "quota_studente");
                    $refQuotaOrig = $quotaStudente;
                    $speseCommissioni = $result['totale']-$quotaStudente;
                }

                $formattedCommissioni = number_format($speseCommissioni, 2, ',', '');
                if ($result['tipo_pagamento'] == 'paypal')
                    $strCommissioni = ' (di cui &euro; ' . $formattedCommissioni . ' per le spese di commissione)';
                else {
                    $formattedQuota = $result['totale']-$refQuotaOrig;
                    $formattedQuota = number_format($formattedQuota, 2, ',', '');
                    $formattedQuotaOrig = number_format($refQuotaOrig, 2, ',', '');
                    $strCommissioni = ' (applicato sconto di &euro; ' . $formattedQuotaOrig . ' per utilizzo voucher)';
                }
            }

            echo <<<HTML
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    * { font-family: Calibri; font-size: 20px; }
                    table {
                        /* width: auto; */
                    }
                    table, th, td  {
                        border: 0px !important;
                        border-collapse: collapse;
                        /* padding: 2px 3px;
                        text-align: center; */
                    }
                    @media print
                    {
                        .no-print, .no-print *
                        {
                            display: none !important;
                        }
                    }

                </style>
            </head>
            <body>
                <table style="width: 100%">
                    <tbody>
                        <tr>
                            <td>
                                <img src="{$logoAsand}" alt="logo" title="logo" />
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right;">Genova, {$result['data_pagamento']}</td>
                        </tr>
                    </tbody>
                </table>
                <p>Si certifica che {$result['cb_cognome']} {$result['cb_nome']} nato/a a {$result['cb_luogodinascita']} ({$result['cb_provinciadinascita']}) il {$result['cb_datadinascita']},
                    con codice fiscale {$formattedCf}, ha versato ad ASAND la quota associativa relativa all'anno {$result['anno']}, pari a &euro; {$formattedQuota}{$strCommissioni}.<br />
                    Il socio {$result['cb_cognome']} {$result['cb_nome']} &egrave; iscritto all'Associazione tecnico Scientifica Alimentazione, Nutrizione e Dietetica ASAND per l'anno {$result['anno']}.
                </p>
                <p>NI: {$result['anno']}-{$result['user_id']}</p>
                <br />
                <br />
                <table style="width: 100%">
                    <tbody>
                        <tr>
                            <td style="text-align: center;">
                                Il segretario
                            </td>
                            <td style="text-align: center;">
                                Il tesoriere
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">
                                <img style="width: 40%" src="{$firma_1}" alt="firma1" title="firma1" />
                            </td>
                            <td style="text-align: center;">
                                <img style="width: 40%" src="{$firma_2}" alt="firma2" title="firma2" />
                            </td>
                        </tr>
                        <tr class="no-print">
                            <td colspan="2" style="text-align: center">
                                <button onclick="window.print();">STAMPA</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </body>
            </html>
HTML;

        }
        catch(Exception $e) {
            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage() . " -> ". print_r($_POST, true), __FUNCTION__ . "_error");
            echo $e->getMessage();
        }

        $this->_japp->close();
    }

    // abilito i coupon in appoggio alla tabella di controllo
    public function attivazione_coupons_utenti($id_piattaforma) {

        try {

            $query = "SELECT cpcheck.id AS id_rif, cpcheck.codice_coupon, utenti.id AS user_id, coupon.id_gruppi AS gruppo_corso
                        FROM #__gg_check_coupon_xml cpcheck
                        INNER JOIN #__users AS utenti ON utenti.username = cpcheck.codice_fiscale
                        INNER JOIN #__gg_coupon coupon ON cpcheck.codice_coupon = coupon.coupon
                        WHERE cpcheck.coupon IS NULL";

            $this->_db->setQuery($query);
            $results = $this->_db->loadAssocList();

            if (!is_array($results)
                || count($results) == 0)
                throw new Exception("Nessuna referenza disponibile", E_USER_ERROR);

            $user_model = new gglmsModelUsers();
            $this->_db->transactionStart();
            $updates_arr = [];

            foreach ($results as $key_result => $coupon) {

                $update_coupon = "UPDATE #__gg_coupon
                                    SET id_utente = " . $this->_db->quote($coupon['user_id']) . ",
                                        data_utilizzo = " . $this->_db->quote(date('Y-m-d H:i:s')) . "
                                    WHERE coupon = " . $this->_db->quote($coupon['codice_coupon']);

                // inserisco utente in gruppo corso
                $insert_ug = $user_model->insert_user_into_usergroup($coupon['user_id'], $coupon['gruppo_corso']);
                if (is_null($insert_ug))
                    throw new Exception("Inserimento utente in gruppo corso fallito: " . $coupon['user_id'] . ", " . $coupon['gruppo_corso'], E_USER_ERROR);

                $this->_db->setQuery($update_coupon);
                if (!$this->_db->execute())
                    throw new Exception("update coupon query ko -> " . $update_coupon, 1);

                $updates_arr[] = $coupon['id_rif'];

            }

            if (count($updates_arr) == 0)
                throw new Exception("Nessuna referenza aggiornata in coupons", E_USER_ERROR);

            // aggiorno la tabella dei riferimenti
            $update_refs = "UPDATE #__gg_check_coupon_xml
                            SET coupon = 1
                            WHERE id IN (" . implode(",", $updates_arr) . ") ";
            $this->_db->setQuery($update_refs);
            if (!$this->_db->execute())
                throw new Exception("update refs query ko -> " . $update_coupon, 1);

            $this->_db->transactionCommit();

            return "Riferimenti aggiornati: " . count($updates_arr);
        }
        catch(Exception $e) {
            $this->_db->transactionRollback();
            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return 0;
        }

    }

    // importazione corsi da file xml chiamata api
    public function load_corsi_from_xml_ep() {

        $id_piattaforma = $this->_filterparam->id_piattaforma;
        echo $this->load_corsi_from_xml($id_piattaforma);

        $this->_japp->close();

    }

    // importazione corsi da file xml
    public function load_corsi_from_xml($id_piattaforma = 16,
        $ragione_sociale = "Utenti privati skillab",
        $piva = "00000000000",
        $email = "skillabfad@skillab.it",
        $is_debug = false,
        $lista_file_locali = array()) {

        try {

            if (!isset($id_piattaforma)
                || $id_piattaforma == "")
                throw new Exception("Nessuna piattaforma indicata", E_USER_ERROR);

            $local_file = JPATH_ROOT . '/tmp/';
            if (!$is_debug) {
                $get_corsi = UtilityHelper::get_xml_remote($local_file, true, __FUNCTION__);
            }
            else {
                $get_corsi = $lista_file_locali;
            }

            if (!is_array($get_corsi))
                throw new Exception("Nessun file di anagrafica corsi disponibile", E_USER_ERROR);

            // elaborazione dei corsi
            $arr_anagrafica_corsi = UtilityHelper::create_unit_group_corso($get_corsi, $local_file);
            /*
             if (is_null($arr_anagrafica_corsi)
                || count($arr_anagrafica_corsi) == 0)
                throw new Exception("Nessun corso trovato durante l'elaborazione dei file", E_USER_ERROR);
            */

            // elaborazione delle aziende e degli iscritti
            $arr_iscrizioni = UtilityHelper::create_aziende_group_users_iscritti($get_corsi, $local_file, $id_piattaforma, $ragione_sociale, $piva, $email, __FUNCTION__);
            if (is_null($arr_iscrizioni)
                || !is_array($arr_iscrizioni))
                throw new Exception("Si è verificato un problema durante la generazione dei coupon", E_USER_ERROR);

            // invio email riferite ai coupon
            $genera_model = new gglmsModelGeneraCoupon();
            $unita_model = new gglmsModelUnita();
            $iscrizioni = false;
            foreach ($arr_iscrizioni as $piva_key => $single_gen) {

                // informazioni dell'azienda per email
                $company_infos = $arr_iscrizioni[$piva_key]['infos'];

                $coupons = $arr_iscrizioni[$piva_key]['coupons'];
                $registrati = $arr_iscrizioni[$piva_key]['registrati'];

                $_html_users = "";
                $_html_tutor = "";
                $template = JPATH_COMPONENT . '/models/template/xml_coupons_mail.tpl';

                // get recipients --> tutor piattaforma (cc) + tutor aziendale (to)
                $recipients = $genera_model->get_coupon_mail_recipients($company_infos['id_piattaforma'], $company_infos['id_gruppo_societa'], $company_infos['email_coupon'], true);
                if (!$recipients)
                    throw new Exception("Non ci sono tutor piattaforma configurati per questa piattaforma: " . print_r($company_infos, true), E_USER_ERROR);
                // get sender
                $sender = $genera_model->get_mail_sender($company_infos['id_piattaforma'], true);
                if (!$sender)
                    throw new Exception("Non è configurato un indirizzo mail di piattaforma: " . print_r($company_infos, true), E_USER_ERROR);

                $info_piattaforma = $genera_model->get_info_piattaforma($company_infos['id_piattaforma'], true);
                if (is_null($info_piattaforma)
                    || !is_array($info_piattaforma))
                    throw new Exception("Nessuna info piattaforma", E_USER_ERROR);

                // se viene fornita una mail a cui inviare i coupon  non la mando al tutor aziendale ma alla mail fornita
                $to = $company_infos['email_coupon'] != '' ? $company_infos['email_coupon'] : $recipients["to"]->email;
                $_info_corso = $genera_model->get_info_corso($company_infos["gruppo_corsi"], true);

                $mostra_nome_societa = $this->_config->getConfigValue('nome_azienda_intestazione_email_coupon');
                if ((int)$mostra_nome_societa == 0
                    && !is_null($mostra_nome_societa))
                    $company_infos['nome_societa'] = "";

                if ($info_piattaforma['mail_from_default'] == 1) {

                    // ricavo alias e name dalla piattaforma di default
                    $piattaforma_default = $genera_model->get_info_piattaforma_default(true);
                    if (is_null($piattaforma_default)
                        || !is_array($piattaforma_default))
                        throw new Exception("nessuna piattaforma di default trovata", E_USER_ERROR);

                    $info_piattaforma["alias"] = $piattaforma_default['alias'];
                    $info_piattaforma["name"] = $piattaforma_default['name'];
                    $info_piattaforma["dominio"] = $piattaforma_default['dominio'];
                }

                $mailer = JFactory::getMailer();
                $mailer->setSender($sender);
                $mailer->addRecipient($to);
                $mailer->addCc($recipients["cc"]);

                $mailer->setSubject('Coupon corso ' . $_info_corso["titolo"]);

                // costituisco il corpo della email
                // creato tutor aziendale
                // inibito l'invio di email al tutor per eventuale creazione
                // l'azienda riceverà un email unica che contiene tutti dati necessari
                /*
                if (isset($company_infos['company_user'])
                    && $company_infos['company_user'] != "") {
                    // nuovo tutor creato
                    $template_tutor = JPATH_COMPONENT . '/models/template/xml_new_tutor_mail.tpl';
                    $tutor_infos = $company_infos['company_user'];

                    $tutor_email_send = utilityHelper::invia_email_tutor_template($info_piattaforma["name"],
                        $info_piattaforma["alias"],
                        $info_piattaforma["dominio"],
                        $tutor_infos['piva'],
                        $tutor_infos['password'],
                        $template_tutor,
                        $sender,
                        $this->mail_debug,
                        $to,
                        $recipients,
                        $is_debug
                    );

                    if (is_null($tutor_email_send))
                        throw new Exception("Errore durante l'invio della email di registrazione tutor", E_USER_ERROR);

                    $iscrizioni = true;

                } */

                // nuovo tutor creato
                if (isset($company_infos['company_user'])
                    && $company_infos['company_user'] != "") {

                        $tutor_infos = $company_infos['company_user'];

                        $_html_tutor = <<<HTML
                            <p>
                            Le sue credenziali di accesso in qualit&agrave; di tutor aziendale sono:
                            </p>
                            <div style="font-family: monospace;">
                                Username: {$tutor_infos['piva']} / Password: {$tutor_infos['password']}
                            </div>
                            <p>
                                Di seguito le modalit&agrave; di primo accesso al portale
                            </p>
                            <p>
                                Per i tutor: <br />
                                <ul>
                                    <li>
                                        Username: P.IVA aziendale / password: P.IVA aziendale <br />
                                        Nel caso di Libero professionista o altro in cui &egrave; stato indicato lo stesso
                                        dato sia come Codice fiscale che P.IVA, sar&agrave; necessario anteporre <b>XX</b>
                                        (es XX123456789 / XX123456789 oppure XXFGCBCF11A12A969C / XXFGCBCF11A12A969C)
                                    </li>
                                </ul>
                            </p>
                            <p>
                                Per gli utenti: <br />
                                <ul>
                                    <li>Username: Codice fiscale / password: Codice fiscale</li>
                                </ul>
                            </p>
                            <p>
                            Suggeriamo di cambiare la propria password <b>(Menu Accedi/Registrati > Modifica Dati)</b>.
                            <br />
                            Se necessita di recuperare le credenziali contatti l’helpdesk tecnico.
                            </p>
HTML;
                }

                // creati utenti
                if (isset($company_infos['new_users'])
                    && is_array($company_infos['new_users'])
                    && count($company_infos['new_users']) > 0) {
                    $_html_users = <<<HTML
                        <p>
                        In questa mail trover&agrave; gli account creati per i nuovi utenti non ancora registrati in piattaforma.
                        Sia username che password corrispondono al codice fiscale (suggeriamo agli utenti di cambiare la propria password al primo accesso).
                        Gli utenti gi&agrave; registrati dovranno invece continuare a utilizzare le credenziali gi&agrave; in loro possesso
                        </p>
                        <p>
                            <h3>Ecco le credenziali per gli utenti non ancora registrati alla piattaforma https://{$info_piattaforma["dominio"]}</h3>
                        </p>
                        <div style="font-family: monospace;">
HTML;
                    foreach ($company_infos['new_users'] as $key_user => $user) {

                        $_html_users .= <<<HTML
                        Username: {$user['username']} / Password: {$user['clear_password']}
                        <br />
HTML;
                    }
                    $_html_users .= <<<HTML
                        </div>
HTML;

                    $iscrizioni = true;
                }
                else {
                    $_html_users = <<<HTML
                    <p>
                        <b>Tutti gli utenti iscritti sono gi&agrave; in possesso di un account e possono accedere con le proprie credenziali.</b>
                    </p>
HTML;
                }

                $_html_coupons = "";
                $coupons_count = 0;
                $arr_coupons = [];
                foreach ($coupons as $coupon_key => $sub_coupon) {

                    foreach ($sub_coupon as $sub_coupon_key => $coupon) {
                        $_html_coupons .= <<<HTML
                        {$coupon} <br />
HTML;
                        $arr_coupons[] = $coupon;
                    }

                    $coupons_count++;
                }

                // loop registrati per corso
                if (is_array($registrati)
                    && count($registrati) > 0) {

                    $cc = 0;
                    foreach ($registrati as $reg_key => $reg) {

                        if ($reg == "" || strpos($reg, "|") === false)
                            continue;

                        $expl_reg = explode("|", $reg);
                        $insert_check_user = $unita_model->insert_utenti_iscritti_xml($expl_reg[0], $expl_reg[1], $arr_coupons[$cc]);

                        $cc++;

                    }
                }

                if (!$is_debug) {

                    $smarty = new EasySmarty();
                    //$smarty->assign('coupons', $_html_coupons);
                    //$smarty->assign('coupons_count', $coupons_count);
                    $smarty->assign('course_name', $_info_corso["titolo"]);
                    $smarty->assign('company_name', $company_infos['nome_societa']);
                    $smarty->assign('piattaforma_name', $info_piattaforma["alias"]);
                    $smarty->assign('recipient_name', $recipients["to"]->name);
                    $smarty->assign('piattaforma_link', 'https://' . $info_piattaforma["dominio"]);
                    $smarty->assign('company_users', $_html_users);
                    $smarty->assign('creazione_tutor', $_html_tutor);

                    $mailer->setBody($smarty->fetch_template($template, null, true, false, 0));
                    $mailer->isHTML(true);

                    $email_status = 1;

                    if (!$mailer->Send())
                        $email_status = 0;

                    utilityHelper::make_debug_log(__FUNCTION__, "Invio email: " . $email_status . " -> " . print_r($recipients, true), __FUNCTION__ . "_info");

                }
            }

            echo 1;
        }
        catch (Exception $e) {
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            UtilityHelper::send_email("Errore " . __FUNCTION__, $e->getMessage(), array($this->mail_debug));
            echo 0;
        }

        $this->_japp->close();
    }

    // adattamento per versione web
    public function get_completed_report_per_piattaforma_ep() {

        $id_piattaforma = $this->_filterparam->id_piattaforma;
        $tipologia_svolgimento = $this->_filterparam->tipologia_svolgimento;

        echo $this->get_completed_report_per_piattaforma($id_piattaforma, $tipologia_svolgimento);

        $this->_japp->close();

    }

    // report giornaliero che restituisce l'elenco degli utenti che hanno completato il corso per piattaforma
    // adattato per essere chiamato da cli (per i timeout)
    public function get_completed_report_per_piattaforma($id_piattaforma, $tipologia_svolgimento) {

        try {

            // check piattaforma
            if (!isset($id_piattaforma)
                || $id_piattaforma == "")
                throw new Exception("Nessuna piattaforma specificata", E_USER_ERROR);

            $model_user = new gglmsModelUsers();
            $ret_users = $model_user->get_all_user_piattaforma($id_piattaforma, true, __FUNCTION__);
            $arr_xml = array();
            $arr_corsi = array();
            $arr_gruppi = array();
            $arr_codici_corso = array();
            $arr_ref_skill = array();
            $arr_tipologia_corso = array();
            $arr_dt_corsi = array();
            $arr_tutor_az = array();
            $dt = new DateTime();
            $oggi = $dt->format('Y-m-d');
            // ieri
            $_dt_ref = date('Y-m-d', strtotime('-1 day', strtotime($oggi)));
            $_dt_ref_ext =  date('Ymd', strtotime('-1 day', strtotime($oggi)));
            // tipologia svolgimento
            $tipologia_svolgimento = (isset($tipologia_svolgimento) && $tipologia_svolgimento != "") ? $tipologia_svolgimento : 6;

            if (is_null($ret_users))
                throw new Exception("Nessun dato valido - id_piattaforma: " . $id_piattaforma
                    . " - data " . $_dt_ref, E_USER_ERROR);

            $query = $this->_db->getQuery(true)
                ->select('UPPER(cp.cb_codicefiscale) AS cf,
                                COALESCE(cp.cb_nome, "-") AS nome_utente,
                                COALESCE(cp.cb_cognome, "-") AS cognome_utente,
                                u.email,
                                u.id AS user_id,
                                IF(r.stato = 1, "COMPLETATO", "NON COMPLETATO") AS esito,
                                r.stato AS esito_numerico,
                                IF(r.data_inizio_extra = "0000-00-00 00:00:00", "", r.data_inizio_extra) AS data_inizio_corso,
                                IF(r.data_fine_extra = "0000-00-00 00:00:00", "", r.data_fine_extra) AS data_fine_corso,
                                unita.titolo AS titolo_corso,
                                unita.id AS id_corso,
                                unita.codice AS codice_corso,
                                unita.codice_alfanumerico,
                                unita.tipologia_corso,
                                COALESCE(unita.data_inizio, "") AS dt_inizio_corso,
                                COALESCE(unita.data_fine, "") AS dt_fine_corso,
                                ugm.idgruppo AS gruppo_corso,
                                coup.id_societa AS azienda_utente,
                                COALESCE(coup.ref_skill, "") AS ref_skill
                                ')
                ->from('#__comprofiler AS cp')
                ->join('inner', '#__users AS u ON cp.user_id = u.id')
                ->join('inner', '#__gg_report_users AS ru ON u.id = ru.id_user')
                ->join('inner', '#__gg_view_stato_user_corso r ON r.id_anagrafica = ru.id')
                ->join('inner', '#__gg_unit AS unita ON r.id_corso = unita.id')
                ->join('inner', '#__gg_usergroup_map ugm ON ugm.idunita = unita.id')
                ->join('inner', '#__gg_coupon coup ON ugm.idgruppo = coup.id_gruppi AND coup.id_utente = u.id AND coup.gruppo = ' . $this->_db->quote($id_piattaforma))
                ->where('DATE_FORMAT(r.timestamp, "%Y-%m-%d") = ' . $this->_db->quote($_dt_ref))
                ->where('ru.id_user IN (' . implode(",", $ret_users['users']) . ')')
                ->where('r.stato = 1');

            // se non siamo su ausindfad limito la query alla tipologia corsi invocata dalla chiamata
            // posso chiamare tipologie corsi multiple separate da virgola
            if ($tipologia_svolgimento != 6) {
                $_filtro_svolgimento = "";
                // tipologie multiple separate da virgola
                if (strpos($tipologia_svolgimento, ',') !== false)
                    $_filtro_svolgimento = ' IN (' . $tipologia_svolgimento . ') ';
                else
                    $_filtro_svolgimento = ' = ' . $this->_db->quote($tipologia_svolgimento);

                $query = $query->where('unita.tipologia_corso ' . $_filtro_svolgimento);
            }

            $query = $query->order('unita.id');

            $this->_db->setQuery($query);
            $results = $this->_db->loadAssocList();

            if (count($results) == 0)
                throw new Exception("Nessun corso completato - id_piattaforma: " . $id_piattaforma
                    . " - data " . $_dt_ref, E_USER_ERROR);

            foreach ($results as $key_res => $sub_res) {
                $arr_xml[$sub_res['id_corso']][] = $sub_res;
                $arr_corsi[$sub_res['id_corso']] = $sub_res['titolo_corso'];
                $arr_codici_corso[$sub_res['id_corso']] = $sub_res['codice_corso'];

                if ($sub_res['ref_skill'] != "")
                    $arr_ref_skill[$sub_res['id_corso']] = $sub_res['ref_skill'];

                $arr_tipologia_corso[$sub_res['id_corso']] = $sub_res['tipologia_corso'];

                // piva dei tutor aziendali
                if (!in_array($sub_res['azienda_utente'], $arr_tutor_az)) {
                    $tutor_az = $model_user->get_tutor_aziendale_details($sub_res['azienda_utente'], true);
                    if (!is_null($tutor_az)
                        && isset($tutor_az->username))
                        $arr_tutor_az[$sub_res['azienda_utente']] = $tutor_az->username;

                }

                // data inizio / data fine corso nel formato Y-m-d
                if ($sub_res['dt_inizio_corso'] != "" && $sub_res['dt_fine_corso']) {
                    //$arr_dt_corsi[$sub_res['id_corso']] = $sub_res['dt_inizio_corso'] . '||' . $sub_res['dt_fine_corso'];
                    $arr_dt_corsi[$sub_res['id_corso']] = $sub_res['data_inizio_corso'] . '||' . $sub_res['data_fine_corso'];
                }

                $arr_gruppi[$sub_res['id_corso']] = $sub_res['gruppo_corso'];
            }

            $check_xml = UtilityHelper::create_report_xml($arr_xml,
                                                        $arr_corsi,
                                                        $arr_gruppi,
                                                        $ret_users['aziende'],
                                                        $ret_users['dual'],
                                                        $arr_dt_corsi,
                                                        $arr_codici_corso,
                                                        $arr_ref_skill,
                                                        $arr_tipologia_corso,
                                                        $arr_tutor_az,
                                                        $tipologia_svolgimento,
                                                        'GGCorsiCompletati' . $_dt_ref_ext . '.xml',
                                                        __FUNCTION__);

            // si è verificato un errore durante l'elaborazione dell'xml
            if (!$check_xml)
                throw new Exception("Errore di scrittura del file xml - id_piattaforma: " . $id_piattaforma
                    . " - data " . $_dt_ref, E_USER_ERROR);

            // carico file su ftp remoto
            $upload = UtilityHelper::put_xml_remote('GGCorsiCompletati' . $_dt_ref_ext . '.xml', true, __FUNCTION__);
            if (!$upload)
                throw new Exception("Report non caricato su server remoto", E_USER_ERROR);

            return 1;
        }
        catch (Exception $e) {
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            UtilityHelper::send_email("Errore " . __FUNCTION__, $e->getMessage(), array($this->mail_debug));
            return 0;

        }

        //$this->_japp->close();
    }

    // rintraccia tutti gli utenti dei report senza riferimenti in anagrafica, la crea ed aggiorna i riferimenti nelle tabelle dei report
    public function fix_anagrafica_report() {

        try {

            $ids_orfani = array();
            $errors = array();

            $this->_db->setQuery('SET sql_mode=\'\'');
            $this->_db->execute();

            $query = $this->_db->getQuery(true)
                ->select('DISTINCT usr.id AS id_orfano')
                ->from('#__users usr')
                ->join('inner', '#__gg_report cgr ON usr.id = cgr.id_utente')
                ->where('id NOT IN (SELECT id_user FROM #__gg_report_users)')
                ->order('usr.id');

            $this->_db->setQuery($query);
            $results_orfani = $this->_db->loadAssocList();

            if (count($results_orfani) == 0)
                throw new Exception("Nessun orfano di anagrafica trovato!", E_USER_ERROR);

            $model_user = new gglmsModelUsers();
            $model_sync = new gglmsModelSyncdatareport();

            foreach ($results_orfani as $key_orfano => $orfano) {

                $ids_orfani[] = $orfano['id_orfano'];

                $tmpuser = $model_user->get_user($orfano['id_orfano'], 0);

                $tmp = new stdClass();
                $tmp->id_event_booking = 0;
                $tmp->id_user = $orfano['id_orfano'];
                $tmp->nome = $this->_db->quote($tmpuser->nome);
                $tmp->cognome = $this->_db->quote($tmpuser->cognome);
                $tmp->fields = $this->_db->quote(json_encode($tmpuser));
                $last_anag_id = $model_sync->store_report_users($tmp, true);

                $query_update = "UPDATE #__gg_report SET id_anagrafica = " . $this->_db->quote($last_anag_id) . "
                                    WHERE id_utente = " . $this->_db->quote($orfano['id_orfano']) . "
                                    AND id_anagrafica = 0";

                $this->_db->setQuery($query_update);
                $this->_db->execute();

            }

            $query_report = $this->_db->getQuery(true)
                ->select('distinct id_utente, id_anagrafica, id_unita, id_corso')
                ->from('#__gg_report as r')
                ->where('id_utente IN (' . implode(",", $ids_orfani) . ')');

            $this->_db->setQuery($query_report);
            $datauserunit = $this->_db->loadObjectList();

            if (count($datauserunit) == 0)
                throw new Exception("Nessun report da elaborare", E_USER_ERROR);

            // inserisco i risultati in view_corso e view_unit
            $model_syncview = new gglmsModelSyncViewStatoUser();
            $insert_view_unit = $model_syncview->insertViewUnita($datauserunit);
            if (!$insert_view_unit)
                $errors[] = "insertViewUnita FALSE";

            $insert_view_corso = $model_syncview->insertViewCorso($datauserunit);
            if (!$insert_view_corso)
                $errors[] = "insertViewCorso FALSE";

        }
        catch (Exception $e) {
            return "NON COMPLETATO: " . $e->getMessage();
        }

        return count($errors) > 0 ? implode(",", $errors) : 1;

        //$this->_japp->close();
    }

    // rintraccia tutti gli utenti che in gg_report hanno alcune righe in cui l'anagrafica è a zero e le aggiorna con il valore corretto
    public function fix_anagrafica_report_2() {

        try {

            $this->_db->setQuery('SET sql_mode=\'\'');
            $this->_db->execute();

            $query = $this->_db->getQuery(true)
                ->select('DISTINCT usr.id AS id_orfano')
                ->from('#__users usr')
                ->join('inner', '#__gg_report cgr ON usr.id = cgr.id_utente')
                ->where('usr.id IN (SELECT id_user FROM #__gg_report_users)')
                ->where('cgr.id_anagrafica = 0')
                ->order('usr.id');

            $this->_db->setQuery($query);
            $results_orfani = $this->_db->loadAssocList();

            if (count($results_orfani) == 0)
                throw new Exception("Nessun orfano di anagrafica trovato!", E_USER_ERROR);

            $updated = 0;
            foreach ($results_orfani as $key_orfano => $orfano) {

                $query_anag = $this->_db->getQuery(true)
                    ->select('id AS id_anagrafica')
                    ->from('#__gg_report_users')
                    ->where('id_user = ' . $this->_db->quote($orfano['id_orfano']));

                $this->_db->setQuery($query_anag);
                $id_anagrafica = $this->_db->loadResult();

                if (is_null($id_anagrafica)
                    || $id_anagrafica == "")
                    continue;

                $query_update = "UPDATE #__gg_report
                                    SET id_anagrafica = " . $this->_db->quote($id_anagrafica) . "
                                    WHERE id_utente = " . $this->_db->quote($orfano['id_orfano']) . "
                                    AND id_anagrafica = 0";

                $this->_db->setQuery($query_update);
                $this->_db->execute();

                $updated++;

            }

        }
        catch (Exception $e) {
            return "NON COMPLETATO: " . $e->getMessage();
        }

        return "UPDATED: " . $updated;
    }

    //generazione del coupon da una chiamata api per sbloccare un corso come demo
    public function genera_coupon_demo_api() {

        try {

            $id_user = $this->_filterparam->user_id;
            $id_gruppo = $this->_filterparam->gruppo_id;

            if (!isset($id_user)
                || !isset($id_gruppo)
                || !is_numeric($id_user)
                || !is_numeric($id_gruppo))
                throw new Exception("id_gruppo e id_user devono essere di tipo numerico", 1);


           $genera_coupon =  utilityHelper::genera_coupon_demo($id_user, $id_gruppo);

            if(!isset($genera_coupon))
                throw new Exception("Errore nella generazione del coupon demo", 1);

        } catch(Exception $e) {

            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_demo');
            DEBUGG::error($e, __FUNCTION__, 1, true);
        }

    }

    //generazione di report quiz dettagliato con domande e risposte
    public function get_report_dettaglio_quiz()
    {

     try{
         $id_quiz = $this->_filterparam->id_quiz;

         if(!isset($id_quiz)
             || !is_numeric($id_quiz))
             throw new Exception("id_quiz devono essere di tipo numerico", 1);

         $contenuto_model = new gglmsModelContenuto();

         $user_questions_survey = $contenuto_model->get_quiz_response_survey($id_quiz);
         $user_questions_choice = $contenuto_model->get_quiz_response_choice($id_quiz);

         if(!isset($user_questions_survey) || !is_array($user_questions_survey))
             throw new Exception("Erore nella generazione di quiz survey", 1);

         if(!isset($user_questions_choice) || !is_array($user_questions_choice))
             throw new Exception("Erore nella generazione di quiz choice", 1);

         $user_questions = array();
         $key_prec = '';

        foreach ($user_questions_choice as $key_choice=>$choice){

            if ($choice['Id_user_quiz'] != $key_prec){
                foreach ($user_questions_survey as $key_survey=>$survey){

                if($survey['Id_user_quiz'] == $choice['Id_user_quiz']){

                    if(array_key_exists('Domanda',$survey))
                        $survey['Domanda'] = strip_tags($survey['Domanda']);
                    $key_prec = $survey['Id_user_quiz'];
                    array_push($user_questions,$survey);
                }

               }
            }

            if(array_key_exists('Domanda',$survey))
                $choice['Domanda'] = strip_tags($choice['Domanda']);

            array_push($user_questions,$choice);

        }

         $_csv_cols = utilityHelper::get_cols_from_array((array) $user_questions[0]);

        $_export_csv = utilityHelper::esporta_csv_spout($user_questions , $_csv_cols, time() . '.csv');

        // chiusura della finestra dopo generazione del report
$_html = <<<HTML
            <script type="text/javascript">
                window.close();
            </script>
HTML;

//echo $_html;
     } catch (Exception $e) {
    //$_ret['error'] = $e->getMessage();
          echo $e->getMessage();
      }

        $this->_japp->close();
    }

    // importazione anagrafica centri Sinpe da API
    // oppure in locale se si tratta di LP (esempio) from_local è il nome file che attesta se fare riferimento a questo file che si trova nella cartella JPATH_ROOT/tmp/
    public function importa_anagrafica_centri( $is_debug = false,
                                               $from_local = '') {

        try {


            $local_file = JPATH_ROOT . '/tmp/';
            $filename = "anagrafica_centri.csv";

            $get_centri = utilityHelper::get_csv_remote($local_file, $filename, false, $is_debug, $from_local);

            if (!is_array($get_centri)
                || is_null($get_centri))
                throw new Exception("Nessun file di anagrafica corsi disponibile", E_USER_ERROR);

            $this->_db->transactionStart();

            // grande loop di inserimento degli centri

            foreach ($get_centri as $row_key => $row_arr) {
                $centro = trim($row_arr[0]);

                $indirizzo = trim($row_arr[1]);

                $telefono_responsabile = trim($row_arr[2]);

                $telefono_servizio = trim($row_arr[3]);

                $fax = trim($row_arr[4]);

                $email = trim($row_arr[5]);

                $responsabile = trim($row_arr[6]);

                $ruolo = trim($row_arr[7]);

                $latitudine = trim($row_arr[8]);

                $longitudine = trim($row_arr[9]);

                $citta = trim($row_arr[10]);


                $query = "INSERT INTO #__gg_anagrafica_centri
                             (
                              centro,
                              indirizzo,
                              telefono_responsabile,
                              telefono_servizio,
                              fax,
                              email,
                              responsabile,
                              ruolo,
                              latitudine,
                              longitudine,
                              citta)
                      VALUES (
                          ". $this->_db->quote($centro) .",
                          ". $this->_db->quote($indirizzo) .",
                          ". $this->_db->quote($telefono_responsabile) .",
                          ". $this->_db->quote($telefono_servizio) .",
                          ". $this->_db->quote($fax) .",
                          ". $this->_db->quote($email) .",
                          ". $this->_db->quote($responsabile) .",
                          ". $this->_db->quote($ruolo).",
                          ". $this->_db->quote($latitudine).",
                          ". $this->_db->quote($longitudine).",
                          ". $this->_db->quote($citta)."
                           )";

                $this->_db->setQuery($query);
                $result = $this->_db->execute();

                if (!$result)
                    throw new Exception("Inserimento anagrafica centri non andato a buon fine: " . $result, 1);


            }


            $this->_db->transactionCommit();

            return 1;


        }
        catch (Exception $e) {

          $this->_db->transactionRollback();
            utilityHelper::make_debug_log(__FUNCTION__, $e , __FUNCTION__);
            return 0;
        }

        $this->_japp->close();
    }


    public function fix_gruppo_socio() {

        try {



            //not in (select user_id from #__user_usergroup_map WHERE group_id = 28)

            $this->_db->transactionStart();

            $query = $this->_db->getQuery(true)
                ->select('um.user_id, q.anno, um.group_id ')
                ->from('#__user_usergroup_map as um ')
                ->join('inner', '#__gg_quote_iscrizioni as q on um.user_id = q.user_id')
                ->where('um.user_id not in (select user_id from #__user_usergroup_map WHERE group_id = 28)')
                ->where('q.anno <= 2019')
                ->where('um.group_id in (20,23,21)');

            $this->_db->setQuery($query);
            $results_soci = $this->_db->loadAssocList();


            if (count($results_soci) == 0)
                throw new Exception("Nessun socio trovato!", E_USER_ERROR,1);

            $updated = 0;
            $new_ug = 21;
            foreach ($results_soci as $key_socio => $socio) {


                   //cancello user da moroso o online
                   $query_del = "DELETE
                                FROM #__user_usergroup_map
                                WHERE user_id = " . $this->_db->quote($socio['user_id']) . "
                                AND group_id = " . $this->_db->quote($socio['group_id']);

                   $this->_db->setQuery($query_del);
                   if (!$this->_db->execute())
                       throw new Exception("delete query ko -> " . $query_del, E_USER_ERROR);


                   // aggiungo user in moroso
                   $query_ins = "INSERT IGNORE INTO #__user_usergroup_map (user_id, group_id)
                                VALUES (" . $this->_db->quote($socio['user_id']) . ", " . $this->_db->quote($new_ug) . ")";

                   $this->_db->setQuery($query_ins);
                   if (!$this->_db->execute())
                       throw new Exception("insert query ko -> " . $query_ins, E_USER_ERROR);


                $updated++;


            }

            $this->_db->transactionCommit();

        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            utilityHelper::make_debug_log(__FUNCTION__, $e , __FUNCTION__);

        }

        echo "UPDATED: " . $updated;

    }

    public function get_fatture()
    {
        try {


            $id_user =  $this->_filterparam->id_user;


            $offset = (isset($this->_filterparam->offset) && $this->_filterparam->offset != "") ? $this->_filterparam->offset : 0;
            $limit = (isset($this->_filterparam->limit) && $this->_filterparam->limit != "") ? $this->_filterparam->limit : 5;
            $_sort = (isset($this->_filterparam->sort) && $this->_filterparam->sort != "") ? $this->_filterparam->sort : null;
            $_order = (isset($this->_filterparam->order) && $this->_filterparam->order != "") ? $this->_filterparam->order : null;


            if(!isset($id_user)
                || !is_numeric($id_user))
                throw new Exception("id_user non valorizzato " . $this->_filterparam->id_user, 1);



            $db = JFactory::getDbo();
            $query_count = $db->getQuery(true)
                ->select('count(*)')
                ->from('#__gg_quote_iscrizioni')
               ->where('user_id = ' . $id_user);

            $db->setQuery($query_count);
            $count = $db->loadObject();

            $query = $db->getQuery(true)
                ->select('id, CONCAT( anno, "-", user_id ) AS numero_fattura, tipo_pagamento,DATE_FORMAT(data_pagamento, "%d-%m-%Y") as data_pagamento, totale, stato')
                ->from('#__gg_quote_iscrizioni')
                 ->where('user_id = ' . $id_user);

            // ordinamento per colonna - di default per id utente
            if (!is_null($_sort)
                && !is_null($_order)) {
                $query = $query->order($_sort . ' ' . $_order);
            }
            else
                $query = $query->order('id', 'asc');



            $db->setQuery($query,$offset,$limit);

            $fatture = $db->loadObjectList();


            if (isset($fatture)) {
                foreach ($fatture as $_key_row => $_row) {

                    //rows da colorare
                    if($_row->stato == 1){
                        $color_cell = 'color:green';
                        $_row->stato = <<<HTML
                            <span style="{$color_cell}" >Pagato</span>
HTML;
                        $encodedReceiptId = utilityHelper::build_randon_token($_row->id);

                        $url = "index.php?option=com_gglms&task=api.printReceiptAsnd&recepit_id=" . $encodedReceiptId ;
                        $_row->fattura = <<<HTML
                                   <a class="far fa-file-pdf fa-2x"
                                      target="popup"
                                      onclick="window.open('{$url}','popup','width=600,height=600'); return false;" style="cursor: pointer;color: red">

                                    </a>
HTML;


                    }else if($_row.'stato' == 0) {
                        $color_cell = 'color:red';
                        $_row->stato = <<<HTML
                            <span style="{$color_cell}" >Non pagato</span>
HTML;
                        $_row->fattura = '';
                    }



                }
            }

            $result['rowCount'] = $count;
            $result['rows'] = $fatture;


        } catch (Exception $e) {
            echo $e->getMessage();
        }


        echo json_encode($result);
        $this->_japp->close();
    }


    public function genera_votazione_code() {

        $db = JFactory::getDBO();
        $exclude_query = $db->getQuery(true)
                    ->select('user_id')
                    ->from('#__user_usergroup_map')
                    ->where('group_id = ' . $db->quote('68'))
                    ->group('user_id');
        $db->setQuery($exclude_query);
        $result_users = $db->loadAssocList();

        $_ids_codici = array();
        $dettagli_utente = array();


        if (count($result_users) == 0)
            utilityHelper::make_debug_log(__FUNCTION__, "Nessun risultato users da elaborare" , __FUNCTION__);

        if (!is_null($result_users)
            && count($result_users) > 0) {

            foreach ($result_users as $key_user => $user) {

                $bytes = random_bytes(5);
                $codice = bin2hex($bytes);

                // Verifica e gestione dell'unicità del codice

                $encode_user = UtilityHelper::encrypt_decrypt('encrypt', $user['user_id'], 'Sinpe', '2023');

                $_ids_codici[] = sprintf("('%s', '%s', '%s')",
                    $encode_user,
                    $codice,
                    date('Y-m-d H:i:s')
                );

                $dettagli_utente[$key_user]['user_id'] = $user['user_id'];
                $dettagli_utente[$key_user]['codice'] = $codice ;
            }

        }

        // li inserisco nel DB
        $query_insert = 'INSERT INTO #__gg_cod_votazioni_users (id_user,
                                                codice,
                                                timestamp
                                                ) VALUES ' . join(',', $_ids_codici);
        $this->_db->setQuery($query_insert);

        if (false === $this->_db->execute()) {
            throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
        }

        foreach ($dettagli_utente as $utente) {

            $_model_user = new gglmsModelUsers();
            $user_model = $_model_user->get_user_by_id($utente['user_id']);

            $_send_email = UtilityHelper::send_codice_vitazione_email_sinpe('', $user_model->name, $user_model->email, $utente['codice'], 'salma@ggallery.it');

        }
        $this->_ret['success'] = "tuttook";
        echo json_encode($this->_ret);
        die();

    }


    public function check_codice_votazione()
    {

        $japp = JFactory::getApplication();

        $codice = JRequest::getVar('codice');
        $user_id = JRequest::getVar('user_id');

        $dettagli_codice = $this->check_codice_sinpe($codice);
        $results = array();


        if(empty($dettagli_codice) || count($dettagli_codice) < 0) {

            $results['report'] = "<p class='alert-danger alert'>Il codice inserito sbagliato</p>";
            $results['valido'] = 0;

        }else{

            $decode_user = UtilityHelper::encrypt_decrypt('decrypt', $dettagli_codice['id_user'], 'Sinpe', '2023');

            $check_votazione = $this->check_votazioni_candidati($codice);

            if ($decode_user != $user_id){

                $results['report'] = "<p class='alert-danger alert'>Il codice inserito sbagliato </p>";
                $results['valido'] = 0;

            } elseif (!empty($check_votazione) || count($check_votazione) > 0) {

                $decode_user_vot = UtilityHelper::encrypt_decrypt('decrypt', $check_votazione['id_user'], 'Sinpe', '2023');

                if ($user_id === $decode_user_vot) {

                    $results['report'] = "<p class='alert-danger alert'>Ha già votato con il codice inserito</p>";
                    $results['valido'] = 0;
                }
            }else {

                $results['report'] = "<p class='alert-success alert'>Codice corretto</p>";
                $results['valido'] = 1;
            }


        }

        echo json_encode($results);
        $japp->close();
    }

    public function check_codice_sinpe($codice)
    {
        try {

                $query = $this->_db->getQuery(true)
                    ->select('*')
                    ->from('#__gg_cod_votazioni_users as c')
                    ->where('c.codice="' . ($codice) . '"');


            $this->_db->setQuery($query);
            if (false === ($results = $this->_db->loadAssoc()))
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);

            $_codice = empty($results) ? array() : $results;

        } catch (Exception $e) {
            utilityHelper::make_debug_log(__FUNCTION__, $e , __FUNCTION__);
        }
        return $_codice;
    }


    public function check_votazioni_candidati($codice)
    {
        try {

            $query = $this->_db->getQuery(true)
                ->select('c.id_user, c.codice')
                ->from('#__gg_votazioni_candidati as c')
                ->where('c.codice="' . ($codice) . '"');


            $this->_db->setQuery($query);
            if (false === ($results = $this->_db->loadAssoc()))
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);


            $_codice = empty($results) ? array() : $results;

        } catch (Exception $e) {
            utilityHelper::make_debug_log(__FUNCTION__, $e , __FUNCTION__);
        }
        return $_codice;
    }

    public function store_votazione_candidati()
    {

        try {


                $japp = JFactory::getApplication();

                $codice = JRequest::getVar('codice');
                $id_candidato = JRequest::getVar('id_candidato');
                $user_id = JRequest::getVar('user_id');

                $db = JFactory::getDBO();

                $encode_user_id = UtilityHelper::encrypt_decrypt('encrypt', $user_id, 'Sinpe', '2023');
                $encode_candidato_id = UtilityHelper::encrypt_decrypt('encrypt', $id_candidato, 'Sinpe', '2023');

                $query = "insert into #__gg_votazioni_candidati (id_user, id_candidato, codice)  VALUES ('$encode_user_id','$encode_candidato_id','$codice')";

                $db->setQuery($query);

                if (false === ($db->execute()))
                    throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);

                $result['valido'] = 1;


       } catch (Exception $e) {
                utilityHelper::make_debug_log(__FUNCTION__, $e , __FUNCTION__);
        }

        echo json_encode($result);
        $japp->close();
    }

    public function fix_create_company_forum()
    {
        try {

            $data = JRequest::get($_POST);
            $model_genera = new gglmsModelgeneracoupon();
            $gruppo_azienda = $this->_filterparam->gruppo_id;
            $model_users = new gglmsModelUsers();
            $nome_azienda = $model_users->get_nome_societa_by_id($gruppo_azienda);

            $model_genera->_create_company_forum($gruppo_azienda,$nome_azienda->nome_societa);

            $this->_ret['success'] = "tuttook";
            echo json_encode($this->_ret);

        } catch (Exception $e) {

            utilityHelper::make_debug_log(__FUNCTION__, $e , __FUNCTION__);
        }
        $this->_japp->close();
    }

    private function registerFromApi($name, $lastname, $email, $phone,$company,$region): void
    {
        $db = JFactory::getDbo();
        $config = JFactory::getConfig();
        try {

        $db->transactionStart();

        $pass = uniqid();
        $query = "INSERT INTO #__users (name, username, email, password, requireReset)
                        VALUES (
                              '" . $name . "',
                              '" . $email . "',
                              '" . $email . "',
                              '" .JUserHelper::hashPassword($pass)."',
                              1
                        )";

        $db->setQuery($query);
        $db->execute();

        $lastUser = $db->insertid();

            $query = "INSERT INTO #__comprofiler (id , user_id, cb_cognome, cb_nome, cb_telefono,cb_azienda, cb_regioneazienda)
                        VALUES (
                                $lastUser,
                                $lastUser,
                              '" . $lastname . "',
                              '" . $name . "',
                              '" . $phone . "',
                              '" . $company . "',
                              '" . $region . "'
                        )";

            $db->setQuery($query);
            $db->execute();

            $ug_id = utilityHelper::check_usergroups_by_name('Registered');

            $query = "INSERT INTO #__user_usergroup_map (user_id, group_id)
                        VALUES (
                                $lastUser,
                                $ug_id
                        )";

            $db->setQuery($query);
            $db->execute();

            $db->transactionCommit();

            $body   = 'Grazie '. $name .' '. $lastname .' <br>
                 Abbiamo creato il tuo account sulla piattaforma e-learning, con le seguenti credenziali <br>'
                .'USERNAME: <b>'.$email .'</b> <br> '
                .'PASSWORD: <b>'.$pass .'</b> <br><br> '

                . '<div>Per completare la registrazione, entra nella piattaforma a questo link: <a href="https://'.utilityHelper::getHostname().'/accedi'  .'" >'.utilityHelper::getHostname().'/accedi'  .' </a></div> <br>
                 Utilizza le credenziali appena indicate e compila i restanti campi della scheda anagrafica, obbligatori per normativa ECM.<br>
                 Al primo accesso ti sarà anche richiesto di modificare la password con una a tua scelta.<br>
                 Ti chiediamo di prestare la massima attenzione durante la compilazione della scheda anagrafica, in quanto in presenza di dati errati il provider non può garantire la corretta rendicontazione dei crediti ECM.<br>
                 Una volta completata la registrazione, avrai accesso all\'area formativa per consultare i contenuti, superare il test e scaricare l\'attestato ECM.<br>
                 Grazie e buona formazione<br>
                 ';

            $sendMail = new gglmsControllerUsers();
            $res = $sendMail->sendMail($email,'Dettagli Nuovo Utente', $body);

            if($res != 'Mail inviata'){
                throw new Exception('Send Email error', E_USER_ERROR);
            }
            return;

        } catch (Exception $e) {
            $db->transactionRollback();
            utilityHelper::make_debug_log(__FUNCTION__, $e , __FUNCTION__);
            $this->sendResponse(500, 'Internal server error');
        }
    }

    private function checkApiKey(): bool
    {
        $KEY = 'zI*hog7fRIgu7r0thuN7';

        $headers = apache_request_headers();

        if(isset($headers['X-Api-Key'])){
            if($headers['X-Api-Key'] == $KEY) return true;
        }
        return false;
    }

    private function sendResponse($status, $description){
        JFactory::getApplication()->setHeader('Content-type', 'application/json', true);
        Jfactory::getApplication()->setHeader('status', $status, true);
        http_response_code($status);
        echo json_encode($description);
        Jfactory::getApplication()->close();
    }


    public function registerUser(){
        $app = JFactory::getApplication();
        $input = $app->input;

        if($_SERVER['REQUEST_METHOD'] != 'POST'){
            $this->sendResponse(405, 'Method not allowed');
        }

        if(!$this->checkApiKey()){
            $this->sendResponse(401, 'Not Authorized');
        }

        $data = json_decode(file_get_contents("php://input"),true);
        if (empty($data)) {
            $this->sendResponse(400, 'Bad Request');
        }

        //validation
        if (!isset($data['name']) || !isset($data['lastname']) || !isset($data['email']) || !isset($data['phone']) || !isset($data['company']) || !isset($data['region'])) {
            $this->sendResponse(406, 'Missing required fields');
        }

        $emailCheck = utilityHelper::check_user_by_column_row('email', $data['email']);
        if (!is_null($emailCheck)) $this->sendResponse(406, 'User already exists');

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $this->sendResponse(406, 'Invalid email format');


        $this->registerFromApi(htmlspecialchars($data['name']), htmlspecialchars($data['lastname']), $data['email'], htmlspecialchars($data['phone']), htmlspecialchars($data['company']), htmlspecialchars($data['region']));

        $response = [
            'status' => 'success',
            'message' => 'User data submitted successfully',
            'data' => $data
        ];

        $this->sendResponse('success', $response);

    }


//	INUTILIZZATO
//	public function getSummarizeCourse(){
//		$query = $this->_db->getQuery(true);
//		$query->select('stato, count(stato) as total ');
//		$query->from('#__gg_report AS r');
//		$query->where("id_contenuto =" . $this->_filterparam->corso_id);
//		$query->group('stato');
//		$this->_db->setQuery($query);
//		$summarize = $this->_db->loadAssocList('stato');
//		return $summarize;
//	}

}
