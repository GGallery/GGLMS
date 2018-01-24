<?php

/**
 * @package		Joomla.Tutorials
 * @subpackage	Components
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

class gglmsHelper {

    public static function addSubmenu($submenu) {


//      LINK ICONE JOOMLA
//      https://docs.joomla.org/J3.x:Joomla_Standard_Icomoon_Fonts/it


        JHtmlSidebar::addEntry(
            '<i class="icon-cog"></i>' . JText::_('Configurazione'),
            'index.php?option=com_gglms&view=configs&extension=com_gglms',
            $submenu == 'configs'
        );

        JHtmlSidebar::addEntry(
            '<i class="icon-file-2"></i>' . JText::_('Contenuti'),
            'index.php?option=com_gglms',
            $submenu == 'contents'
        );

        JHtmlSidebar::addEntry(
            '<i class="icon-folder-open"></i>' . JText::_('Unita / Corsi'),
            'index.php?option=com_gglms&view=unitas',
            $submenu == 'unitas'
        );

        JHtmlSidebar::addEntry(
            '<i class="icon-attachment "></i>' . JText::_('Files / Allegati'),
            'index.php?option=com_gglms&view=files',
            $submenu == 'unitas'
        );

        JHtmlSidebar::addEntry(
            '<i class="icon-print  "></i>' . JText::_('Genera coupon'),
            'index.php?option=com_gglms&view=generacoupon',
            $submenu == 'generacoupon'
        );

        JHtmlSidebar::addEntry(
            '<i class="icon-users  "></i>' . JText::_('Utenti'),
            'index.php?option=com_gglms&view=users',
            $submenu == 'generacoupon'
        );


        $document = JFactory::getDocument();

        if ($submenu == 'configs') {
            $document->setTitle("Configurazione GGLMS");
        }

        if ($submenu == 'contents') {
            $document->setTitle("Contenuti");
        }

        if ($submenu == 'unitas') {
            $document->setTitle("Unita");
        }

        if ($submenu == 'generacoupon') {
            $document->setTitle("generacoupon");
        }

        if ($submenu == 'users') {
            $document->setTitle("Utenti");
        }




    }


    public static function setAlias($text){


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

    public static function SetMappaContenutoUnita($item){


        $db = JFactory::getDBO();

        $contentid=$item['id'];
        $categorie = explode("," , $item['categoria']);

        if(!$contentid)
            return false;

//        $query = "DELETE FROM #__gg_unit_map WHERE idcontenuto= $contentid";
//        $db->setQuery((string) $query);
//        $db->execute();

        foreach ($categorie as $value) {
            $query = "INSERT INTO #__gg_unit_map (idcontenuto, idunita) values ($contentid,$value) ON DUPLICATE KEY UPDATE idunita=".$value;
            $db->setQuery((string) $query);
            $db->execute();
        }

        return true;
    }



    public static function GetMappaContenutoUnita($item){

        $res= array();
        if(!$item->id)
            return $res;

        try{
            $db = JFactory::getDBO();

            $query = $db->getQuery(true);
            $query->select('idunita');
            $query->from('#__gg_unit_map');
            $query->where('idcontenuto=' . $item->id);

            $db->setQuery((string) $query);
            $res = $db->loadColumn();

            return $res;
        }
        catch(Exception $e)
        {
            print_r($e);
        }


    }

    public static function SetMappaContenutoFiles($item){

        try{
            $db = JFactory::getDBO();

            $contentid=$item['id'];
            $files = explode(",",$item['files']);

            $query_del = "DELETE FROM #__gg_files_map WHERE idcontenuto= $contentid";
            $db->setQuery((string) $query_del);
            $db->execute();

            foreach ($files as $value) {
                $query = "INSERT IGNORE INTO #__gg_files_map (idcontenuto, idfile ) values ($contentid,$value)";

                $db->setQuery((string) $query);
                $res = $db->execute();

            }
        }catch (Exception $e){
            echo "<pre>";
            print_r($e);
            echo "</pre>";
            die("SetMappaContenutoFile");
        }

        return $res;
    }

    public static function GetMappaContenutoFiles($item){
        $db = JFactory::getDBO();

        $res = array();
        if(!$item->id)
            return $res;

        try{
            $query = $db->getQuery(true);
            $query->select('idfile');
            $query->from('#__gg_files_map');
            $query->where('idcontenuto=' . $item->id);

            $db->setQuery((string) $query);
            $res = $db->loadColumn();

        }
        catch(Exception $e)
        {
            print_r($e);
            die("Errore getMappaContenutofile");
        }
        return $res;
    }

    public static function GetMappaContenutoAcl($item){
        FB::info($item, "->GetMappaContenutoAcl");
        $db = JFactory::getDBO();

        $res = array();
        if(!$item->id)
            return $res;

        try{
            $query = $db->getQuery(true);
            $query->select('id_group');
            $query->from('#__gg_contenuti_acl');
            $query->where('id_contenuto=' . $item->id);

            $db->setQuery((string) $query);
            $res = $db->loadColumn();

            FB::log((string)$query, "GetMappaContenutoAcl");

        }
        catch(Exception $e)
        {
            FB::error($e);
        }
        return $res;
    }

    public static function SetMappaContenutoAcl($item){
        FB::info($item."->SetMappaContenutoAcl");
        $db = JFactory::getDBO();

        $contentid=$item['id'];
        $acl = explode(",",$item['acl']);

        $query = "DELETE FROM #__gg_contenuti_acl WHERE id_contenuto= $contentid";
        $db->setQuery((string) $query);
        $res = $db->loadResult();


        foreach ($acl as $value) {
            $query = "INSERT IGNORE INTO #__gg_contenuti_acl (id_contenuto, id_group ) values ($contentid,$value)";

            $db->setQuery((string) $query);
            $res = $db->loadResult();
        }
        return $res;
    }


    public static function SetMappaContenutoParams($item){
        FB::info($item."->SetmappaContenutoParams");
        $db = JFactory::getDBO();

        $contentid=$item['id'];
        $params = explode(",",$item['parametri']);

        $query = "DELETE FROM #__gg_param_map WHERE idcontenuto= $contentid";
        $db->setQuery((string) $query);
        $res = $db->loadResult();


        foreach ($params as $value) {
            $query = "INSERT IGNORE INTO #__gg_param_map (idcontenuto, idparametro ) values ($contentid,$value)";

            $db->setQuery((string) $query);
            $res = $db->loadResult();
        }
        return $res;
    }


    public static function GetMappaContenutoParams($item){
        FB::info($item, "->GetMappaContenutoParams");
        $db = JFactory::getDBO();

        $res = array();
        if(!$item->id)
            return $res;

        try{
            $query = $db->getQuery(true);
            $query->select('idparametro');
            $query->from('#__gg_param_map');
            $query->where('idcontenuto=' . $item->id);

            $db->setQuery((string) $query);
            $res = $db->loadColumn();

            FB::log($res, "GetMappaContenutoParams");

        }
        catch(Exception $e)
        {
            FB::error($e);
        }
        return $res;
    }

    public static function getUserGroupName($user_id, $return_text = false){


        $db     = JFactory::getDBO();
        $groups = JAccess::getGroupsByUser($user_id);
        $groupid_list      = '(' . implode(',', $groups) . ')';
        $query  = $db->getQuery(true);
        $query->select('title');
        $query->from('#__usergroups');
        $query->where('id IN ' .$groupid_list);
        $db->setQuery($query);
        $rows   = $db->loadColumn();

        if($return_text){
            return implode(', <br>',$rows);
        }
        else
            return $rows;

    }


    public static function SetMappaAccessoGruppi($item){

        try{
            $db = JFactory::getDBO();

            $unitid=$item['id'];
            $files = explode(",",$item['id_gruppi_abilitati']);

            $query_del = "DELETE FROM #__gg_usergroup_map WHERE idunita = $unitid";
            $db->setQuery((string) $query_del);
            $db->execute();

            foreach ($files as $value) {
                $query = "INSERT IGNORE INTO #__gg_usergroup_map (idunita, idgruppo) values ($unitid,$value)";

                $db->setQuery((string) $query);
                $res = $db->execute();

            }
        }catch (Exception $e){
            echo "<pre>";
            print_r($e);
            echo "</pre>";
            die("SetMappaAccessoGruppi");
        }

        return $res;
    }

    public static function GetMappaAccessoGruppi($item){
        $db = JFactory::getDBO();

        $res = array();
        if(!$item->id)
            return $res;

        try{
            $query = $db->getQuery(true);
            $query->select('idgruppo');
            $query->from('#__gg_usergroup_map');
            $query->where('idunita=' . $item->id);

            $db->setQuery((string) $query);
            $res = $db->loadColumn();

        }
        catch(Exception $e)
        {
            print_r($e);
            die("Errore GetMappaAccessoGruppi");
        }
        return $res;
    }




}