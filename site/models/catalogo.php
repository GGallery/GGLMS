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
                        b1.description,
                        b.order')
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

    public function get_calendario_corsi($dominio, $modalita="1,2", $_offset=0, $_limit=10, $_search=null, $_sort=null, $_order=null) {

        try
        {
            $_ret = array();

            $query = $this->_db->getQuery(true)
                ->select('DISTINCT u.id,
                        u.titolo as evento,
                        u.alias,
                        u.posti_disponibili,
                        u.data_inizio as data,
                        u.data_fine,
                        u.is_bookable,
                        IF(u.is_bookable = 1, "' . JText::_('COM_GGLMS_BOXES_PRENOTAZIONE_RICHIESTA') . '", "' . JText::_('COM_GGLMS_BOXES_PRENOTAZIONE_NON_RICHIESTA') . '") AS note,
                        u.bookable_a_gruppi as destinatari,
                        u.modalita,
                        CONCAT(UCASE(LEFT(u.sede, 1)),SUBSTRING(u.sede, 2)) AS sede,
                        IF(u.obbligatorio = 1, "' . JText::_('COM_GGLMS_BOXES_PRENOTAZIONE_OBBLIGATORIO') . '", "") AS tipologia,
                        u.obbligatorio,
                        b1.description as area,
                        ugm.idgruppo as gruppo_corso
                        ');

            $count_query = $this->_db->getQuery(true)
                ->select('COUNT(DISTINCT(u.id))');

            $query = $query->from('#__gg_unit as u')
                ->join('inner', '#__gg_piattaforma_corso_map as piattamap on piattamap.id_unita=u.id')
                ->join('inner', '#__usergroups_details as det on det.group_id=piattamap.id_gruppo_piattaforma')
                ->join('inner', '#__gg_box_unit_map as b on b.id_unita=u.id')
                ->join('inner', '#__gg_box_details as b1 on b1.id=b.box')
                ->join('inner', '#__gg_usergroup_map as ugm on ugm.idunita = u.id')
                ->where('det.dominio = "' . $dominio . '" ')
                ->where('u.pubblicato = 1')
                ->where('u.modalita IN (' . $modalita . ')');

            $count_query = $count_query->from('#__gg_unit as u')
                ->join('inner', '#__gg_piattaforma_corso_map as piattamap on piattamap.id_unita=u.id')
                ->join('inner', '#__usergroups_details as det on det.group_id=piattamap.id_gruppo_piattaforma')
                ->join('inner', '#__gg_box_unit_map as b on b.id_unita=u.id')
                ->join('inner', '#__gg_box_details as b1 on b1.id=b.box')
                ->where('det.dominio="' . $dominio . '" ')
                ->where('u.pubblicato = 1')
                ->where('u.modalita IN (' . $modalita . ')');


            // ricerca
            if (!is_null($_search)) {

                $subquery_ug = $this->_db->getQuery(true)
                    ->select('id')
                    ->from('#__usergroups')
                    ->where('title LIKE \'%' . $_search . '%\'');
                $this->_db->setQuery($subquery_ug);
                $results = $this->_db->loadAssocList();

                $_extra = "";
                if (!is_null($results)
                    && count($results)) {
                    $_extra = " OR (";
                    $normalize = utilityHelper::normalizza_loadassoc($results, null, 'id', true);
                    $index = 0;
                    foreach ($normalize as $ref_id) {
                        $_extra .= $index > 0 ? " OR " : "";
                        $_extra .= " u.bookable_a_gruppi LIKE '%" . $ref_id . "%' ";
                        $index++;
                    }
                    $_extra .= ")";
                }

                $query = $query->where('(
                                            u.titolo LIKE \'%' . $_search . '%\'
                                            OR u.modalita LIKE \'%' . $_search . '%\'
                                            OR u.sede LIKE \'%' . $_search . '%\'
                                            OR b1.description LIKE \'%' . $_search . '%\'
                                            ' . $_extra . '
                                            )
                                    ');

                $count_query = $count_query->where('(
                                            u.titolo LIKE \'%' . $_search . '%\'
                                            OR u.modalita LIKE \'%' . $_search . '%\'
                                            OR u.sede LIKE \'%' . $_search . '%\'
                                            OR b1.description LIKE \'%' . $_search . '%\'
                                            ' . $_extra . '
                                            )
                                    ');

            }

            // ordinamento per colonna - di default per data inizio
            if (!is_null($_sort)
                && !is_null($_order)) {
                $query = $query->order($_sort . ' ' . $_order);
            }
            else
                $query = $query->order('u.data_inizio');

            //echo $query; die();
            $this->_db->setQuery($query, $_offset, $_limit);
            $result = $this->_db->loadAssocList();

            $this->_db->setQuery($count_query);
            $result_count = $this->_db->loadResult();

            // se nessun risultato restituisco un array vuoto
            if (!$result) {
                return $_ret;
            }

            $_ret['rows'] = $result;
            $_ret['total_rows'] = $result_count;

            return $_ret;

        }
        catch (Exception $e) {
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return null;
        }

    }

    // i box delle categorie corsi
    public function get_box_categorie_corso($box_id = null, $dominio = null, $ordinamento = false) {

        try {

            $query = $this->_db->getQuery(true);

            if (!is_null($box_id)) {
                return $this->getCatalogo($dominio, $box_id);
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

