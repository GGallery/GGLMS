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

}
