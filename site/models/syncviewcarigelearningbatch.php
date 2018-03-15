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
require_once JPATH_COMPONENT . '/models/report.php';
require_once JPATH_COMPONENT . '/models/contenuto.php';


/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsModelSyncViewCarigeLearningBatch extends JModelLegacy
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
                ->from('#__gg_view_carige_learning_batch as v')

            ;

            $this->_db->setQuery($query);
            $data = $this->_db->loadResult();
            DEBUGG::log('CARIGE BATCH MAX TIMESTAMP: '.$data, 'max_timestamp',0,1,0);
            return $data;

        }
        catch (Exception $e)
        {
            DEBUGG::log($e->getMessage(), 'deltaReport CARIGE',0,1,0);
            $this->_app->close();
        }

    }

    public function syncViewCarigeLearningBatch($offset, $limit, $maxts,$typeofcall)
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
            $this->insertView($datauserunit);
            $result= '0';


        }else{
            $result='1';
            DEBUGG::log('FINE PROCEDURA CARIGE BATCH', ' ',0,1,0);
        }
        return $result;

    }

    public function deltaReport($offset,$limit,$maxts){

        try
        {
            $query = $this->_db->getQuery(true)
                ->select('distinct id_utente, id_anagrafica, id_corso')
                ->from('#__gg_report as r');
                if($maxts!=null) {
                   $query->where('r.`timestamp`> \'' . $maxts . '\' and  id_unita<>0');
                }
            $query->setLimit($offset,$limit);
            $this->_db->setQuery($query);


            $data = $this->_db->loadObjectList();

            DEBUGG::log('CARIGE BATCH elaboro per LIMIT: '.$limit.' dati:'.count($data), 'deltaReport',0,1,0);
            return $data;
        }
        catch (Exception $e)
        {
            DEBUGG::log($e->getMessage(), 'CARIGE BATCH deltaReport userunita',0,1,0);
        }

    }



    private function insertView($datausercorsi){

        // ini_set('max_execution_time', 600);

        try {

            //DEBUGG::log('inserisco '.count($datausercorsi).' record per syncUserCorso', 'insertData',0,1,0);


            foreach ($datausercorsi as $record){

                $dateiniziofine=$this->dataPrimoAccessoDataFine($record->id_corso,$record->id_anagrafica);
                $datainizio=$dateiniziofine[0]['data_inizio'];
                $datafine=$dateiniziofine[0]['data_fine'];

                $dataultimoaccesso=$this->dataUltimoAccesso($record->id_corso,$record->id_anagrafica);
                $percentualecompletamento=$this->percentualeCompletamento($record->id_corso,$record->id_anagrafica);


                $query = 'INSERT into #__gg_view_carige_learning_batch (id_corso, id_user, data_primo_accesso, data_ultimo_accesso, data_completamento_edizione,percentuale_completamento,timestamp) VALUES (';
                $query=$query. $record->id_corso . ',' . $record->id_utente . ',\'' . $datainizio .'\',\'' . $dataultimoaccesso. '\',\'' . $datafine. '\','.$percentualecompletamento.', NOW()) 
                ON DUPLICATE KEY UPDATE data_primo_accesso=\''.$datainizio.'\', data_ultimo_accesso=\''.$dataultimoaccesso.'\', data_completamento_edizione=\''.$datafine.'\', percentuale_completamento='.$percentualecompletamento;

               // DEBUGG::log('inserisco '.$query, 'insertData',0,1,0);
                $this->_db->setQuery($query);
                $this->_db->execute();



            }

            DEBUGG::log('CARIGE BATCH elaborati '.count($datausercorsi).' record per syncUserCorso', 'insertData',0,1,0);

            return;

        }
        catch (Exception $e)
        {
            DEBUGG::log($e->getMessage(), 'CARIGE BATCH insert syncUserCorso',0,1,0);
        }



    }

    private function dataPrimoAccessoDataFine($id,$id_anagrafica){

        try {
            $query = "select v.data_inizio as data_inizio,v.data_fine as data_fine from #__gg_view_stato_user_corso as v where v.id_corso=".$id." and v.id_anagrafica=".$id_anagrafica;

            $this->_db->setQuery($query);

            $result = $this->_db->loadAssocList();

            return $result;

        }catch (Exception $e)
        {
            DEBUGG::log($e, 'dataPrimoAccessoDataFine',0,1,1);
        }

    }

    private function dataUltimoAccesso($id_corso,$id_anagrafica){
        try {
            $query = "select data from #__gg_report as v where v.id_corso=".$id_corso." and v.id_anagrafica=".$id_anagrafica." order by data desc limit 1";
            $this->_db->setQuery($query);
            $result = $this->_db->loadResult();
            return $result;

        }catch (Exception $e)
        {
            DEBUGG::log($e, 'dataUltimoAccesso',0,1,1);
        }




    }

    public function percentualeCompletamento($id_corso,$id_anagrafica){
        try {
            $reportModel=new gglmsModelReport();
            $numerocontenuti=count($reportModel->getContenutiArrayList($id_corso));
            $query = "select count(*) from #__gg_report where id_corso=".$id_corso." and stato=1 and id_anagrafica=".$id_anagrafica;
            $this->_db->setQuery($query);
            $result = $this->_db->loadResult();

            return ($result/$numerocontenuti)*100;

        }catch (Exception $e)
        {
            DEBUGG::log($e, 'dataPrimoAccessoDataFine',0,1,1);
        }


    }



}
