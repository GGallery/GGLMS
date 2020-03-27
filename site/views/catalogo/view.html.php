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

require_once JPATH_COMPONENT . '/models/catalogo.php';

class gglmsViewCatalogo extends JViewLegacy
{

    protected $params;

    function display($tpl = null)
    {


        $box = JRequest::getVar('box');

        $this->id_piattaforma = JRequest::getVar('piattaforma_id');
        $this->catalogoModel = new gglmsModelCatalogo();
        $layout = JRequest::getWord('template', '');


        if ($layout === 'prenota') {
            $this->setLayout("catalogo_prenota");
            $this->catalogo = $this->catalogoModel->getCatalogo_prenota( $this->id_piattaforma );
        }
        else{
            // template per piattaforme che non hanno ecommerce
            $this->catalogo = $this->catalogoModel->getCatalogo(DOMINIO, $box);
        }





        parent::display($tpl);
    }
}
