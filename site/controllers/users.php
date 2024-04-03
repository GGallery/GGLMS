<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/libraries/xls/src/Spout/Autoloader/autoload.php';
require_once JPATH_COMPONENT . '/models/generacoupon.php';
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerUsers extends JControllerLegacy
{

    public function login()
    {
        $app = JFactory::getApplication();
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('`id`, `username`, `password`');
        $query->from('`#__users`');
        $query->where('username=' . $db->Quote( $_REQUEST['username'])) ;
        $query->where('password=' . $db->Quote( $_REQUEST['password'])) ;

        $db->setQuery( $query );
        $result = $db->loadObject();

        if($result) {
            JPluginHelper::importPlugin('user');

            $options = array();
            $options['action'] = 'core.login.site';

            $response['username'] = $result->username;
            $logged= $app->triggerEvent('onUserLogin', array((array)$response, $options));
            if($logged)
                $app->enqueueMessage("Accesso effettuato correttamente come utente ". $_REQUEST['username'], 'success');
            else {
                $app->enqueueMessage("Problemi nell'effettuare l'accesso", 'danger');
            }
        }
        else
        {
            $app->enqueueMessage("Credenziali errate", 'danger');
        }

        $app->redirect( JURI::root());
    }

    public function reset(){
        $app = JFactory::getApplication();
        $appinput= $app->input;
        $config = JFactory::getConfig();
        $db= JFactory::getDbo();

        $user_id =  $appinput->get('id', 0, 'integer');
        $username =  $appinput->get('username', '', 'USERNAME');

        $parole=array('rosso','giallo','ambra','albicocca','amaranto','azzurro','bianco','bronzo','rosa','verde', 'turchese','magenta');
        $newpassword= $parole[rand(0, sizeof($parole))] . rand(10,99);

        $query = $db->getQuery(true);
        $query->update("#__users");
        $query->set("password='".JUserHelper::hashPassword($newpassword)."'");
        $query->where("id=".$user_id);

        $db->setQuery((string) $query);
        $db->execute();

        $ret['username']= $username;
        $ret['password']=$newpassword;

        echo "<h2>Nuove credenziali portale ".$config['sitename']."</h2>";
        echo "<b>Username</b>: ".$ret['username']."<br>";
        echo "<b>Password</b>: ".$ret['password'];

        $app->close();

    }

    public function resetsend(){
        $app = JFactory::getApplication();
        $appinput= $app->input;
        $config = JFactory::getConfig();
        $db= JFactory::getDbo();

        $user_id =  $appinput->get('id', 0, 'integer');
        $username =  $appinput->get('username', '', 'USERNAME');

        $parole=array('rosso','giallo','ambra','albicocca','amaranto','azzurro','bianco','bronzo','rosa','verde', 'turchese','magenta');
        $newpassword= $parole[rand(0, sizeof($parole)-1)] . rand(10,99);

        $query = $db->getQuery(true);
        $query->update("#__users");
        $query->set("password='".JUserHelper::hashPassword($newpassword)."'");
        $query->where("id=".$user_id);

        $db->setQuery((string) $query);
        $db->execute();

        $ret['username']= $username;
        $ret['password']=$newpassword;


        //mail di conferma
        $destinatari = array( $_REQUEST['email']);
        $oggetto ="Nuove credenziali portale ".$config['sitename'];
        $body   = 'Le tue credenziali sono: <br><br>'
            .'Username: <b>'.$username .'</b> <br> '
            .'Password: <b>'.$newpassword .'</b> <br><br> '

            . '<div>Lo staff di '.$config['sitename'].' </div> <br><br>';

        echo $this->sendMail($destinatari, $oggetto ,$body);

        echo "<h2>Nuove credenziali portale ".$config['sitename']."</h2>";
        echo "<b>Username</b>: ".$ret['username']."<br>";
        echo "<b>Password</b>: ".$ret['password'];

        $app->close();

    }

    public function sendMail($destinatari, $oggetto, $body ){

        $mailer = JFactory::getMailer();

        $config = JFactory::getConfig();
        $sender = array(
            $config->get( 'mailfrom' ),
            $config->get( 'fromname' )
        );
        $mailer->setSender($sender);


        $mailer->addRecipient($destinatari);
        $mailer->setSubject($oggetto);
        $mailer->isHtml(true);
        $mailer->Encoding = 'base64';
        $mailer->setBody($body);
        //optional
        $mailer->AddEmbeddedImage( JPATH_COMPONENT.'/images/logo.jpg', 'logo_id', 'logo.jpg', 'base64', 'image/jpeg' );


        $send = $mailer->Send();
        if ( $send !== true )
            return 'Errore invio mail: ';
        else
            return 'Mail inviata';

    }

    public function sso()
    {
        $app = JFactory::getApplication();
        $busta = $_REQUEST['busta'];

        echo "-----------------------------<br>";

        if ($busta) {
            echo "Busta : ". $busta . "<br>";
            echo "Busta BASE64DECODED: " . base64_decode($busta) . "<br>";
        }
        else{
            echo "Busta non fornita<br>";
        }

        echo "-----------------------------<br>";

        $app->close();
    }

    // rebuild degli indici usergroups
    public function rebuild_ugs() {

        $app = JFactory::getApplication();

        $db = JFactory::getDbo();
        // rebuild per indici lft, rgt
        $JTUserGroup = new JTableUsergroup($db);
        $JTUserGroup->rebuild();

        echo "Rebuild done";

        $app->close();
    }

    // importazione rinnovi effettuati manualmente
    public function _import_rinnovi() {

        ini_set('max_execution_time', 0);

        $app = JFactory::getApplication();
        $db = JFactory::getDbo();

        $_user = new gglmsModelUsers();

        try {

            // leggo il file di configurazione
            $config_file = JPATH_ROOT . '/tmp/_import.conf';

            // il file di configurazione non esiste
            if (!file_exists($config_file))
                throw new Exception("file _import.conf non trovato", 1);

            $config_content = file_get_contents($config_file);
            if (!$config_content)
                throw new Exception("file _import.conf non leggibile", 1);

            $json_config = utilityHelper::get_json_decode_error($config_content, true);
            if (!is_array($json_config))
                throw new Exception($json_config, 1);

            // target file
            $file_ext = isset($json_config['file_type']) ? $json_config['file_type'] : null;
            $target_file = isset($json_config['file_name']) ? JPATH_ROOT . '/tmp/' . $json_config['file_name'] : null;

            if (is_null($target_file))
                throw new Exception("Nessun file di importazione definito", 1);

            // il file da leggere non esiste
            if (!file_exists($target_file))
                throw new Exception($target_file . " non trovato", 1);

            $log_file = JPATH_ROOT . '/tmp/_log_' . $json_config['file_name'] . '_' . time();
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->setShouldFormatDates(true);

            if (is_null($reader))
                throw new Exception('Nessun formato file impostato');

            // indice da cui partirà il loop sul foglio
            $numero_prima_riga = isset($json_config['cols_schema']['numero_prima_riga']) ? (int)$json_config['cols_schema']['numero_prima_riga'] : 0;

            $reader->open($target_file); //open the file
            $import_report = array();
            $dt = new DateTime();

            $db->transactionStart();
            $i=0;
            if ($file_ext == 'xlsx'
                || $file_ext == 'xls') {

                // controllo quanti sheet ci sono nel file (> 1 vado in errore)
                $sheets_num = count($reader->getSheetIterator());
                if ($sheets_num > 1)
                    throw new Exception("Sheet multipli non supportati. Il foglio " . $file_ext . " deve contenere soltanto un foglio attivo", 1);

                $sheet_data = array();
                foreach ($reader->getSheetIterator() as $sheet) {
                    if ($sheet->getIndex() === 0) {
                        $sheet_data = $sheet->getRowIterator();
                        break;
                    }
                }

                $import_report['row_existing'] = (count($sheet_data)-$numero_prima_riga);

                foreach ($sheet_data as $row) {

                    if ($i<$numero_prima_riga) {
                        $i++;
                        continue;
                    }

                    // do stuff with the row
                    $_riga_xls = $row->getCells();

                    $codice_fiscale = null;
                    $data_pagamento = null;
                    $quota = null;
                    $espen = null;
                    $modalita_pagamento = null;
                    $importo_pagato = 0;

                    foreach ($_riga_xls as $num_cell => $value_cell) {

                        $_row_value = trim($value_cell->getValue());

                        if ($num_cell == 1) {
                            $codice_fiscale = ($_row_value != "") ? $_row_value : $codice_fiscale;
                        }

                        if ($num_cell == 2) {

                            if ($_row_value != "") {
                                $dt = new DateTime($_row_value);
                                $data_pagamento = $dt->format('Y-m-d H:i:s');
                            }

                        }

                        if ($num_cell == 3) {
                            $quota = ($_row_value != "") ? $_row_value : $quota;
                        }

                        if ($num_cell == 4) {
                            $espen = ($_row_value != "") ? $_row_value : $espen;
                        }

                        if ($num_cell == 5) {
                            $modalita_pagamento = ($_row_value != "") ? $_row_value : $modalita_pagamento;
                        }

                        if ($num_cell == 6) {
                            $importo_pagato = ($_row_value != "") ? $_row_value : $importo_pagato;
                        }

                    }  // level 2

                    // se non c'è codice fiscale non faccio nulla
                    if (is_null($codice_fiscale))
                        continue;

                    // altrimenti devo abilitare il socio
                    // id socio da codice fiscale
                    // inserimento utente in gruppo online
                    $_get_user = $_user->get_user_by_field('cb_codicefiscale', $codice_fiscale, '=', 'cb');
                    // l'utente non esiste oppure si è verificato un errore
                    if (!is_array($_get_user)
                        || is_null($_get_user)
                        || !isset($_get_user['id'])) {
                        UtilityHelper::write_file_to($log_file . '_missing_user.log', "MISSING USER: " . ($i+$numero_prima_riga . ', CF: ' . $codice_fiscale),true);
                        $import_report['missing_user'][] = $codice_fiscale;
                        continue;
                    }

                    $_dettagli_transazione = 'QUOTA SINPE ' . str_replace("-", ",", $quota);
                    $_dettagli_transazione .= (!is_null($espen) && $espen != "") ? " QUOTA ESPEN " . $espen : "";

                    // inserimento quote pagamenti
                    $_pagamento = $_user->insert_user_quote_anno_bonifico($_get_user['id'],
                                                                            $dt->format('Y'),
                                                                            $importo_pagato,
                                                                            $_dettagli_transazione,
                                                                            $data_pagamento,
                                                                            strtolower($modalita_pagamento),
                                                                            null,
                                                                            false);

                    if (!is_array($_pagamento)) {
                        UtilityHelper::write_file_to($log_file . '_payment_failed.log', "PAYMENT FAILED: " . ($i+$numero_prima_riga . ', CF: ' . $codice_fiscale . ', ERR: ' . $_pagamento),true);
                        $import_report['payment_failed'][] = $codice_fiscale;
                        continue;
                    }
                    else {
                        $j_user = JFactory::getUser($_get_user['id']);
                        $_ug = implode("," , $j_user->groups);
                        UtilityHelper::write_file_to($log_file . '_payment_success.log', "PAYMENT SUCCESS: " . ($i+$numero_prima_riga . ', USER_ID: ' . $_get_user['id'] . ', CF: ' . $codice_fiscale . ' UG: ' . $_ug),true);
                        $import_report['payment_success'][] = $codice_fiscale;
                    }

                    $i++;
                } // level 1

            }

            $db->transactionCommit();

            $inserted = count($import_report['payment_success']);
            $not_inserted = count($import_report['payment_failed']);
            $missing = count($import_report['missing_user']);
            $_finish_date = date('d/m/Y H:i:s');

            echo <<<HTML
                <pre>
                {$_finish_date} <br />
                Importazione terminata! <br />
                Inserite {$inserted} righe <br />
                Non inserite per errore {$not_inserted} righe <br />
                CF non trovati {$missing} <br />
                File logs disponibile qui: {$log_file}_*.log
                </pre>
HTML;

        }
        catch (Exception $e) {
            $db->transactionRollback();
            echo __FUNCTION__ . ' error: ' . $e->getMessage();
        }

        $app->close();

    }

    // importazione utenti da file xls / csv
    public function _import() {

        ini_set('max_execution_time', 0);

        $app = JFactory::getApplication();
        $db = JFactory::getDbo();

        try {

            // leggo il file di configurazione
            $config_file = JPATH_ROOT . '/tmp/_import.conf';

            // il file di configurazione non esiste
            if (!file_exists($config_file))
                throw new Exception("file _import.conf non trovato", 1);

            $config_content = file_get_contents($config_file);
            if (!$config_content)
                throw new Exception("file _import.conf non leggibile", 1);

            $json_config = utilityHelper::get_json_decode_error($config_content, true);
            if (!is_array($json_config))
                throw new Exception($json_config, 1);

            // target file
            $file_ext = isset($json_config['file_type']) ? $json_config['file_type'] : null;
            $target_file = isset($json_config['file_name']) ? JPATH_ROOT . '/tmp/' . $json_config['file_name'] : null;

            if (is_null($target_file))
                throw new Exception("Nessun file di importazione definito", 1);

            // il file da leggere non esiste
            if (!file_exists($target_file))
                throw new Exception($target_file . " non trovato", 1);

            $log_file = JPATH_ROOT . '/tmp/_log_' . $json_config['file_name'] . '_' . time();
            $reader = null;

            switch ($file_ext) {
                case 'csv':
                    $reader = ReaderEntityFactory::createCSVReader();
                    break;

                case 'xlsx':
                    $reader = ReaderEntityFactory::createXLSXReader();
                    break;

                default:
                    $reader = ReaderEntityFactory::createXLSXReader();
                    break;
            }

            if (is_null($reader))
                throw new Exception('Nessun formato file impostato');

            // non formatta le date in Date object restituendole in formato string
            $reader->setShouldFormatDates(true);

            // indice da cui partirà il loop sul foglio
            $numero_prima_riga = isset($json_config['cols_schema']['numero_prima_riga']) ? (int)$json_config['cols_schema']['numero_prima_riga'] : 0;

            $_users = null;
            if (isset($json_config['cols_schema']['users'])
                        && count($json_config['cols_schema']['users']) > 0)
                $_users = $json_config['cols_schema']['users'];

            $_profiler = null;
            if (isset($json_config['cols_schema']['profiler'])
                        && count($json_config['cols_schema']['profiler']) > 0)
                $_profiler = $json_config['cols_schema']['profiler'];

            $attiva_coupon = isset($json_config['cols_schema']['attiva_coupon']) ? (int)$json_config['cols_schema']['attiva_coupon'] : 0;

            $_groups = null;
            if (isset($json_config['cols_schema']['groups'])
                        && count($json_config['cols_schema']['groups']) > 0)
                $_groups = $json_config['cols_schema']['groups'];

            $_groups_map = null;
            if (isset($json_config['cols_schema']['groups_map'])
                        && count($json_config['cols_schema']['groups_map']) > 0)
                $_groups_map = $json_config['cols_schema']['groups_map'];

            $_test_users_exists = isset($json_config['cols_schema']['test_users_exists']) ? (int)$json_config['cols_schema']['test_users_exists'] : 0;

            // se la sezione users non è compilata non proseguo
            if (is_null($_users))
                throw new Exception("Nessuna configurazione per la sezione users", 1);

            $_registered_group = UtilityHelper::check_usergroups_by_name('Registered');
            if (is_null($_registered_group))
                throw new Exception("Nessun id disponibile per il gruppo Registered", 1);

            // tipo dei campi per effettuare un controllo sui valori prima dell'inserimento
            $cb_fields_type = UtilityHelper::get_comprofiler_fields_type();

            $reader->open($target_file); //open the file
            $import_report = array();
            $user = new gglmsModelUsers();

            $db->transactionStart();
            $i=0;
            if ($file_ext == 'xlsx'
                || $file_ext == 'xls') {

                // controllo quanti sheet ci sono nel file (> 1 vado in errore)
                $sheets_num = count($reader->getSheetIterator());
                if ($sheets_num > 1)
                    throw new Exception("Sheet multipli non supportati. Il foglio " . $file_ext . " deve contenere soltanto un foglio attivo", 1);

                $sheet_data = array();
                foreach ($reader->getSheetIterator() as $sheet) {
                    if ($sheet->getIndex() === 0) {
                        $sheet_data = $sheet->getRowIterator();
                        break;
                    }
                }

                $import_report['row_existing'] = (count($sheet_data)-$numero_prima_riga);

                foreach ($sheet_data as $row) {

                    $_new_user = array();
                    $_new_user_groups = array();
                    $_new_user_cp = array();

                    if ($i<$numero_prima_riga) {
                        $i++;
                        continue;
                    }

                    // do stuff with the row
                    $_riga_xls = $row->getCells();
                    foreach ($_riga_xls as $num_cell => $value_cell) {

                        $_multiple_emails = array();

                        // inserimento parte tabella users
                        foreach ($_users as $db_col => $xls_col) {

                            $_user_value = "";

                            if (strpos($xls_col, "_") !== false) {
                                $_arr_xls = explode("_", $xls_col);
                                foreach ($_arr_xls as $_sub_xls => $_sub_xls_col) {

                                    $col_index = Box\Spout\Reader\XLSX\Helper\CellHelper::getColumnIndexFromCellIndex($_sub_xls_col . $i);
                                    $_row_value = trim($_riga_xls[$col_index]);

                                    if ($_user_value != $_row_value)
                                        $_user_value .= ($_user_value != "") ? "/" . $_row_value : $_row_value;

                                }
                            }
                            else {
                                if ($db_col != "block") {
                                    $col_index = Box\Spout\Reader\XLSX\Helper\CellHelper::getColumnIndexFromCellIndex($xls_col . $i);
                                    $_user_value = addslashes(trim($_riga_xls[$col_index]));
                                }
                                else
                                    $_user_value = (int) trim($xls_col);
                            }

                            if ($db_col == "password")
                                $_user_value = JUserHelper::hashPassword($_user_value);
                            else if ($db_col == "email") {
                                // gestione di eventuali email multiple separate da virgole o punto e virgola
                                // le inserisco nel campo cb_altraemail di CP
                                $_arr_emails = array();
                                if (strpos($_user_value, ";") !== false) {
                                    $_arr_emails = explode(";", $_user_value);
                                }
                                else if (strpos($_user_value, ",") !== false) {
                                    $_arr_emails = explode(",", $_user_value);
                                }

                                // se ho indirizzi alternativi nella medesima colonna email
                                if (count($_arr_emails) > 0) {
                                    $email_index = 0;
                                    foreach ($_arr_emails as $email_key => $email_value) {

                                        if ($email_index == 0)
                                            $_user_value = $email_value;
                                        else {
                                            if ($email_value != "")
                                                $_multiple_emails[] = $email_value;
                                        }

                                        $email_index++;
                                    }
                                }
                            } else if ($db_col == "corso"){

                                $titolo_corso = $_user_value;
                                continue;
                            }

                            $_new_user[$db_col] = $_user_value;

                        }

                        // verifico l'esistenza delle colonne minimali per l'inserimento utente
                        $_test_users_fields = UtilityHelper::check_new_user_array($_new_user);
                        if ($_test_users_fields != "") {
                            // throw new Exception("Mancano i seguenti campi necessari per inserire un nuovo utente:" . $_test_users_fields);
                            UtilityHelper::write_file_to($log_file . '_missing_fields.log', "MISSING FIELDS: " . ($i+$numero_prima_riga) . ":" . $_new_user['username'] . " Mancano i seguenti campi necessari per inserire un nuovo utente:" . $_test_users_fields,true);
                            $import_report['missing_fields'][] = $_new_user['username'];
                            continue 2;
                        }

                        // controllo esistenza utente su username
                        if ($_test_users_exists) {
                            if (UtilityHelper::check_user_by_username($_new_user['username'])) {
                                UtilityHelper::write_file_to($log_file . '_existing.log', "EXISTING: " . ($i+$numero_prima_riga) . ":" . $_new_user['username'],true);
                                $import_report['existing'][] = $_new_user['username'];
                                continue 2;
                            }
                        }

                        // se impostati associazione usergroups e creazione se non esistenti
                        if (!is_null($_groups)) {

                            foreach ($_groups as $db_col => $xls_col) {

                                $col_index = Box\Spout\Reader\XLSX\Helper\CellHelper::getColumnIndexFromCellIndex($xls_col . $i);
                                $_group_value = $_riga_xls[$col_index];

                                // controllo esistenza gruppo
                                $_group_id = utilityHelper::check_usergroups_by_name($_group_value);
                                // creo se non esiste
                                if (is_null($_group_id)) {
                                    $_group_id = utilityHelper::insert_new_usergroups($_group_value, $_registered_group);
                                }

                                $_new_user_groups[] = $_group_id;
                            }

                        }

                        // se impostati associo l'utente a gruppi già esistenti
                        if (!is_null($_groups_map)) {

                            foreach ($_groups_map as $g_key => $_group_name) {
                                $_group_id = utilityHelper::check_usergroups_by_name($_group_name);

                                if($g_key == 1) {
                                    $id_societa = $user->get_societa_new_user($_group_name);
                                    $id_piattaforma = utilityHelper::check_usergroups_by_name($_group_name);
                                    $_group_id = $id_societa->id;
                                }
                                $_new_user_groups[] = $_group_id;
                            }

                            $group_corso = utilityHelper::check_usergroups_by_name($titolo_corso);
                            $_new_user_groups[] = $group_corso;

                        }

                        // se non ho gruppi associati all'utente imposto almeno quello registered
                        if (count($_new_user_groups) == 0) {
                            $_new_user_groups[] = $_registered_group;
                        }
                        // $_new_user['groups'] = $_new_user_groups;

                        // creazione utente
                        /*
                        $user = new JUser;
                        $user->bind($_new_user);

                        if (!$user->save()) {
                        */

                        $_user_insert_query = UtilityHelper::get_insert_query("users", $_new_user);
                        $_user_insert_query_result = UtilityHelper::insert_new_with_query($_user_insert_query);

                        if (!is_array($_user_insert_query_result)) {
                            UtilityHelper::write_file_to($log_file . '_not_inserted.log', "NOT INSERTED: " . ($i+$numero_prima_riga) . ":" . $_new_user['username'] . " -> " . $_user_insert_query_result,true);
                            $import_report['not_inserted'][] = $_new_user['username'];
                            continue 2;
                        }

                        //$_new_user_id = $user->id;
                        $_new_user_id = $_user_insert_query_result['success'];

                        // associo utente a gruppi
                        JUserHelper::setUserGroups($_new_user_id, $_new_user_groups);

                        UtilityHelper::write_file_to($log_file . '_inserted.log', "INSERTED: " . $_new_user_id . ":" . $_new_user['username'],true);
                        $import_report['inserted'][] = $_new_user['username'];

                        // se non valorizzato non compilo CP
                        if (is_null($_profiler)) {
                            $db->transactionCommit();
                            continue 2;
                        }

                        // profiler
                        foreach ($_profiler as $db_col => $xls_col) {

                            $_group_value_cp = "";
                            if (strpos($xls_col, "_") !== false) {
                                $_arr_xls = explode("_", $xls_col);
                                foreach ($_arr_xls as $_sub_xls => $_sub_xls_col) {
                                    $col_index = Box\Spout\Reader\XLSX\Helper\CellHelper::getColumnIndexFromCellIndex($_sub_xls_col . $i);
                                    $_row_value = trim($_riga_xls[$col_index]);

                                    if ($_group_value_cp != $_row_value)
                                        $_group_value_cp .= ($_group_value_cp != "") ? "/" . $_row_value : $_row_value;
                                }
                            }
                            else {

                                $col_index = Box\Spout\Reader\XLSX\Helper\CellHelper::getColumnIndexFromCellIndex($xls_col . $i);
                                $_group_value_cp = trim($_riga_xls[$col_index]);

                            }

                            // controllo e modifico il campo in base alla natura
                            if (isset($cb_fields_type[$db_col])) {

                                if (
                                    ($cb_fields_type[$db_col] == "text"
                                        || $cb_fields_type[$db_col] == "select")
                                    && $_group_value_cp != ""
                                ){

                                    // rimuovo eventuali (SV) da nome città
                                    if ($db_col == 'cb_luogodinascita'
                                        || $db_col == 'cb_citta')
                                        $_group_value_cp = preg_replace('/\s*\([^)]*\)/', '', $_group_value_cp);

                                    // piccola normalizzazione
                                    if ($db_col == 'titolo') {

                                        if ($_group_value_cp == 'Dr.ssa')
                                            $_group_value_cp = 'Dott.ssa';

                                    }

                                    if ($db_col == 'cb_altraemail'
                                        && count($_multiple_emails) > 0) {
                                        $_group_value_cp .= ";" . implode(";", $_multiple_emails);
                                    }

                                    $_group_value_cp = addslashes($_group_value_cp);
                                }
                                else if ($cb_fields_type[$db_col] == "date"
                                    && $_group_value_cp != "") {
                                    $_date = DateTime::createFromFormat('d/m/Y', $_group_value_cp);
                                    $_group_value_cp = $_date->format('Y-m-d');
                                }
                                else if ($cb_fields_type[$db_col] == "checkbox") {
                                    $_group_value_cp = (strtolower($_group_value_cp) == "true" || $_group_value_cp == "1") ? 1 : 0;
                                }

                            }
                            else if ($db_col == "firstname"
                                || $db_col == "lastname")
                                $_group_value_cp = addslashes($_group_value_cp);

                            $_new_user_cp[$db_col] = $_group_value_cp;

                        }

                        // riferimento id per CP
                        $_new_user_cp['id'] = $_new_user_id;
                        $_new_user_cp['user_id'] = $_new_user_id;

                        // inserimento utente in CP
                        $_cp_insert_query = UtilityHelper::get_insert_query("comprofiler", $_new_user_cp);
                        $_cp_insert_query_result = UtilityHelper::insert_new_with_query($_cp_insert_query);
                        if (!is_array($_cp_insert_query_result))
                            throw new Exception(print_r($_new_user_cp, true) . " errore durante inserimento", 1);

                        UtilityHelper::write_file_to($log_file . '_inserted_cp.log', "INSERTED CP: " . $_new_user_id . ":" . $_new_user['username'],true);
                        $import_report['inserted_cp'][] = $_new_user['username'];


                        if ($attiva_coupon) {

                            $data = array();
                            $_config = new gglmsModelConfig();
                            $_model_coupon = new gglmsModelgeneracoupon();

                            $data['attestato'] = 1;
                            $data['id_utente'] = $_new_user_id;
                            $data['gruppo'] = 16;
                            $data['creation_time'] = date('Y-m-d H:i:s', time());
                            $data['data_utilizzo'] = date('Y-m-d H:i:s', time());
                            $data["durata"] = $_config->getConfigValue('durata_standard_coupon');
                            $data['abilitato'] = 1;
                            $data['stampatracciato'] = 0;
                            $data['trial'] = 0;
                            $data['id_societa'] = $id_societa->id;
                            $data['id_gruppi'] = $group_corso;
                            $data['ref_skill'] = 'NULL';
                            $data['id_iscrizione'] = $_model_coupon->_generate_id_iscrizione($id_piattaforma);
                            $data['data_abilitazione'] = date('Y-m-d H:i:s', time());

                            $info_corso = $_model_coupon->get_info_corso($group_corso);
                            $prefisso_coupon = $info_corso["prefisso_coupon"];

                            $nome_societa = (string)$_groups_map[1];

                            $data['coupon'] = $_model_coupon->_generate_coupon($prefisso_coupon, $nome_societa);

                            $_coupon_insert_query = UtilityHelper::get_insert_query("gg_coupon", $data);
                            $_coupon_insert_query_result = UtilityHelper::insert_new_with_query($_coupon_insert_query);
                            if (!is_array($_coupon_insert_query_result))
                                throw new Exception(print_r($data, true) . " errore durante inserimento coupon", 1);


                            UtilityHelper::write_file_to($log_file . '_inserted_coupon.log', "INSERTED COUPON: " . $_new_user_id . " : " . $data['coupon'],true);
                            $import_report['inserted_coupon'][] = $data['coupon'];


                        }

                        $db->transactionCommit();


                        continue 2;

                    }

                    $i++;
                }

            }

            $row_existing = count($import_report['row_existing']);
            $inserted = count($import_report['inserted']);
            $inserted_cp = count($import_report['inserted_cp']);
            $inserted_coupon = count($import_report['inserted_coupon']);
            $not_inserted = count($import_report['not_inserted']);
            $missing_fields = count($import_report['missing_fields']);
            $existing = count($import_report['existing']);
            $_finish_date = date('d/m/Y H:i:s');

            echo <<<HTML
                <pre>
                {$_finish_date} <br />
                Importazione terminata! <br />
                Righe totali {$row_existing} <br />
                Inserite users {$inserted} righe <br />
                Inserite profiler {$inserted_cp} righe <br />
                Inserite coupon {$inserted_coupon} righe <br />
                Non inserite per campi non conformi (es. email non valida) {$not_inserted} righe <br />
                Non inserite per campi necessari mancanti {$missing_fields} righe <br />
                Non inserite perchè già esistenti {$existing} righe <br />
                File logs disponibile qui: {$log_file}_*.log
                </pre>
HTML;

        }
        catch (Exception $e) {
            $db->transactionRollback();
            echo __FUNCTION__ . ' error: ' . $e->getMessage();
        }

        $app->close();
    }

    public function _import_quote_sinpe() {

        ini_set('max_execution_time', 0);

        $app = JFactory::getApplication();
        $db = JFactory::getDbo();

        try {

            // leggo il file di configurazione
            $config_file = JPATH_ROOT . '/tmp/_import_sinpe.conf';

            // il file di configurazione non esiste
            if (!file_exists($config_file))
                throw new Exception("file _import.conf non trovato", 1);

            $config_content = file_get_contents($config_file);
            if (!$config_content)
                throw new Exception("file _import.conf non leggibile", 1);

            $json_config = utilityHelper::get_json_decode_error($config_content, true);
            if (!is_array($json_config))
                throw new Exception($json_config, 1);

            // target file
            $file_ext = isset($json_config['file_type']) ? $json_config['file_type'] : null;
            $target_file = isset($json_config['file_name']) ? JPATH_ROOT . '/tmp/' . $json_config['file_name'] : null;

            if (is_null($target_file))
                throw new Exception("Nessun file di importazione definito", 1);

            // il file da leggere non esiste
            if (!file_exists($target_file))
                throw new Exception($target_file . " non trovato", 1);

            $log_file = JPATH_ROOT . '/tmp/_log_' . $json_config['file_name'] . '_' . date('YmdHis');
            $reader = ReaderEntityFactory::createXLSXReader();

            // indice da cui partirà il loop sul foglio
            $numero_prima_riga = isset($json_config['cols_schema']['numero_prima_riga']) ? (int)$json_config['cols_schema']['numero_prima_riga'] : 0;

            $_users_quote = null;
            if (isset($json_config['cols_schema']['quote'])
                && count($json_config['cols_schema']['quote']) > 0)
                $_users_quote = $json_config['cols_schema']['quote'];

            $_users_espen = null;
            if (isset($json_config['cols_schema']['espen'])
                && count($json_config['cols_schema']['espen']) > 0)
                $_users_espen = $json_config['cols_schema']['espen'];

            // se la sezione users non è compilata non proseguo
            if (is_null($_users_quote))
                throw new Exception("Nessuna configurazione per la sezione users_quote", 1);

            if (is_null($_users_espen))
                throw new Exception("Nessuna configurazione per la sezione users_espen", 1);

            $reader->open($target_file); //open the file
            $log_file = JPATH_ROOT . '/tmp/_log_' . $json_config['file_name'] . '_' . time();

            $import_report = array();

            $db->transactionStart();

            // creo tabella che conterrà i riferimenti per le iscrizione degli anni passati
            $_create_table_iscrizioni = "CREATE TABLE IF NOT EXISTS `#__gg_quote_iscrizioni`
                                          (
                                              `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
                                              `user_id` INT(11) UNSIGNED NOT NULL ,
                                              `anno` INT(4) NOT NULL ,
                                              `tipo_quota` VARCHAR(20) NOT NULL ,
                                              `tipo_pagamento` VARCHAR(50) NULL,
                                              `data_pagamento` DATETIME NULL,
                                              `totale` DECIMAL(6,2) NULL,
                                              `dettagli_transazione` TEXT NULL,
                                              PRIMARY KEY (`id`), INDEX (`user_id`)
                                          ) ENGINE = InnoDB;";

            $_create_table_iscrizioni_result = UtilityHelper::insert_new_with_query($_create_table_iscrizioni);
            if (!is_array($_create_table_iscrizioni_result)) {
                throw new Exception("Impossibile creare tabella quote: " . $_create_table_iscrizioni_result, 1);
            }

            $i=0;

            // controllo quanti sheet ci sono nel file (> 1 vado in errore)
            $sheets_num = count($reader->getSheetIterator());
            if ($sheets_num > 1)
                throw new Exception("Sheet multipli non supportati. Il foglio " . $file_ext . " deve contenere soltanto un foglio attivo", 1);

            $sheet_data = array();
            foreach ($reader->getSheetIterator() as $sheet) {
                if ($sheet->getIndex() === 0) {
                    $sheet_data = $sheet->getRowIterator();
                    break;
                }
            }

            foreach ($sheet_data as $row) {

                if ($i<$numero_prima_riga) {
                    $i++;
                    continue;
                }

                // do stuff with the row
                $_insert_quote_users = "INSERT INTO #__gg_quote_iscrizioni
                                              (
                                                user_id,
                                                anno,
                                                tipo_quota
                                              )
                                              VALUES ";
                $_riga_xls = $row->getCells();

                foreach ($_riga_xls as $num_cell => $value_cell) {

                    $_row_quota = array();
                    $_user_id = null;

                    // inserimento parte quote per utente
                    foreach ($_users_quote as $db_col => $xls_col) {

                        if ($db_col == "id") {
                            $col_index = Box\Spout\Reader\XLSX\Helper\CellHelper::getColumnIndexFromCellIndex($xls_col . $i);
                            $_user_id = trim($_riga_xls[$col_index]);
                        }
                        else {

                            $_user_anno = (int) trim($db_col);

                            $col_index = Box\Spout\Reader\XLSX\Helper\CellHelper::getColumnIndexFromCellIndex($xls_col . $i);
                            $_user_value = trim($_riga_xls[$col_index]);

                            if ($_user_value > 0) {
                                $_row_quota['quota'][] = $_user_anno;
                            }
                        }

                    }

                    // inserimento parte espen per utente
                    foreach ($_users_espen as $db_col => $xls_col) {

                        if ($db_col == "id") {
                            $col_index = Box\Spout\Reader\XLSX\Helper\CellHelper::getColumnIndexFromCellIndex($xls_col . $i);
                            $_user_id = trim($_riga_xls[$col_index]);
                        }
                        else {

                            $_user_anno = (int) trim($db_col);

                            $col_index = Box\Spout\Reader\XLSX\Helper\CellHelper::getColumnIndexFromCellIndex($xls_col . $i);
                            $_user_value = trim($_riga_xls[$col_index]);

                            if ($_user_value > 0) {
                                $_row_quota['espen'][] = $_user_anno;
                            }
                        }

                    }

                    // tutto a zero nulla da inserire
                    if (count($_row_quota) == 0)
                        continue 2;

                    // inserimento quote per utente
                    foreach ($_row_quota as $tipo => $sub_tipo) {

                        foreach ($sub_tipo as $sub_anno => $anno) {

                            $_insert_quote_users .= " ('" . $_user_id . "', '" . $anno . "', '" . $tipo . "'), ";

                        }

                    }

                    // esecuzione query inserimento
                    $_insert_quote_users = rtrim(trim($_insert_quote_users), ",");
                    $_insert_quote_users_result = UtilityHelper::insert_new_with_query($_insert_quote_users);
                    if (!is_array($_insert_quote_users_result)) {
                        $import_report['not_inserted'][] = $_user_id;
                        UtilityHelper::write_file_to($log_file . '_not_inserted.log', $_insert_quote_users . " errore durante inserimento: " . $_insert_quote_users_result,true);
                        continue 2;
                    }

                    $import_report['inserted'][] = $_user_id;
                    UtilityHelper::write_file_to($log_file . '_inserted.log', "INSERTED: " . $_user_id . " -> " . print_r($_row_quota, true),true);

                    $db->transactionCommit();

                    continue 2;

                }

            }

            $inserted = count($import_report['inserted']);
            $not_inserted = count($import_report['not_inserted']);
            $_finish_date = date('d/m/Y H:i:s');

            echo <<<HTML
                <pre>
                {$_finish_date} <br />
                Importazione terminata! <br />
                Inserite {$inserted} righe <br />
                Non inserite per campi non conformi (es. email non valida) {$not_inserted} righe <br />
                File logs disponibile qui: {$log_file}_*.log
                </pre>
HTML;
        }
        catch (Exception $e) {
            $db->transactionRollback();
            echo __FUNCTION__ . ' error: ' . $e->getMessage();
        }

        $app->close();
    }

    public function conferma_pagamento_bonifico_asand() {

        $app = JFactory::getApplication();
        $_ret = array();

        try {

            $params = JRequest::get($_GET);
            $id_quota = $params['id_quota'];

            if (!isset($id_quota)
                || $id_quota == ""
                || !is_numeric($id_quota))
                throw new Exception("Missing quota id", E_USER_ERROR);

            // controllo se la quota esiste
            $userModel = new gglmsModelUsers();
            $checkQuota = $userModel->get_quota_per_id($id_quota);

            if (is_null($checkQuota))
                throw new Exception("Nessun riferimento alla quota trovato", E_USER_ERROR);

            $userId = $checkQuota['user_id'];
            $annoQuota = $checkQuota['anno'];
            $userGroupId = $checkQuota['gruppo_corso'];

            $checkUser = $userModel->get_user_joomla($userId);
            if (is_null($checkUser)
                || !isset($checkUser->id)) {
                $this->show_view = true;
                throw new Exception("Nessun utente trovato", E_USER_ERROR);
            }

            // sblocco l'utente
            $updateUser = $userModel->update_user_column($userId, "block", 0);

            if (is_null($updateUser))
                throw new Exception("Errore durante l'aggiornamento dell'utente", E_USER_ERROR);

            // inserisco l'utente nel gruppo quota di riferimento
            $insert_ug = $userModel->insert_user_into_usergroup($userId, $userGroupId);
            if (is_null($insert_ug))
                throw new Exception("Inserimento utente in gruppo corso fallito: " . $userId . ", " . $userGroupId, E_USER_ERROR);

            // aggiorno ultimo anno pagato
            $_ultimo_anno = $userModel->update_ultimo_anno_pagato($userId, $annoQuota);
            if (!is_array($_ultimo_anno))
                throw new Exception($_ultimo_anno, E_USER_ERROR);

            // aggiorno lo stato del pagamento
            $updateQuota = $userModel->update_user_column($id_quota, "stato", 1, "gg_quote_iscrizioni");
            if (is_null($updateQuota))
                throw new Exception("Errore durante l'aggiornamento della quota", E_USER_ERROR);

            $_params = utilityHelper::get_params_from_plugin('cb.checksociasand');
            $dettagliUtente = $userModel->get_user_full_details_cb($userId);

            // l'integrazione dei campi extra al momento è soltanto per community builder
            $_config = new gglmsModelConfig();
            $dettagliUtente['nome_utente'] = $dettagliUtente[$_config->getConfigValue('campo_community_builder_nome')];
            $dettagliUtente['cognome_utente'] = $dettagliUtente[$_config->getConfigValue('campo_community_builder_cognome')];
            $dettagliUtente['codice_fiscale'] = $dettagliUtente[$_config->getConfigValue('campo_community_builder_controllo_cf')];
            $dettagliUtente['email'] = $checkUser->email;
            $dettagliUtente['mail_from'] = utilityHelper::get_ug_from_object($_params, 'email_from');

            $sendEmail = utilityHelper::send_acquisto_evento_email($checkUser->email,
                                                                    "",
                                                                    $dettagliUtente,
                                                                    $checkQuota['totale'],
                                                                    $checkQuota['data_pagamento'],
                                                                    "bb_buy_confirm_asand",
                                                                    $dettagliUtente['mail_from'],
                                                                    true,
                                                                    $id_quota);

            $_ret['success'] = "tuttook";

        }
        catch(Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        echo json_encode($_ret);
        $app->close();

    }

    public function inserisci_pagamento_extra() {

        $app = JFactory::getApplication();
        $_ret = array();

        try {

            $params = JRequest::get($_GET);
            $user_id = $params["user_id"];
            $totale = $params["totale"];
            $tipo_quota = $params["tipo_quota"];

            if (!isset($user_id)
                || $user_id == "")
                throw new Exception("Missing user id", E_USER_ERROR);

            if (!isset($totale)
                || $totale == ""
                || $totale == 0)
                throw new Exception("Missing totale", E_USER_ERROR);

            if (!isset($tipo_quota)
                || $tipo_quota == "")
                throw new Exception("Missing service type", E_USER_ERROR);

            $dt = new DateTime();
            $_anno_quota = $dt->format('Y');

            $_user = new gglmsModelUsers();

            $_bonifico = $_user->insert_user_quote_anno_bonifico($user_id,
                                                                $_anno_quota,
                                                                $totale,
                                                                "",
                                                                null,
                                                                null,
                                                                $tipo_quota,
                                                                true);

            if (!is_array($_bonifico))
                throw new Exception($_bonifico, E_USER_ERROR);

            $_ret['success'] = "tuttook";

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        echo json_encode($_ret);
        $app->close();

    }

    public function inserisci_pagamento_moroso() {

        $app = JFactory::getApplication();
        $_ret = array();

        try {

            $params = JRequest::get($_GET);
            $user_id = $params["user_id"];
            $totale = $params["totale"];

            if (!isset($user_id)
                || $user_id == "")
                throw new Exception("Missing user id", E_USER_ERROR);

            if (!isset($totale)
                || $totale == ""
                || $totale == 0)
                throw new Exception("Missing totale", E_USER_ERROR);

            $dt = new DateTime();
            $_anno_quota = $dt->format('Y');

            $_user = new gglmsModelUsers();

            $_bonifico = $_user->insert_user_quote_anno_bonifico($user_id,
                                                                $_anno_quota,
                                                                $totale,
                                                                "",
                                                                null,
                                                                null,
                                                                null,
                                                                true);

            $log_arr = array(
                'user_id' => $user_id,
                'bonifico' => $_bonifico

            );

            utilityHelper::make_debug_log(__FUNCTION__, print_r($log_arr, true), __FUNCTION__);

            if (!is_array($_bonifico)) throw new Exception($_bonifico, E_USER_ERROR);

            /*
            $_user_details = $_user->get_user_details_cb($user_id);
            if (!is_array($_user_details)) throw new Exception($_user_details, E_USER_ERROR);
            
            $email_default = utilityHelper::get_params_from_object(utilityHelper::get_params_from_plugin(), "email_default");
            $selectedUser = $_user->get_user_joomla($user_id);
            if (isset($selectedUser->email) && $selectedUser->email != '') {
                utilityHelper::send_sinpe_email_pp($email_default,
                                                date('Y-m-d'),
                                                "",
                                                "",
                                                $_user_details,
                                                0,
                                                0,
                                                'conferma_bonifico_sinpe',
                                                $selectedUser->email);
            }
            */

            $_ret['success'] = "tuttook";

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        echo json_encode($_ret);
        $app->close();
    }

    public function conferma_acquisto_evento() {

        $app = JFactory::getApplication();
        $_ret = array();

        try {

            $params = JRequest::get($_GET);
            $id_pagamento = $params["id_pagamento"];
            $user_id = $params["user_id"];
            $gruppo_corso = $params["gruppo_corso"];

            // parametri dal plugin di gestione acquisto corsi
            $_params_module = UtilityHelper::get_params_from_module();
            $ug_conferma_acquisto = UtilityHelper::get_ug_from_object($_params_module, "ug_conferma_acquisto", true);

            if (!isset($id_pagamento)
                || $id_pagamento == "")
                throw new Exception("Missing payment id", 1);

            if (!isset($user_id)
                || $user_id == "")
                throw new Exception("Missing user id", 1);

            if (!isset($gruppo_corso)
                || $gruppo_corso == "")
                throw new Exception("Missing user group", 1);

            // l'utente va inserito nel gruppo corso
            $_check_add = UtilityHelper::set_usergroup_generic($user_id, $gruppo_corso);
            if (!is_array($_check_add))
                throw new Exception($_check_add, 1);

            // l'utente va rimosso dal gruppo dell'acquisto sospeso
            $_check_remove = UtilityHelper::remote_usergroup_generic($user_id, $ug_conferma_acquisto);
            if (!is_array($_check_remove))
                throw new Exception($_check_remove, 1);

            // aggiorno la quota di iscrizione impostando la tipologia
            // aggiorno ultimo anno pagato
            $_user = new gglmsModelUsers();
            $_check_conferma = $_user->update_tipo_quota_iscrizione($id_pagamento, "evento");
            if (!is_array($_check_conferma))
                throw new Exception($_check_conferma, 1);

            $_ret['success'] = "tuttook";

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        echo json_encode($_ret);
        $app->close();

    }

    public function get_utenti_per_corso() {

        $app = JFactory::getApplication();
        $_ret = array();

        try {

            $params = JRequest::get($_GET);
            $id_corso = $params["id_corso"];

            if (!isset($id_corso)
                || $id_corso == "")
                throw new Exception("Missing corso id", 1);

            // id utenti iscritti ad un corso
            $users_id_arr = UtilityHelper::get_user_iscritti_corso($id_corso);
            if (count($users_id_arr) == 0)
                throw new Exception("Nessun utente iscritto al corso selezionato", 1);

            $user_model = new gglmsModelUsers();
            $users = $user_model->get_utenti_iscritti_corso($id_corso, $users_id_arr);

            if (is_null($users)
                || count($users) == 0)
                throw new Exception("Nessuna anagrafica disponibile per il corso selezionato", 1);

            $_ret['success'] = $users;

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        echo json_encode($_ret);
        $app->close();
    }

    // sposto un socio decaduto nel gruppo moroso
    public function riabilita_decaduto() {

        $app = JFactory::getApplication();
        $_ret = array();

        try {

            $params = JRequest::get($_GET);
            $user_id = $params["user_id"];
            $preiscritto = isset($params["preiscritto"]) ? 
                (int) $params["preiscritto"]
                : 0;

            if (!isset($user_id)
                || $user_id == "") throw new Exception("Missing user id", E_USER_ERROR);

            $dt = new DateTime();
            // non anno corrente perchè da moroso deve pagare partendo dall'ultimo anno
            $_anno_quota = $dt->format('Y')-1;

            $_params = utilityHelper::get_params_from_plugin();
            $gruppi_online = utilityHelper::get_ug_from_object($_params, "ug_online");
            $gruppi_moroso = utilityHelper::get_ug_from_object($_params, "ug_moroso");
            $gruppi_decaduto = utilityHelper::get_ug_from_object($_params, "ug_decaduto");

            // inserisco utente nel gruppo moroso
            $_check = utilityHelper::set_usergroup_moroso($user_id, $gruppi_online, $gruppi_moroso, $gruppi_decaduto);
            if (!is_array($_check)) throw new Exception($_check, E_USER_ERROR);

            // aggiorno ultimo anno pagato
            $_user = new gglmsModelUsers();
            $_ultimo_anno = $_user->update_ultimo_anno_pagato($user_id, $_anno_quota);
            if (!is_array($_ultimo_anno)) throw new Exception($_ultimo_anno, E_USER_ERROR);

            // se preiscritto devo inviare email per riabilitazione account
            if ($preiscritto == 1) {
                $_user_details = $_user->get_user_details_cb($user_id);
                $email_default = utilityHelper::get_params_from_object($_params, "email_default");
                $selectedUser = $_user->get_user_joomla($user_id);
                if (isset($selectedUser->email) && $selectedUser->email != '') {
                    utilityHelper::send_sinpe_email_pp($email_default,
                                                        date('Y-m-d'),
                                                        "",
                                                        "",
                                                        $_user_details,
                                                        0,
                                                        0,
                                                        'preiscritto',
                                                        $selectedUser->email);
                }
            }

            $_ret['success'] = "tuttook";

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        echo json_encode($_ret);
        $app->close();

    }

    public function get_quote_iscrizione() {

        $app = JFactory::getApplication();
        $_rows = array();
        $_ret = array();
        $_total_rows = 0;

        try {

            $_call_params = JRequest::get($_GET);
            $_search = (isset($_call_params['search']) && $_call_params['search'] != "") ? $_call_params['search'] : null;
            $_offset = (isset($_call_params['offset']) && $_call_params['offset'] != "") ? $_call_params['offset'] : 0;
            $_limit = (isset($_call_params['limit']) && $_call_params['limit'] != "") ? $_call_params['limit'] : 10;
            $_sort = (isset($_call_params['sort']) && $_call_params['sort'] != "") ? $_call_params['sort'] : null;
            $_order = (isset($_call_params['order']) && $_call_params['order'] != "") ? $_call_params['order'] : null;

            $_current_user = JFactory::getUser();
            $_user_id = ($_current_user->authorise('core.admin')) ? null : $_current_user->id;
            $this->user_id = $_user_id;

            // parametri dal plugin di gestione acquisto corsi
            $_params_module = UtilityHelper::get_params_from_module();
            $gruppo_conferma_acquisto = UtilityHelper::get_ug_from_object($_params_module, "ug_conferma_acquisto", true);

            $_user = new gglmsModelUsers();
            $_quote = $this->quote_iscrizione = $_user->get_quote_iscrizione($_user_id,
                                                                            $_offset,
                                                                            $_limit,
                                                                            $_search,
                                                                            $_sort,
                                                                            $_order,
                                                                            $gruppo_conferma_acquisto);

            $_label_conferma_acquisto = JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR31');
            $_label_conferma_acquisto_user = JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR33');

            if (isset($_quote['rows'])) {

                $_total_rows = $_quote['total_rows'];

                foreach ($_quote['rows'] as $_key_quota => $_quota) {

                    $_fab_pagamento = "";
                    /*$_icon_check = <<<HTML
                            <i class="fas fa-check"></i>
HTML;*/
                    $_icon_check = "OK";
                    $payment_ko = false;
                    foreach ($_quota as $key => $value) {

                        $_icon_tipo_quota = "";

                        if ($key == "tipo_quota") {

                            $_tipo_quota = (!is_null($value)) ? strtoupper($value) : "";
                            $_tipo_pagamento = (!is_null($_quota['tipo_pagamento'])) ? strtolower($_quota['tipo_pagamento']) : "";

                            //$_fab_pagamento = ($_tipo_pagamento == "paypal") ? "fab fa-paypal" : "fas fa-university";
                            $_fab_pagamento = strtoupper($_tipo_pagamento);

                            if (is_null($_tipo_pagamento)
                                || $_tipo_pagamento == "") {
                                //$_fab_pagamento = "fas fa-dollar-sign";
                                $_fab_pagamento = "ARCHIVIATO";
                            }

                            if ($_tipo_quota == "EVENTO_NC") {

                                if (is_null($_user_id))
                                    $value = <<<HTML
                                    <a href="javascript:" class="btn btn-info" style="min-height: 50px;" onclick="confermaAcquistaEvento({$_quota['id_pagamento']}, {$_quota['user_id']}, {$_quota['gruppo_corso']})">{$_label_conferma_acquisto} {$_quota['titolo_corso']}</a>
HTML;
                                else
                                    $value = $_label_conferma_acquisto_user . ' ' . $_quota['titolo_corso'];

                                $payment_ko = true;
                            }
                            else if ($_tipo_quota == "EVENTO") {
                                $value = $_quota['titolo_corso'];
                            }

                            /*$_icon_tipo_quota = <<<HTML
                                    <i class="{$_fab_pagamento}"></i>
HTML;*/
                            $_icon_tipo_quota = $_fab_pagamento;
                            $_ret[$_key_quota]['icon_pagamento'] = trim($_icon_tipo_quota);

                        }

                        if ($payment_ko) {
                            /*$_icon_check = <<<HTML
                                <i class="fas fa-times"></i>
HTML;*/
                            $_icon_check = "NO";

                        }

                        $_ret[$_key_quota]['check_pagamento'] = trim($_icon_check);
                        $_ret[$_key_quota][$key] = $value;

                    }

                }

            }

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        $_rows['rows'] = $_ret;
        $_rows['total_rows'] = $_total_rows;

        echo json_encode($_rows);
        $app->close();

    }

    public function get_quote_asand() {

        $app = JFactory::getApplication();
        $_rows = array();
        $_ret = array();
        $_total_rows = 0;

        try {

            $_call_params = JRequest::get($_GET);
            $_search = (isset($_call_params['search']) && $_call_params['search'] != "") ? $_call_params['search'] : null;
            $_offset = (isset($_call_params['offset']) && $_call_params['offset'] != "") ? $_call_params['offset'] : 0;
            $_limit = (isset($_call_params['limit']) && $_call_params['limit'] != "") ? $_call_params['limit'] : 10;
            $_sort = (isset($_call_params['sort']) && $_call_params['sort'] != "") ? $_call_params['sort'] : null;
            $_order = (isset($_call_params['order']) && $_call_params['order'] != "") ? $_call_params['order'] : null;
            $_tipo_quota = (isset($_call_params['tipo_quota']) && $_call_params['tipo_quota'] != "") ? $_call_params['tipo_quota'] : null;
            $_tipo_pagamento = (isset($_call_params['tipo_pagamento']) && $_call_params['tipo_pagamento'] != "") ? $_call_params['tipo_pagamento'] : null;
            $_stato_pagamento = (isset($_call_params['stato_pagamento']) && $_call_params['stato_pagamento'] != "") ? $_call_params['stato_pagamento'] : null;

            $_user = new gglmsModelUsers();
            $_label_paga = JText::_('COM_REGISTRAZIONE_ASAND_STR13');
            $_label_pagato = JText::_('COM_REGISTRAZIONE_ASAND_STR16');
            $_label_nonpagato = JText::_('COM_REGISTRAZIONE_ASAND_STR17');

            $_azione_btn = "";

            $_soci = $_user->get_quote_asand($_tipo_quota, $_tipo_pagamento, $_stato_pagamento, $_offset, $_limit, $_search, $_sort, $_order);

            if (isset($_soci['rows'])) {

                $_total_rows = $_soci['total_rows'];

                foreach ($_soci['rows'] as $_key_socio => $_socio) {

                    foreach ($_socio as $key => $value) {

                        if ($key == "stato_pagamento" && $value == 0) {

                            $_azione_btn = '<a href="javascript:" class="btn btn-info" style="min-height: 50px;" onclick="impostaPagato(' . $_socio['id_quota'] . ')">' . $_label_paga . '</a>';
                            $_ret[$_key_socio]['tipo_azione'] = trim($_azione_btn);

                        }
                        else if ($key == "stato_pagamento2") {
                            $value = $value == 1
                                ? $_label_pagato
                                : $_label_nonpagato;
                        }
                        else if ($key == "data_pagamento") {
                            $dt = new DateTimeImmutable($value);
                            $value = $dt->format('d-m-Y H:i:s');
                        }
                        else if ($key == "codice_fiscale") {
                            $value = strtoupper($value);
                        }
                        else if ($key == "tipo_pagamento") {
                            $value = strtoupper($value);
                        }

                        $_ret[$_key_socio][$key] = $value;

                    }


                    unset($_ret[$_key_socio]['stato_pagamento']);

                }

            }

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        $_rows['rows'] = $_ret;
        $_rows['total_rows'] = $_total_rows;

        echo json_encode($_rows);
        $app->close();
    }

    public function get_soci_iscritti() {

        $app = JFactory::getApplication();
        $_rows = array();
        $_ret = array();
        $_total_rows = 0;

        try {

            $_call_params = JRequest::get($_GET);
            $_search = (isset($_call_params['search']) && $_call_params['search'] != "") ? $_call_params['search'] : null;
            $_offset = (isset($_call_params['offset']) && $_call_params['offset'] != "") ? $_call_params['offset'] : 0;
            $_limit = (isset($_call_params['limit']) && $_call_params['limit'] != "") ? $_call_params['limit'] : 10;
            $_sort = (isset($_call_params['sort']) && $_call_params['sort'] != "") ? $_call_params['sort'] : null;
            $_order = (isset($_call_params['order']) && $_call_params['order'] != "") ? $_call_params['order'] : null;
            $_tipo_socio = (isset($_call_params['tipo_socio']) && $_call_params['tipo_socio'] != "") ? $_call_params['tipo_socio'] : null;

            $_user = new gglmsModelUsers();
            $_plugin_params = utilityHelper::get_params_from_plugin();
            $gruppi_online = utilityHelper::get_ug_from_object($_plugin_params, "ug_online");
            $gruppi_decaduto = utilityHelper::get_ug_from_object($_plugin_params, "ug_decaduto");
            $gruppi_moroso = utilityHelper::get_ug_from_object($_plugin_params, "ug_moroso");
            $_plugin_params_2 = utilityHelper::get_params_from_plugin("cb.cbsetgroup");
            $gruppi_preiscritto = utilityHelper::get_ug_from_object($_plugin_params_2, "ug_destinazione");

            $_label_attiva = JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR11');
            $_label_paga = JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR20');
            $_label_iscrivi = JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR26');
            $_label_extra = JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR36');
            $_ref_ug = "";

            // se non specificato carico online
            if (is_null($_tipo_socio))
                $_ref_ug .= ($gruppi_online != "") ? (($_ref_ug != "") ? "," . $gruppi_online : $gruppi_online) : "";
            else
                $_ref_ug = $_tipo_socio;

            $_soci = $_user->get_soci_iscritti($_ref_ug, $_offset, $_limit, $_search, $_sort, $_order);

            if (isset($_soci['rows'])) {

                $_total_rows = $_soci['total_rows'];

                foreach ($_soci['rows'] as $_key_socio => $_socio) {

                    foreach ($_socio as $key => $value) {

                        if ($key == "id_group") {

                            $_azione_btn = "";
                            if (in_array($value, explode(",", $gruppi_decaduto)))
                                $_azione_btn = '<a href="javascript:" class="btn btn-info" style="min-height: 50px;" onclick="riabilitaDecaduto(' . $_socio['user_id'] . ')">' . $_label_attiva . '</a>';
                            else if (in_array($value, explode(",", $gruppi_moroso)))
                                $_azione_btn = '<a href="javascript:" class="btn btn-info" style="min-height: 50px;" onclick="impostaPagato(' . $_socio['user_id'] . ')">' . $_label_paga . '</a>';
                            else if (in_array($value, explode(",", $gruppi_preiscritto)))
                                $_azione_btn = '<a href="javascript:" class="btn btn-info" style="min-height: 50px;" onclick="impostaMoroso(' . $_socio['user_id'] . ')">' . $_label_iscrivi . '</a>';
                            else if (in_array($value, explode(",", $gruppi_online)))
                                $_azione_btn = '<a href="javascript:" class="btn btn-info" style="min-height: 50px;" onclick="impostaPagamentoExtra(' . $_socio['user_id'] . ')">' . $_label_extra . '</a>';

                            $_ret[$_key_socio]['tipo_azione'] = trim($_azione_btn);
                        }
                        else if ($key == "sinpe_dep") {
                            $value = ($value == 1) ? 'SI' : 'NO';    
                        }

                        $_ret[$_key_socio][$key] = $value;
                    }

                    unset($_ret[$_key_socio]['id_group']);
                }
            }

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        $_rows['rows'] = $_ret;
        $_rows['total_rows'] = $_total_rows;

        echo json_encode($_rows);
        $app->close();
    }

    public function esegui_reset_password() {

        $app = JFactory::getApplication();
        $_ret = [];

        try {

            $id_utente = $_REQUEST['id_utente'];
            $gruppo_azienda = $_REQUEST['gruppo_azienda'];

            if (!isset($id_utente)
                || $id_utente == "")
                throw new Exception("Identificativo utente non valorizzato", E_USER_ERROR);

            if (!isset($gruppo_azienda)
                || $gruppo_azienda == "")
                throw new Exception("Gruppo azienda non valorizzato", E_USER_ERROR);

            // verifico se l'utente esiste
            $user_model = new gglmsModelUsers();
            $_current_user = $user_model->get_user($id_utente);
            if (!isset($_current_user->id)
                || $_current_user->id == "")
                throw new Exception("Nessun utente corrispondente a quello ricercato", E_USER_ERROR);

            // verifico il tutor aziendale
            $tutor_az = $user_model->get_tutor_aziendale_details($gruppo_azienda);
            if (!isset($tutor_az->id)
                || $tutor_az->id == "")
                throw new Exception("Nessun tutor aziendale trovato", E_USER_ERROR);

            // eseguo reset password su utente
            $update_password = $user_model->update_users_password($_current_user->id, $_current_user->username);
            if (is_null($update_password))
                throw new Exception("Aggiornamento password fallito", E_USER_ERROR);

            // invio email a tutor che certifica il buon esito dell'operazione
            $email_body = <<<HTML
                <p>La richiesta di reset password per l'utente <u>{$_current_user->username}</u> è andata a buon fine</p>
                <p>La nuova password è stata impostata a <b>{$_current_user->username}</b></p>
HTML;
            $email_send = utilityHelper::send_email("Richiesta reset password per utente " . $_current_user->username, $email_body, array($tutor_az->email));

            $_ret['success'] = 'tuttook';

        }
        catch(Exception $e) {
            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            $_ret['error'] = $e->getMessage();
        }

        echo json_encode($_ret);
        $app->close();

    }

    public function get_utenti_per_societa() {

        $_html = "";
        $_rows = array();
        $_ret = array();
        $_total_rows = 0;
        $boot_table = isset($_REQUEST['boot_table']) ? true : false;

        try {

            $aziende_ids = [];
            $user_model = new gglmsModelUsers();
            $ref_aziende = utilityHelper::getSocietaByUser();

            // nessun riferimento aziendale
            if (!is_array($ref_aziende)
                || count($ref_aziende) == 0) throw new Exception("Nessun azienda definita", E_USER_ERROR);

            foreach ($ref_aziende as $key_azienda => $azienda) {
                $aziende_ids[] = $azienda->id;
            }

            $users = $user_model->get_utenti_per_azienda($aziende_ids);
            if (is_null($users))
                throw new Exception("Nessun utente trovato", E_USER_ERROR);

            $_total_rows = $users['total_rows'];
            $Juser = JFactory::getUser();

            if($boot_table) {

                $app = JFactory::getApplication();
                $_total_rows = $_soci['total_rows'];

                foreach ($users['rows'] as $_key_user => $user) {

                    foreach ($user as $key => $value) {

                        $_ret[$_key_user][$key] = $value;

                    }
                    $action_btn = $Juser->id == $user['id_utente'] ? '' : "resetPassword('" . $user['id_utente']. "', '" . $user['gruppo_utente'] . "')";
                    $color_btn = $Juser->id == $user['id_utente'] ? '#ccc' : 'red';
                    $_ret[$_key_user]['azioni'] = <<<HTML
                    <span style="cursor: pointer; color:{$color_btn};" onclick="{$action_btn}"><i class="fas fa-times-circle fa-2x"></i></span>
HTML;

                }

            }
            else {

                // normalizzo array
                foreach ($users['rows'] as $_key_user => $user) {

                // in tabella non deve essere inserito l'utente corrente (caso tutor...)
                if ($Juser->id == $user['id_utente']) {
                    continue;
                }

                $_html .= <<<HTML
                <tr>
                    <td>{$user['username']}</td>
                    <td>
                        <a href="javascript:" class="btn btn-info btn-sm" style="min-height: 50px;" onclick="resetPassword('{$user['id_utente']}', '{$user['gruppo_utente']}')">RESET PASSWORD</a>
                    </td>
                </tr>
HTML;

                }

                return $_html;

            }



        }
        catch(Exception $e) {
            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");

            if ($boot_table) {
                $_ret['error'] = $e->getMessage();
                echo json_encode($_ret);
                $app->close();
            }

            return null;
        }

        if ($boot_table) {

            $_rows['rows'] = $_ret;
            $_rows['total_rows'] = $_total_rows;

            echo json_encode($_rows);
            $app->close();
        }

    }

    public function get_anagrafica_centri() {

        $app = JFactory::getApplication();
        $_rows = array();
        $_ret = array();
        $_total_rows = 0;

        try {

            $_call_params = JRequest::get($_GET);
            $_offset = (isset($_call_params['offset']) && $_call_params['offset'] != "") ? $_call_params['offset'] : 0;
            $_limit = (isset($_call_params['limit']) && $_call_params['limit'] != "") ? $_call_params['limit'] : 10;
            $_sort = (isset($_call_params['sort']) && $_call_params['sort'] != "") ? $_call_params['sort'] : null;
            $_order = (isset($_call_params['order']) && $_call_params['order'] != "") ? $_call_params['order'] : null;


           $_user = new gglmsModelUsers();


            $_centri = $_user->get_anagrafica_centri($_offset, $_limit, $_sort, $_order);

            if (isset($_centri['rows'])) {

                $_total_rows = $_centri['total_rows'];

                foreach ($_centri['rows'] as $_key_centri => $_centri) {

                    foreach ($_centri as $key => $value) {

                        $_ret[$_key_centri][$key] = $value;
                    }


                }
            }

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        $_rows['rows'] = $_ret;
        $_rows['total_rows'] = $_total_rows;


        echo json_encode($_rows);
        $app->close();
    }

    public function get_accesso_utenti() {

        $app = JFactory::getApplication();
        $_config = new gglmsModelConfig();
        $_rows = array();
        $_ret = array();
        $_total_rows = 0;

        try {

            $_call_params = JRequest::get($_GET);
            $_search = (isset($_call_params['search']) && $_call_params['search'] != "") ? $_call_params['search'] : null;
            $_offset = (isset($_call_params['offset']) && $_call_params['offset'] != "") ? $_call_params['offset'] : 0;
            $_limit = (isset($_call_params['limit']) && $_call_params['limit'] != "") ? $_call_params['limit'] : 10;
            $_sort = (isset($_call_params['sort']) && $_call_params['sort'] != "") ? $_call_params['sort'] : null;
            $_order = (isset($_call_params['order']) && $_call_params['order'] != "") ? $_call_params['order'] : null;

            $_user = new gglmsModelUsers();

            $_label_disabilita = JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR46');
            $_label_abilita = JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR47');


            $_current_user = JFactory::getUser();
            $_user_id =  $_current_user->id;


            $tutor_az = $_user->is_tutor_aziendale($_user_id);
            $tutor_piattaforma = $_user->is_tutor_piattaforma($_user_id);

            if($tutor_az || $tutor_piattaforma) {

                if($tutor_az){
                    $id_azienda = $_user->get_user_societa($_user_id, true);
                    $ret_users  = $_user->get_utenti_dettagli_per_azienda($id_azienda[0]->id, $_offset, $_limit, $_search, $_sort, $_order);

                }elseif ($tutor_piattaforma){

                    $id_piattaforma = $_config->getConfigValue('id_gruppo_piattaforme');
                    $ret_users  = $_user->get_dettagli_user_piattaforma($id_piattaforma, $_offset, $_limit, $_search, $_sort, $_order);

                }


                if (isset($ret_users['rows']) && !is_null($ret_users)) {

                    $_total_rows = $ret_users['total_rows'];

                    foreach ($ret_users['rows'] as $_key_user => $user) {

                        foreach ($user as $key => $value) {


                            if ($key == "stato_user") {

                            $_azione_btn = "";
                            if ($value === '0')
                                $_azione_btn = '<a href="javascript:" class="btn btn-danger btn-sm " style="min-height: 50px;" onclick="DisabilitaAccessoUtente(' . $user['user_id'] . ', ' . $user['stato_user'] . ')">' . $_label_disabilita . '</a>';
                            else if ($value === '1')
                                $_azione_btn = '<a href="javascript:" class="btn btn-success btn-sm " style="min-height: 50px;" onclick="AbilitaAccessoUtente(' . $user['user_id'] . ', ' . $user['stato_user'] . ')">' . $_label_abilita . '</a>';
//

                            $_ret[$_key_user]['tipo_azione'] = trim($_azione_btn);
                            }

                            $_ret[$_key_user][$key] = $value;
                        }


                    }
                }

            }

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        $_rows['rows'] = $_ret;
        $_rows['total_rows'] = $_total_rows;

        echo json_encode($_rows);
        $app->close();
    }

    public function disabilita_accesso_utente() {

        $app = JFactory::getApplication();
        $_ret = array();

        try {

            $params = JRequest::get($_GET);
            $user_id = $params["user_id"];
            $stato_user = $params["stato_user"];


            if (!isset($user_id)
                || $user_id == "")
                throw new Exception("Missing user id", E_USER_ERROR);

            if (!isset($stato_user)
                || $stato_user == "")
                throw new Exception("Missing stato user", E_USER_ERROR);


            $_user = new gglmsModelUsers();
            $_result = $_user->update_accesso_utente($user_id, $stato_user);

            if (!is_array($_result))
                throw new Exception("Update query failed", 1);


            $_ret['success'] = "tuttook";

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        echo json_encode($_ret);
        $app->close();

    }

}
