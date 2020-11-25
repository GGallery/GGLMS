<?php

/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.form.helper');

JFormHelper::loadFieldClass('list');

class JFormFieldlistafieldxlang extends JFormFieldList {

    /**
     * A flexible category list that respects access controls
     *
     * @var		string
     * @since	1.6
     */
    public $type = 'listafieldxlang';

    /**
     * Method to get a list of categories that respects access controls and can be used for
     * either category assignment or parent category assignment in edit screens.
     * Use the parent element to indicate that the field will be used for assigning parent categories.
     *
     * @return	array	The field option objects.
     * @since	1.6
     */
    protected function getOptions() {
        // Initialise variables.

        $options = new stdClass();
        $options->value="";
        $options->text="DEFAULT";
        $array_options[] = $options;

        try {

            // localizzazione
            $lang = JFactory::getLanguage();
            $f = JPATH_ROOT . '\components\com_gglms\language\\' . $lang->getTag() . '\\' . $lang->getTag() . '.com_gglms.ini';
            if (!file_exists($f)) {
                // se il file non esiste provo anche sostituendo le \ con /
                $f = str_replace('\\', '/', $f);

                // proprio non esiste
                if (!file_exists($f)) {

                    echo <<<HTML
                <p style="color: red;">File {$f} non esistente!</p>
HTML;
                    throw new Exception("Dictionary file not found!", 1);
                }
            }

            $content = explode("\n", file_get_contents($f));
            $content = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\r\n", $content);

            foreach ($content as $rows => $row) {

                $row = trim($row);
                if ($row == ""
                    || empty($row))
                    continue;

                $ex_row = explode("=", $row);

                $ex_options = new stdClass();
                $ex_options->value = $ex_row[0];
                $ex_options->text = $ex_row[0];
                $array_options[] = $ex_options;
            }
        }
        catch (Exception $e) {
            return $array_options;
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $array_options);
        
        return $options;
    }

}
