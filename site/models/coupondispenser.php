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
class gglmsModelcoupondispenser extends JModelLegacy
{

    private $_japp;
    private $_coupon;
    protected $_db;
    private $_userid;
    private $_user;
    public $_params;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_dbg = JRequest::getBool('dbg', 0);
        $this->_japp = JFactory::getApplication();
        $this->_db = JFactory::getDbo();
        $this->_user = JFactory::getUser();
        $this->_userid = $this->_user->get('id');
        $this->_params = $this->_japp->getParams();

    }

    public function __destruct()
    {

    }

    public function getDispenser($id = null)
    {
        $this->_id = (!empty($id)) ? $id : (int)$this->getState('coupondispenser.id');

        DEBUGG::log($this->_id, 'idcoupon dispenser', 1, 0);


        try {
            $query = $this->_db->getQuery(true)
                ->select('c.*,t.tipologia as tipologia_contenuto')
                ->from('#__gg_contenuti as c')
                ->leftJoin('#__gg_contenuti_tipology as t on t.id=c.tipologia')
                ->where('c.id = ' . (int)$this->_id);


            $this->_db->setQuery($query);

            $data = $this->_db->loadObject();

            if (empty($data)) {
                DEBUGG::log('contenuto non trovato, id: ' . $id, 'error in getContenuto', 0, 1, 0);
                return null;
            }
        } catch (Exception $e) {
            DEBUGG::query($query, 'query get contenuto');
            DEBUGG::log($e->getMessage(), 'error in getContenuto', 0, 1, 0);
        }
        return $data;
    }


}
