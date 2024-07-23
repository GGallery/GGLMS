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

class AllineaUsegroupUtenti extends JApplicationCli{
    public function doExecute(){
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

            $filename = JPATH_ROOT . "/tmp/allinea_usergroup_".time().".log";

            $older_files = JPATH_ROOT . "/tmp/allinea_usergroup_*";
            foreach (glob($older_files) as $filefound){
                if (time()-filemtime($filefound) > 604800) {
                    unlink($filefound);
                  }
            }

            $this->out('Script start at:' . date('H:i:s') . ' on ' . date('d/m/Y'));

            $api = new gglmsControllerApi();
            
            $host_string = (!is_null($db_host) && !is_null($db_port)) ? $db_host . ":" . $db_port : null;
            $allinea_utenti = $api->allinea_usergroups_farmacisti(
                                                            $host_string,
                                                            $db_user,
                                                            $db_password,
                                                            $db_database,
                                                            $db_prefix,
                                                            $db_driver,
                                                            $filename
                                                        );

            $this->out('Script ended with ' . $allinea_utenti . ' at:' . date('H:i:s') . ' on ' . date('d/m/Y'));
        } catch (Exception $e) {
            utilityHelper::log_to_file(__FUNCTION__,$e->getMessage(),$filename);
            $this->out(date('d/m/Y H:i:s') . ' - ERRORE: ' . $e->getMessage());
        }
    }
}



JApplicationCli::getInstance('AllineaUsegroupUtenti')->execute();