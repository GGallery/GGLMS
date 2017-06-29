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

class gglmsModelFile extends JModelAdmin {

    public function getForm($data = array(), $loadData = true) {
        // Get the form.
        $form = $this->loadForm('com_gglms.file', 'file', array('control' => 'jform', 'load_data' => $loadData));
        
        return $form;
    }

    
    protected function loadFormData() {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_gglms.edit.file.data', array());
        if (empty($data)) {
            $data = $this->getItem();
        }
        return $data;
    }

    /*
     * Verifico la durata del contenuto 
     */

    public function checkContentDuration($id) {
//        $path = "/var/www/vhosts/e-taliano.tv/httpdocs/home/mediatv/_contenuti/$id/$id.flv";
//        $getID3 = new getID3();
//        $file = $getID3->analyze($path);
//        return (int) $file['playtime_seconds'];
        return 0;
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
        //debug::msg('model->getItem');

        if ($item = parent::getItem($pk)) {



        }

        return $item;
    }

}
