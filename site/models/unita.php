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
require_once JPATH_COMPONENT . '/models/coupon.php';
require_once JPATH_COMPONENT . '/models/syncdatareport.php';
require_once JPATH_COMPONENT . '/models/report.php';
//require_once JPATH_COMPONENT . '/models/unita.php';

/**
 * WebTVContenuto Model
 *
 * @package    Joomla.Components
 * @subpackage WebTV
 */
class gglmsModelUnita extends JModelLegacy
{

    private $_dbg;
    private $_app;
    private $_userid;
    public $_params;
    protected $_db;
    private $unitas = array();
    public $contenuti = array();


    public function __construct($config = array())
    {
        parent::__construct($config);

        $user = JFactory::getUser();
        $this->_userid = $user->get('id');

        $this->_db = $this->getDbo();

        $this->_app = JFactory::getApplication('site');
        $this->_params = $this->_app->getParams();


    }


    protected function populateState()
    {
        $app = JFactory::getApplication('site');

        // Load state from the request.
        $pk = $app->input->getInt('id');
        $this->setState('unita.id', $pk);

        $offset = $app->input->getUInt('limitstart');
        $this->setState('list.offset', $offset);


        // Load the parameters.
        $params = $app->getParams();
        $this->setState('params', $params);


        $this->setState('filter.language', JLanguageMultilang::isEnabled());
    }

    public function getUnita($pk = null)
    {
        $pk = (!empty($pk)) ? $pk : (int)$this->getState('unita.id');
        try {
            $query = $this->_db->getQuery(true)
                ->select('*')
                ->from('#__gg_unit as u')
                ->where('u.id = ' . (int)$pk)
                ->where('u.pubblicato = 1');


            $this->_db->setQuery($query);
            $unit = $this->_db->loadObject('gglmsModelUnita');

            if (empty($unit)) {
                return JError::raiseError(404, JText::_('Unita non disponibile -->') . (string)$query);
            }
        } catch (Exception $e) {
            DEBUGG::log($e, 'getUnita');
        }

        return $unit;
    }


    public function getSottoUnita($pk = null, $is_attestato = null)
    {
        if ($pk) {
            $this->id = $pk;
        }

        try {
            $query = $this->_db->getQuery(true)
                ->select('*')
                ->from('#__gg_unit as u')
                ->where('u.unitapadre  = ' . $this->id)
                ->where('u.pubblicato = 1')
                ->order('ordinamento')
                ->order('id');
            $this->_db->setQuery($query);
            $data = $this->_db->loadObjectList('', 'gglmsModelUnita');

//            DEBUGG::log($data, 'getSottoUnita', 1);

        } catch (Exception $e) {
            DEBUGG::log($e, 'getSottoUnita');
        }

//	
        return $data;
    }


    public function getContenuti($is_attestato = null, $unit_id = null)
    {

        $query_id = $this->id;
        if ($unit_id != null) {
            $query_id = $unit_id;
        }

        try {
            $query = $this->_db->getQuery(true)
                ->select('c.*')
                ->from('#__gg_unit_map as m')
//                ->where('m.idunita = ' . $this->id)
                ->where('m.idunita = ' . $query_id)
                ->innerJoin('#__gg_contenuti as c on c.id = m.idcontenuto')
                ->where('c.pubblicato = 1')
                ->order('m.ordinamento');


            if ($is_attestato == 1) {
                $query = $query->where('c.tipologia = 5');
            }

            $this->_db->setQuery($query);
            $contenuti = $this->_db->loadObjectList('', 'gglmsModelContenuto');

        } catch (Exception $e) {
            DEBUGG::log($e, 'getContenuti');

        }
        return $contenuti;
    }


    public function isUnitaCompleta($pk = null, $userid = null)
    {

        $pk = (!empty($pk)) ? $pk : (int)$this->getState('unita.id');
        $this->getSottoUnitaRic($pk);//CHIAMATA ALLA FUNZIONE RICORSIVA
        $result = $this->getContenuti($pk);//QUI CARICHIAMO I CONTENUTI ALLA RADICE DELL'UNITA
        if ($result) {

            foreach ($result as $res) {
                array_push($this->contenuti, $res); //LA VARIABILE DI CLASSE contenuti E' QUELLA CHE VIENE POPOLATA DALLA RICORSIVA
            }
        }

        foreach ($this->contenuti as $contenuto) {   //ANALISI DI OGNI CONTENUTO: APPENA NE TROVI UNO NON COMPLETO, ESCI FALSE

            $contenutoObj = new gglmsModelContenuto();
            $obj = $contenutoObj->getContenuto($contenuto->id);
            if ($obj->getStato($userid)->completato == 0) {
                return false;
            }
        }
        return true;
    }

    public function getSottoUnitaRic($pk = null, $is_attestato = null)
    {
        $result = $this->getSottoUnita($pk);

        if ($result != null) {
            if ($this->unitas == null) {
                $this->unitas = $result;
            } else {
                array_push($this->unitas, $result);
            }
        } else {

            // non ha sotto unità cerco i soi contenuti
            $content_list = $this->getContenuti($is_attestato, $pk);
            foreach ($content_list as $c) {
                array_push($this->contenuti, $c);
            }

        }

        foreach ($result as $unita) {

            $result = $this->getContenuti($is_attestato, $this->id);
            foreach ($result as $res) {

                array_push($this->contenuti, $res); //LA VARIABILE DI CLASSE contenuti E' QUELLA CHE VIENE POPOLATA DALLA RICORSIVA
            }
            $this->getSottoUnitaRic($unita->id, $is_attestato);
        }
        return;
    }

    public function access()
    {

        if ($this->is_corso || $this->id == 1)
            return $this->access_tipology($this);
        else
            return $this->access_tipology($this->find_corso($this->unitapadre));

    }

    public function find_corso($check)
    {
        try {
            $query = $this->_db->getQuery(true)
                ->select('*')
                ->from('#__gg_unit as u')
                ->where('u.id  = ' . $check);
            $this->_db->setQuery($query);
            $data = $this->_db->loadObject();

            if ($data->pubblicato == 0) { //APPENA UNA UNITA' SUPERIORE NON E' PUBBLICATA, ESCI
                return $data;
            }


            if ($data->id == 1 && !$data->is_corso) {
                $this->_app->enqueueMessage('L\'Unita alla quale hai tentato di accedere e nessuna di quelle dei livelli superiori sono impostate come UNITA-CORSO. Finchè non sarà definito il corso padre non potrai accedere a questa unità', 'Error');
                $this->_app->redirect('index.php');

            }
            return ($data->is_corso) ? $data : $this->find_corso($data->unitapadre);

        } catch (Exception $e) {
            $this->setError($e);
        }
    }

    public function access_tipology($corso)
    {
        $access_list = explode(",", $corso->accesso);

        if ($corso->accesso) {
            foreach ($access_list as $metodo) {
                switch ($metodo) {
                    case 'coupon':
                        return $this->check_Standard_Coupon($corso);
                        break;

                    case 'couponeb':
                        return $this->check_EventBookingField_Coupon($corso);
                        break;

                    case 'iscrizioneeb':
                        return $this->check_iscrizione_eb($corso);
                        break;

                    case 'gruppo':
                        return $this->check_iscrizione_gruppo($corso);
                        break;
                }
            }
        }

        return true;
    }

    private function check_Standard_Coupon($corso)
    {
        try {
            $query = $this->_db->getQuery(true)
                ->select('count(coupon)')
                ->from('#__gg_coupon as u')
                ->where("u.id_utente = $this->_userid")
                ->where("u.corsi_abilitati = $corso->id")
                ->where("(data_scadenza > current_date() OR data_scadenza IS NULL)")
                ->where("if(durata is not null, DATEDIFF(DATE_ADD(data_utilizzo, INTERVAL durata DAY), current_date()) > 0, true)");


            $this->_db->setQuery($query);
            $data = $this->_db->loadResult();

            if ($data == 0)
                return false;

//            if ($data == 0)
//            {
//                $query_ = $this->_db->getQuery(true)
//                    ->select('count(coupon)')
//                    ->from('#__gg_coupon as u')
//                    ->where("u.id_utente = $this->_userid");
//
//
////echo $query;
//                $this->_db->setQuery($query_);
//                $data_ = $this->_db->loadResult();
//
//                if($data_==0) {
//                    $message = "Inserire il coupon per accedere a questa unita";
//                    $url = JRoute::_('index.php?option=com_gglms&view=coupon');
//                    $this->_app->redirect($url, $message);
//                }
//
//            } else
            return true;

        } catch (Exception $e) {
            $this->setError($e);
        }
    }

    private function check_EventBookingField_Coupon($corso)
    {
        try {
            $query = $this->_db->getQuery(true)
                ->select('count(coupon)')
                ->from('#__gg_coupon as c')
                ->join('inner', '#__eb_field_values as v on c.coupon = v.field_value')
                ->join('inner', '#__eb_registrants as r on v.registrant_id = r.id')
                ->where('v.field_id = ' . $this->_params->get('campo_event_booking_auto_abilitazione_coupon'))
                ->where('r.user_id= ' . $this->_userid)
                ->where('r.published = 1 ')
                ->where('r.event_id = ' . $corso->id_event_booking)
                ->where('abilitato = 1')
                ->where('FIND_IN_SET(' . $corso->id . ', corsi_abilitati)');

            $this->_db->setQuery($query);
            $data = $this->_db->loadResult();

            if ($data == 0)
                return false;
            else
                return true;
        } catch (Exception $e) {
            $this->setError($e);
        }
    }

    private function check_iscrizione_eb($corso)
    {

        try {
            $query = $this->_db->getQuery(true)
                ->select('count(id)')
                ->from('#__eb_registrants as r')
                ->where('r.event_id = ' . $corso->id_event_booking)//parametrizzare con campo EB
                ->where('r.user_id= ' . $this->_userid)
                ->where('r.published = 1 ');

            $this->_db->setQuery($query);
            $data = $this->_db->loadResult();
//echo($query);
//var_dump($data);die;

            if ($data == 0)
                return false;
            else
                return true;
        } catch (Exception $e) {
            DEBUGG::query($query);
            DEBUGG::log($e, 'check_iscrizione_eb', 1);

        }
    }


    public function check_iscrizione_gruppo($corso)
    {

        try {
            $query = $this->_db->getQuery(true)
                ->select('count(idunita)')
                ->from('#__gg_usergroup_map AS ug')
                ->join('inner', '#__user_usergroup_map AS uj ON uj.group_id = ug.idgruppo')
                ->where('ug.idunita = ' . $corso->id)//parametrizzare con campo EB
                ->where('uj.user_id= ' . $this->_userid);

            $this->_db->setQuery($query);
            $data = $this->_db->loadResult();

            if ($data == 0)
                return false;
            else
                return true;
        } catch (Exception $e) {
            DEBUGG::query($query);
            DEBUGG::log($e, 'check_iscrizione_gruppo', 1);

        }
    }

    public function get_durata_unita($pk = null)
    {
        try {
            $pk = (!empty($pk)) ? $pk : (int)$this->getState('unita.id');
            $repotObj = new gglmsModelReport();
            $contenuti = $repotObj->getContenutiArrayList($pk);

            if (sizeof($contenuti) == 0)
                return self::convertiDurata(0);

            $contenuti = implode(',', array_column($contenuti, 'id'));
            $query = $this->_db->getQuery(true)
                ->select('SUM(durata)')
                ->from('#__gg_contenuti AS c')
                ->where('c.id in (' . $contenuti . ')');

            $this->_db->setQuery($query);
            $data = $this->_db->loadResult();

        } catch (Exception $e) {

        }
        return self::convertiDurata($data);
    }

    public static function convertiDurata($durata)
    {
        $h = floor($durata / 3600);
        $m = floor(($durata % 3600) / 60);
        $s = ($durata % 3600) % 60;
        $result = sprintf('h:%02d m:%02d s:%02d', $h, $m, $s);
        return $result;
    }

    ////////////////////////////////////////////////
    public function isStampaTracciato($user_id = null)
    {

        try {

            if ($user_id) {
                $user_id = $user_id != null ? $user_id : $this->_userid;
            }


            if ($this->is_corso == 1) {

                //id gruppo by utente e by idcorso
                $subQuery = $this->_db->getQuery(true)
                    ->select('idgruppo')
                    ->from('#__gg_usergroup_map AS ug')
                    ->join('inner', '#__user_usergroup_map AS uj ON uj.group_id = ug.idgruppo')
                    ->where('ug.idunita = ' . $this->id)//parametrizzare con campo EB
                    ->where('uj.user_id= ' . $user_id);

                // uso subquery per tovare il coupon giusto
                $query = $this->_db->getQuery(true)
                    ->select('stampatracciato')
                    ->from('#__gg_coupon AS c')
                    ->where('c.id_utente = ' . $user_id)
                    ->where($this->_db->quoteName('id_gruppi') . ' IN (' . $subQuery->__toString() . ')');


                $this->_db->setQuery($query);

                $data = $this->_db->loadResult();
//                var_dump((string)$query);
//                die();

                if ($data == 0)
                    return false;
                else
                    return $data;
            } else {
                $this->_app->enqueueMessage('L\'Unita non è un corso', 'Error');
                $this->_app->redirect('index.php');
            }


        } catch (Exception $e) {
            DEBUGG::query($query);
            DEBUGG::log($e, 'isStampaTracciato', 1);

        }
    }

    public function getAllContentsByCorso()
    {
        if ($this->is_corso) {


            $reportObj = new gglmsModelReport();
            $all_contents = $reportObj->getContenutiArrayList($this->id);
//            $this->getSottoUnitaRic($this->id);
//
//
            return $all_contents;

        } else {
            return null;
        }
    }

    public function getAllAttestatiByCorso()
    {

        if ($this->is_corso) {
            $this->getSottoUnitaRic($this->id, 1);


            return $this->contenuti;
        } else {
            return null;
        }
    }

    public function setAsCorso($unit_id)
    {
        $this->is_corso = 1;
        $this->id = $unit_id;
    }

    public function is_visibile_today($unita)
    {

        if (!$unita->is_corso) {
            // unità non corso è sempre visibile
            return true;

        } else {
            // un corso è visibile se: 'filtro_date_corsi' è spento  OPPURE  'filtro_date_corsi' attivo  e passa il check delle date
//            return  ($this->_params->get('filtro_date_corsi') == 0) || ($this->_params->get('filtro_date_corsi') == 1 && ($unita->data_inizio <= date("Y-m-d") && $unita->data_fine >= date("Y-m-d")));
            return ($this->_params->get('filtro_date_corsi') == 0) || ($this->_params->get('filtro_date_corsi') == 1 && !$this->is_corso_expired($unita));

        }
    }

    public function is_corso_expired($unita)
    {
        if (!$unita->is_corso) {
            // unità non è mai expired
            return false;

        } else {

            return !($unita->data_inizio <= date("Y-m-d") && $unita->data_fine >= date("Y-m-d"));

        }
    }

    public function get_access_class()
    {

        $retval = '';
        if ($this->is_corso == 1 && !$this->isUnitacompleta($this->id)) {

            if ($this->is_corso_expired($this) || $this->check_coupon_is_expired($this)) {

                $retval = 'disabled';
            }
        }
        return $retval;
    }


    public function check_coupon_is_expired($corso)
    {


        $retval = null;
        $access_list = explode(",", $corso->accesso);
        if ($corso->accesso) {
            foreach ($access_list as $metodo) {
                switch ($metodo) {
                    case 'gruppo':
                        $coupon_model = new gglmsModelcoupon();

                        $retval = $coupon_model->is_coupon_expired_by_corso($corso);
                        break;
                    default:
                        //todo per ora se acesso non è gruppo ritorno tutti i coupon come validi
                        $retval = false;
//
                        break;
                }
            }
        }


        return $retval;
    }

}

