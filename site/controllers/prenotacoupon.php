<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

//require_once JPATH_COMPONENT . '/models/contenuto.php';
require_once JPATH_COMPONENT . '/models/generacoupon.php';

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerPrenotaCoupon extends JControllerLegacy
{

    private $_user;
    private $_japp;
    public $_params;
    public $_db;
    public $generaCoupon;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();

        $this->_user = JFactory::getUser();
        $this->_db = &JFactory::getDbo();


        $this->generaCoupon = new gglmsModelGeneraCoupon();
        $this->lista_corsi = $this->generaCoupon->lista_corsi;
        $this->societa_venditrici = $this->generaCoupon->societa_venditrici;

    }

    public function _getPrezziByCorso($id_corso)
    {

        $query = $this->_db->getQuery(true)
            ->select('p.*,r.*,u.titolo')
            ->from('#__gg_prezzi as p ')
            ->join('inner', '#__gg_prezzi_range as r on r.id_corso = p.id_corso')
            ->join('inner', '#__gg_unit as u on u.id = p.id_corso')
            ->where('p.id_corso = "' . $id_corso . '"') ;


        $this->_db->setQuery($query);
        $prezzi = $this->_db->loadObjectList();


        return $prezzi;
    }





}
