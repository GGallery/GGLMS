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

class allineaPermanenza extends JApplicationCli {

    public function doExecute()
    {
        try {

            // Database connector
            $db = JFactory::getDBO();

            $con_report = $this->input->get('con_report', 0);
            $this->out(date('d/m/Y H:i:s') . ' - inizio esecuzione script');

            //$db->setQuery('SET sql_mode=\'\'');
            //$db->execute();

            $_rand_check = array();
            $_uniq = null;

            $query = "SELECT (r.permanenza_tot*1) AS log_tempo,
                            subq.varValue AS scorm_tempo,
                            ABS(subq.varValue-r.permanenza_tot) AS diff_tempo,
                            subq.scoid AS scorm_id_contenuto,
                            subq.userid AS scorm_user,
                            subq.timestamp AS scorm_timestamp,
                            (CASE 
                                WHEN subq.scoid != r.id_contenuto THEN 'diverso'
                                ELSE 'uguale'
                            END) as tp_contenuto,
                            (CASE
                                WHEN subq.userid != r.id_utente THEN 'diverso'
                                ELSE 'uguale'
                                END) as tp_user
                            FROM #__gg_report r
                            JOIN #__gg_contenuti c ON r.id_contenuto = c.id
                            JOIN (SELECT scoid, userid, varValue, timestamp
                                    FROM #__gg_scormvars
                                    WHERE varName = 'cmi.core.total_time') subq ON r.id_contenuto = subq.scoid AND r.id_utente = subq.userid
                    WHERE c.tipologia != 7
                    AND r.stato = 1
                    AND r.permanenza_tot < subq.varValue
                    AND r.id_utente > 0
                    ORDER BY r.permanenza_tot, diff_tempo";

            $db->setQuery($query, 0, 10);
            $results_contenuti = $db->loadAssocList();

            if (count($results_contenuti) == 0)
                throw new Exception("Nessun contenuto da elaborare", E_USER_ERROR);

            $db->transactionStart();
            foreach ($results_contenuti as $key_c => $iniziato) {

                // contenuto diverso fra gg_report e gg_scormvars
                if ($iniziato['tp_contenuto'] != 'uguale') {
                    $this->out(date('d/m/Y H:i:s') . ' - contenuto diverso fra gg_report e gg_scormvars');
                    continue;
                }

                // id_utente diverso fra gg_report e gg_scormvars
                if ($iniziato['tp_user'] != 'uguale') {
                    $this->out(date('d/m/Y H:i:s') . ' - id_utente diverso fra gg_report e gg_scormvars');
                    continue;
                }


                $gg_log_query = "";
                $gg_report_query = "";

                // se log_tempo = 0 con molta probabilità non è presente in gg_log
                // controllo anche se è già in gg_log
                // nel caso la precedenza è per il dato esistente
                $query_log = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gg_log')
                    ->where('id_utente = ' . $db->quote($iniziato['scorm_user']))
                    ->where('id_contenuto = ' . $db->quote($iniziato['scorm_id_contenuto']));

                $db->setQuery($query_log);
                $_row_results_log = $db->loadAssocList();
                // non esiste devo inserire
                if (count($_row_results_log) == 0) {

                    $this->out(date('d/m/Y H:i:s') . ' - record in gg_log non esistente - inserimento');

                    while (1) {
                        $_uniq = mt_rand(10000000, 99999999);
                        if (!in_array($_uniq, $_rand_check))
                            break;
                    }
                    $_rand_check[] = $_uniq;

                    $gg_log_query = "INSERT INTO #__gg_log (
                                                            id_utente,
                                                            id_contenuto,
                                                            data_accesso,
                                                            uniqid,
                                                            permanenza
                                                            )
                                                            VALUES (
                                                            " . $db->quote($iniziato['scorm_user']) . ",
                                                            " . $db->quote($iniziato['scorm_id_contenuto']) . ",
                                                            " . $db->quote($iniziato['scorm_timestamp']) . ",
                                                            " . $db->quote($_uniq) . ",
                                                            " . $db->quote($iniziato['scorm_tempo']) . "
                                                            )";
                }
                // esiste prendo l'ultima riga di gg_log per data_accesso
                else {

                    $this->out(date('d/m/Y H:i:s') . ' - record in gg_log esistente - aggiornamento');

                    $gg_log_query = "UPDATE #__gg_log
                                        SET permanenza = (permanenza + " . $iniziato['diff_tempo'] . ")
                                        WHERE id_utente = " . $db->quote($iniziato['scorm_user']) . "
                                        AND id_contenuto = " . $db->quote($iniziato['scorm_id_contenuto']) . "
                                        ORDER BY data_accesso
                                        LIMIT 1";

                }

                // esecuzione query gg_log
                $db->setQuery($gg_log_query);
                $gg_log_result = $db->execute();

                if (!$gg_log_result)
                    throw new Exception("Inserimento gg_log non andato a buon fine: " . $gg_log_query, 1);

                if ($con_report == 1) {
                    // aggiorno gg_report
                    $gg_report_query = "UPDATE #__gg_report
                                        SET permanenza_tot = (permanenza_tot + " . $iniziato['diff_tempo'] . ")
                                        WHERE id_utente = " . $db->quote($iniziato['scorm_user']) . "
                                        AND id_contenuto = " . $db->quote($iniziato['scorm_id_contenuto']) . "
                                        ORDER BY timestamp
                                        LIMIT 1";

                    // esecuzione query gg_report
                    $db->setQuery($gg_report_query);
                    $gg_report_result = $db->execute();

                    if (!$gg_report_result)
                        throw new Exception("Inserimento gg_report non andato a buon fine: " . $gg_report_query, 1);

                }

            }
            $db->transactionCommit();

            $this->out(date('d/m/Y H:i:s') . " - Procedimento completato");


        }
        catch (Exception $e) {
            $db->transactionRollback();
            $this->out(date('d/m/Y H:i:s') . ' - ERRORE: ' . $e->getMessage());
        }
    }
}
JApplicationCli::getInstance('allineaPermanenza')->execute();
