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

require_once JPATH_COMPONENT . '/models/contenuto.php';

class gglmsViewContenuto extends JViewLegacy {

    protected $params;

    function display($tpl = null)
    {
        JFactory::getDocument()->addScript(JURI::root(true) . '/components/com_gglms/libraries/js/userlog.js');
        $this->contenuto = $this->get('Contenuto');

        $this->contenuto->setStato(); //  D A R I A B I L I T A R E -----------------------------------------------------------------------------------------------------------------------------

        $this->_params = $this->contenuto->_params; //faccio questa riattribuzione inutile in modo da uniformare il codice delle breadcrumb

        $user = JFactory::getUser();
        $this->id_utente = $user->get('id');

        switch ($this->contenuto->tipologia_contenuto){
            case 'videoslide':
                $this->jumper = $this->contenuto->getJumperXML();
                $this->contenuto->createVTT_slide($this->jumper);
                break;

            case 'solovideo':
                $this->jumper = [];
                break;
        }

        $this->breadcrumbs = outputHelper::buildContentBreadcrumb($this->contenuto->id);

        parent::display($this->contenuto->tipologia_contenuto);
    }
}
