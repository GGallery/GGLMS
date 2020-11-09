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

require_once JPATH_COMPONENT . '/controllers/generacoupon.php';


class gglmsViewAttestatiBulk extends JViewLegacy
{

    protected $params;
    public $lista_corsi;
    public $lista_azienda;
    public $societa_venditrici;
    public $check_coupon_attestato;
    public $is_durata_standard;
    public $show_trial = 0;


    function display($tpl = null)
    {

        $document = JFactory::getDocument();
        $document->addStyleSheet('https://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css', array('version' => 'auto'));
        JHtml::_('script', 'https://momentjs.com/downloads/moment.min.js', array('version' => 'auto', 'relative' => true));
        JHtml::_('script', 'https://code.jquery.com/ui/1.9.2/jquery-ui.min.js', array('version' => 'auto', 'relative' => true));

        JHtml::script(Juri::base() . 'components/com_gglms/libraries/js/scaricaattestati.js');

        // abilito il filtro azienda anche per i corsi
        $this->lista_corsi = utilityHelper::getIdCorsi(null, true);

        // lista aziende
        $coupon = new gglmsControllerGeneraCoupon();
        $this->lista_azienda = $coupon->get_lista_piva(false);


        // Display the view
        parent::display($tpl);

    }
}
