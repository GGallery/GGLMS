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
class gglmsModelcoupon extends JModelLegacy {

    private $_japp;
    private $_coupon;
    protected $_db;
    private $_userid;
    private $_user;

    public function __construct($config = array()) {
        parent::__construct($config);
        $this->_dbg = JRequest::getBool('dbg', 0);
        $this->_japp =  JFactory::getApplication();
        $this->_db =  JFactory::getDbo();
        $this->_user = JFactory::getUser();
        $this->_userid = $this->_user->get('id');

    }

    public function __destruct() {

    }

    public function corsi_abilitati($coupon) {
        try {

            $query = $this->_db->getQuery(true)
                ->select('corsi_abilitati')
                ->from('#__gg_coupon as c')
                ->where('c.coupon = "' .$coupon .'"');


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

    public function check_Coupon($coupon) {
        try {
            $query = $this->_db->getQuery(true)
                ->select('*')
                ->from('#__gg_coupon as c')
                ->where('c.coupon="'.($coupon).'"')
                ->where('c.id_utente IS NULL')
                ->where('c.data_abilitazione < NOW()');

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

    public function assegnaCoupon($coupon) {

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

    public function get_listaCorsiFast($id_corsi) {

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
            $report.='<p><a href="index.php?option=com_gglms&view=unita&id=' . $unita['id'] . '">' . $unita['titolo'] . '</a></p>';
        }
        return $report;
    }

}
