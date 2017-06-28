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
class JFormFieldlistaformati extends JFormFieldList
{
   protected $type = 'listaformati';
   protected function getOptions() {
        // Initialise variables.
        $options = array();
        

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('id AS value,formato AS text' );
        $query->from('#__gg_formati');


        // Get the options.
        $db->setQuery($query);
FB::info((string)$query, "query listaformati ");
        $options = $db->loadObjectList();

        // Check for a database error.
        if ($db->getErrorNum()) {
            JError::raiseWarning(500, $db->getErrorMsg());
        }


        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);
        return $options;
    }
}

