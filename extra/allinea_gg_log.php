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

            $query = $db->getQuery(true);
            $subq = $db->getQuery(true);

            $subq = $subq->select('scoid, userid, varValue, timestamp')
                ->from('#__gg_scormvars')
                ->where('varName = \'cmi.core.total_time\'');

            $query = $query->select('r.permanenza_tot AS log_tempo,
                                    subq.varValue AS scorm_tempo,
                                    r.id_contenuto AS log_id_contenuto,
                                    subq.scoid AS scorm_id_contenuto,
                                    r.id_utente AS log_user,
                                    subq.userid AS scorm_user,
                                    subq.timestamp AS scorm_timestamp')
                ->from('#__gg_report r')
                ->join('inner', '#__gg_contenuti c ON r.id_contenuto = c.id')
                ->join('inner', '(' . $subq . ') AS subq ON r.id_contenuto = subq.scoid AND r.id_utente = subq.userid')
                ->where('c.tipologia != 7')
                ->where('r.stato = 1')
                ->where('r.permanenza_tot < subq.varValue')
                ->where('r.id_utente > 0')
                ->order('r.permanenza_tot');

            $db->setQuery($query, 0, 5);
            $results = $db->loadAssocList();
            $rs_num_rows = count($results);

            if ($rs_num_rows == 0)
                throw new Exception("Nessuna contenuto appare disallineato fra gg_log e gg_scormvars", 1);

            $_arr_query = array();
            $counter = 0;

            foreach ($results as $key => $sub) {

                // controllo se i contenuti sono congruenti fra le due tabelle
                if ($sub['log_user'] != $sub['scorm_user']
                    || $sub['log_id_contenuto'] != $sub['scorm_id_contenuto'])
                    continue;

                $db->setQuery('SET sql_mode=\'\'');
                $db->execute();

                // va controllato se in gg_log esiste una corrispondenza
                /*
                 * SELECT id AS id_log, id_contenuto, id_utente, SUM(permanenza) AS permanenza_tot
                 * FROM #__gg_log
                 * WHERE id_contenuto = $sub['log_id_contenuto'] AND id_utente = $sub['log_user']
                 * GROUP BY id_contenuto, id_utente
                   ORDER BY data_accesso DESC

                - se esiste un riferimento prendo l'ultima aggiungo la differenza fra permanenza_tot e scorm_tempo
                UPDATE #__gg_log SET permanenza = ($sub['scorm_tempo'] - permanenza_tot) WHERE id = id_log
                - se non esiste devo inserire la referenza in gg_log
                INSERT INTO #__gg_log (id_utente, id_contenuto, data_accesso, uniq, permanenza)
                                VALUES ($sub['log_user'], $sub['log_id_contenuto'], $sub['scorm_timestamp'], rand(), $sub['scorm_tempo'])
                */
            }

            /*
            $mask_insert = "INSERT INTO #__gg_log (id, permanenza) 
                              VALUES ";

            foreach ($results as $key => $sub) {

                // controllo se i contenuti sono congruenti fra le due tabelle
                if ($sub['log_user'] != $sub['scorm_user']
                    || $sub['log_id_contenuto'] != $sub['scorm_id_contenuto']
                    || $sub['scorm_value'] != 'completed')
                    continue;

                $_insert = "(
                                '" . $sub['log_id'] . "',
                                '" . $sub['scorm_tempo'] . "'
                            ), ";

                $_arr_query[] = $_insert;
            }

            $_arr_query_chunked = array_chunk($_arr_query, 500);

            foreach ($_arr_query_chunked as $key => $sub_query) {

                $db->transactionStart();

                $_executed_query = "";

                foreach ($sub_query as $kk => $vv) {

                    $_executed_query .= $vv;
                    $counter++;

                }

                $_executed_query = (substr(trim($_executed_query), -1) == ",") ? substr(trim($_executed_query), 0, -1) : $_executed_query;
                $_row_query = $mask_insert . $_executed_query . " ON DUPLICATE KEY UPDATE permanenza = VALUES(permanenza);";

                $db->setQuery($_row_query);
                $_row_rs = $db->execute();

                if (!$_row_rs)
                    throw new Exception("Si è verifica un problema durante l'aggiornamento dei valori di gg_log. Il procedimento verrà arrestato");

                $db->transactionCommit();

            }
            */

            $this->out(date('d/m/Y H:i:s') . " - Procedimento completato sono stati aggiornati " . $counter . " su " . $rs_num_rows . " totali");


        }
        catch (Exception $e) {
            $this->out(date('d/m/Y H:i:s') . ' - ERRORE: ' . $e->getMessage());
        }
    }
}
JApplicationCli::getInstance('allineaGGLog')->execute();
