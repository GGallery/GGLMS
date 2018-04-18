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
    private $array_corrispondenze_contenuti=[];
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
             $this->updateContenuti();
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
                select titolo,CONCAT(alias,concat('_',convert(floor(RAND()*1000),CHAR(25)))),pubblicato,durata,descrizione,access,meta_tag,abstract,datapubblicazione,slide,path,id_quizdeluxe,tipologia,files,mod_track,prerequisiti,id_completed_data from #__gg_contenuti where id=" . $contenuto['idcontenuto'];
                $db->setQuery($query);
                $db->execute();
                $this->contenuti_inseriti++;
                $query = "select max(id) from #__gg_contenuti";
                $db->setQuery($query);
                $newid_contenuto = $db->loadResult();
                array_push($this->array_corrispondenze_contenuti,['vecchioid'=>$contenuto['idcontenuto'],'nuovoid'=>$newid_contenuto]);


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

    public function duplicate_folder($oldcontentid,$newcontentid){

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
                        $query = 'update #__gg_unit set id_contenuto_completamento=' . $this->newid_completamento . ',titolo=CONCAT(titolo,\'_copy\'),pubblicato=0  where id=' . $nuovoidcorso;
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


    private function updateContenuti(){

        try {
            $db = JFactory::getDBO();

            foreach ($this->array_corrispondenze_contenuti as $contenuto) {

                $query = 'select prerequisiti from #__gg_contenuti where id=' . $contenuto['nuovoid'];
                $db->setQuery($query);
                $prerequisiti = $db->loadResult();
                $nuovi_prerequisiti = [];
                $array_prerequisiti = explode(',', $prerequisiti);
                foreach ($array_prerequisiti as $prerequisito) {

                    foreach ($this->array_corrispondenze_contenuti as $contenuto_) {

                        if ($prerequisito == $contenuto_['vecchioid'])
                            array_push($nuovi_prerequisiti, $contenuto_['nuovoid']);

                    }


                }
                $query = 'update #__gg_contenuti set prerequisiti =\'' . implode(',', $nuovi_prerequisiti) . '\' where id=' . $contenuto['nuovoid'];

                $db->setQuery($query);
                $db->execute();

            }
        }catch(Exception $e){

            DEBUGG::log($e->getMessage(), 'update contenuti',0,1,0);
        }

    }

    public function verify_id_completamento_corso($pk){

        try{
            $db = JFactory::getDBO();
            $query=$db->getQuery(true);
            $query->select('is_corso, id_contenuto_completamento');
            $query->from('#__gg_unit');
            $query->where('id='.$pk);
            $db->setQuery($query);
            $idcontenuto=$db->loadObjectList();
            if($idcontenuto[0]->is_corso==0){
                return true;
            }elseif($idcontenuto[0]->id_contenuto_completamento){
                $query=$db->getQuery(true);
                $query->select('count(*)');
                $query->from('#__gg_contenuti');
                $query->where('id='.$idcontenuto[0]->id_contenuto_completamento);
                $db->setQuery($query);
                $count=$db->loadResult();
                if($count>0){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }

        }catch(Exception $e){
            DEBUGG::log($e->getMessage(), 'update contenuti',0,1,0);
        }

    }

    public function deleteUnita($pk){

        try {
            $db = JFactory::getDBO();
            $query = 'delete from #__gg_unit where id=' . $pk;
            $db->setQuery($query);
            $db->execute();
            $query = "delete c.* from #__gg_contenuti as c inner join #__gg_unit_map as m on c.id=m.idcontenuto where m.idunita=" . $pk;
            $db->setQuery($query);
            $db->execute();
            $query = "delete from #__gg_unit_map where idunita=" . $pk;
            $db->setQuery($query);
            $db->execute();
            $query = "select id from #__gg_unit where unitapadre=" . $pk;
            $db->setQuery($query);
            $sotto_unita = $db->loadAssocList();
            foreach ($sotto_unita as $unita) {

                $this->deleteUnita($unita['id']);
            }
        return "operazione conclusa";
        }catch (Exception $e){

            DEBUGG::log($e->getMessage(), 'delete unita',0,1,0);
            return "operazione fallita: ".$e->getMessage();
        }
    }
}
