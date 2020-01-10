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

require_once JPATH_COMPONENT . '/controllers/helpdesk.php';


class gglmsViewHelpdesk extends JViewLegacy
{

    protected $params;
    public $info_piattaforma;


    function display($tpl = null)
    {

        $helpDeskCtrl = new gglmsControllerHelpDesk();

        $this->info_piattaforma = $helpDeskCtrl->info_piattaforma;
        $this->request_recipients = $helpDeskCtrl->request_recipients;



        // Display the view
        parent::display($tpl);

    }
}
