<?php
/**
 * Created by IntelliJ IDEA.
 * User: Luca
 * Date: 18/02/2021
 * Time: 16:54
 */

// Set flag that this is a parent file.
define('_JEXEC', 1);

error_reporting(E_ALL | E_NOTICE);
ini_set('display_errors', 1);
define('JDEBUG', 1);

// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php'))
{
    require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES'))
{
    define('JPATH_BASE', dirname(__DIR__));
    require_once JPATH_BASE . '/includes/defines.php';
}

if (!defined('JPATH_COMPONENT')) {
    define('JPATH_COMPONENT', JPATH_SITE . '/components/com_gglms');
}

require_once JPATH_LIBRARIES . '/import.legacy.php';
require_once JPATH_LIBRARIES . '/cms.php';

// Load the configuration
require_once JPATH_CONFIGURATION . '/configuration.php';

class allineaGGLog extends JApplicationCli {

    public function doExecute()
    {
        try {

            // Database connector
            $db = JFactory::getDBO();

            $this->out(date('d/m/Y H:i:s') . ' - inizio esecuzione script');

            $scorm_filter_data = $this->input->get('scorm_filter_data', '');
            $scorm_exe_init = $this->input->get('scorm_exe_init', 0);

            // reset delle varibili caso mai fossere state abilitate
            $_tmp_completed_insert = array();
            $_rand_check = array();
            $_uniq = null;

            $_insert_log = "INSERT INTO #__gg_log (
                                                  id_utente,
                                                  id_contenuto,
                                                  data_accesso,
                                                  uniqid,
                                                  permanenza)
                                                  VALUES ";

            $db->setQuery('SET sql_mode=\'\'');
            $db->execute();

            // seleziono tutti i contenuti in stato init quindi iniziati di cui devo prendere scoid e user_id
            // per capire quali sono completati

            if ($scorm_exe_init == 1) {

                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gg_scormvars');

                if ($scorm_filter_data != '')
                    $query = $query->where('DATE_FORMAT(timestamp, "%Y-%m-%d") < ' . $db->quote($scorm_filter_data));

                $query = $query->where("varName = 'cmi.core.lesson_status'")
                    ->where("varValue = 'init'")
                    ->order("timestamp, scoid, userid");

                $db->setQuery($query);
                $results_iniziati = $db->loadAssocList();

                $counter_init = 0;
                if (count($results_iniziati) > 0) {

                    foreach ($results_iniziati as $k_iniziati => $iniziato) {

                        // controllo anche se è già in gg_log
                        // nel caso la precedenza è per il dato esistente
                        $query_log = $db->getQuery(true)
                            ->select('*')
                            ->from('#__gg_log')
                            ->where('id_utente = ' . $db->quote($iniziato['userid']))
                            ->where('id_contenuto = ' . $db->quote($iniziato['scoid']));

                        $db->setQuery($query_log);
                        $_row_results_log = $db->loadAssocList();
                        if (count($_row_results_log) > 0) {
                            $this->out(date('d/m/Y H:i:s') . " content init in gg_log -> 
                                                                                id_utente " . $iniziato['userid'] . "
                                                                                id_contenuto " . $iniziato['scoid']);
                            continue;
                        }

                        $query = $db->getQuery(true)
                            ->select('*')
                            ->from('#__gg_scormvars')
                            ->where("varName = 'cmi.core.lesson_status'")
                            ->where("varValue != 'init'")
                            ->where('scoid = ' . $db->quote($iniziato['scoid']))
                            ->where('userid = ' . $db->quote($iniziato['userid']));

                        $db->setQuery($query);
                        $_row_results = $db->loadAssocList();

                        // se è completato non lo inserisco
                        if (count($_row_results) > 0) {
                            $this->out(date('d/m/Y H:i:s') . " content init completed -> 
                                                                                id_utente " . $iniziato['userid'] . "
                                                                                id_contenuto " . $iniziato['scoid']);
                            continue;
                        }

                        while (1) {
                            $_uniq = mt_rand(10000000, 99999999);
                            if (!in_array($_uniq, $_rand_check))
                                break;
                        }

                        $_rand_check[] = $_uniq;
                        $_permanenza = rand(5, 10);

                        $_tmp_completed_insert[] = "(
                                                        " . $db->quote($iniziato['userid']) . ",
                                                        " . $db->quote($iniziato['scoid']) . ",
                                                        " . $db->quote($iniziato['timestamp']) . ",
                                                        " . $db->quote($_uniq) . ",
                                                        " . $db->quote($_permanenza) . "
                                                    ), ";


                        $counter_init++;
                        $this->out(date('d/m/Y H:i:s') . " content init riga -> " . $counter_init);

                    }

                    // ci sono righe da inserire in gg_log
                    if (count($_tmp_completed_insert) > 0) {

                        $db->transactionStart();

                        $_arr_query_chunked = array_chunk($_tmp_completed_insert, 500);

                        foreach ($_arr_query_chunked as $key => $sub_query) {

                            $_executed_query = "";

                            foreach ($sub_query as $kk => $vv) {

                                $_executed_query .= $vv;

                            }

                            $_executed_query = (substr(trim($_executed_query), -1) == ",") ? substr(trim($_executed_query), 0, -1) : $_executed_query;
                            $_row_query = $_insert_log . $_executed_query . ";";

                            $db->setQuery($_row_query);
                            $insert_result = $db->execute();

                            if (!$insert_result)
                                throw new Exception("Inserimento non andato a buon fine: " . $_row_query, 1);

                        }

                        $db->transactionCommit();

                    }

                }

            } // insert init

            // reset delle varibili caso mai fossere state abilitate
            $_tmp_completed_insert = array();

            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gg_scormvars');

            // inserimento dei completati
            if ($scorm_filter_data != '')
                $query = $query->where('DATE_FORMAT(timestamp, "%Y-%m-%d") < ' . $db->quote($scorm_filter_data));

            $query = $query->where("varName = 'cmi.core.lesson_status'")
                            ->where("varValue != 'init'")
                            ->order("timestamp, scoid, userid");

            $db->setQuery($query);
            $results_completati = $db->loadAssocList();

            // controllo se degli init sono stati completati
            if (count($results_completati) == 0)
                throw new Exception("Nessun contenuto scorm da migrare", 1);

            $counter_completed = 0;
            foreach ($results_completati as $k_completato => $completato) {

                if ($completato['varValue'] != 'completed') {
                    $this->out(date('d/m/Y H:i:s') . " completed not completed -> 
                                                                                id_utente " . $completato['userid'] . "
                                                                                id_contenuto " . $completato['scoid']);
                    continue;
                }

                // controllo anche se è già in gg_log
                // nel caso la precedenza è per il dato esistente
                $query_log = $db->getQuery(true)
                                        ->select('*')
                                        ->from('#__gg_log')
                                        ->where('id_utente = ' . $db->quote($completato['userid']))
                                        ->where('id_contenuto = ' . $db->quote($completato['scoid']));

                $db->setQuery($query_log);
                $_row_results_log = $db->loadAssocList();
                if (count($_row_results_log) > 0) {
                    $this->out(date('d/m/Y H:i:s') . " completed in gg_log -> 
                                                                                id_utente " . $completato['userid'] . "
                                                                                id_contenuto " . $completato['scoid']);
                    continue;
                }

                // prendo scoid e userid per controllare se esiste il riferimento al total_time
                $_select_complete = $db->getQuery(true)
                    ->select('varValue as total_time')
                    ->from('#__gg_scormvars')
                    ->where('scoid = ' . $db->quote($completato['scoid']))
                    ->where('userid = ' . $db->quote($completato['userid']));
                $db->setQuery($_select_complete);
                $_permanenza = $db->loadResult();

                // non mi ha trovato il valore total_time, lo prendo allora a da un altro contenuto possibilmente valorizzato
                if (is_null($_permanenza)
                    || $_permanenza == ""
                    || $_permanenza == 0) {

                    $this->out(date('d/m/Y H:i:s') . " completed nessun total_time -> 
                                                                                id_utente " . $completato['userid'] . "
                                                                                id_contenuto " . $completato['scoid']);

                    $_select_complete_extra = $db->getQuery(true)
                        ->select('varValue as total_time')
                        ->from('#__gg_scormvars')
                        ->where('scoid = ' . $db->quote($completato['scoid']))
                        ->where("varName = 'cmi.core.total_time'")
                        ->where('varValue > 0')
                        ->group('scoid')
                        ->order('varValue DESC');

                    $db->setQuery($_select_complete_extra);
                    $_permanenza_extra = $db->loadResult();
                    $_permanenza = $_permanenza_extra;

                }

                // controllo nuovamente il valore del total_time
                if (is_null($_permanenza)
                    || $_permanenza == "") {
                    $this->out(date('d/m/Y H:i:s') . " completed total_time forzato a valore di default -> 
                                                                                id_utente " . $completato['userid'] . "
                                                                                id_contenuto " . $completato['scoid']);
                    $_permanenza = rand(1000, 1111);
                }

                while (1) {
                    $_uniq = mt_rand(10000000, 99999999);
                    if (!in_array($_uniq, $_rand_check))
                        break;
                }

                $_rand_check[] = $_uniq;
                $_tmp_completed_insert[] = "(
                                                        " . $db->quote($completato['userid']) . ",
                                                        " . $db->quote($completato['scoid']) . ",
                                                        " . $db->quote($completato['timestamp']) . ",
                                                        " . $db->quote($_uniq) . ",
                                                        " . $db->quote($_permanenza) . "
                                                    ), ";

                $counter_completed++;
                $this->out(date('d/m/Y H:i:s') . " content completed riga -> " . $counter_completed);

            }

            if (count($_tmp_completed_insert) > 0) {

                $db->transactionStart();

                $_arr_query_chunked = array_chunk($_tmp_completed_insert, 500);

                foreach ($_arr_query_chunked as $key => $sub_query) {

                    $_executed_query = "";

                    foreach ($sub_query as $kk => $vv) {

                        $_executed_query .= $vv;

                    }

                    $_executed_query = (substr(trim($_executed_query), -1) == ",") ? substr(trim($_executed_query), 0, -1) : $_executed_query;
                    $_row_query = $_insert_log . $_executed_query . ";";

                    $db->setQuery($_row_query);
                    $insert_result = $db->execute();

                    if (!$insert_result)
                        throw new Exception("Inserimento non andato a buon fine: " . $_row_query, 1);

                }

                $db->transactionCommit();

            }

            $this->out(date('d/m/Y H:i:s') . " - Procedimento completato -> init: " . $counter_init . " completed: " . $counter_completed);


        }
        catch (Exception $e) {
            $this->out(date('d/m/Y H:i:s') . ' - ERRORE: ' . $e->getMessage());
        }
    }
}
JApplicationCli::getInstance('allineaGGLog')->execute();
