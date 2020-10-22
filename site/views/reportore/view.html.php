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


require_once JPATH_COMPONENT . '/models/unita.php';
require_once JPATH_COMPONENT . '/models/report.php';

class gglmsViewReportOre extends JViewLegacy {

    protected $params;

    function display($tpl = null)
    {
        $model = new gglmsModelReport();
        $this->state = $this->get('State');

        $this->usergroups = utilityHelper::getSocietaByUser();

        $this->corsi = $model->getCorsi(true);

        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/bootstrap.min.css');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/container-fluid.css');
        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/loader.css');

        parent::display($tpl);
    }




}
