<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/models/generacoupon.php';

require_once JPATH_COMPONENT . '/libraries/xls/src/Spout/Autoloader/autoload.php';
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

        $app->redirect('/home');
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

                $sheet_data = null;
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

            $db->transactionStart();
            $i=0;
            if ($file_ext == 'xlsx'
                || $file_ext == 'xls') {

                // controllo quanti sheet ci sono nel file (> 1 vado in errore)
                $sheets_num = count($reader->getSheetIterator());
                if ($sheets_num > 1)
                    throw new Exception("Sheet multipli non supportati. Il foglio " . $file_ext . " deve contenere soltanto un foglio attivo", 1);

                $sheet_data = null;
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
                                $_new_user_groups[] = $_group_id;
                            }

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

                        $db->transactionCommit();

                        continue 2;

                    }

                    $i++;
                }

            }

            $row_existing = count($import_report['row_existing']);
            $inserted = count($import_report['inserted']);
            $inserted_cp = count($import_report['inserted_cp']);
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

            $sheet_data = null;
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
                throw new Exception("Missing user id", 1);

            if (!isset($totale)
                || $totale == ""
                || $totale == 0)
                throw new Exception("Missing totale", 1);

            if (!isset($tipo_quota)
                || $tipo_quota == "")
                throw new Exception("Missing service type", 1);

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
                throw new Exception($_bonifico, 1);

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
                throw new Exception("Missing user id", 1);

            if (!isset($totale)
                || $totale == ""
                || $totale == 0)
                throw new Exception("Missing totale", 1);

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

            if (!is_array($_bonifico))
                throw new Exception($_bonifico, 1);

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

            if (!isset($user_id)
                || $user_id == "")
                throw new Exception("Missing user id", 1);

            $dt = new DateTime();
            // non anno corrente perchè da moroso deve pagare partendo dall'ultimo anno
            $_anno_quota = $dt->format('Y')-1;

            $_params = utilityHelper::get_params_from_plugin();
            $gruppi_online = utilityHelper::get_ug_from_object($_params, "ug_online");
            $gruppi_moroso = utilityHelper::get_ug_from_object($_params, "ug_moroso");
            $gruppi_decaduto = utilityHelper::get_ug_from_object($_params, "ug_decaduto");

            // inserisco utente nel gruppo moroso
            $_check = utilityHelper::set_usergroup_moroso($user_id, $gruppi_online, $gruppi_moroso, $gruppi_decaduto);
            if (!is_array($_check))
                throw new Exception($_check, 1);

            // aggiorno ultimo anno pagato
            $_user = new gglmsModelUsers();
            $_ultimo_anno = $_user->update_ultimo_anno_pagato($user_id, $_anno_quota);
            if (!is_array($_ultimo_anno))
                throw new Exception($_ultimo_anno, 1);

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

    public function iscrivi_utente_gruppo() {

        $app = JFactory::getApplication();
        $_ret = array();

        try {

            $params = JRequest::get($_POST);
            $corso_id = $params['group_id'];
            $user_id = $params['user_id'];
            $dominio = $params['dominio'];
            $_current_user = JFactory::getUser();

            if (!isset($corso_id)
                || $corso_id == ""
                || $corso_id == 0)
                throw new Exception("Missing corso id", E_USER_ERROR);

            if (!isset($user_id)
                || $user_id == "")
                throw new Exception("Missing user id", E_USER_ERROR);

            if (!isset($dominio)
                || $dominio == "")
                throw new Exception("Missing dominio", E_USER_ERROR);

            if ($user_id == 0)
                throw new Exception(JText::_('COM_GGLMS_BOXES_SCHEDA_POSTI_LOGIN_ERROR'), E_USER_ERROR);

            if (isset($_current_user)
                && $_current_user->id != $user_id)
                throw new Exception(JText::_('COM_GGLMS_BOXES_SCHEDA_ERROR_USER_MISMATCH'), E_USER_ERROR);

            // controllo se il corso è completo nel caso in cui abbia una soglia di prenotazione
            $model_unita = new gglmsModelUnita();
            $unita = $model_unita->getUnita($corso_id);
            $gruppo_corso = $model_unita->get_id_gruppo_unit($corso_id);

            if (is_null($gruppo_corso)
                || $gruppo_corso == "")
                throw new Exception("Nessun gruppo corso definito per unita id: " . $corso_id, E_USER_ERROR);

            // controllo se l'utente è già iscritto al corso
            if (utilityHelper::check_user_into_ug($user_id, (array) $gruppo_corso))
                throw new Exception(JText::_('COM_GGLMS_BOXES_SCHEDA_ISCRITTO'), E_USER_ERROR);

            // se si tratta di un corso con prenotazione controllo quanti posti sono ancora disponibili
            $model_user = new gglmsModelUsers();
            if ($unita->posti_disponibili > 0) {
                $utenti_per_gruppo = $model_user->get_users_per_gruppo($gruppo_corso);
                $utenti_iscritti = (is_array($utenti_per_gruppo)) ? count($utenti_per_gruppo) : 0;

                $posti_disponibili = ($unita->posti_disponibili - $utenti_iscritti);
                if ($posti_disponibili <= 0)
                    throw new Exception(JText::_('COM_GGLMS_BOXES_SCHEDA_POSTI_DISPONIBILI_NESSUNO') . ' (MAX ' . $unita->posti_disponibili . ')', E_USER_ERROR);

            }

            // controllo se il corso è in uno dei gruppi dell'utente
            if (!utilityHelper::check_user_into_ug($user_id, explode(",", $unita->bookable_a_gruppi)))
                throw new Exception(JText::_('COM_GGLMS_BOXES_SCHEDA_GRUPPO_NON_ABILITATO'), E_USER_ERROR);

            // inserimento coupon per il corso a cui l'utente si vuole iscrivere
            $data_coupon = array();
            $model_genera_coupon = new gglmsModelgeneracoupon();

            // parametri dalla configurazione
            $model_config = new gglmsModelConfig();
            $data_coupon["attestato"] = $model_config->getConfigValue('check_coupon_attestato') ? 1 : 0;
            $durata_coupon = $model_config->getConfigValue('durata_standard_coupon');
            $durata_coupon = (is_null($durata_coupon) || $durata_coupon == "" || $durata_coupon == 0)
                ? 60 : $durata_coupon;
            $data_coupon["abilitato"] = 1;
            $data_coupon['stampatracciato'] = 0;
            $data_coupon['trial'] = 0;
            $data_coupon['venditore'] = NULL;
            $data_coupon['genera_coupon_tipi_coupon'] = NULL;
            $data_coupon['gruppo_corsi'] = $gruppo_corso;
            // informazioni piattaforma
            $_tmp_id_piattaforma = $model_user->get_user_piattaforme($user_id, true);
            // controllo eventuali errori
            if (is_null($_tmp_id_piattaforma))
                throw new RuntimeException("generazione coupon - no user piattaforme found", E_USER_ERROR);

            $data_coupon["id_piattaforma"] = $_tmp_id_piattaforma[0]->value;

            $id_iscrizione = $model_genera_coupon->_generate_id_iscrizione($data_coupon['id_piattaforma']);
            $info_societa = $model_genera_coupon->_get_info_gruppo_societa($_current_user->username, $data_coupon["id_piattaforma"], true);
            // controllo eventuali errori
            if (is_null($info_societa)
                || !is_array($info_societa))
                throw new RuntimeException("generazione coupon - nessun gruppo societa trovato", E_USER_ERROR);

            $id_gruppo_societa = $info_societa["id"];
            $nome_societa = $info_societa["name"];

            $_info_corso = $model_genera_coupon->get_info_corso($data_coupon["gruppo_corsi"], true);
            if (is_null($_info_corso)
                || !is_array($_info_corso))
                throw new RuntimeException("generazione coupon - nessun info corso trovato", E_USER_ERROR);

            $prefisso_coupon = $_info_corso["prefisso_coupon"];
            // inserimento effettivo del coupon
            $data_utilizzo = $unita->data_inizio . ' ' . date('H:i:s');
            $insert_coupon = $model_genera_coupon->make_insert_coupon($prefisso_coupon, $nome_societa, $id_iscrizione, $durata_coupon, $id_gruppo_societa, $data_coupon, $user_id, $data_utilizzo, true);
            if (is_null($insert_coupon))
                throw new Exception("generazione coupon - " . $insert_coupon, E_USER_ERROR);

            // associo l'utente al gruppo
            $_add_ug = utilityHelper::set_usergroup_generic($user_id, $gruppo_corso);
            if (!is_array($_add_ug))
                throw new Exception($_add_ug, E_USER_ERROR);

            // invio email di conferma iscrizione
            $confirm_email = utilityHelper::email_conferma_registrazione_corso($unita->titolo,
                $unita->data_inizio,
                $unita->data_fine,
                $dominio,
                $_current_user->name,
                $_current_user->email);

            if (is_null($confirm_email))
                throw new Exception("Si è verificato un errore durante l'invio della Email di conferma iscrizione al corso", E_USER_ERROR);

            $_ret['success'] = JText::_('COM_GGLMS_BOXES_SCHEDA_PRENOTAZIONE_OK');

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        echo json_encode($_ret);
        $app->close();
    }
}
