<?php

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


class reportFarmacie extends JApplicationCli {

    public static $extDb;

    public function doExecute() {

        try {

            // sessione interna al framework necessaria per accedere a database, parametri ecc
            jimport( 'joomla.user.user');
            jimport( 'joomla.session.session');
            jimport( 'joomla.user.authentication');

            $app = JFactory::getApplication('site');
            $app->initialise();

            $db_host = $this->input->get('db_host', '127.0.0.1');
            $db_port = $this->input->get('db_port', 3306);
            $db_user = $this->input->get('db_user', null);
            $db_password = $this->input->get('db_password', null);
            $db_database = $this->input->get('db_database', null);
            $db_prefix = $this->input->get('db_prefix', null);
            $db_driver = $this->input->get('db_driver', null);

            require_once JPATH_COMPONENT . '/controllers/api.php';

            $this->out('Script start at:' . date('H:i:s') . ' on ' . date('d/m/Y'));

            $api = new gglmsControllerApi();
            $host_string = (!is_null($db_host) && !is_null($db_port)) ? $db_host . ":" . $db_port : null;
            $db_option['driver'] = $db_driver;
            $db_option['host'] = $db_host;
            $db_option['user'] = $db_user;
            $db_option['password'] = utilityHelper::encrypt_decrypt('decrypt', $db_password, "GGallery00!", "GGallery00!");
            $db_option['database'] = $db_database;
            $db_option['prefix'] = $db_prefix;

            $extDb = JDatabaseDriver::getInstance($db_option);

            $date = $api->check_report_requests_status($extDb);

            $update = $api->report_queue_update($date['id'],$extDb,"progress");
            if(isset($update['error'])) throw new Exception($update['error']);

            if(isset($date['error'])) throw new Exception($date['error']);

            $dal = $date['report_dal'];
            $al = $date['report_al'];

            DEBUGG::log('Generazione report dal '.$dal.' al '.$al,'', 0, 1, 1);

            $report_farmacie = $api->get_report_per_farmacie($dal,
                $al,
                $host_string,
                $db_user,
                $db_password,
                $db_database,
                $db_prefix,
                $db_driver
            );

            if(isset($report_farmacie['error']))throw new Exception($report_farmacie['error']);

            $sendMail = $api->sendReportMail($date['user_id'],
                $report_farmacie,
                $extDb
            );

            if(isset($sendMail['error'])) throw new Exception($sendMail['error']);

            $update = $api->report_queue_update($date['id'],$extDb,"completed");
            if(isset($update['error'])) throw new Exception($update['error']);
            DEBUGG::log('Generato il report:'.$report_farmacie,'', 0, 1, 1);

            $this->out('Script ended with ' . $sendMail . ' at:' . date('H:i:s') . ' on ' . date('d/m/Y'));

        }
        catch (Exception $e) {
            DEBUGG::log($e->getMessage(), 'check_report_request_status', 0, 1, 0);
            $update = $api->report_queue_update($date['id'],$extDb,"error");

            $this->out(date('d/m/Y H:i:s') . ' - ERRORE: ' . $e->getMessage());
        }

    }
}
JApplicationCli::getInstance('reportFarmacie')->execute();

