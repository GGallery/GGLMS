<?php

/**
 * WebTVContenuto Model
 *
 * @package    Joomla.Components
 * @subpackage WebTV
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');



/**
 * WebTVContenuto Model
 *
 * @package    Joomla.Components
 * @subpackage WebTV
 */

class gglmsModelReportTempiLearning extends JModelLegacy {

	private $_dbg;
	private $_app;
	private $_userid;
	protected $params;
	protected  $_db;


	public function __construct($config = array()) {
		parent::__construct($config);
		$this->_db = JFactory::getDbo();
		$this->_app = JFactory::getApplication('site');
	}

	public function __destruct() {

	}
    public function get_user_name($userid){

        try {

            $query = $this->_db->getQuery(true);
            $query->select('name');
            $query->from('#__users');
            $query->where('id=' . $userid);
            $this->_db->setQuery($query);
            $result = $this->_db->loadResult();
            return $result;
        }catch (exceptions $e){
            DEBUGG::log('ERRORE DA GETUSER','ERRORE DA GET USER',1,1);
        }
    }

    public function get_tempi($userid){

        try {

            $query = $this->_db->getQuery(true);
            $query->select('month(data) as mese, year(data) as anno, sum(totale) as totale');
            $query->from('#__ggif_cartellini');
            $query->where('user_id=' . $userid);
            $query->group('month(data), year(data)');
            $query->order('year(data),month(data) asc');
            $this->_db->setQuery($query);
            $result = $this->_db->loadObjectList();
            return $result;
        }catch (exceptions $e){
            DEBUGG::log('ERRORE DA GET_TEMPI','ERRORE DA GET TEMPI',1,1);
        }
    }




}

