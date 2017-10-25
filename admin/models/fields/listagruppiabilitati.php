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

class JFormFieldlistagruppiabilitati extends JFormFieldList {

    /**
     * A flexible category list that respects access controls
     *
     * @var		string
     * @since	1.6
     */
    public $type = 'listagruppiabilitati';

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
        $options->value="0";
        $options->text="CAMPO NON DISPONIBILE";

        try {

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            $db->setQuery("SELECT  config_value FROM #__gg_configs WHERE config_key= 'id_gruppi_visibili'");
            $gruppiabilitati = $db->loadResult();

            $query->select('id as value, title as text');
            $query->from('#__usergroups as e');
            $query->where("e.id in($gruppiabilitati)");
            $query->order('id');

            // Get the options.
            $db->setQuery($query);

            $options = $db->loadObjectList();


        }catch (Exception $e )
        {
            return $options;
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

}
