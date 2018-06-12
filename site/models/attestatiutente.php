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
require_once JPATH_COMPONENT . '/models/syncviewcarigelearningbatch.php';
class gglmsModelAttestatiUtente extends JModelLegacy {

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


}

