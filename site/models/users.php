<?php
/**
 * Created by PhpStorm.
 * User: Tony
 * Date: 04/05/2017
 * Time: 17:03
 */
class gglmsModelUsers  extends JModelLegacy {

    protected $_db;
    private $_params;
    private $_app;
    public $_userid;
    public $nome;
    public $cognome;

    public function __construct($config = array()) {
        parent::__construct($config);

        $user = JFactory::getUser();
        $this->_userid = $user->get('id');
        $this->_db = $this->getDbo();
        $this->_app = JFactory::getApplication();
        $this->_params = $this->_app->getParams();

    }



    public function get_user($id = null, $integration_element_id = null  )
    {
        switch ($this->_params->get('integrazione')) {
            case 'cb':
                $data =  $this->get_user_cb($id);
                break;

            case 'eb':
                $data =  $this->get_user_eb($id, $integration_element_id);
                break;

            default:
                $data =  $this->get_user_joomla($id);
                break;
        }

        return $data;

    }

    private function get_user_joomla($id){

        try {

            $query = $this->_db->getQuery(true)
                ->select('*, SUBSTRING_INDEX(name,\' \',1) as nome, SUBSTRING_INDEX(name,\' \',-1) as cognome ')
                ->from('#__users as u')
                ->where('u.id = ' . $id);

            $this->_db->setQuery($query);
            $registrants = $this->_db->loadObject();

            return $registrants;
        }
        catch (Exception $e){
            DEBUGG::error($e, 'error get user cb', 1);
        }

    }
        
    private function get_user_cb($id){

        $colonna_nome=$this->_app->getParams()->get('campo_community_builder_nome');
        $colonna_cognome=$this->_app->getParams()->get('campo_community_builder_cognome');


        try {

            $query = $this->_db->getQuery(true)
                ->select('*, '.$colonna_nome.' as nome, '.$colonna_cognome.' as cognome ')
                ->from('#__comprofiler as r')
                ->join('inner', '#__users as u on u.id = r.id')
                ->where('r.user_id = ' . $id);


            $this->_db->setQuery($query);
            $registrants = $this->_db->loadObject();

            return $registrants;
        }
        catch (Exception $e){
            DEBUGG::error($e, 'error get user cb', 1);
        }

    }

    private function get_user_eb($id, $id_eb){

        $colonna_nome=$this->_app->getParams()->get('campo_event_booking_nome');
        $colonna_cognome=$this->_app->getParams()->get('campo_event_booking_cognome');


        try {
            $query = $this->_db->getQuery(true)
                ->select('*')
                ->from('#__eb_registrants as r')
                ->where('r.user_id = ' . $id)
                ->where('r.event_id = ' . $id_eb);


            $this->_db->setQuery($query);
            $registrants = $this->_db->loadAssoc();

            if($registrants['id']) {
                $registrants['nome'] = $registrants[$colonna_nome];
                $registrants['cognome'] = $registrants[$colonna_cognome];

                $extrafieldfields = $this->get_user_field_eb($registrants['id']);

                if ($extrafieldfields)
                    $registrants = (object)array_merge($registrants, $extrafieldfields);
            }


            return $registrants;
        }catch (Exception $e){
            DEBUGG::query($query, 'query error in get user eb');
            DEBUGG::error($e, 'error in get user eb', 1);

        }
    }

    private function get_user_field_eb($registrant_id){

        $query = $this->_db->getQuery(true)
            ->select('f.`name`, v.field_value')
            ->from('#__eb_field_values AS v')
            ->join('inner', '#__eb_fields AS f ON f.id = v.field_id')
            ->where('v.registrant_id = ' . $registrant_id)
        ;

        $this->_db->setQuery($query);
        $fields = $this->_db->loadAssoclist('name', 'field_value');

        return $fields;
    }


   

}

