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
    //public $request_recipients;
    public $richiesta_privacy;
    public $richiesta_privacy_link;
    public $href;


    function display($tpl = null)
    {

        $helpDeskCtrl = new gglmsControllerHelpDesk();

        $this->info_piattaforma = $helpDeskCtrl->info_piattaforma;
        //$this->request_recipients = $helpDeskCtrl->request_recipients;

        $_config = new gglmsModelConfig();
        $this->richiesta_privacy = utilityHelper::get_display_from_configuration($this->richiesta_privacy, 'richiesta_privacy');
        $this->richiesta_privacy_link = $_config->getConfigValue('richiesta_privacy_link');
        $this->richiesta_privacy_link = ($this->richiesta_privacy_link != "") ? $this->richiesta_privacy_link : "#";
        if ($this->richiesta_privacy_link != "#")
            $this->href = <<<HTML
                target="_blank"
HTML;

        $_privacy_1 = JText::_('COM_GGLMS_HELP_DESK_PRIVACY_1');
        $_privacy_2 = JText::_('COM_GGLMS_HELP_DESK_PRIVACY_2');
        $_privacy_3 = JText::_('COM_GGLMS_HELP_DESK_PRIVACY_3');

        $this->richiesta_privacy_link = <<<HTML
            {$_privacy_1} <a href="{$this->richiesta_privacy_link}" {$this->href}>{$_privacy_2}</a> {$_privacy_3}
HTML;

        // Display the view
        parent::display($tpl);

    }
}
