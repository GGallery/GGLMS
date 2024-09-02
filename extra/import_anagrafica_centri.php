<?php
/**
 * Created by IntelliJ IDEA.
 * User: Salma
 * Date: 19/10/2022
 * Time: 09:02
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


class importAnagraficaCentri extends JApplicationCli {

    public function doExecute() {

        try {

            // sessione interna al framework necessaria per accedere a database, parametri ecc
            jimport( 'joomla.user.user');
            jimport( 'joomla.session.session');
            jimport( 'joomla.user.authentication');

            $app = JFactory::getApplication('site');
            $app->initialise();

             $is_debug = $this->input->get('is_debug', false);
             $from_local = $this->input->get('from_local', '');

            require_once JPATH_COMPONENT . '/controllers/api.php';

            $this->out('Script start at:' . date('H:i:s') . ' on ' . date('d/m/Y'));

            $api = new gglmsControllerApi();
            $importa_anagrafica_centri = $api->importa_anagrafica_centri(  $is_debug, $from_local);

            $this->out('Script ended with ' . $importa_anagrafica_centri . ' at:' . date('H:i:s') . ' on ' . date('d/m/Y'));
        }
        catch (Exception $e) {
            $this->out(date('d/m/Y H:i:s') . ' - ERRORE: ' . $e->getMessage());
        }

    }
}
JApplicationCli::getInstance('importAnagraficaCentri')->execute();
