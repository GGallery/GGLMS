<?php

/**
 * @version        1
 * @package        webtv
 * @author        antonio
 * @author mail    tony@bslt.it
 * @link
 * @copyright    Copyright (C) 2011 antonio - All rights reserved.
 * @license        GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

jimport('joomla.application.component.helper');

require_once JPATH_COMPONENT . '/controllers/generacoupon.php';


class gglmsViewAttestatiBulk extends JViewLegacy
{

    protected $params;
    public $lista_corsi;
    public $societa_venditrici;
    public $check_coupon_attestato;
    public $is_durata_standard;
    public $show_trial = 0;


    function display($tpl = null)
    {

        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/scaricaattestati.js');
//        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/scaricaattestati.css');

        $this->lista_corsi = utilityHelper::getIdCorsi();

        // lista aziende
        $coupon = new gglmsControllerGeneraCoupon();
        $this->lista_azienda = $coupon->get_lista_piva(false);


        // Display the view
        parent::display($tpl);

    }
}
