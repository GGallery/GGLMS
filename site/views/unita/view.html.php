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

jimport('joomla.application.pathway');


require_once JPATH_COMPONENT . '/models/unita.php';

class gglmsViewUnita extends JViewLegacy
{

    protected $params;
    protected $url_base;
    protected $box_corsi;
    protected $_html;
    protected $model_unita;
    protected $box_id = null;
    protected $box_title = '';

    function display($tpl = null)
    {

        $app = JFactory::getApplication();   // equivalent of $app = JFactory::getApplication();
        $model_catalogo = new gglmsModelCatalogo();
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

        // verifica dalla vista se è abilitata la vista per box e non per unit
        $box_unita = (null !== $app->input->get('box_unita') && $app->input->get('box_unita') == 1) ? true : false;
        $layout = ($box_unita) ? 'box_unita' : 'default';
        $this->setLayout($layout);

        // richiesta di visualizzazione per un determinato box_id
        $this->box_id = (null !== $app->input->get('box_id')) ? $app->input->get('box_id') : null;

        if (!$box_unita) {

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
        }
        else if (!is_null($this->box_id)) {

            require_once JPATH_COMPONENT . '/models/helpdesk.php';

            $model_helpdesk = new gglmsModelHelpDesk();

            $helpdesk_info = $model_helpdesk->getPiattaformaHelpDeskInfo();
            $this->dominio = $helpdesk_info->dominio;
            $this->box_corsi = $model_catalogo->get_box_categorie_corso($this->box_id, $this->dominio);
            $this->model_unita = new gglmsModelUnita();
            $this->box_title = isset($this->box_corsi[0]->description) ? $this->box_corsi[0]->description : null;

            $pathway = $app->getPathway();
            $pathway->addItem( $this->box_title, null);
            $this->breadcrumbs = $pathway->setPathway(array());

            $this->setLayout('lista_unita');

        }
        else {

            $this->box_corsi = $model_catalogo->get_box_categorie_corso(null, null, true);
            $this->_html = outputHelper::get_box_details($this->box_corsi);

        }

        parent::display($tpl);
    }
}
