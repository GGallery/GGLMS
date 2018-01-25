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

class JFormFieldlistaalberounita extends JFormFieldList {

    /**
     * A flexible category list that respects access controls
     *
     * @var		string
     * @since	1.6
     */
    protected $type = 'listaalberounita';

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



        $options = $this->getUnitTree();

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

    protected function getUnitTree($item = 0) {
        $tree = array();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.id AS value, a.titolo AS text');
        $query->from('#__gg_unit AS a');
        $query->where("unitapadre=" . $item);

        $db->setQuery($query);

        // Check for a database error.
        if ($db->getErrorNum()) {
            JError::raiseWarning(500, $db->getErrorMsg());
        }

        $tmptree = $db->loadObjectList();
        foreach ($tmptree as $item) {
            array_push($tree, $item);
            foreach ($this->getUnitTree($item->value) as $item2) {
                $item2->text = "─" . $item2->text;
                array_push($tree, $item2);
            }
        }
        unset($tmptree);
        return $tree;
    }

}
