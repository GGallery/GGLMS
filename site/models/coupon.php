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
class gglmsModelcoupon extends JModelLegacy
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

    public function corsi_abilitati($coupon)
    {
        try {

            $query = $this->_db->getQuery(true)
                ->select('corsi_abilitati')
                ->from('#__gg_coupon as c')
                ->where('c.coupon = "' . $coupon . '"');


            $this->_db->setQuery($query);
            if (false === ($results = $this->_db->loadColumn()))
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            $corsi_abilitati = empty($results) ? array() : $results;
        } catch (Exception $e) {
            DEBUGG::error($e);
            $corsi_abilitati = array();
        }

        $corsi_abilitati = implode(",", $corsi_abilitati);

        return $corsi_abilitati;
    }

    public function check_Coupon($coupon, $isRinnovo = false)
    {
        try {

            if (!$isRinnovo) {

                $query = $this->_db->getQuery(true)
                    ->select('*')
                    ->from('#__gg_coupon as c')
                    ->where('c.coupon="' . ($coupon) . '"')
                    ->where('c.id_utente IS NULL')
                    ->where('c.data_abilitazione < NOW()');

            } else {

                // caso rinnovo
                $query = $this->_db->getQuery(true)
                    ->select('*')
                    ->from('#__gg_coupon as c')
                    ->where('c.coupon="' . ($coupon) . '"');
            }


            $this->_db->setQuery($query);
            if (false === ($results = $this->_db->loadAssoc()))
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);

            $this->_coupon = empty($results) ? array() : $results;

        } catch (Exception $e) {

        }
        return $this->_coupon;
    }

    /*
     * Aggiorno la tabella coupon inserendo l'id dell'utente che sta utilizzando quel coupon
     */

    public function assegnaCoupon($coupon)
    {

        try {
            $query = '
                UPDATE
                    #__gg_coupon 
                SET id_utente = ' . $this->_userid . ', 
                data_utilizzo = NOW()
                WHERE 
                    substring_index(coupon,"@",1)    =   "' . $coupon . '" 
                ';

            $this->_db->setQuery($query);
            if (false === ($results = $this->_db->query()))
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
        } catch (Exception $e) {
            DEBUGG::error($e);
        }
        return true;
    }


    /*
     * Gli passo una stringa di id corsi e mi restituisce la lista dei corsi con relativi link.
     *
     */

    public function get_listaCorsiFast($id_corsi)
    {

        $id_corsi_array = explode(",", $id_corsi);
        if (count($id_corsi_array) > 1)
            $report = "<p><h3>Sei iscritto ai seguenti corsi: </h3></p> ";
        else
            $report = "<p><h3>Sei iscritto al seguente corso: </h3></p> ";

        foreach ($id_corsi_array as $id_corso) {
            try {

                $query = $this->_db->getQuery(true)
                    ->select('id, titolo')
                    ->from('#__gg_unit as u')
                    ->where('u.id = ' . $id_corso);

                $this->_db->setQuery($query);
                if (false === ($results = $this->_db->loadAssoc()))
                    throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
                $unita = empty($results) ? array() : $results;

            } catch (Exception $e) {
                DEBUGG::error($e);
            }
            $report .= '<p><a href="index.php?option=com_gglms&view=unita&id=' . $unita['id'] . '">' . $unita['titolo'] . '</a></p>';
        }
        return $report;
    }

    public function setUsergroupUserGroup($ids)
    {

        try {
            $list = explode(',', $ids);

            foreach ($list as $id_gruppo) {
                if ($id_gruppo) {
                    $query = "INSERT IGNORE INTO #__user_usergroup_map VALUE($this->_userid , $id_gruppo)";
                    $this->_db->setQuery($query);
                    $this->_db->execute();
                }
            }


        } catch (Exception $e) {
            throw new BadMethodCallException('Errore nella procedura setUsergroupUserMap', E_USER_ERROR);
        }

    }

    /////////////////////////////////////////////////

    public function check_already_enrolled($coupon)
    {
        try {
            $query = $this->_db->getQuery(true)
                ->select('count(*)')
                ->from('#__gg_coupon as c')
                ->where('c.id_societa=' . (int)$coupon['id_societa'])
                ->where('c.id_utente=' . $this->_userid)
                ->where('c.id_gruppi=' . (int)$coupon['id_gruppi']);


            $this->_db->setQuery($query);
            $count = (int)$this->_db->loadResult();

            return $count > 0 ? true : false;


        } catch (Exception $e) {

            throw new BadMethodCallException('Errore nella procedura check_already_enrolled', E_USER_ERROR);
        }

    }

    public function is_coupon_expired_by_corso($corso)
    {
        try {

            // se il corso non prevede coupon non è scaduto a prescindere
            if ($corso->usa_coupon == 0)
                return false;

            $subQuery = $this->_db->getQuery(true)
                ->select('idgruppo')
                ->from('#__gg_usergroup_map AS ug')
                ->join('inner', '#__user_usergroup_map AS uj ON uj.group_id = ug.idgruppo')
                ->where('ug.idunita = ' . $corso->id)//parametrizzare con campo EB
                ->where('uj.user_id= ' . $this->_user->id);


            // calcolo data_scadenza_calc come data_scadenza + durata
            // aggiunto order by data scadenza così da prendere il coupon con la data di scadenza meno prossima in caso di corrispondenza multiple
            // eseguo il test della scandenza tipo mysql
            $query = $this->_db->getQuery(true)
                ->select('DATE_ADD(c.data_utilizzo,INTERVAL c.durata DAY) as data_scadenza_calc, 
                            c.data_utilizzo,
                            (case when ((c.data_utilizzo + interval c.durata day) < now()) then 1 else 0 end) AS scaduto
                            ')
                ->from('#__gg_coupon AS c')
                ->where('c.id_utente = ' . $this->_user->id)
                ->where($this->_db->quoteName('id_gruppi') . ' IN (' . $subQuery->__toString() . ')')
                ->order('data_scadenza_calc desc');

            $this->_db->setQuery($query);

            if (null === ($results = $this->_db->loadAssoc())) {
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            }

            $data_scadenza_calc = strtotime($results['data_scadenza_calc']);
            $today = strtotime(date("Y-m-d"));

//            var_dump($data_scadenza_calc < $today );
//            die();

            // se $data_scadenza_calc è minore di oggi il coupon è expired
            // passo direttamente il risultato della valutazione in mysql
            //return $data_scadenza_calc < $today ? true : false;
            return $results['scaduto'];


        } catch (Exception $e) {

            //var_dump($e);
            //DEBUGG::error($e, 'is_coupon_expired_by_corso');
            DEBUGG::error($e->getMessage(), 'is_coupon_expired_by_corso');
        }

        return false;
    }

    public function is_logged_user_tutor()
    {


        $user = new gglmsModelUsers();
        $user->get_user($this->_userid);

        return $user->is_tutor_piattaforma($this->_userid) || $user->is_tutor_aziendale($this->_userid);


    }

    public function check_id_societa_match_user($id_societa_coupon, $user_id)
    {

        $user = new gglmsModelUsers();
        $lista_societa_utente = array_column($user->get_user_societa($user_id, false), 'id');

        return in_array($id_societa_coupon, $lista_societa_utente);

    }

    public function is_expired($coupon)
    {

        try {

            // rifaccio la query per far fare il calcolo a sql
            $query = $this->_db->getQuery(true)
                ->select('DATE_ADD(c.data_utilizzo,INTERVAL c.durata DAY) as data_scadenza_calc , c.data_utilizzo')
                ->from('#__gg_coupon AS c')
                ->where("c.coupon = '" . $coupon . "'");

            $this->_db->setQuery($query);

            if (null === ($results = $this->_db->loadAssoc())) {
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            }

            $data_scadenza_calc = new DateTime($results['data_scadenza_calc']);
            $today = new DateTime(date("Y-m-d"));

            // se $data_scadenza_calc è minore di oggi il coupon è expired
            return $data_scadenza_calc < $today ? true : false;


        } catch (Exception $e) {
            DEBUGG::error($e, 'is_expired');
        }

    }

    public function rinnova_coupon($coupon)
    {

        try {
            // rifaccio la query per far fare il calcolo a sql

            // calcolo i gg di differenza tra data_utilizzo e oggi
            // nuova_durata = durata + gg + 60

            $query = $this->_db->getQuery(true)
                ->select('DATEDIFF(CURDATE(),c.data_utilizzo) AS diff_days, durata as current_durata')
                ->from('#__gg_coupon AS c')
                ->where("c.coupon = '" . $coupon . "'");
            $this->_db->setQuery($query);

            if (null === ($res = $this->_db->loadAssoc())) {
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            }

            $new_durata = (int)$res['diff_days'] + (int)$res['current_durata'];


            $updatequery = "UPDATE #__gg_coupon set durata= " . $new_durata . " WHERE coupon= '" . $coupon . "'";
            $this->_db->setQuery($updatequery);
            $this->_db->execute();

            return true;

        } catch (Exception $e) {
            DEBUGG::error($e, 'rinnova_coupon');
            return false;
        }

    }

    public function disattiva_coupon($codice_coupon) {

        try {

            $_ret = array();
            $dt = new DateTime();
            $query = $this->_db->getQuery(true);
            $query->update('#__gg_coupon')
                        ->set('durata = 0')
                        ->where('coupon = ' . $this->_db->quote($codice_coupon));
            $this->_db->setQuery($query);
            $result = $this->_db->execute();

            if (!$result)
                throw new Exception("Update query failed", 1);

            $_ret['success'] = "tuttook";
            return $_ret;

        }
        catch (Exception $e) {
            DEBUGG::log($e->getMessage(), __FUNCTION__, 0, 1, 0);
            return __FUNCTION__ . " errore: " . $e->getMessage();
        }

    }
}
