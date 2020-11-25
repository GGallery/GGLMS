<?php

/**
 * @package        Joomla.Tutorials
 * @subpackage    Components
 * @copyright    Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license        License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

class gglmsHelper
{

    public static function addSubmenu($submenu)
    {


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

//        JHtmlSidebar::addEntry(
//            '<i class="icon-print  "></i>' . JText::_('Genera coupon'),
//            'index.php?option=com_gglms&view=generacoupon',
//            $submenu == 'generacoupon'
//        );

        JHtmlSidebar::addEntry(
            '<i class="icon-users  "></i>' . JText::_('Utenti'),
            'index.php?option=com_gglms&view=users',
            $submenu == 'generacoupon'
        );

        JHtmlSidebar::addEntry(
            '<i class="icon-users  "></i>' . JText::_('Iscrizioni'),
            'index.php?option=com_gglms&view=iscrizioni',
            $submenu == 'iscrizioni'
        );

        JHtmlSidebar::addEntry(
            '<i class="icon-users  "></i>' . JText::_('Coupon Dispenser'),
            'index.php?option=com_gglms&view=coupondispensers',
            $submenu == 'iscrizioni'
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

//        if ($submenu == 'generacoupon') {
//            $document->setTitle("generacoupon");
//        }

        if ($submenu == 'users') {
            $document->setTitle("Utenti");
        }

        if ($submenu == 'iscrizioni') {
            $document->setTitle("Iscrizioni");
        }

        if ($submenu == 'coupondispensers') {
            $document->setTitle("Coupon Dispenser");
        }



    }


    public static function setAlias($text)
    {


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

    public static function SetMappaContenutoUnita($item)
    {


        $db = JFactory::getDBO();

        $contentid = $item['id'];
        $new_unit = $item['categoria'];

        if (!$contentid) {
            return false;
        }

        // categorie = new unit;

        $query = $db->getQuery(true)
            ->select('idunita')
            ->from('#__gg_unit_map')
            ->where("idcontenuto = '" . $contentid . "'");

        $db->setQuery($query);
        $current_unit = $db->loadResult();

        if (isset($current_unit)) {

            if ($current_unit != $new_unit) {

                // contenuto già associato ad un'unita --> update del record
                $query = "UPDATE  #__gg_unit_map SET idunita=" . $new_unit . " WHERE idcontenuto=" . $contentid;
                $db->setQuery((string)$query);
                $db->execute();
            }

        } else {

            // nuovo contenuto
            $query = "INSERT INTO #__gg_unit_map (idcontenuto, idunita) values ($contentid,$new_unit) "; // ON DUPLICATE KEY UPDATE idunita=" . $value;
            $db->setQuery((string)$query);
            $db->execute();

        }


        // se sto cambiando unità di appartenenza --> update
        // altrimenti non tocco ste tabelle

//        $query = "DELETE FROM #__gg_unit_map WHERE idcontenuto= $contentid";
//        $db->setQuery((string) $query);
//        $db->execute();


        return true;
    }


    public static function GetMappaContenutoUnita($item)
    {

        $res = array();
        if (!$item->id)
            return $res;

        try {
            $db = JFactory::getDBO();

            $query = $db->getQuery(true);
            $query->select('idunita');
            $query->from('#__gg_unit_map');
            $query->where('idcontenuto=' . $item->id);

            $db->setQuery((string)$query);
            $res = $db->loadColumn();

            return $res;
        } catch (Exception $e) {
            print_r($e);
        }


    }

    public static function SetMappaContenutoFiles($item)
    {

        try {
            $db = JFactory::getDBO();

            $contentid = $item['id'];
            $files = explode(",", $item['files']);

            $query_del = "DELETE FROM #__gg_files_map WHERE idcontenuto= $contentid";
            $db->setQuery((string)$query_del);
            $db->execute();

            foreach ($files as $value) {
                $query = "INSERT IGNORE INTO #__gg_files_map (idcontenuto, idfile ) values ($contentid,$value)";

                $db->setQuery((string)$query);
                $res = $db->execute();

            }
        } catch (Exception $e) {
            echo "<pre>";
            print_r($e);
            echo "</pre>";
            die("SetMappaContenutoFile");
        }

        return $res;
    }

    public static function GetMappaContenutoFiles($item)
    {
        $db = JFactory::getDBO();

        $res = array();
        if (!$item->id)
            return $res;

        try {
            $query = $db->getQuery(true);
            $query->select('idfile');
            $query->from('#__gg_files_map');
            $query->where('idcontenuto=' . $item->id);

            $db->setQuery((string)$query);
            $res = $db->loadColumn();

        } catch (Exception $e) {
            print_r($e);
            die("Errore getMappaContenutofile");
        }
        return $res;
    }

    public static function GetMappaContenutoAcl($item)
    {
        FB::info($item, "->GetMappaContenutoAcl");
        $db = JFactory::getDBO();

        $res = array();
        if (!$item->id)
            return $res;

        try {
            $query = $db->getQuery(true);
            $query->select('id_group');
            $query->from('#__gg_contenuti_acl');
            $query->where('id_contenuto=' . $item->id);

            $db->setQuery((string)$query);
            $res = $db->loadColumn();

            FB::log((string)$query, "GetMappaContenutoAcl");

        } catch (Exception $e) {
            FB::error($e);
        }
        return $res;
    }

    public static function SetMappaContenutoAcl($item)
    {
        FB::info($item . "->SetMappaContenutoAcl");
        $db = JFactory::getDBO();

        $contentid = $item['id'];
        $acl = explode(",", $item['acl']);

        $query = "DELETE FROM #__gg_contenuti_acl WHERE id_contenuto= $contentid";
        $db->setQuery((string)$query);
        $res = $db->loadResult();


        foreach ($acl as $value) {
            $query = "INSERT IGNORE INTO #__gg_contenuti_acl (id_contenuto, id_group ) values ($contentid,$value)";

            $db->setQuery((string)$query);
            $res = $db->loadResult();
        }
        return $res;
    }


    public static function SetMappaContenutoParams($item)
    {
        FB::info($item . "->SetmappaContenutoParams");
        $db = JFactory::getDBO();

        $contentid = $item['id'];
        $params = explode(",", $item['parametri']);

        $query = "DELETE FROM #__gg_param_map WHERE idcontenuto= $contentid";
        $db->setQuery((string)$query);
        $res = $db->loadResult();


        foreach ($params as $value) {
            $query = "INSERT IGNORE INTO #__gg_param_map (idcontenuto, idparametro ) values ($contentid,$value)";

            $db->setQuery((string)$query);
            $res = $db->loadResult();
        }
        return $res;
    }


    public static function GetMappaContenutoParams($item)
    {
        FB::info($item, "->GetMappaContenutoParams");
        $db = JFactory::getDBO();

        $res = array();
        if (!$item->id)
            return $res;

        try {
            $query = $db->getQuery(true);
            $query->select('idparametro');
            $query->from('#__gg_param_map');
            $query->where('idcontenuto=' . $item->id);

            $db->setQuery((string)$query);
            $res = $db->loadColumn();

            FB::log($res, "GetMappaContenutoParams");

        } catch (Exception $e) {
            FB::error($e);
        }
        return $res;
    }

    public static function getUserGroupName($user_id, $return_text = false)
    {


        $db = JFactory::getDBO();
        $groups = JAccess::getGroupsByUser($user_id);
        $groupid_list = '(' . implode(',', $groups) . ')';
        $query = $db->getQuery(true);
        $query->select('title');
        $query->from('#__usergroups');
        $query->where('id IN ' . $groupid_list);
        $db->setQuery($query);
        $rows = $db->loadColumn();

        if ($return_text) {
            return implode(', <br>', $rows);
        } else
            return $rows;

    }

    public static function GetMappaAccessoGruppi($item)
    {
        $db = JFactory::getDBO();

        $res = array();
        if (!$item->id)
            return $res;

        try {
            $query = $db->getQuery(true);
            $query->select('idgruppo');
            $query->from('#__gg_usergroup_map');
            $query->where('idunita=' . $item->id);

            $db->setQuery((string)$query);
            $res = $db->loadColumn();

        } catch (Exception $e) {
            print_r($e);
            die("Errore GetMappaAccessoGruppi");
        }
        return $res;
    }

    public static function SetMappaAccessoGruppi($item)
    {

        try {
            $db = JFactory::getDBO();

            $unitid = $item['id'];
            $files = explode(",", $item['id_gruppi_abilitati']);

            $query_del = "DELETE FROM #__gg_usergroup_map WHERE idunita = $unitid";
            $db->setQuery((string)$query_del);
            $db->execute();

            foreach ($files as $value) {
                $query = "INSERT IGNORE INTO #__gg_usergroup_map (idunita, idgruppo) values ($unitid,$value)";

                $db->setQuery((string)$query);
                $res = $db->execute();

            }
        } catch (Exception $e) {
            echo "<pre>";
            print_r($e);
            echo "</pre>";
            die("SetMappaAccessoGruppi");
        }

        return $res;
    }

    public static function GetBoxId($item)
    {
        $db = JFactory::getDBO();

        $res = array();
        if (!$item->id)
            return $res;

        try {
            $query = $db->getQuery(true);
            $query->select('box');
            $query->from('#__gg_box_unit_map');
            $query->where('id_unita=' . $item->id);

            $db->setQuery((string)$query);
            $res = $db->loadColumn();

        } catch (Exception $e) {
            print_r($e);
            die("Errore GetBoxId");
        }
        return $res;
    }

    public static function SetBoxId($item)
    {

        try {
            $db = JFactory::getDBO();

            $unitid = $item['id'];
            $id_box = $item['id_box'];

            //se non cambio box di appartenenza mantengo lo stesso ordine
            $columns = array('box', 'id_unita', 'order');
            $query = $db->getQuery(true);
            $query->select($db->quoteName($columns));
            $query->from('#__gg_box_unit_map');
            $query->where('id_unita=' . $unitid);
            $db->setQuery((string)$query);
            $current = $db->loadAssoc();


            $order = $current['box'] == $id_box ? $current["order"] : 0;


            $query_del = "DELETE FROM #__gg_box_unit_map WHERE id_unita = $unitid";
            $db->setQuery((string)$query_del);
            $res = $db->execute();

            if ($id_box > -1) {


                $query = $db->getQuery(true);
                $values = array($id_box, $unitid, $order);
                $query->insert($db->quoteName('#__gg_box_unit_map'))
                    ->columns($db->quoteName($columns))
                    ->values(implode(',', $values));

                $db->setQuery($query);
                $db->execute();

            }


        } catch (Exception $e) {
            echo "<pre>";
            print_r($e);
            echo "</pre>";
            die("SetBoxId");
        }

        return $res;
    }


    public static function GetMappaAccessoPiattaforme($item)
    {

        $db = JFactory::getDBO();

        $res = array();
        if (!$item->id)
            return $res;

        try {
            $query = $db->getQuery(true);
            $query->select('id_gruppo_piattaforma');
            $query->from('#__gg_piattaforma_corso_map');
            $query->where('id_unita=' . $item->id);

            $db->setQuery((string)$query);
            $res = $db->loadColumn();

        } catch (Exception $e) {
            print_r($e);
            die("Errore GetMappaAccessoPiattaforme");
        }
        return $res;
    }

    public static function SetMappaAccessoPiattaforme($item)
    {

        try {
            $db = JFactory::getDBO();

            $unitid = $item['id'];
            $list_piattaforme = $item['id_piattaforme_abilitate'];


            $query_del = "DELETE FROM #__gg_piattaforma_corso_map WHERE id_unita = $unitid";
            $db->setQuery((string)$query_del);
            $db->execute();

            foreach ($list_piattaforme as $value) {
                $query = "INSERT IGNORE INTO #__gg_piattaforma_corso_map (id_unita, id_gruppo_piattaforma) values ($unitid,$value)";

                $db->setQuery((string)$query);
                $res = $db->execute();

            }
        } catch (Exception $e) {
            echo "<pre>";
            print_r($e);
            echo "</pre>";
            die("SetMappaAccessoPiattaforme");
        }

        return $res;
    }

    public function GetSummaryReportColumns() {

        /*
         * coupon => Coupon
         * nome => COM_GGLMS_REPORT_NOME
         * cognome => COM_GGLMS_REPORT_COGNOME
         * cb_codicefiscale => Codice fiscale
         * id_user => COM_GGLMS_REPORT_USERDETAIL
         * titolo_corso => COM_GGLMS_GLOBAL_CORSO
         * data_creazione => COM_GGLMS_GLOBAL_CREATION_DATE
         * data_utilizzo => COM_GGLMS_GLOBAL_USE_DATE
         * azienda => COM_GGLMS_GLOBAL_COMPANY
         * stato => COM_GGLMS_GLOBAL_STATO
         * data_inizio => COM_GGLMS_REPORT_DATA_INIZIO
         * data_fine => COM_GGLMS_REPORT_DATA_FINE
         * id_corso => COM_GGLMS_GLOBAL_ATTESTATO
         * venditore => COM_GGLMS_GLOBAL_VENDITORE
         * scaduto => COM_GGLMS_REPORT_USER_SCADUTO
         *
         * */

        // localizzazione
        $lang = JFactory::getLanguage();
        // file di traduzione del componente
        $lang->load('com_gglms', JPATH_SITE . '/components/com_gglms', $lang->getTag(), true);

        $columns = array(
            array('text' => 'Coupon', 'value' => 'coupon'),
            array('text' => JText::_('COM_GGLMS_REPORT_NOME'), 'value' => 'nome'),
            array('text' => JText::_('COM_GGLMS_REPORT_COGNOME'), 'value' => 'cognome'),
            array('text' => JText::_('COM_GGLSM_REPORT_CODICE_FISCALE'), 'value' => 'cb_codicefiscale'),
            array('text' => JText::_('COM_GGLMS_REPORT_USERDETAIL'), 'value' => 'id_user'),
            array('text' => JText::_('COM_GGLMS_GLOBAL_CORSO'), 'value' => 'titolo_corso'),
            array('text' => JText::_('COM_GGLMS_GLOBAL_CREATION_DATE'), 'value' => 'data_creazione'),
            array('text' => JText::_('COM_GGLMS_GLOBAL_USE_DATE'), 'value' => 'data_utilizzo'),
            array('text' => JText::_('COM_GGLMS_GLOBAL_COMPANY'), 'value' => 'azienda'),
            array('text' => JText::_('COM_GGLMS_GLOBAL_STATO'), 'value' => 'stato'),
            array('text' => JText::_('COM_GGLMS_REPORT_DATA_INIZIO'), 'value' => 'data_inizio'),
            array('text' => JText::_('COM_GGLMS_REPORT_DATA_FINE'), 'value' => 'data_fine'),
            array('text' => JText::_('COM_GGLMS_GLOBAL_ATTESTATO'), 'value' => 'id_corso'),
            array('text' => JText::_('COM_GGLMS_GLOBAL_VENDITORE'), 'value' => 'venditore'),
            array('text' => JText::_('COM_GGLMS_REPORT_USER_SCADUTO'), 'value' => 'scaduto')
        );

        return $columns;
    }

}
