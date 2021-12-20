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


class fixReportAnagrafiche extends JApplicationCli {

    public function doExecute() {

        try {

            // sessione interna al framework necessaria per accedere a database, parametri ecc
            jimport( 'joomla.user.user');
            jimport( 'joomla.session.session');
            jimport( 'joomla.user.authentication');

            $app = JFactory::getApplication('site');
            $app->initialise();

            require_once JPATH_COMPONENT . '/controllers/api.php';

            // start
            $this->out('Script general start at:' . date('H:i:s') . ' on ' . date('d/m/Y'));

            $api = new gglmsControllerApi();

            // servizi
            $this->out('Process servizi:' . date('H:i:s') . ' on ' . date('d/m/Y'));
            $arr_servizi = [421, 422, 423, 424, 425, 426, 427, 428, 429, 430, 431, 432, 433];
            $fix_coupon_adesioni = $api->fix_coupon_adesioni(231, $arr_servizi);
            $this->out('Script servizi ended with ' . $fix_coupon_adesioni . ' at:' . date('H:i:s') . ' on ' . date('d/m/Y'));

            // la farmacia
            $this->out('Process la farmacia:' . date('H:i:s') . ' on ' . date('d/m/Y'));
            $arr_farmacia = [434, 435, 436];
            $fix_coupon_adesioni = $api->fix_coupon_adesioni(297, $arr_farmacia);
            $this->out('Script la farmacia ended with ' . $fix_coupon_adesioni . ' at:' . date('H:i:s') . ' on ' . date('d/m/Y'));

            // end
            $this->out('Script general ended at:' . date('H:i:s') . ' on ' . date('d/m/Y'));
        }
        catch (Exception $e) {
            $this->out(date('d/m/Y H:i:s') . ' - ERRORE: ' . $e->getMessage());
        }

    }
}
JApplicationCli::getInstance('fixReportAnagrafiche')->execute();

