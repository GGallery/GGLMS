<?php

/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class gglmsControllerContent extends JControllerForm {

    private $_app;
    protected $_db;

    public function __construct($config = array()) {
        parent::__construct($config);
        $this->_db = JFactory::getDBO();
        $this->_app = JFactory::getApplication('site');
    }

    public function clonaContenuto()
    {

        try {

            $app = JFactory::getApplication();
            $contentModel = $this->getModel('content');
            $jinput = $app->input;
            $id = $jinput->get('cid')[0];
            if ($id == null) {

                $app->redirect(JRoute::_('index.php?option=com_gglms&view=contents', false), $app->enqueueMessage('non hai selezionato alcun corso', 'Warning'));
                return null;
            }
            $result = $contentModel->clonaContenuto($id);
            $app->redirect(JRoute::_('index.php?option=com_gglms&view=contents', false), $app->enqueueMessage($result));
        } catch (exception $exception) {

            $app->redirect(JRoute::_('index.php?option=com_gglms&view=contents', false), $app->enqueueMessage($exception->getMessage(), 'Error'));
        }

    }

    public function parsescorm() {

        try {
            $current_id = $this->input->get('id', null, 'int');
            $manifestfile = "../../mediagg/contenuti/". $current_id ."/imsmanifest.xml";
            $array_manifest_element = ($this->readIMSManifestFile($manifestfile));

            $current_item = $this->getContent($current_id);
            $current_unit = $this->getUnitPadre($current_id);

            $ordinamento = 1;
            $log = '';

            foreach ($array_manifest_element as $item) {
                $object = new stdClass();
                $object->titolo = $item['title'];
                $object->alias = $this->setAlias($item['title'].random_int(100,999));
                $object->pubblicato = $current_item->pubblicato;
                $object->tipologia = $current_item->tipologia;
                $object->mod_track = $current_item->mod_track;
                $object->abstract = "DERIVATO DA ID: ".$current_item->id;
                $object->path =  "../".$current_id."/".$item['href'];
                $newElementId = $this->saveNewContent($object);
                $log .= "Creato contenuto <b>". $object->titolo . "</b> (id ".$newElementId.")";


                $objunit = new stdClass();
                $objunit->idcontenuto = $newElementId;
                $objunit->idunita = $current_unit;
                $objunit->ordinamento = $ordinamento++;
                $this->setUnitPadre($objunit);
                $log .= " e aggiunto all'unita ".$current_unit. "<br>";

            }


            $this->_app->redirect('index.php?option=com_gglms&view=content&layout=edit&id='. $current_id , JText::_($log));


        } catch (Exception $e){
            var_dump($e);
        }
        $this->_app->close();

    }

    public function readIMSManifestFile($manifestfile) {

        // PREPARATIONS

        // central array for resource data
        global $resourceData;

        // load the imsmanifest.xml file
        $xmlfile = new DomDocument;
        $xmlfile->preserveWhiteSpace = FALSE;
        $xmlfile->load($manifestfile);

        // adlcp namespace
        $manifest = $xmlfile->getElementsByTagName('manifest');
        $adlcp = $manifest->item(0)->getAttribute('xmlns:adlcp');

        // READ THE RESOURCES LIST

        // array to store the results
        $resourceData = array();

        // get the list of resource element
        $resourceList = $xmlfile->getElementsByTagName('resource');

        $r = 0;
        foreach ($resourceList as $rtemp) {

            // decode the resource attributes
            $identifier = $resourceList->item($r)->getAttribute('identifier');
            $resourceData[$identifier]['type'] = $resourceList->item($r)->getAttribute('type');
            $resourceData[$identifier]['scormtype'] = $resourceList->item($r)->getAttribute('adlcp:scormtype');
            $resourceData[$identifier]['href'] = $resourceList->item($r)->getAttribute('href');

            // list of files
            $fileList = $resourceList->item($r)->getElementsByTagName('file');

            $f = 0;
            foreach ($fileList as $ftemp) {
                $resourceData[$identifier]['files'][$f] =  $fileList->item($f)->getAttribute('href');
                $f++;
            }

            // list of dependencies
            $dependencyList = $resourceList->item($r)->getElementsByTagName('dependency');

            $d = 0;
            foreach ($dependencyList as $dtemp) {
                $resourceData[$identifier]['dependencies'][$d] =  $dependencyList->item($d)->getAttribute('identifierref');
                $d++;
            }

            $r++;

        }

        // resolve resource dependencies to create the file lists for each resource
        foreach ($resourceData as $identifier => $resource) {
            $resourceData[$identifier]['files'] = $this->resolveIMSManifestDependencies($identifier);
        }

        // READ THE ITEMS LIST

        // arrays to store the results
        $itemData = array();

        // get the list of resource element
        $itemList = $xmlfile->getElementsByTagName('item');

        $i = 0;
        foreach ($itemList as $itemp) {

            // decode the resource attributes
            $identifier = $itemList->item($i)->getAttribute('identifier');
            $itemData[$identifier]['identifierref'] = $itemList->item($i)->getAttribute('identifierref');
            $itemData[$identifier]['title'] = $itemList->item($i)->getElementsByTagName('title')->item(0)->nodeValue;
            $itemData[$identifier]['masteryscore'] = $itemList->item($i)->getElementsByTagNameNS($adlcp,'masteryscore')->item(0)->nodeValue;
            $itemData[$identifier]['datafromlms'] = $itemList->item($i)->getElementsByTagNameNS($adlcp,'datafromlms')->item(0)->nodeValue;

            $i++;

        }

        // PROCESS THE ITEMS LIST TO FIND SCOS

        // array for the results
        $SCOdata = array();

        // loop through the list of items
        foreach ($itemData as $identifier => $item) {

            // find the linked resource
            $identifierref = $item['identifierref'];

            // is the linked resource a SCO? if not, skip this item
            if (strtolower($resourceData[$identifierref]['scormtype']) != 'sco') { continue; }

            // save data that we want to the output array
            $SCOdata[$identifier]['title'] = $item['title'];
            $SCOdata[$identifier]['masteryscore'] = $item['masteryscore'];
            $SCOdata[$identifier]['datafromlms'] = $item['datafromlms'];
            $SCOdata[$identifier]['href'] = $resourceData[$identifierref]['href'];
            $SCOdata[$identifier]['files'] = $resourceData[$identifierref]['files'];

        }

        return $SCOdata;

    }

    public function resolveIMSManifestDependencies($identifier) {

        global $resourceData;

        $files = $resourceData[$identifier]['files'];

        $dependencies = $resourceData[$identifier]['dependencies'];
        if (is_array($dependencies)) {
            foreach ($dependencies as $d => $dependencyidentifier) {
                $files = array_merge($files,resolveIMSManifestDependencies($dependencyidentifier));
                unset($resourceData[$identifier]['dependencies'][$d]);
            }
            $files = array_unique($files);
        }

        return $files;

    }

    public function getContent($id){

        $this->_db= JFactory::getDBO();

        $query = $this->_db->getQuery(true)
            ->select('*')
            ->from('#__gg_contenuti as s')
            ->where('id = ' . $id);


        $this->_db->setQuery($query);
        $data = $this->_db->loadObject();

        return $data;
    }

    public function getUnitPadre($id){

        try {
            $query = $this->_db->getQuery(true)
                ->select('idunita')
                ->from('#__gg_unit_map as m')
                ->where('idcontenuto = ' . $id);

            $this->_db->setQuery($query);
            $data = $this->_db->loadResult();

            return $data;
        }catch (Exception $e){
            DEBUGG::log($e->getMessage(), 'error in getUnitPAdre' , 0,1,0);
        }
    }

    public function saveNewContent($object){

        $this->_db->insertObject('#__gg_contenuti', $object);
        $lastId = $this->_db->insertid();

        return $lastId;

    }

    public function setUnitPadre($object){

        $this->_db->insertObject('#__gg_unit_map', $object);
        $this->_db->insertid();

    }


    public function setAlias($text){


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


