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

class gglmsModelunita extends JModelAdmin {

    public function getForm($data = array(), $loadData = true) {
        // Get the form.
        $form = $this->loadForm('com_gglms.unita', 'unita', array('control' => 'jform', 'load_data' => $loadData));

        return $form;
    }

    protected function loadFormData() {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_gglms.edit.unita.data', array());

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
        //debug::msg('model->getItem');

        if ($item = parent::getItem($pk)) {

//            $item->accesso=json_decode($item->accesso);

            // Convert the params field to an array.
            /* 	$registry = new JRegistry;
              $registry->loadString($item->attribs);
              $item->attribs = $registry->toArray();

              // Convert the metadata field to an array.
              $registry = new JRegistry;
              $registry->loadString($item->metadata);
              $item->metadata = $registry->toArray();

              // Convert the images field to an array.
              $registry = new JRegistry;
              $registry->loadString($item->images);
              $item->images = $registry->toArray();

              // Convert the urls field to an array.
              $registry = new JRegistry;
              $registry->loadString($item->urls);
              $item->urls = $registry->toArray();



              $item->articletext = trim($item->fulltext) != '' ? $item->introtext . "<hr id=\"system-readmore\" />" . $item->fulltext : $item->introtext;
             * 
             */
        }

        return $item;
    }

}
