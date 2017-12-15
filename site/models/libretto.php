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
class gglmsModelLibretto extends JModelLegacy {

	private $_dbg;
	private $_app;
	private $_userid;
	protected $params;
	protected  $_db;


	public function __construct($config = array()) {
		parent::__construct($config);

		//$user = JFactory::getUser();
		//$this->_userid = $user->get('id');

		$this->_db = JFactory::getDbo();

		$this->_app = JFactory::getApplication('site');
		//$this->params = $this->_app->getParams();


		//$this->populateState();
	}

	public function __destruct() {

	}

    public function get_data($userid){

        try {

            $query = $this->_db->getQuery(true);
            $query->select('u.titolo as corso, DATE_FORMAT(r.data,\'%d/%m/%Y\') as data_fine, DATEDIFF(r.data,(select r2.data from #__gg_report as r2 where r2.id_utente 
                = ' . $userid . ' and r2.id_corso = r.id_corso ORDER BY r2.data  limit 1)) as durata');
            $query->from('#__gg_report as r');
            $query->join('inner', '#__gg_unit as u on r.id_contenuto=u.id_contenuto_completamento');
            $query->where(' stato=1 and r.id_utente=' . $userid);
            $query->order('r.data');
            $this->_db->setQuery($query);
            $rows = $this->_db->loadAssocList();
            $result['query'] =(string)$query;
            $result['rows'] = $rows;
            return $result;
        }catch (exceptions $e){

            DEBUGG::log('ERRORE DA GETDATA','ERRORE DA GET DATA',1,1);
        }
    }

    public function get_nome($userid){

        try {

            $query = $this->_db->getQuery(true);
            $query->select('name');
            $query->from('#__users as u');
            $query->where('u.id=' . $userid);
            $this->_db->setQuery($query);
            $result = $this->_db->loadResult();

            return $result;
        }catch (exceptions $e){

            DEBUGG::log('ERRORE DA GETDATA','ERRORE DA GET DATA',1,1);
        }

    }

    public function get_user($userid){

        try {

            $query = $this->_db->getQuery(true);
            $query->select('nome, cognome, id_user');
            $query->from('#__gg_report_users as u');
            $query->where('u.id_user=' . $userid);
            $this->_db->setQuery($query);
            $result = $this->_db->loadAssocList();

            return $result[0];
        }catch (exceptions $e){

            DEBUGG::log('ERRORE DA GETUSER','ERRORE DA GET USER',1,1);
        }
    }




}

