<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

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


	}

	public function get_report(){
		$data = $this->get_data();
		echo  json_encode($data);
		$this->_japp->close();
	}

	private function get_data() {

		$this->_filterparam->task = JRequest::getVar('task');

		try {
			$query = $this->_db->getQuery(true);
			$query->select('r.id_utente , anagrafica.nome, anagrafica.cognome, r.stato');
			$query->select('(select r1.data from #__gg_report as r1 where r1.id_utente = r.id_utente and id_corso = '. explode('|', $this->_filterparam->corso_id)[0].' 
                ORDER BY r1.data  limit 1) as hainiziato,
                                (select r2.data from #__gg_report as r2 where r2.id_utente = r.id_utente and id_contenuto= '. explode('|', $this->_filterparam->corso_id)[1]. ' and stato = 1 
                ORDER BY r2.data limit 1) as hacompletato');
			$query->select('anagrafica.fields, r.`data`');
			$query->from('#__gg_report as r');
			$query->join('inner', '#__gg_report_users as anagrafica ON anagrafica.id = r.id_anagrafica');
			$query->where('r.id_corso = ' . explode('|', $this->_filterparam->corso_id)[0]);//id_corso, primo paramentro in combo

			if ($this->_filterparam->startdate)
				$query->where('r.data >= "' . $this->_filterparam->startdate . '"');

			if ($this->_filterparam->finishdate)
				$query->where('r.data <= "' . $this->_filterparam->finishdate . '"');

			if ($this->_filterparam->searchPhrase)
				$query->where('concat(nome,cognome,fields) like "%'. $this->_filterparam->searchPhrase .'%"');

			//Solo se completati
            if ($this->_filterparam->filterstato == 2)
                $query->where('r.stato <>1');
			if ($this->_filterparam->filterstato == 1)
				$query->where('r.stato = ' . $this->_filterparam->filterstato . ' and  r.id_contenuto='.explode('|', $this->_filterparam->corso_id)[1]);

            if ($this->_filterparam->filterstato == 0)
                $query->where('r.stato <>1 AND r.id_utente NOT IN (SELECT r.id_utente FROM #__gg_report as r 
                               INNER JOIN #__user_usergroup_map as gruppo  ON gruppo.user_id = r.id_utente
                               WHERE r.id_corso = '. explode('|', $this->_filterparam->corso_id)[0].' AND r.stato = 1 
                               and  r.id_contenuto='.explode('|', $this->_filterparam->corso_id)[1].' AND group_id = '.$this->_filterparam->usergroups.')');

			if ($this->_filterparam->usergroups) {
				$query->join('inner', '#__user_usergroup_map as gruppo  ON gruppo.user_id = r.id_utente');
				$query->where('group_id = ' . $this->_filterparam->usergroups );
			}

            $offset=0;
            if($this->_filterparam->task != 'get_csv' ) {
                $offset = $this->_filterparam->rowCount * $this->_filterparam->current - $this->_filterparam->rowCount;
                $query->setLimit($this->_filterparam->rowCount, $offset);
            }

			$this->_db->setQuery($query);
			$this->_db->execute();
            $total=null;
            $total=$this->getNumRows($query,$this->_filterparam->filterstato)[0];//risultato della query


			if ($this->_filterparam->sort && $this->_filterparam->filterstato == 1) {
				foreach ($this->_filterparam->sort as $key => $value)
					$query->order($key . " " . $value);

			}
            $this->_db->setQuery($query);
			$rows = $this->_db->loadAssocList();

		}catch (Exception $e){

			DEBUGG::error($e, 'error', 1);
		}

		$result['query']=(string)$query;
		$result['offset']=$offset;
		$result['current']=$this->_filterparam->current;
		$result['rowCount']=10;
		$result['rows']=$rows;
		$result['total']=$total;
       	return $result;
	}

	public function getNumRows($query,$filtestate){

        $query=explode("from",strtolower($query)); //spacchetta in base ai from
        if($filtestate==0){//qui prende due tronchi con il from
            $query= 'select count(*) from '.$query[count($query)-2].' from '.$query[count($query)-1];
        }else{//per gli altri filtri basta solo un tronco, l'ultimo
        $query= 'select count(*) from '.$query[count($query)-1];
        }
        $query=explode('limit',strtolower($query))[0]; //poichè nel postback dai numeri di pagina genera il limit, lo tolgo
        $this->_db->setQuery($query);
        $rows=(int)$this->_db->loadResult();
        return [$rows, $query];
    }

	public function get_csv()
	{

		$this->_japp = JFactory::getApplication();
		$data = $this->get_data();

		$rows = $data['rows'];

		if (!empty($rows)) {
			$comma = ';';
			$quote = '"';
			$CR = "\015\012";
			// Make csv rows for field name
			$i=0;
			$fields = $rows[0];
			$cnt_fields = count($fields);
			$csv_fields = '';

			foreach($fields as $name=>$val) {
				$i++;
				if ($cnt_fields<=$i) $comma = '';
				$csv_fields .= $quote.$name.$quote.$comma;

			}
			// Make csv rows for data
			$csv_values = '';
			foreach($rows as $row) {
				$i=0;
				$comma = ';';
				foreach($row as $name=>$val) {
					$i++;
					if ($cnt_fields<=$i) $comma = '';
					$csv_values .= $quote.$val.$quote.$comma;
				}
				$csv_values .= $CR;
			}
			$csv_save = $csv_fields.$CR.$csv_values;
		}
		echo $csv_save;


		$filename = $this->get_CourseName();
		$filename = preg_replace('~[^\\pL\d]+~u', '_', $filename);
		$filename = iconv('utf-8', 'us-ascii//TRANSLIT', $filename);
		$filename = strtolower($filename);
		$filename = trim($filename, '_');
		$filename = preg_replace('~[^-\w]+~', '', $filename);
		$filename .= "-".date("d/m/Y");
		$filename=$filename.".csv";


		header("Content-Type: text/plain");
		header("Content-disposition: attachment; filename=$filename");
		header("Content-Transfer-Encoding: binary");
		header("Pragma: no-cache");
		header("Expires: 0");

		$this->_japp->close();
	}

	private  function get_CourseName(){
		$query = $this->_db->getQuery(true);
		$query->select('titolo');
		$query->from('#__gg_unit as u');
		$query->where('u.id= ' .explode('|', $this->_filterparam->corso_id)[0]);
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
