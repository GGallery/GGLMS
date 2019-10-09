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

    private $_app;
    public $_params;
    protected $_db;
    public $contenuti = array();

    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->_db = $this->getDbo();
        $this->_app = JFactory::getApplication('site');

    }

    public function getCatalogo($dominio, $box)
    {

        $query = $this->_db->getQuery(true)
            ->select(' u.id,u.titolo,u.descrizione, u.alias, b1.description')
            ->from('#__gg_unit as u')
//        ->join('inner','#__gg_usergroup_map as mp on mp.idunita=u.id')
            ->join('inner', '#__gg_piattaforma_corso_map as piattamap on piattamap.id_unita=u.id')
            ->join('inner', '#__usergroups_details as det on det.group_id=piattamap.id_gruppo_piattaforma')
            ->join('inner', '#__gg_box_unit_map as b on b.id_unita=u.id')
            ->join('inner', '#__gg_box_details as b1 on b1.id=b.box')
            ->where('det.dominio="' . $dominio . '" ')
            ->where('b.box =' . $box)
            ->where('u.pubblicato = 1');

//        echo $query; die;

        $this->_db->setQuery($query);
        $catalogo = $this->_db->loadObjectList();


        return $catalogo;
    }

}

