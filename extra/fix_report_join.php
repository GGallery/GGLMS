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

require_once JPATH_SITE . '/administrator/components/com_gglms/models/libs/debugg/debugg.php';
require_once JPATH_COMPONENT . '/helpers/output.php';
require_once JPATH_COMPONENT . '/helpers/utility.php';

class fixReportJoin extends JApplicationCli {

    public function doExecute()
    {
        $from = $this->input->get('from', null);

        try {

            // sessione interna al framework necessaria per accedere a database, parametri ecc
            jimport( 'joomla.user.user');
            jimport( 'joomla.session.session');
            jimport( 'joomla.user.authentication');

            $app = JFactory::getApplication('site');
            $app->initialise();

            // Database connector
            $db = JFactory::getDBO();

            require_once JPATH_COMPONENT . '/models/contenuto.php';
            require_once JPATH_COMPONENT . '/models/unita.php';
            require_once JPATH_COMPONENT . '/models/syncdatareport.php';
            require_once JPATH_COMPONENT . '/controllers/allineareport.php';
            require_once JPATH_COMPONENT . '/models/syncviewstatouser.php';

            $this->out(date('d/m/Y H:i:s') . ' - inizio esecuzione script');

            if (is_null($from)) {
                throw new \Exception('Nessuna data è stata specificata, impossibile eseguire lo script');
            }

            $query_sel = "SELECT DISTINCT c.id AS id_contenuto,q.c_student_id AS id_utente
                FROM #__quiz_r_student_quiz AS q
                INNER JOIN #__gg_contenuti AS c
                ON q.c_quiz_id = c.id_quizdeluxe
                WHERE DATE(q.c_date_time) = ". $db->quote($from);

            $db->setQuery($query_sel);
            $quizResults = $db->loadAssocList();

            $this->out('Query eseguita: ' . $query_sel);

            if (empty($quizResults))
                throw new \Exception('Nessun risultato valido per l\'elaborazione per la data ' . $from);

            $this->out('Ci sono ' . count($quizResults) . ' riferimenti ai quiz');

            $insertionCounter = 0;
            $userIds = [];

            foreach ($quizResults as $item) {

                $data = new Stdclass();
                $data->id_utente = $item['id_utente'];
                $data->id_contenuto = $item['id_contenuto'];
                if (!in_array($item['id_utente'], $userIds))
                    $userIds[] = $item['id_utente'];

                $modelcontenuto = new gglmsModelContenuto();
                $contenuto = $modelcontenuto->getContenuto($item['id_contenuto']);
                if ($contenuto == null) {
                    $this->out("Il contenuto corrente ID " . $item['id_contenuto'] . " utente " . $item['id_utente'] . " è NULL, continuo..");
                    continue;
                }

                $this->out("ID CONTENUTO: " . $item['id_contenuto']);

                $stato = $contenuto->getStato($data->id_utente);
                $data->data = $stato->data;

                $data->data_extra = $stato->data_extra;
                $data->data_primo_accesso = $stato->data_primo_accesso;

                $data->stato = $stato->completato;
                $data->visualizzazioni =  $stato->visualizzazioni;

                $permanenza_second = utilityHelper::getSecondsFromHMS($stato->permanenza);
                $data->permanenza_tot = isset($permanenza_second) ? $permanenza_second : 0;
                $data->id_unita = $contenuto->getUnitPadre();//se  questo fallisce non lo metto nel report

                $this->out("ID UNITA: " . $data->id_unita);

                if (!isset($data->id_unita)
                    || $data->id_unita == 0) {
                    $this->out("L'unità corrente ha ID 0 (contenuto ID " . $item['id_contenuto'] . " utente " . $item['id_utente'] . "), continuo...");
                    continue;
                }
                $modelunita = new gglmsModelUnita();
                $unita = $modelunita->getUnita($data->id_unita);

                $corso = $unita->find_corso($data->id_unita, false);
                if ($corso->pubblicato == 0) {
                    $this->out("Il corso ID " . $corso->id . " (contenuto ID " . $item['id_contenuto'] . " utente " . $item['id_utente'] . ") non è pubblicato, continuo...<br />");
                    continue;
                }

                $modelDataReport = new gglmsModelSyncdatareport();
                $data->id_corso = $corso->id;
                $data->id_event_booking = ($corso->id_event_booking) ? $corso->id_event_booking : 0;
                $data->id_anagrafica = $modelDataReport->_getAnagraficaid($data->id_utente, $data->id_event_booking);

                $this->out("ID CORSO: " . $data->id_corso);

                if($data->id_anagrafica == 0)
                    $this->out('Anagrafica dell\'utente ID ' . $data->id_utente . ' non presente');

                $modelDataReport->store_report($data);

                unset($modelunita);
                unset($unita);
                unset($data);

                unset($modelcontenuto);
                unset($contenuto);

                $insertionCounter++;

            }

            $this->out("Elaborati " . $insertionCounter . " record, allinea_vista_con_report");

            // parte relativa a gg_view_stato_user_corso
            $allineaReport = new gglmsControllerAllineaReport();
            $allineaReport->allinea_vista_con_report();

            $modelViewStatoUser = new gglmsModelSyncViewStatoUser();

            $rs = $modelViewStatoUser->insertData(null, null, $from, $userIds);
            if (!$rs) {
                $this->out("DT: " . $from . " insert views con esito false");
            }
            else {
                $this->out("DT: " . $from . " insert views con esito true");
            }
            
            $this->out(date('d/m/Y H:i:s') . " - Procedimento completato");


        }
        catch (Exception $e) {
            // if ($innodb == 1)
            //     $db->transactionRollback();

            $this->out(date('d/m/Y H:i:s') . ' - ERRORE: ' . $e->getMessage());
        }
    }
}
JApplicationCli::getInstance('fixReportJoin')->execute();
