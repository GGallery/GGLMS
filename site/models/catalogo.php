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
class gglmsModelCatalogo extends JModelLegacy
{

    private $_dbg;
    private $_app;
    private $_userid;
    public $_params;
    protected $_db;
    private $unitas = array();
    public $contenuti = array();


    public function __construct($config = array())
    {
        parent::__construct($config);
      

        $this->_db = $this->getDbo();

        $this->_app = JFactory::getApplication('site');
      


    }

    public function getCatalogo($dominio,$box){

        $query = $this->_db->getQuery(true)
        ->select('u.id,u.titolo,u.descrizione')
        ->from('cis19_gg_unit as u')
        ->join('inner','cis19_gg_usergroup_map as mp on mp.idunita=u.id')
        ->join('inner','cis19_gg_piattaforma_corso_map as piattamap on piattamap.id_gruppo_corso=mp.idgruppo')
        ->join('inner','cis19_usergroups_details as det on det.group_id=piattamap.id_gruppo_piattaforma')
        ->join('inner','cis19_gg_box_unit_map as b on b.id_gruppo_corso=piattamap.id_gruppo_corso')
        ->where('det.dominio="'.$dominio.'" and b.box='.$box);
        //echo $query; die;

        $this->_db->setQuery($query);
        $catalogo = $this->_db->loadObjectList();
        return $catalogo;
    }
   
}

