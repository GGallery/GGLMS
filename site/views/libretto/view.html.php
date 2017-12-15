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




class gglmsViewLibretto extends JViewLegacy {

    protected $params;

    function display($tpl = null)
    {
        try {


            JHtml::_('stylesheet', '/components/com_gglms/libraries/css/bootstrap.min.css');
            JHtml::_('stylesheet','https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css');
            JHtml::_('stylesheet', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-bootgrid/1.3.1/jquery.bootgrid.min.css');


            JFactory::getDocument()->addScript(JURI::root(true) . '/components/com_gglms/libraries/js/jquery.bootgrid.fa.min.js');
            JFactory::getDocument()->addScript(JURI::root(true) . '/components/com_gglms/libraries/js/jquery.bootgrid.min.js');


            parent::display($tpl);


        }catch (exceptions $e){
            //var_dump($e);
           // die;
        }

    }


}
    