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
            $result = true;
            DEBUGG::log('FINE PROCEDURA CON TRUE', ' ',0,1,0);

        }else{
            $result= false;
            DEBUGG::log('FINE PROCEDURA CON FALSE', ' ',0,1,0);
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


                $dateiniziofine=$this->dataInizioDataFine('id_unita',$record->id_unita,$record->id_utente);
                $datainizio=$dateiniziofine[0];
                $datafine='null';
                if($datainizio==null)
                    $datainizio='null';

                $completed=0;
                //echo $record->id_utente;

                if($this->isUnitaCompleta($record->id_unita, $record->id_utente)==1) {
                    $completed = 1;
                    $datafine=$dateiniziofine[1];
                }

                $query = 'INSERT into #__gg_view_stato_user_unita (id_anagrafica, id_unita, id_corso, stato, data_inizio, data_fine, timestamp) VALUES (';
                $query=$query. $record->id_anagrafica . ',' . $record->id_unita . ',' . $record->id_corso . ',' . $completed . ',\'' . $datainizio. '\',\'' . $datafine. '\',NOW()) ON DUPLICATE KEY UPDATE stato=' . $completed.' , data_inizio=\''.$datainizio.'\', data_fine=\''.$datafine.'\'';
                //echo $query;
                $this->_db->setQuery($query);
                $this->_db->execute();
          }

            DEBUGG::log('inseriti '.count($datauserunit).' record per INSERT VIEW UNITA ', 'insertData',0,1,0);

            return true;

        }
        catch (Exception $e)
        {
            DEBUGG::log($e, 'insert syncUserUnita',0,1,1);

            return false;
        }
   }

    private function insertViewCorso($datausercorsi){

        // ini_set('max_execution_time', 600);

        try {

            //DEBUGG::log('inserisco '.count($datausercorsi).' record per syncUserCorso', 'insertData',0,1,0);


            foreach ($datausercorsi as $record){

                $dateiniziofine=$this->dataInizioDataFine('id_corso',$record->id_corso,$record->id_utente);
                $datainizio=$dateiniziofine[0];
                $datafine='null';
                if($datainizio==null)
                    $datainizio='null';

                $completed=0;
                $isCorsoCompletoArray=$this->isCorsoCompleto($record->id_corso, $record->id_utente);
                if($isCorsoCompletoArray['isCorsoCompleto']==1){

                    $completed=1;
                    $datafine=$isCorsoCompletoArray['data'];

                }



                $query = 'INSERT into #__gg_view_stato_user_corso (id_anagrafica, id_corso, stato, data_inizio, data_fine,timestamp) VALUES (';
                $query=$query. $record->id_anagrafica . ',' . $record->id_corso . ',' . $completed .',\'' . $datainizio. '\',\'' . $datafine. '\',NOW()) ON DUPLICATE KEY UPDATE stato=' . $completed.' , data_inizio=\''.$datainizio.'\', data_fine=\''.$datafine.'\'';
                //DEBUGG::log('inserisco '.$query, 'insertData',0,1,0);
                $this->_db->setQuery($query);
                $this->_db->execute();
                if($completed==1)
                    DEBUGG::log('anagrafica '.$record->id_anagrafica.' ha completato corso '.$record->id_corso, '',0,1,0);



            }

            DEBUGG::log('elaborati '.count($datausercorsi).' record per INSERT VIEW CORSO', '',0,1,0);

            return true;

        }
        catch (Exception $e)
        {
            DEBUGG::log($e, 'insert syncUserCorso',0,1,0);
            return false;
        }



    }

    private function dataInizioDataFine($type,$id,$id_utente){

        try {
            $query_data_inizio = "select `data` from #__gg_report where id_utente = " . $id_utente . " and " . $type . " = " . $id . " and `data`<>'0000-00-00' ORDER BY `data`  limit 1";
            $query_data_fine = "select `data` from #__gg_report where id_utente = " . $id_utente . " and " . $type . " = " . $id . " and stato=1 and `data`<>'0000-00-00' ORDER BY `data` desc limit 1";
            $this->_db->setQuery($query_data_inizio);
            $data_inizio = $this->_db->loadResult();
            $this->_db->setQuery($query_data_fine);
            $data_fine = $this->_db->loadResult();
            //DEBUGG::log('prendo data inizio '.$query_data_inizio, 'insertData',0,1,0);
            //DEBUGG::log('prendo data fine '.$query_data_fine, 'insertData',0,1,0);
            return [$data_inizio, $data_fine];

        }catch (Exception $e)
        {
            DEBUGG::log($e, 'insert syncUserUnita',0,1,1);
        }

    }
    private function isUnitaCompleta($pk,$userid)
    {

        try {
            $obj = new gglmsModelUnita();
            $obj->getSottoUnitaRic($pk);//CHIAMATA ALLA FUNZIONE RICORSIVA
            $result = $obj->getContenuti($pk);//QUI CARICHIAMO I CONTENUTI ALLA RADICE DELL'UNITA
            if ($result) {

                foreach ($result as $res) {
                    array_push($obj->contenuti, $res); //LA VARIABILE DI CLASSE contenuti E' QUELLA CHE VIENE POPOLATA DALLA RICORSIVA
                }
            }
            foreach ($obj->contenuti as $contenuto) {   //ANALISI DI OGNI CONTENUTO: APPENA NE TROVI UNO NON COMPLETO, ESCI FALSE

                $query = "select stato from #__gg_report where id_utente=" . $userid . " and id_contenuto=" . $contenuto->id;
                //if($pk==121)
                    //DEBUGG::log('considero: '.$query, 'insertData',0,1,0);

                $this->_db->setQuery($query);

                if ($this->_db->loadResult() == 0 || $this->_db->loadResult() == null) {
                    return false;
                }
            }
            return true;
        }catch (Exception $e)
        {
            DEBUGG::log($e, ' isUnitaCompleta',0,1,1);
        }
    }
    private function isCorsoCompleto($pk,$userid){

        try
        {
            $query = $this->_db->getQuery(true)
                ->select('r.stato as stato, r.data as data')
                ->from('#__gg_report as r')
                ->where("r.stato=1 and r.id_utente=".$userid." and id_corso=".$pk." and id_contenuto = (select u.id_contenuto_completamento from #__gg_unit as u where id=".$pk.")");

            $this->_db->setQuery($query);
            $data = $this->_db->loadAssocList();
            if (count($data)>0) {

                return ['isCorsoCompleto' => 1, 'data' => $data[0]['data']];
            }else{
                return ['isCorsoCompleto' => 0, 'data' => null];
            }
        }
        catch (Exception $e)
        {
            DEBUGG::log($e->getMessage(), 'IsCorso',0,1,0);
        }

    }

}
