<?php

/**
 * WebTVContenuto Model
 *
 * @package    Joomla.Components
 * @subpackage WebTV
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once JPATH_COMPONENT . '/models/stato.php';


/**
 * WebTVContenuto Model
 *
 * @package    Joomla.Components
 * @subpackage WebTV
 */
class gglmsModelCoupon extends JModelLegacy
{

	protected $_db;
	private $_user_id;


	public function __construct($config = array())
	{
		parent::__construct($config);

		$user = JFactory::getUser();
		$this->_user_id = $user->get('id');

		$this->_db = $this->getDbo();

	}

	public function __destruct()
	{

	}


	public function access($unita)
	{


		$access_list = explode(",",$unita->accesso);
		$model_coupon = new gglmsModelCoupon();

		if($unita->accesso) {
			foreach ($access_list as $metodo => $stato) {
				switch ($metodo) {
					case 'coupon':
						return $model_coupon->check_Standard_Coupon($unita->id);
						break;

					case 'couponeb':
						return $model_coupon->check_EventBookingField_Coupon($unita->id);
						break;

					case 'iscrizioneeb':
						return $model_coupon->check_iscrizione_eb($unita->id_event_booking);
						break;
				}
			}
		}
		return true;


	}


	public function check_Standard_Coupon($unit_id)
	{
		try {
			$query = $this->_db->getQuery(true)
				->select('count(coupon)')
				->from('#__gg_coupon as u')
				->where("u.id_utente = $this->_user_id")
				->where("u.corsi_abilitati = $unit_id")
				->where("(data_scadenza > current_date() OR data_scadenza IS NULL)")
				->where("if(durata is not null, DATEDIFF(DATE_ADD(data_utilizzo, INTERVAL durata DAY), current_date()) > 0, true)");


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


	public function check_EventBookingField_Coupon($unit_id)
	{
		try {
			$query = $this->_db->getQuery(true)
				->select('count(coupon)')
				->from('#__gg_coupon as c')
				->join('inner', '#__eb_field_values as v on c.coupon = v.field_value')
				->join('inner', '#__eb_registrants as r on v.registrant_id = r.id')
				->where('v.field_id = 33')//parametrizzare con campo EB
				->where('r.user_id= ' . $this->_user_id)
				->where('abilitato = 1')
//				->where("u.id_utente = $this->_user_id")
//				->where("u.corsi_abilitati = $unit_id")
//				->where("(data_scadenza > current_date() OR data_scadenza IS NULL)")
//				->where("if(durata is not null, DATEDIFF(DATE_ADD(data_utilizzo, INTERVAL durata DAY), current_date()) > 0, true)");
//				->setLimit(1)
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

	public function check_iscrizione_eb($id_event_booking)
	{

		try {
			$query = $this->_db->getQuery(true)
				->select('count(id)')
				->from('#__eb_registrants as r')
				->where('r.event_id = '. $id_event_booking)//parametrizzare con campo EB
				->where('r.user_id= ' . $this->_user_id)
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




}

