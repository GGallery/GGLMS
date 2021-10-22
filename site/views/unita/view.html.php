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
    protected $url_base;

    function display($tpl = null)
    {

        $this->unita = $this->get('Unita');

        //faccio questa riattribuzione inutile in modo da uniformare il codice delle breadcrumb, lo so è una vaccata
        $this->_params = $this->unita->_params;

        JHtml::_('stylesheet', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/css/bootstrap-grid.min.css');
        JHtml::_('script', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.min.js');
        JHtml::_('stylesheet', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/css/bootstrap-reboot.min.css');
        JHtml::_('stylesheet', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/css/bootstrap.min.css');
        JHtml::_('script', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.bundle.min.js');

        $arr_url = parse_url(JURI::base());
        $this->url_base = $arr_url['scheme'] . '://' . $arr_url['host'];

        if (!$this->unita->access()) {
            $app = JFactory::getApplication();
            JFactory::getApplication()->enqueueMessage('Non puoi ancora accedere a questo corso', 'warning');

            $url = ($this->_params->get('url_redirect_on_access_deny'))
                ? $this->_params->get('url_redirect_on_access_deny')
                : htmlspecialchars($_SERVER['HTTP_REFERER']);


            $app->redirect($url);
        }


        $this->sottounita = $this->unita->getSottoUnita(null, null, 'DESC');
//        DEBUGG::log($e, 'getSottoUnita');

        $this->contenuti = $this->unita->getContenuti_u($this->unita->id, null);

        $this->breadcrumbs = outputHelper::buildUnitBreadcrumb($this->unita->id);


        parent::display($tpl);
    }
}
