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

    public function check_user($username, $password) {

        try {

            $_ret = array();

            // Get a database object
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('id, password')
                ->from('#__users')
                ->where('username = ' . $db->quote($username));

            $db->setQuery($query);
            $result = $db->loadObject();

            if (!$result)
                return "User not exist!";

            $match = JUserHelper::verifyPassword($password, $result->password, $result->id);

            if (!$match)
                return "Password mismatch";

            $_ret['success'] = $result->id;
            return $_ret;

        }
        catch (Exception $e) {
            return __FUNCTION__ . ' error: ' . $e->getMessage();
        }

    }

    public function get_user_quote($user_id) {

        try {

            $_ret = array();

            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('tipo_quota, anno')
                ->from('#__gg_quote_iscrizioni')
                ->where("user_id = '" . $user_id . "'")
                ->group($db->quoteName('tipo_quota'))
                ->group($db->quoteName('anno'))
                ->order('anno DESC', 'DESC');

            $db->setQuery($query);
            $result = $db->loadAssocList();

            // se nessun risultato restituisco un array vuoto
            if (!$result) {
                return $_ret;
            }

            return $result;

        }
        catch (Exception $e) {
            return __FUNCTION__ . ' error: ' . $e->getMessage();
        }

    }

    public function get_user_details_cb($user_id) {

        try {

            $_ret = array();

            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->select('cb_professionedisciplina as professione,
                                cb_laureain as tipo_laurea, 
                                cb_laureanno as anno_laurea, 
                                cb_datadinascita as data_nascita,
                                firstname as nome_utente,
                                lastname as cognome_utente,
                                cb_codicefiscale as codice_fiscale')
                    ->from('#__comprofiler')
                    ->where("user_id = '" . $user_id . "'");

            $db->setQuery($query);
            $result = $db->loadAssoc();

            // se nessun risultato restituisco un array vuoto
            if (!$result) {
                return $_ret;
            }

            return $result;

        }
        catch (Exception $e) {
            return __FUNCTION__ . ' error: ' . $e->getMessage();
        }

    }

    public function update_ultimo_anno_pagato($user_id, $ultimo_anno_pagato) {

        try {

            $_ret = array();

            $db = JFactory::getDbo();

            $query = $db->getQuery(true);
            $query->update("#__comprofiler");
            $query->set("cb_ultimoannoinregola = '" . $ultimo_anno_pagato . "'");
            $query->where("user_id = " . $user_id);

            $db->setQuery($query);
            $db->execute();

            $_ret['success'] = 'tuttook';

            return $_ret;

        }
        catch (Exception $e) {
            return __FUNCTION__ . ' error: ' . $e->getMessage();
        }

    }

    public function insert_user_quote_anno($user_id,
                                           $_anno_quota,
                                           $_data_creazione,
                                           $_order_details,
                                           $gruppi_online,
                                           $gruppi_moroso,
                                           $gruppi_decaduto,
                                           $totale_sinpe,
                                           $totale_espen=0) {

        $db = JFactory::getDbo();

        try {

            $_ret = array();
            $db->transactionStart();

            // inserisco le righe riferite agli anni
            //$query = $db->getQuery(true);
            $query = "INSERT INTO #__gg_quote_iscrizioni (user_id, 
                                                                anno, 
                                                                tipo_quota, 
                                                                tipo_pagamento, 
                                                                data_pagamento, 
                                                                totale, 
                                                                dettagli_transazione) 
                            VALUES ";

            $query .= "(
                               '" . $user_id . "',
                               '" . $_anno_quota . "',
                               'quota',
                               'paypal',
                               '" . $_data_creazione . "',
                               '" . $totale_sinpe . "',
                               '" . addslashes($_order_details) . "'
                            )";

            if ($totale_espen)
                $query .= ", (
                               '" . $user_id . "',
                               '" . $_anno_quota . "',
                               'espen',
                               'paypal',
                               '" . $_data_creazione . "',
                               '" . $totale_espen . "',
                               NULL
                            )";

            $query .= ";";

            $db->setQuery($query);
            $db->execute();

            // aggiorno ultimo anno pagato
            $_ultimo_anno = $this->update_ultimo_anno_pagato($user_id, $_anno_quota);
            if (!is_array($_ultimo_anno))
                throw new Exception($_ultimo_anno, 1);

            // inserisco l'utente nel gruppo online
            UtilityHelper::set_usergroup_online($user_id,
                $gruppi_online,
                $gruppi_moroso,
                $gruppi_decaduto);

            $db->transactionCommit();

            $_ret['success'] = "tuttook";

            return $_ret;

        }
        catch (Exception $e) {
            $db->transactionRollback();
            return __FUNCTION__ . ' error: ' . $e->getMessage();
        }

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

}
