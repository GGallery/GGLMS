<?php

/**
 * WebTVContenuto Model
 *
 * @package    Joomla.Components
 * @subpackage WebTV
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
require_once JPATH_COMPONENT . '/models/contenuto.php';
require_once JPATH_COMPONENT . '/models/unita.php';
require_once JPATH_COMPONENT . '/models/coupon.php';
require_once JPATH_COMPONENT . '/models/users.php';
require_once JPATH_COMPONENT . '/models/config.php';


/**
 * WebTVContenuto Model
 *
 * @package    Joomla.Components
 * @subpackage WebTV
 */
class gglmsModelReport extends JModelLegacy
{

    private $_dbg;
    private $_app;
    private $_userid;
    protected $params;
    protected $_db;
    protected $_config;


    public function __construct($config = array())
    {
        parent::__construct($config);

        $user = JFactory::getUser();
        $this->_userid = $user->get('id');

        $this->_db = JFactory::getDbo();

        $this->_app = JFactory::getApplication('site');
        $this->params = $this->_app->getParams();

        $this->_config = new gglmsModelConfig();

        $this->populateState();
    }

    public function __destruct()
    {

    }

    protected function populateState()
    {
        $app = JFactory::getApplication('site');

        //PERSONALIZZARE IL CORSO BASE

        $id_corso = $app->input->getInt('id_corso', 2);
        $this->setState('id_corso', $id_corso);


        $offset = $app->input->getUInt('limitstart');
        $this->setState('list.offset', $offset);

        // Load the parameters.
        $params = $app->getParams();
        $this->setState('params', $params);

    }


    public function getUser()
    {

        try {

            $query = $this->_db->getQuery(true);
            $query->select('distinct id_utente, id_event_booking');
            $query->from('report');
            $query->where('id_corso = ' . $this->getState('id_corso'));
            $query->order('id_utente');

            $this->_db->setQuery($query);

            $utenti = $this->_db->loadObjectList();

            foreach ($utenti as $utente) {

                $modelUtente = new gglmsModelUsers();
                $utente->info = $modelUtente->get_user($utente->id_utente, $utente->id_event_booking);
                $utente->report = $this->getUserReport($utente->id_utente);
            }

            return $utenti;

        } catch (Exception $e) {
            DEBUGG::query($query, 'query USer');
            DEBUGG::error($e, 'getUser', 1);

        }

    }

    private function getUserReport($user_id)
    {
        try {

            $query = $this->_db->getQuery(true);
            $query->select('id_contenuto, stato, `data`');
            $query->from('report');
            $query->where('id_corso = ' . $this->getState('id_corso'));
            $query->where('id_utente = ' . $user_id);

            $this->_db->setQuery($query);

            $data = $this->_db->loadAssocList('id_contenuto');

            return $data;
        } catch (Exception $e) {
            DEBUGG::query($query, 'getUserReport');
            DEBUGG::error($e, 'getUserReport', 1);
        }
    }

    public function getOutput()
    {
        try {
            $query = $this->_db->getQuery(true);

            $query->select('corso.titolo as titolo_corso, corso.id_event_booking, corso.id_contenuto_completamento, unita.titolo as titolo_unita, contenuti.titolo as titolo_contenuto, r.*');
            $query->from('#__gg_report as r');

            $query->join('inner', '#__gg_unit as corso on corso.id = r.id_corso ');
            $query->join('inner', '#__gg_unit as unita on unita.id = r.id_unita ');

            $query->join('inner', '#__gg_contenuti as contenuti on contenuti.id = r.id_contenuto');

            if ($this->getState('id_corso'))
                $query->where('corso.id_contenuto_completamento = contenuti.id');

            $this->_db->setQuery($query);
            $data = $this->_db->loadObjectList();

            foreach ($data as &$row) {
                $utente = new gglmsModelUsers();
                $row->utente = $utente->get_user($row->id_utente, $row->id_event_booking);
                unset($utente);
            }

            return $data;

            //


        } catch (Exception $e) {

            DEBUGG::query($query, 'get Output', 0);
            DEBUGG::error($e, 'errore get Output', 1);
        }

    }


    public function getSottoUnita($item = 0)
    {
        $tree = array();

        $query = $this->_db->getQuery(true);

        $query->select('a.id, a.titolo');
        $query->from('#__gg_unit AS a');
        $query->where("unitapadre=" . $item);

        $this->_db->setQuery($query);

        $tmptree = $this->_db->loadObjectList();
        foreach ($tmptree as $item) {
            array_push($tree, $item);
            foreach ($this->getSottoUnita($item->id) as $item2) {
                $item2->titolo = "<span class=\"icon-forward-2\"> </span>" . $item2->titolo;
                array_push($tree, $item2);
            }
            $item->contenuti = $this->getContenutiUnita($item->id);
        }
        unset($tmptree);
        return $tree;
    }

    public function getSottoUnitaArrayList($item = 0)
    {
        $tree = array();

        $query = $this->_db->getQuery(true);

        $query->select('a.id, a.titolo');
        $query->from('#__gg_unit AS a');
        $query->where("unitapadre=" . $item);
        $query->order('a.ordinamento');
        $query->order('a.id');


        $this->_db->setQuery($query);

        $tmptree = $this->_db->loadAssocList();
        foreach ($tmptree as $item) {
            array_push($tree, $item);
            foreach ($this->getSottoUnitaArrayList($item['id']) as $item2) {
                //$item2['titolo'] = $item2['titolo'];
                array_push($tree, $item2);
            }

        }
        unset($tmptree);
        return $tree;
    }

    public function getContenutiArrayList($item = 0)
    {
        $contenuti = array();


        $unitas = $this->getSottoUnitaArrayList($item);

        foreach ($unitas as $unita) {

            foreach ($this->getContenutiUnitaArrayList($unita['id']) as $contenuto) {
                array_push($contenuti, $contenuto);
            }
        }

        foreach ($this->getContenutiUnitaArrayList($item) as $contenuto) {
            array_push($contenuti, $contenuto);
        }

        return $contenuti;
    }

    public function getContenutiUnita($item)
    {

        try {
            $query = $this->_db->getQuery(true);

            $query->select('c.id, c.titolo');
            $query->from('#__gg_unit_map AS a');
            $query->join('inner', '#__gg_contenuti AS c on c.id = a.idcontenuto');
            $query->where("idunita=" . $item);
            $query->order('a.ordinamento');

            $this->_db->setQuery($query);
            $data = $this->_db->loadObjectList();

            return $data;
        } catch (Exception $e) {

            DEBUGG::query($query, 'query contenuti unita');
            DEBUGG::error($e, 'errore get Conteuti unita', 1);

        }
    }

    public function getContenutiUnitaArrayList($item)
    {

        try {
            $query = $this->_db->getQuery(true);

            $query->select('c.id as id, c.titolo as titolo');
            $query->from('#__gg_unit_map AS a');
            $query->join('inner', '#__gg_contenuti AS c on c.id = a.idcontenuto');
            $query->where("idunita=" . $item);
            $query->order('a.ordinamento');
            $query->order('a.idunita');
            $query->order('c.id');

            $this->_db->setQuery($query);
            $data = $this->_db->loadAssocList();

//            var_dump((string)$query);
//            die();

            return $data;
        } catch (Exception $e) {

            DEBUGG::query($query, 'query contenuti unita');
            DEBUGG::error($e, 'errore get Conteuti unita', 1);

        }
    }

    public function getCorsi($by_platform = null)
    {

        $corsi_ammessi_utente = $this->get_report_view_permessi();

        $query = $this->_db->getQuery(true);

        $query->select('distinct a.*');
        $query->from('#__gg_unit AS a');
        if ($corsi_ammessi_utente != null) {
            $query->where("is_corso=1 and id in (" . $corsi_ammessi_utente . ")");
        } else {
            $query->where("is_corso=1 ");
        }


        if ($by_platform == true) {

            $model_user = new gglmsModelUsers();
            $id_piattaforma = $model_user->get_user_piattaforme($this->_userid);
            $id_piattaforma_array = array();


            foreach ($id_piattaforma as $p) {
                array_push($id_piattaforma_array, $p->value);
            }

            $query->join('inner', '#__gg_piattaforma_corso_map AS pc ON pc.id_unita = a.id');
            $query->join('inner', '#__usergroups_details AS ud ON pc.id_gruppo_piattaforma = ud.group_id');
            $query->where("a.pubblicato=1");
            //$query->where(" ud.dominio = '" . DOMINIO . "'");
            $query->where($this->_db->quoteName('ud.group_id') . ' IN (' . implode(", ", $id_piattaforma_array) . ')');

            //NB con il barbatrucco dei coupon la piattaforma di riferimento non è più quella del dominio MA quella dell'utente collegato
            //nella query uso WHERE IN perchè se l'utente collegato è super_admin e vede più piattaforme


        }
        $query->order('a.titolo');
        $this->_db->setQuery($query);
        $corsi = $this->_db->loadObjectList();

        return $corsi;

    }

    public function getSummarizeCourse()
    {

        $query = $this->_db->getQuery(true);

        $query->select('stato, count(stato) as total ');
        $query->from('#__gg_report AS r');
        $query->where("id_corso=" . $this->getState('id_corso'));
        $query->group('stato');

        $this->_db->setQuery($query);

        $summarize = $this->_db->loadAssocList('stato');

        return $summarize;
    }

    public function insertUserLog($id_utente, $id_contenuto, $supporto = null, $ip_address, $uniqid)
    {

        $insertquery = "";

        try {

            $insertquery = 'INSERT INTO #__gg_log (id_utente, id_contenuto,data_accesso, supporto, ip_address, uniqid, permanenza) VALUES(';
            $insertquery = $insertquery . $id_utente . ',';
            $insertquery = $insertquery . $id_contenuto . ',';
            $insertquery = $insertquery . 'NOW(),';
            $insertquery = $insertquery . $supporto . ',\'';
            $insertquery = $insertquery . $ip_address . '\',';
            $insertquery = $insertquery . $uniqid . ',';
            $insertquery = $insertquery . '0)';

            //utilityHelper::make_debug_log(__FUNCTION__, $insertquery, __FUNCTION__);

            $this->_db->setQuery($insertquery);
            //$this->_db->execute();

            // gestione dell'errore di query
            if (!$this->_db->execute())
                throw new Exception($this->_db->getErrorMsg(), E_USER_ERROR);

            return true;

        } catch (Exception $ex) {
            //DEBUGG::log('QUERY: ' . $insertquery . ' ERR: ' . $ex->getMessage(),__FUNCTION__,0,1);
            utilityHelper::make_debug_log(__FUNCTION__, $insertquery . ' ERR: ' . $ex->getMessage(), __FUNCTION__);
            return false;
        }

    }

    public function updateUserLog($uniquid)
    {

        $updatequery = "";

        try {

            $updatequery = 'UPDATE #__gg_log
                                    set permanenza=TIME_TO_SEC(TIMEDIFF(NOW(),data_accesso))
                                    where uniqid=' . $uniquid;

            //utilityHelper::make_debug_log(__FUNCTION__, $updatequery, __FUNCTION__);

            $this->_db->setQuery($updatequery);
            //$this->_db->execute();

            // gestione dell'errore di query
            if (!$this->_db->execute())
                throw new Exception($this->_db->getErrorMsg(), E_USER_ERROR);

            return true;

        } catch (Exception $ex) {
            //DEBUGG::log('QUERY: ' . $updatequery . ' ERR: ' . $ex->getMessage(),__FUNCTION__,0,1);
            utilityHelper::make_debug_log(__FUNCTION__, $updatequery . ' ERR: ' . $ex->getMessage(), __FUNCTION__);
            return false;
        }

    }

    public function getUtentiInScadenzaCorso($corso_id)
    {

        try {
            $result = null;
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('id,accesso,id_event_booking, titolo');
            $query->from('#__gg_unit');
            $query->where('is_corso=1 and IF(date(now())>DATE_ADD(data_fine, INTERVAL -30 DAY),1,0)=1 and id=' . $corso_id);
            $db->setQuery($query);
            $corso = $db->loadObjectList();
            if ($corso) {
                $corso = $corso[0];

                switch ($corso->accesso) {

                    case 'iscrizioneeb':
                        $query = $db->getQuery(true);
                        $query->select('*');
                        $query->from('#__gg_report_users');
                        $query->where('id_event_booking=' . $corso->id_event_booking . ' and id not in (select id_anagrafica from #__gg_view_stato_user_corso where id_corso=' . $corso->id . ' and stato=1)');

                        $db->setQuery($query);
                        $result['titolo'] = $corso->titolo;
                        $result['rows'] = $db->loadObjectList();

                        break;
                    case 'gruppo':
                        $query = $db->getQuery(true);
                        $query->select('anagrafica.*');
                        $query->from('#__gg_report_users as anagrafica');
                        $query->join('inner', '#__user_usergroup_map as um on anagrafica.id_user=um.user_id');
                        $query->join('inner', '#__gg_usergroup_map as m on m.idgruppo=um.group_id');
                        $query->where('m.idunita=' . $corso->id . ' and anagrafica.id not in ( select id_anagrafica from #__gg_view_stato_user_corso where id_corso=' . $corso->id . ' and stato=1)');

                        $db->setQuery($query);
                        $result['titolo'] = $corso->titolo;
                        $result['rows'] = $db->loadObjectList();


                        break;
                }

            }
            return $result;
        } catch (Exception $e) {

            echo $e->getMessage();
        }


    }

    private function get_report_view_permessi()
    {

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id_corsi');
        $query->from('#__gg_report_view_permessi');
        $query->where('id_utente=' . $this->_userid);
        $db->setQuery($query);
        return $db->loadResult();

    }

    private function get_report_view_permessi_gruppi()
    {

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id_gruppi');
        $query->from('#__gg_report_view_permessi_gruppi');
        $query->where('id_utente=' . $this->_userid);
        $db->setQuery($query);
        return $db->loadResult();

    }


}

