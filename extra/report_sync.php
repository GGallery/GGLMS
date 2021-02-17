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


class reportSync extends JApplicationCli {

    public function doExecute() {

        try {

            // sessione interna al framework necessaria per accedere a database, parametri ecc
            jimport( 'joomla.user.user');
            jimport( 'joomla.session.session');
            jimport( 'joomla.user.authentication');

            $app = JFactory::getApplication('site');
            $app->initialise();

            $params = JComponentHelper::getParams('com_gglms');
            $data_sync = $params->get('data_sync');
            $colonna_datetime = $params->get('colonna_datetime');
            $integrazione = $params->get('integrazione');
            $campo_nome = $params->get('campo_community_builder_nome');
            $campo_cognome = $params->get('campo_community_builder_cognome');
            if ($integrazione != "cb") {
                $campo_nome = $params->get('campo_event_booking_nome');
                $campo_cognome = $params->get('campo_event_booking_cognome');
            }


            require_once JPATH_COMPONENT . '/controllers/report.php';
            require_once JPATH_COMPONENT . '/controllers/allineareport.php';

            $this->out('Script start at:' . date('H:i:s') . ' on ' . date('d/m/Y'));
            $report = new gglmsControllerReport();

            // task report.sync
            $this->out('task report.sync');
            $syncdatareport = $this->getModelSite("com_gglms", 'syncdatareport', 'gglmsModel');

            $result_1 = $syncdatareport->sync_report_users($data_sync, $integrazione, $campo_nome, $campo_cognome);

            if (!$result_1)
                throw new Exception("Report ko sync_report_users", 1);

            $result_2 = $syncdatareport->sync_report_count($data_sync);
            if ($result_2 == -1)
                throw new Exception("Report ko sync_report_count", 1);

            $result_3 = $syncdatareport->sync_report(null, null, $data_sync, $colonna_datetime);

            if (!$result_3)
                throw new Exception("Report ko sync_report", 1);

            $result_4 = $syncdatareport->sync_report_complete();

            if (!$result_4)
                throw new Exception("Report ko sync_report_complete", 1);

            $result_5 = $syncdatareport->updateconfig();
            if (!$result_5)
                throw new Exception("Report ko updateconfig", 1);

            $syncviewstatouser = $this->getModelSite("com_gglms", 'syncviewstatouser', 'gglmsModel');
            $result_6 = $syncviewstatouser->syncViewStatoUser(null, null, null, 'task');

            if (!$result_6)
                throw new Exception("Report ko syncViewStatoUser", 1);

            $allinea_controller = new gglmsControllerAllineaReport();

            // task allineareport.allinea
            $this->out('task allineareport.allinea');
            $allinea_controller->allinea();

            $this->out('task allineareport.insert_vista_con_report');
            // task allineareport.insert_vista_con_report
            $allinea_controller->insert_vista_con_report();

            $this->out('Script ended at:' . date('H:i:s') . ' on ' . date('d/m/Y'));
        }
        catch (Exception $e) {
            $this->out('ERRORE: ' . $e->getMessage());
        }

    }

    public function getModelSite($component, $name = 'Custom', $prefix = 'CustomModel')
    {
        if (!isset($component)) {
            JFactory::getApplication()->enqueueMessage(JText::_("COM_ERROR_MSG"), 'error');
            return false;
        }
        $path = JPATH_SITE . '/components/' . $component . '/models/';
        JModelLegacy::addIncludePath($path);
        require_once $path . strtolower($name) . '.php';
        $model = JModelLegacy::getInstance($name, $prefix);

        // If the model is not loaded then $model will get false
        if ($model == false) {
            $class = $prefix . $name;
            // initilize the model
            new $class();
            $model = JModelLegacy::getInstance($name, $prefix);
        }
        return $model;
    }

}
JApplicationCli::getInstance('reportSync')->execute();

