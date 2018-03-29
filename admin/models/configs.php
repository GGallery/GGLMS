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

class gglmsModelconfigs extends JModelAdmin {

    public function getForm($data = array(), $loadData = true) {
        // Get the form.
        $form = $this->loadForm('com_gglms.configs', 'configs', array('control' => 'jform', 'load_data' => $loadData));

        return $form;
    }



    protected function loadFormData() {
        // Check the session for previously entered form data.
        //RS $data = JFactory::getApplication()->getUserState('com_gglms.edit.content.data', array());

//        $app = JFactory::getApplication();
//        $data = $app->getUserState('com_gglms.edit.configs.data', array());
//
//
//        return $data;

        $data = new stdClass;
        $db = JFactory::getDBO();

        $db->setQuery("SELECT `config_key`, `config_value` FROM #__gg_configs");
        $settings = $db->loadObjectList();

        if(count($settings)){
            foreach($settings as $setting){
                $array = (array) $setting;

                $key= $array['config_key'];
                $value= $array['config_value'];

                $data->$key = $value;

                if($key=='id_gruppi_visibili' || $key=='alert_lista_corsi' || $key=='campi_csv')
                    $data->$key = explode(",", $value);


            }
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

        if ($item = parent::getItem($pk)) {
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
             */

        }

        return $item;
    }



    public function store($data)
    {


        $db = $this->getDbo();
        $db->truncateTable('#__gg_configs');
        $row = $this->getTable('Configs');
        $params = & JComponentHelper::getParams('com_gglms');

        foreach ($data as $key => $value)
        {
            if (is_array($value))
            {
                $value = implode(',', $value);
            }
            $row->id           = 0;
            $row->config_key   = $key;
            $row->config_value = $value;

            $row->store();

            //necessario per salvare i parametri nel campo params del componente.
            $params->set($key, $value);
        }


        //Ora vado a scrivere i parametri sul componente
        $componentid = JComponentHelper::getComponent('com_gglms')->id;
        $table = JTable::getInstance('extension');
        $table->load($componentid);
        $table->bind(array('params' => $params->toString()));

        if (!$table->check()) {
            $this->setError('lastcreatedate: check: ' . $table->getError());
            return false;
        }

        // Save to database
        if (!$table->store()) {
            $this->setError('lastcreatedate: store: ' . $table->getError());
            return false;
        }

        return true;
    }




}
