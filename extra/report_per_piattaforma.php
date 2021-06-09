<?php
/**
 * Created by IntelliJ IDEA.
 * User: Francesca
 * Date: 01/02/2021
 * Time: 15:39
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


class reportPerPiattaforma extends JApplicationCli {

    public function doExecute() {

        try {

            // sessione interna al framework necessaria per accedere a database, parametri ecc
            jimport( 'joomla.user.user');
            jimport( 'joomla.session.session');
            jimport( 'joomla.user.authentication');

            // Database connector
            $db = JFactory::getDBO();

            $app = JFactory::getApplication('site');
            $app->initialise();

            $id_piattaforma = $this->input->get('id_piattaforma', '');
            $tipologia_svolgimento = $this->input->get('tipologia_svolgimento', 6);

            require_once JPATH_COMPONENT . '/controllers/api.php';

            $this->out('Script start at:' . date('H:i:s') . ' on ' . date('d/m/Y'));

            $api = new gglmsControllerApi();
            $report_giornaliero_rs = $api->get_completed_report_per_piattaforma($id_piattaforma, $tipologia_svolgimento);

            $this->out('Script ended with ' . $report_giornaliero_rs . ' at:' . date('H:i:s') . ' on ' . date('d/m/Y'));
        }
        catch (Exception $e) {
            $this->out(date('d/m/Y H:i:s') . ' - ERRORE: ' . $e->getMessage());
        }

    }
}
JApplicationCli::getInstance('reportPerPiattaforma')->execute();

