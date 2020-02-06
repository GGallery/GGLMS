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

require_once JPATH_COMPONENT . '/models/users.php';
require_once JPATH_COMPONENT . '/models/users.php';


class gglmsViewLoginAs extends JViewLegacy
{

    protected $params;
    public $users;
    public $model;


    function display($tpl = null)
    {

        $this->model =  new gglmsModelUsers();

//        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/bootstrap.min.css');
//        JHtml::_('stylesheet', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-bootgrid/1.3.1/jquery.bootgrid.min.css');
//        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/container-fluid.css');
//
//
//        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/monitoraCoupon.js');
//        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/monitoraCoupon.css');
//
//
//        JFactory::getDocument()->addScript(JURI::root(true) . '/components/com_gglms/libraries/js/jquery.bootgrid.min.js');


        $this->users = $this->model->get_all_users();


        // Display the view
        parent::display($tpl);

    }
}
