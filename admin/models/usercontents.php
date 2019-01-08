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

class gglmsModelusercontents extends JModelList
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
        $id_utente = $this->getState('id_utente');
        $id_corso=$this->getState('id_corso');
        $query->select("anagrafica.nome, anagrafica.cognome, u.titolo as 'corso', c.id as 'id_contenuto', c.titolo, if(r.stato=1,'completato','non completato') as stato, date_format(r.data,'%d/%m/%Y') as 'data', r.visualizzazioni")
               ->from("#__gg_report as r")
               ->join('inner','#__gg_unit as u on r.id_corso=u.id')
               ->join('inner','#__gg_contenuti as c on r.id_contenuto=c.id')
               ->join('inner','#__gg_report_users as anagrafica on r.id_anagrafica=anagrafica.id');

            $query->where('r.id_utente='. $id_utente);
            $query->where('r.id_corso='. $id_corso);

        return $query;
    }

    public function getItems()
    {
        $id_utente = $this->getState('id_utente');

        $items = parent::getItems();
        foreach ($items as &$item){
            $item->logcontenuti=$this->getLogs($id_utente,$item->id_contenuto);

        }

        return $items;
    }


        private function getLogs($id_utente,$id_contenuto){


            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select("l.id_utente, c.titolo, date_format(l.data_accesso,'%d/%m/%Y %H:%i:%s') as 'data_accesso', l.supporto, l.ip_address, l.permanenza, l.permanenza_conteggiabile")
                ->from('#__gg_log as l')
                ->join('inner','#__gg_contenuti as c on l.id_contenuto=c.id')
                ->where('id_utente='. $id_utente)
                ->where('id_contenuto='. $id_contenuto)
                ->order('data_accesso DESC');
            $db->setQuery($query);

            return $db->loadAssocList();
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
        $search = $this->getUserStateFromRequest($this->context . '.id_utente', 'id_utente');
        //Omit double (white-)spaces and set state
        $this->setState('id_utente', preg_replace('/\s+/', ' ', $search));

        $search_ = $this->getUserStateFromRequest($this->context . '.id_corso', 'id_corso');
        //Omit double (white-)spaces and set state
        $this->setState('id_corso', preg_replace('/\s+/', ' ', $search_));


    }
}

