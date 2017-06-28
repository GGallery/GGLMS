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

class JFormFieldcategorie extends JFormFieldList {

    protected $type = 'categorie';

    public function getOptions() {
        // Initialize variables.
        $options = array();

        $db     = JFactory::getDbo();
        $query  = $db->getQuery(true);

        $query->select('id As value, titolo As text');
        $query->from('#__gg_unit AS a');
        $query->order('a.titolo');


        FB::log("Recupero le categorie");

        // Get the options.
        $db->setQuery($query);

        $options = $db->loadObjectList();

        // Check for a database error.
        if ($db->getErrorNum()) {
            JError::raiseWarning(500, $db->getErrorMsg());
        }

        return $options;
    }

}
