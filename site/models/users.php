<?php
/**
 * Created by PhpStorm.
 * User: Tony
 * Date: 04/05/2017
 * Time: 17:03
 */
class gglmsModelUsers  extends JModelLegacy {

    protected $_db;
    public $_userid;
    public $nome;
    public $cognome;
    

    public function __construct($config = array()) {
        parent::__construct($config);

        $user = JFactory::getUser();
        $this->_userid = $user->get('id');

        $this->_db = $this->getDbo();
    }

    public function get_user($id = null, $integration_element_id = null)
    {

        $app = JFactory::getApplication();
        $params = $app->getParams();

        switch ($params->get('integrazione')) {
            case 'cb':
                break;

            case 'eb':
                $data =  $this->get_user_eb($id, $integration_element_id);

                
                break;
        }



        return $data;

    }


    private function get_user_eb($id, $id_eb){

        $query = $this->_db->getQuery(true)
            ->select('*')
            ->from('#__eb_registrants as r')
            ->where('r.user_id = ' . $id)
            ->where('r.event_id = ' . $id_eb)
        ;

        $this->_db->setQuery($query);
        $registrants = $this->_db->loadObject();

        $registrants->nome=  $registrants->first_name;
        $registrants->cognome=  $registrants->last_name;
        $registrants->fields = $this->get_user_field_eb($registrants->id);

        return $registrants;
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

