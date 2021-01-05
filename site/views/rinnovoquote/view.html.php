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

require_once JPATH_COMPONENT . '/controllers/users.php';

class gglmsViewRinnovoQuote extends JViewLegacy {

    protected $params;

    function display($tpl = null)
    {
        try {


            JHtml::_('stylesheet', '/components/com_gglms/libraries/css/bootstrap.min.css');
            JHtml::_('stylesheet','https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css');

            // campi encoded dalla chiamata
            $pp = JRequest::getVar('pp');
            $_decripted = UtilityHelper::encrypt_decrypt('decrypt', $pp, 'GGallery00!', 'GGallery00!');
            if (strpos($_decripted, "|==|") == false)
                die("Forbidden");

            $_arr_decr = explode("|==|", $_decripted);
            $_username = $_arr_decr[0];
            $_password = UtilityHelper::encrypt_decrypt('decrypt', $_arr_decr[1], 'GGallery00!', 'GGallery00!');
            $_ultimo_anno_pagato = $_arr_decr[2];

            // controllo esistenza utente
            $_user = new gglmsControllerUsers();
            $_check_user = $_user->check_user($_username, $_password);

            if (!is_array($_check_user))
                throw new Exception($_check_user, 1);

            $_user_id = $_check_user['success'];

            parent::display($tpl);


        } catch (Exception $e){
            die("Access denied: " . $e->getMessage());
        }

    }


}
