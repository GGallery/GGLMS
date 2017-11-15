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
//require_once JPATH_COMPONENT . '/models/unita.php';

/**
 * WebTVContenuto Model
 *
 * @package    Joomla.Components
 * @subpackage WebTV
 */
class gglmsModelUnita extends JModelLegacy {

	private $_dbg;
	private $_app;
	private $_userid;
	public $_params;
	protected  $_db;


	public function __construct($config = array()) {
		parent::__construct($config);

		$user = JFactory::getUser();
		$this->_userid = $user->get('id');

		$this->_db = $this->getDbo();

		$this->_app = JFactory::getApplication('site');
		$this->_params = $this->_app->getParams();

	}

	public function __destruct() {

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
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('unita.id');
		try
		{
			$query = $this->_db->getQuery(true)
				->select('*')
				->from('#__gg_unit as u')
				->where('u.id = ' . (int) $pk)
				->where('u.pubblicato = 1');


			$this->_db->setQuery($query);
			$unit = $this->_db->loadObject('gglmsModelUnita');

			if (empty($unit))
			{
				return JError::raiseError(404, JText::_('Unita non disponibile -->').(string)$query);
			}
		}
		catch (Exception $e)
		{
			DEBUGG::log($e, 'getUnita');
		}

		return $unit;
	}

	
	public function getSottoUnita($pk = null)
	{
		if($pk)
			$this->id = $pk;

		try
		{
			$query = $this->_db->getQuery(true)
				->select('*')
				->from('#__gg_unit as u')
				->where('u.unitapadre  = ' . $this->id)
				->where('u.pubblicato = 1')
				->order('ordinamento')
			;
			$this->_db->setQuery($query);
			$data = $this->_db->loadObjectList('', 'gglmsModelUnita');

		}
		catch (Exception $e)
		{
			DEBUGG::log($e, 'getSottoUnita');
		}

//	
		return $data;
	}

	public function getAllContenuti()
	{
		$sottunita = $this->getSottoUnita();

		$contenuti= array();
		foreach ($sottunita as $unitafiglio){
			$sottocontenuti = $unitafiglio->getContenuti();
			$contenuti=array_merge($contenuti, $sottocontenuti);
		}

//		DEBUGG::log($contenuti, 'contenuti', 1);

		return $contenuti;
	}

	public function getContenuti()
	{
		try
		{
			$query = $this->_db->getQuery(true)
				->select('c.*')
				->from('#__gg_unit_map as m')
				->where('m.idunita = ' . $this->id)
				->innerJoin('#__gg_contenuti as c on c.id = m.idcontenuto')
				->where('c.pubblicato = 1')
				->order('m.ordinamento');

//			echo (string)$query;
//			die();


			$this->_db->setQuery($query);
			$contenuti = $this->_db->loadObjectList('', 'gglmsModelContenuto');

		}
		catch (Exception $e)
		{
			DEBUGG::log($e, 'getContenuti');

		}
		return $contenuti;
	}


	public function access(){

		if($this->is_corso || $this->id == 1)
			return $this->access_tipology($this);
		else
			return $this->access_tipology($this->find_corso($this->unitapadre));

	}

	public function find_corso($check){
		try
		{
			$query = $this->_db->getQuery(true)
				->select('*')
				->from('#__gg_unit as u')
				->where('u.id  = ' . $check)
			;
			$this->_db->setQuery($query);
			$data = $this->_db->loadObject();

			if($data->id == 1 && !$data->is_corso) {
				$this->_app->enqueueMessage('L\'Unita alla quale hai tentato di accedere e nessuna di quelle dei livelli superiori sono impostate come UNITA-CORSO. Finchè non sarà definito il corso padre non potrai accedere a questa unità', 'Error');
				$this->_app->redirect('index.php');

			}
			return ($data->is_corso) ? $data : $this->find_corso($data->unitapadre);

		}
		catch (Exception $e)
		{
			$this->setError($e);
		}
	}

	public function access_tipology($corso)
	{
		$access_list = explode(",",$corso->accesso);

		if($corso->accesso) {
			foreach ($access_list as $metodo ) {
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
            {
                $message = "Inserire il coupon per accedere a questa unita";
                $url = JRoute::_('index.php?option=com_gglms&view=coupon');
                $this->_app->redirect($url, $message);
            }
			else
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
				->where('v.field_id = '. $this->_params->get('campo_event_booking_auto_abilitazione_coupon'))
				->where('r.user_id= ' . $this->_userid)
				->where('r.published = 1 ')
				->where('r.event_id = ' . $corso->id_event_booking)
				->where('abilitato = 1')
				->where('FIND_IN_SET('.$corso->id .', corsi_abilitati)')
			;

			$this->_db->setQuery($query);
			$data = $this->_db->loadResult();

			if ($data == 0)
				return false;
			else
				return true;
		}
		catch (Exception $e)
		{
			$this->setError($e);
		}
	}

	private function check_iscrizione_eb($corso)
	{

		try {
			$query = $this->_db->getQuery(true)
				->select('count(id)')
				->from('#__eb_registrants as r')
				->where('r.event_id = '. $corso->id_event_booking)//parametrizzare con campo EB
				->where('r.user_id= ' . $this->_userid)
				->where('r.published = 1 ')
			;

			$this->_db->setQuery($query);
			$data = $this->_db->loadResult();


			if ($data == 0)
				return false;
			else
				return true;
		}
		catch (Exception $e)
		{
			DEBUGG::query($query);
			DEBUGG::log($e, 'check_iscrizione_eb', 1);

		}
	}


    private function check_iscrizione_gruppo($corso)
    {

        try {
            $query = $this->_db->getQuery(true)
                ->select('count(idunita)')
                ->from('#__gg_usergroup_map AS ug')
                ->join('inner','#__user_usergroup_map AS uj ON uj.group_id = ug.idgruppo')
                ->where('ug.idunita = '. $corso->id)//parametrizzare con campo EB
                ->where('uj.user_id= ' . $this->_userid)
            ;

            $this->_db->setQuery($query);
            $data = $this->_db->loadResult();

            if ($data == 0)
                return false;
            else
                return true;
        }
        catch (Exception $e)
        {
            DEBUGG::query($query);
            DEBUGG::log($e, 'check_iscrizione_gruppo', 1);

        }
    }


}

