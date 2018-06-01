<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
require_once JPATH_COMPONENT . '/models/report.php';

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerApi extends JControllerLegacy
{
    private $_japp;
    public  $_params;
    protected $_db;
    private $_filterparam;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();
        $this->_db = JFactory::getDbo();

        $this->_filterparam = new stdClass();

        $this->_filterparam->corso_id = JRequest::getVar('corso_id');
        $this->_filterparam->current = JRequest::getVar('current');
        $this->_filterparam->rowCount = JRequest::getVar('rowCount');
        $this->_filterparam->startdate= JRequest::getVar('startdate');
        $this->_filterparam->finishdate = JRequest::getVar('finishdate');
        $this->_filterparam->filterstato = JRequest::getVar('filterstato');
        $this->_filterparam->usergroups = JRequest::getVar('usergroups');
        $this->_filterparam->sort = JRequest::getVar('sort');
        $this->_filterparam->searchPhrase = JRequest::getVar('searchPhrase');
        $this->_filterparam->csvlimit = JRequest::getVar('csvlimit');
        $this->_filterparam->csvoffset=JRequest::getVar('csvoffset');
        $this->_filterparam->id_chiamata=JRequest::getVar('id_chiamata');
        $this->_filterparam->to=JRequest::getVar('to');
        $this->_filterparam->oggettomail=JRequest::getVar('oggettomail');
        $this->_filterparam->testomail=JRequest::getVar('testomail');
        $this->_filterparam->tipo_report=JRequest::getVar('tipo_report');
        $this->_filterparam->limit=JRequest::getVar('limit');
        $this->_filterparam->offset=JRequest::getVar('offset');

    }

    public function get_report(){

        $data = $this->new_get_data();
        //$data = $this->get_data();
        echo  json_encode($data);
        $this->_japp->close();
    }

    private function  new_get_data($offsetforcsv=null) {

        //$this->_filterparam->task = JRequest::getVar('task');
        //FILTERSTATO: 2=TUTTI 1=COMPLETATI 0=SOLO NON COMPLETATI 3=IN SCADENZA
        $id_corso=explode('|', $this->_filterparam->corso_id)[0];
        $id_contenuto=explode('|', $this->_filterparam->corso_id)[1];
        $alert_days_before=$this->_params->get('alert_days_before');
        $tipo_report=$this->_filterparam->tipo_report;
        $offset=$this->_filterparam->offset;
        $limit=$this->_filterparam->limit;
        $filters=array('startdate'=>$this->_filterparam->startdate,'finishdate'=>$this->_filterparam->finishdate,'filterstato'=>$this->_filterparam->filterstato,'searchPhrase'=>$this->_filterparam->searchPhrase,'usergroups'=>$this->_filterparam->usergroups);

        try {


            $columns=array();
            switch ($tipo_report){

                case 0: //PER CORSO
                    $query = $this->_db->getQuery(true);
                    $query->select('vista.id_anagrafica as id_anagrafica, vista.stato as stato, vista.data_inizio as data_inizio, vista.data_fine as data_fine , IF(date(now())>DATE_ADD((select data_fine from #__gg_unit where id='.$id_corso.'), INTERVAL -'.$alert_days_before.' DAY), IF(stato=0,1,0),0) as scadenza');
                    $query->from('#__gg_view_stato_user_corso  as vista');
                    $query->where('id_corso='.$id_corso);
                    switch ($filters['filterstato']){

                        case 0: //qualsiasi stato
                            $arrayresult=$this->buildGeneralDataCubeUtentiInCorso($id_corso,$offset,$limit,$filters['searchPhrase'],$filters['usergroups']);
                            $users=$arrayresult[0];
                            $count=$arrayresult[1];
                            $queryGeneralCube=$arrayresult[2];
                            $queryGeneralCubeCount=$arrayresult[3];
                            $result['secondaryCubeQuery']=(string)$query;
                            $datas=$this->buildPrimaryDataCube($query);
                            $users=$this->addColumn($users,$datas,"id_anagrafica",null, "stato",'outer');
                            $users=$this->addColumn($users,$datas,"id_anagrafica",null, "data_inizio",'outer');
                            $users=$this->addColumn($users,$datas,"id_anagrafica",null, "data_fine",'outer');
                            $users=$this->addColumn($users,$datas,"id_anagrafica",null, "scadenza",'outer');
                            $columns=array('id_anagrafica','cognome','nome','stato','data_inizio','data_fine','scadenza','fields');
                            $rows=$users;
                            break;

                        case 1:
                        case 2:
                        case 3:
                            if ($filters['filterstato']==1)
                                $query->where("vista.stato=1");
                            if ($filters['filterstato']==2)
                                $query->where("vista.stato=0");
                            if($filters['filterstato']==3)
                                $query->where("vista.stato=0 and IF(date(now())>DATE_ADD((select data_fine from #__gg_unit where id=".$id_corso."), INTERVAL -".$alert_days_before." DAY), IF(stato=0,1,0),0)=1");


                            if($filters['startdate']!=null)
                                $query->where("vista.data_fine>'".$filters['startdate']."'");
                            if ($filters['finishdate']!=null)
                                $query->where("vista.data_fine<'".$filters['finishdate']."'");

                            if($filters['searchPhrase']!=null) {
                                $query->where('id_anagrafica in (select anagrafica.id from #__gg_report_users as anagrafica where anagrafica.cognome LIKE \'%' . $filters['searchPhrase'] . '%\')');

                            }
                            $result['secondaryCubeQuery']=(string)$query;
                            $count=$this->countPrimaryDataCube($query);
                            $datas=$this->buildPrimaryDataCube($query,$offset,$limit);

                            $arrayresult=$this->buildGeneralDataCubeUtentiInCorso($id_corso,null,null,$filters['searchPhrase'],$filters['usergroups'],implode(",",(array_column($datas,"id_anagrafica"))));
                            $users=$arrayresult[0];

                            $queryGeneralCube=$arrayresult[2];
                            $queryGeneralCubeCount=$arrayresult[3];
                            $datas=$this->addColumn($datas,$users,"id_anagrafica",null, "nome",'inner');
                            $datas=$this->addColumn($datas,$users,"id_anagrafica",null, "cognome",'inner');
                            $datas=$this->addColumn($datas,$users,"id_anagrafica",null, "fields",'inner');
                            $rows=$datas;

                            $columns=array('id_anagrafica','cognome','nome','stato','data_inizio','data_fine','scadenza','fields');

                            break;


                    }

                   break;



                case 1: //PER UNITA'
                    $arrayresult=$this->buildGeneralDataCubeUtentiInCorso($id_corso,$offset,$limit,$filters['searchPhrase'],$filters['usergroups']);
                    $users=$arrayresult[0];
                    $count=$arrayresult[1];
                    $queryGeneralCube=$arrayresult[2];
                    $queryGeneralCubeCount=$arrayresult[3];
                    $query = $this->_db->getQuery(true);
                    $query->select('vista.id_anagrafica as id_anagrafica, u.id as id_unita,u.titolo as titolo_unita, vista.stato as stato, vista.data_inizio as data_inizio, vista.data_fine as data_fine');
                    $query->from('#__gg_view_stato_user_unita  as vista');
                    $query->join('inner','#__gg_unit as u on vista.id_unita=u.id');
                    $query->where('id_corso='.$id_corso);
                    $result['secondaryCubeQuery']=(string)$query;
                    $datas=$this->buildPrimaryDataCube($query);
                    $users=$this->addColumn($users,$datas,"id_anagrafica","titolo_unita", "stato",'outer');
                    $columns=$this->buildColumnsforUnitaView($id_corso);
                    $rows=$users;
                    break;
                case 2://PER CONTENUTO
                    $arrayresult=$this->buildGeneralDataCubeUtentiInCorso($id_corso,$offset,$limit,$filters['searchPhrase'],$filters['usergroups']);
                    $users=$arrayresult[0];
                    $count=$arrayresult[1];
                    $queryGeneralCube=$arrayresult[2];
                    $queryGeneralCubeCount=$arrayresult[3];

                    $query = $this->_db->getQuery(true);
                    $query->select('vista.id_anagrafica as id_anagrafica, c.id as id_contenuto,c.titolo as titolo_contenuto, vista.stato as stato');
                    $query->from('#__gg_report  as vista ');
                    $query->join('inner','#__gg_contenuti as c on vista.id_contenuto=c.id');
                    $query->where('id_corso='.$id_corso);
                    $result['secondaryCubeQuery']=(string)$query;
                    $datas=$this->buildPrimaryDataCube($query);
                    $users=$this->addColumn($users,$datas,"id_anagrafica","titolo_contenuto", "stato",'outer');
                    $columns=$this->buildColumnsforContenutiView($id_corso);
                    $rows=$users;
                    break;


            }



            $rows=$this->buildPivot($rows,$columns,"");


        }catch (Exception $e){

            DEBUGG::log('ERRORE DA GETDATA:' . json_encode($e->getMessage()),'ERRORE DA GET DATA',1,1);
            //DEBUGG::error($e, 'error', 1);
        }

        $result['queryGeneralCube']=(string)$queryGeneralCube;

        //$result['offset']=$offset;
        //$result['current']=$this->_filterparam->current;
        $result['columns']=$columns;
        $result['rowCount']=$count;
        $result['rows']=$rows;
        //$result['total']=$total;
        $result['totalquery']=(string)$queryGeneralCubeCount;
        //echo json_encode($result);
        return $result;
    }

    private function buildGeneralDataCubeUtentiInCorso($id_corso,$offset,$limit,$searchPrase,$usergroups,$anagrafica_filter=null)
    {
        try {
            $query = $this->_db->getQuery(true);
            $query->select("accesso");
            $query->from("#__gg_unit as u");
            $query->where('u.id=' . $id_corso);
            $this->_db->setQuery($query);
            $accesso = $this->_db->loadResult();

            $query = $this->_db->getQuery(true);
            $countquery = $this->_db->getQuery(true);
            $query->select("anagrafica.id as id_anagrafica, anagrafica.cognome as cognome, anagrafica.nome as nome, anagrafica.fields as fields");
            $countquery->select("count(*)");
            $query->from("#__gg_report_users as anagrafica");
            $countquery->from("#__gg_report_users as anagrafica");

            //INNER E CONDIZIONI SULLA BASE DELL'ACCESSO
            switch ($accesso) {

                case 'coupon':
                    $query->join('inner', '#__gg_coupon as c on c.id_utente=anagrafica.id_user');
                    $countquery->join('inner', '#__gg_coupon as c on c.id_utente=anagrafica.id_user');
                    $query->where('c.corsi_abilitati like '. $id_corso);
                    $countquery->where('c.corsi_abilitati like '. $id_corso);
                    break;

                default:
                    $query->join('inner', '#__gg_unit as u on anagrafica.id_event_booking=u.id_event_booking');
                    $countquery->join('inner', '#__gg_unit as u on anagrafica.id_event_booking=u.id_event_booking');
                    $query->where('u.id=' . $id_corso);
                    $countquery->where('u.id=' . $id_corso);
                    break;
            }

            if($usergroups!=null) {
                $query->join('inner', '#__user_usergroup_map as um on anagrafica.id_user=um.user_id');
                $countquery->join('inner', '#__user_usergroup_map as um on anagrafica.id_user=um.user_id');
            }

            //$query->where('anagrafica.id=11497');
            if($searchPrase!=null) {
                $query->where('anagrafica.fields LIKE \'%' . $searchPrase . '%\'');
                $countquery->where('anagrafica.fields LIKE \'%' . $searchPrase . '%\'');
            }
            if($usergroups!=null) {
                $query->where('um.group_id=' . $usergroups);
                $countquery->where('um.group_id=' . $usergroups);
            }

            if($anagrafica_filter!=null) {
                $query->where('anagrafica.id in(' . $anagrafica_filter . ')');
                $countquery->where('anagrafica.id in(' . $anagrafica_filter . ')');
            }
            $query->order('anagrafica.cognome', 'asc');
            $query->setlimit($offset, $limit);
            $this->_db->setQuery($query);
            $rows = $this->_db->loadAssocList();
            foreach ($rows as &$row){//FILTRO PER CAMPI DI FIELDS
                $row['fields']=json_decode($row['fields']);
                unset($row['fields']->password);
                $row['fields']=json_encode($row['fields']);
            }
            $this->_db->setQuery($countquery);
            $count = $this->_db->loadResult();
            return[$rows,$count,$query,$countquery];
        } catch (Exception $e) {

            DEBUGG::log('ERRORE DA GETDATA:' . json_encode($e->getMessage()), 'ERRORE DA GET DATA', 1, 1);
            //DEBUGG::error($e, 'error', 1);
        }
    }
    private function buildPrimaryDataCube($query,$offset=null,$limit=null){
        try {


            $this->_db->setQuery($query);

            $query->setLimit($offset, $limit);

            $datas = $this->_db->loadAssocList();
            return $datas;

        }catch (Exception $e) {

            DEBUGG::log('ERRORE DA GETDATA:' . json_encode($e->getMessage()), 'ERRORE DA GET DATA', 1, 1);
            //DEBUGG::error($e, 'error', 1);
        }
    }
    private function countPrimaryDataCube($query){
        try {

            $this->_db->setQuery($query);
            $datas = $this->_db->loadAssocList();
            return count($datas);

        }catch (Exception $e) {

            DEBUGG::log('ERRORE DA GETDATA:' . json_encode($e->getMessage()), 'ERRORE DA GET DATA', 1, 1);
            //DEBUGG::error($e, 'error', 1);
        }

    }
    private  function buildColumnsforUnitaView($id_corso){

        $reportObj=new gglmsModelReport();
        $unitas=$reportObj->getSottoUnitaArrayList($id_corso);
        $columns=['id_anagrafica','cognome','nome','fields'];
        foreach ($unitas as $unita) {
            array_push($columns, $unita['titolo']);
        }
        return $columns;
    }
    private  function buildColumnsforContenutiView($id_corso){

        $reportObj=new gglmsModelReport();
        $contenuti=$reportObj->getContenutiArrayList($id_corso);

        $columns=['id_anagrafica','cognome','nome','fields'];
        foreach ($contenuti as $contenuto) {
            array_push($columns, $contenuto['titolo']);
        }
        return $columns;
    }
    private function buildPivot($basearray,$columns,$nullvalue){

        $table=array();
        foreach ($basearray as $item){

            foreach ($columns as $column){


                if(isset($item[$column])) {
                    $row{$column} = $item[$column];
                }else{
                    $row{$column} =$nullvalue;
                }
            }
            array_push($table,$row);

        }
        return $table;
    }
    private function addColumn($basearray,$arraytoadd,$key, $newcolumname, $columnvalue, $typeofjoin){

        $innerjoinedarray=array();
        foreach ($arraytoadd as $newitem){

            foreach ($basearray as &$item){

                if($newitem[$key]==$item[$key]) {

                    if ($newcolumname!=null){
                        $item{$newitem[$newcolumname]} = $newitem[$columnvalue];
                        array_push($innerjoinedarray,$item);
                    }else{
                        $item{$columnvalue} = $newitem[$columnvalue];
                        array_push($innerjoinedarray,$item);

                    }
               }
                 unset($item);
            }
        }
        if($typeofjoin=='inner') {

            return $innerjoinedarray;
        }else{

            return $basearray;

        }
    }
    private function  get_data($offsetforcsv=null) {

        $this->_filterparam->task = JRequest::getVar('task');
        //FILTERSTATO: 2=TUTTI 1=COMPLETATI 0=SOLO NON COMPLETATI 3=IN SCADENZA
        $id_corso=explode('|', $this->_filterparam->corso_id)[0];
        $id_contenuto=explode('|', $this->_filterparam->corso_id)[1];
        $alert_days_before=$this->_params->get('alert_days_before');


        try {

            $query = $this->_db->getQuery(true);
            $countquery= $this->_db->getQuery(true);

            if($this->_filterparam->filterstato==1) {
                $query->select('r.id_utente , anagrafica.nome, anagrafica.cognome,anagrafica.fields, users.email');

            }else{
                $query->select('DISTINCT r.id_utente , anagrafica.nome, anagrafica.cognome,anagrafica.fields, users.email');
            }
            //SELECT COUNT PER LA COUNTQUERY
            if($this->_filterparam->filterstato==1) {
                $countquery = 'select count(*) ';
            }else{
                $countquery = 'SELECT count(DISTINCT r.id_utente , anagrafica.nome, anagrafica.cognome, anagrafica.fields,users.email) ';
            }

            //DISTINZIONE PER DEFINIZIONE VALORE DI STATO
            if($this->_filterparam->filterstato==1) {
                $query->select('1 as stato');
            }else{
                $query->select('COALESCE((select r2.stato from #__gg_report as r2 where r2.id_utente = r.id_utente and id_corso = '.$id_corso.' and id_contenuto= '.$id_contenuto.' and stato = 1 limit 1),0) 
                                as stato');
            }

            // SUBQUERY COMUNI A TUTTE E LE QUERY
            $query->select('(select r1.data from #__gg_report as r1 where r1.id_utente = r.id_utente and id_corso = '.$id_corso.' 
               and r1.data<>\'0000-00-00\' ORDER BY r1.data  limit 1) as hainiziato,
                                (select r2.data from #__gg_report as r2 where r2.id_utente = r.id_utente and id_corso = '.$id_corso.' and 
                                id_contenuto= '. $id_contenuto. ' and stato = 1 ORDER BY r2.data limit 1) as hacompletato');


            //SUBQUERY PER SOMME TEMPI DI COMPLETAMENTO
            $param_colonne_somme=$this->_params->get('colonne_somme_tempi');

            if($param_colonne_somme) {
                $query->select($this->buildSelectColonneTempi($id_corso));
            }

            //DISTINZIONE PER DEFINIZIONE VALORE DI ALERT
            if($this->_filterparam->filterstato==1){
                $query->select('0 as alert');
            }else{
                $query->select('IF(date(now())>DATE_ADD(un.data_fine, INTERVAL -'.$alert_days_before.' DAY),	IF((select r2.stato from #__gg_report as r2 where r2.id_utente = r.id_utente 
                                and id_contenuto='.$id_contenuto.' and stato = 1 limit 1),0,1),0) as alert');
            }

            // FINE DELLA SELECT INIZIO FROM - INNER JOIN

            //FROM E JOIN COMUNI A TUTTE LE QUERY
            $query->from('#__gg_report as r');
            $query->join('inner', '#__gg_report_users as anagrafica ON anagrafica.id = r.id_anagrafica');
            $query->join('inner','#__users as users on r.id_utente=users.id');

            //SE NON SONO COMPLETI ALLORA BISOGNA RECUPERARE LA DATA DI SCADENZA: JOIN
            if($this->_filterparam->filterstato != 1) {
                $query->join('inner', '#__gg_unit as un on r.id_corso=un.id');
            }

            //FINE FROM - JOIN INIZIO WHERE

            //WHERE COMUNE A TUTTE LE QUERY
            $query->where('r.id_corso = '.$id_corso);//id_corso, primo paramentro in combo
            if ($this->_filterparam->usergroups) {
                $query->join('inner', '#__user_usergroup_map as gruppo  ON gruppo.user_id = r.id_utente');
                $query->where('group_id = ' . $this->_filterparam->usergroups );
            }

            //WHERE DISTINTE IN BASE AI FILTERSTATE, PER TUTTI VA BENE COSI'
            if ($this->_filterparam->filterstato == 1)
                $query->where('r.stato = ' . $this->_filterparam->filterstato . ' and  r.id_contenuto='.$id_contenuto);

            if($this->_filterparam->filterstato == 3)
                $query->where('date(now())>DATE_ADD(un.data_fine, INTERVAL -'.$alert_days_before.' DAY)');
            //SOLO NON COMPLETATI O IN SCADENZA
            if ($this->_filterparam->filterstato == 0 || $this->_filterparam->filterstato == 3)
                $query->where('r.id_utente NOT IN (SELECT r.id_utente FROM #__gg_report as r 
                               INNER JOIN #__user_usergroup_map as gruppo  ON gruppo.user_id = r.id_utente
                               WHERE r.id_corso = '.$id_corso.' AND r.stato = 1 
                               and  r.id_contenuto='.$id_contenuto.' AND group_id = '.$this->_filterparam->usergroups.')');

            //FILTRI DA REPORT
            if ($this->_filterparam->startdate)
                $query->where(' (select r2.data from #__gg_report as r2 where r2.id_utente = r.id_utente and id_contenuto= '. $id_contenuto. ' and stato = 1 
                ORDER BY r2.data limit 1) >= "' . $this->_filterparam->startdate . '"');

            if ($this->_filterparam->finishdate)
                $query->where('(select r2.data from #__gg_report as r2 where r2.id_utente = r.id_utente and id_contenuto= '. $id_contenuto. ' and stato = 1 
                ORDER BY r2.data limit 1) <= "' . $this->_filterparam->finishdate . '"');

            if ($this->_filterparam->searchPhrase)
                $query->where('concat(nome,cognome,fields) like "%'. $this->_filterparam->searchPhrase .'%"');

            $offset=0;
            $csvoffset=$this->_filterparam->csvoffset;
            $csvlimit=$this->_filterparam->csvlimit;
            if($this->_filterparam->task != 'get_csv' ) {
                $offset = $this->_filterparam->rowCount * $this->_filterparam->current - $this->_filterparam->rowCount;
                $query->setLimit($this->_filterparam->rowCount, $offset);
            }else {
                if ($csvlimit==0){
                    $query->setLimit(1);
                }else
                {
                    $query->setLimit($csvoffset, $csvlimit - $csvoffset);
                }

            }

            $total=null;
            $countquery=$countquery.$query->from;
            $countquery=$countquery.(is_array($query->join)?implode($query->join):$query->join);
            $countquery=$countquery.(is_array($query->where)?implode($query->where):$query->where);

            if ($this->_filterparam->sort && $this->_filterparam->filterstato == 1) {
                foreach ($this->_filterparam->sort as $key => $value)
                    $query->order($key . " " . $value);

            }
            //echo $query;
            $this->_db->setQuery($query);
            $rows = $this->_db->loadAssocList();
            $this->_db->setQuery($countquery);
            $total=$this->_db->LoadResult();



        }catch (Exception $e){

            DEBUGG::log('ERRORE DA GETDATA:' . json_encode($e->getMessage()),'ERRORE DA GET DATA',1,1);
            //DEBUGG::error($e, 'error', 1);
        }

        $result['query']=(string)$query;
        $result['offset']=$offset;
        $result['current']=$this->_filterparam->current;
        $result['rowCount']=10;
        $result['rows']=$rows;
        $result['total']=$total;
        $result['totalquery']=$countquery;
        return $result;
    }
    public function get_csv()
    {
        //ini_set('max_execution_time', 600);
        $this->_japp = JFactory::getApplication();
        //$csvlimit=$this->_filterparam->csvlimit;
        $csvlimit=1;
        $id_chiamata=$this->_filterparam->id_chiamata;
        $data=$this->new_get_data();
        $this->createCSV($data['rows'],$id_corso=explode('|', $this->_filterparam->corso_id)[0]);



        /*
        if($csvlimit>0) { //COSI' LA PRIMA CHIAMATA, PER IL TOTALE, NON GENERA RECORD
            foreach ($data['rows'] as $row) {

                try {

                    $insertquery = "INSERT INTO #__gg_csv_report VALUES (";
                    $insertquery = $insertquery . $id_chiamata . ",";
                    $insertquery = $insertquery . $row['id_utente'] . ",";
                    $insertquery = $insertquery . "'" . addslashes($row['nome']) . "',";
                    $insertquery = $insertquery . "'" . addslashes($row['cognome']) . "',";
                    $insertquery = $insertquery . "'" . addslashes($row['fields']) . "',";
                    $insertquery = $insertquery . "'" . $row['email'] . "',";
                    $insertquery = $insertquery . $row['stato'] . ",";
                    $insertquery = $insertquery . "'" . $row['hainiziato'] . "',";
                    $insertquery = $insertquery . "'" . $row['hacompletato'] . "',";


                    if($param_colonne_somme) {
                        $insertquery = $insertquery . "'" . $row['tempo_lavorativo'] . "',";
                        $insertquery = $insertquery . "'" . $row['tempo_straordinario'] . "',";
                    }else{
                        $insertquery = $insertquery . "null,";
                        $insertquery = $insertquery . "null,";

                    }
                    $insertquery = $insertquery . $row['alert'] . ")";



                    $this->_db->setQuery($insertquery);
                    $this->_db->execute();
                }catch (Exception $exception){
                    echo $exception->getMessage();
                }

            }

        }

        echo  json_encode($data);
*/
        //$this->_japp->close();
    }
    public function createCSV($rows, $corso_id){

        $elenco_campi_per_csv_da_back_end = explode(',', $this->_params->get('campi_csv'));
        if($elenco_campi_per_csv_da_back_end[0]!='no_column') {
            $added_colums_rows = [];
            //var_dump($elenco_campi_per_csv_da_back_end);
            foreach ($rows as $row) {
                $rowfields = (array)json_decode($row['fields']);

                foreach ($elenco_campi_per_csv_da_back_end as $nuovacolonna => $nuovovalore) {
                    if(array_key_exists($nuovovalore,$rowfields))
                        $row[$nuovovalore]=$rowfields[$nuovovalore] ;
                }
                array_push($added_colums_rows, $row);
            }
            $rows = $added_colums_rows;
        }

        $csv_row_filters=['fields'];//CAMPI DA NON MOSTRARE NEL CSV
       foreach ($rows as &$row) {
            foreach ($csv_row_filters as $csv_row_filter){
                unset($row[$csv_row_filter]);
            }
        }
        try {
            if (!empty($rows)) {
                $comma = ';';
                $quote = '"';
                $CR = "\015\012";
                // Make csv rows for field name
                $i = 0;
                $fields = $rows[0];

                $cnt_fields = count($fields);
                $csv_fields = '';

                foreach ($fields as $name => $val) {
                    $i++;
                    if ($cnt_fields <= $i) $comma = '';
                    $csv_fields .= $quote . $name . $quote . $comma;


                }

                // Make csv rows for data
                $csv_values = '';
                foreach ($rows as $row_) {
                    $i = 0;
                    $comma = ';';
                    foreach ($row_ as $name => $val) {
                        $i++;
                        if ($cnt_fields <= $i) $comma = '';
                        $csv_values .= $quote . $val . $quote . $comma;
                    }
                    $csv_values .= $CR;
                }

                //echo ($csv_values);

                $csv_save = $csv_fields . $CR . $csv_values;
            }
            echo $csv_save;


            $filename = $this->get_CourseName($corso_id);

            $filename = preg_replace('~[^\\pL\d]+~u', '_', $filename);
            $filename = iconv('utf-8', 'us-ascii//TRANSLIT', $filename);
            $filename = strtolower($filename);
            $filename = trim($filename, '_');
            $filename = preg_replace('~[^-\w]+~', '', $filename);
            $filename .= "-" . date("d/m/Y");
            $filename = $filename . ".csv";

            //var_dump($filename);die;


            header("Content-Type: text/plain");
            header("Content-disposition: attachment; filename=$filename");
            header("Content-Transfer-Encoding: binary");
            header("Pragma: no-cache");
            header("Expires: 0");
        }catch (exceptions $exception){
            echo $exception->getMessage();
        }
        $this->_japp->close();
    }

    private  function get_CourseName($corso_id){
        $query = $this->_db->getQuery(true);
        $query->select('titolo');
        $query->from('#__gg_unit as u');
        $query->where('u.id= '.$corso_id);
        $this->_db->setQuery($query);
        $titolo = $this->_db->loadResult();

        return $titolo;
    }

    public function buildDettaglioCorso(){

        $id_utente=(int)$_GET['id_utente'];
        $id_corso=(int)$_GET['id_corso'];
        $query = $this->_db->getQuery(true);
        $query->select('u.titolo as \'titolo unità\',c.titolo as \'titolo contenuto\', 
                      IF (r.stato=1, \'completato\',\'non completato\') as \'stato\', r.`data` as \'data\'');
        $query->from('#__gg_report as r');
        $query->join('inner','#__gg_unit as u on r.id_unita=u.id');
        $query->join('inner','#__gg_contenuti c on r.id_contenuto=c.id');
        $query->join('inner','#__gg_unit_map um on c.id=um.idcontenuto');
        $query->where('id_utente='.$id_utente);
        $query->where('id_corso='.$id_corso);
        $query=$query." order by u.ordinamento, u.id, um.ordinamento ,r.`data`";
        $this->_db->setQuery($query);
        $result = $this->_db->loadAssocList();
        echo  json_encode($result);
        $this->_japp->close();
    }

    public function sendMail(){
        try {
        $to=(string)$this->_filterparam->to;

        //$to="a.petruzzella71@gmail.com";
        $oggettomail=$this->_filterparam->oggettomail;
        $testomail=$this->_filterparam->testomail;
        $recipients=array();
        array_push($recipients,$to);
        $mailer = JFactory::getMailer();
        $config = JFactory::getConfig();
        $sender = array(
            $config->get( 'mailfrom' ),
            $config->get( 'fromname' )
        );

        $mailer->setSender($sender);

        $mailer->addRecipient($recipients);
        $mailer->setSubject($oggettomail);
        $mailer->setBody($testomail);

        $send = $mailer->Send();

        echo json_encode($send);
       }catch (exceptions $exception){
           echo $exception->getMessage();

       }
        $this->_japp->close();
    }

    public function sendAllMail(){
    try{
        $id_corso=explode('|', $this->_filterparam->corso_id)[0];
        $id_contenuto=explode('|', $this->_filterparam->corso_id)[1];
        $group_id=$this->_filterparam->usergroups;
        $alert_days_before=$this->_params->get('alert_days_before');
        $query = $this->_db->getQuery(true);
        $query->select('DISTINCT users.email');
        $query->from('#__gg_report as r');
        $query->join('inner','#__gg_report_users as anagrafica ON anagrafica.id = r.id_anagrafica');
        $query->join('inner','#__users as users on r.id_utente=users.id');
        $query->join('inner','#__gg_unit as un on r.id_corso=un.id');
        $query->join('inner','#__user_usergroup_map as gruppo  ON gruppo.user_id = r.id_utente');
        $query->where('id_corso='.$id_corso);
        $query->where('group_id='.$group_id);
        $query->where('IF(date(now())>DATE_ADD(un.data_fine, INTERVAL -'.$alert_days_before.' DAY),	IF((select r2.stato from #__gg_report as r2 where r2.id_utente = r.id_utente 
                                and id_contenuto='.$id_contenuto.' and stato = 1 limit 1),0,1),0)=1');
        $query->where('r.id_utente NOT IN (SELECT r.id_utente FROM #__gg_report as r INNER JOIN  #__user_usergroup_map as gruppo  ON gruppo.user_id = r.id_utente
                               WHERE r.id_corso = '.$id_corso.' AND r.stato = 1 and  r.id_contenuto='.$id_contenuto.' AND group_id = '.$group_id.')');



        $this->_db->setQuery($query);
        $rows = $this->_db->loadAssocList();

        $oggettomail=$this->_filterparam->oggettomail;
        $testomail=$this->_filterparam->testomail;
        $recipients=array_column($rows,'email');


        $mailer = JFactory::getMailer();
        $config = JFactory::getConfig();
        $sender = array(
            $config->get( 'mailfrom' ),
            $config->get( 'fromname' )
        );

        $mailer->setSender($sender);
#@_
        $mailer->addRecipient($recipients);
        $mailer->setSubject($oggettomail);
        $mailer->setBody($testomail);

        //$send = $mailer->Send();  ATTENZIONE IL VERO INVIO E' DISABILITATO IN PROVA

        //echo $send;
    }catch (exceptions $exception){
echo $exception->getMessage();

}
$this->_japp->close();

    }

    private function buildSelectColonneTempi($id_corso){

        $orario_inizio='09:00:00';
        $orario_fine='17:00:00';
        $select_somma_log_fascia_int='(select SEC_TO_TIME((select IFNULL(sum(permanenza),0) from #__gg_log as l where l.id_utente=r.id_utente and  l.id_contenuto in (select id_contenuto from #__gg_report where id_utente=l.id_utente and id_corso=' . $id_corso . ') and TIME(l.data_accesso)>\''.$orario_inizio.'\' and TIME(l.data_accesso)<\''.$orario_fine.'\')';
        $select_somma_log_fascia_est='(select SEC_TO_TIME((select IFNULL(sum(permanenza),0) from #__gg_log as l where l.id_utente=r.id_utente and  l.id_contenuto in (select id_contenuto from #__gg_report where id_utente=l.id_utente and id_corso=' . $id_corso . ') and (TIME(l.data_accesso)<\''.$orario_inizio.'\' or TIME(l.data_accesso)>\''.$orario_fine.'\')';


        $sub_query = 'select distinct c.id_quizdeluxe from #__gg_contenuti as c inner join #__gg_report as r on c.id=r.id_contenuto where c.tipologia = 7 AND id_corso=' . $id_corso;
        $this->_db->setQuery($sub_query);
        $id_quizzes = $this->_db->loadAssocList();

        $in_quizzes = '';
        if (count($id_quizzes) > 0){
            foreach ($id_quizzes as $id_quiz){

                $in_quizzes = $in_quizzes . ',' . $id_quiz['id_quizdeluxe'];
            }
            $in_quizzes = substr($in_quizzes, 1);
            //echo $in_quizzes;
            $select_somma_quiz_fascia_int='(select ifnull(sum(c_total_time),0) from #__quiz_r_student_quiz where c_quiz_id in (' . $in_quizzes . ') and c_student_id=r.id_utente and TIME(c_date_time) > \''.$orario_inizio.'\' and TIME(c_date_time) < \''.$orario_fine.'\')))';
            $select_somma_quiz_fascia_est='(select ifnull(sum(c_total_time),0) from #__quiz_r_student_quiz where c_quiz_id in (' . $in_quizzes . ') and c_student_id=r.id_utente and (TIME(c_date_time) < \''.$orario_inizio.'\' or TIME(c_date_time) > \''.$orario_fine.'\')))))';

            $select_result=$select_somma_log_fascia_int.' + '.$select_somma_quiz_fascia_int.'as tempo_lavorativo, '.$select_somma_log_fascia_est.'+'.$select_somma_quiz_fascia_est.' as tempo_straordinario';
        }else{

            $select_result=$select_somma_log_fascia_int.'))as tempo_lavorativo,'.$select_somma_log_fascia_est.'))) as tempo_straordinario';

        }

    return $select_result;







}
    public function  getContenuti(){//CONTROLLER CHE SERVE A VEDERSI STAMPARE L'ELENCO DI TUTTI I CONTENUTI DI UNA UNITA E SOTTO UNITA'. NON VIENE USATO IN ALCUNA VISTA

        $id = JRequest::getVar('corso_id');
        $reportModel = new gglmsModelReport();
        $contenuti=$reportModel->getContenutiArrayList($id);
        echo implode('<br>',array_column($contenuti,'titolo'));
        echo '<br> '.count($contenuti);
    }
//	INUTILIZZATO
//	public function getSummarizeCourse(){
//		$query = $this->_db->getQuery(true);
//		$query->select('stato, count(stato) as total ');
//		$query->from('#__gg_report AS r');
//		$query->where("id_contenuto =" . $this->_filterparam->corso_id);
//		$query->group('stato');
//		$this->_db->setQuery($query);
//		$summarize = $this->_db->loadAssocList('stato');
//		return $summarize;
//	}

}
