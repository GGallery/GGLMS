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
	public $_params;
	protected  $_db;


	public function __construct($config = array()) {
		parent::__construct($config);

		$user = JFactory::getUser();
		$this->_userid = $user->get('id');

		$this->_db = JFactory::getDbo();

		$this->_app = JFactory::getApplication('site');
		$this->_params = $this->_app->getParams();

	}

	public function __destruct() {

	}


	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = $app->input->getInt('id');
		$this->setState('unita.id', $pk);

		$offset = $app->input->getUInt('limitstart');
		$this->setState('list.offset', $offset);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);



		$this->setState('filter.language', JLanguageMultilang::isEnabled());
	}



	public function getOutput(){

		$id=$_REQUEST['idunit'];
		$unit= $this->getUnit($id);
		$contenuti = $unit->getAllContenuti();


		$this->debug($unit);

		print_r($contenuti);
		die;
//		$contenuti = $unit->getContenuti();

		
		$headerContenuti = array();

		foreach ($contenuti as $contenuto)
			$headerContenuti[] = $contenuto;


		$outputTable = array($headerContenuti);

		$userColumn = $this->getUsersEvent($unit->id_event_booking);


		foreach ($userColumn as $user){
			$outputRow = array($user);
			foreach ($contenuti as $contenuto)
				$outputRow[] = $contenuto->getStato($user->user_id);

			$outputTable [] = $outputRow;
		}

		return $outputTable;
	}




	public function getUnit($id){

		try {
			$query = $this->_db->getQuery(true)
				->select('*')
				->from('#__gg_unit as u')
				->where('u.id = ' . $id);

			$this->_db->setQuery($query);
			$unit = $this->_db->loadObject('gglmsModelUnita');


			$this->debug($unit);
			die;

			return $unit;
		}
		catch (Exception $e) {
			die ('Errore getUnit report'.(string)$query);
		}

	}


	public function getUsersEvent($event_id){
		try {
			$query = $this->_db->getQuery(true)
				->select('*')
				->from('#__eb_registrants')
				->where('event_id = ' . $event_id);

			$this->_db->setQuery($query);
			$data = $this->_db->loadObjectList();
			return $data;
		}
		catch (Exception $e) {
			die ('Errore getUsersEventreport'. (string)$query);
		}


	}

	public function debug($data, $die = false){

		echo "<pre>";
		print_r($data);
		echo "</pre>";

		if($die)
			die();


	}


}

