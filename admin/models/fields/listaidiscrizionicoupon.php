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

class JFormFieldlistaidiscrizionicoupon extends JFormFieldList
{

    /**
     * A flexible category list that respects access controls
     *
     * @var        string
     * @since    1.6
     */
    protected $type = 'listaidiscrizionicoupon';

    /**
     * Method to get a list of categories that respects access controls and can be used for
     * either category assignment or parent category assignment in edit screens.
     * Use the parent element to indicate that the field will be used for assigning parent categories.
     *
     * @return    array    The field option objects.
     * @since    1.6
     */
    protected function getOptions() {
        // Initialise variables.
        $options = array();

        try{
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('DISTINCT a.id_iscrizione AS value, a.id_iscrizione AS text, creation_time');
        $query->from('#__gg_coupon AS a');
        $query->order('creation_time desc');



        // Get the options.
        $db->setQuery($query);

        $options = $db->loadObjectList();

        }catch (Exception $e) {
            echo $exception->getMessage();
        }


        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }


}
