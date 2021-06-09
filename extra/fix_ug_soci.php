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


class fixUgSoci extends JApplicationCli {

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

            $_params = utilityHelper::get_params_from_plugin();
            $gruppi_online = utilityHelper::get_ug_from_object($_params, "ug_online");
            $gruppi_moroso = utilityHelper::get_ug_from_object($_params, "ug_moroso");
            $gruppi_decaduto = utilityHelper::get_ug_from_object($_params, "ug_decaduto");
            $ug_categoria = utilityHelper::get_ug_from_object($_params, "ug_categoria");
            $ug_default = utilityHelper::get_ug_from_object($_params, "ug_default");
            $ug_extra = utilityHelper::get_ug_from_object($_params, "ug_extra");
            $ug_nonsocio = utilityHelper::get_ug_from_object($_params, "ug_nonsocio");
            $anno_quota_decaduto  = utilityHelper::get_params_from_object($_params, 'anno_quota_decaduto');
            $anno_ref = $this->input->get('anno_ref', date('Y')-1);
            $_ids_excluded = array();
            require_once JPATH_COMPONENT . '/models/config.php';

            $this->out('Script start at:' . date('H:i:s') . ' on ' . date('d/m/Y'));

            $query = $db->getQuery(true)
                ->select('jc.user_id, jc.cb_ultimoannoinregola')
                ->from('#__comprofiler jc')
                ->where("jc.cb_ultimoannoinregola <= " . $db->quote($anno_ref) . "
			                    OR jc.cb_ultimoannoinregola IS NULL
			                    OR jc.cb_ultimoannoinregola = ''");

            $db->setQuery($query);
            $results = $db->loadAssocList();
            if (count($results) == 0)
                throw new Exception("Nessun risultato da elaborare", E_USER_ERROR);

            $exclude_query = $db->getQuery(true)
                ->select('user_id')
                ->from('#__user_usergroup_map')
                ->where('group_id = ' . $db->quote($ug_nonsocio))
                ->group('user_id');
            $db->setQuery($exclude_query);
            $results_exclude = $db->loadAssocList();

            if (!is_null($results_exclude)
                && count($results_exclude) > 0) {

                foreach ($results_exclude as $key_ex => $user_ex) {
                    $_ids_excluded[] = $user_ex['user_id'];
                }

            }

            $counter = 0;
            foreach ($results as $user_key => $user) {

                $user_id = $results[$user_key]['user_id'];
                $cb_ultimoannoinregola = $results[$user_key]['cb_ultimoannoinregola'];
                $_diff = ($anno_ref-$cb_ultimoannoinregola);
                $_semaforo = "";

                if (in_array($user_id, $_ids_excluded)) {
                    $this->out('User solo_eventi excluded: ' . $user_id . ' at:' . date('H:i:s') . ' on ' . date('d/m/Y'));
                    continue;
                }

                $_user_quote = $this->getModelSite("com_gglms", 'users', 'gglmsModel');
                $_user_details = $_user_quote->get_user_details_cb($user_id);
                if (!is_array($_user_details)) {
                    $this->out('User details empty: ' . $user_id . ' at:' . date('H:i:s') . ' on ' . date('d/m/Y'));
                    continue;
                }

                // inserimento dell'utente nella categoria socio di competenza
                $_ins_categoria = utilityHelper::set_usergroup_categorie($user_id, $ug_categoria, $ug_default, $ug_extra, $_user_details, true);
                if (!is_array($_ins_categoria)) {
                    $this->out('User inserimento in categoria fallito: ' . $user_id . ' -> ' . $_ins_categoria . ' at: ' . date('H:i:s') . ' on ' . date('d/m/Y'));
                    continue;
                }

                $this->out('Elaborato user_id: ' . $user_id . ' inserito in categoria at: ' . date('H:i:s') . ' on ' . date('d/m/Y'));

                if ($_diff >= $anno_quota_decaduto) {
                    $_check = utilityHelper::set_usergroup_decaduto($user_id, $gruppi_online, $gruppi_moroso, $gruppi_decaduto);
                    $_semaforo = "decaduto";
                }
                else {
                    $_check = utilityHelper::set_usergroup_moroso($user_id, $gruppi_online, $gruppi_moroso, $gruppi_decaduto);
                    $_semaforo = "moroso";
                }

                if (!is_array($_check))
                    throw new Exception($_check, E_USER_ERROR);

                $counter++;
                $this->out('Elaborato user_id: ' . $user_id . ' -> ' . $_semaforo . ' - ' . date('H:i:s') . ' on ' . date('d/m/Y'));
            }

            $this->out('Script ended at:' . date('H:i:s') . ' on ' . date('d/m/Y') . ' -> tot. elaborati: ' . $counter);
        }
        catch (Exception $e) {
            $this->out(date('d/m/Y H:i:s') . ' - ERRORE: ' . $e->getMessage());
        }

    }

    private function getModelSite($component, $name = 'Custom', $prefix = 'CustomModel')
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
JApplicationCli::getInstance('fixUgSoci')->execute();

