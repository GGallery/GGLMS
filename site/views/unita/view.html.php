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

require_once JPATH_COMPONENT . '/models/unita.php';

class gglmsViewUnita extends JViewLegacy
{

    protected $params;

    function display($tpl = null)
    {

        $this->unita = $this->get('Unita');

        //faccio questa riattribuzione inutile in modo da uniformare il codice delle breadcrumb, lo so è una vaccata
        $this->_params = $this->unita->_params;

        if (!$this->unita->access()) {
            $app = JFactory::getApplication();
            JFactory::getApplication()->enqueueMessage('Non puoi ancora accedere a questo corso', 'warning');

            $url = ($this->_params->get('url_redirect_on_access_deny'))
                ? $this->_params->get('url_redirect_on_access_deny')
                : htmlspecialchars($_SERVER['HTTP_REFERER']);


            $app->redirect($url);
        }


        $this->sottounita = $this->unita->getSottoUnita();
//        DEBUGG::log($e, 'getSottoUnita');

        $this->contenuti = $this->unita->getContenuti_u($this->unita->id, null);

        $this->breadcrumbs = outputHelper::buildUnitBreadcrumb($this->unita->id);


        parent::display($tpl);
    }
}
