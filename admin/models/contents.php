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
    protected $searchInFields = array( 'a.id','a.titolo', 'descrizione');
    private $unitas=array();
    private $contenuti=array();

//Override construct to allow filtering and ordering on our fields
    public function __construct($config = array()) {
        $config['filter_fields'] = array_merge($this->searchInFields, array('a.id'));
        //$config['filter_fields'] = array_merge($this->searchInFields, array('u.id'));
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
        $query->select('a.*')
            ->from('#__gg_contenuti as a')
            ->join('left','#__gg_unit_map as m on a.id=m.idcontenuto')
            ->order(' m.ordinamento ' . ' ' .
                $db->escape($this->getState('list.direction', 'desc')))
        ;



        // Filter search // Extra: Search more than one fields and for multiple words
        $regex = str_replace(' ', '|', $this->getState('filter.search'));
        if (!empty($regex)) {
            $regex = ' REGEXP ' . $db->quote($regex);
            $query->where('(' . implode($regex . ' OR ', $this->searchInFields) . $regex . ')');
        }

        $jorm=JFactory::getApplication()->getUserStateFromRequest('jform','jform');
        $id_categoria=$jorm['categoria'];
        if($id_categoria!=null) {

            switch ($id_categoria) {
                case "1":
                    break;
                default:
                    $query->where('m.idunita='.$id_categoria);
                    break;
            }
        }

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
        $app = JFactory::getApplication('administrator');

        // Load the filter state.
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        //Omit double (white-)spaces and set state
        $this->setState('filter.search', preg_replace('/\s+/', ' ', $search));

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

        parent::populateState('c.ordinamento', 'asc');
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
        return $this->getSottoUnitas($id_corso);//CHIAMATA ALLA FUNZIONE RICORSIVA
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

        return $this->contenuti; //SE SEI ARRIVATO QUI HAI FINITO IL CICLO DELLE CHIAMATE RICORSIVE
    }

    public function getContenuti($pk=null){

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('id');
        $query->from('#__gg_contenuti AS a');
        $query->join('inner','#__gg_unit_map as u on a.id = u.idcontenuto');

        if($pk!=null)
            $query->where("idunita=".$pk);

        $db->setQuery($query);
        $result=$db->loadAssocList();

        foreach ($result as $res) {
            array_push($this->contenuti, $res['id']); //LA VARIABILE DI CLASSE contenuti E' QUELLA CHE VIENE POPOLATA DALLA RICORSIVA
        }
    }

    public function updateOrderValue($pk,$i){
        try {

            $db = JFactory::getDBO();
            $query='update #__gg_unit_map set ordinamento='.((int)$i+1).' where idcontenuto='.$pk;
            $db->setQuery($query);
            $result=$db->execute();
            echo $query.'<br>';

            return $result;
        }catch (exceptions $e){

            DEBUGG::log('errore '.$e->getMessage(),"errore in UpdateOrderValue",0,1);
        }

    }

    public function getOldTable(){

        $db = JFactory::getDBO();
        $query='SELECT idcontenuto, ordinamento from #__gg_unit_map';
        $db->setQuery($query);
        $result=$db->loadAssocList('ordinamento');
        return $result;

    }


}
