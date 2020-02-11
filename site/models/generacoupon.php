<?php

/**
 * WebTVContenuto Model
 *
 * @package    Joomla.Components
 * @subpackage WebTV
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * WebTVContenuto Model
 *
 * @package    Joomla.Components
 * @subpackage WebTV
 */

require_once JPATH_COMPONENT . '/models/config.php';
require_once(JPATH_COMPONENT . '/libraries/smarty/EasySmarty.class.php');

class gglmsModelgeneracoupon extends JModelLegacy
{

    private $_japp;
    protected $_db;
    private $_userid;
    private $_user;
    public $_params;
    public $lista_corsi;
    public $societa_venditrici;
    private $_config;
    private $_info_corso;


    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_dbg = JRequest::getBool('dbg', 0);
        $this->_japp = JFactory::getApplication();
        $this->_db = JFactory::getDbo();
        $this->_user = JFactory::getUser();
        $this->_userid = $this->_user->get('id');
        $this->_params = $this->_japp->getParams();
        $this->_config = new gglmsModelConfig();

        // valori per dropdown
        $this->lista_corsi = utilityHelper::getGruppiCorsi();
        $this->societa_venditrici = utilityHelper::getPiattaformeByUser();

    }

    public function __destruct()
    {

    }

    // entry point
    public function insert_coupon($data)
    {

        try {


            // check for attestato
            if (!$this->_config->getConfigValue('check_coupon_attestato')) {
                // se il controllo è spento, creo tutti i copon con campo attesato =1 ;
                $data['attestato'] = 1;

            } else {
//                altrimenti lo leggo dal form
                $data['attestato'] = $data['attestato'] == 'on' ? 1 : 0;
            }

            // check durata coupon, se il campo è nel form vince l'input dell'utente
            $durata_coupon = $data["durata"] ? $data["durata"] : $this->_config->getConfigValue('durata_standard_coupon');
            if ($durata_coupon == null) {
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            }

            // se non è specificato nel form il default è coupon abilitati.
            $data['abilitato'] = $data['abilitato'] == 'on' ? 1 : $this->_config->getConfigValue('coupon_active_default') == 0 ? 1 : 0;
            $data['stampatracciato'] = $data['stampatracciato'] == 'on' ? 1 : 0;
            $data['trial'] = $data['trial'] == 'on' ? 1 : 0;
            $data['venditore'] = isset($data['venditore']) ? $data["venditore"] : NULL;
//            $data['email_coupon'] = $data['email_coupon'] == '' ? $data['email_coupon'] : NULL;


            // se non esiste crea utente ( tutor ) legato alla company
            // esiste gia' l'username (P.iva) ?
            $user_id = $this->_check_username((string)$data['username']);

            $company_user = null;
            $new_societa = false;

            if (empty($user_id)) {
                $new_societa = true;
                $company_user = $this->create_new_company_user($data);

            } else {
                // se l'utente esiste già, la parte del from aziendale e disabilitata
                // non mi arriva l'id piattaforma
                // lo ricavo dall'utente partita iva
                $model_user = new gglmsModelUsers();
                $data["id_piattaforma"] = $model_user->get_user_piattaforme($user_id)[0]->value;
                
            }

            $id_iscrizione = $this->_generate_id_iscrizione($data['id_piattaforma']);
            $info_societa = $this->_get_info_gruppo_societa($data['username'], $data["id_piattaforma"]);
            $id_gruppo_societa = $info_societa["id"];
            $nome_societa = $info_societa["name"];

            $this->_info_corso = $this->get_info_corso($data["gruppo_corsi"]);
            $prefisso_coupon = $this->_info_corso["prefisso_coupon"];


            $coupons = array();
            $values = array();


            // campo unico il set di coupon composto da idPiattaformaVenditrice_stringone senza senso basato sul now
            for ($i = 0; $i < intval($data['qty']); $i++) {

                $coupons[$i] = $this->_generate_coupon($prefisso_coupon, $nome_societa);

                // se abilitato -> dataabilitazione = now

                $values[] = sprintf("('%s', '%s', %d, '%s', '%s', %d, %d , %d , %d , %d, %d , '%s')",
                    $coupons[$i],
                    date('Y-m-d H:i:s', time()), //  time(), //creation_time
                    $data['abilitato'],
                    $id_iscrizione,
                    $data['abilitato'] == 1 ? date('Y-m-d H:i:s', time()) : 'NULL',
                    $durata_coupon,
                    $data['attestato'],
                    $id_gruppo_societa,
                    $data['gruppo_corsi'],
                    $data['stampatracciato'],
                    $data['trial'],
                    $data['venditore']
                );

            }


            // li inserisco nel DB
            $query = 'INSERT INTO #__gg_coupon (coupon, creation_time, abilitato, id_iscrizione, data_abilitazione, durata ,attestato, id_societa, id_gruppi, stampatracciato, trial, venditore) VALUES ' . join(',', $values);
            $this->_db->setQuery($query);
            if (false === $this->_db->execute()) {
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            }


            // leggo da configurazione se mandare le mail con i coupon generati
            $send_mail = $this->_config->getConfigValue('mail_coupon_acitve');
            if ($send_mail == 1) {

                if ($this->send_coupon_mail($coupons, $data["id_piattaforma"], $nome_societa, $id_gruppo_societa, $data['email_coupon']) === false) {
                    throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
                }

                // send new credentials
                if ($new_societa) {

                    if ($this->send_new_company_user_mail($company_user, $nome_societa, $data["id_piattaforma"], $data['email_coupon']) === false) {
                        throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
                    }

                }


            }


            // leggo da configurazione se creare o meno forum
            $genera_forum = $this->_config->getConfigValue('genera_forum');
            if ($genera_forum == 1) {
                $forum_corso = $this->_check_corso_forum($id_gruppo_societa, $data['gruppo_corsi']);

                if (empty($forum_corso)) {

                    if (false === ($forum_corso = $this->_create_corso_forum($id_gruppo_societa, $data['gruppo_corsi'], $nome_societa))) {
                        throw new RuntimeException('Error: cannot create forum corso', E_USER_ERROR);
                    }
                }

            }


            return $id_iscrizione;

        } catch (Exception $ex) {

            DEBUGG::error($ex, 'insert_coupon');

        }

    }

    public function _get_info_gruppo_societa($piva, $id_piattaforma)
    {
        // prendo utente username= p.iva

        try {
            $query = $this->_db->getQuery(true)
                ->select('u.id')
                ->from('#__users as u')
                ->where('username="' . $piva . '"');


            $this->_db->setQuery($query);
            $user_societa = $this->_db->loadResult();


            // prendo i gruppi a cui appartiene
            $gruppi_appartenenza_utente = JAccess::getGroupsByUser($user_societa, true);


            // filtro i gruppi a cui appartiene l'utente piva per quelli figli di piattaforma $id_piattaforma
            $query = $this->_db->getQuery(true)
                ->select('ug.id as id , ug.title as name')
                ->from('#__usergroups as ug')
                ->where('parent_id="' . $id_piattaforma . '"')
                ->where('ug.id IN ' . ' (' . implode(',', $gruppi_appartenenza_utente) . ')');


            $this->_db->setQuery($query);
            $id_gruppo_societa = $this->_db->loadAssoc();

            return $id_gruppo_societa;
        } catch (Exception $e) {
            DEBUGG::error($e, '_get_id_gruppo_societa');
        }


    }

    public function create_new_company_user($data)
    {
        try {

            // genero una password casuale
            $password = $this->_generate_pwd(8);
            $salt = JUserHelper::genRandomPassword(32);
            $crypt = JUserHelper::getCryptedPassword($password, $salt) . ':' . $salt;

            // creo nuovo user
            $query = sprintf('INSERT INTO #__users (name, username, password, email, sendEmail, registerDate, activation) VALUES (\'%s\', \'%s\', \'%s\', \'%s\', 0, NOW(), \'\')', $data['ragione_sociale'], $data['username'], $crypt, $data['email']);
            $this->_db->setQuery($query);
            if (false === $this->_db->query())
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
//                debug::msg('Nuovo utente ' . $data['username'] . ':' . $password . ' inserito.');

            // id del nuovo user
            $user_id = $this->_db->insertid();


            // creo nuovo gruppo figlio di piattaforma e lo associo all'utente che ho appena creato
            if (false === ($company_group_id = $this->_create_company_group($user_id, $data['ragione_sociale'], $data["id_piattaforma"])))
                throw new Exception('Errore nella creazione del gruppo', E_USER_ERROR);


            $new_user = new gglmsModelUsers();
            $new_user->set_user_tutor($user_id, 'aziendale');


            // inserisco in comprofiler
            $query = 'INSERT INTO #__comprofiler (id, user_id, cb_cognome, cb_ateco) VALUES (
                ' . $user_id . ',
                ' . $user_id . ',
                \'' . $data['ragione_sociale'] . '\' ,
                \'' . $data['ateco'] . '\'
                )';

            $this->_db->setQuery($query);
            if (false === $this->_db->query()) {
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);

            }


            // leggo da configurazione se creare o meno forum compagnia
            $genera_forum = $this->_config->getConfigValue('genera_forum');
            if ($genera_forum == 1) {
                if (false === $this->_create_company_forum($company_group_id, $data['ragione_sociale'], $data['vendor'])) {
                    throw new Exception('Errore nella creazione del forum', E_USER_ERROR);
                }
            }


            $res = array('user_id' => $user_id,
                'password' => isset($password) ? $password : null,
                'id_gruppo_societa' => $company_group_id,
                'email' => $data['email'],
                'company_name' => $data['ragione_sociale'],
                'piva' => $data['username']);

            return $res;
        } catch (Exception $e) {

            DEBUGG::error($e, 'create_new_company_user');

        }
        return false;
    }

    private function _check_username($username)
    {
        $query = 'SELECT id FROM #__users WHERE username=\'' . $username . '\' LIMIT 1';
        $this->_db->setQuery($query);
        if (false === ($results = $this->_db->loadRow())) {
            throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
        }

        return isset($results[0]) ? $results[0] : null;
    }


    private function _generate_pwd($l = 8)
    {
        return chr(65 + rand(0, 1) * 32 + rand(0, 25)) . ($l ? $this->_generate_pwd(--$l) : '');
    }

    /**
     * Crea un nuovo gruppo con il nome della società ($company_name) gerarchicamente sotto il gruppo della piattaforma veditrice ($parent_id) e associa
     * l'id della società ($company_id) al gruppo appena creato.
     *
     * @param int $company_id
     * @param string $company_name
     * @param  int $piattaforma_group_id
     * @return int Ritorn l'ID del gruppo appena creato o FALSE in caso di errore.
     */
    private function _create_company_group($company_id, $company_name, $piattaforma_group_id)
    {

        try {
            // creo un gruppo figlio della piattaforma
            $insertquery_group = 'INSERT INTO #__usergroups (parent_id, lft,rgt, title) VALUES(';
            $insertquery_group = $insertquery_group . $piattaforma_group_id . ',';
            $insertquery_group = $insertquery_group . '0' . ',';
            $insertquery_group = $insertquery_group . '0' . ',';
            $insertquery_group = $insertquery_group . '\'' . $company_name . '\'' . ')';

            //echo $insertquery; die;
            $this->_db->setQuery($insertquery_group);
            $this->_db->execute();
            $new_group_id = $this->_db->insertid(); // id del gruppo appena inserito


            // associo utente al gruppo societa
            $insertquery_map = 'INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (' . $company_id . ', ' . $new_group_id . ')';
            $this->_db->setQuery($insertquery_map);
            $this->_db->execute();

            // rebuild usergroups to fix lft e rgt
            $JTUserGroup = new JTableUsergroup($this->_db);
            $JTUserGroup->rebuild();

            return $new_group_id;

        } catch (Exception $ex) {

            var_dump($ex);
            echo 'errore in _create_company_group';
            return false;


        }

    }

    private function _generate_coupon($prefisso_coupon, $nome_societa)
    {

        $var_1 = 'X-' . str_replace(' ', '_', $prefisso_coupon) . substr($nome_societa, 0, 3);
        $var_2 = str_replace('.', 'p', str_replace('0', 'k', uniqid('', true))); // no zeros , no dots

        return str_replace(' ', '_', $var_1 . $var_2);

    }

    private function _generate_id_iscrizione($id_piattaforma)
    {
        return $id_piattaforma . '_' . uniqid(time());
    }

//////////////////////////////  MAIL   /////////////////////

    // MAIL COUPON
    public function send_coupon_mail($coupons, $id_piattaforma, $nome_societa, $id_gruppo_societa, $email_coupon = '')
    {

        // get recipients --> tutor piattaforma (cc) + tutor aziendale (to)
        if (false == ($recipients = $this->get_coupon_mail_recipients($id_piattaforma, $id_gruppo_societa, $email_coupon))) {
            $this->_japp->redirect(JRoute::_('/home/genera-coupon'), $this->_japp->enqueueMessage('Non ci sono tutor piattaforma configurati per questa piattaforma', 'Error'));

        }

        // get sender
        if (false == ($sender = $this->get_mail_sender($id_piattaforma))) {
            $this->_japp->redirect(JRoute::_('/home/genera-coupon'), $this->_japp->enqueueMessage('Non è configurato un indirizzo mail di piattaforma', 'Error'));

        }

        // get data
        $info_piattaforma = $this->get_info_piattaforma($id_piattaforma);

        // send mail
        $template = JPATH_COMPONENT . '/models/template/coupons_mail.tpl';

        // se viene fornita una mail a cui inviare i coupon  non la mando al tutor aziendale ma alla mail fornita
        $to = $email_coupon != '' ? $email_coupon : $recipients["to"]->email;


        $mailer = JFactory::getMailer();
        $mailer->setSender($sender);
        $mailer->addRecipient($to);
        $mailer->addCc($recipients["cc"]);
        $mailer->setSubject('Coupon corso ' . $this->_info_corso["titolo"]);


//        // modidifca custom per ausind, da cambiare in piattaforma hidden!!
//        if ($id_piattaforma == '141') {
//            $info_piattaforma["alias"] = "Ausind";
//        }

        if ($info_piattaforma['mail_from_default'] == 1) {

//            ricavo alias e name dalla piattaforma di default
            $piattaforma_default = $this->get_info_piattaforma_default();

            $info_piattaforma["alias"] =$piattaforma_default['alias'];
            $info_piattaforma["name"] = $piattaforma_default['name'];
//
        }

        $smarty = new EasySmarty();
        $smarty->assign('coupons', $coupons);
        $smarty->assign('coupons_count', count($coupons));
        $smarty->assign('course_name', $this->_info_corso["titolo"]);
        $smarty->assign('company_name', $nome_societa);
        $smarty->assign('piattaforma_name', $info_piattaforma["alias"]);
        $smarty->assign('recipient_name', $recipients["to"]->name);

        $mailer->setBody($smarty->fetch_template($template, null, true, false, 0));
        $mailer->isHTML(true);

        if (!$mailer->Send()) {
//            throw new RuntimeException('Error sending mail', E_USER_ERROR);
            utilityHelper::logMail('coupons_mail', $sender, $recipients["to"]->email, 0, implode(", ", $recipients['cc']), $this->_info_corso["idgruppo"]);
        }

        //log mail sent
        utilityHelper::logMail('coupons_mail', $sender, $recipients["to"]->email, 1, implode(", ", $recipients['cc']), $this->_info_corso["idgruppo"]);
        return true;

    }

    public function get_mail_sender($id_piattaforma)
    {

        try {

            $query = $this->_db->getQuery(true)
                ->select(' d.email_riferimento as email_riferimento')
                ->from('#__usergroups_details AS d')
                ->where("d.group_id= " . $id_piattaforma);

            $this->_db->setQuery($query);
            $data = $this->_db->loadResult();


            return $data;

        } catch (Exception $e) {
            DEBUGG::query($query);
            DEBUGG::log($e, 'get_coupon_sender', 1);

        }


    }

    public function get_coupon_mail_recipients($id_piattaforma, $id_gruppo_societa, $email_coupon = '')
    {

        // TO = tutor aziendale
        // CC = tutor di piattaforma

        // utente loggato
        $to = null;
        $cc = array();

        $user = new gglmsModelUsers();
        $tutor_piattaforma_id_list = $user->get_all_tutor_piattaforma($id_piattaforma);

        foreach ($tutor_piattaforma_id_list as $tutor_id) {
            array_push($cc, $this->get_user_info($tutor_id, 'email'));
        }


        $tutor_az = $user->get_tutor_aziendale($id_gruppo_societa);
        $to->email = $email_coupon == '' ? $this->get_user_info($tutor_az, 'email') : $email_coupon;
        $to->name = $this->get_user_info($tutor_az, 'name');

//        $to->email = $this->get_user_info($this->_userid, 'email');
//        $to->name = $this->get_user_info($this->_userid, 'name');


        $result = array('to' => $to, 'cc' => $cc);

        return empty($to) || empty($cc) ? false : $result;

    }

    public function get_user_info($user_id, $field)
    {
        $user = JFactory::getUser($user_id);
        $info = $user->get($field);

        return $info;
    }

    public function get_info_corso($id_gruppo_corso)
    {

        try {

            $query = $this->_db->getQuery(true)
                ->select('u.prefisso_coupon, u.titolo, gm.idgruppo')
                ->from('#__usergroups as g')
                ->join('inner', '#__gg_usergroup_map AS gm ON g.id = gm.idgruppo')
                ->join('inner', '#__gg_unit AS u ON u.id = gm.idunita')
                ->where('gm.idgruppo=' . $id_gruppo_corso)
                ->setLimit('1');

            $this->_db->setQuery($query);


            if (false === ($result = $this->_db->loadAssoc())) {

                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            }

            return $result;

        } catch (Exception $e) {
            DEBUGG::error($e, 'get_info_corso');
        }


    }

    public function get_info_piattaforma($id_piattaforma)
    {


        try {

            $query = $this->_db->getQuery(true)
                ->select('ug.id as id , ug.title as name, ud.dominio as dominio, ud.alias as alias, ud.mail_from_default as mail_from_default')
                ->from('#__usergroups as ug')
                ->join('inner', '#__usergroups_details AS ud ON ug.id = ud.group_id')
                ->where('id=' . $id_piattaforma);


            $this->_db->setQuery($query);
            $info_piattaforma = $this->_db->loadAssoc();

            return $info_piattaforma;

        } catch (Exception $e) {
            DEBUGG::error($e, 'get_info_piattaforma');
        }


    }


    public function get_info_piattaforma_default()
    {


        try {

            $query = $this->_db->getQuery(true)
                ->select('ug.id as id , ug.title as name, ud.dominio as dominio, ud.alias as alias, ud.mail_from_default as mail_from_default')
                ->from('#__usergroups as ug')
                ->join('inner', '#__usergroups_details AS ud ON ug.id = ud.group_id')
                ->where('ud.is_default = 1');


            $this->_db->setQuery($query);
            $info_piattaforma = $this->_db->loadAssoc();

            return $info_piattaforma;

        } catch (Exception $e) {
            DEBUGG::error($e, 'get_info_piattaforma');
        }


    }

    // MAIL REGISTRAZIONE NUOVA SOCIETA'

    public function send_new_company_user_mail($company_user, $nome_societa, $id_piattaforma, $email_coupon = '')
    {

        // se viene fornita una mail a cui inviare i coupon  non la mando al tutor aziendale ma alla mail fornita
        $recipients = $email_coupon == '' ? $company_user["email"] : $email_coupon;
        if (false == $recipients) {
            DEBUGG::log($recipients, 'send_new_company_user_mail');
        }

        // get sender
        if (false == ($sender = $this->get_mail_sender($id_piattaforma))) {
            $this->_japp->redirect(JRoute::_('/home/genera-coupon'), $this->_japp->enqueueMessage('Non è configurato un indirizzo mail di piattaforma', 'Error'));

        }

        $info_piattaforma = $this->get_info_piattaforma($id_piattaforma);
        $template = JPATH_COMPONENT . '/models/template/new_tutor_mail.tpl';

//         modidifca custom per ausind,
        if ($info_piattaforma['mail_from_default'] == 1) {

//            ricavo alias e name dalla piattaforma di default
            $piattaforma_default = $this->get_info_piattaforma_default();

            $info_piattaforma["alias"] =$piattaforma_default['alias'];
            $info_piattaforma["name"] = $piattaforma_default['name'];
            $info_piattaforma["dominio"] = $piattaforma_default['dominio'];
        }




        $mailer = JFactory::getMailer();
        $mailer->setSender($sender);
        $mailer->addRecipient($recipients);
        $mailer->setSubject('Registrazione  ' . $info_piattaforma["name"]);


        $smarty = new EasySmarty();
        $smarty->assign('company_name', $nome_societa);
        $smarty->assign('user_name', $company_user["piva"]);
        $smarty->assign('user_password', $company_user["password"]);
        $smarty->assign('piattaforma_name', $info_piattaforma["alias"]);
        $smarty->assign('piattaforma_link', 'https://' . $info_piattaforma["dominio"]);


        $mailer->setBody($smarty->fetch_template($template, null, true, false, 0));
        $mailer->isHTML(true);

        if (!$mailer->Send()) {
//            throw new RuntimeException('Error sending mail', E_USER_ERROR);
            utilityHelper::logMail('new_tutor_mail', $sender, $recipients, 0);

        }

        utilityHelper::logMail('new_tutor_mail', $sender, $recipients, 1);
        return true;
    }


    ////////////////////////////////////// FORUM ////////////////////

    public function _create_company_forum($company_group_id, $company_name, $id_piattaforma)
    {


        try {
            $parent_id = 0;
            $forum_name = 'Forum aziendale ' . $company_name;
            $alias = str_replace(' ', '-', filter_var(strtolower($forum_name), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
            $access_type = 'joomla.group';
            $access = 2;
            $pub_access = $admin_access = $company_group_id;
            $pub_recurse = $admin_recurse = 0;
            $published = 1;
            $description = $headerdesc = '';//'Forum di discussione della società ' . $company_name;
            $params = '{"display":{"index":{"parent":"3","children":"3"}}}';

            $query = 'INSERT INTO #__kunena_categories (parent_id, name, alias, accesstype, access, pub_access, pub_recurse, admin_access , admin_recurse , published, description, headerdesc, params)';
            $query = $query . 'VALUES ( ' . $parent_id . ', \'' . $forum_name . '\', \'' . $alias . '\', \'' . $access_type . '\', ' . $access . ',' . $pub_access . ',' . $pub_recurse . ',' . $admin_access . ',' . $admin_recurse . ',' . $published . ', \'' . $description . '\', \'' . $headerdesc . '\', \'' . $params . '\')';

            $this->_db->setQuery($query);
            if (false === ($results = $this->_db->query()))
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);

            // ID della categoria del forum appena creata
            $query = 'SELECT LAST_INSERT_ID() AS id';
            $this->_db->setQuery($query);
            if (false === ($results = $this->_db->loadAssoc())) {
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            }
            $company_forum_id = filter_var($results['id'], FILTER_VALIDATE_INT);
            if (empty($company_forum_id)) {
                throw RuntimeException('Cannot get forum ID from database', E_USER_ERROR);
            }

            // inserisco nella tabella alias altrimento il link al forum non è cliccabile
            $alias_type = 'catid';
            $query = 'INSERT INTO #__kunena_aliases (alias, type, item)';
            $query = $query . 'VALUES ( \'' . $alias . '\', \'' . $alias_type . '\',' . $company_forum_id . ')';
            $this->_db->setQuery($query);
            if (false === ($results = $this->_db->query()))
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);


            // se va a buon fine
            //tutor aziendale diventa  il moderatore del forum
            $mu = new gglmsModelUsers();
            $tutor_id = $mu->get_tutor_aziendale($company_group_id);
            $mu->set_user_forum_moderator($tutor_id, $company_forum_id);


        } catch (Exception $e) {
            DEBUGG::error($e, '_create_company_forum');
            return false;


        }


    }

    public function _get_company_forum($company_group_id)
    {

        try {

            $query = $this->_db->getQuery(true)
                ->select('c.id')
                ->from('#__kunena_categories as c')
                ->where("c.parent_id = " . 0)
                ->where("c.pub_access =" . $company_group_id);

            $this->_db->setQuery($query);


            if (false === ($results = $this->_db->loadResult())) {
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            }

            return isset($results) ? $results : null;


        } catch (Exception $e) {

            DEBUGG::error($e, '_get_company_forum');
            return false;
        }


    }

    public function _check_corso_forum($id_societa, $id_gruppo_corso)
    {


        try {

            $company_forum = $this->_get_company_forum($id_societa);
            if (null === $company_forum) {

                // se sono arrivato qui il company forum deve esistere
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            }

            $query = $this->_db->getQuery(true)
                ->select('c.id')
                ->from('#__kunena_categories as c')
                ->where("c.parent_id = " . $company_forum)
                ->where("c.pub_access =" . $id_gruppo_corso);

            $this->_db->setQuery($query);


            if (false === ($results = $this->_db->loadRow())) {
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            }

            return isset($results) ? $results : null;


        } catch (Exception $e) {

            DEBUGG::error($e, '_check_corso_forum');
            return false;
        }


    }

    public function _create_corso_forum($id_societa, $id_gruppo_corso, $nome_societa)
    {

        // il forum del corso è figlio del forum aziendale
        $parent_id = $this->_get_company_forum($id_societa);
        if (null === $parent_id) {
            // se sono arrivato qui il company forum deve esistere
            throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
        }

//        if (false == ($titolo_corso = $this->get_info_corso($id_gruppo_corso)["title"])) {
//            throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
//
//        }

        if (false == ($titolo_corso = $this->_info_corso["titolo"])) {
            throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);

        }

        // alias deve essere combinazione di nome corso, nome società perchè kunena lo vuole unique nella tabella alias
        $id_gruppo_tutor_aziendale = $this->_config->getConfigValue('id_gruppo_tutor_aziendale');


        $alias = str_replace(' ', '-', filter_var(strtolower($titolo_corso . ' - ' . $nome_societa), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
        $access_type = 'joomla.group';
        $access = 1;
        $pub_access = $id_gruppo_corso;
        $admin_access = $id_gruppo_tutor_aziendale;
        $pub_recurse = $admin_recurse = 0;
        $published = 1;
        $description = $headerdesc = '';//'Forum di discussione della corso ' . $titolo_corso;
        $params = '{"access_post":["6","2","8"],"access_reply":["6","2","8"],"display":{"index":{"parent":"3","children":"3"}}}'; //todo check access post e access reply

        $query = 'INSERT INTO #__kunena_categories (parent_id, name, alias, accesstype, access, pub_access, pub_recurse, admin_access , admin_recurse , published, description, headerdesc, params)';
        $query = $query . 'VALUES ( ' . $parent_id . ', \'' . $titolo_corso . '\', \'' . $alias . '\', \'' . $access_type . '\', ' . $access . ',' . $pub_access . ',' . $pub_recurse . ',' . $admin_access . ',' . $admin_recurse . ',' . $published . ', \'' . $description . '\', \'' . $headerdesc . '\', \'' . $params . '\')';


        $this->_db->setQuery($query);
        if (false === ($results = $this->_db->query()))
            throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);


        // ID della categoria del forum appena creata
        $query = 'SELECT LAST_INSERT_ID() AS id';
        $this->_db->setQuery($query);
        if (false === ($results = $this->_db->loadAssoc())) {
            throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
        }

        $corso_forum_id = filter_var($results['id'], FILTER_VALIDATE_INT);
        if (empty($corso_forum_id)) {
            throw RuntimeException('Cannot get forum ID from database', E_USER_ERROR);
        }

        // inserisco nella tabella alias altrimento il link al forum non è cliccabile
        $alias_type = 'catid';
        $query = 'INSERT INTO #__kunena_aliases (alias, type, item)';
        $query = $query . 'VALUES ( \'' . $alias . '\', \'' . $alias_type . '\',' . $corso_forum_id . ')';
        $this->_db->setQuery($query);
        if (false === ($results = $this->_db->query())) {
            throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);

        }


        //tutor aziendale diventa  il moderatore del forum
        $mu = new gglmsModelUsers();
        $tutor_id = $mu->get_tutor_aziendale($id_societa);
        $mu->set_user_forum_moderator($tutor_id, $corso_forum_id);


    }

//    public function _set_tutor_aziendale_forum_moderator($company_group_id, $company_forum_id)
//    {
//
//        try {
//            // tutor aziendale  moderatore forum
//            $mu = new gglmsModelUsers();
//
//            $tutor_id = $mu->get_tutor_aziendale($company_group_id);
//            $mu->set_user_forum_moderator($tutor_id, $company_forum_id);
//
//
//        } catch (Exception $e) {
//            DEBUGG::error($e, '_set_forum_moderator');
//            return false;
//
//
//        }
//
//
//    }


}





