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
//defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

jimport('joomla.application.component.helper');

require_once JPATH_COMPONENT . '/controllers/generacoupon.php';


class gglmsViewPrenota extends JViewLegacy
{

    protected $params;
    public $lista_corsi;
    public $societa_venditrici;
    public $check_coupon_attestato;
    public $is_durata_standard;
    public $show_trial = 0;


    function display($tpl = null)
    {
        // scripts per input text venditori  filtrato


        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/bootstrap.min.css');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/container-fluid.css');

        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/jquery-3.2.1.min.js');
        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/kendo/kendo.all.min.js');
        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/kendo/jszip.min.js');
        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/kendo/kendo.messages.it-IT.min.js');
        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/kendo/kendo.culture.it-IT.min.js');
        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/kendo/kendo.helper.js');




        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/kendo/kendo.common.min.css');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/kendo/kendo.bootstrap.min.css');


        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/loader.css');
        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/prenotaCoupon.js');
//        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/prenotacoupon.css');

        $couponCtrl = new gglmsControllerGeneraCoupon();
        $this->lista_corsi = $couponCtrl->generaCoupon->lista_corsi;
        $this->societa_venditrici = $couponCtrl->generaCoupon->societa_venditrici;





        // Display the view
        parent::display($tpl);
    }



}
