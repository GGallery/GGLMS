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
            $cli_script_1 = "report_sync.php";
            $cli_script_2 = "allinea_gg_log.php";
            $cli_script_3 = "allinea_permanenza.php";
            $cli_script_4 = "fix_ug_soci.php";
            $cli_script_5 = "report_per_piattaforma.php";
            $cli_script_6 = "load_corsi_from_xml.php";

            JFile::move($temp_dir . '/extra/' . $cli_script_1, JPATH_SITE . '/cli/' . $cli_script_1);
            JFile::move($temp_dir . '/extra/' . $cli_script_2, JPATH_SITE . '/cli/' . $cli_script_2);
            JFile::move($temp_dir . '/extra/' . $cli_script_3, JPATH_SITE . '/cli/' . $cli_script_3);
            JFile::move($temp_dir . '/extra/' . $cli_script_4, JPATH_SITE . '/cli/' . $cli_script_4);
            JFile::move($temp_dir . '/extra/' . $cli_script_5, JPATH_SITE . '/cli/' . $cli_script_5);
            JFile::move($temp_dir . '/extra/' . $cli_script_6, JPATH_SITE . '/cli/' . $cli_script_6);

            // scorm folder
            $scorm_folder = "scorm";
            JFolder::move($temp_dir . '/' . $scorm_folder, $_SERVER['DOCUMENT_ROOT'] . '/' . $scorm_folder);
        }

    }

}
