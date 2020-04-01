<?php

/**
 * @package        Joomla.Tutorials
 * @subpackage    Component
 * @copyright    Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license        License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
require_once 'libs/getid3/getid3.php';
require_once JPATH_COMPONENT . '/models/libs/debugg/debugg.php';

class gglmsModelcoupondispenser extends JModelAdmin
{


    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_gglms.coupondispenser', 'coupondispenser', array('control' => 'jform', 'load_data' => $loadData));

        return $form;
    }

    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_gglms.edit.coupondispenser.data', array());

        if (empty($data)) {
            $data = $this->getItem();

            // $data->categoria = explode(',', $data->categoria);
            // $data->esercizi = explode(',', $data->esercizi);


        }
        return $data;
    }

    /*
     * Verifico la durata del contenuto 
     */

    public function getTable($name = '', $prefix = 'gglmsTable', $options = array())
    {

        return parent::getTable($name, $prefix, $options);
    }

    /**
     * Method to get a single record.
     *
     * @param    integer    The id of the primary key.
     *
     * @return    mixed    Object on success, false on failure.
     */
    public function getItem($pk = null)
    {
        //debug::msg('model->getItem');

        $item = parent::getItem($pk);

        return $item;
    }




}
