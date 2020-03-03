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


class gglmsViewMonitora extends JViewLegacy
{

    protected $params;
    public $societa;
    public $lista_corsi;


    function display($tpl = null)
    {


        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/bootstrap.min.css');
        JHtml::_('stylesheet', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-bootgrid/1.3.1/jquery.bootgrid.min.css');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/container-fluid.css');


        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/monitoraCoupon.js');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/monitoraCoupon.css');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/loader.css');

        JFactory::getDocument()->addScript(JURI::root(true) . '/components/com_gglms/libraries/js/jquery.bootgrid.min.js');


        $this->societa = utilityHelper::getSocietaByUser();
        $this->lista_corsi = utilityHelper::getGruppiCorsi();


        // Display the view
        parent::display($tpl);

    }
}
