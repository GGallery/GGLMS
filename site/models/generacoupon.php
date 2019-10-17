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
class gglmsModelgeneracoupon extends JModelLegacy
{

    private $_japp;
    private $_coupon;
    protected $_db;
    private $_userid;
    private $_user;
    public $_params;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_dbg = JRequest::getBool('dbg', 0);
        $this->_japp = JFactory::getApplication();
        $this->_db = JFactory::getDbo();
        $this->_user = JFactory::getUser();
        $this->_userid = $this->_user->get('id');
        $this->_params = $this->_japp->getParams();

    }

    public function __destruct()
    {

    }


    public function insert_coupon($data)
    {

        try {

            // se non esiste crea utente ( tutor ) legato alla company
            if (false === ($new_user = $this->create_new_company_user($data))) {
                throw new RuntimeException('Error: cannot create user.', E_USER_ERROR);

            }


        } catch (Exception $ex) {

            echo 'error in insert_coupon';

        }

    }


    public function create_new_company_user($data)
    {
        try {
            // esiste gia' l'username?
            $user_id = $this->_check_username($data['username']);

            if (empty($user_id)) {
//
//                // genero una password casuale
                $password = $this->_generate_pwd(8);
                $salt = JUserHelper::genRandomPassword(32);
                $crypt = JUserHelper::getCryptedPassword($password, $salt) . ':' . $salt;
//
//                // creo nuovo user
                $query = sprintf('INSERT INTO #__users (name, username, password, email, sendEmail, registerDate, activation) VALUES (\'%s\', \'%s\', \'%s\', \'%s\', 0, NOW(), \'\')', $data['ragione_sociale'], $data['username'], $crypt, $data['email']);
                $this->_db->setQuery($query);
                if (false === $this->_db->query())
                    throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
//                debug::msg('Nuovo utente ' . $data['username'] . ':' . $password . ' inserito.');
//
//                // id del nuovo user
                $user_id = $this->_db->insertid();


                // creo nuovo gruppo figlio di piattaforma e lo associo all'utente che ho appena creato
                if (false === ($company_group_id = $this->_create_company_group($user_id, $data['ragione_sociale'], $data["vendor"])))
                    throw new Exception('Errore nella creazione del gruppo', E_USER_ERROR);

                $this->_set_user_turor($user_id);


                // inserisco in comprofiler
                $query = 'INSERT INTO #__comprofiler (id, user_id, cb_cognome, cb_ateco) VALUES (
                ' . $user_id . ',
                ' . $user_id . ',
                \'' . $data['ragione_sociale'] . '\' ,
                \'' . $data['ateco'] . '\'
                )';

                $this->_db->setQuery($query);
                if (false === $this->_db->query()) {
                    throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);

                }
//                debug::msg('Nuovo utente aggiornata anagrafica');
//
//

//                // creo nuovo forum
//                /** @todo sostituire il 16 della riga sotto con un sistema che prelevi l'ID del gruppo tutor da DB */
//                if (false === $this->_create_company_forum($user_id, $company_group_id, $data['ragione_sociale'], $data['id_associazione'], 16))
//                    throw new Exception('Errore nella creazione del forum', E_USER_ERROR);
//
            }

            $res= array('user_id' => $user_id, 'password' => isset($password) ? $password : null);

//            var_dump($res);

            return $res;
        } catch (Exception $e) {


            var_dump($e);
            echo('eccezione in create_new_company_user');
            //debug::exception($e);
        }
        return false;
    }


    private function _check_username($username)
    {
        $query = 'SELECT id FROM #__users WHERE username=\'' . $username . '\' LIMIT 1';
        $this->_db->setQuery($query);
        if (false === ($results = $this->_db->loadRow()))
            throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
//        debug::vardump($results, 'società');
        return isset($results[0]) ? $results[0] : null;
    }

    private function _generate_pwd($l = 8)
    {
        return chr(65 + rand(0, 1) * 32 + rand(0, 25)) . ($l ? $this->_generate_pwd(--$l) : '');
    }


    /**
     * Crea un nuovo gruppo con il nome della società ($company_name) gerarchicamente sotto il gruppo della piattaforma veditrice ($parent_id) e associa
     * l'id della società ($company_id) al gruppo appena creato.
     *
     * @param int $company_id
     * @param string $company_name
     * @param  int $parent_id
     * @return int Ritorn l'ID del gruppo appena creato o FALSE in caso di errore.
     */
    private function _create_company_group($company_id, $company_name, $piattaforma_group_id)
    {

        try {
            // creo un gruppo figlio della piattaforma
            $insertquery_group = 'INSERT INTO #__usergroups (parent_id, lft,rgt, title) VALUES(';
            $insertquery_group = $insertquery_group . $piattaforma_group_id . ',';
            $insertquery_group = $insertquery_group . '0' . ',';
            $insertquery_group = $insertquery_group . '0' . ',';
            $insertquery_group = $insertquery_group . '\'' . $company_name . '\'' . ')';

            //echo $insertquery; die;
            $this->_db->setQuery($insertquery_group);
            $this->_db->execute();
            $new_group_id = $this->_db->insertid(); // id del gruppo appena inserito


            // associo utente al gruppo societa
            $insertquery_map = 'INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (' . $company_id . ', ' . $new_group_id . ')';
            $this->_db->setQuery($insertquery_map);
            $this->_db->execute();

            // rebuild usergroups to fix lft e rgt
            $JTUserGroup = new JTableUsergroup($this->_db);
            $JTUserGroup->rebuild();

            return $new_group_id;

        } catch (Exception $ex) {

            var_dump($ex);
            echo 'errore in _create_company_group';


        }


        return false;
    }

    private function _set_user_turor($user_id){

        $query = $this->_db->getQuery(true)
            ->select('config_value')
            ->from('#__gg_configs')
            ->where("config_key='id_gruppo_tutor'");

        $this->_db->setQuery($query);
        $tutor_group_id = $this->_db->loadResult();


        $insertquery_map = 'INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (' . $user_id . ', ' . $tutor_group_id . ')';
        $this->_db->setQuery($insertquery_map);
        $this->_db->execute();


    }

}
