<?php

/**
 * @version		1
 * @package		webtv
 * @author 		antonio
 * @author mail	tony@bslt.it
 * @link
 * @copyright	Copyright (C) 2011 antonio - All rights reserved.
 * @license		GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

jimport('joomla.application.component.helper');


class gglmsViewrinnovacoupon extends JViewLegacy {

    protected $params;
    public $visualizza_durata_rinnovo_coupon = 0;

    function display($tpl = null)
    {

        // leggo parametro visualizza_durata_rinnovo_coupon
        // se 1 visualizzo il campo se 0 lo nascondo
        $this->visualizza_durata_rinnovo_coupon = utilityHelper::get_display_from_configuration($this->visualizza_durata_rinnovo_coupon, 'visualizza_durata_rinnovo_coupon');

        $this->coupon = new gglmsModelcoupon();

        $this->_params = $this->coupon->_params;


        parent::display($tpl);
    }
}

