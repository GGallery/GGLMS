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


    //todo da spostare a database in tabella config?
    const DEFAULT_LENGHT = 60;


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
        $this->lista_corsi = $this->getGruppiCorsi();
        $this->societa_venditrici = $this->getVenditrici();

    }

    public function __destruct()
    {

    }

    public function insert_coupon($data)
    {

        try {

            $data['attestato'] = $data['attestato'] == 'on' ? 1 : 0;
            $data['stampatracciato'] = $data['stampatracciato'] == 'on' ? 1 : 0;
            $data['abilitato'] = $data['abilitato'] == 'on' ? 1 : 0;

            // se non esiste crea utente ( tutor ) legato alla company
            // esiste gia' l'username (P.iva) ?
            $user_id = $this->_check_username($data['username']);
            $company_user = null;
            $new_societa = false;

            if (empty($user_id)) {
                $new_societa = true;
                if (false === ($company_user = $this->create_new_company_user($data))) {
                    throw new RuntimeException('Error: cannot create user.', E_USER_ERROR);

                }
            }

            $id_iscrizione = $this->_generate_id_iscrizione($data['vendor']);
            $info_societa = $this->_get_info_gruppo_societa($data['username'], $data["vendor"]);
            $id_gruppo_societa = $info_societa["id"];
            $nome_societa = $info_societa["name"];


            $coupons = array();
            $values = array();


            // campo unico il set di coupon composto da idPiattaformaVenditrice_stringone senza senso basato sul now
            for ($i = 0; $i < $data['qty']; $i++) {

                $coupons[$i] = $this->_generate_coupon($data, $id_gruppo_societa);

                // se abilitato -> dataabilitazione = now

                $values[] = sprintf("('%s', '%s', %d, '%s', '%s', %d, %d , %d , %d , %d)",
                    $coupons[$i],
                    date('Y-m-d H:i:s', time()), //  time(), //creation_time
                    $data['abilitato'],
                    $id_iscrizione,
                    $data['abilitato'] == 1 ? date('Y-m-d H:i:s', time()) : 'NULL',
                    self::DEFAULT_LENGHT,
                    $data['attestato'],
                    $id_gruppo_societa,
                    $data['gruppo_corsi'],
                    $data['stampatracciato']
                );

            }


            // li inserisco nel DB
            $query = 'INSERT INTO #__gg_coupon (coupon, creation_time, abilitato, id_iscrizione, data_abilitazione, durata ,attestato, id_societa, id_gruppi, stampatracciato) VALUES ' . join(',', $values);
            $this->_db->setQuery($query);
            if (false === $this->_db->execute())
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);


            //todo scommenta per attivare invio mail

            // send coupon
//            if ($this->send_coupon_mail($coupons, $data["vendor"], $data['gruppo_corsi'], $nome_societa) === false) {
//                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
//            }
//
//            // send new credentials
//            if ($new_societa) {
//
//                if ($this->send_new_company_user_mail($company_user, $nome_societa, $data["vendor"]) === false) {
//                    throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
//                }
//
//            }


            $forum_corso = $this->_check_corso_forum($id_gruppo_societa, $data['gruppo_corsi']);
            if (empty($forum_corso)) {

                if (false === ($forum_corso = $this->_create_corso_forum($id_gruppo_societa, $data['gruppo_corsi'],$nome_societa))) {
                    throw new RuntimeException('Error: cannot create user.', E_USER_ERROR);
                }
            }

            $this->_japp->redirect(('index.php?option=com_gglms&view=genera'), $this->_japp->enqueueMessage('Coupon creato/i con successo!', 'Success'));


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
            if (false === ($company_group_id = $this->_create_company_group($user_id, $data['ragione_sociale'], $data["vendor"])))
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


            // todo scommenta per abilitare i forum
//            if (false === $this->_create_company_forum($company_group_id, $data['ragione_sociale'], $data['vendor'])) {
//                throw new Exception('Errore nella creazione del forum', E_USER_ERROR);
//            }

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

    private function _generate_coupon($data, $id_gruppo_societa)
    {


//        $var_1 = str_replace(' ', '_', $data['prefisso_coupon'] . substr($data['ragione_sociale'], 0, 3)) . str_replace('0', 'k', uniqid('', true)); // no zeros
        $var_1 = str_replace(' ', '_', $data['prefisso_coupon']) . str_replace('0', 'k', uniqid('', true)); // no zeros
        $var_2 = 's' . $id_gruppo_societa . 'c' . $data['gruppo_corsi'];

        return $var_1 . $var_2;
//        return str_replace(' ', '_', $prefisso . substr($usr_ragionesociale, 0, 3)) . str_replace('0', 'k', md5(uniqid('', true))); // no zeros

    }

    private function _generate_id_iscrizione($id_piattaforma)
    {
        return $id_piattaforma . '_' . uniqid(time());
    }

    public function getGruppiCorsi()
    {

        // carico i gruppi dei corsi

        $_config = new gglmsModelConfig();
        $id_gruppo_accesso_corsi = $_config->getConfigValue('id_gruppo_corsi');

        $query = $this->_db->getQuery(true)
            ->select('g.id as value, g.title as text')
            ->from('#__usergroups as g')
            ->where(" g.parent_id =" . $id_gruppo_accesso_corsi);

        $this->_db->setQuery($query);
        $corsi = $this->_db->loadObjectList();


        return $corsi;
    }

    public function getVenditrici()
    {


        $user = new gglmsModelUsers();
        $Juser = JFactory::getUser();
        $user->get_user($Juser->id);


        if ($user->is_venditore($Juser->id)) {
            $società_venditrici = $user->get_user_piattaforme($Juser->id);


        } else {
            echo "l'utente loggato non appartiene al gruppo venditore, non può generare coupon";
        }

        return $società_venditrici;

    }

//////////////////////////////  MAIL   /////////////////////


    // MAIL COUPON
    public function send_coupon_mail($coupons, $id_piattaforma, $id_gruppo_corso, $nome_societa)
    {

        // get recipients --> tutor piattaforma (cc) + utente loggato
        if (false == ($recipients = $this->get_coupon_mail_recipients($id_piattaforma))) {
            $this->_japp->redirect(JRoute::_('/home/genera-coupon'), $this->_japp->enqueueMessage('Non ci sono tutor piattaforma configurati per questa piattaforma', 'Error'));

        }

        // get sender
        if (false == ($sender = $this->get_mail_sender($id_piattaforma))) {
            $this->_japp->redirect(JRoute::_('/home/genera-coupon'), $this->_japp->enqueueMessage('Non è configurato un indirizzo mail di piattaforma', 'Error'));

        }

        // get data
        // get course info
        if (false == ($titolo_corso = $this->get_info_corso($id_gruppo_corso))) {

            DEBUGG::log($titolo_corso, 'send_coupon_mail');

        }


        $info_piattaforma = $this->get_info_piattaforma($id_piattaforma);

        // send mail
        $template = JPATH_COMPONENT . '/models/template/coupons_mail.tpl';


        $mailer = JFactory::getMailer();
        $mailer->setSender($sender);
        $mailer->addRecipient($recipients["to"]->email);
        $mailer->addCc($recipients["cc"]);
        $mailer->setSubject('Coupon corso ' . $titolo_corso);


        $smarty = new EasySmarty();
        $smarty->assign('coupons', $coupons);
        $smarty->assign('coupons_count', count($coupons));
        $smarty->assign('course_name', $titolo_corso);
        $smarty->assign('company_name', $nome_societa);
        $smarty->assign('piattaforma_name', $info_piattaforma["name"]);
        $smarty->assign('recipient_name', $recipients["to"]->name);

        $mailer->setBody($smarty->fetch_template($template, null, true, false, 0));
        $mailer->isHTML(true);

        if (!$mailer->Send())
            throw new RuntimeException('Error sending mail', E_USER_ERROR);

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

    public function get_coupon_mail_recipients($id_piattaforma)
    {

        // TO = utente loggato = venditore
        // CC = tutor di piattaforma

        // utente loggato
        $to = null;
        $cc = array();

        $user = new gglmsModelUsers();
        $tutor_piattaforma_id_list = $user->get_all_tutor_piattaforma($id_piattaforma);

        foreach ($tutor_piattaforma_id_list as $tutor_id) {
            array_push($cc, $this->get_user_info($tutor_id, 'email'));
        }

        $to->email = $this->get_user_info($this->_userid, 'email');
        $to->name = $this->get_user_info($this->_userid, 'name');


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
            $query = 'SELECT * FROM #__usergroups WHERE id=' . $id_gruppo_corso . ' LIMIT 1';
            $this->_db->setQuery($query);
            if (false === ($course_info = $this->_db->loadAssoc())) {

                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            }

            return $course_info["title"];

        } catch (Exception $e) {
            DEBUGG::error($e, 'get_info_corso');
        }


    }

    public function get_info_piattaforma($id_piattaforma)
    {


        try {

            $query = $this->_db->getQuery(true)
                ->select('ug.id as id , ug.title as name, ud.dominio as dominio')
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

    // MAIL REGISTRAZIONE NUOVA SOCIETA'
    public function send_new_company_user_mail($company_user, $nome_societa, $id_piattaforma)
    {

        if (false == $recipients = $company_user["email"]) {
            DEBUGG::log($recipients, 'send_new_company_user_mail');
        }

        // get sender
        if (false == ($sender = $this->get_mail_sender($id_piattaforma))) {
            $this->_japp->redirect(JRoute::_('/home/genera-coupon'), $this->_japp->enqueueMessage('Non è configurato un indirizzo mail di piattaforma', 'Error'));

        }

        $info_piattaforma = $this->get_info_piattaforma($id_piattaforma);

        // send mail
        $template = JPATH_COMPONENT . '/models/template/new_tutor_mail.tpl';


        $mailer = JFactory::getMailer();
        $mailer->setSender($sender);
        $mailer->addRecipient($recipients);


        $smarty = new EasySmarty();
        $smarty->assign('company_name', $nome_societa);
        $smarty->assign('user_name', $company_user["piva"]);
        $smarty->assign('user_password', $company_user["password"]);
        $smarty->assign('piattaforma_name', $info_piattaforma["name"]);
        $smarty->assign('piattaforma_link', ' https://www.' . $info_piattaforma["dominio"]);


        $mailer->setBody($smarty->fetch_template($template, null, true, false, 0));
        $mailer->isHTML(true);

        if (!$mailer->Send())
            throw new RuntimeException('Error sending mail', E_USER_ERROR);

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

    public function _create_corso_forum($id_societa, $id_gruppo_corso,$nome_societa)
    {

        // il forum del corso è figlio del forum aziendale
        $parent_id = $this->_get_company_forum($id_societa);
        if (null === $parent_id) {
            // se sono arrivato qui il company forum deve esistere
            throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
        }

        if (false == ($titolo_corso = $this->get_info_corso($id_gruppo_corso))) {
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
}





