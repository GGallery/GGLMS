<?php

/**
 * @package        Joomla.Tutorials
 * @subpackage    Component
 * @copyright    Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license        License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.form.helper');

JFormHelper::loadFieldClass('list');

class JFormFieldlistapiattaformeabilitate extends JFormFieldList
{

    /**
     * A flexible category list that respects access controls
     *
     * @var        string
     * @since    1.6
     */
    public $type = 'listapiattaformeabilitate';

    /**
     * Method to get a list of categories that respects access controls and can be used for
     * either category assignment or parent category assignment in edit screens.
     * Use the parent element to indicate that the field will be used for assigning parent categories.
     *
     * @return    array    The field option objects.
     * @since    1.6
     */
    protected function getOptions()
    {
        $options = array();


        try {

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            $query->select('p.group_id as value, p.alias as text');
            $query->from('#__usergroups_details AS p');
            $query->order("p.group_id");


            $db->setQuery($query);

            $options = $db->loadObjectList();


        } catch (Exception $e) {
            return $options;
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

}
