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
require_once JPATH_COMPONENT . '/models/unita.php';
require_once JPATH_COMPONENT . '/models/config.php';

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
    private $_filterparam;


//https://api.joomla.org/cms-3/classes/Joomla.Utilities.ArrayHelper.html

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();
        $this->_db = JFactory::getDbo();

        $this->_filterparam = new stdClass();

        $this->_filterparam->corso_id = JRequest::getVar('corso_id');
        $this->_filterparam->current = JRequest::getVar('current');
        $this->_filterparam->rowCount = JRequest::getVar('rowCount');
        $this->_filterparam->startdate = JRequest::getVar('startdate');
        $this->_filterparam->finishdate = JRequest::getVar('finishdate');
        $this->_filterparam->filterstato = JRequest::getVar('filterstato');
        $this->_filterparam->usergroups = JRequest::getVar('usergroups');
        $this->_filterparam->sort = JRequest::getVar('sort');
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

    }

    public function get_report()
    {

        $data = $this->new_get_data();

        echo json_encode($data);
        $this->_japp->close();
    }

    private function new_get_data($offsetforcsv = null)
    {

        //$this->_filterparam->task = JRequest::getVar('task');
        //FILTERSTATO: 2=TUTTI 1=COMPLETATI 0=SOLO NON COMPLETATI 3=IN SCADENZA
        $id_corso = explode('|', $this->_filterparam->corso_id)[0];
        $id_contenuto = explode('|', $this->_filterparam->corso_id)[1];
        $alert_days_before = $this->_params->get('alert_days_before');
        $tipo_report = $this->_filterparam->tipo_report;
        $limit = $this->_filterparam->limit;
        $offset = $this->_filterparam->offset;
        $filters = array(
                        'startdate' => $this->_filterparam->startdate,
                        'finishdate' => $this->_filterparam->finishdate,
                        'filterstato' => $this->_filterparam->filterstato,
                        'searchPhrase' => $this->_filterparam->searchPhrase,
                        'usergroups' => $this->_filterparam->usergroups);

        try {


            $columns = array();
            switch ($tipo_report) {

                case 0: //PER CORSO


                    $att_id_string = $this->getAttestati($id_corso);

                    $query = $this->_db->getQuery(true);
                    $query->select('vista.id_anagrafica as id_anagrafica,"' . $att_id_string . '" as attestati_hidden, vista.stato as stato, vista.data_inizio as data_inizio, vista.data_fine as data_fine , IF(date(now())>DATE_ADD((select data_fine from #__gg_unit where id=' . $id_corso . '), INTERVAL -' . $alert_days_before . ' DAY), IF(stato=0,1,0),0) as scadenza');
                    $query->from('#__gg_view_stato_user_corso  as vista');
                    $query->where('id_corso=' . $id_corso);
                    switch ($filters['filterstato']) {

                        case 0: //qualsiasi stato
                            $arrayresult = $this->buildGeneralDataCubeUtentiInCorso($id_corso, $limit, $offset, $filters['searchPhrase'], $filters['usergroups']);
                            $users = $arrayresult[0];
                            $count = $arrayresult[1];
                            $queryGeneralCube = $arrayresult[2];
                            $queryGeneralCubeCount = $arrayresult[3];
                            $result['secondaryCubeQuery'] = (string)$query;
                            echo $query; die();
                            $datas = $this->buildPrimaryDataCube($query);
                            $users = $this->addColumn($users, $datas, "id_anagrafica", null, "stato", 'outer');
                            $users = $this->addColumn($users, $datas, "id_anagrafica", null, "data_inizio", 'outer');
                            $users = $this->addColumn($users, $datas, "id_anagrafica", null, "data_fine", 'outer');
                            $users = $this->addColumn($users, $datas, "id_anagrafica", null, "scadenza", 'outer');
                            $users = $this->addColumn($users, $datas, "id_anagrafica", null, "attestati_hidden", 'outer');
                            $columns = array('id_anagrafica', 'cognome', 'nome', 'stato', 'data_inizio', 'data_fine', 'scadenza', 'fields', 'attestati_hidden');

                            $rows = $users;
//                            var_dump($rows);
////                            die();

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
                            $result['secondaryCubeQuery'] = (string)$query;


//                            $count = $this->countPrimaryDataCube($query);!!! BUG il count che viene su da qui non è filtrato per azienda!!
//                            $datas = $this->buildPrimaryDataCube($query,$offset, $limit); !!! BUG offset e limit erano invertiti!!
                            $datas = $this->buildPrimaryDataCube($query, $limit, $offset);


                            $arrayresult = $this->buildGeneralDataCubeUtentiInCorso($id_corso, null, null, $filters['searchPhrase'], $filters['usergroups'], implode(",", (array_column($datas, "id_anagrafica"))));
                            $users = $arrayresult[0];

                            // in sostituzione del count commentato sopra --> il count era già presente come parametro di ritorno ma non veniva usato
                            $count = $arrayresult[1];

                            $queryGeneralCube = $arrayresult[2];
                            $queryGeneralCubeCount = $arrayresult[3];
                            $datas = $this->addColumn($datas, $users, "id_anagrafica", null, "nome", 'inner');
                            $datas = $this->addColumn($datas, $users, "id_anagrafica", null, "cognome", 'inner');
                            $datas = $this->addColumn($datas, $users, "id_anagrafica", null, "fields", 'inner');
                            $rows = $datas;

                            $columns = array('id_anagrafica', 'cognome', 'nome', 'stato', 'data_inizio', 'data_fine', 'scadenza', 'fields', 'attestati_hidden');

                            break;


                    }

                    break;


                case 1: //PER UNITA'
                    $arrayresult = $this->buildGeneralDataCubeUtentiInCorso($id_corso, $limit, $offset, $filters['searchPhrase'], $filters['usergroups']);
                    $users = $arrayresult[0];
                    $count = $arrayresult[1];
                    $queryGeneralCube = $arrayresult[2];
                    $queryGeneralCubeCount = $arrayresult[3];
                    $query = $this->_db->getQuery(true);
                    $query->select('vista.id_anagrafica as id_anagrafica, u.id as id_unita,u.titolo as titolo_unita, vista.stato as stato, vista.data_inizio as data_inizio, vista.data_fine as data_fine');
                    $query->from('#__gg_view_stato_user_unita  as vista');
                    $query->join('inner', '#__gg_unit as u on vista.id_unita=u.id');
                    $query->where('id_corso=' . $id_corso);
                    $result['secondaryCubeQuery'] = (string)$query;
                    $datas = $this->buildPrimaryDataCube($query);
                    $users = $this->addColumn($users, $datas, "id_anagrafica", "titolo_unita", "stato", 'outer');
                    $columns = $this->buildColumnsforUnitaView($id_corso);
                    $rows = $users;
                    break;
                case 2://PER CONTENUTO
                    $arrayresult = $this->buildGeneralDataCubeUtentiInCorso($id_corso, $limit, $offset, $filters['searchPhrase'], $filters['usergroups']);
                    $users = $arrayresult[0];
                    $count = $arrayresult[1];
                    $queryGeneralCube = $arrayresult[2];
                    $queryGeneralCubeCount = $arrayresult[3];

                    $query = $this->_db->getQuery(true);
                    $query->select('vista.id_anagrafica as id_anagrafica, c.id as id_contenuto,c.titolo as titolo_contenuto, vista.stato as stato, vista.permanenza_tot as permanenza, vista.data as last_visit');
                    $query->from('#__gg_report  as vista ');
                    $query->join('inner', '#__gg_contenuti as c on vista.id_contenuto=c.id');
                    $query->where('id_corso=' . $id_corso);
                    $result['secondaryCubeQuery'] = (string)$query;
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

        $result['queryGeneralCube'] = (string)$queryGeneralCube;

//        $result['datas'] = $datas;
//        $result['buildGeneralDataCubeUtentiInCorso'] = $arrayresult[0];
        //$result['current']=$this->_filterparam->current;
        $result['columns'] = $columns;
        $result['rowCount'] = $count;
        $result['rows'] = $rows;
        //$result['total']=$total;
        $result['totalquery'] = (string)$queryGeneralCubeCount;
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
			                SQ.c_passed AS esito, SQ.c_quiz_id AS quiz_ref, 
			                SQ.c_id AS quiz_id, SQ.c_student_id AS student_id, SQ.c_passing_score AS punteggio');

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



    private function buildGeneralDataCubeUtentiInCorso($id_corso, $offset, $limit, $searchPrase, $gruppo_azienda, $anagrafica_filter = null)
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

//            var_dump($accesso);

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
            $query->order('anagrafica.cognome', 'asc');

//            var_dump((string)$query);

            $query->setlimit($offset, $limit);
            $this->_db->setQuery($query);
            $rows = $this->_db->loadAssocList();
            foreach ($rows as &$row) {//FILTRO PER CAMPI DI FIELDS
                $row['fields'] = json_decode($row['fields']);
                unset($row['fields']->password);
                $row['fields'] = json_encode($row['fields']);
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
        $columns = ['id_anagrafica', 'cognome', 'nome', 'fields'];
        foreach ($unitas as $unita) {
            array_push($columns, $unita['titolo']);
        }
        return $columns;
    }

    private function buildColumnsforContenutiView($id_corso)
    {

        $reportObj = new gglmsModelReport();
        $contenuti = $reportObj->getContenutiArrayList($id_corso);

        $columns = ['id_anagrafica', 'cognome', 'nome', 'fields'];
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

            $userFields = json_decode($row['fields']);
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

        $corso_obj = $this->_db->loadObject('gglmsModelUnita');
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
