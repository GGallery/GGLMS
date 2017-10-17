<?php

/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;


jimport('joomla.database.table');

class gglmsTableContent extends JTable {

    function __construct(&$db) {
        parent::__construct('#__gg_contenuti', 'id', $db);
    }

    /**
     * Overloaded bind function
     *
     * @param	array		$hash named array
     * @return	null|string	null is operation was satisfactory, otherwise returns an error
     * @see JTable:bind
     * @since 1.5
     */


    public function bind($array, $ignore = '') {




        if (isset($array['categoria'])) {
            if(is_array($array['categoria']))
                $array['categoria'] = implode(',', $array['categoria']);

            gglmsHelper::SetMappaContenutoUnita($array);
        }
//
        if (isset($array['files'])) {
            if(is_array($array['files']))
                $array['files'] = implode(',', $array['files']);

            gglmsHelper::SetMappaContenutoFiles($array);
        }

        if (is_array($array['prerequisiti'])){
            $array['prerequisiti'] = implode(',',$array['prerequisiti']);
        }

        return parent::bind($array, $ignore);
    }


    /*
     * Verifico la durata del contenuto 
     */

    public function checkContentDuration($id) {

//        $path = "/var/www/vhosts/md-oncology.tv/httpdocs/home/mediatv/contenuti/$id/$id.flv";
//
//        $getID3 = new getID3();
//        $file = $getID3->analyze($path);

//        return (int) $file['playtime_seconds'];
    }

    /**
     * Overloaded check function
     *
     * @return	boolean
     * @see		JTable::check
     * @since	1.5
     */
    function check() {

        // Set name
        $this->titolo = htmlspecialchars_decode($this->titolo, ENT_QUOTES);
//
//        // Set alias
        $this->alias = $this->setAlias($this->alias);
        if (empty($this->alias)) {
            $this->alias = $this->setAlias($this->titolo." ".rand(100,999));
        }

        return true;
    }





    function setAlias($text) {


        $text = preg_replace('~[^\\pL\d]+~u', '_', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '_');

        return $text;
    }

}
