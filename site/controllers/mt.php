<?php
/**
 * Created by IntelliJ IDEA.
 * User: Francesca
 * Date: 26/01/2021
 * Time: 09:10
 */
defined('_JEXEC') or die;

class gglmsControllerMt extends JControllerLegacy {

    private $_user;
    private $_japp;
    public $_params;
    public $_db;
    private $_config;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();

        $this->_user = JFactory::getUser();
        $this->_db = &JFactory::getDbo();
        $this->_config = new gglmsModelConfig();

        $this->_filterparam->id_utente = JRequest::getVar('id_utente');
        $this->_filterparam->id_corso = JRequest::getVar('id_corso');

    }

    public function get_report_details() {

        try {

            $ids_contenuti = array();
            $titoli_unita = array();
            $denominazione_utente = array();
            $filename = 'report_' . time() . '.csv';
            $_ret = array();

            // utenti selezionati
            $ids_utenti = explode(",", $this->_filterparam->id_utente);
            $query_utente = "SELECT u.id AS id_utente, 
                              UPPER(COALESCE(cp.cb_nome, '')) AS nome_utente, 
                              UPPER(COALESCE(cp.cb_cognome, '')) AS cognome_utente, 
                              UPPER(COALESCE(cp.cb_codicefiscale, '')) AS codice_fiscale
                              FROM #__users u
                              JOIN #__comprofiler cp ON u.id = cp.user_id
                              WHERE u.id IN (" . implode(',',$ids_utenti) . ")";

            $this->_db->setQuery($query_utente);
            $results_utente = $this->_db->loadAssocList();

            if (count($results_utente) == 0)
                throw new Exception("Nessuna anagrafica disponibile per gli utenti selezionati", E_USER_ERROR);

            foreach ($results_utente as $key_utente => $utente) {
                $denominazione_utente[$utente['id_utente']] = $utente['nome_utente'] . ' ' . $utente['cognome_utente'];
            }

            // tutte le unità per id_corso
            $query_unita = "SELECT id AS id_unita, titolo AS titolo_unita
                            FROM #__gg_unit
                            WHERE (
                                    id = " . $this->_db->quote($this->_filterparam->id_corso) . " 
                                    OR unitapadre = " . $this->_db->quote($this->_filterparam->id_corso) . "
                                    )
                            ORDER BY id";
            $this->_db->setQuery($query_unita);
            $unita_results = $this->_db->loadAssocList();

            if (count($unita_results) == 0)
                throw new Exception("Nessuna unità disponibile per il corso selezionato", E_USER_ERROR);

            foreach ($unita_results as $key_unita => $unita) {

                $titoli_unita[$unita['id_unita']] = $unita['titolo_unita'];

                $query_contenuto = "SELECT idcontenuto
                                      FROM #__gg_unit_map
                                      WHERE idunita = " . $this->_db->quote($unita['id_unita']) . "
                                      ORDER BY ordinamento";
                $this->_db->setQuery($query_contenuto);
                $contenuto_results = $this->_db->loadAssocList();

                if (count($contenuto_results) == 0)
                    continue;

                foreach ($contenuto_results as $key_contenuto => $contenuto) {

                    if (!in_array($contenuto['idcontenuto'], $ids_contenuti))
                        $ids_contenuti[] = $contenuto['idcontenuto'];

                }

            }

            if (count($ids_contenuti) == 0)
                throw new Exception("Nessun contenuto disponibile per le unità selezionate", E_USER_ERROR);

            $query = "SELECT contenuti.id AS id_contenuto, 
                            contenuti.titolo AS titolo_contenuto, 
                            contenuti.tipologia, 
                            ctipo.descrizione AS tipologia_descrizione, 
                            contenuti.id_quizdeluxe AS id_quiz,
                            unit.id AS id_unita
                        FROM #__gg_contenuti contenuti
                        JOIN #__gg_contenuti_tipology ctipo ON contenuti.tipologia = ctipo.id
                        JOIN #__gg_unit_map unit_map ON contenuti.id = unit_map.idcontenuto
                        JOIN #__gg_unit unit ON unit_map.idunita = unit.id
                        WHERE contenuti.id IN (" . implode(",", $ids_contenuti) . ")";


            $this->_db->setQuery($query);
            $results = $this->_db->loadAssocList();

            if (count($results) == 0)
                throw new Exception("Nessun risultato per corso ed utente selezionati", E_USER_ERROR);

            // per utente
            foreach ($ids_utenti as $id_utente) {

                foreach ($results as $key_corso => $corso) {

                    $_data_inizio = null;
                    $_data_fine = null;
                    $_secondi = null;
                    $_giorno = null;

                    // controllo la tipologia se quiz proseguo per il momento
                    if ($corso['tipologia'] == 7) {

                        $quiz_query = "SELECT c_total_time AS secondi, 
                                              DATE_FORMAT(c_date_time, '%Y-%m-%d') AS giorno, 
                                              c_date_time AS data_inzio, 
                                              timestamp AS data_fine
                                        FROM #__quiz_r_student_quiz
                                        WHERE c_student_id = " . $this->_db->quote($id_utente) . "
                                        AND c_quiz_id = " . $this->_db->quote($corso['id_quiz']);
                        $this->_db->setQuery($quiz_query);
                        $quiz_results = $this->_db->loadAssocList();

                        if (count($quiz_results) == 0)
                            continue;

                        // elenco i tentativi per ogni quiz
                        foreach ($quiz_results as $key => $quiz) {

                            $_ret[$id_utente][$corso['id_unita']][] = array(
                                'titolo' => $corso['titolo_contenuto'],
                                'tipologia' => $corso['tipologia_descrizione'],
                                'giorno' => $quiz['giorno'],
                                'data_inizio' => $quiz['data_inzio'],
                                'data_fine' => $quiz['data_fine'],
                                'tempo' => $quiz['secondi']
                            );

                        }

                        continue;
                    }

                    // altrimenti consulto scormvars
                    $scorm_query = "SELECT * 
                                        FROM #__gg_scormvars 
                                        WHERE scoid = " . $this->_db->quote($corso['id_contenuto']) . "
                                        AND userid = " . $this->_db->quote($id_utente);
                    $this->_db->setQuery($scorm_query);
                    $scorm_results = $this->_db->loadAssocList();

                    if (count($scorm_results) == 0)
                        continue;

                    foreach ($scorm_results as $key => $scorm) {

                        foreach ($scorm as $key_scorm => $value) {

                            if ($key_scorm == 'varName'
                                && $value == 'cmi.core.last_visit_date') {
                                $_data_inizio = $scorm['timestamp'];
                                $_giorno = $scorm['varValue'];
                            }

                            if ($key_scorm == 'varName'
                                && $value == 'cmi.core.total_time') {
                                $_data_fine = $scorm['timestamp'];
                                $_secondi = $scorm['varValue'];
                            }

                        }

                    }

                    $_ret[$id_utente][$corso['id_unita']][] = array(
                        'titolo' => $corso['titolo_contenuto'],
                        'tipologia' => $corso['tipologia_descrizione'],
                        'giorno' => $_giorno,
                        'data_inizio' => $_data_inizio,
                        'data_fine' => $_data_fine,
                        'tempo' => $_secondi
                    );

                }
            }

            // elaborazione dei risultati
            if (count($_ret) == 0)
                throw new Exception("Nessun dato disponibile per l'esportazione", E_USER_ERROR);

            $arr_cols = array('titolo', 'tipologia', 'giorno', 'data_inizio', 'data_fine', 'tempo');
            /*
            $csv = "";
            $rows = 0;
            $_check_user = array();
            $_check_corso = array();

            if ($rows == 0)
                $csv = implode(";", $arr_cols);

            foreach ($_ret as $report_user => $arr_sotto_unita) {

                if (!in_array($report_user, $_check_user)) {
                    $csv .= $denominazione_utente[$report_user] . ";;;;;\n";
                    $_check_user[] = $report_user;
                }

                foreach ($arr_sotto_unita as $key_su => $value_su) {

                    if (!in_array($key_su, $_check_corso)) {
                        $csv .= ";" . strtoupper($titoli_unita[$key_su]) . ";;;;\n";
                        $_check_corso[] = $key_su;
                    }

                    foreach ($value_su as $kk => $vv) {

                        $csv .= $value_su[$kk]['titolo'] .
                            ";" . $value_su[$kk]['tipologia'] .
                            ";" . $value_su[$kk]['giorno'] .
                            ";" . $value_su[$kk]['data_inizio'] .
                            ";" . $value_su[$kk]['data_fine'] .
                            ";" . $value_su[$kk]['tempo'] . "\n";


                    }

                }

            }

            echo $csv;*/

            UtilityHelper::esporta_csv_corsi_finanza($_ret, $arr_cols, $filename, $denominazione_utente, $titoli_unita);

        }
        catch (Exception $e) {
            echo $e->getMessage();
        }

        $this->_japp->close();

    }

    public function test_() {

        $check = null;

        if (!$check)
            echo "ok";
        else
            echo "non ok";

        $this->_japp->close();

    }

    public function get_tz() {

        $oggi = '2020-11-16T07:21:36Z';
        $dt = new DateTime($oggi, new DateTimeZone('UTC'));
        $dt->setTimezone(new DateTimeZone('Europe/Rome'));
        echo $dt->format('Y-m-d H:i:s');

        $this->_japp->close();

    }

    public function batch_responsabili() {

        $_ret = array();
        $_ending_msg = "";

        try {

            $dt = new DateTime();
            $_arr_resp = array();
            $_insert_arr = array();

            $target = $_SERVER['DOCUMENT_ROOT'] . '/batch/' . $dt->format('Ymd') . '_IMPORT_RESPONSABILI.txt';
            if (!file_exists($target))
                throw new Exception("File: " . $target . " non trovato", 1);

            $fp = @fopen($target, 'r');

            if (empty($fp))
                throw new Exception("File " . $target . " non leggibile", 1);

            if (!$fp)
                throw new Exception("File " . $target . " handler file non valido", 1);

            $ug_direttori = JRequest::getVar('ug_direttori');
            if (!isset($ug_direttori)
                || $ug_direttori == "")
                throw new Exception("Gruppo direttori non definito", 1);

            $insert_map = "INSERT INTO #__ggif_direttore_dipendenti_map
                                    (userid_direttore, userid_dipendente) VALUES ";
            $insert_group = "INSERT INTO #__user_usergroup_map 
                                    (user_id, group_id) VALUES ";
            $counter = 0;
            $rows = explode("\n", fread($fp, filesize($target)));
            foreach ($rows as $key => $row) {

                $_tmp = array();
                if ($counter == 0
                    || $row == "") {
                    $counter++;
                    continue;
                }

                $_tmp = explode(";", trim($row));
                // gli id responsabile che poi saranno usati per inserimento in usergroups
                if (!in_array($_tmp[0], $_arr_resp))
                    $_arr_resp[] = $_tmp[0];

                $_tmp_query = " (
                                       " . $this->_db->quote($_tmp[0]) . ",
                                       " . $this->_db->quote($_tmp[1]) . "
                                    ), ";
                $_insert_arr[] = $_tmp_query;

                $counter++;

            }

            if (count($_insert_arr) == 0)
                throw new Exception("Nessun valore da inserire a database", 1);

            $counter = 0;
            $_arr_query_chunked = array_chunk($_insert_arr, 500);

            $this->_db->transactionStart();

            // tronco tabella ggif_direttore_dipendenti_map
            $truncate_query = "TRUNCATE #__ggif_direttore_dipendenti_map";
            $this->_db->setQuery($truncate_query);
            if (!$this->_db->execute())
                throw new Exception("Query truncate errore -> " . $truncate_query, 1);

            foreach ($_arr_query_chunked as $key => $sub_query) {

                $_executed_query = "";

                foreach ($sub_query as $kk => $vv) {

                    $_executed_query .= $vv;

                }

                $_executed_query = (substr(trim($_executed_query), -1) == ",") ? substr(trim($_executed_query), 0, -1) : $_executed_query;
                $_row_query = $insert_map . $_executed_query;

                $this->_db->setQuery($_row_query);
                if (!$this->_db->execute())
                    throw new Exception("Query inserimento errore -> " . $_row_query, 1);

                $counter++;
            }

            // cancello tutti gli utenti appartenenti al gruppo direttori che viene passato come parametro
            $delete_ug = "DELETE FROM #__user_usergroup_map
                            WHERE group_id = " . $this->_db->quote($ug_direttori);
            $this->_db->setQuery($delete_ug);
            if (!$this->_db->execute())
                throw new Exception("Query inserimento errore -> " . $delete_ug, 1);

            // inserisco tutti gli utenti appartenenti al gruppo direttori
            $_insert_arr = array();
            foreach ($_arr_resp as $resp) {

                $_tmp_query = "(
                                " . $this->_db->quote($resp) . ",
                                " . $this->_db->quote($ug_direttori) . "
                            ), ";

                $_insert_arr[] = $_tmp_query;

            }

            if (count($_insert_arr) == 0)
                throw new Exception("Nessun gruppo da inserire a database", 1);

            $_arr_query_chunked = array_chunk($_insert_arr, 500);

            foreach ($_arr_query_chunked as $key => $sub_query) {

                $_executed_query = "";

                foreach ($sub_query as $kk => $vv) {

                    $_executed_query .= $vv;

                }

                $_executed_query = (substr(trim($_executed_query), -1) == ",") ? substr(trim($_executed_query), 0, -1) : $_executed_query;
                $_row_query = $insert_group . $_executed_query;

                $this->_db->setQuery($_row_query);
                if (!$this->_db->execute())
                    throw new Exception("Query inserimento errore -> " . $_row_query, 1);

                $counter++;
            }

            $this->_db->transactionCommit();
            $_ending_msg = " SUCCESS: " . $dt->format('d/m/Y H:i:s') . " operazione conclusa";
        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            $_ending_msg = " ERROR: " . $e->getMessage();
        }

        DEBUGG::log($_ending_msg,__FUNCTION__,0,1);
        $this->_japp->close();

    }

    public function _get_config() {

        $_check = $this->_config->getConfigValue('cassu');
        var_dump($_check);

        if ((int) $_check == 0
            && !is_null($_check))
            echo "ZERO";
        else
            echo "NON ZERO";

        $this->_japp->close();

    }

    public function get_last_insert_coupon() {

        try {

            $_ret = array();

            $query = $this->_db->getQuery(true)
                    ->select('messaggio')
                    ->from('#__gg_error_log')
                    ->where('messaggio LIKE ' . $this->_db->quote('%api_genera_coupon_response%'))
                    ->order('id DESC');

            $this->_db->setQuery($query);
            $result = $this->_db->loadAssoc();

            if (is_null($result)
                || !isset($result['messaggio'])
                || $result['messaggio'] == "")
                throw new Exception("Nessun riferimento trovato", 1);

            $_response = preg_replace('/\s/', '', $result['messaggio']);
            $_response = str_replace("api_genera_coupon_response:", "", $_response);

            $_decode = json_decode($_response);

            if (
                (is_object($_decode) && !isset($_decode->id_iscrizione))
                    || (is_array($_decode) && !isset($_decode['id_iscrizione']))
                )
                throw new Exception("Il riferimento ha un valore non valido", 1);


            $_ret['success'] = (is_object($_decode)) ? $_decode->id_iscrizione : $_decode['id_iscrizione'];

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        echo json_encode($_ret);
        $this->_japp->close();

    }

}
