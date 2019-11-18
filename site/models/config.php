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
require_once JPATH_COMPONENT . '/models/coupon.php';
require_once JPATH_COMPONENT . '/models/syncdatareport.php';
require_once JPATH_COMPONENT . '/models/report.php';
//require_once JPATH_COMPONENT . '/models/unita.php';

/**
 * WebTVContenuto Model
 *
 * @package    Joomla.Components
 * @subpackage WebTV
 */
class gglmsModelConfig extends JModelLegacy
{

    private $_dbg;
    private $_app;
    private $_userid;
    public $_params;
    protected $_db;


    public function __construct($config = array())
    {
        parent::__construct($config);

        $user = JFactory::getUser();
        $this->_userid = $user->get('id');

        $this->_db = $this->getDbo();

        $this->_app = JFactory::getApplication('site');
        $this->_params = $this->_app->getParams();


    }

    public function getConfigValue($key)
    {

        try {
            $query = $this->_db->getQuery(true)
                ->select('config_value')
                ->from('#__gg_configs')
                ->where("config_key='" . $key . "'");

            $this->_db->setQuery($query);
            $result = $this->_db->loadResult();

            if($result === false){
                throw new Exception('Non riesco a leggere parametro di configueazione ' . $key, E_USER_ERROR);
            }

            return $result;
        } catch (Exception $e) {
            DEBUGG::error($e, 'getConfigValue');
        }

        return false;

    }

}

