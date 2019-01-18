<?php

/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class gglmsModellogs extends JModelList
{

    //Add this handy array with database fields to search in
    protected $searchInFields = array('id_utente');

//Override construct to allow filtering and ordering on our fields
    public function __construct($config = array())
    {
        $config['filter_fields'] = array_merge($this->searchInFields, array('a.id_utente'));

        parent::__construct($config);
    }

    protected function getListQuery()
    {
        // Create a new query object.
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);



            $query->select("anagrafica.id_user,anagrafica.nome, anagrafica.cognome, u.id, u.titolo, 
if((select stato from crg_gg_view_stato_user_corso as v where v.id_anagrafica=anagrafica.id and v.id_corso=u.id)=1,'completato','non completato') as stato, 
(select date_format(v.data_inizio,'%d/%m/%Y') from crg_gg_view_stato_user_corso as v where v.id_anagrafica=anagrafica.id and v.id_corso=u.id)as 'data_inizio',
(select date_format(v.data_fine,'%d/%m/%Y') from crg_gg_view_stato_user_corso as v where v.id_anagrafica=anagrafica.id and v.id_corso=u.id)as 'data_fine'
")
               ->from("#__user_usergroup_map as map")
               ->join('inner','#__ggif_edizione_unita_gruppo as e on e.id_gruppo=map.group_id')
               ->join('inner','#__gg_unit as u on e.id_unita=u.id')
               ->join('inner','#__gg_report_users as anagrafica on map.user_id=anagrafica.id_user');
            $id_utente = $this->getState('filter.search');
        if (!empty($id_utente)) {
            $query->where('map.user_id='. $id_utente);
        }
        $query->order('data_inizio desc');

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();

        return $items;
    }


    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since       1.6
     */
    protected function populateState($ordering = null, $direction = null)
    {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        // Load the filter state.
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        //Omit double (white-)spaces and set state
        $this->setState('filter.search', preg_replace('/\s+/', ' ', $search));

//        //Filter (dropdown) state
//        $state = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string');
//        $this->setState('filter.state', $state);
        //Filter (dropdown) company
        $state = $this->getUserStateFromRequest($this->context . '.filter.categoria', 'filter_categoria', '', 'string');
        $this->setState('filter.categoria', $state);
        //Takes care of states: list. limit / start / ordering / direction
        parent::populateState('a.id_utente', 'asc');

    }
}

