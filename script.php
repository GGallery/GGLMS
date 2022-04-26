<?php
/**
 * Created by IntelliJ IDEA.
 * User: Francesca
 * Date: 02/02/2021
 * Time: 11:27
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class com_gglmsInstallerScript {

    /**
     * Runs right after any installation action is preformed on the component.
     *
     * @param  string    $type   - Type of PostFlight action. Possible values are:
     *                           - * install
     *                           - * update
     *                           - * discover_install
     * @param  \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    function postflight($type, $parent) {

        if (strtolower($type) == "install"
            || strtolower($type) == "update") {

            // cli files
            $temp_dir = $parent->getParent()->getPath('source');
            $cli_script_arr = array(
                                    "report_sync.php",
                                    "allinea_gg_log.php",
                                    "allinea_permanenza.php",
                                    "fix_ug_soci.php",
                                    "report_per_piattaforma.php",
                                    "load_corsi_from_xml.php",
                                    "fix_report_anagrafiche.php",
                                    "fix_report_anagrafiche_2.php",
                                    "import_users.php",
                                    );

            foreach ($cli_script_arr as $key_script => $cli_script) {
                JFile::move($temp_dir . '/extra/' . $cli_script, JPATH_SITE . '/cli/' . $cli_script);
            }

            // scorm folder
            $scorm_folder = "scorm";
            JFolder::move($temp_dir . '/' . $scorm_folder, $_SERVER['DOCUMENT_ROOT'] . '/' . $scorm_folder);
        }

    }

}
