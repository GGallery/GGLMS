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

require_once JPATH_COMPONENT . '/controllers/generacoupon.php';


class gglmsViewAnnullaQuiz extends JViewLegacy {

    protected $params;

    function display($tpl = null)
    {

        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/bootstrap.min.css');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/container-fluid.css');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/loader.css');

        //JHtml::_('script', 'https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js', array('version' => 'auto', 'relative' => true));

        // abilito il filtro azienda anche per i corsi
        $this->lista_corsi = utilityHelper::getIdCorsi(null, true);

        // lista aziende
        $coupon = new gglmsControllerGeneraCoupon();
        $this->usergroups = utilityHelper::getSocietaByUser();

        //print_r($this->usergroups); die();
        //print_r($this->lista_corsi); die();
        parent::display($tpl);
    }




}
