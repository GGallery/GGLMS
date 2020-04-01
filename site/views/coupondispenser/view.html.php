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


class gglmsViewCoupondispenser extends JViewLegacy
{

    protected $params;

    function display($tpl = null)
    {

        $this->dispenser = $this->get('Dispenser');



        parent::display($tpl);
    }
}
