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

class JFormFieldlistafieldxcsv extends JFormFieldList {

    /**
     * A flexible category list that respects access controls
     *
     * @var		string
     * @since	1.6
     */
    public $type = 'listafieldcsv';

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

        $options->no_column="nessun campo selezionato";
        //$options->text="CAMPO NON DISPONIBILE";

        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            $query->select('fields');
            $query->from('#__gg_report_users');
            $query->setLimit('1');

            // Get the options.
            $db->setQuery($query);
            $fields = (array)json_decode($db->loadResult());
            $fields=array_keys($fields);
            foreach ($fields as $field){
               $options->$field=$field;
            }


          //array_unshift($options,(object)['text'=>'nessun campo selezionato','value'=>'no_column']);


        }catch (Exception $e )
        {
            return $options;
        }

        // Merge any additional options in the XML definition.
        //$options = array_merge(parent::getOptions(), $options);
//var_dump($options);die;
        return $options;
    }

}
