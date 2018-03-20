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
require_once JPATH_COMPONENT . '/models/libs/debugg/debugg.php';

class gglmsModelunita extends JModelAdmin {

    private $contenuti_inseriti=0;
    private $unita_inserite=0;
    private $array_corrispondenze=[];
    private $id_completamento;
    private $newid_completamento;
    private $oldcontentid;
    private $newcontentid;


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

            $item->id_gruppi_abilitati= gglmsHelper::GetMappaAccessoGruppi($item);

        }

        return $item;
    }

    public function clonaCorso($pk){

         try {

             $this->id_completamento = $this->getIdCompletamento($pk);
             $this->clonaUnita($pk);
             $this->updateUnita($pk);
             return "unita inserite: " . $this->unita_inserite . " contenuti inseriti: " . $this->contenuti_inseriti;
         }catch (Exception $e){

             DEBUGG::log($e->getMessage(), 'clona corso',0,1,0);
         }

    }

    private function getIdCompletamento($pk){

        try {
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('id_contenuto_completamento');
            $query->from('#__gg_unit');
            $query->where('id=' . $pk);
            $db->setQuery($query);

            return $db->loadResult();
        }catch (Exception $e){

            DEBUGG::log($e->getMessage(), 'get id completamento',0,1,0);
        }
    }

    private function clonaUnita($pk){

        try {
            $db = JFactory::getDBO();

            $query = 'insert into #__gg_unit (titolo,alias,pubblicato,descrizione,unitapadre, id_event_booking,id_contenuto_completamento,ordinamento,accesso,is_corso,data_inizio,data_fine) 
        select titolo,CONCAT(alias,concat(\'_\',convert(floor(RAND()*1000),CHAR(25)))),pubblicato,descrizione,unitapadre, id_event_booking,id_contenuto_completamento,ordinamento,accesso,is_corso,data_inizio,data_fine from #__gg_unit where id=' . $pk;
            $db->setQuery($query);
            $db->execute();
            $this->unita_inserite++;

            $query = "select max(id) from #__gg_unit";

            $db->setQuery($query);
            $newid = $db->loadResult();
            array_push($this->array_corrispondenze,['vecchioid'=>$pk,'nuovoid'=>$newid]);
            $query = "select idcontenuto from #__gg_unit_map where idunita=" . $pk;
            $db->setQuery($query);
            $contenuti = $db->loadAssocList();

            foreach ($contenuti as $contenuto) {



                $query = "insert into #__gg_contenuti (titolo,alias,pubblicato,durata,descrizione,access,meta_tag,abstract,datapubblicazione,slide,path,id_quizdeluxe,tipologia,files,mod_track,prerequisiti,id_completed_data)
                select titolo,alias,pubblicato,durata,descrizione,access,meta_tag,abstract,datapubblicazione,slide,path,id_quizdeluxe,tipologia,files,mod_track,prerequisiti,id_completed_data from #__gg_contenuti where id=" . $contenuto['idcontenuto'];
                $db->setQuery($query);
                $db->execute();
                $this->contenuti_inseriti++;

                $query = 'select ordinamento from #__gg_unit_map where idcontenuto=' . $contenuto['idcontenuto'] . ' limit 1';
                $db->setQuery($query);
                $ordinamento = $db->loadResult();
                $query = 'insert into #__gg_unit_map values ((select max(id) from #__gg_contenuti), ' . $newid . ', ' . $ordinamento . ') ';
                $db->setQuery($query);
                $db->execute();

                $query='select max(id) from #__gg_contenuti';
                $db->setQuery($query);
                $newcontentid= $db->loadResult();
                $this->duplicate_folder($contenuto['idcontenuto'],$newcontentid);
                if($contenuto['idcontenuto']==$this->id_completamento){

                    $this->newid_completamento=$newcontentid;
                }
            }
            $query = "select id from #__gg_unit where unitapadre=" . $pk;
            $db->setQuery($query);
            $sotto_unita = $db->loadAssocList();
            foreach ($sotto_unita as $unita) {

                $this->clonaUnita($unita['id']);
            }



        }catch (Exception $e){

            DEBUGG::log($e->getMessage(), 'clone unita',0,1,0);
        }
    }

    private function duplicate_folder($oldcontentid,$newcontentid){

        try {

            $this->oldcontentid = $oldcontentid;
            $this->newcontentid = $newcontentid;
            $source = $_SERVER['DOCUMENT_ROOT'] . '/mediagg/contenuti/' . $oldcontentid;
            $dest = $_SERVER['DOCUMENT_ROOT'] . '/mediagg/contenuti/' . $newcontentid;
            if ($this->dircopy($source, $dest) == true) {

               DEBUGG::log('duplicate:' . $source . ' in to ' . $dest , 'duplicate folder',0,1,0);
            }
        }catch (Exception $e){

            DEBUGG::log($e->getMessage(), 'duplicate folder',0,1,0);
        }

    }

    private function dircopy($source, $dest, $permissions = 0755){

        try {
            // Check for symlinks
            if (is_link($source)) {
                return symlink(readlink($source), $dest);
            }

            // Simple copy for a file
            if (is_file($source)) {
                if (pathinfo($source)['filename'] == $this->oldcontentid) {

                    //DEBUGG::log('copy ' . $source . ' in to ' . pathinfo($dest)['dirname'] . '/' . $this->newcontentid . '.' . pathinfo($source)['extension'], 'dircopy',0,1,0);
                    return copy($source, pathinfo($dest)['dirname'] . '/' . $this->newcontentid . '.' . pathinfo($source)['extension']);
                } else {

                    //DEBUGG::log('copy ' . $source . ' in to ' . $dest, 'dircopy',0,1,0);
                    return copy($source, $dest);
                }
            }

            // Make destination directory
            if (!is_dir($dest)) {
                mkdir($dest, $permissions);
            }

            // Loop through the folder

            if (file_exists($source)) {
                $dir = dir($source);
                while (false !== $entry = $dir->read()) {
                    // Skip pointers
                    if ($entry == '.' || $entry == '..') {
                        continue;
                    }

                    // Deep copy directories
                    $this->dircopy("$source/$entry", "$dest/$entry", $permissions);
                }

                // Clean up
                $dir->close();
                return true;
            }
        }catch (Exception $e){

                DEBUGG::log($e->getMessage(), 'dircopy',0,1,0);
            }


    }

    private function updateUnita($pk){

        try {
            $db = JFactory::getDBO();
            $nuovoidcorso = null;

            foreach ($this->array_corrispondenze as $corrispondenza) {

                $query = 'select unitapadre from #__gg_unit where id=' . $corrispondenza['nuovoid'];
                $db->setQuery($query);
                $unitapadretochange = $db->loadResult();
                $unitapadre = 1;

                foreach ($this->array_corrispondenze as $corrispondenza_) {

                    if ($corrispondenza_['vecchioid'] == $unitapadretochange)
                        $unitapadre = $corrispondenza_['nuovoid'];

                }

                if ($corrispondenza['vecchioid'] == $pk) {
                    $nuovoidcorso = $corrispondenza['nuovoid'];
                    if ($this->newid_completamento != null && $nuovoidcorso != null) {
                        $query = 'update #__gg_unit set id_contenuto_completamento=' . $this->newid_completamento . ',titolo=CONCAT(titolo,\'_copy\')  where id=' . $nuovoidcorso;
                        $db->setQuery($query);

                        $db->execute();
                    }
                }

                $query = 'update #__gg_unit set unitapadre=' . $unitapadre . ' where id=' . $corrispondenza['nuovoid'];

                $db->setQuery($query);
                $db->execute();



            }
        }catch (Exception $e){

            DEBUGG::log($e->getMessage(), 'update unita',0,1,0);
        }
    }
    function setAlias($text) {


        $text = preg_replace('~[^\\pL\d]+~u', '_', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '_');

        return $text;
    }

}
