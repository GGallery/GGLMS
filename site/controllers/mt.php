<?php
/**
 * Created by IntelliJ IDEA.
 * User: Francesca
 * Date: 26/01/2021
 * Time: 09:10
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.model');
require_once JPATH_COMPONENT . '/models/contenuto.php';
require_once JPATH_COMPONENT . '/models/syncdatareport.php';

class gglmsControllerMt extends JControllerLegacy {

    private $_user;
    private $_japp;
    public $_params;
    public $_db;
    private $_config;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();

        $this->_user = JFactory::getUser();
        $this->_db = &JFactory::getDbo();
        $this->_config = new gglmsModelConfig();

        $this->_filterparam->id_utente = JRequest::getVar('id_utente');
        $this->_filterparam->id_corso = JRequest::getVar('id_corso');

    }

    public function test_() {

        $this->_japp->close();

    }



    public function get_tz() {

        $oggi = '2020-11-16T07:21:36Z';
        $dt = new DateTime($oggi, new DateTimeZone('UTC'));
        $dt->setTimezone(new DateTimeZone('Europe/Rome'));
        echo $dt->format('Y-m-d H:i:s');

        $this->_japp->close();

    }


    public function get_last_insert_coupon() {

        try {

            $_ret = array();

            $query = $this->_db->getQuery(true)
                    ->select('messaggio')
                    ->from('#__gg_error_log')
                    ->where('messaggio LIKE ' . $this->_db->quote('%api_genera_coupon_response%'))
                    ->order('id DESC');

            $this->_db->setQuery($query);
            $result = $this->_db->loadAssoc();

            if (is_null($result)
                || !isset($result['messaggio'])
                || $result['messaggio'] == "")
                throw new Exception("Nessun riferimento trovato", 1);

            $_response = preg_replace('/\s/', '', $result['messaggio']);
            $_response = str_replace("api_genera_coupon_response:", "", $_response);

            $_decode = json_decode($_response);

            if (
                (is_object($_decode) && !isset($_decode->id_iscrizione))
                    || (is_array($_decode) && !isset($_decode['id_iscrizione']))
                )
                throw new Exception("Il riferimento ha un valore non valido", 1);


            $_ret['success'] = (is_object($_decode)) ? $_decode->id_iscrizione : $_decode['id_iscrizione'];

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        echo json_encode($_ret);
        $this->_japp->close();

    }

}
