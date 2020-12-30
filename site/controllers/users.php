<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


//require_once JPATH_COMPONENT . '/libraries/xls/phpspreadsheet.php';
require_once JPATH_COMPONENT . '/libraries/xls/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

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

    public function _import() {

        $app = JFactory::getApplication();
        $db = JFactory::getDbo();

        try {

            require_once JPATH_COMPONENT . '/libraries/xls/phpspreadsheet.php';

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

            $reader = null;
            switch ($file_ext) {
                case 'csv':
                    $reader = new PhpOffice\PhpSpreadsheet\Reader\Csv();
                    break;

                case 'xlsx':
                    $reader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                    break;

                case 'xls':
                    $reader = new PhpOffice\PhpSpreadsheet\Reader\Xls();
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

            /*
            $_test_cols_profiler = null;
            if (isset($json_config['cols_schema']['test_cols']['profiler'])
                && count($json_config['cols_schema']['test_cols']['profiler']) > 0)
                $_test_cols_profiler = $json_config['cols_schema']['test_cols']['profiler'];
            */

            // se la sezione users non è compilata non proseguo
            if (is_null($_users))
                throw new Exception("Nessuna configurazione per la sezione users", 1);

            $_registered_group = UtilityHelper::check_usergroups_by_name('Registered');
            if (is_null($_registered_group))
                throw new Exception("Nessun id disponibile per il gruppo Registered", 1);

            // tipo dei campi per effettuare un controllo sui valori prima dell'inserimento
            $cb_fields_type = UtilityHelper::get_comprofiler_fields_type();

            $spreadsheet = $reader->load($target_file);
            $import_report = array();

            $db->transactionStart();

            if ($file_ext == 'xlsx' || $file_ext == 'xls') {

                // controllo quanti sheet ci sono nel file (> 1 vado in errore)
                $sheets_num = $spreadsheet->getSheetCount();
                if ($sheets_num > 1)
                    throw new Exception("Sheet multipli non supportati. Il foglio " . $file_ext . " deve contenere soltanto un foglio attivo", 1);

                $sheet_data =  $spreadsheet->getActiveSheet()->toArray();
                $num_rows = ($numero_prima_riga > 0) ? count($sheet_data) : count($sheet_data)-1;

                for ($i=$numero_prima_riga; $i<=$num_rows; $i++) {

                    $_new_user = array();
                    $_new_user_groups = array();
                    $_new_user_cp = array();

                    // array riga
                    $_riga_xls = $sheet_data[$i];

                    foreach ($_riga_xls as $num_cell => $value_cell) {

                        $_multiple_emails = array();

                        // inserimento parte tabella users
                        foreach ($_users as $db_col => $xls_col) {

                            $_user_value = "";
                            if (strpos($xls_col, "_") !== false) {
                                $_arr_xls = explode("_", $xls_col);
                                foreach ($_arr_xls as $_sub_xls => $_sub_xls_col) {

                                    $col_index = PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($_sub_xls_col);
                                    $col_index = ($col_index > 0) ? $col_index-1 : $col_index;
                                    $_row_value = trim($_riga_xls[$col_index]);

                                    if ($_user_value != $_row_value)
                                        $_user_value .= ($_user_value != "") ? "/" . $_row_value : $_row_value;

                                }
                            }
                            else {
                                $col_index = PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($xls_col);
                                $col_index = ($col_index > 0) ? $col_index-1 : $col_index;
                                $_user_value = addslashes(trim($_riga_xls[$col_index]));
                            }

                            if ($db_col == "password")
                                $_user_value = JUserHelper::hashPassword($_user_value);
                            else if ($db_col == "block")
                                $_user_value = (int) trim($xls_col);
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
                        if ($_test_users_fields != "")
                            throw new Exception("Mancano i seguenti campi necessari per inserire un nuovo utente:" . $_test_users_fields);

                        // controllo esistenza utente su username
                        if ($_test_users_exists) {
                            if (UtilityHelper::check_user_by_username($_new_user['username'])) {
                                $import_report['existing'][] = ($i+$numero_prima_riga) . ":" . $_new_user['username'];
                                continue;
                            }
                        }

                        // se impostati associazione usergroups e creazione se non esistenti
                        if (!is_null($_groups)) {

                            foreach ($_groups as $db_col => $xls_col) {

                                $col_index = PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($xls_col);
                                $col_index = ($col_index > 0) ? $col_index-1 : $col_index;
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
                        $_new_user['groups'] = $_new_user_groups;

                        // creazione utente
                        $user = new JUser;
                        $user->bind($_new_user);
                        if (!$user->save()) {
                            //throw new Exception("Errore durante l'inserimento utente. Riga: " . $i . " - " . print_r($_new_user, true));
                            $import_report['not_inserted'][] = ($i+$numero_prima_riga) . ":" . $_new_user['username'];
                            continue;
                        }

                        $_new_user_id = $user->id;

                        $import_report['inserted'][] = $_new_user_id . ":" . $_new_user['username'];

                        // se non valorizzato non compilo CP
                        if (is_null($_profiler)) {
                            $db->transactionCommit();
                            continue;
                        }

                        // profiler
                        foreach ($_profiler as $db_col => $xls_col) {

                            $_group_value_cp = "";
                            if (strpos($xls_col, "_") !== false) {
                                $_arr_xls = explode("_", $xls_col);
                                foreach ($_arr_xls as $_sub_xls => $_sub_xls_col) {
                                    $col_index = PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($_sub_xls_col);
                                    $col_index = ($col_index > 0) ? $col_index-1 : $col_index;
                                    $_row_value = trim($_riga_xls[$col_index]);

                                    if ($_group_value_cp != $_row_value)
                                        $_group_value_cp .= ($_group_value_cp != "") ? "/" . $_row_value : $_row_value;
                                }
                            }
                            else {

                                $col_index = PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($xls_col);
                                $col_index = ($col_index > 0) ? $col_index-1 : $col_index;
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
                        $_cp_insert_query = UtilityHelper::get_cp_insert_query($_new_user_cp);
                        $_cp_insert_query_result = UtilityHelper::insert_new_cp_user_with_query($_cp_insert_query);
                        if (!is_array($_cp_insert_query_result))
                            throw new Exception(print_r($_new_user_cp, true) . " errore durante inserimento", 1);

                        $import_report['inserted_cp'][] = $_new_user_id . ":" . $_new_user['username'];

                        $db->transactionCommit();

                    }

                }

                echo "<pre>";
                    print_r($import_report);
                echo "</pre>";

            }

        }
        catch (Exception $e) {
            $db->transactionRollback();
            echo __FUNCTION__ . ' error: ' . $e->getMessage();
        }

        $app->close();
    }

}
