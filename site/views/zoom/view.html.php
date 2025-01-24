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
    protected $dt_today;
    protected $dt_past;
    protected $dt_range;
    protected $select_range_body;

    function display($tpl = null)
    {
        try {


            JHtml::_('stylesheet', 'components/com_gglms/libraries/css/bootstrap.min.css');
            JHtml::_('stylesheet', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
            JHtml::_('script', 'components/com_gglms/libraries/js/bootstrap.min.js');

            $_config = new gglmsModelConfig();
            $this->api_key = $_config->getConfigValue('zoom_api_key');
            $this->api_secret = $_config->getConfigValue('zoom_api_secret');
            $this->api_endpoint = $_config->getConfigValue('zoom_api_endpoint');
            $this->api_version = $_config->getConfigValue('zoom_api_version');
            $this->api_scadenza_token = $_config->getConfigValue('zoom_api_scadenza_token');

            $zoom_call = new gglmsControllerZoom($this->api_key, $this->api_secret, $this->api_endpoint, $this->api_version, $this->api_scadenza_token, true);
            $this->zoom_users = $zoom_call->get_users();
            $dt = new DateTime();
            $this->dt_today = $dt->format('Y-m');
            $this->dt_past = UtilityHelper::get_past_month_from_date($dt->format('Y-m-d'), 12, 'Y-m');
            $this->dt_range = UtilityHelper::get_months_range_from_to($this->dt_past . '-01', $this->dt_today . '-01');
            $this->select_range_body = OutputHelper::get_month_select_body($this->dt_range);

            parent::display($tpl);


        } catch (Exception $e){
            die("Access denied: " . $e->getMessage());
        }

    }


}
