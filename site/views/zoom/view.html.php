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

require_once JPATH_COMPONENT . '/controllers/zoom.php';

class gglmsViewZoom extends JViewLegacy {

    protected $api_key;
    protected $api_secret;
    protected $api_endpoint;
    protected $api_version;
    protected $api_scadenza_token;
    protected $zoom_users;


    function display($tpl = null)
    {
        try {


            JHtml::_('stylesheet', '/components/com_gglms/libraries/css/bootstrap.min.css');
            JHtml::_('script', '/components/com_gglms/libraries/js/bootstrap.min.js');
            JHtml::_('script', 'https://kit.fontawesome.com/dee2e7c711.js');

            $_config = new gglmsModelConfig();
            $this->api_key = $_config->getConfigValue('zoom_api_key');
            $this->api_secret = $_config->getConfigValue('zoom_api_secret');
            $this->api_endpoint = $_config->getConfigValue('zoom_api_endpoint');
            $this->api_version = $_config->getConfigValue('zoom_api_version');
            $this->api_scadenza_token = $_config->getConfigValue('zoom_api_scadenza_token');

            $zoom_call = new gglmsControllerZoom($this->api_key, $this->api_secret, $this->api_endpoint, $this->api_version, $this->api_scadenza_token, true);
            $this->zoom_users = $zoom_call->get_users();


            parent::display($tpl);


        } catch (Exception $e){
            die("Access denied: " . $e->getMessage());
        }

    }


}
