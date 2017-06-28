<?php

/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
require_once 'libs/getid3/getid3.php';

class gglmsModelContent extends JModelAdmin {

    public function getForm($data = array(), $loadData = true) {
        // Get the form.
        $form = $this->loadForm('com_gglms.content', 'content', array('control' => 'jform', 'load_data' => $loadData));

        return $form;
    }

    protected function loadFormData() {
        $app = JFactory::getApplication();
        $data = $app->getUserState('com_gglms.edit.content.data', array());

        if (empty($data)) {
            $data = $this->getItem();

            //$data->categoria = explode(',', $data->categoria);
            //$data->unita = gglmsHelper::GetMappaContenutoUnita($data);

            //print_r($data);
        }
        return $data;
    }

    public function getTable($name = '', $prefix = 'gglmsTable', $options = array()) {

        return parent::getTable($name, $prefix, $options);
    }

    /**
     * Method to get a single record.
     *
     * @param	integer	The id of the primary key.
     *
     * @return	mixed	Object on success, false on failure.
     */
    public function getItem($pk = null) {

        if ($item = parent::getItem($pk)) {
//            Convert the params field to an array.
//            $registry = new JRegistry;
//            $registry->loadString($item->unita);

            $item->categoria= gglmsHelper::GetMappaContenutoUnita($item);

            $item->files= gglmsHelper::GetMappaContenutoFiles($item);

            $item->prerequisiti= explode(',', $item->prerequisiti);

//            print_r($item);

//              $item->articletext = trim($item->fulltext) != '' ? $item->introtext . "<hr id=\"system-readmore\" />" . $item->fulltext : $item->introtext;
        }


        return $item;
    }





}
