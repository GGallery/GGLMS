<?php

/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

require_once 'models/libs/getid3/getid3.php';
require_once 'models/libs/FirePHPCore/fb.php';

jimport('joomla.application.component.controller');

class gglmsController extends JControllerLegacy {

    public function __construct($config = array()) {
        parent::__construct($config);



        $this->registerTask('fileupload', 'fileupload');
        $this->registerTask('exerciseupload', 'exerciseupload');
        $this->registerTask('updatePalinsesto', 'updatePalinsesto');
        $this->registerTask('updateUnita', 'updateUnita');
        $this->registerTask('caricaContenutiUnit', 'caricaContenutiUnit');

    }

    public function __destruct() {
    }

    function display($cachable = false, $urlparams = false) {

// Set default view if not set

// Add submenu
        gglmsHelper::addSubmenu('messages');
//        echo  $this->sidebar = JHtmlSidebar::render();  //RS

        
        JRequest::setVar('view', JRequest::getCmd('view', 'contents'));
        parent::display($cachable);
    }

    function fileupload() {
//import joomlas filesystem functions, we will do all the filewriting with joomlas functions,
//so if the ftp layer is on, joomla will write with that, not the apache user, which might
//not have the correct permissions
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');

        $fieldName = 'Filedata';

//any errors the server registered on uploading
        $fileError = $_FILES[$fieldName]['error'];
        if ($fileError > 0) {
            switch ($fileError) {
                case 1:
                    echo JText::_('FILE TO LARGE THAN PHP INI ALLOWS');
                    return;

                case 2:
                    echo JText::_('FILE TO LARGE THAN HTML FORM ALLOWS');
                    return;

                case 3:
                    echo JText::_('ERROR PARTIAL UPLOAD');
                    return;

                case 4:
                    echo JText::_('ERROR NO FILE');
                    return;
            }
        }

//check for filesize
        $fileSize = $_FILES[$fieldName]['size'];
        if ($fileSize > 500000000) {
            echo JText::_('FILE BIGGER THAN 500MB: ' . $fileSize);
        }

//check the file extension is ok
        $fileName = $_FILES[$fieldName]['name'];
        $uploadedFileNameParts = explode('.', $fileName);
        $uploadedFileExtension = array_pop($uploadedFileNameParts);

        $validFileExts = explode(',', 'jpeg,jpg,png,gif,flv,mp3,ogv,mp4,webm,xml,pdf,zip');

//assume the extension is false until we know its ok
        $extOk = false;

//go through every ok extension, if the ok extension matches the file extension (case insensitive)
//then the file extension is ok
        foreach ($validFileExts as $key => $value) {
            if (preg_match("/$value/i", $uploadedFileExtension)) {
                $extOk = true;
            }
        }

        if ($extOk == false) {
            echo JText::_('INVALID EXTENSION');
            return;
        }

//the name of the file in PHP's temp directory that we are going to move to our folder
        $fileTemp = $_FILES[$fieldName]['tmp_name'];

//for security purposes, we will also do a getimagesize on the temp file (before we have moved it 
//to the folder) to check the MIME type of the file, and whether it has a width and height
//$imageinfo = getimagesize($fileTemp);
//we are going to define what file extensions/MIMEs are ok, and only let these ones in (whitelisting), rather than try to scan for bad
//types, where we might miss one (whitelisting is always better than blacklisting) 
        $okMIMETypes = 'image/jpeg,image/pjpeg,image/png,image/x-png,image/gif,';
        $validFileTypes = explode(",", $okMIMETypes);

//if the temp file does not have a width or a height, or it has a non ok MIME, return
//        if (!is_int($imageinfo[0]) || !is_int($imageinfo[1]) || !in_array($imageinfo['mime'], $validFileTypes)) {
//            echo JText::_('INVALID FILETYPE');
//            return;
//        }
//lose any special characters in the filename
// $fileName = preg_replace("/[^A-Za-z0-9]/i", "-", $fileName);
//$fileName = $fileName . JRequest::getVar('id_contenuto');

        $id_contenuto = JRequest::getVar('id_contenuto');
        $path_contenuto = JRequest::getVar('path_contenuto');


        if (strstr($fileName, "cover")) {
            $fileName = $id_contenuto . "_evidenza." . $uploadedFileExtension;
        } elseif (strstr($fileName, "esercizio")) {
            $fileName = $id_contenuto . "_esercizio." . $uploadedFileExtension;
        } elseif (strstr($fileName, "slide") || strstr($fileName, "Diapositiva")) {
            $fileName = "slide" . DS . "normal" . DS . $fileName;
        } else {
            $fileName = $id_contenuto . "." . $uploadedFileExtension;
        }

        $filename = strtolower($filename);

        FB::info($_FILES, " _FILES ");

//always use constants when making file paths, to avoid the possibilty of remote file inclusion
        $uploadPath = '/var/www/html/ediacademy/mediagg/contenuti' . DS . $id_contenuto . DS . $fileName;
        FB::info($uploadPath, "UploadPath");


        if (!JFile::upload($fileTemp, $uploadPath)) {
            echo JText::_('ERROR MOVING FILE');
            return;
        } else {
            if (preg_match('/\.flv$/', $fileName)) {
                // se inserisco un flv lo metto nella coda dei file da convertire
                $query = 'INSERT INTO queue (ipath, opath, filename, type, size, actions_id, priority, status, start) VALUES
                    (\'' . $uploadPath . '\', \'/var/www/vhosts/e-taliano.tv/httpdocs/home/mediatv/_contenuti/' . $id_contenuto . '/\', \'' . $id_contenuto . '\' , \'video/flv\', 0, 1, 5, 0, NOW()),
                    (\'' . $uploadPath . '\', \'/var/www/vhosts/e-taliano.tv/httpdocs/home/mediatv/_contenuti/' . $id_contenuto . '/\', \'' . $id_contenuto . '\' , \'video/flv\', 0, 2, 5, 0, NOW()),
                    (\'' . $uploadPath . '\', \'/var/www/vhosts/e-taliano.tv/httpdocs/home/mediatv/_contenuti/' . $id_contenuto . '/\', \'' . $id_contenuto . '\' , \'video/flv\', 0, 3, 5, 0, NOW()),
                    (\'' . $uploadPath . '\', \'/var/www/vhosts/e-taliano.tv/httpdocs/home/mediatv/_contenuti/' . $id_contenuto . '/\', \'' . $id_contenuto . '\' , \'video/flv\', 0, 4, 5, 0, NOW())';
                FB::info($query, "inserisco i video nella coda di conversione");
                $db = JFactory::getDBO();
                $db->setQuery((string) $query);
                try {
                    $db->query();
                } catch (Exception $e) {
                    FB::error($e);
                }
            }

            // success, exit with code 0 for Mac users, otherwise they receive an IO Error
            exit(0);
        }
    }

    function exerciseupload() {
//import joomlas filesystem functions, we will do all the filewriting with joomlas functions,
//so if the ftp layer is on, joomla will write with that, not the apache user, which might
//not have the correct permissions
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');

        $fieldName = 'Filedata';

//any errors the server registered on uploading
        $fileError = $_FILES[$fieldName]['error'];
        if ($fileError > 0) {
            switch ($fileError) {
                case 1:
                    echo JText::_('FILE TO LARGE THAN PHP INI ALLOWS');
                    return;

                case 2:
                    echo JText::_('FILE TO LARGE THAN HTML FORM ALLOWS');
                    return;

                case 3:
                    echo JText::_('ERROR PARTIAL UPLOAD');
                    return;

                case 4:
                    echo JText::_('ERROR NO FILE');
                    return;
            }
        }

//check for filesize
        $fileSize = $_FILES[$fieldName]['size'];
        if ($fileSize > 500000000) {
            echo JText::_('FILE BIGGER THAN 500MB: ' . $fileSize);
        }

//check the file extension is ok
        $fileName = $_FILES[$fieldName]['name'];
        $uploadedFileNameParts = explode('.', $fileName);
        $uploadedFileExtension = array_pop($uploadedFileNameParts);

        $validFileExts = explode(',', 'jpeg,jpg,png,gif,flv,mp3,ogv,mp4,webm,xml,pdf,zip');

//assume the extension is false until we know its ok
        $extOk = false;

//go through every ok extension, if the ok extension matches the file extension (case insensitive)
//then the file extension is ok
        foreach ($validFileExts as $key => $value) {
            if (preg_match("/$value/i", $uploadedFileExtension)) {
                $extOk = true;
            }
        }

        if ($extOk == false) {
            echo JText::_('INVALID EXTENSION');
            return;
        }

//the name of the file in PHP's temp directory that we are going to move to our folder
        $fileTemp = $_FILES[$fieldName]['tmp_name'];

//for security purposes, we will also do a getimagesize on the temp file (before we have moved it 
//to the folder) to check the MIME type of the file, and whether it has a width and height
//$imageinfo = getimagesize($fileTemp);
//we are going to define what file extensions/MIMEs are ok, and only let these ones in (whitelisting), rather than try to scan for bad
//types, where we might miss one (whitelisting is always better than blacklisting) 
        $okMIMETypes = 'image/jpeg,image/pjpeg,image/png,image/x-png,image/gif,';
        $validFileTypes = explode(",", $okMIMETypes);

//if the temp file does not have a width or a height, or it has a non ok MIME, return
//        if (!is_int($imageinfo[0]) || !is_int($imageinfo[1]) || !in_array($imageinfo['mime'], $validFileTypes)) {
//            echo JText::_('INVALID FILETYPE');
//            return;
//        }
//lose any special characters in the filename
// $fileName = preg_replace("/[^A-Za-z0-9]/i", "-", $fileName);
//$fileName = $fileName . JRequest::getVar('id_contenuto');

        $id_contenuto = JRequest::getVar('id_contenuto');
        $path_contenuto = JRequest::getVar('path_contenuto');


        $fileName = "esercizio.zip";



//always use constants when making file paths, to avoid the possibilty of remote file inclusion
        $uploadPath = '/var/www/vhosts/e-taliano.tv/httpdocs/home/mediatv/_esercizi' . DS . $id_contenuto . DS . $fileName;
        $extractPath = '/var/www/vhosts/e-taliano.tv/httpdocs/home/mediatv/_esercizi' . DS . $id_contenuto;
        FB::log($uploadPath, "UploadPath");
        FB::log($extractPath, "extractPath");


        if (!JFile::upload($fileTemp, $uploadPath)) {
            echo JText::_('ERROR MOVING FILE');
        }
        //extract
        else {

            $zip = new ZipArchive;
            $res = $zip->open($uploadPath);
            if ($res === TRUE) {
                $zip->extractTo($extractPath);
                $zip->close();
                unlink($uploadPath);
                echo 'woot!';
            } else {
                echo 'doh!';
            }
        }
        //fine extract
        return;
    }

    public function updateUnita() {

//        debug::init();
//        debug::startNested('log', array('debug_level' => DEBUG_DEV, 'logfile' => '/var/www/vhosts/e-taliano.tv/httpdocs/home/tmp/logel.txt', 'append' => false));
//
//        debug::msg("inizio");




        $app = &JFactory::getApplication();
        $db = JFactory::getDBO();
        $dp = $_POST['arr'];


        FB::log($dp, "dp");

        // $stringdp = array();
        // foreach ($dp as $Item) {
        //     array_push($stringdp, $Item['idcontenuto']);
        // }
        // $stringdp = implode(",", $stringdp);

        $query = "DELETE FROM #__gg_unit_map WHERE idunita = " . $dp[0]['idunita'] . "";
        // and idcontenuto not in ( $stringdp )";

        // FB::log($query, "Query update unità");

        $db->setQuery((string) $query);
        $res = $db->query();
        foreach ($dp as $Item) {
            if($Item['idcontenuto']){
                $query = "INSERT INTO #__gg_unit_map (idcontenuto, idunita  , ordinamento )";
                $query .= " VALUES (" . $Item['idcontenuto'] . ", '" . $Item['idunita'] . "', " . $Item['ordinamento'] . ")";
                $db->setQuery((string) $query);
                $res = $db->query();
            }
        }

//        debug::end('log');
        $app->close();
    }

    public function updatePalinsesto() {
        $app = &JFactory::getApplication();
        $db = JFactory::getDBO();
        $dp = $_POST['arr'];
        $query = "DELETE FROM #__gg_palinsesti WHERE datapalinsesto = '" . $dp[0]['datapalinsesto'] . "'";
        $db->setQuery((string) $query);
        $res = $db->query();
        foreach ($dp as $Item) {
            $query = "INSERT INTO #__gg_palinsesti (idcontenuto, datapalinsesto, ordinamento )";
            $query .= " VALUES (" . $Item['idcontenuto'] . ", '" . $Item['datapalinsesto'] . "', " . $Item['ordinamento'] . ")";
            $db->setQuery((string) $query);
            $res = $db->query();
        }
        $app->close();
    }

    public function cercaContenuto() {

        $app = &JFactory::getApplication();
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $where = $_POST['where'];

        $query->select('c.id AS id, c.titolo as titolo');
        $query->from('#__gg_contenuti as c');

        if (isset($where))
            $query->where($where);

        $query->order('c.id desc');

        $db->setQuery((string) $query, 0, 20);
        $res = $db->loadAssocList();

        echo json_encode($res);

        $app->close();
    }

    public function caricaContenutiUnit() {

        $app = &JFactory::getApplication();
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $where = $_POST['where'];
        $query->select(' id, titolo');
        $query->from('#__gg_contenuti as c');
        $query->join('left', '#__gg_unit_map as m on m.idcontenuto=c.id');

        if (isset($where))
            $query->where($where);

        $query->order('m.ordinamento');


        $db->setQuery((string) $query, 0, 20);
        $res = $db->loadAssocList();

        if ($res == null)
            echo 'vuoto';
        else
            echo json_encode($res);

        $app->close();
    }

    public function loadUnit() {

        // $app = &JFactory::getApplication();
        // $db = JFactory::getDBO();
        // $query = $db->getQuery(true);

        // $where = $_POST['where'];

        // $query->select('c.id AS id, c.alias as titolo');
        // $query->from('#__gg_unit as c');

        // if (isset($where))
        //     $query->where($where);

        // $query->order('c.ordinamento, c.id desc');

        // $db->setQuery((string) $query, 0, 20);
        // $res = $db->loadAssocList();

        // echo json_encode($res);

        // $app->close();

        $res = array();
        $res = $this->getUnitTree(1);

        echo json_encode($res);

        $app->close();


    }

    public function generateUniTree(){
        $tree = array();
        $tmptree = array();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.id AS value, a.categoria AS titolo');
        $query->from('#__gg_unit AS a');
        $query->where("categoriapadre=" . $item);

        $db->setQuery($query);

        // Check for a database error.
        if ($db->getErrorNum()) {
            JError::raiseWarning(500, $db->getErrorMsg());
        }

        $tmptree = $db->loadObjectList();
        foreach ($tmptree as $item) {
            array_push($tree, $item);
            foreach ($this->getUnitTree($item->value) as $item2) {
                $item2->text = "─" . $item2->text;
                array_push($tree, $item2);
            }
        }
        unset($tmptree);
        return $tree;

    }

    public function duplicaPalinsesto() {

        $app = &JFactory::getApplication();
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $where = $_POST['where'];

        $query->select('c.id AS id, c.titolo as titolo');
        $query->from('#__gg_palinsesti as p');
        $query->join('left', '#__gg_contenuti as c on c.id=p.idcontenuto');

        if (isset($where))
            $query->where($where);

        $query->order('p.ordinamento');

        $db->setQuery((string) $query, 0, 30);
        $res = $db->loadAssocList();

        FB::log($query, "duplicaPalinsesto");

        echo json_encode($res);

        $app->close();
    }

}
