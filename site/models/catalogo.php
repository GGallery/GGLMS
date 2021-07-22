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

    public function getCatalogo($dominio, $box, $show_bookable = false)
    {

        $query = $this->_db->getQuery(true)
            ->select('distinct u.id,
                        u.titolo,
                        u.descrizione,
                        u.alias,
                        u.posti_disponibili,
                        u.data_inizio,
                        u.data_fine,
                        u.bookable_a_gruppi,
                        b1.description, b.order')
            ->from('#__gg_unit as u')
            ->join('inner', '#__gg_piattaforma_corso_map as piattamap on piattamap.id_unita=u.id')
            ->join('inner', '#__usergroups_details as det on det.group_id=piattamap.id_gruppo_piattaforma')
            ->join('inner', '#__gg_box_unit_map as b on b.id_unita=u.id')
            ->join('inner', '#__gg_box_details as b1 on b1.id=b.box')
            ->where('det.dominio="' . $dominio . '" ')
            ->where('b.box =' . $box)
            ->order('b.order')
            ->where('u.pubblicato = 1');

        if ($show_bookable)
            $query = $query->where('u.is_bookable = 1');

        $this->_db->setQuery($query);
        $catalogo = $this->_db->loadObjectList();

        return $catalogo;
    }

    /*ritorna lista corsi della piattaforma di riferimento per la prenotazione in caso di ecommerce non presente--> usato nel template catalogo_prenota*/
    public function getCatalogo_prenota($id_piattaforma)
    {
        //

        $query = $this->_db->getQuery(true)
            ->select('distinct u.id as id_corso,u.titolo,u.descrizione, u.alias')
            ->from('#__gg_unit as u')
            ->join('inner', '#__gg_piattaforma_corso_map as piattamap on piattamap.id_unita=u.id')
            ->join('inner', '#__usergroups_details as det on det.group_id=piattamap.id_gruppo_piattaforma')
            ->where('piattamap.id_gruppo_piattaforma="' . $id_piattaforma . '" ')
            ->where('u.pubblicato = 1')
            ->where('u.is_corso=1');

//        echo $query; die;

        $this->_db->setQuery($query);
        $catalogo = $this->_db->loadObjectList();


        return $catalogo;
    }

    // i box delle categorie corsi
    public function get_box_categorie_corso($box_id = null, $dominio = null, $ordinamento = false) {

        try {

            $query = $this->_db->getQuery(true);

            if (!is_null($box_id)) {
                return $this->getCatalogo($dominio, $box_id, true);
            }
            else
                $query = $query->select('*')
                            ->from('#__gg_box_details');

            if ($ordinamento)
                $query = $query->order('ordinamento');

            $this->_db->setQuery($query);
            return $this->_db->loadAssocList();
        }
        catch (Exception $e) {
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return null;
        }
    }

}

