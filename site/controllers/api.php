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
require_once JPATH_COMPONENT . '/models/generacoupon.php';
require_once JPATH_COMPONENT . '/models/syncdatareport.php';
require_once JPATH_COMPONENT . '/models/syncviewstatouser.php';
require_once JPATH_COMPONENT . '/controllers/zoom.php';

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
        $this->_filterparam->user_id = JRequest::getVar('user_id');
        $this->_filterparam->zoom_user = JRequest::getVar('zoom_user');
        $this->_filterparam->zoom_tipo = JRequest::getVar('zoom_tipo');
        $this->_filterparam->zoom_mese = JRequest::getVar('zoom_mese');
        $this->_filterparam->zoom_event_id = JRequest::getVar('zoom_event_id');
        $this->_filterparam->zoom_event_label = JRequest::getVar('zoom_label');
        $this->_filterparam->id_piattaforma = JRequest::getVar('id_piattaforma');
        $this->_filterparam->tipologia_svolgimento = JRequest::getVar('tipologia_svolgimento');
        // email di debug
        $this->mail_debug = $this->_config->getConfigValue('mail_debug');
        $this->mail_debug = ($this->mail_debug == "" || is_null($this->mail_debug)) ? "luca.gallo@gallerygroup.it" : $this->mail_debug;

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
    function completa_corsi_per_utente_iscritto() {

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

        try {

            $params = JRequest::get($_GET);
            $quiz_id = $params["quiz_id"];
            $user_id = $params["user_id"];

            if (!isset($quiz_id)
                || $quiz_id == "")
                throw new Exception("Missing quiz id", 1);

            if (!isset($user_id)
                || $user_id == "")
                throw new Exception("Missing user id", 1);

            $model_content = new gglmsModelContenuto();
            $dettagli_quiz = $model_content->get_dettagli_quiz_per_utente($quiz_id, $user_id);

            if (!$dettagli_quiz
                || !is_array($dettagli_quiz)
                || count($dettagli_quiz) == 0)
                throw new Exception("Nessun dettaglio disponibile per il quiz e per lo user selezionati", E_USER_ERROR);


            $_csv_cols = utilityHelper::get_cols_from_array($dettagli_quiz[0]);
            $dettagli_quiz = utilityHelper::clean_quiz_array($dettagli_quiz);
            $_export_csv = utilityHelper::esporta_csv_spout($dettagli_quiz, $_csv_cols, time() . '.csv');

            // chiusura della finestra dopo generazione del report
            $_html = <<<HTML
            <script type="text/javascript">
                window.close();
            </script>
HTML;

        }
        catch (Exception $e) {

            $_ret['error'] = $e->getMessage();
        }

        $this->_japp->close();
    }

    // interazione con API zoom
    function get_local_events() {

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

    function get_event_participants() {

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
            $_events = $zoom_call->get_event_participants($this->_filterparam->zoom_event_id, $this->_filterparam->zoom_tipo);

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
            $_get_event = $_zoom_model->get_event($this->_filterparam->zoom_event_id, $this->_filterparam->zoom_tipo);

            if (is_null($_get_event)
                || !is_array($_get_event)) {
                $_store_event = $_zoom_model->store_events($this->_filterparam->zoom_event_id,
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
                $_csv_cols = $_participants[0];
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
            $_ret['error'] = $e->getMessage();
        }

        $this->_japp->close();
    }

    function get_event_list() {

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

    // importazione corsi da file xml
    function load_corsi_from_xml($id_piattaforma, $is_debug = false) {

        try {

            if (!isset($id_piattaforma)
                || $id_piattaforma == "")
                throw new Exception("Nessuna piattaforma indicata", E_USER_ERROR);

            $local_file = JPATH_ROOT . '/tmp/';
            if (!$is_debug) {
                $get_corsi = UtilityHelper::get_xml_remote($local_file, __FUNCTION__);
                if (!is_array($get_corsi))
                    throw new Exception("Nessun file di anagrafica corsi disponibile", E_USER_ERROR);
            }
            else {
                $get_corsi[] = 'GGCorsiElenco_210520101221.xml';
                $get_corsi[] = 'GGCorsoIscritti_210520101221.xml';
            }

            // elaborazione dei corsi
            $arr_anagrafica_corsi = UtilityHelper::create_unit_group_corso($get_corsi, $local_file, __FUNCTION__);
            /*
             if (is_null($arr_anagrafica_corsi)
                || count($arr_anagrafica_corsi) == 0)
                throw new Exception("Nessun corso trovato durante l'elaborazione dei file", E_USER_ERROR);
            */

            // elaborazione delle aziende e degli iscritti
            $arr_iscrizioni = UtilityHelper::create_aziende_group_users_iscritti($get_corsi, $local_file, $id_piattaforma, __FUNCTION__);
            if (is_null($arr_iscrizioni)
                || !is_array($arr_iscrizioni))
                throw new Exception("Si è verificato un problema durante la generazione dei coupon", E_USER_ERROR);

            // invio email riferite ai coupon
            $genera_model = new gglmsModelGeneraCoupon();
            foreach ($arr_iscrizioni as $piva_key => $single_gen) {

                // informazioni dell'azienda per email
                $company_infos = $arr_iscrizioni[$piva_key]['infos'];
                $coupons = $arr_iscrizioni[$piva_key]['coupons'];
                $_html_users = "";
                $_html_tutor = "";
                $template = JPATH_COMPONENT . '/models/template/xml_coupons_mail.tpl';

                // get recipients --> tutor piattaforma (cc) + tutor aziendale (to)
                $recipients = $genera_model->get_coupon_mail_recipients($company_infos['id_piattaforma'], $company_infos['id_gruppo_societa'], $company_infos['email_coupon'], true);
                if (!$recipients)
                    throw new Exception("Non ci sono tutor piattaforma configurati per questa piattaforma", E_USER_ERROR);
                // get sender
                $sender = $genera_model->get_mail_sender($company_infos['id_piattaforma'], true);
                if (!$sender)
                    throw new Exception("Non è configurato un indirizzo mail di piattaforma", E_USER_ERROR);

                $info_piattaforma = $genera_model->get_info_piattaforma($company_infos['id_piattaforma'], true);
                if (is_null($info_piattaforma)
                    || !is_array($info_piattaforma))
                    throw new Exception("nessun info piattaforma", E_USER_ERROR);

                // se viene fornita una mail a cui inviare i coupon  non la mando al tutor aziendale ma alla mail fornita
                $to = $company_infos['email_coupon'] != '' ? $company_infos['email_coupon'] : $recipients["to"]->email;
                $_info_corso = $genera_model->get_info_corso($company_infos["gruppo_corsi"], true);

                $mostra_nome_societa = $this->_config->getConfigValue('nome_azienda_intestazione_email_coupon');
                if ((int)$mostra_nome_societa == 0
                    && !is_null($mostra_nome_societa))
                    $company_infos['nome_societa'] = "";

                if ($info_piattaforma['mail_from_default'] == 1) {

                    //            ricavo alias e name dalla piattaforma di default
                    $piattaforma_default = $genera_model->get_info_piattaforma_default(true);
                    if (is_null($piattaforma_default)
                        || !is_array($piattaforma_default))
                        throw new Exception("nessuna piattaforma di default trovata", E_USER_ERROR);

                    $info_piattaforma["alias"] = $piattaforma_default['alias'];
                    $info_piattaforma["name"] = $piattaforma_default['name'];
                    $info_piattaforma["dominio"] = $piattaforma_default['dominio'];
                    //
                }

                $mailer = JFactory::getMailer();
                $mailer->setSender($sender);
                if (!$is_debug) {
                    $mailer->addRecipient($to);
                    $mailer->addCc($recipients["cc"]);
                }
                else
                    $mailer->addRecipient($this->mail_debug);

                $mailer->setSubject('Coupon corso ' . $_info_corso["titolo"]);

                // costituisco il corpo della email
                // creato tutor aziendale
                if (isset($company_infos['company_user'])
                    && $company_infos['company_user'] != "") {
                    // nuovo tutor creato
                    $tutor_infos = $company_infos['company_user'];
                    $_html_tutor = <<<HTML
                    <p>
                        La informiamo che è stato creato un account aziendale sulla piattaforma <a href="https://{$info_piattaforma['dominio']}">{$info_piattaforma["alias"]}</a>
                    </p>
                    <p>
                        Per accedere in qualità di tutor aziendale e monitorare la formazione degli utenti, è possibile utilizzare le seguenti credenziali:
                    </p>
                    <div style="font-family: monospace;">
                        <b> USERNAME:</b> {$tutor_infos['piva']}
                        <br />
                        <b> PASSWORD:</b> {$tutor_infos['password']}
                    </div>
HTML;
                }
                // creati utenti
                if (isset($company_infos['new_users'])
                    && is_array($company_infos['new_users'])
                    && count($company_infos['new_users']) > 0) {
                    $_html_users = <<<HTML
                    <p>Nuovi utenti creati:</p>
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
                }

                $_html_coupons = "";
                $coupons_count = 0;
                foreach ($coupons as $coupon_key => $sub_arr) {

                    foreach ($sub_arr as $sub_key => $coupon) {
                        $_html_coupons .= <<<HTML
                        {$coupon} <br />
HTML;
                    }

                    $coupons_count++;
                }

                $smarty = new EasySmarty();
                $smarty->assign('coupons', $_html_coupons);
                $smarty->assign('coupons_count', $coupons_count);
                $smarty->assign('course_name', $_info_corso["titolo"]);
                $smarty->assign('company_name', $company_infos['nome_societa']);
                $smarty->assign('piattaforma_name', $info_piattaforma["alias"]);
                $smarty->assign('recipient_name', $recipients["to"]->name);
                $smarty->assign('piattaforma_link', 'https://' . $info_piattaforma["dominio"]);
                $smarty->assign('company_tutor', $_html_tutor);
                $smarty->assign('company_users', $_html_users);

                $mailer->setBody($smarty->fetch_template($template, null, true, false, 0));
                $mailer->isHTML(true);

                if (!$mailer->Send()) {
                    utilityHelper::logMail(__FUNCTION__, $sender, $recipients, 0);
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
            $arr_tipologia_corso = array();
            $arr_dt_corsi = array();
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
                                ugm.idgruppo AS gruppo_corso
                                ')
                ->from('#__comprofiler AS cp')
                ->join('inner', '#__users AS u ON cp.user_id = u.id')
                ->join('inner', '#__gg_report_users AS ru ON u.id = ru.id_user')
                ->join('inner', '#__gg_view_stato_user_corso r ON r.id_anagrafica = ru.id')
                ->join('inner', '#__gg_unit AS unita ON r.id_corso = unita.id')
                ->join('inner', '#__gg_usergroup_map ugm ON ugm.idunita = unita.id')
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
                $arr_tipologia_corso[$sub_res['id_corso']] = $sub_res['tipologia_corso'];

                // data inizio / data fine corso nel formato Y-m-d
                if ($sub_res['dt_inizio_corso'] != "" && $sub_res['dt_fine_corso'])
                    $arr_dt_corsi[$sub_res['id_corso']] = $sub_res['dt_inizio_corso'] . '||' . $sub_res['dt_fine_corso'];

                $arr_gruppi[$sub_res['id_corso']] = $sub_res['gruppo_corso'];
            }

            $check_xml = UtilityHelper::create_report_xml($arr_xml,
                                                        $arr_corsi,
                                                        $arr_gruppi,
                                                        $ret_users['aziende'],
                                                        $ret_users['dual'],
                                                        $arr_dt_corsi,
                                                        $arr_codici_corso,
                                                        $arr_tipologia_corso,
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
