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

class gglmsModelHelpDesk extends JModelLegacy
{

    private $_japp;
    protected $_db;
    private $_userid;
    private $_user;
    public $_params;

    private $_config;



    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_dbg = JRequest::getBool('dbg', 0);
        $this->_japp = JFactory::getApplication();
        $this->_db = JFactory::getDbo();
        $this->_user = JFactory::getUser();
        $this->_userid = $this->_user->get('id');
        $this->_params = $this->_japp->getParams();
        $this->_config = new gglmsModelConfig();




    }

    public function __destruct()
    {

    }


    public function getPiattaformaHelpDeskInfo(){
        try {
            $query = $this->_db->getQuery(true)
                ->select('d.name, d.alias, d.telefono,d.email_riferimento,d.link_ecommerce, d.nomi_tutor, d.email_tutor')
                ->from('#__usergroups_details AS d')
                ->where("d.dominio= '" . DOMINIO . "'");

            $this->_db->setQuery($query);
            $data = $this->_db->loadObject();


            return $data;

        } catch (Exception $e) {
            DEBUGG::query($query);
            DEBUGG::log($e, 'getPiattaformaHelpDeskInfo', 1);

        }
    }

}
