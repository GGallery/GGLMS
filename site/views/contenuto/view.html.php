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

require_once JPATH_COMPONENT . '/models/contenuto.php';

class gglmsViewContenuto extends JViewLegacy
{

    protected $_params;
    protected $url_base;
    protected $slide_pdf;
    protected $id_utente;
    protected $jumper;
    protected $att_scaricabile;
    protected $id_unita;


    function display($tpl = null)
    {

        $app = JFactory::getApplication();
        $template = $app->getTemplate();


        if($template == 'tz_meetup'){

            JHtml::_('stylesheet', 'components/com_gglms/libraries/css/fix_tz_meetup.css');
        }

        JFactory::getDocument()->addScript(JURI::root(true) . '/components/com_gglms/libraries/js/userlog.js');
        $this->contenuto = $this->get('Contenuto');

        $this->contenuto->setStato(); //  D A R I A B I L I T A R E -----------------------------------------------------------------------------------------------------------------------------

        $this->_params = $this->contenuto->_params; //faccio questa riattribuzione inutile in modo da uniformare il codice delle breadcrumb

        $user = JFactory::getUser();
        $this->id_utente = $user->get('id');

        $arr_url = parse_url(JURI::base());
        $this->slide_pdf = null;
        $this->url_base = $arr_url['scheme'] . '://' . $arr_url['host'];

        switch ($this->contenuto->tipologia_contenuto) {
            case 'videoslide':
                $this->jumper = $this->contenuto->getJumperXML();
                $this->contenuto->createVTT_slide($this->jumper);
                // scaricamento delle slide in formato pdf se file presente
                $_slide_pdf = null;
                $c_path = '/mediagg/contenuti/' . $this->contenuto->id . '/slide.pdf';
                $c_file = $_SERVER['DOCUMENT_ROOT'] . $c_path;
                // carico l'immagine da indirizzo assoluto
                if (file_exists($c_file)) {
                    $this->slide_pdf = $this->url_base . $c_path;
                }
                break;

            case 'solovideo':
                $this->jumper = array();
                break;
            case 'attestato':
                $this->att_scaricabile = $this->contenuto->attestato_scaricabile_by_user();
                $this->id_unita = UtilityHelper::get_unita_padre_corso_da_contenuto($this->contenuto->id);
                break;
        }

        $this->breadcrumbs = outputHelper::buildContentBreadcrumb($this->contenuto->id);

        parent::display($this->contenuto->tipologia_contenuto);
    }
}
