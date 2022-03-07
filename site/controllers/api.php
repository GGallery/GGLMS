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
require_once JPATH_COMPONENT . '/models/catalogo.php';
require_once JPATH_COMPONENT . '/models/helpdesk.php';
require_once JPATH_COMPONENT . '/models/users.php';
require_once JPATH_COMPONENT . '/controllers/zoom.php';
require_once JPATH_COMPONENT . '/controllers/users.php';

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
        $this->_filterparam->zoom_event_type = JRequest::getVar('zoom_event_type');
        $this->_filterparam->cf = JRequest::getVar('cf');
        $this->_filterparam->pw = JRequest::getVar('pw');
        $this->_filterparam->rep_pw = JRequest::getVar('rep_pw');
        $this->_filterparam->c_name = JRequest::getVar('c_name');
        $this->_filterparam->ref_token = JRequest::getVar('ref_token');
        $this->_filterparam->dominio = JRequest::getVar('dominio');
        $this->_filterparam->gruppo_id_piattaforma = JRequest::getVar('gruppo_id_piattaforma');

        $this->_filterparam->id_piattaforma = JRequest::getVar('id_piattaforma');
        $this->_filterparam->tipologia_svolgimento = JRequest::getVar('tipologia_svolgimento');
        $this->_filterparam->force_debug = JRequest::getVar('force_debug');
        $this->_filterparam->function_name = JRequest::getVar('function_name', null);
        $this->_filterparam->db_target = JRequest::getVar('db_target', null);

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
            $_events = $zoom_call->get_event_participants($this->_filterparam->zoom_event_id, $this->_filterparam->zoom_tipo, $this->_filterparam->zoom_event_type);

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

    // per sistemare il riordino delle anagrafiche dei corsi e relativa visualizzazione dei riferimenti a coupon nella tabella report
    public function fix_coupon_adesioni($old_gruppo, $arr_servizi)
    {

        /*
        SERVIZI
        231 -> 421, 422, 423, 424, 425, 426, 427, 428, 429, 430, 431, 432, 433
        */
        /*
        LA FARMACIA
        297 -> 434, 435, 436
        */
        try {

            $generation_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $generated_coupon_code = [];
            $generated_id_iscrizione = [];
            $codice_coupon = "";
            $id_iscrizione = "";
            $servizi_inserted = 0;
            $delete_servizi = [];

            $query = "SELECT *
                        FROM #__gg_coupon
                        WHERE id_gruppi = " . $this->_db->quote($old_gruppo);

            $this->_db->setQuery($query);
            $rif_servizi = $this->_db->loadAssocList();

            // esistono riferimenti
            if (count($rif_servizi)) {

                $this->_db->transactionStart();

                foreach ($rif_servizi as $key_servizio => $servizio) {

                    $insert_query = "INSERT INTO #__gg_coupon (
                        coupon,
                        id_utente,
                        gruppo,
                        creation_time,
                        abilitato,
                        id_iscrizione,
                        data_utilizzo,
                        data_abilitazione,
                        durata,
                        id_societa,
                        id_gruppi
                    ) VALUES ";

                    foreach ($arr_servizi as $key_gruppo => $id_gruppi) {

                        // codice coupon
                        $codice_coupon = 'X-' . substr(str_shuffle($generation_chars), 0, 32);
                        while(!in_array($codice_coupon, $generated_coupon_code)) {
                            $codice_coupon = 'X-' . substr(str_shuffle($generation_chars), 0, 32);
                            $generated_coupon_code[] = $codice_coupon;
                        }

                        // id iscrizione
                        $id_iscrizione = $servizio['gruppo'] . '_' . substr(str_shuffle($generation_chars), 0, 16) . '_' . $servizio['id_utente'];
                        while(!in_array($id_iscrizione, $generated_id_iscrizione)) {
                            $id_iscrizione = $servizio['gruppo'] . '_' . substr(str_shuffle($generation_chars), 0, 16) . '_' . $servizio['id_utente'];
                            $generated_id_iscrizione[] = $id_iscrizione;
                        }

                        $insert_query .= "
                            (
                                " . $this->_db->quote($codice_coupon) . ",
                                " . $this->_db->quote($servizio['id_utente']) . ",
                                " . $this->_db->quote($servizio['gruppo']) . ",
                                " . $this->_db->quote($servizio['creation_time']) . ",
                                " . $this->_db->quote($servizio['abilitato']) . ",
                                " . $this->_db->quote($id_iscrizione) . ",
                                " . $this->_db->quote($servizio['data_utilizzo']) . ",
                                " . $this->_db->quote($servizio['data_abilitazione']) . ",
                                " . $this->_db->quote($servizio['durata']) . ",
                                " . $this->_db->quote($servizio['id_societa']) . ",
                                " . $this->_db->quote($id_gruppi) . "
                            ),
                        ";

                    }

                    $insert_query = rtrim(trim($insert_query), ",") . ";";
                    $this->_db->setQuery($insert_query);
                    if (!$this->_db->execute()) throw new Exception("Query inserimento gg_coupon utente: " . $insert_query, E_USER_ERROR);

                    $delete_servizi[] = $servizio['coupon'];
                    $servizi_inserted++;

                }

                // cancello tutti i coupon vecchi associati al gruppo corso estinto
                $imp = "'" . implode( "','", $delete_servizi) . "'";
                $delete_query = "DELETE FROM #__gg_coupon WHERE coupon IN (" . $imp . ")";
                $this->_db->setQuery($delete_query);
                if (!$this->_db->execute()) throw new Exception("Query cancellazione vecchi riferimenti coupon: " . $delete_query, E_USER_ERROR);

                $this->_db->transactionCommit();

            } // servizi

            return "SONO STATI INSERITI " . $servizi_inserted . ", SONO STATI CANCELLATI " . count($delete_servizi);

        }
        catch(Exception $e) {
            $this->_db->transactionRollback();
            return __FUNCTION__ . " - FIX NON COMPLETATO: " . $e->getMessage();
        }

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

    // utility per inviare email da dominio di appoggio per procedure cli
    // richiede i parametri function_name=importa_anagrafica_farmacie&db_target (opzionale)
    public function get_debug_log() {

        try {

            if (is_null($this->_filterparam->function_name)
                || $this->_filterparam->function_name == "")
                throw new Exception("Nessun nome di funzione definito", E_USER_ERROR);

            // importa_anagrafica_farmacie_master_gg_dev_error
            $where_build = $this->_filterparam->function_name;
            $where_build .= (!is_null($this->_filterparam->db_target) && $this->_filterparam->db_target != "") ? "_" . $this->_filterparam->db_target : "";
            $where_build .= "_error";
            $dt_ref = date('Y-m-d');
            $email_dest = (!is_null($this->_filterparam->to) && $this->_filterparam->to != "") ? $this->_filterparam->to : $this->mail_debug;

            $query = $this->_db->getQuery(true)
                ->select('messaggio')
                ->from('#__gg_error_log')
                ->where('messaggio LIKE ' . $this->_db->quote('%' . $where_build . '%'))
                ->where('timestamp LIKE ' . $this->_db->quote($dt_ref . '%'))
                ->order('id DESC');

            $this->_db->setQuery($query);
            $result = $this->_db->loadAssoc();

            if (is_null($result)
                || !isset($result['messaggio'])
                || $result['messaggio'] == "")
                throw new Exception("Nessun riferimento trovato nei log", 1);

            $_response = preg_replace('/\s/', '', $result['messaggio']);
            $_response = str_replace($where_build . ":", "", $_response);
            $_response = str_replace($this->_filterparam->function_name . ":", "", $_response);
            $_response = str_replace('\\n', '<br/>', $_response);

            UtilityHelper::send_email("Errore " . $where_build, $_response, array($email_dest));

            echo "email inviata";

        }
        catch (Exception $e) {
            echo "Errore: " . $e->getMessage();
        }

        $this->_japp->close();

    }

    // importazione dell'anagrafica della farmacie da chiamata API
    public function importa_master_farmacie($db_host = null,
                                            $db_user = null,
                                            $db_password = null,
                                            $db_database = null,
                                            $db_prefix = null,
                                            $db_driver = null,
                                            $is_debug = false,
                                            $from_local = '') {

        $exists_check = array();
        $db_option = array();

        try {

            // gestisco la chiamata per andare su di un altro database
            if (!is_null($db_host)) {

                $db_option['driver'] = $db_driver;
                $db_option['host'] = $db_host;
                $db_option['user'] = $db_user;
                $db_option['password'] = utilityHelper::encrypt_decrypt('decrypt', $db_password, "GGallery00!", "GGallery00!");
                $db_option['database'] = $db_database;
                $db_option['prefix'] = $db_prefix;

                $this->_db = JDatabaseDriver::getInstance($db_option);
            }

            // parametri da configurazione del modulo farmacie
            $_params = utilityHelper::get_params_from_module('mod_farmacie', $db_option);
            $api_endpoint_farmacie = utilityHelper::get_ug_from_object($_params, "api_endpoint_farmacie");
            $api_user_auth = utilityHelper::get_ug_from_object($_params, "api_user_auth");
            $api_user_password = utilityHelper::get_ug_from_object($_params, "api_user_password");

            $local_file = JPATH_ROOT . '/tmp/';
            $filename = "master_farmacie.csv";
            if (isset($this->_filterparam->force_debug))
                $is_debug = true;

            // provo a scaricare il master delle farmacie
            $farmacie = utilityHelper::get_csv_remote($api_endpoint_farmacie, $api_user_auth, $api_user_password, $local_file, $filename, false, $is_debug, $from_local);
            if (!$farmacie)
                throw new Exception("Impossibile continuare, si è verificato un errore durante lo scaricamento di " . $filename , E_USER_ERROR);

            // la piattaforma di default del sistema
            $genera_model = new gglmsModelgeneracoupon();
            $piattaforma_default = $genera_model->get_info_piattaforma_default(true, $db_option);
            if (is_null($piattaforma_default)
                || !is_array($piattaforma_default))
                throw new Exception("nessuna piattaforma di default trovata", E_USER_ERROR);

            /*
            // una volta scaricato devo confrontare eventuali differenze nelle anagrafiche
            $model_user = new gglmsModelUsers();
            $local_master = $model_user->get_all_farmacie();

            // se il master non è vuoto devo controllare eventuali modifiche
            if ($local_master) {
                // controllo delle anagrafiche
            }
            */

            // inserisco in tabella master e creo i gruppi sulla denominazione
            $this->_db->transactionStart();
            //$query_truncate = "TRUNCATE TABLE #__gg_master_farmacie";
            $query_truncate = "DELETE FROM #__gg_master_farmacie WHERE id > 2";

            $this->_db->setQuery($query_truncate);
            if (!$this->_db->execute())
                throw new Exception("Query troncamento #__gg_master_farmacie fallita!", E_USER_ERROR);

            $query_insert = "INSERT INTO #__gg_master_farmacie
                                (id,
                                hh_store_code,
                                ragione_sociale,
                                comune,
                                tipologia,
                                legale_rappresentante,
                                hh_status,
                                ordine,
                                partita_iva,
                                indirizzo,
                                cap,
                                regione,
                                nazione,
                                telefono,
                                longitudine,
                                latitudine,
                                sigla_prov) VALUES ";

            $insert_farmacia = "";
            $arr_insert = array();
            $miss_check = array();
            // inietto ITALSALUTE SRL che non viene passato dalla chiamata API
            // inietto HIPPOCRATES HOLDING che non viene passato dalla chiamata API per i dipendenti interni
            $arr_ragsoc = ['ITALSALUTE SRL', 'HIPPOCRATES HOLDING'];
            $counter = 1;
            $farma_id = 3;
            foreach ($farmacie as $key_farmacia => $farmacia) {

                // per gestire eventuali codici a 5 cifre è necessario effettuare un controllo
                $hh_store_code = trim($farmacia[0]);
                if (strlen($hh_store_code) < 6)
                    $hh_store_code = str_pad((string)$hh_store_code, 6, '0', STR_PAD_LEFT);

                $ragione_sociale = trim($farmacia[3]);
                $comune = trim($farmacia[4]);
                $tipologia = trim($farmacia[6]);
                $legale_rappresentante = trim($farmacia[7]);
                $hh_status = trim($farmacia[8]);
                $ordine = trim($farmacia[9]);
                $partita_iva = trim($farmacia[13]);
                $indirizzo = trim($farmacia[14]);
                $cap = trim($farmacia[15]);
                $regione = trim($farmacia[16]);
                $nazione = trim($farmacia[17]);
                $telefono = trim($farmacia[18]);
                $longitudine = trim($farmacia[19]);
                $latitudine = trim($farmacia[20]);
                $sigla_prov = trim($farmacia[28]);


                // se non è impostata il codice di riferimento per gli utenti o la ragione sociale continuo
                if ($hh_store_code == ""
                    || $ragione_sociale == "") {
                    $miss_check[] = $counter;
                    $counter++;
                    continue;
                }

                // se piva esiste non inserisco di nuovo
                if (in_array($hh_store_code, $exists_check))
                    continue;

                $insert_farmacia =  "(" .
                    $farma_id . "," .
                    $this->_db->quote($hh_store_code) . "," .
                    $this->_db->quote($this->_db->escape($ragione_sociale)) . "," .
                    $this->_db->quote($this->_db->escape($comune)) . "," .
                    $this->_db->quote($tipologia) . "," .
                    $this->_db->quote($this->_db->escape($legale_rappresentante)) . "," .
                    $this->_db->quote($hh_status) . "," .
                    $this->_db->quote($ordine) . "," .
                    $this->_db->quote($partita_iva) . "," .
                    $this->_db->quote($this->_db->escape($indirizzo)) . "," .
                    $this->_db->quote($cap) . "," .
                    $this->_db->quote($this->_db->escape($regione)) . "," .
                    $this->_db->quote($this->_db->escape($nazione)) . "," .
                    $this->_db->quote($telefono) . "," .
                    $this->_db->quote($longitudine) . "," .
                    $this->_db->quote($latitudine) . "," .
                    $this->_db->quote($sigla_prov)
                    . "),";

                $arr_insert[] = $insert_farmacia;
                $exists_check[] = $hh_store_code;

                if (!in_array($ragione_sociale, $arr_ragsoc))
                    $arr_ragsoc[] = $ragione_sociale;

                $counter++;
                $farma_id++;

            }


            $_arr_query_chunked = array_chunk($arr_insert, 500);

            foreach ($_arr_query_chunked as $key => $sub_query) {

                $_executed_query = "";

                foreach ($sub_query as $kk => $vv) {

                    $_executed_query .= $vv;

                }

                $_executed_query = (substr(trim($_executed_query), -1) == ",") ? substr(trim($_executed_query), 0, -1) : $_executed_query;
                $_row_query = $query_insert . $_executed_query . ";";

                $this->_db->setQuery($_row_query);
                if (!$this->_db->execute())
                    throw new Exception("Query inserimento master farmacie fallita:
                    " . $_row_query, E_USER_ERROR);

            }

            // finito questo giro creo tutti i gruppi relativi alle farmacie
            foreach ($arr_ragsoc as $key_ragsoc => $ragsoc) {

                // controllo l'esistenza del gruppo qualifica
                $ug_farmacia = utilityHelper::check_usergroups_by_name($ragsoc, $db_option);
                // se lo usergroup non esiste lo creo
                if (is_null($ug_farmacia)) {
                    $ug_farmacia = utilityHelper::insert_new_usergroups($ragsoc, $piattaforma_default['id'], false, $db_option);

                    if (is_null($ug_farmacia))
                        throw new Exception("Errore durante l'inserimento dello usergroup " . $ragsoc, E_USER_ERROR);

                }

                $update_ug = "UPDATE #__gg_master_farmacie
                                    SET id_gruppo = " . $this->_db->quote($ug_farmacia) . "
                                    WHERE ragione_sociale = " . $this->_db->quote(addslashes(trim($ragsoc)));

                $this->_db->setQuery($update_ug);
                if (!$this->_db->execute())
                    throw new Exception("Query aggiornamento usergroup master farmacie fallita:
                    " . $update_ug, E_USER_ERROR);

            }

            $rebuild = utilityHelper::rebuild_ug_index(null, $db_option);

            $this->_db->transactionCommit();

            return 1;

        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            $_err_msg = $e->getMessage() . "\n" . print_r($exists_check, true);
            UtilityHelper::make_debug_log(__FUNCTION__, $_err_msg, __FUNCTION__ . (!is_null($db_database) ? "_" . $db_database : "") . "_error");

            // solo in produzione e non in cli
            if (!$is_debug && is_null($db_host))
                UtilityHelper::send_email("Errore " . __FUNCTION__, $_err_msg, array($this->mail_debug));

            return 0;
        }

        $this->_japp->close();

    }

    // importazione anagrafica dipendenti delle farmacie da API
    // oppure in locale se si tratta di LP (esempio) from_local è il nome file che attesta se fare riferimento a questo file che si trova nella cartella JPATH_ROOT/tmp/
    public function importa_anagrafica_farmacie($db_host = null,
                                                $db_user = null,
                                                $db_password = null,
                                                $db_database = null,
                                                $db_prefix = null,
                                                $db_driver = null,
                                                $is_debug = false,
                                                $from_local = '') {

        try {

            $db_option = array();

            /*
             * In fase di importazione devo creare un gruppo relativo a cb_descrizione_qualifica
             * a cui l'utente sarà poi associato
             * */

            // gestisco la chiamata per andare su di un altro database
            if (!is_null($db_host)) {

                $db_option['driver'] = $db_driver;
                $db_option['host'] = $db_host;
                $db_option['user'] = $db_user;
                $db_option['password'] = utilityHelper::encrypt_decrypt('decrypt', $db_password, "GGallery00!", "GGallery00!");
                $db_option['database'] = $db_database;
                $db_option['prefix'] = $db_prefix;

                $this->_db = JDatabaseDriver::getInstance( $db_option );
            }

            $local_file = JPATH_ROOT . '/tmp/';
            $_new_user = array();
            $_new_user_cp = array();
            $arr_farmacie = array();
            $jumped = array();
            $inserted_ug = false;
            $counter = 1;

            $get_farmacie = null;
            // parametri da configurazione del modulo farmacie
            $_params = utilityHelper::get_params_from_module('mod_farmacie', $db_option);
            // scarico il file csv dal repository remoto
            $api_endpoint_dipendenti = utilityHelper::get_ug_from_object($_params, "api_endpoint_dipendenti");
            $api_user_auth = utilityHelper::get_ug_from_object($_params, "api_user_auth");
            $api_user_password = utilityHelper::get_ug_from_object($_params, "api_user_password");
            $filename = "anagrafica_dipendenti.csv";

            // array dei codici qualifica, necessario per categorizzare i gruppi professione degli utenti
            $gruppi_qualifica = utilityHelper::get_codici_qualifica_farmacie($db_option);
            if (is_null($gruppi_qualifica))
                throw new Exception("Impossibile continuare, nessun codice qualifica trovato", E_USER_ERROR);

            $get_farmacie = utilityHelper::get_csv_remote($api_endpoint_dipendenti, $api_user_auth, $api_user_password, $local_file, $filename, false, $is_debug, $from_local);

            if (!is_array($get_farmacie)
                || is_null($get_farmacie))
                throw new Exception("Nessun file di anagrafica corsi disponibile", E_USER_ERROR);

            // la piattaforma di default del sistema
            $genera_model = new gglmsModelgeneracoupon();
            $piattaforma_default = $genera_model->get_info_piattaforma_default(true, $db_option);
            if (is_null($piattaforma_default)
                || !is_array($piattaforma_default))
                throw new Exception("nessuna piattaforma di default trovata", E_USER_ERROR);

            $model_user = new gglmsModelUsers();

            // mapputura dei campi da modulo
            $_campo_cb_azienda = utilityHelper::get_cb_field_name($_params, 'campo_cb_azienda', 'name', $db_option);
            $_campo_cb_filiale = utilityHelper::get_cb_field_name($_params, 'campo_cb_filiale', 'name', $db_option);
            $_campo_cb_matricola = utilityHelper::get_cb_field_name($_params, 'campo_cb_matricola', 'name', $db_option);
            $_campo_cb_cognome = utilityHelper::get_cb_field_name($_params, 'campo_cb_cognome', 'name', $db_option);
            $_campo_cb_nome = utilityHelper::get_cb_field_name($_params, 'campo_cb_nome', 'name', $db_option);
            $_campo_cb_codicefiscale = utilityHelper::get_cb_field_name($_params, 'campo_cb_codicefiscale', 'name', $db_option);
            $_campo_cb_data_nascita = utilityHelper::get_cb_field_name($_params, 'campo_cb_data_nascita', 'name', $db_option);
            $_campo_cb_codice_comune_nascita = utilityHelper::get_cb_field_name($_params, 'campo_cb_codice_comune_nascita', 'name', $db_option);
            $_campo_cb_comune_nascita = utilityHelper::get_cb_field_name($_params, 'campo_cb_comune_nascita', 'name', $db_option);
            $_campo_cb_pv_nascita = utilityHelper::get_cb_field_name($_params, 'campo_cb_pv_nascita', 'name', $db_option);
            $_campo_cb_indirizzo_residenza = utilityHelper::get_cb_field_name($_params, 'campo_cb_indirizzo_residenza', 'name', $db_option);
            $_campo_cb_cap_residenza = utilityHelper::get_cb_field_name($_params, 'campo_cb_cap_residenza', 'name', $db_option);
            $_campo_cb_comune_residenza = utilityHelper::get_cb_field_name($_params, 'campo_cb_comune_residenza', 'name', $db_option);
            $_campo_cb_pv_residenza = utilityHelper::get_cb_field_name($_params, 'campo_cb_pv_residenza', 'name', $db_option);
            $_campo_cb_data_assunzione = utilityHelper::get_cb_field_name($_params, 'campo_cb_data_assunzione', 'name', $db_option);
            $_campo_cb_data_inizio_rapporto = utilityHelper::get_cb_field_name($_params, 'campo_cb_data_inizio_rapporto', 'name', $db_option);
            $_campo_cb_data_licenziamento = utilityHelper::get_cb_field_name($_params, 'campo_cb_data_licenziamento', 'name', $db_option);
            $_campo_cb_stato_dipendente = utilityHelper::get_cb_field_name($_params, 'campo_cb_stato_dipendente', 'name', $db_option);
            $_campo_cb_descrizione_qualifica = utilityHelper::get_cb_field_name($_params, 'campo_cb_descrizione_qualifica', 'name', $db_option);
            $_campo_cb_email = utilityHelper::get_cb_field_name($_params, 'campo_cb_email', 'name', $db_option);
            $_campo_cb_codice_esterno_cdc_2 = utilityHelper::get_cb_field_name($_params, 'campo_cb_codice_esterno_cdc_2', 'name', $db_option);
            $_campo_cb_codice_esterno_cdc_3 = utilityHelper::get_cb_field_name($_params, 'campo_cb_codice_esterno_cdc_3', 'name', $db_option);
            $_campo_cb_esterno_rep_2 = utilityHelper::get_cb_field_name($_params, 'campo_cb_esterno_rep_2', 'name', $db_option);

            $this->_db->transactionStart();

            // grande loop di inserimento degli utenti
            // la chiave per controllare l'esistenza dell'utente sarà il codice fiscale impostato anche come username
            foreach ($get_farmacie as $row_key => $row_arr) {
                // colonna 0 - anno - no
                // colonna 1 - mese - no
                // colonna 2 - azienda - si
                $cb_azienda = trim($row_arr[2]);
                // colonna 3 - filiale - si
                $cb_filiale = trim($row_arr[3]);
                // colonna 4 - ragione sociale - no
                // colonna 5 - descrizione filiale - no
                // colonna 6 - matricola - si
                $cb_matricola = trim($row_arr[6]);
                // colonna 7 - cognome - si
                $cb_cognome = utilityHelper::normalizza_stringa($row_arr[7]);
                // colonna 8 - nome - si
                $cb_nome = utilityHelper::normalizza_stringa($row_arr[8]);
                // colonna 9 - codice fiscale - si
                $cb_codicefiscale = trim($row_arr[9]);
                // colonna 10 - data di nascita - si
                $cb_data_nascita = utilityHelper::convert_dt_in_mysql(trim($row_arr[10]));
                // colonna 11 - codice comune di nascita - si
                $cb_codice_comune_nascita = trim($row_arr[11]);
                // colonna 12 - comune di nascita - si
                $cb_comune_nascita = trim($row_arr[12]);
                // colonna 13 - provincia di nascita - si
                $cb_pv_nascita = trim($row_arr[13]);
                // colonna 14 - indirizzo di residenza - si
                $cb_indirizzo_residenza = trim($row_arr[14]);
                // colonna 15 - cap di residenza - si
                $cb_cap_residenza = trim($row_arr[15]);
                // colonna 16 - comune di residenza - si
                $cb_comune_residenza = trim($row_arr[16]);
                // colonna 17 - provincia di residenza - si
                $cb_pv_residenza = trim($row_arr[17]);
                // colonna 18 - data assunzione - si
                $cb_data_assunzione = utilityHelper::convert_dt_in_mysql(trim($row_arr[18]));
                // colonna 19 - data licenziamento - si
                $cb_data_licenziamento = utilityHelper::convert_dt_in_mysql(trim($row_arr[19]));
                // colonna 20 - stato del dipendente - si
                $cb_stato_dipendente = trim($row_arr[20]);
                // colonna 21 - Cod.tab.qualifica - in base a questo si decide il gruppo di appartenenza dell'utente
                $codice_tab_qualifica = trim($row_arr[21]);
                // colonna 22 - descrizione qualifica - si
                // questo campo se uguale a DIRETTORE FARMACIA lo elegge a tutor aziendale
                // al momento però non è richiesta questa differenziazione
                $cb_descrizione_qualifica = utilityHelper::normalizza_stringa($row_arr[22]);
                // colonna 23 - email - si
                $cb_email = trim($row_arr[23]);
                // colonna 24 - codice esterno cdc 2 - si
                $cb_codice_esterno_cdc_2 = trim($row_arr[24]);
                // colonna 25 - codice esterno cdc 3 - si
                // chiave univoca per fare riferimento al master delle farmacie
                // per gestire eventuali codici a 5 cifre è necessario effettuare un controllo
                $cb_codice_esterno_cdc_3 = trim($row_arr[25]);
                if (strlen($cb_codice_esterno_cdc_3) < 6)
                    $cb_codice_esterno_cdc_3 = str_pad((string)$cb_codice_esterno_cdc_3, 6, '0', STR_PAD_LEFT);

                // colonna 26 - codice esterno rep 2 - si
                $cb_esterno_rep_2 = (isset($row_arr[26]) && !is_null($row_arr[26]) && $row_arr[26] != "") ? trim($row_arr[26]) : "";
                // colonna 27 - data inizio rapporti - si
                $cb_data_inizio_rapporto = utilityHelper::convert_dt_in_mysql(trim($row_arr[27]));

                // controllo se la data di licenziamento è maggiore di oggi
                if (!is_null($cb_data_licenziamento)
                    && $cb_data_licenziamento != ""
                    && utilityHelper::check_dt_major(date('Y-m-d'), $cb_data_licenziamento))
                    $cb_stato_dipendente = 9;
                else
                    $cb_stato_dipendente = (is_null($cb_stato_dipendente) || $cb_stato_dipendente == "") ? 0 : $cb_stato_dipendente;

                $check_user_id = utilityHelper::check_user_by_username($cb_codicefiscale, false, $db_option);
                $_new_user_id = null;
                $new_user = false;
                $ug_qualifica = null;
                $master_farmacia = null;
                $ug_farmacia = null;

                // per il momento non restituisco errore ma vado avanti nell'inserimento delle utenze
                if (is_null($cb_codicefiscale)
                    || $cb_codicefiscale == "") {
                    $jumped[] = "Riferimento a codice fiscale non valorizzato -> riga: " . $counter . " -> " . $cb_email . " | " . $cb_codicefiscale;
                    continue;
                }

                // codice di riferimento che lega l'utente ad una farmacia
                if (is_null($cb_codice_esterno_cdc_3)
                    || $cb_codice_esterno_cdc_3 == "") {
                    $jumped[] = "Riferimento a codice_esterno_cdc_3 non valorizzato -> CF: " . $cb_codicefiscale;
                    continue;
                }

                // codice che determina la qualifica dell'utente
                if (is_null($codice_tab_qualifica)
                    || $codice_tab_qualifica == ""
                    || (int) $codice_tab_qualifica == 0) {
                    $jumped[] = "Riferimento a codice_tab_qualifica non valorizzato -> CF: " . $cb_codicefiscale;
                    continue;
                }

                // email dell'utente
                if (is_null($cb_email)
                    || $cb_email == "") {
                    $jumped[] = "Riferimento a email non valorizzato -> CF: " . $cb_codicefiscale;
                    continue;
                }

                // validita email
                if (!filter_var($cb_email, FILTER_VALIDATE_EMAIL)) {
                    $jumped[] = "Riferimento a email non valida -> CF: " . $cb_codicefiscale;
                    continue;
                }

                // carico la farmacia di riferimento in relazione al $cb_codice_esterno_cdc_3
                if (!in_array($cb_codice_esterno_cdc_3, $arr_farmacie)) {
                    $master_farmacia = $model_user->get_farmacie($cb_codice_esterno_cdc_3, $db_option);

                    if (is_null($master_farmacia)) {
                        $jumped[] = "Impossibile specificare il gruppo farmacia per " . $cb_codice_esterno_cdc_3;
                        continue;
                    }

                    // associo il gruppo alla farmacia in base al codice
                    $arr_farmacie[$cb_codice_esterno_cdc_3] = $master_farmacia['id_gruppo'];
                }

                $ug_farmacia = $arr_farmacie[$cb_codice_esterno_cdc_3];

                // utente non esistente che quindi va creato
                if (is_null($check_user_id)) {
                    $_new_user['name'] = addslashes($cb_nome) . " " . addslashes($cb_cognome);
                    $_new_user['username'] = $cb_codicefiscale;
                    $_new_user['email'] = $cb_email;
                    $password = utilityHelper::genera_stringa_randomica('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!$%&/?-_', 8);
                    $_new_user['password'] = JUserHelper::hashPassword($password);

                    // imposto l'utente bloccato a priori
                    // se licenziato l'utente va bloccato
                    // if ((int) $cb_stato_dipendente == 9)
                    $_new_user['block'] = 1;

                    $_user_insert_query = UtilityHelper::get_insert_query("users", $_new_user);
                    $_user_insert_query_result = UtilityHelper::insert_new_with_query($_user_insert_query, true, $db_option);
                    if (!is_array($_user_insert_query_result))
                        throw new Exception("Inserimento utente fallito: " . $_user_insert_query_result . " -> query: " . $_user_insert_query, E_USER_ERROR);

                    $_new_user_id = $_user_insert_query_result['success'];
                    // riferimento id per CP
                    $_new_user_cp['id'] = $_new_user_id;
                    $_new_user_cp['user_id'] = $_new_user_id;

                    $new_user = true;
                }

                // cablo i campi per popolare CB
                $_new_user_cp[$_campo_cb_azienda] = $cb_azienda;
                $_new_user_cp[$_campo_cb_filiale] = addslashes($cb_filiale);
                $_new_user_cp[$_campo_cb_matricola] = $cb_matricola;
                $_new_user_cp[$_campo_cb_cognome] = addslashes($cb_cognome);
                $_new_user_cp[$_campo_cb_nome] = addslashes($cb_nome);
                $_new_user_cp[$_campo_cb_codicefiscale] = $cb_codicefiscale;
                $_new_user_cp[$_campo_cb_data_nascita] = $cb_data_nascita;
                $_new_user_cp[$_campo_cb_codice_comune_nascita] = $cb_codice_comune_nascita;
                $_new_user_cp[$_campo_cb_comune_nascita] = addslashes($cb_comune_nascita);
                $_new_user_cp[$_campo_cb_pv_nascita] = addslashes($cb_pv_nascita);
                $_new_user_cp[$_campo_cb_indirizzo_residenza] = addslashes($cb_indirizzo_residenza);
                $_new_user_cp[$_campo_cb_cap_residenza] = $cb_cap_residenza;
                $_new_user_cp[$_campo_cb_comune_residenza] = addslashes($cb_comune_residenza);
                $_new_user_cp[$_campo_cb_pv_residenza] = $cb_pv_residenza;
                $_new_user_cp[$_campo_cb_data_assunzione] = $cb_data_assunzione;
                $_new_user_cp[$_campo_cb_data_inizio_rapporto] = $cb_data_inizio_rapporto;
                $_new_user_cp[$_campo_cb_data_licenziamento] = $cb_data_licenziamento;
                $_new_user_cp[$_campo_cb_stato_dipendente] = $cb_stato_dipendente;
                $_new_user_cp[$_campo_cb_descrizione_qualifica] = addslashes($cb_descrizione_qualifica);
                //$_new_user_cp[$_campo_cb_email] = $cb_email;
                $_new_user_cp[$_campo_cb_codice_esterno_cdc_2] = $cb_codice_esterno_cdc_2;
                $_new_user_cp[$_campo_cb_codice_esterno_cdc_3] = $cb_codice_esterno_cdc_3;
                $_new_user_cp[$_campo_cb_esterno_rep_2] = $cb_esterno_rep_2;

                // se l'utente non esiste lo aggiungo
                // inserimento utente in CP
                if ($new_user) {
                    $_cp_insert_query = UtilityHelper::get_insert_query("comprofiler", $_new_user_cp);
                    $_cp_insert_query_result = UtilityHelper::insert_new_with_query($_cp_insert_query, true, $db_option);
                    if (!is_array($_cp_insert_query_result))
                        throw new Exception(print_r($_new_user_cp, true) . " errore durante inserimento -> query: " . $_cp_insert_query, E_USER_ERROR);

                    // aggiungo il suo riferimento nella tabella farmacie_dipendenti
                    $user_farmacia = $model_user->insert_user_farmacia($_new_user_id, $ug_farmacia, $cb_codice_esterno_cdc_3, $cb_data_inizio_rapporto, $cb_data_licenziamento, $db_option);
                    if (is_null($user_farmacia))
                        throw new Exception("Inserimento user_farmacia fallito per user_id: " . $_new_user_id, E_USER_ERROR);

                    // devo associare l'utente ad un gruppo farmacia
                    $insert_user_ug_farmacia = utilityHelper::set_usergroup_generic($_new_user_id, (array) $ug_farmacia, $db_option);
                    if (!is_array($insert_user_ug_farmacia))
                        throw new Exception("Si è verificato un errore durante l'inserimento dell'utente nel gruppo farmacia " . $_new_user_id . " nel gruppo: " . $ug_farmacia . " errore: " . $insert_user_ug_farmacia, E_USER_ERROR);

                }
                else { // utente esiste devo aggiornarlo

                    unset($_new_user_cp['id']);
                    unset($_new_user_cp['user_id']);

                    $_cp_update_query = utilityHelper::get_update_query("comprofiler", $_new_user_cp, "user_id = '". $check_user_id . "'");
                    $_cp_update_query_result = utilityHelper::update_with_query($_cp_update_query, $db_option);
                    if (!is_array($_cp_update_query_result))
                        throw new Exception(print_r($_new_user_cp, true) . " errore durante aggiornamento -> query: " . $_cp_update_query, E_USER_ERROR);

                    // se licenziato l'utente va bloccato
                    if ((int) $cb_stato_dipendente == 9) {
                        // uso un array di appoggio altrimenti può generare errori di chiave unica duplicata in update
                        //$_new_user['block'] = 1;
                        $_update_tmp['block'] = 1;
                        $_cp_update_query = utilityHelper::get_update_query("users", $_update_tmp, "id = '". $check_user_id . "'");
                        $_cp_update_query_result = utilityHelper::update_with_query($_cp_update_query, $db_option);
                        if (!is_array($_cp_update_query_result))
                            throw new Exception(print_r($_new_user_cp, true) . " errore durante aggiornamento -> query: " . $_cp_update_query, E_USER_ERROR);

                    }

                    // verifico se l'utente ha cambiato farmacia oppure
                    $get_user_farmacia = $model_user->get_user_farmacia($check_user_id, $cb_codice_esterno_cdc_3, $db_option);

                    if (is_null($get_user_farmacia)
                        || count($get_user_farmacia) == 0) {

                        // mi serve l'ultimo gruppo dell'utente
                        $last_farmacia = $model_user->get_user_farmacia($check_user_id, null, $db_option);
                        if (is_null($last_farmacia))
                            throw new Exception("Nessun gruppo farmacia precedente per " . $check_user_id, E_USER_ERROR);

                        // rimuovo utente dal vecchio gruppo
                        $remove_ug_user = utilityHelper::remove_user_from_usergroup($check_user_id, (array) $last_farmacia['id_gruppo'], $db_option);
                        if (is_null($remove_ug_user))
                            throw new Exception("Errore durante la rimozione dell'utente " . $check_user_id . " da gruppo: " . $last_farmacia['id_gruppo'], E_USER_ERROR);

                        // ha cambiato farmacia (o non c'è nessun riferimento)
                        $user_farmacia = $model_user->insert_user_farmacia($check_user_id, $ug_farmacia, $cb_codice_esterno_cdc_3, $cb_data_inizio_rapporto, $cb_data_licenziamento, $db_option);
                        if (is_null($user_farmacia))
                            throw new Exception("Inserimento user_farmacia fallito per user_id: " . $check_user_id, E_USER_ERROR);

                        // inserimento dell'utente nel gruppo farmacia nuovo
                        $insert_user_ug_farmacia = utilityHelper::set_usergroup_generic($check_user_id, (array) $ug_farmacia, $db_option);
                        if (!is_array($insert_user_ug_farmacia))
                            throw new Exception("Si è verificato un errore durante l'inserimento dell'utente " . $check_user_id . " nel gruppo: " . $ug_farmacia . " errore: " . $insert_user_ug_farmacia, E_USER_ERROR);

                    }
                    else {
                        // è nella medesima - aggiorno i campi perchè potrebbe essere stato licenziato
                        $user_farmacia = $model_user->update_user_farmacia($_new_user_id, $cb_codice_esterno_cdc_3, $cb_data_licenziamento, $db_option);
                        if (is_null($user_farmacia))
                            throw new Exception("Aggiornamento user_farmacia fallito per user_id: " . $_new_user_id, E_USER_ERROR);

                    }
                }

                // controllo l'esistenza del gruppo qualifica
                // Attenzione!!!
                // il gruppo non punta più cb_descrizione_qualifica ma fa riferimento al codice codice_tab_qualifica ed all'array gruppi_qualifica
                if (!isset($gruppi_qualifica[$codice_tab_qualifica])
                    || $gruppi_qualifica[$codice_tab_qualifica] == "") {
                    $jumped[] = "Gruppo qualifica non trovato -> GRUPPO: " . $codice_tab_qualifica . " CF: " . $cb_codicefiscale;
                    continue;
                }
                //$ug_qualifica = utilityHelper::check_usergroups_by_name($cb_descrizione_qualifica, $db_option);
                $ug_qualifica = utilityHelper::check_usergroups_by_name($gruppi_qualifica[$codice_tab_qualifica], $db_option);
                // se lo usergroup non esiste lo creo
                if (is_null($ug_qualifica)) {
                    //$ug_qualifica = utilityHelper::insert_new_usergroups($cb_descrizione_qualifica, $piattaforma_default['id'], false, $db_option);
                    $ug_qualifica = utilityHelper::insert_new_usergroups($gruppi_qualifica[$codice_tab_qualifica], $piattaforma_default['id'], false, $db_option);
                    if (is_null($ug_qualifica)) {
                        //throw new Exception("Errore durante l'inserimento dello usergroup " . $cb_descrizione_qualifica, E_USER_ERROR);
                        throw new Exception("Errore durante l'inserimento dello usergroup " . $gruppi_qualifica[$codice_tab_qualifica], E_USER_ERROR);
                    }

                    $inserted_ug = true;

                }

                $_user_id_ref = !is_null($check_user_id) ? $check_user_id : $_new_user_id;

                // controllo se l'utente è nel gruppo mansione in caso contrario lo aggiungo
                $check_user_ug = utilityHelper::check_user_into_ug($_user_id_ref, (array) $ug_qualifica, $db_option);
                if (!$check_user_ug) {
                    $insert_user_ug = utilityHelper::set_usergroup_generic($_user_id_ref, (array) $ug_qualifica, $db_option);
                    if (!is_array($insert_user_ug))
                        throw new Exception("Si è verificato un errore durante l'inserimento dell'utente " . $_user_id_ref . " nel gruppo: " . $cb_descrizione_qualifica . " errore: " . $insert_user_ug, E_USER_ERROR);
                }

                $counter++;

            }

            // se ho inserito un nuovo gruppo faccio rebuild
            if ($inserted_ug)
                $rebuild = utilityHelper::rebuild_ug_index(null, $db_option);

            $this->_db->transactionCommit();

            // mail inibite se processo da terminale
            if (count($jumped) > 0) {
                //UtilityHelper::send_email("Utenti non inseriti per dati mancanti " . __FUNCTION__, print_r($jumped, true), array($this->mail_debug));
                UtilityHelper::make_debug_log(__FUNCTION__, "Utenti non inseriti per dati mancanti " . print_r($jumped, true), __FUNCTION__ . (!is_null($db_database) ? "_" . $db_database : "") . "_error");
            }

            return 1;

        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            UtilityHelper::make_debug_log(__FUNCTION__ . (!is_null($db_host)) ? $db_host : "", $e->getMessage(), __FUNCTION__ . (!is_null($db_database) ? "_" . $db_database : "") . "_error");

            if (is_null($db_host))
                UtilityHelper::send_email("Errore " . __FUNCTION__, $e->getMessage(), array($this->mail_debug));

            return 0;
        }

        $this->_japp->close();
    }

    public function get_report_per_completamento_corso() {

        $_ret = array();

        try {

            $model_report = new gglmsModelReport();
            $_tmp = array();
            $filename = "";
            $first_id = 0;

            if (!isset($this->_filterparam->corso_id)
                || $this->_filterparam->corso_id == "")
                throw new Exception("Impossibile continuare, corso_id non specificato", E_USER_ERROR);

            if (!isset($this->_filterparam->gruppo_id_piattaforma)
                || $this->_filterparam->gruppo_id_piattaforma == "")
                throw new Exception("Impossibile continuare, gruppo_id_piattaforma non specificato", E_USER_ERROR);

            $get_report = $model_report->get_report_utenti_completamento_corso($this->_filterparam->corso_id, $this->_filterparam->gruppo_id_piattaforma);

            // errore
            if (is_null($get_report))
                throw new Exception("Si è verificato un errore durante la generazione del report", E_USER_ERROR);

            // nessun risultato
            if (count($get_report) == 0)
                throw new Exception("Nessun risultato disponibile per il corso selezionato", E_USER_ERROR);

            // processo il risultato
            foreach ($get_report as $key_report => $single_report) {

                if ($filename == "") $filename = preg_replace('/\s+/', '', strtolower($single_report['titolo_corso']));

                if ($first_id == 0) $first_id = $single_report['id_utente'];

                $_tmp[$single_report['id_utente']]['titolo_corso'] = $single_report['titolo_corso'];
                $_tmp[$single_report['id_utente']]['nominativo'] = $single_report['nominativo'];
                $_tmp[$single_report['id_utente']]['codice_fiscale'] = $single_report['codice_fiscale'];
                $_tmp[$single_report['id_utente']]['email'] = $single_report['email'];
                $_tmp[$single_report['id_utente']]['qualifica'] = $single_report['qualifica'];
                $_tmp[$single_report['id_utente']]['ragione_sociale'] = $single_report['ragione_sociale'];
                $_tmp[$single_report['id_utente']]['cod_farmacia'] = str_pad($single_report['cod_farmacia'], 6, '0', STR_PAD_LEFT);
                $_tmp[$single_report['id_utente']]['stato_corso'] = $single_report['stato_corso'];

            }

            $_csv_cols = utilityHelper::get_cols_from_array($_tmp[$first_id]);
            $_export_csv = utilityHelper::esporta_csv_spout($_tmp, $_csv_cols, $filename . '.csv');

            $this->_japp->close();

            // chiusura della finestra dopo generazione del report
            $_html = <<<HTML
            <script type="text/javascript">
                window.close();
            </script>
HTML;

        }
        catch(Exception $e) {
            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            $_ret['error'] = $e->getMessage();
        }

        echo json_encode($_ret);

        $this->_japp->close();

    }

    // report iscritti per corso
    public function get_report_per_corso() {

        $_ret = array();

        try {

            $model_report = new gglmsModelReport();
            $model_helpdesk = new gglmsModelHelpDesk();
            $_tmp = array();
            $filename = "";
            $first_id = 0;

            if (!isset($this->_filterparam->corso_id)
                || $this->_filterparam->corso_id == "")
                throw new Exception("Impossibile continuare, corso_id non specificato", E_USER_ERROR);

            if (!isset($this->_filterparam->dominio)
                || $this->_filterparam->dominio == "")
                throw new Exception("Impossibile continuare, dominio non specificato", E_USER_ERROR);

            $helpdesk_info = $model_helpdesk->getPiattaformaHelpDeskInfo($this->_filterparam->dominio);
            $get_report = $model_report->get_report_utenti_iscritti_per_corso($this->_filterparam->corso_id, $this->_filterparam->dominio, $helpdesk_info->group_id);

            // errore
            if (is_null($get_report))
                throw new Exception("Si è verificato un errore durante la generazione del report", E_USER_ERROR);

            // nessun risultato
            if (count($get_report) == 0)
                throw new Exception("Nessun risultato disponibile per il corso selezionato", E_USER_ERROR);

            // processo il risultato
            foreach ($get_report as $key_report => $single_report) {

                if ($filename == "")
                    $filename = (isset($single_report['alias']) && $single_report['alias'] != "")
                        ? $single_report['alias']
                        : preg_replace('/\s+/', '', strtolower($single_report['titolo_corso']));

                if ($first_id == 0)
                    $first_id = $single_report['id_utente'];

                //$_tmp[$single_report['id_utente']]['titolo_corso'] = $single_report['titolo_corso'];
                $_tmp[$single_report['id_utente']]['nominativo'] = $single_report['nominativo'];
                $_tmp[$single_report['id_utente']]['username'] = $single_report['username'];
                $_tmp[$single_report['id_utente']]['gruppo_utente'] = isset($_tmp[$single_report['id_utente']]['gruppo_utente'])
                    ? $_tmp[$single_report['id_utente']]['gruppo_utente'] . "," . $single_report['gruppo_utente']
                    : $single_report['gruppo_utente'];
                $_tmp[$single_report['id_utente']]['cod_farmacia'] = str_pad($single_report['cod_farmacia'], 6, '0', STR_PAD_LEFT);

            }

            $_csv_cols = utilityHelper::get_cols_from_array($_tmp[$first_id]);
            $_export_csv = utilityHelper::esporta_csv_spout($_tmp, $_csv_cols, $filename . '.csv');

            $this->_japp->close();

            // chiusura della finestra dopo generazione del report
            $_html = <<<HTML
            <script type="text/javascript">
                window.close();
            </script>
HTML;

        }
        catch (Exception $e) {
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            $_ret['error'] = $e->getMessage();
        }

        echo json_encode($_ret);

        $this->_japp->close();
    }

    // il numero di utenti correntemente attivati
    public function get_activated_users()
    {

        try {

            $_tmp = array();
            $filename = "utentiAttivi-" . time();
            $first_id = 0;
            $user_model = new gglmsModelUsers();
            $activatedUsers = $user_model->get_activated_users_details();

            if (is_null($activatedUsers))
                throw new Exception("Nessun utente attivo al momento", E_USER_ERROR);

            // processo il risultato
            foreach ($activatedUsers as $key_report => $single_user) {

                if ($first_id == 0) $first_id = $single_user['id_utente'];

                $_tmp[$single_user['id_utente']]['nominativo'] = $single_user['nominativo'];
                $_tmp[$single_user['id_utente']]['codice_fiscale'] = $single_user['codice_fiscale'];
                $_tmp[$single_user['id_utente']]['email'] = $single_user['email'];
                $_tmp[$single_user['id_utente']]['qualifica'] = $single_user['qualifica'];
                $_tmp[$single_user['id_utente']]['ragione_sociale'] = $single_user['ragione_sociale'];

            }

            $_csv_cols = utilityHelper::get_cols_from_array($_tmp[$first_id]);
            $_export_csv = utilityHelper::esporta_csv_spout($_tmp, $_csv_cols, $filename . '.csv');

        }
        catch(Exception $e) {
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            echo $e->getMessage();
        }

        $this->_japp->close();

    }

    // visualizza elenco corsi per report
    public function get_rows_partecipazione_corsi() {

        $_rows = array();
        $_ret = array();
        $_total_rows = 0;

        try {

            $_call_params = JRequest::get($_GET);
            $gruppo_id_piattaforma = (isset($_call_params['gruppo_id_piattaforma']) && $_call_params['gruppo_id_piattaforma'] != "") ? $_call_params['gruppo_id_piattaforma'] : null;
            $_search = (isset($_call_params['search']) && $_call_params['search'] != "") ? $_call_params['search'] : null;
            $_offset = (isset($_call_params['offset']) && $_call_params['offset'] != "") ? $_call_params['offset'] : 0;
            $_limit = (isset($_call_params['limit']) && $_call_params['limit'] != "") ? $_call_params['limit'] : 10;
            $_sort = (isset($_call_params['sort']) && $_call_params['sort'] != "") ? $_call_params['sort'] : null;
            $_order = (isset($_call_params['order']) && $_call_params['order'] != "") ? $_call_params['order'] : null;

            $model_catalogo = new gglmsModelCatalogo();
            $corsi = $model_catalogo->get_svolgimento_corsi($_offset, $_limit, $_search, $_sort, $_order);

            if (isset($corsi['rows'])) {

                $_total_rows = $corsi['total_rows'];

                foreach ($corsi['rows'] as $_key_corso => $corso) {

                    foreach ($corso as $key => $value) {

                        if ($key == 'report_extra') {
                            $value = <<<HTML
                                <span
                                    style="color: red; cursor: pointer;"
                                    onclick="window.open('index.php?option=com_gglms&task=api.get_report_per_completamento_corso&corso_id={$corso['id_unita']}&gruppo_id_piattaforma={$gruppo_id_piattaforma}')">
                                        <i class="fas fa-file-download fa-2x"></i>
                                </span>
HTML;
                        }

                        $_ret[$_key_corso][$key] = $value;

                    }

                }

            }
        }
        catch(Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        $_rows['rows'] = $_ret;
        $_rows['total_rows'] = $_total_rows;

        echo json_encode($_rows);

        $this->_japp->close();

    }

    // visualizza il calendario corsi
    public function get_rows_tabella_corsi() {

        $_rows = array();
        $_ret = array();
        $_total_rows = 0;

        try {

            $_call_params = JRequest::get($_GET);
            $dominio = (isset($_call_params['dominio']) && $_call_params['dominio'] != "") ? $_call_params['dominio'] : null;
            $_search = (isset($_call_params['search']) && $_call_params['search'] != "") ? $_call_params['search'] : null;
            $_offset = (isset($_call_params['offset']) && $_call_params['offset'] != "") ? $_call_params['offset'] : 0;
            $_limit = (isset($_call_params['limit']) && $_call_params['limit'] != "") ? $_call_params['limit'] : 10;
            $_sort = (isset($_call_params['sort']) && $_call_params['sort'] != "") ? $_call_params['sort'] : null;
            $_order = (isset($_call_params['order']) && $_call_params['order'] != "") ? $_call_params['order'] : null;
            $modalita = (isset($_call_params['modalita']) && $_call_params['modalita'] != "") ? $_call_params['modalita'] : "1,2";
            $_before_today = (isset($_call_params['before_today'])) ? true : false;

            $model_catalogo = new gglmsModelCatalogo();
            $corsi = $model_catalogo->get_calendario_corsi($dominio, $modalita, $_offset, $_limit, $_search, $_sort, $_order, $_before_today);

            if (isset($corsi['rows'])) {
                // verifico se l'utente è loggato
                $_current_user = JFactory::getUser();

                $usergroups = utilityHelper::get_usergroups_list();
                $categorie_evento = utilityHelper::get_categorie_evento();
                $_total_rows = $corsi['total_rows'];

                foreach ($corsi['rows'] as $_key_corso => $corso) {

                    foreach ($corso as $key => $value) {

                        $_new = "";

                        if ($key == 'data')
                            $value = utilityHelper::convert_dt_in_format($value, 'd-m-Y');
                        else if ($key == 'destinatari') {
                            $explode_arr = explode(",", $value);
                            // se ci sono più di 5 elementi visualizzo la dicitura gruppi multipli
                            if (count($explode_arr) <= 5) {
                                foreach ($explode_arr as $comma) {
                                    $_new .= $usergroups[$comma] . ",";
                                }
                                $value = rtrim($_new, ',');
                            }
                            else
                                $value = JText::_('COM_GGLMS_BOXES_SCHEDA_GRUPPI_MULTI');
                        }
                        else if ($key == 'tipologia') {
                            $value = $corso['obbligatorio'] == 1 ? '<span style="color: #E82B31">' . strtoupper($value) . '</span>' : $value;
                        }
                        else if ($key == 'modalita') {

                            if (isset($categorie_evento[$value]))
                                $value = $categorie_evento[$value];

                        }
                        else if ($key == 'note') {
                            $booked = false;
                            // se l'utente è loggato verifico se è già iscritto
                            if ($_current_user->id) {
                                /*
                                if (!is_null($corso['destinatari']) && $corso['destinatari'] != "") {
                                    $explode_arr = explode(",", $corso['destinatari']);
                                    foreach ($explode_arr as $group_id => $group) {
                                        if (isset($_current_user->groups)
                                            && in_array($group_id, $_current_user->groups)) {
                                            $booked = true;
                                            break;
                                        }
                                    }
                                }
                                else {
                                    if (in_array($corso['gruppo_corso'], $_current_user->groups))
                                        $booked = true;
                                }
                                */

                                if (in_array($corso['gruppo_corso'], $_current_user->groups)) {
                                    $value = '<span style="color: red">' . JText::_('COM_GGLMS_BOXES_SCHEDA_ISCRITTO') . '</span>';
                                    $booked = true;
                                }

                            }

                            // in base al fatto che sia prenotabile o meno visualizzo un pulsante
                            // $corso['is_bookable'] == 1
                            if (!$booked) {
                                $user_id =  $_current_user->id ? $_current_user->id : 0;
                                $value = '<a href="javascript:" class="btn btn-info" style="min-height: 50px;" onclick="iscriviUtenteCorso(' . $corso['id'] . ', ' . $user_id . ', \'' . $dominio . '\')">' . JText::_('COM_GGLMS_BOXES_SCHEDA') . '</a>';
                            }
                            else
                                $value = ucfirst($value);

                        }
                        else if ($_before_today
                            && $key == 'report_extra') {
                            $value = <<<HTML
                                <span
                                    style="color: red; cursor: pointer;"
                                    onclick="window.open('index.php?option=com_gglms&task=api.get_report_per_corso&corso_id={$corso['id']}&dominio={$dominio}')">
                                        <i class="fas fa-file-download fa-2x"></i>
                                </span>
HTML;
                        }

                        $_ret[$_key_corso][$key] = $value;

                    }

                }

            }

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        $_rows['rows'] = $_ret;
        $_rows['total_rows'] = $_total_rows;

        echo json_encode($_rows);

        $this->_japp->close();

    }

    // primo login l'utente viene attivato per codice fiscale
    public function active_dipendente_by_cf() {

        $_ret = array();

        try {

            // controllo del cf
            $cb_codicefiscale = trim($this->_filterparam->cf);
            $user_password = trim($this->_filterparam->pw);
            $c_name = trim($this->_filterparam->c_name);

            if ($cb_codicefiscale == "")
                throw new Exception("Il Codice fiscale deve essere compilato!", E_USER_ERROR);

            if ($c_name == "")
                throw new Exception("Nessun riferimento al valore cookie da impostare", E_USER_ERROR);

            $check_cf = utilityHelper::conformita_cf($cb_codicefiscale);
            if ($check_cf['valido'] == 0)
                throw new Exception($check_cf['cf'] . ': ' . $check_cf['msg'], E_USER_ERROR);

            if (isset($user_password)
                && $user_password != "") {

                // devo fare login
                $result_login = JFactory::getApplication()->login(
                    [
                        'username' => $cb_codicefiscale,
                        'password' => $user_password
                    ],
                    [
                        'remember' => true,
                        'silent'   => true
                    ]
                );

                if (!$result_login)
                    throw new Exception("Login fallito oppure l'utente non è ancora stato attivato", E_USER_ERROR);

                utilityHelper::_set_cookie_by_name("logged_in", time(), time()+3600);
                $_ret['success'] = 'logged_in';

                echo json_encode($_ret);

                $this->_japp->close();

            }

            // controllo se l'utente esiste
            $check_user_id = utilityHelper::check_user_by_username($cb_codicefiscale);
            if (is_null($check_user_id))
                throw new Exception("Il Codice fiscale " . $cb_codicefiscale . " non è stato trovato", E_USER_ERROR);

            // parametri da configurazione del modulo farmacie
            $_params = utilityHelper::get_params_from_module('mod_farmacie');
            // la chiave di criptazione di default
            $secret_key = $secret_iv = utilityHelper::get_ug_from_object($_params, "secret_key");

            // controllo se il codice fiscale è già stato attivato
            $model_user = new gglmsModelUsers();
            $check_activation = $model_user->check_activation_user_farmarcie($check_user_id, $cb_codicefiscale);
            $crypted_cf = utilityHelper::encrypt_decrypt('encrypt', $cb_codicefiscale, $secret_key, $secret_iv);
            $decrypt_c_name = utilityHelper::encrypt_decrypt('decrypt', $c_name, $secret_key, $secret_iv);

            // se l'utente non è stato attivato inserisco il record nella tabella di riferimento
            if (!$check_activation) {

                $this->_db->transactionStart();

                // per sicurezza - nel caso fosse già in tabella - cancello il suo record
                $delete_activation = $model_user->delete_activation_user_farmarcie($check_user_id, $cb_codicefiscale);
                if (is_null($delete_activation))
                    throw new Exception("Si è verificato un errore durante l'attivazione dell'utente:
                    " . $check_user_id .
                        " CF: " . $cb_codicefiscale, E_USER_ERROR);

                $insert_activation = $model_user->insert_activation_user_farmarcie($check_user_id, $cb_codicefiscale);
                if (is_null($insert_activation))
                    throw new Exception("Si è verificato un errore durante l'attivazione dell'utente:
                    " . $check_user_id .
                        " CF: " . $cb_codicefiscale, E_USER_ERROR);

                $this->_db->transactionCommit();

                $_ret['success'] = 'set_password';
                // imposto cookie di set_password di 1 ora
                utilityHelper::_set_cookie_by_name("set_password", $crypted_cf, time()+3600);
            }
            else {
                // l'utente è già presente nella tabella di attivazione
                $_ret['success'] = 'is_activated';
                // imposto cookie di is_activated di 5 minuti
                utilityHelper::_set_cookie_by_name("is_activated", $crypted_cf, time()+300);
                // cookie attivazione 10 anni
                utilityHelper::_set_cookie_by_name($decrypt_c_name, $crypted_cf, time() + (10 * 365 * 24 * 60 * 60));
            }

        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            //UtilityHelper::send_email("Errore " . __FUNCTION__, $e->getMessage(), array($this->mail_debug));
            $_ret['error'] = $e->getMessage();
        }

        echo json_encode($_ret);

        $this->_japp->close();
    }

    // imposta password dell'utente al primo login
    public function set_dipendente_password() {

        $_ret = array();

        try {
            $user_password = trim($this->_filterparam->pw);
            $user_password_rep = trim($this->_filterparam->rep_pw);
            $cb_codicefiscale = trim($this->_filterparam->cf);
            $c_name = trim($this->_filterparam->c_name);

            // controllo dei dati
            if ($user_password == "")
                throw new Exception("La password non può essere vuota", E_USER_ERROR);

            if ($user_password != $user_password_rep)
                throw new Exception("Password e Ripeti password non corrispondono", E_USER_ERROR);

            if ($cb_codicefiscale == "")
                throw new Exception("Nessun riferimento al Codice fiscale", E_USER_ERROR);

            if ($c_name == "")
                throw new Exception("Nessun riferimento al valore cookie da impostare", E_USER_ERROR);

            // decriptazione del codice fiscale
            // parametri da configurazione del modulo farmacie
            $_params = utilityHelper::get_params_from_module('mod_farmacie');
            // email di comunicazione
            $email_default = utilityHelper::get_ug_from_object($_params, "email_default");
            // la chiave di criptazione di default
            $secret_key = $secret_iv = utilityHelper::get_ug_from_object($_params, "secret_key");
            $decrypt_c_name = utilityHelper::encrypt_decrypt('decrypt', $c_name, $secret_key, $secret_iv);
            $decrypt_cf = utilityHelper::encrypt_decrypt('decrypt', $cb_codicefiscale, $secret_key, $secret_iv);

            // controllo se l'utente esiste
            $check_user_id = utilityHelper::check_user_by_username($decrypt_cf);
            if (is_null($check_user_id))
                throw new Exception("Il Codice fiscale non è stato trovato", E_USER_ERROR);

            $site_config = JFactory::getConfig();
            $model_user = new gglmsModelUsers();
            $get_user = $model_user->get_user($check_user_id);

            $this->_db->transactionStart();

            $update_password = $model_user->update_password_user_farmacia($decrypt_cf, $user_password);
            if (is_null($update_password))
                throw new Exception("Si è verificato un errore durante l'aggiornamento della password", E_USER_ERROR);

            $this->_db->transactionCommit();

            // accendo cookie di 10 anni
            utilityHelper::_unset_cookie_by_name("set_password");
            // cookie completed 1 ora
            utilityHelper::_set_cookie_by_name("completed", $cb_codicefiscale, time()+3600);
            // cookie attivazione 10 anni
            utilityHelper::_set_cookie_by_name($decrypt_c_name, $cb_codicefiscale, time() + (10 * 365 * 24 * 60 * 60));

            // invio email a utente
            $controller_user = new gglmsControllerUsers();

            // controllo validità email
            if (!utilityHelper::check_email_validation($get_user->email)) {
                throw new Exception("E-mail utente non valida", E_USER_ERROR);
            }

            /*
             * andrebbe modificato sendMail
             * aggiungendo nella funzione addCc
            if (utilityHelper::check_email_validation($email_default))
                $destinatari[] = $email_default;
            */

            $oggetto ="Nuove credenziali per accesso a " . $site_config['sitename'];
            $site_root = JURI::root();
            $dt_now = utilityHelper::convert_time_to_tz(date('Y-m-d H:i:s'));
            $activation_params = $decrypt_cf . '||' . $check_user_id . '||' . $dt_now;
            $crypt_activation_params = utilityHelper::encrypt_decrypt('encrypt', $activation_params, $secret_key, $secret_iv);
            $activation_link = $site_root . 'index.php?option=com_gglms&task=api.confirm_dipendente_activation&ref_token=' . $crypt_activation_params;
            $body = <<<HTML
                <p>Questa è un messaggio generato automaticamente e contiene le tue credenziali per accedere a {$site_config['sitename']}</p>
                <p>I tuoi dati sono:</p>
                <p>Username: {$decrypt_cf}</p>
                <p>Password: {$user_password}</p>
                <p>Per completare l'attivazione del tuo profilo clicca su questo <a href="{$activation_link}" target="_blank">LINK</a></p>
                <br /><br />
                <p>
                    <i>Cogliamo l'occasione per ringraziarti, lo staff di {$site_config['sitename']}</i>
                </p>
HTML;

            $send_email = $controller_user->sendMail($get_user->email, $oggetto, $body);

            $_ret['success'] = 'completed';

        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            //UtilityHelper::send_email("Errore " . __FUNCTION__, $e->getMessage(), array($this->mail_debug));
            $_ret['error'] = $e->getMessage();
        }

        echo json_encode($_ret);

        $this->_japp->close();
    }

    // prima di resettare la password invio una email informativa all'utente
    public function reset_password_dipendente_by_cf() {

        $_ret = array();

        try {

            $cb_codicefiscale = trim($this->_filterparam->cf);
            if ($cb_codicefiscale == "")
                throw new Exception("Nessun riferimento al Codice fiscale", E_USER_ERROR);

            // controllo se l'utente esiste
            // l'utente deve essere privo di blocchi
            $check_user_id = utilityHelper::check_user_by_username($cb_codicefiscale, true);
            if (is_null($check_user_id))
                throw new Exception("Il Codice fiscale non è stato trovato o l'utente non è ancora stato attivato", E_USER_ERROR);

            // se esiste permetto all'utente di effettuare il reset sul codice fiscale corrente
            // parametri da configurazione del modulo farmacie
            $_params = utilityHelper::get_params_from_module('mod_farmacie');
            // la chiave di criptazione di default
            $secret_key = $secret_iv = utilityHelper::get_ug_from_object($_params, "secret_key");
            $crypted_cf = utilityHelper::encrypt_decrypt('encrypt', $cb_codicefiscale, $secret_key, $secret_iv);
            // cookie reset_password_exec 5 minuti
            utilityHelper::_set_cookie_by_name("reset_password_exec", $crypted_cf, time()+3600);

            $site_config = JFactory::getConfig();
            $model_user = new gglmsModelUsers();
            $get_user = $model_user->get_user($check_user_id);

            // invio email a utente
            $controller_user = new gglmsControllerUsers();
            // controllo validità email
            if (!utilityHelper::check_email_validation($get_user->email)) {
                throw new Exception("E-mail utente non valida", E_USER_ERROR);
            }

            $site_root = JURI::root();
            $dt_now = utilityHelper::convert_time_to_tz(date('Y-m-d H:i:s'));
            $activation_params = $cb_codicefiscale . '||' . $check_user_id . '||' . $dt_now;
            $crypt_activation_params = utilityHelper::encrypt_decrypt('encrypt', $activation_params, $secret_key, $secret_iv);
            /*
             * nessuna email reindirizzo l'utente passandogli il token come parametro
            $oggetto ="Richiesta reset password da " . $site_config['sitename'];
            $body = <<<HTML
                <p>Questa è un messaggio generato automaticamente dal sito {$site_config['sitename']} per effettuare il reset della password di accesso</p>
                <p>Username: {$cb_codicefiscale}</p>
                <p>Per confermare il procedimento copia e incolla questa stringa nel campo Token di conferma {$crypt_activation_params}</p>
                <br /><br />
                <p>
                    <i>Cogliamo l'occasione per ringraziarti, lo staff di {$site_config['sitename']}</i>
                </p>
HTML;

            $send_email = $controller_user->sendMail($get_user->email, $oggetto, $body, true);
            */

            $_ret['success'] = 'reset_password_exec&token=' . $crypt_activation_params;

        }
        catch (Exception $e) {
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            //UtilityHelper::send_email("Errore " . __FUNCTION__, $e->getMessage(), array($this->mail_debug));
            $_ret['error'] = $e->getMessage();
        }

        echo json_encode($_ret);

        $this->_japp->close();
    }

    // reset password
    public function reset_dipendente_password() {

        $_ret = array();

        try {
            $user_password = trim($this->_filterparam->pw);
            $user_password_rep = trim($this->_filterparam->rep_pw);
            $cb_codicefiscale = trim($this->_filterparam->cf);
            $confirm_token = trim($this->_filterparam->ref_token);

            // controllo dei dati
            if ($user_password == "")
                throw new Exception("La password non può essere vuota", E_USER_ERROR);

            if ($user_password != $user_password_rep)
                throw new Exception("Password e Ripeti password non corrispondono", E_USER_ERROR);

            if ($cb_codicefiscale == "")
                throw new Exception("Nessun riferimento al Codice fiscale", E_USER_ERROR);

            // decriptazione del codice fiscale
            // parametri da configurazione del modulo farmacie
            $_params = utilityHelper::get_params_from_module('mod_farmacie');
            // email di comunicazione
            $email_default = utilityHelper::get_ug_from_object($_params, "email_default");
            // la chiave di criptazione di default
            $secret_key = $secret_iv = utilityHelper::get_ug_from_object($_params, "secret_key");
            $decrypt_cf = utilityHelper::encrypt_decrypt('decrypt', $cb_codicefiscale, $secret_key, $secret_iv);
            // controllo se l'utente esiste
            $check_user_id = utilityHelper::check_user_by_username($decrypt_cf);
            if (is_null($check_user_id))
                throw new Exception("Il Codice fiscale non è stato trovato", E_USER_ERROR);

            $site_config = JFactory::getConfig();
            $model_user = new gglmsModelUsers();
            $get_user = $model_user->get_user($check_user_id);

            // controllo token
            $decrypt_ref_token = utilityHelper::encrypt_decrypt('decrypt', $confirm_token, $secret_key, $secret_iv);

            // il token deve contenere il codice fiscale e lo user_id altrimenti restituisco errore
            $activation_params = explode("||", $decrypt_ref_token);
            if (count($activation_params) < 3)
                throw new Exception("Il token ricevuto non è corretto", E_USER_ERROR);

            // il primo elemento deve essere un codice fiscale valido
            if ($decrypt_cf != $activation_params[0])
                throw new Exception("Il Codice fiscale per il quale si sta richiedendo il reset non è corrispondente", E_USER_ERROR);

            // il secondo elemento deve essere user_id
            if ($check_user_id != $activation_params[1])
                throw new Exception("Il codice utente per il quale si sta richiedendo il reset non è corrispondente", E_USER_ERROR);

            // il terzo elemento è una data - controllo se la differenza non è superiore all'ora
            $dt_now = utilityHelper::convert_time_to_tz(date('Y-m-d H:i:s'));
            $hours_diff = utilityHelper::get_hours_diff($dt_now, $activation_params[2]);
            if ($hours_diff > 1)
                throw new Exception("Token di reset password scaduto!", E_USER_ERROR);

            $this->_db->transactionStart();
            $update_password = $model_user->update_password_user_farmacia($decrypt_cf, $user_password);
            if (is_null($update_password))
                throw new Exception("Si è verificato un errore durante l'aggiornamento della password", E_USER_ERROR);

            $this->_db->transactionCommit();

            // invio email a utente
            $controller_user = new gglmsControllerUsers();
            // controllo validità email
            if (!utilityHelper::check_email_validation($get_user->email)) {
                throw new Exception("E-mail utente non valida", E_USER_ERROR);
            }

            $oggetto ="Richiesto reset password per " . $site_config['sitename'];
            $body = <<<HTML
                <p>Il reset password richiesto è andato a buone fine!</p>
                <p>Le tue credenziali per accedere a {$site_config['sitename']} sono:</p>
                <p>Username: {$decrypt_cf}</p>
                <p>Password: {$user_password}</p>
                <br /><br />
                <p>
                    <i>Lo staff di {$site_config['sitename']}</i>
                </p>
HTML;

            $send_email = $controller_user->sendMail($get_user->email, $oggetto, $body);

            $_ret['success'] = 'La tua password è stata correttamente aggiornata. Si prega di controllare ' . $get_user->email;

        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            //UtilityHelper::send_email("Errore " . __FUNCTION__, $e->getMessage(), array($this->mail_debug));
            $_ret['error'] = $e->getMessage();
        }

        echo json_encode($_ret);

        $this->_japp->close();
    }

    // invia una email all'utente per confermare l'avvenuta attivazione
    public function confirm_dipendente_activation() {

        try {

            // controllo la presenza del token
            if (!isset($this->_filterparam->ref_token)
                || $this->_filterparam->ref_token == "")
                throw new Exception("Il procedimento non può essere completato, token di riferimento mancante", E_USER_ERROR);

            // parametri da configurazione del modulo farmacie
            $_params = utilityHelper::get_params_from_module('mod_farmacie');// la chiave di criptazione di default
            $secret_key = $secret_iv = utilityHelper::get_ug_from_object($_params, "secret_key");
            $decrypt_ref_token = utilityHelper::encrypt_decrypt('decrypt', $this->_filterparam->ref_token, $secret_key, $secret_iv);
            $_new_user = array();

            // il token deve contenere il codice fiscale e lo user_id altrimenti restituisco errore
            $activation_params = explode("||", $decrypt_ref_token);
            if (count($activation_params) < 3)
                throw new Exception("Il token ricevuto non è corretto", E_USER_ERROR);

            // il terzo elemento è una data - controllo se la differenza non è superiore all'ora
            $dt_now = utilityHelper::convert_time_to_tz(date('Y-m-d H:i:s'));
            $hours_diff = utilityHelper::get_hours_diff($dt_now, $activation_params[2]);
            if ($hours_diff > 24)
                throw new Exception("Token di attivazione account scaduto!", E_USER_ERROR);

            // il primo elemento deve essere un codice fiscale valido
            $codice_fiscale = $activation_params[0];
            // il secondo elemento deve essere user_id
            $user_id = $activation_params[1];

            // controllo se effettivamente lo è
            $check_cf = utilityHelper::conformita_cf($codice_fiscale);
            if ($check_cf['valido'] == 0)
                throw new Exception($check_cf['cf'] . ': ' . $check_cf['msg'], E_USER_ERROR);

            // controllo se l'utente esiste
            $check_user_id = utilityHelper::check_user_by_username($codice_fiscale);
            if (is_null($check_user_id))
                throw new Exception("Il Codice fiscale non è stato trovato", E_USER_ERROR);

            // controllo se mi risulta lo user_id arrivato dalla chiamata con quello a database
            if ($check_user_id != $user_id)
                throw new Exception("L'identificativo utente non è congruente ai parametri di sistema", E_USER_ERROR);

            // controllo se l'utente è già stato sbloccato
            $model_user = new gglmsModelUsers();
            $get_user = $model_user->get_user($check_user_id);
            if ($get_user->block == 0)
                throw new Exception("L'utente è già stato attivato", E_USER_ERROR);

            $this->_db->transactionStart();

            $_new_user['block'] = 0;
            // procedo all'attivazione dell'utente
            $_cp_update_query = utilityHelper::get_update_query("users", $_new_user, "username = '". $codice_fiscale . "'");
            $_cp_update_query_result = utilityHelper::update_with_query($_cp_update_query);
            if (!is_array($_cp_update_query_result))
                throw new Exception("Si è verificato un errore durante l'aggiornamento", E_USER_ERROR);

            $this->_db->transactionCommit();

            echo "Utente correttamente attivato";

        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            //UtilityHelper::send_email("Errore " . __FUNCTION__, $e->getMessage(), array($this->mail_debug));
            echo $e->getMessage();
        }

        $this->_japp->close();

    }

    public function popola_codici_qualifica_farmacie() {

        try {

            $this->_db->transactionStart();

            $query_truncate = "TRUNCATE #__gg_codici_qualifica_farmacie";

            $this->_db->setQuery($query_truncate);
            if (!$this->_db->execute())
                throw new Exception("Query troncamento #__gg_codici_qualifica_farmacie fallita!", E_USER_ERROR);

            $insert_query = "INSERT INTO #__gg_codici_qualifica_farmacie (codice, rif_gruppo) VALUES ";

            // Farmacisti
            // Dal 110 al 119 + 166 e 167
            $insert_farmacisti = "";
            for ($i=110; $i<=119; $i++) {
                $insert_farmacisti .= " (" . $i . ", 'Farmacisti'), ";
            }
            $insert_farmacisti .= "(166, 'Farmacisti') , (167, 'Farmacisti'), ";

            // Commessi
            // Dal 120 al 125 + 168 e 169
            $insert_commessi = "";
            for ($i=120; $i<=125; $i++) {
                $insert_commessi .= " (" . $i . ", 'Commessi'), ";
            }
            $insert_commessi .= "(168, 'Commessi') , (169, 'Commessi'), ";

            // Magazzino
            // Dal 126 al 131 + 170 e 171
            $insert_magazzino = "";
            for ($i=126; $i<=131; $i++) {
                $insert_magazzino .= " (" . $i . ", 'Magazzino'), ";
            }
            $insert_magazzino .= "(170, 'Magazzino') , (171, 'Magazzino'), ";

            // Dermocosmesi
            // Dal 132 al 137 + 172 e 173
            $insert_dermoc = "";
            for ($i=132; $i<=137; $i++) {
                $insert_dermoc .= " (" . $i . ", 'Dermocosmesi'), ";
            }
            $insert_dermoc .= "(172, 'Dermocosmesi') , (173, 'Dermocosmesi'), ";

            // Pulizie
            // Dal 138 al 141
            $insert_pulizie = "";
            for ($i=138; $i<=141; $i++) {
                $insert_pulizie .= " (" . $i . ", 'Pulizie'), ";
            }

            // Estetica
            // Dal 142 al 147 + 174 e 175
            $insert_estetica = "";
            for ($i=142; $i<=147; $i++) {
                $insert_estetica .= " (" . $i . ", 'Estetica'), ";
            }
            $insert_estetica .= "(174, 'Estetica') , (175, 'Estetica'), ";

            // Laboratorio
            // Dal 148 al 153 + 176 e 177
            $insert_lab = "";
            for ($i=148; $i<=153; $i++) {
                $insert_lab .= " (" . $i . ", 'Laboratorio'), ";
            }
            $insert_lab .= "(176, 'Laboratorio') , (177, 'Laboratorio'), ";

            // Infermieri
            // Dal 182 al 185
            $insert_inf = "";
            for ($i=182; $i<=185; $i++) {
                $insert_inf .= " (" . $i . ", 'Infermieri'), ";
            }

            // Segreteria
            // Dal 154 al 159 + 178 e 179
            $insert_segreteria = "";
            for ($i=154; $i<=159; $i++) {
                $insert_segreteria .= " (" . $i . ", 'Segreteria'), ";
            }
            $insert_segreteria .= "(178, 'Segreteria') , (179, 'Segreteria'), ";

            // Imp. Amministr.
            // Dal 160 al 165 + 180 e 181
            $insert_impieg = "";
            for ($i=160; $i<=165; $i++) {
                $insert_impieg .= " (" . $i . ", 'Imp. Amministr.'), ";
            }
            $insert_impieg .= "(180, 'Imp. Amministr.') , (181, 'Imp. Amministr.'), ";

            // Resp Ottica
            // 191
            $insert_ottica = "(191, 'Resp Ottica'), ";

            // Gruppi extra
            $insert_extra = "(88888, 'Liberi Professionisti'), (99999, 'Dipendenti Holding')";

            // eseguo query inserimento
            $this->_db->setQuery($insert_query .
                $insert_farmacisti .
                $insert_commessi .
                $insert_magazzino .
                $insert_dermoc .
                $insert_pulizie .
                $insert_estetica .
                $insert_lab .
                $insert_inf .
                $insert_segreteria .
                $insert_impieg .
                $insert_ottica .
                $insert_extra);
            if (!$this->_db->execute())
                throw new Exception("Query inserimento #__gg_codici_qualifica_farmacie fallita!", E_USER_ERROR);

            $this->_db->transactionCommit();

            echo "OK!";

        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            echo $e->getMessage();
        }

        $this->_japp->close();

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
