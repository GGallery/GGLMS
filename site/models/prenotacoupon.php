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

require_once JPATH_COMPONENT . '/models/config.php';
require_once(JPATH_COMPONENT . '/libraries/smarty/EasySmarty.class.php');

class gglmsModelprenotacoupon extends JModelLegacy
{

    private $_japp;
    protected $_db;
    private $_userid;
    private $_user;
    public $_params;
    public $lista_corsi;
    public $societa_venditrici;
    private $_config;
    private $_info_corso;


    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->_japp = JFactory::getApplication();
        $this->_db = JFactory::getDbo();
        $this->_user = JFactory::getUser();
        $this->_userid = $this->_user->get('id');
        $this->_params = $this->_japp->getParams();





    }

    public function __destruct()
    {

    }




}





