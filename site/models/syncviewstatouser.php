<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

//require_once JPATH_COMPONENT . '/models/contenuto.php';
require_once JPATH_COMPONENT . '/models/unita.php';
//require_once JPATH_COMPONENT . '/models/users.php';
//require_once JPATH_COMPONENT . '/models/syncdatareport.php';
//require_once JPATH_COMPONENT . '/models/report.php';
//require_once JPATH_COMPONENT . '/models/contenuto.php';


/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsModelSyncViewStatoUser extends JModelLegacy
{
    protected $_db;
    private $_app;
    private $params;


    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_app = JFactory::getApplication();
        $this->params = $this->_app->getParams();
        $this->_db = JFactory::getDbo();

    }

    public function MaxTimeStampforSession(){

        try
        {
            $query = $this->_db->getQuery(true)
                ->select('COALESCE(MAX(TIMESTAMP),\'1900-01-01\')')
                ->from('#__gg_view_stato_user_unita as v')

            ;

            $this->_db->setQuery($query);
            $data = $this->_db->loadResult();
            DEBUGG::log('MAX TIMESTAMP: '.$data, 'max_timestamp',0,1,0);
            return $data;

        }
        catch (Exception $e)
        {
            DEBUGG::log($e->getMessage(), 'deltaReport userunita',0,1,0);
            $this->_app->close();
        }

    }

    public function syncViewStatoUser($offset, $limit, $maxts,$typeofcall)
    {

        switch ($typeofcall) {
            case 'curl':

               return $this->insertData($offset, $limit, $maxts);


            break;

            case 'task':
            case null:
                $maxts=$this->MaxTimeStampforSession();

                return $this->insertData(null, null, $maxts);

            break;
        }
    }

    private function insertData($offset,$limit,$maxts){


        $datauserunit=$this->deltaReport($offset,$limit,$maxts);

        if(count($datauserunit)>0){
            $this->insertViewUnita($datauserunit);
            $this->insertViewCorso($datauserunit);
            $result= '0';


        }else{
            $result='1';
            DEBUGG::log('FINE PROCEDURA ', ' ',0,1,0);
        }
        return $result;

    }

    public function deltaReport($offset,$limit,$maxts){

        try
        {
            $query = $this->_db->getQuery(true)
                ->select('distinct id_utente, id_anagrafica, id_unita, id_corso')
                ->from('#__gg_report as r');
                if($maxts!=null) {
                   $query->where('r.`timestamp`> \'' . $maxts . '\' and  id_unita<>0');
                }
            $query->setLimit($offset,$limit);
            $this->_db->setQuery($query);


            $data = $this->_db->loadObjectList();

            DEBUGG::log('elaboro per LIMIT: '.$limit.' dati:'.count($data), 'deltaReport',0,1,0);
            return $data;
        }
        catch (Exception $e)
        {
            DEBUGG::log($e->getMessage(), 'deltaReport userunita',0,1,0);
        }

    }


    private function insertViewUnita($datauserunit){

         //ini_set('max_execution_time', 600);

        try {

            //DEBUGG::log('inserisco '.count($datauserunit).' record per syncUser', 'insertData',0,1,0);

            foreach ($datauserunit as $record){



                $obj = new gglmsModelUnita();
                $completed=0;
                //echo $record->id_utente;
                if($obj->isUnitaCompleta($record->id_unita, $record->id_utente)!=null)
                    $completed=1;

                $query = 'INSERT into #__gg_view_stato_user_unita (id_anagrafica, id_unita, id_corso, stato, timestamp) VALUES (';
                $query=$query. $record->id_anagrafica . ',' . $record->id_unita . ',' . $record->id_corso . ',' . $completed . ',NOW()) ON DUPLICATE KEY UPDATE stato=' . $completed;
                //echo $query;
                $this->_db->setQuery($query);
                $this->_db->execute();



            }

            DEBUGG::log('inseriti '.count($datauserunit).' record per syncUser', 'insertData',0,1,0);

            return;




        }
        catch (Exception $e)
        {
            DEBUGG::log($e, 'insert syncUserUnita',0,1,1);
        }



    }

    private function insertViewCorso($datausercorsi){

        // ini_set('max_execution_time', 600);

        try {

            DEBUGG::log('inserisco '.count($datausercorsi).' record per syncUserCorso', 'insertData',0,1,0);


            foreach ($datausercorsi as $record){

                $obj=new gglmsModelUnita();
                $completed=0;
                if($obj->isUnitaCompleta($record->id_corso, $record->id_utente)!=null)
                    $completed=1;


                $query = 'INSERT into #__gg_view_stato_user_corso (id_anagrafica, id_corso, stato, timestamp) VALUES (';
                $query=$query. $record->id_anagrafica . ',' . $record->id_corso . ',' . $completed . ',NOW()) ON DUPLICATE KEY UPDATE stato=' . $completed;
                $this->_db->setQuery($query);
                $this->_db->execute();



            }

            DEBUGG::log('elaborati '.count($datausercorsi).' record per syncUserCorso', 'insertData',0,1,0);

            return;

        }
        catch (Exception $e)
        {
            DEBUGG::log($e, 'insert syncUserCorso',0,1,0);
        }



    }


}
