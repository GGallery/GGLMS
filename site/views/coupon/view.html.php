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


class gglmsViewCoupon extends JViewLegacy {

    protected $params;
    protected string $currentUrl;
    protected bool $hideCouponGeneration;
    protected string $newPlatformUrl;

    function display($tpl = null)
    {


        $this->coupon = new gglmsModelcoupon();

        $this->_params = $this->coupon->_params;

        // current URL
        $blackListedAddresses = [
            'csifad.confindustriacuneo.it', 
            'fad.assoservizilegnano.it', 
            'serviziconfindustriavarese.ausindfad.it',
            //'test.gallerygroup.dvl.to',
        ];
        $urlPairs = [
            'web.ausindfad.it' => 'https://training.ausindfad.it',
            'web.assolombardaservizifad.it' => 'https://web2.assolombardaservizifad.it',
            'csifad.confindustriacuneo.it' => 'https://csi-elearning.confindustriacuneo.it',
            'fad.assoservizilegnano.it' => 'https://elearning.assoservizilegnano.it',
            'serviziconfindustriavarese.ausindfad.it' => 'https://scv.ausindfad.it',
            //'test.gallerygroup.dvl.to' => 'https://nasa.gov',
        ];
        $this->currentUrl = $_SERVER['HTTP_HOST'];
        // verifico se l'indirizzo corrente è blacklisted
        $this->hideCouponGeneration = in_array(
            $this->currentUrl,
            array_map('strtolower', $blackListedAddresses),
            true
        );

        if ($this->hideCouponGeneration) {
            $this->newPlatformUrl = $urlPairs[$this->currentUrl];
        }

        parent::display($tpl);
    }
}
    