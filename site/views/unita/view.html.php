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

require_once JPATH_COMPONENT . '/models/unita.php';

class gglmsViewUnita extends JViewLegacy {

    protected $params;

    function display($tpl = null)
    {
  
        $this->unita       = $this->get('Unita');

        //faccio questa riattribuzione inutile in modo da uniformare il codice delle breadcrumb, lo so è una vaccata
        $this->_params = $this->unita->_params;
 
        if(!$this->unita->access()) {
            $app = JFactory::getApplication();
            JFactory::getApplication()->enqueueMessage('Non puoi accedere a questa unita', 'error');
            $url = htmlspecialchars($_SERVER['HTTP_REFERER']);
            $app->redirect($url);
        }



        $this->sottounita   = $this->unita->getSottoUnita();

        $this->contenuti    = $this->unita->getContenuti();

        $this->breadcrumbs = outputHelper::buildUnitBreadcrumb($this->unita->id);





        parent::display($tpl);
    }
}
    