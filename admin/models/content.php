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
require_once 'unita.php';

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
            

//          $item->articletext = trim($item->fulltext) != '' ? $item->introtext . "<hr id=\"system-readmore\" />" . $item->fulltext : $item->introtext;
        }


        return $item;
    }

    public function clonaContenuto($pk){

        $db = JFactory::getDBO();
        $query = "insert into #__gg_contenuti (titolo,alias,pubblicato,durata,descrizione,access,meta_tag,abstract,datapubblicazione,slide,path,id_quizdeluxe,tipologia,files,mod_track,prerequisiti,id_completed_data)
                select titolo,CONCAT(alias,concat('_',convert(floor(RAND()*1000),CHAR(25)))),pubblicato,durata,descrizione,access,meta_tag,abstract,datapubblicazione,slide,path,id_quizdeluxe,tipologia,files,mod_track,prerequisiti,id_completed_data from #__gg_contenuti where id=" . $pk;
        $db->setQuery($query);
        $db->execute();
        $query = "select max(id) from #__gg_contenuti";
        $db->setQuery($query);
        $newid_contenuto = $db->loadResult();
        $modelUnita=new gglmsModelUnita();
        $modelUnita->duplicate_folder($pk,$newid_contenuto);


    }





}
