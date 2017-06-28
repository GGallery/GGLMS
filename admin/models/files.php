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

class gglmsModelFiles extends JModelList {

    //Add this handy array with database fields to search in
    protected $searchInFields = array( 'a.nome');

//Override construct to allow filtering and ordering on our fields
    public function __construct($config = array()) {
//        $config['filter_fields'] = array_merge($this->searchInFields, array('a.nome'));
        
        parent::__construct($config);
    }

  
 
    protected function getListQuery() {
        // Create a new query object.
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        // Select some fields
        $query->select('f.*')
                ->from('#__gg_files as f')
                    ->order('id desc')
//                ->order($db->escape($this->getState('list.ordering', 'pa.id')) . ' ' .
//                        $db->escape($this->getState('list.direction', 'desc')))
        ;

        

//        // Filter search // Extra: Search more than one fields and for multiple words
//        $regex = str_replace(' ', '|', $this->getState('filter.search'));
//        if (!empty($regex)) {
//            $regex = ' REGEXP ' . $db->quote($regex);
//            $query->where('(' . implode($regex . ' OR ', $this->searchInFields) . $regex . ')');
//        }
//
//        // Filter company
//        $id_categoria = $db->escape($this->getState('filter.categoria'));
//        if (!empty($id_categoria)) {
//            $query->where("categoria REGEXP '[[:<:]]". $id_categoria ."[[:>:]]'");
//        }
//
//        // Filter congresso
//        $id_congresso = $db->escape($this->getState('filter.congresso'));
//        if (!empty($id_congresso)) {
//            $query->where("id_congresso REGEXP '[[:<:]]". $id_congresso ."[[:>:]]'");
//        }
        
        return $query;
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since       1.6
     */
    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables.
//        $app = JFactory::getApplication('administrator');
//
//        // Load the filter state.
//        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
//        //Omit double (white-)spaces and set state
//        $this->setState('filter.search', preg_replace('/\s+/', ' ', $search));

//        //Filter (dropdown) state
//        $state = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string');
//        $this->setState('filter.state', $state);
        //Filter (dropdown) company
//        $state = $this->getUserStateFromRequest($this->context . '.filter.categoria', 'filter_categoria', '', 'string');
//        $this->setState('filter.categoria', $state);
//        //Takes care of states: list. limit / start / ordering / direction
//        parent::populateState('a.categoria', 'asc');
        
//        $state = $this->getUserStateFromRequest($this->context . '.filter.congresso', 'filter_congresso', '', 'string');
//        $this->setState('filter.congresso', $state);
//        parent::populateState('a.congresso', 'asc');
    }

}
