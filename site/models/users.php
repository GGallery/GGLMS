<?php

/**
 * Created by PhpStorm.
 * User: Tony
 * Date: 04/05/2017
 * Time: 17:03
 */
class gglmsModelUsers extends JModelLegacy
{

    protected $_db;
    private $_params;
    private $_app;
    public $_userid;
    public $nome;
    public $cognome;

    public function __construct($config = array())
    {
        parent::__construct($config);

        $user = JFactory::getUser();
        $this->_userid = $user->get('id');
        $this->_db = $this->getDbo();
        $this->_app = JFactory::getApplication();
        $this->_params = $this->_app->getParams();

    }


    public function get_user($id = null, $integration_element_id = null)
    {
        switch ($this->_params->get('integrazione')) {
            case 'cb':
                $data = $this->get_user_cb($id);
                break;

            case 'eb':
                $data = $this->get_user_eb($id, $integration_element_id);
                break;

            default:
                $data = $this->get_user_joomla($id);
                break;
        }

        return $data;

    }

    private function get_user_joomla($id)
    {

        try {

            $query = $this->_db->getQuery(true)
                ->select('*, SUBSTRING_INDEX(name,\' \',1) as nome, SUBSTRING_INDEX(name,\' \',-1) as cognome ')
                ->from('#__users as u')
                ->where('u.id = ' . $id);

            $this->_db->setQuery($query);
            $registrants = $this->_db->loadObject();

            return $registrants;
        } catch (Exception $e) {
            DEBUGG::error($e, 'error get user cb', 1);
        }

    }

    private function get_user_cb($id)
    {

        $colonna_nome = $this->_app->getParams()->get('campo_community_builder_nome');
        $colonna_cognome = $this->_app->getParams()->get('campo_community_builder_cognome');


        try {

            $query = $this->_db->getQuery(true)
                ->select('*, ' . $colonna_nome . ' as nome, ' . $colonna_cognome . ' as cognome ')
                ->from('#__comprofiler as r')
                ->join('inner', '#__users as u on u.id = r.id')
                ->where('r.user_id = ' . $id);


            $this->_db->setQuery($query);
            $registrants = $this->_db->loadObject();

            return $registrants;
        } catch (Exception $e) {
            DEBUGG::error($e, 'error get user cb', 1);
        }

    }

    private function get_user_eb($id, $id_eb)
    {

        $colonna_nome = $this->_app->getParams()->get('campo_event_booking_nome');
        $colonna_cognome = $this->_app->getParams()->get('campo_event_booking_cognome');


        try {
            $query = $this->_db->getQuery(true)
                ->select('*')
                ->from('#__eb_registrants as r')
                ->where('r.user_id = ' . $id)
                ->where('r.event_id = ' . $id_eb);


            $this->_db->setQuery($query);
            $registrants = $this->_db->loadAssoc();

            if ($registrants['id']) {
                $registrants['nome'] = $registrants[$colonna_nome];
                $registrants['cognome'] = $registrants[$colonna_cognome];

                $extrafieldfields = $this->get_user_field_eb($registrants['id']);

                if ($extrafieldfields)
                    $registrants = (object)array_merge($registrants, $extrafieldfields);
            }


            return $registrants;
        } catch (Exception $e) {
            DEBUGG::query($query, 'query error in get user eb');
            DEBUGG::error($e, 'error in get user eb', 1);

        }
    }

    private function get_user_field_eb($registrant_id)
    {

        $query = $this->_db->getQuery(true)
            ->select('f.`name`, v.field_value')
            ->from('#__eb_field_values AS v')
            ->join('inner', '#__eb_fields AS f ON f.id = v.field_id')
            ->where('v.registrant_id = ' . $registrant_id);

        $this->_db->setQuery($query);
        $fields = $this->_db->loadAssoclist('name', 'field_value');

        return $fields;
    }

    /////////////////////////////////////////////////////////////////////////////////////

    public function is_tutor_piattaforma($id)
    {
        $_config = new gglmsModelConfig();
        $user_groups = JAccess::getGroupsByUser($id, false);
        $id_gruppo_tutor_piattaforma = $_config->getConfigValue('id_gruppo_tutor_piattaforma');

        return in_array($id_gruppo_tutor_piattaforma, $user_groups);

    }


    public function is_tutor_aziendale($id)
    {
        $_config = new gglmsModelConfig();
        $user_groups = JAccess::getGroupsByUser($id, false);
        $id_gruppo_tutor_aziendale = $_config->getConfigValue('id_gruppo_tutor_aziendale');

        return in_array($id_gruppo_tutor_aziendale, $user_groups);
    }


    public function is_venditore($id)
    {
        $_config = new gglmsModelConfig();
        $user_groups = JAccess::getGroupsByUser($id, false);
        $id_gruppo_venditori = $_config->getConfigValue('id_gruppo_venditori');

        return in_array($id_gruppo_venditori, $user_groups);

    }



    // $strict = true --> solo societa a cui l'utente appartiene, la ricavo dal dominio per essere sicura di avere un solo risultato (in caso di configurazioni sbagliate)
    // $strict = false --> tutte le società delle piattaforme a cui appartiene
    public function get_user_societa($id, $strict = true)
    {

        $res = array();

        $_config = new gglmsModelConfig();
        $id_gruppo_piattaforme = $_config->getConfigValue('id_gruppo_piattaforme');

        $user_groups = JAccess::getGroupsByUser($id, false);
        $groupid_list = '(' . implode(',', $user_groups) . ')';


        if ($strict) {

            $subQuery_strict = $this->_db->getQuery(true)
                ->select('group_id')
                ->from('#__usergroups_details')
                ->where("dominio= '" . DOMINIO ."'");


            $query_strict = $this->_db->getQuery(true)
                ->select('id, title')
                ->from('#__usergroups')
                ->where($this->_db->quoteName('parent_id') . ' IN (' . $subQuery_strict->__toString() . ')')
                ->where('id IN ' . $groupid_list);


//           echo (string)$query_strict;

            $this->_db->setQuery($query_strict);
            $res = $this->_db->loadObjectList();

        } else {


            $subQuery = $this->_db->getQuery(true)
                ->select('id')
                ->from('#__usergroups')
                ->where('id IN ' . $groupid_list)
                ->where('parent_id= ' . $id_gruppo_piattaforme);


            $query_P = $this->_db->getQuery(true)
                ->select('id, title')
                ->from('#__usergroups')
                ->where($this->_db->quoteName('parent_id') . ' IN (' . $subQuery->__toString() . ')');

            $this->_db->setQuery($query_P);
            $res = $this->_db->loadObjectList();
        }


        return $res;


    }


    public function get_user_piattaforme($id)
    {
        $_config = new gglmsModelConfig();
        $id_gruppo_piattaforme = $_config->getConfigValue('id_gruppo_piattaforme');

        $user_groups = JAccess::getGroupsByUser($id, true);
        $groupid_list = '(' . implode(',', $user_groups) . ')';

        $query = $this->_db->getQuery(true)
            ->select('g.id as value, d.alias as text')
            ->from('#__usergroups as g')
            ->join('inner', '#__usergroups_details as d ON g.id = d.group_id')
            ->where("g.parent_id=" . $id_gruppo_piattaforme)
            ->where('g.id IN ' . $groupid_list);

//        echo(string)$query;
        $this->_db->setQuery($query);

        $result = $this->_db->loadObjectList();

        return $result;


    }


}

