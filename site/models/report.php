<?php

/**
 * WebTVContenuto Model
 *
 * @package    Joomla.Components
 * @subpackage WebTV
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
require_once JPATH_COMPONENT . '/models/contenuto.php';
require_once JPATH_COMPONENT . '/models/unita.php';
require_once JPATH_COMPONENT . '/models/coupon.php';
require_once JPATH_COMPONENT . '/models/users.php';


/**
 * WebTVContenuto Model
 *
 * @package    Joomla.Components
 * @subpackage WebTV
 */
class gglmsModelReport extends JModelLegacy {

	private $_dbg;
	private $_app;
	private $_userid;
	protected $params;
	protected  $_db;


	public function __construct($config = array()) {
		parent::__construct($config);

		$user = JFactory::getUser();
		$this->_userid = $user->get('id');

		$this->_db = JFactory::getDbo();

		$this->_app = JFactory::getApplication('site');
		$this->params = $this->_app->getParams();


		$this->populateState();
	}

	public function __destruct() {

	}

	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$id_corso = $app->input->getInt('id_corso');
		$this->setState('id_corso', $id_corso);


		$offset = $app->input->getUInt('limitstart');
		$this->setState('list.offset', $offset);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

	}


	public function getUser(){

		try{

			$query= $this->_db->getQuery(true);
			$query->select('distinct id_utente, id_event_booking');
			$query->from('report');
			$query->where('id_corso = ' . $this->getState('id_corso'));
			$query->orderBy('id_utente');

			$this->_db->setQuery($query);

			$utenti = $this->_db->loadObjectList();



			foreach ($utenti as $utente){

				$modelUtente = new gglmsModelUsers();
				$utente->info = $modelUtente->get_user($utente->id_utente, $utente->id_event_booking);
				$utente->report =$this->getUserReport($utente->id_utente);
			}

//			DEBUGG::log($utenti, 'utenti');

			return $utenti;

		}
		catch (Exception $e){
			DEBUGG::query($query, 'query USer');
			DEBUGG::error($e, 'getUser', 1);

		}

	}

	private function getUserReport($user_id){
		try{

			$query= $this->_db->getQuery(true);
			$query->select('id_contenuto, stato, `data`');
			$query->from('report');
			$query->where('id_corso = ' . $this->getState('id_corso'));
			$query->where('id_utente = ' . $user_id);

			$this->_db->setQuery($query);

			$data = $this->_db->loadAssocList('id_contenuto');

			return $data;
		}
		catch (Exception $e){
			DEBUGG::query($query, 'getUserReport');
			DEBUGG::error($e, 'getUserReport', 1);
		}
	}

	public function getOutput(){
		try {
			$query = $this->_db->getQuery(true);

			$query->select('corso.titolo as titolo_corso, corso.id_event_booking, corso.id_contenuto_completamento, unita.titolo as titolo_unita, contenuti.titolo as titolo_contenuto, r.*');
			$query->from('#__gg_report as r');

			$query->join('inner', '#__gg_unit as corso on corso.id = r.id_corso ');
			$query->join('inner', '#__gg_unit as unita on unita.id = r.id_unita ');

			$query->join('inner', '#__gg_contenuti as contenuti on contenuti.id = r.id_contenuto');



			if($this->getState('id_corso'))
				$query->where('corso.id_contenuto_completamento = contenuti.id');

			$this->_db->setQuery($query);
			$data = $this->_db->loadObjectList();



			foreach ($data as &$row){
				$utente= new gglmsModelUsers();
				$row->utente= $utente->get_user($row->id_utente, $row->id_event_booking);
				unset($utente);
			}

			return $data;

			//


		}
		catch(Exception $e){

			DEBUGG::query($query, 'get Output', 0);
			DEBUGG::error($e, 'errore get Output', 1);
		}

	}

	 
	public function getSottoUnita($item = 0) {
		$tree = array();

		$query = $this->_db->getQuery(true);

		$query->select('a.id, a.titolo');
		$query->from('#__gg_unit AS a');
		$query->where("unitapadre=" . $item);

		$this->_db->setQuery($query);

		$tmptree = $this->_db->loadObjectList();
		foreach ($tmptree as $item) {
			array_push($tree, $item);
			foreach ($this->getSottoUnita($item->id) as $item2) {
				$item2->titolo = "<span class=\"icon-forward-2\"> </span>" . $item2->titolo;
				array_push($tree, $item2);
			}
			$item->contenuti= $this->getContenutiUnita($item->id);
		}
		unset($tmptree);
		return $tree;
	}

	public function getContenutiUnita($item) {

		try {
			$query = $this->_db->getQuery(true);

			$query->select('c.id, c.titolo');
			$query->from('#__gg_unit_map AS a');
			$query->join('inner', '#__gg_contenuti AS c on c.id = a.idcontenuto');
			$query->where("idunita=" . $item);
			$query->order('a.ordinamento');

			$this->_db->setQuery($query);
			$data = $this->_db->loadObjectList();

			return $data;
		}catch (Exception $e){

			DEBUGG::query($query, 'query contenuti unita');
			DEBUGG::error($e, 'errore get Conteuti unita', 1);

		}
	}


}

