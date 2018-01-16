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

class gglmsModelContents extends JModelList {

    //Add this handy array with database fields to search in
    protected $searchInFields = array( 'a.titolo', 'descrizione');
    private $unitas=array();
    private $contenuti=array();

//Override construct to allow filtering and ordering on our fields
    public function __construct($config = array()) {
        $config['filter_fields'] = array_merge($this->searchInFields, array('a.categoria'));
        $config['filter_fields'] = array_merge($this->searchInFields, array('u.id'));
        parent::__construct($config);
    }

    public function getForm($data = array(), $loadData = true) {
        // Get the form.
        $form = $this->loadForm('com_gglms.contents', 'contents', array('control' => 'jform', 'load_data' => $loadData));

        return $form;
    }

    protected function getListQuery() {
        // Create a new query object.
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        // Select some fields
        $query->select('*')
                ->from('#__gg_contenuti as a')
                ->order('id desc')
//                ->order($db->escape($this->getState('list.ordering', 'pa.id')) . ' ' .
//                        $db->escape($this->getState('list.direction', 'desc')))
        ;

        

        // Filter search // Extra: Search more than one fields and for multiple words
        $regex = str_replace(' ', '|', $this->getState('filter.search'));
        if (!empty($regex)) {
            $regex = ' REGEXP ' . $db->quote($regex);
            $query->where('(' . implode($regex . ' OR ', $this->searchInFields) . $regex . ')');
        }

        // Filter company
        $id_categoria = $db->escape($this->getState('filter.categoria'));
        if (!empty($id_categoria)) {
            $query->where("categoria REGEXP '[[:<:]]". $id_categoria ."[[:>:]]'");
        }



        if ($this->getState('corsi')!='null') {

            $contenuti_str=null;
            $contenuti=$this->getContenutiList($this->getState('corsi'));
            foreach ($contenuti as $contenuto){

                $contenuti_str=$contenuti_str.'-'.$contenuto;
            }
            $contenuti_str=substr($contenuti_str,1);
            $contenuti_str=str_replace("-",",",$contenuti_str);
            $query->where("id in (".$contenuti_str.")");
        }

       // echo $query;

        return $query;
    }

    public function getSottoUnita($pk = null)
    {

        try
        {
            $query = $this->_db->getQuery(true)
                ->select('id')
                ->from('#__gg_unit as u')
                ->where('u.unitapadre  = ' . $pk)

            ;
            $this->_db->setQuery($query);
            $data = $this->_db->loadAssocList();

        }
        catch (Exception $e)
        {
            DEBUGG::log($e, 'getSottoUnita');
        }

//
        return $data;
    }

    public function getAllContenuti($pk)
    {
        $sottunita = $this->getSottoUnita($pk);

        $contenuti= array();
        foreach ($sottunita as $unitafiglio){
            $sottounita=$unitafiglio->getSottoUnita($pk);
            $sottocontenuti = $unitafiglio->getContenuti();
            $contenuti=array_merge($contenuti, $sottocontenuti);
        }

//		DEBUGG::log($contenuti, 'contenuti', 1);

        return $contenuti;
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
        parent::populateState('a.categoria', 'asc');
        
        $state = $this->getUserStateFromRequest($this->context . '.filter.congresso', 'filter_congresso', '', 'string');
        $this->setState('filter.congresso', $state);
        parent::populateState('a.congresso', 'asc');

        //$state = $this->getUserStateFromRequest($this->context . '.campo_lista_corsi', 'campo_lista_corsi', '', 'string');
        $state =$app->input->get('corsi','','');
       // var_dump($app->input->get('corsi','',''));
        $this->setState('corsi', $state);



        parent::populateState('corsi', 'asc');
    }

    public function getCorsi(){

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from('#__gg_unit AS a');
        $query->where("is_corso=1 ");

        $db->setQuery($query);

        $corsi = $db->loadObjectList();


        return $corsi;

    }

    public function getContenutiList($id_corso)
    {

            return $this->getSottoUnitas($id_corso);

    }
    public function getSottoUnitas($pk=null){


        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from('#__gg_unit AS a');

        if($pk!=null)
            $query->where("unitapadre=".$pk);

        //echo $query.'<br>';
        $db->setQuery($query);
        $result=$db->loadObjectList();

        if($result!=null) {
            if ($this->unitas == null) {
                $this->unitas = $result;
            } else {
                array_push($this->unitas, $result);

            }
        }


            foreach ($result as $unita) {

                $this->getContenuti($unita->id);
                $this->getSottoUnitas($unita->id);


        }
        //echo count($this->unitas).'<br>';
        //var_dump($this->contenuti);

        return $this->contenuti;
    }

    public function getContenuti($pk=null){

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('id');
        $query->from('#__gg_contenuti AS a');
        $query->join('inner','#__gg_unit_map as u on a.id = u.idcontenuto');


        if($pk!=999)
            $query->where("idunita=".$pk);

        //echo $query.'<br>';

        $db->setQuery($query);
        $result=$db->loadAssocList();

        foreach ($result as $res) {

            array_push($this->contenuti, $res['id']);

        }
        //var_dump($this->contenuti);
        //return $this->contenuti;
        //

    }


}
