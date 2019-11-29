<?php
/**
 * @package        Joomla.Tutorials
 * @subpackage    Components
 * @copyright    Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license        License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

class utilityHelper
{

    public static function getGGlmsParam()
    {
        try {

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            $query->select('*');
            $query->from('#__gg_configs AS u');
            $db->setQuery($query);

            // Check for a database error.
            if ($db->getErrorNum()) {
                JError::raiseWarning(500, $db->getErrorMsg());
            }

            $res = $db->loadObjectList();

            foreach ($res as $key => $item) {

            }

            return $res;
        } catch (Exception $e) {

        }
    }

    public static function conformita_cf($cf)
    {

        $cf = strtoupper($cf);

        if ($cf === '') {
            $res['valido'] = 0;
            $res['msg'] = 'non è compilato';
            $res['cf'] = $cf;
            return $res;
        };
        if (strlen($cf) != 16) {
            $res['valido'] = 0;
            $res['msg'] = "ha una lunghezza non \n"
                . "corretta: il codice fiscale dovrebbe essere lungo\n"
                . "esattamente 16 caratteri";
            $res['cf'] = $cf;
            return $res;
        }


        if (preg_match("/^[A-Z0-9]+\$/", $cf) != 1) {

            $res['valido'] = 0;
            $res['msg'] = "contiene dei caratteri non validi:\n"
                . "i soli caratteri validi sono le lettere e le cifre";
            $res['cf'] = $cf;
            return $res;

        }
        $s = 0;
        for ($i = 1; $i <= 13; $i += 2) {
            $c = $cf[$i];
            if (strcmp($c, "0") >= 0 and strcmp($c, "9") <= 0)
                $s += ord($c) - ord('0');
            else
                $s += ord($c) - ord('A');
        }
        for ($i = 0; $i <= 14; $i += 2) {
            $c = $cf[$i];
            switch ($c) {
                case '0':
                    $s += 1;
                    break;
                case '1':
                    $s += 0;
                    break;
                case '2':
                    $s += 5;
                    break;
                case '3':
                    $s += 7;
                    break;
                case '4':
                    $s += 9;
                    break;
                case '5':
                    $s += 13;
                    break;
                case '6':
                    $s += 15;
                    break;
                case '7':
                    $s += 17;
                    break;
                case '8':
                    $s += 19;
                    break;
                case '9':
                    $s += 21;
                    break;
                case 'A':
                    $s += 1;
                    break;
                case 'B':
                    $s += 0;
                    break;
                case 'C':
                    $s += 5;
                    break;
                case 'D':
                    $s += 7;
                    break;
                case 'E':
                    $s += 9;
                    break;
                case 'F':
                    $s += 13;
                    break;
                case 'G':
                    $s += 15;
                    break;
                case 'H':
                    $s += 17;
                    break;
                case 'I':
                    $s += 19;
                    break;
                case 'J':
                    $s += 21;
                    break;
                case 'K':
                    $s += 2;
                    break;
                case 'L':
                    $s += 4;
                    break;
                case 'M':
                    $s += 18;
                    break;
                case 'N':
                    $s += 20;
                    break;
                case 'O':
                    $s += 11;
                    break;
                case 'P':
                    $s += 3;
                    break;
                case 'Q':
                    $s += 6;
                    break;
                case 'R':
                    $s += 8;
                    break;
                case 'S':
                    $s += 12;
                    break;
                case 'T':
                    $s += 14;
                    break;
                case 'U':
                    $s += 16;
                    break;
                case 'V':
                    $s += 10;
                    break;
                case 'W':
                    $s += 22;
                    break;
                case 'X':
                    $s += 25;
                    break;
                case 'Y':
                    $s += 24;
                    break;
                case 'Z':
                    $s += 23;
                    break;
                /*. missing_default: .*/
            }
        }
        if (chr($s % 26 + ord('A')) != $cf[15]) {
            $res['valido'] = 0;
            $res['msg'] = "non &egrave; corretto:\n"
                . "il codice di controllo non corrisponde";
            $res['cf'] = $cf;
            return $res;
        }

        $res['valido'] = 1;
        $res['msg'] = '';
        $res['cf'] = $cf;
        return $res;
    }

    public static function setComponentParam($key, $value)
    {

        $params = JComponentHelper::getParams('com_gglms');
        $params->set($key, $value);

        $componentid = JComponentHelper::getComponent('com_gglms')->id;
        $table = JTable::getInstance('extension');
        $table->load($componentid);
        $table->bind(array('params' => $params->toString()));

        if (!$table->check()) {
            DEBUGG::log('Errore salvataggio parametri', '', 1);
            return false;
        }
        if (!$table->store()) {
            DEBUGG::log('Errore salvataggio parametri', '', 1);
            return false;
        }
    }

    public static function DISATTIVATOconvertiDurata($durata)
    {
        $m = floor(($durata % 3600) / 60);
        $s = ($durata % 3600) % 60;
        $result = sprintf('%02d:%02d', $m, $s);

        return $result;
    }

    /////////////////////////////////////

    // metodi per dropdown report, monitora coupon, generacoupon

    public static function getGruppiCorsi()
    {

        // carico i gruppi dei corsi, filtrati per piattaforma
        try {
            $_config = new gglmsModelConfig();
            $id_gruppo_accesso_corsi = $_config->getConfigValue('id_gruppo_corsi');

            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('g.id as value, u.titolo as text')
                ->from('#__usergroups as g')
                ->join('inner', '#__gg_usergroup_map AS gm ON g.id = gm.idgruppo')
                ->join('inner', '#__gg_unit AS u ON u.id = gm.idunita')
                ->join('inner', '#__gg_piattaforma_corso_map  AS pcm ON pcm.id_unita = u.id')
                ->join('inner', '#__usergroups_details  AS ud ON ud.group_id = pcm.id_gruppo_piattaforma')
                ->where(" g.parent_id=" . $id_gruppo_accesso_corsi)
                ->where("ud.dominio='" . DOMINIO . "'");

//            var_dump((string)$query);
//            die();

            $db->setQuery($query);
            $corsi = $db->loadObjectList();

        } catch (Exception $e) {
            DEBUGG::error($e, 'getGruppiCorsi');

        }


        return $corsi;
    }

    public static function getSocietaByUser()
    {

        try {

            $Juser = JFactory::getUser();

            $user = new gglmsModelUsers();
            $user->get_user($Juser->id);


            if ($user->is_tutor_piattaforma($Juser->id) || $user->is_user_superadmin($Juser->id)) {

                //  utente loggato  ha ruolo TUTOR PIATTAFORMA, prendo le scoieta figlie di piattaforma
                // lo stesso se è super admin
                $usergroups = $user->get_user_societa($Juser->id, false);


            } else if ($user->is_tutor_aziendale($Juser->id)) {

                // utente loggato ha ruolo di TUTOR AZIENDALE , prendo la sua società
                $usergroups = $user->get_user_societa($Juser->id, true);

            }


            return $usergroups;

        } catch (Exceptions $e) {
//
            DEBUGG::error($e, 'errore get userGroup ', 1);
        }


        // vecchia logica
//        try {
//            $query = $this->_db->getQuery(true);
//            $query->select('config_value');
//            $query->from('#__gg_configs');
//            $query->where('config_key=\'id_gruppi_visibili\'');
//
//            $this->_db->setQuery($query);
//            $usergroupsfromparams = $this->_db->loadResult();
//
//            $usergroupsfrompermessi = $this->get_report_view_permessi_gruppi();
//
//            $query = $this->_db->getQuery(true);
//            $query->select('id, title');
//            $query->from('#__usergroups AS u');
//            if ($usergroupsfrompermessi != null) {
//                $query->where('u.id in (' . $usergroupsfrompermessi . ') ');
//            } else {
//                $query->where('u.id in (' . $usergroupsfromparams . ') ');
//            }
//
//            $this->_db->setQuery($query);
//
//            $usergroups = $this->_db->loadObjectList();
//
//            return $usergroups;
//        } catch (exceptions $e) {
//
//            DEBUGG::error($e, 'errore get Contenuti unita', 1);
//        }
    }

    public static function getPiattaformeByUser()
    {
        try {
            $user = new gglmsModelUsers();
            $Juser = JFactory::getUser();
            $user->get_user($Juser->id);
            $_japp = JFactory::getApplication();
            $societa_venditrici = [];

            if ($user->is_venditore($Juser->id)) {
                $societa_venditrici = $user->get_user_piattaforme($Juser->id);


            }
//            else {
//                $_japp->redirect(('index.php?option=com_gglms&view=genera'), $_japp->enqueueMessage('L\'utente loggato non appartiene al gruppo venditore, non può generare coupon', 'Error'));
//            }

            return $societa_venditrici;
        } catch (Exception $e) {

            DEBUGG::error($e, 'getVenditrici');
        }
    }


    ////////////////////////////////////    export csv


    // esporta $data in un file csv
    // se $column_list != null esporta solo le colonne inidicate altrimenti le esporta tutte
    public static function _export_data_csv($filename, $data_input, $column_list = array())
    {

        $data = array();
        if (!empty($column_list)) {

            // creo nuovo array con dati che hanno solo le colonne da esportare
            foreach ($data_input as &$row) {
                $d = [];
                foreach ($column_list as $column) {
                    $d[$column] = $row[$column];
                }
                array_push($data, $d);
            }


        } else {
            $data = $data_input;
        }

        try {
            if (!empty($data)) {
                $comma = ';';
                $quote = '"';
                $CR = "\015\012";
                // Make csv rows for field name
                $i = 0;
                $fields = $data[0];

                $cnt_fields = count($fields);
                $csv_fields = '';

                foreach ($fields as $name => $val) {
                    $i++;
                    if ($cnt_fields <= $i) $comma = '';
                    $csv_fields .= $quote . $name . $quote . $comma;


                }

                // Make csv rows for data
                $csv_values = '';
                foreach ($data as $row_) {
                    $i = 0;
                    $comma = ';';
                    foreach ($row_ as $name => $val) {
                        $i++;
                        if ($cnt_fields <= $i) $comma = '';
                        $csv_values .= $quote . $val . $quote . $comma;
                    }
                    $csv_values .= $CR;
                }

                //echo ($csv_values);

                $csv_save = $csv_fields . $CR . $csv_values;
            }
            echo $csv_save;


//                $filename = 'monitora_coupon';

            $filename = preg_replace('~[^\\pL\d]+~u', '_', $filename);
            $filename = iconv('utf-8', 'us-ascii//TRANSLIT', $filename);
            $filename = strtolower($filename);
            $filename = trim($filename, '_');
            $filename = preg_replace('~[^-\w]+~', '', $filename);
            $filename .= "-" . date("d/m/Y");
            $filename = $filename . ".csv";


            header("Content-Type: text/plain");
            header("Content-disposition: attachment; filename=$filename");
            header("Content-Transfer-Encoding: binary");
            header("Pragma: no-cache");
            header("Expires: 0");


        } catch (exceptions $exception) {
            echo $exception->getMessage();
        }


    }


}
