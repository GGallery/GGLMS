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

    public static $white_cf = '----------------';

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

        // forzatura per consentire la gestione del codice fiscale per stranieri
        if ($cf == self::$white_cf) {
            $res['valido'] = 1;
            $res['msg'] = '';
            $res['cf'] = $cf;

            return $res;
        }

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
//Moni
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

    public static function getGruppiCorsi($id_piattaforma = null, $mobile = false)
    {

        // carico i gruppi dei corsi, filtrati per piattaforma
        try {
            $_config = new gglmsModelConfig();
            $id_gruppo_accesso_corsi = $_config->getConfigValue('id_gruppo_corsi');

            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('DISTINCT  g.id as value, u.titolo as text')
                ->from('#__usergroups as g')
                ->join('inner', '#__gg_usergroup_map AS gm ON g.id = gm.idgruppo')
                ->join('inner', '#__gg_unit AS u ON u.id = gm.idunita')
                ->join('inner', '#__gg_piattaforma_corso_map  AS pcm ON pcm.id_unita = u.id')
                ->join('inner', '#__usergroups_details  AS ud ON ud.group_id = pcm.id_gruppo_piattaforma')
                ->where(" g.parent_id=" . $id_gruppo_accesso_corsi)
                ->where(" u.pubblicato=1")
                ->order('u.titolo');


            if($mobile){
                $query = $query->where("u.mobile = 1");
            }


            if ($id_piattaforma != null) {

                // specifica piattaforma, serve nel form genera coupon qunado un super admin vede due piattaforme
                $query = $query->where("ud.group_id=" . $id_piattaforma);

            } else {
                // piattaforma corrente
//                $query = $query->where("ud.dominio='" . DOMINIO . "'");

                // come in models\report getCorsi()

                $user = JFactory::getUser();
                $userid = $user->get('id');
                // controllo se esiste un utente collegato, potrebbe non esserci im caso di chiamata genera coupon da ecommerce esterni!!
                if($userid != null)
                {
                    //  con il barbatrucco dei coupon la piattaforma corrente  non è più quella del dominio MA quella dell'utente collegato
                    $model_user = new gglmsModelUsers();
                    $id_piattaforma = $model_user->get_user_piattaforme($userid);
                    $id_piattaforma_array = array();


                    foreach ($id_piattaforma as $p) {
                        array_push($id_piattaforma_array, $p->value);
                    }

                    $query->where($db->quoteName('ud.group_id') . ' IN (' . implode(", ", $id_piattaforma_array) . ')');
                }


            }



            $db->setQuery($query);
            $corsi = $db->loadObjectList();

        } catch (Exception $e) {
            DEBUGG::error($e, 'getGruppiCorsi');

        }


        return $corsi;
    }

    // filtro azienda se tutor aziendale
    public static function getIdCorsi($id_piattaforma = null, $filtro_azienda = null)
    {

        // carico i gruppi dei corsi, filtrati per piattaforma
        try {
            $_config = new gglmsModelConfig();
            $id_gruppo_accesso_corsi = $_config->getConfigValue('id_gruppo_corsi');

            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('distinct u.id as value, u.titolo as text')
                ->from('#__usergroups as g')
                ->join('inner', '#__gg_usergroup_map AS gm ON g.id = gm.idgruppo')
                ->join('inner', '#__gg_unit AS u ON u.id = gm.idunita')
                ->join('inner', '#__gg_piattaforma_corso_map  AS pcm ON pcm.id_unita = u.id')
                ->join('inner', '#__usergroups_details  AS ud ON ud.group_id = pcm.id_gruppo_piattaforma');


            // gestione filtro per azienda
            if (!is_null($filtro_azienda)) {

                // al volo includo il model users
                require_once JPATH_COMPONENT . '/models/users.php';

                // utente corrente
                $user = JFactory::getUser();
                $user_id = $user->get('id');

                // verifico se è tutor aziendale
                $model_user = new gglmsModelUsers();
                $tutor_az = $model_user->is_tutor_aziendale($user_id);

                // utente è tutor aziendale
                if ($tutor_az) {

                    /*
                    $id_piattaforma = $model_user->get_user_piattaforme($user_id);

                    $id_piattaforma_array = array();
                    foreach ($id_piattaforma as $p) {
                        array_push($id_piattaforma_array, $p->value);
                    }


                    $sub_query->select("DISTINCT id_gruppi")
                        ->from('#__gg_coupon')
                        ->where('gruppo IN (' . implode(", ", $id_piattaforma_array) . ')');
                    */

                    $lista_aziende = $model_user->get_user_societa($user_id, true);

                    // applico filtro soltanto se ci sono società associate al tutor aziendale
                    if (count($lista_aziende) > 0) {
                        $sub_query = $db->getQuery(true);
                        $sub_query->select("DISTINCT id_gruppi")
                            ->from('#__gg_coupon')
                            ->where("id_societa in (" . implode(', ', self::get_id_aziende($lista_aziende)) . ")");

                        $query->join('inner', '(' . $sub_query . ') AS sub1 ON g.id = sub1.id_gruppi');
                    }
                }
            }

            $query
                ->where(" g.parent_id=" . $id_gruppo_accesso_corsi)
                ->where(" u.pubblicato=1")
                ->order('u.titolo');
            if ($id_piattaforma != null) {

                // specifica piattaforma, serve nel form genera coupon qunado un super admin vede due piattaforme
                $query = $query->where("ud.group_id=" . $id_piattaforma);

            } else {

                // per l'ambiente di sviluppo..altrimenti la query non produce risultati per i corsi
                $_domain = self::filtra_dominio_per_test(DOMINIO);

                // piattaforma corrente
                //$query = $query->where("ud.dominio='" . DOMINIO . "'");
                $query = $query->where("ud.dominio='" . $_domain . "'");
            }

            $db->setQuery($query);
            $corsi = $db->loadObjectList();

        } catch (Exception $e) {
            DEBUGG::error($e, 'getGruppiCorsi');
        }


        return $corsi;
    }

    public static function getTitoloCorsoPadre($id_padre, $arr_corsi) {

        try {

            foreach ($arr_corsi as $key => $corso) {
                if ($corso->id == $id_padre)
                    return $corso->titolo;
            }

            throw new Exception("Nessun titolo per id_padre " . $id_padre);

        }
        catch (Exception $e) {
            DEBUGG::error($e, __FUNCTION__);
        }
    }

    public static function getDettaglioDurataByCorso($id_corso, $user_id = null) {

        try {

            $db = JFactory::getDbo();

            $query = $db->getQuery(true);
            $sub_query1 = $db->getQuery(true);

            $query->select('CN.titolo AS titolo_evento, CN.durata AS durata_evento, 
                                SUM(LG.permanenza) AS tempo_visualizzato');
            $query->from('#__comprofiler CP');
            $query->join('inner', '#__gg_log LG ON CP.user_id = LG.id_utente');
            $query->join('inner', '#__gg_contenuti CN ON LG.id_contenuto = CN.id');

            $sub_query1->select('MAP.idcontenuto');
            $sub_query1->from('#__gg_unit_map MAP');
            $sub_query1->join('inner', '#__gg_unit U ON MAP.idunita = U.id');

            $sub_query1->where(' (MAP.idunita = ' . $id_corso . ' OR U.unitapadre = ' . $id_corso . ')');
            $sub_query1->where('U.pubblicato = 1');
            $query->join('inner', '(' . $sub_query1 . ') AS SUB1 ON CN.id = SUB1.idcontenuto');

            if (!is_null($user_id))
                $query->where('LG.id_utente = ' . $user_id);

            $query->where('CN.pubblicato = 1');

            $query->group($db->quoteName('LG.id_utente'));
            $query->group($db->quoteName('LG.id_contenuto'));

            $db->setQuery($query);
            $rows = $db->loadAssocList();

            return $rows;

        }
        catch (Exception $e) {
            DEBUGG::error($e, __FUNCTION__);
        }
    }

    public static function getDettaglioDurataByCorsi($arr_corsi = array()) {

        try {

            if (!is_array($arr_corsi)
                || count($arr_corsi) == 0)
                return null;

            $Juser = JFactory::getUser();
            $arr_dettagli = array();

            foreach ($arr_corsi as $key => $corso) {

                $dettagli = self::getDettaglioDurataByCorso($corso->id, $Juser->id);
                if (!is_array($dettagli)
                    || count($dettagli) == 0)
                    continue;

                $arr_dettagli[$corso->id] = $dettagli;
            }

            return $arr_dettagli;

        }
        catch (Exception $e) {
            DEBUGG::error($e, __FUNCTION__);
        }

    }

    public static function getSocietaByUser()
    {

        try {

            $Juser = JFactory::getUser();

            $user = new gglmsModelUsers();
            $user->get_user($Juser->id);

            $usergroups = null;


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

// se $only_vendor è TRUE --> ritorno la piattaforma SOLO se utente è venditore per quella piattaforma
//se $only_vendor è TRUE --> ritorno la piattaforma
    public static function getPiattaformeByUser($only_vendor)
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


            return $societa_venditrici;
        } catch (Exception $e) {

            DEBUGG::error($e, 'getVenditrici');
        }
    }

    public static function logMail($template, $sender, $recipient, $status, $cc = null, $id_gruppo_corso = null)
    {
        try {


            $values[] = sprintf("('%s', '%s', '%s', '%s', '%s', '%s' ,%d)",
                $template,
                $sender,
                $recipient,
                $cc,
                $id_gruppo_corso,
                date('Y-m-d H:i:s', time()), //  time(), //creation_time
                $status
            );

            $query = 'INSERT INTO #__gg_mail_log (template, sender, recipient, cc, id_gruppo_corso,created_at,status) VALUES ' . join(',', $values);

//var_dump($query);
//die();

            $db = JFactory::getDbo();
            $db->setQuery($query);
            if (false === $db->execute()) {
                throw new RuntimeException($db->getErrorMsg(), E_USER_ERROR);
            }


        } catch (Exception $e) {

            DEBUGG::error($e, 'logMail');
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

    // in giorni, mesi, anni ritorna la differenza fra due date
    public static function get_date_diff_format($date1, $date2, $format = "d") {

        $diff = abs(strtotime($date2)-strtotime($date1));
        $years = floor($diff / (365*60*60*24));
        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

        switch ($format) {

            case 'd':
                return $days;

            case 'm':
                return $months;

            case 'y':
                return $years;

            default:
                return $days;

        }
    }

    // da oggetto azienda ad array con lista id
    public static function get_id_aziende($azienda)
    {

        $a_list = array();
        foreach ($azienda as $a) {
            array_push($a_list, $a->id);
        }

        return $a_list;

    }

    // in base a dei valori predefiniti imposto il nome del file con il quale un attestato verrà scaricato
    public static function build_nome_file_attestato($data, $salva_come) {

        $arr_salva = explode(",", $salva_come);
        $nome_file = 'attestato';

        foreach ($arr_salva as $indice) {

            switch (trim($indice)) {

                case 'nome':
                    $nome_file .= '_' . $data->user->nome;
                    break;

                case 'cognome':
                    $nome_file .= '_' . $data->user->cognome;
                    break;

                case 'codice_fiscale':
                    $nome_file .= '_' . $data->user->cb_codicefiscale;
                    break;

                case 'codice_corso':
                    $nome_file .= '_' . $data->dati_corso[0]->codice_corso;
                    break;

                case 'data_inizio_corso':
                    $nome_file .= '_' . $data->dati_corso[0]->data_inizio;
                    break;

                case 'data_fine_corso':
                    $nome_file .= '_' . $data->dati_corso[0]->data_fine;
                    break;

            }
        }

        $nome_file .= '_' . rand() . '.pdf';
        //$nome_file .= '.pdf';
        return $nome_file;

    }

    // per sviluppo filtro DOMAIN - di default i siti che in produzione iniziano per web. in sviluppo saranno test.
    function filtra_dominio_per_test($_domain) {

        if (strpos($_domain, 'test.') !== false)
            $_domain = str_replace("test.", "web.", $_domain);

        return $_domain;

    }

}
