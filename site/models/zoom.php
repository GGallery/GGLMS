<?php
/**
 * Created by IntelliJ IDEA.
 * User: Luca
 * Date: 22/02/2021
 * Time: 11:26
 */

defined('_JEXEC') or die;

class gglmsModelZoom extends JModelLegacy {

    protected $_db;
    protected $_dt;
    private $_app;

    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->_db = $this->getDbo();
        $this->_app = JFactory::getApplication();

        $this->_config = new gglmsModelConfig();
        $this->dt = new DateTime();
    }

    public function get_local_events($evento_id = "") {

        try {

            $_ret = array();

            $query = $this->_db->getQuery(true)
                ->select('id_evento AS id_local, label_evento, response')
                ->from('#__gg_zoom_events');

            if (isset($evento_id)
                && $evento_id != "")
                $query = $query->where('id_evento = ' . $this->_db->quote($evento_id));

            $query = $query->order('id ASC, tipo_evento ASC');

            $this->_db->setQuery($query);
            $results = $this->_db->loadAssocList();

            if (is_null($results))
                return $results;

            $_ret['success'] = $results;

            return $_ret;

        }
        catch (Exception $e) {
            DEBUGG::log($e->getMessage(), __FUNCTION__, 0, 1, 0);
            return $e->getMessage();
        }
    }

    public function get_event($id_evento, $tipo_evento) {

        try {

            $_ret = array();

            $query = $this->_db->getQuery(true)
                    ->select('*')
                    ->from('#__gg_zoom_events')
                    ->where('id_evento = ' . $this->_db->quote($id_evento))
                    ->where('tipo_evento = ' . $this->_db->quote($tipo_evento));

            $this->_db->setQuery($query);
            $result = $this->_db->loadAssoc();

            if (is_null($result))
                return $result;

            $_ret['success'] = $result;

            return $_ret;

        }
        catch (Exception $e) {
            DEBUGG::log($e->getMessage(), __FUNCTION__, 0, 1, 0);
            return $e->getMessage();
        }

    }

    public function get_valid_access_token($_scadenza = 3600) {

        try {

            $_ret = array();

            $query = $this->_db->getQuery(true)
                ->select('token')
                ->from('#__gg_zoom')
                ->where($this->_db->quote($this->dt->format('Y-m-d H:i:s')) . ' BETWEEN data_registrazione AND DATE_ADD(data_registrazione, INTERVAL ' . $_scadenza . ' SECOND)')
                ->order('data_registrazione DESC');

            $this->_db->setQuery($query);
            $result = $this->_db->loadResult();

            $_ret['success'] = $result;

            return $_ret;
        }
        catch (Exception $e) {
            DEBUGG::log($e->getMessage(), __FUNCTION__, 0, 1, 0);
            return $e->getMessage();
        }

    }

    public function store_events($id_evento, $tipo_evento, $label_evento, $_json) {

        try {

            $_ret = array();
            $_registrazione = $this->dt->format('Y-m-d H:i:s');
            $this->_db->transactionStart();

            $query = "INSERT INTO #__gg_zoom_events
                      (
                      id_evento,
                      tipo_evento,
                      label_evento,
                      response,
                      data_registrazione
                      )
                      VALUES
                      (
                      " . $this->_db->quote($id_evento) . ",
                      " . $this->_db->quote($tipo_evento) . ",
                      " . $this->_db->quote($label_evento) . ",
                      " . $this->_db->quote(json_encode($_json)) . ",
                      " . $this->_db->quote($_registrazione) . "
                      )";

            $this->_db->setQuery($query);
            $result = $this->_db->execute();

            $this->_db->transactionCommit();
            $_ret['success'] = 'tuttook';

            return $_ret;

        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            DEBUGG::log($e->getMessage(), __FUNCTION__, 0, 1, 0);
            return $e->getMessage();
        }

    }

    public function store_access_token($_token) {

        try {

            $_ret = array();
            $_registrazione = $this->dt->format('Y-m-d H:i:s');
            $this->_db->transactionStart();

            $query = "INSERT INTO #__gg_zoom
                        (
                            id,
                            token,
                            data_registrazione
                        ) 
                        VALUES 
                        (
                            1,
                            '" . $_token . "',
                            '" . $_registrazione . "'
                        )
                        ON DUPLICATE KEY UPDATE
                        token = '" . $_token . "',
                        data_registrazione = '" . $_registrazione . "'
                        ";

            $this->_db->setQuery($query);
            $result = $this->_db->execute();

            $this->_db->transactionCommit();
            $_ret['success'] = 'tuttook';

            return $_ret;
        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            DEBUGG::log($e->getMessage(), __FUNCTION__, 0, 1, 0);
            return $e->getMessage();
        }
    }

    public function update_zoom_log($id_utente, $codice_fiscale, $id_contenuto, $durata, $data_accesso) {

        try {

            $_ret = array();

            $this->_db->transactionStart();

            $query = "UPDATE #__gg_zoom_log SET data_accesso = CASE WHEN data_accesso = NULL THEN ". $this->_db->quote($data_accesso)
                ." WHEN data_accesso < ". $this->_db->quote($data_accesso) ." THEN data_accesso ELSE ". $this->_db->quote($data_accesso)
                ."END,durata = durata + ". $this->_db->quote($durata) ." WHERE codice_fiscale = " . $this->_db->quote($codice_fiscale)
                . " AND id_contenuto = ". $this->_db->quote($id_contenuto) ." AND id_utente = " . $this->_db->quote($id_utente);

            $this->_db->setQuery($query);
            $result = $this->_db->execute();
            $this->_db->transactionCommit();
            $_ret['success'] = 'tuttook';
            return $_ret;
        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            DEBUGG::log($e->getMessage(), __FUNCTION__, 0, 1, 0);
            return $e->getMessage();
        }
    }


    public function store_zoom_log($codice_fiscale, $id_utente, $id_contenuto) {

        try {

            $_ret = array();
            $this->_db->transactionStart();

            $query = "INSERT INTO #__gg_zoom_log
                        (
                            id_utente,
                            codice_fiscale,
                            id_contenuto
                        ) 
                        VALUES 
                        (
                            " .  $this->_db->quote($id_utente) . ",
                            " .  $this->_db->quote($codice_fiscale) . ",
                            " .  $this->_db->quote($id_contenuto) . "
                            
                        ) ON DUPLICATE KEY UPDATE
                        id_utente = " . $this->_db->quote($id_utente)  . ",
                        codice_fiscale = " . $this->_db->quote($codice_fiscale) ;

            $this->_db->setQuery($query);
            $result = $this->_db->execute();

            $this->_db->transactionCommit();
            $_ret['success'] = 'tuttook';

            return $_ret;
        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            DEBUGG::log($e->getMessage(), __FUNCTION__, 0, 1, 0);
            return $e->getMessage();
        }
    }

    //verifico se l'utente esiste nella tabella d'appoggio zoom_log
    public function get_user_id_log($codice_fiscale) {

        try {

            $query = $this->_db->getQuery(true)
                ->select('id_utente')
                ->from('#__gg_zoom_log')
                ->where('codice_fiscale = '. $this->_db->quote($codice_fiscale) );

            $this->_db->setQuery($query);
            $result = $this->_db->loadResult();


            return $result;
        }
        catch (Exception $e) {
            DEBUGG::log($e->getMessage(), __FUNCTION__, 0, 1, 0);
            return $e->getMessage();
        }

    }

    //inserimento degli utenti dalla tabella d'appoggio zoom_log a gg_log
    public function store_zoom_gg_log() {

        try {

            $_ret = array();
            $this->_db->transactionStart();

            $query = "INSERT INTO #__gg_log
                        (
                            id_utente,
                            id_contenuto,
                            data_accesso,
                            permanenza
                        ) 
                        SELECT id_utente, id_contenuto, data_accesso, durata
                        FROM #__gg_zoom_log";

            $this->_db->setQuery($query);
            $result = $this->_db->execute();

            $this->_db->transactionCommit();
            $_ret['success'] = 'tuttook';

            return $_ret;
        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            DEBUGG::log($e->getMessage(), __FUNCTION__, 0, 1, 0);
            return $e->getMessage();
        }

    }

    public function check_zoom_registrants($user_id) {

        try {

            $query = $this->_db->getQuery(true)
                ->select('id_utente')
                ->from('#__gg_zoom_log')
                ->where('id_zoom_user = '. $this->_db->quote($user_id) );

            $this->_db->setQuery($query);
            $result = $this->_db->loadResult();


            return $result;
        }
        catch (Exception $e) {
            DEBUGG::log($e->getMessage(), __FUNCTION__, 0, 1, 0);
            return $e->getMessage();
        }

    }

    public function store_zoom_users($id_user_zoom, $email) {

        try {

            $_ret = array();
            $this->_db->transactionStart();

            $query = "INSERT INTO #__gg_zoom_users
                        (
                            id_user_zoom,
                            email
                        ) 
                        VALUES 
                        (
                            " .  $this->_db->quote($id_user_zoom) . ",
                            " .  $this->_db->quote($email) . "
                            
                        )";

            $this->_db->setQuery($query);
            $result = $this->_db->execute();

            $this->_db->transactionCommit();
            $_ret['success'] = 'tuttook';

            return $_ret;
        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            DEBUGG::log($e->getMessage(), __FUNCTION__, 0, 1, 0);
            return $e->getMessage();
        }
    }

    public function check_zoom_users($user_id_zoom) {

        try {

            $query = $this->_db->getQuery(true)
                ->select('id')
                ->from('#__gg_zoom_users')
                ->where('id_user_zoom = '. $this->_db->quote($user_id_zoom) );

            $this->_db->setQuery($query);
            $result = $this->_db->loadResult();


            return $result;
        }
        catch (Exception $e) {
            DEBUGG::log($e->getMessage(), __FUNCTION__, 0, 1, 0);
            return $e->getMessage();
        }

    }

    public function get_zoom_users($user_id) {

        try {

            $query = $this->_db->getQuery(true)
                ->select('id_user_zoom')
                ->from('#__gg_zoom_users')
                ->where('id = '. $this->_db->quote($user_id) );

            $this->_db->setQuery($query);
            $result = $this->_db->loadResult();


            return $result;
        }
        catch (Exception $e) {
            DEBUGG::log($e->getMessage(), __FUNCTION__, 0, 1, 0);
            return $e->getMessage();
        }

    }

    public function store_zoom_riferimento($uuid, $id_evento, $data_evento, $id_contenuto) {

        try {

            $_ret = array();
            $this->_db->transactionStart();


            $query = "INSERT INTO #__gg_zoom_riferimento
                        (
                            id_evento,
                            uuid_evento,
                            id_contenuto,
                            data_evento
                        ) 
                        VALUES 
                        (
                            " .  $this->_db->quote($id_evento) . ",
                            " .  $this->_db->quote($uuid) . ",
                            " .  $this->_db->quote($id_contenuto) . ",
                            " .  $this->_db->quote($data_evento) . "
                        )";

            $this->_db->setQuery($query);
            $result = $this->_db->execute();

            $this->_db->transactionCommit();
            $_ret['success'] = 'tuttook';

            return $_ret;
        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            DEBUGG::log($e->getMessage(), __FUNCTION__, 0, 1, 0);
            return $e->getMessage();
        }
    }

    public function store_zoom_codice_fiscale($id_utente, $codice_fiscale, $id_zoom_user) {

        try {

            $_ret = array();
            $this->_db->transactionStart();

            $query = "INSERT INTO #__gg_zoom_codice_fiscale
                        (
                            id_utente,
                            codice_fiscale,
                            id_zoom_user
                        ) 
                        VALUES 
                        (
                            " .  $this->_db->quote($id_utente) . ",
                            " .  $this->_db->quote($codice_fiscale) . ",
                            " .  $this->_db->quote($id_zoom_user) . "
                            
                        )";

            $this->_db->setQuery($query);
            $result = $this->_db->execute();

            $this->_db->transactionCommit();
            $_ret['success'] = 'tuttook';

            return $_ret;
        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            DEBUGG::log($e->getMessage(), __FUNCTION__, 0, 1, 0);
            return $e->getMessage();
        }
    }

    public function update_zoom_codice_fiscale($id_zoom_user, $durata, $data_accesso) {

        try {

            $_ret = array();

            $this->_db->transactionStart();

            $query = "UPDATE #__gg_zoom_codice_fiscale SET data_accesso = CASE WHEN data_accesso = NULL THEN ". $this->_db->quote($data_accesso)
                ." WHEN data_accesso < ". $this->_db->quote($data_accesso) ." THEN data_accesso ELSE ". $this->_db->quote($data_accesso)
                ."END ,durata = durata + ". $this->_db->quote($durata) ." WHERE id_zoom_user = " . $this->_db->quote($id_zoom_user) ;

            $this->_db->setQuery($query);
            $result = $this->_db->execute();
            $this->_db->transactionCommit();
            $_ret['success'] = 'tuttook';
            return $_ret;
        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            DEBUGG::log($e->getMessage(), __FUNCTION__, 0, 1, 0);
            return $e->getMessage();
        }
    }

    public function get_user_id_zoom($user_id) {

        try {

            $query = $this->_db->getQuery(true)
                ->select('codice_fiscale')
                ->from('#__gg_zoom_codice_fiscale')
                ->where('id_zoom_user = '. $this->_db->quote($user_id) );

            $this->_db->setQuery($query);
            $result = $this->_db->loadResult();

            return $result;
        }
        catch (Exception $e) {
            DEBUGG::log($e->getMessage(), __FUNCTION__, 0, 1, 0);
            return $e->getMessage();
        }

    }

    public function get_user_details_zoom() {

        try {

            $query = $this->_db->getQuery(true)
                ->select('id_utente, codice_fiscale, durata, data_accesso')
                ->from('#__gg_zoom_codice_fiscale');

            $this->_db->setQuery($query);
            $result = $this->_db->loadAssocList();

            return $result;
        }
        catch (Exception $e) {
            DEBUGG::log($e->getMessage(), __FUNCTION__, 0, 1, 0);
            return $e->getMessage();
        }

    }


}
