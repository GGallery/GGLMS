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
        $this->societa_venditrici = utilityHelper::getPiattaformeByUser(true);

    }

    public function __destruct()
    {

    }

    // entry point
    public function insert_coupon($data, $from_api=false, $from_xml=false)
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
                throw new RuntimeException("durata coupon non specificata", E_USER_ERROR);
            }

            // se non è specificato nel form il default è coupon abilitati.
            $data['abilitato'] = $data['abilitato'] == 'on' ? 1 : $this->_config->getConfigValue('coupon_active_default') == 0 ? 1 : 0;
            $data['stampatracciato'] = $data['stampatracciato'] == 'on' ? 1 : 0;
            $data['trial'] = $data['trial'] == 'on' ? 1 : 0;
            $data['venditore'] = isset($data['venditore']) ? $data["venditore"] : NULL;
            $data['genera_coupon_tipi_coupon'] = isset($data['genera_coupon_tipi_coupon']) ? $data['genera_coupon_tipi_coupon'] : NULL;


            // se non esiste crea utente ( tutor ) legato alla company
            // esiste gia' l'username (P.iva) ?
            $user_id = $this->_check_username((string)$data['username'], $from_api);
            // controllo se il check è andato in errore
            if (is_array($user_id))
                throw new RuntimeException("check username in errore:" . $user_id['error'], E_USER_ERROR);

            $company_user = null;
            $new_societa = false;

            if (empty($user_id)) {

                // prima di procedere controllo se lo usergroups è già esistente visto che viene inizializzato come la ragione sociale e joomla non accetta gruppi con lo stesso nome
                $check_usergroups = $this->_check_usergroups((string)$data['ragione_sociale'], $from_api);
                if (is_array($check_usergroups))
                    throw new RuntimeException("check usergroups in errore:" . $check_usergroups['errore'], E_USER_ERROR);

                if (!is_null($check_usergroups))
                    throw new RuntimeException("duplicate user_groups", E_USER_ERROR);

                $new_societa = true;
                $company_user = $this->create_new_company_user($data, $from_api);
                // controllo eventuali errori
                if (is_null($company_user))
                    throw new RuntimeException("company user creation failed", E_USER_ERROR);

            } else {
                // se l'utente esiste già, la parte del from aziendale e disabilitata
                // non mi arriva l'id piattaforma
                // lo ricavo dall'utente partita iva
                $model_user = new gglmsModelUsers();
                //$data["id_piattaforma"] = $model_user->get_user_piattaforme($user_id, $from_api)[0]->value;
                $_tmp_id_piattaforma = $model_user->get_user_piattaforme($user_id, $from_api);
                // controllo eventuali errori
                if (is_null($_tmp_id_piattaforma))
                    throw new RuntimeException("no user piattaforme found", E_USER_ERROR);

                $data["id_piattaforma"] = $_tmp_id_piattaforma[0]->value;

            }

            $id_iscrizione = $this->_generate_id_iscrizione($data['id_piattaforma']);
            $info_societa = $this->_get_info_gruppo_societa($data['username'], $data["id_piattaforma"], $from_api);
            // controllo eventuali errori
            if (is_null($info_societa)
                || !is_array($info_societa))
                throw new RuntimeException("nessun gruppo societa trovato", E_USER_ERROR);

            $id_gruppo_societa = $info_societa["id"];
            $nome_societa = $info_societa["name"];

            $this->_info_corso = $this->get_info_corso($data["gruppo_corsi"], $from_api);
            if (is_null($this->_info_corso)
                || !is_array($this->_info_corso))
                throw new RuntimeException("nessun info corso trovato", E_USER_ERROR);

            $prefisso_coupon = $this->_info_corso["prefisso_coupon"];


            $coupons = array();
            $values = array();


            // campo unico il set di coupon composto da idPiattaformaVenditrice_stringone senza senso basato sul now
            for ($i = 0; $i < intval($data['qty']); $i++) {

                /*
                $coupons[$i] = $this->_generate_coupon($prefisso_coupon, $nome_societa);

                // se abilitato -> dataabilitazione = now

                $values[] = sprintf("('%s', '%s', %d, '%s', '%s', %d, %d , %d , %d , %d, %d , '%s', %d, '%s', '%s')",
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
                    $data['venditore'],
                    $data['id_piattaforma'],
                    $data['genera_coupon_tipi_coupon'],
                    (isset($data['ref_skill']) && $data['ref_skill'] != "") ? $data['ref_skill'] : 'NULL'
                );
                */

                // accentro tutto in una funzione specifica di inserimento
                $insert_coupon = $this->make_insert_coupon($prefisso_coupon, $nome_societa, $id_iscrizione, $durata_coupon, $id_gruppo_societa, $data);
                if (is_null($insert_coupon))
                    throw new Exception($insert_coupon, E_USER_ERROR);

                $values[] = $insert_coupon;

            }


            // li inserisco nel DB
            $query = 'INSERT INTO #__gg_coupon (coupon,
                                                creation_time,
                                                abilitato,
                                                id_iscrizione,
                                                data_abilitazione,
                                                durata,
                                                attestato,
                                                id_societa,
                                                id_gruppi,
                                                stampatracciato,
                                                trial,
                                                venditore,
                                                gruppo,
                                                tipologia_coupon,
                                                ref_skill) VALUES ' . join(',', $values);
            $this->_db->setQuery($query);
            if (false === $this->_db->execute()) {
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            }


            // leggo da configurazione se mandare le mail con i coupon generati
            // non invio email singola se sto generando i coupon da xml perchè ne invierò una cumulativa
            $send_mail = $this->_config->getConfigValue('mail_coupon_acitve');
            if ($send_mail == 1
                && !$from_xml) {


                // send new credentials
                if ($new_societa) {

                    if ($this->send_new_company_user_mail($company_user,
                            $nome_societa,
                            $id_gruppo_societa,
                            $data["id_piattaforma"],
                            $data['email_coupon'],
                            $from_api) === false) {
                        throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
                    }

                }

                if ($this->send_coupon_mail($coupons,
                        $data["id_piattaforma"],
                        $nome_societa,
                        $id_gruppo_societa,
                        $data['email_coupon'],
                        $from_api) === false) {
                    throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
                }


            }


            // leggo da configurazione se creare o meno forum
            $genera_forum = $this->_config->getConfigValue('genera_forum');
            if ($genera_forum == 1) {

                $forum_corso = $this->_check_corso_forum($id_gruppo_societa, $data['gruppo_corsi'], $from_api);

                if (empty($forum_corso)) {

                    if (false === ($forum_corso = $this->_create_corso_forum($id_gruppo_societa,
                            $data['gruppo_corsi'],
                            $nome_societa,
                            null,
                            $from_api))) {
                        throw new RuntimeException('Error: cannot create forum corso', E_USER_ERROR);
                    }
                }

            }

            // se sto generando il coupon da xml ritorno il codice che devo associare all'utente
            if ($from_xml) {
                return array('coupons' => $coupons,
                            'nome_societa' => $nome_societa,
                            'company_user' => $company_user,
                            'id_gruppo_societa' => $id_gruppo_societa,
                            'id_piattaforma' => $data["id_piattaforma"],
                            'email_coupon' => $data['email_coupon']
                );
            }


            return $id_iscrizione;

        } catch (Exception $e) {
            if ($from_api)
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_response');

            DEBUGG::error($e, 'insert_coupon', 0, true);
            return null;
        }

    }

    public function _get_info_gruppo_societa($piva, $id_piattaforma, $from_api=false)
    {
        // prendo utente username= p.iva

        try {
            $query = $this->_db->getQuery(true)
                ->select('u.id')
                ->from('#__users as u')
                ->where('username="' . $this->_db->escape($piva) . '"');


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
            if ($from_api)
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_response');

            DEBUGG::error($e, __FUNCTION__);
            return null;
        }


    }

    public function create_new_company_user($data, $from_api=false)
    {
        try {

            // genero una password casuale
            $password = $this->_generate_pwd(8);
            $salt = JUserHelper::genRandomPassword(32);
            $crypt = JUserHelper::getCryptedPassword($password, $salt) . ':' . $salt;

            // creo nuovo user
            $query = sprintf('INSERT INTO #__users (name, username, password, email, sendEmail, registerDate, activation) VALUES (\'%s\', \'%s\', \'%s\', \'%s\', 0, NOW(), \'\')', $this->_db->escape($data['ragione_sociale']), $this->_db->escape($data['username']), $crypt, $data['email']);
            $this->_db->setQuery($query);
            if (false === $this->_db->query())
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
//                debug::msg('Nuovo utente ' . $data['username'] . ':' . $password . ' inserito.');

            // id del nuovo user
            $user_id = $this->_db->insertid();


            // creo nuovo gruppo figlio di piattaforma e lo associo all'utente che ho appena creato
            if (false === ($company_group_id = $this->_create_company_group($user_id,
                    $data['ragione_sociale'],
                    $data["id_piattaforma"],
                    $from_api)))
                throw new Exception('Errore nella creazione del gruppo', E_USER_ERROR);


            $new_user = new gglmsModelUsers();
            // controllo impostazione del tutor aziendale
            $_set_user_tutor = $new_user->set_user_tutor($user_id, 'aziendale', $from_api);
            if (!$_set_user_tutor)
                throw new RuntimeException("impossibile impostare il tutor aziendale", E_USER_ERROR);

            // inserisco in comprofiler
            $query = 'INSERT INTO #__comprofiler (id, user_id, cb_cognome, cb_ateco) VALUES (
                ' . $user_id . ',
                ' . $user_id . ',
                \'' . $this->_db->escape($data['ragione_sociale']) . '\' ,
                \'' . $data['ateco'] . '\'
                )';

            $this->_db->setQuery($query);
            if (false === $this->_db->query()) {
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            }


            // leggo da configurazione se creare o meno forum compagnia
            $genera_forum = $this->_config->getConfigValue('genera_forum');
            if ($genera_forum == 1) {
                if (false === $this->_create_company_forum($company_group_id,
                        $data['ragione_sociale'],
                        $data['vendor'],
                        $from_api)) {
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
            if ($from_api)
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_response');

            DEBUGG::error($e, __FUNCTION__);
            return null;
        }
        //return false;
    }

    private function _check_username($username, $from_api=false)
    {
        try {
            $query = 'SELECT id FROM #__users WHERE username=\'' . $this->_db->escape($username) . '\' LIMIT 1';
            $this->_db->setQuery($query);
            if (false === ($results = $this->_db->loadRow())) {
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            }

            return isset($results[0]) ? $results[0] : null;
        }
        catch (Exception $e) {
            if ($from_api)
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_response');

            $_ret['error'] = $e->getMessage();
            return $_ret;
        }
    }

    public function _check_usergroups($usergroup, $from_api=false) {

        try {

            $query = "SELECT id FROM #__usergroups WHERE title = '" . $this->_db->escape($usergroup) . "'";
            $this->_db->setQuery($query);

            if (false === ($results = $this->_db->loadRow())) {
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            }

            return isset($results[0]) ? $results[0] : null;
        }
        catch (Exception $e) {
            if ($from_api)
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_response');

            $_ret['error'] = $e->getMessage();
            return $_ret;

        }

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
     * @param boolean $from_api
     * @return int Ritorna l'ID del gruppo appena creato o FALSE in caso di errore.
     */
    private function _create_company_group($company_id, $company_name, $piattaforma_group_id, $from_api=false)
    {

        try {
            // creo un gruppo figlio della piattaforma
            $insertquery_group = 'INSERT INTO #__usergroups (parent_id, lft,rgt, title) VALUES(';
            $insertquery_group = $insertquery_group . $piattaforma_group_id . ',';
            $insertquery_group = $insertquery_group . '0' . ',';
            $insertquery_group = $insertquery_group . '0' . ',';
            $insertquery_group = $insertquery_group . '\'' . $this->_db->escape($company_name) . '\'' . ')';

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

        } catch (Exception $e) {
            if ($from_api)
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_response');
            else
                DEBUGG::error($e, __FUNCTION__);

            //var_dump($e);
            //echo 'errore in _create_company_group';
            return false;


        }

    }

    private function _generate_coupon($prefisso_coupon, $nome_societa)
    {
        // controllo lunghezza del nome della società
        $_check_len = strlen($nome_societa);
        if ($_check_len < 3) {
            for ($i = $_check_len; $i < 3; $i++) {
                $nome_societa .= "s";
            }
        }

        // prende il nome società fino al quarto carattere - quindi se la prima parola è UN' nel codice coupon finisce '
        // tolgo dal prefisso tutto ciò che non è lettera
        $_prefisso_az = preg_replace('~[^a-zA-Z]~i', '', substr($nome_societa, 0, 3));
        // controllo la lunghezza della stringa
        $_str_leng = strlen($_prefisso_az);
        // se minore di 3 caratteri accodo "s"
        if ($_str_leng < 3) {
            for ($i = $_str_leng; $i < 3; $i++) {
                $_prefisso_az .= "s";
            }
        }

        //$var_1 = 'X-' . str_replace(' ', '_', $prefisso_coupon) . substr($nome_societa, 0, 3);
        $var_1 = 'X-' . str_replace(' ', '_', $prefisso_coupon) . $_prefisso_az;
        $var_2 = str_replace('.', 'p', str_replace('0', 'k', uniqid('', true))); // no zeros , no dots

        return str_replace(' ', '_', $var_1 . $var_2);

    }

    // cambiata accessibilità da private a public
    public function _generate_id_iscrizione($id_piattaforma)
    {
        $created_by=  $this->_userid === null ? '0' :  $this->_userid;
        return $id_piattaforma . '_' . uniqid(time()) . '_' . $created_by  ;
    }

//////////////////////////////  MAIL   /////////////////////

    // MAIL COUPON
    public function send_coupon_mail($coupons,
                                     $id_piattaforma,
                                     $nome_societa,
                                     $id_gruppo_societa,
                                     $email_coupon = '',
                                     $from_api=false)
    {

        try {
            // get recipients --> tutor piattaforma (cc) + tutor aziendale (to)
            if (false == ($recipients = $this->get_coupon_mail_recipients($id_piattaforma, $id_gruppo_societa, $email_coupon, $from_api))) {
                $_msg = 'Non ci sono tutor piattaforma configurati per questa piattaforma';
                if (!$from_api)
                    $this->_japp->redirect(JRoute::_('/home/genera-coupon'), $this->_japp->enqueueMessage($_msg, 'Error'));
                else
                    throw new RuntimeException($_msg, E_USER_ERROR);
            }

            // get sender
            if (false == ($sender = $this->get_mail_sender($id_piattaforma, $from_api))) {
                $_msg = 'Non è configurato un indirizzo mail di piattaforma';
                if (!$from_api)
                    $this->_japp->redirect(JRoute::_('/home/genera-coupon'), $this->_japp->enqueueMessage($_msg, 'Error'));
                else
                    throw new RuntimeException($_msg, E_USER_ERROR);

            }

            // get data
            $info_piattaforma = $this->get_info_piattaforma($id_piattaforma, $from_api);
            if (is_null($info_piattaforma)
                || !is_array($info_piattaforma))
                throw new RuntimeException("nessun info piattaforma", E_USER_ERROR);

            // send mail
            $template = JPATH_COMPONENT . '/models/template/coupons_mail.tpl';

            // se viene fornita una mail a cui inviare i coupon  non la mando al tutor aziendale ma alla mail fornita
            $to = $email_coupon != '' ? $email_coupon : $recipients["to"]->email;

            // verifico da impostazione se voglio vedere il name del tutor nel titolo della E-mail di generazione
            // proprietà company_name Es. Generazione coupon NameTutor
            $mostra_nome_societa = $this->_config->getConfigValue('nome_azienda_intestazione_email_coupon');
            if ((int)$mostra_nome_societa == 0
                && !is_null($mostra_nome_societa))
                $nome_societa = "";

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
                $piattaforma_default = $this->get_info_piattaforma_default($from_api);
                if (is_null($piattaforma_default)
                    || !is_array($piattaforma_default))
                    throw new RuntimeException("nessuna piattaforma di default trovata", E_USER_ERROR);

                $info_piattaforma["alias"] = $piattaforma_default['alias'];
                $info_piattaforma["name"] = $piattaforma_default['name'];
                $info_piattaforma["dominio"] = $piattaforma_default['dominio'];
                //
            }

            $smarty = new EasySmarty();
            $smarty->assign('coupons', $coupons);
            $smarty->assign('coupons_count', count($coupons));
            $smarty->assign('course_name', $this->_info_corso["titolo"]);
            $smarty->assign('company_name', $nome_societa);
            $smarty->assign('piattaforma_name', $info_piattaforma["alias"]);
            $smarty->assign('recipient_name', $recipients["to"]->name);
            $smarty->assign('piattaforma_link', 'https://' . $info_piattaforma["dominio"]);


            $mailer->setBody($smarty->fetch_template($template, null, true, false, 0));
            $mailer->isHTML(true);

            // rimosso il riferimento a $recipients["to"]->email
            if (!$mailer->Send()) {
                //            throw new RuntimeException('Error sending mail', E_USER_ERROR);
                utilityHelper::logMail('coupons_mail_send_error',
                    $sender,
                    implode(",", $to),
                    0,
                    implode(", ", $recipients['cc']),
                    $this->_info_corso["idgruppo"]);
            }

            //log mail sent
            // rimosso il riferimento a $recipients["to"]->email
            utilityHelper::logMail('coupons_mail',
                $sender,
                implode(",", $to),
                1,
                implode(", ", $recipients['cc']),
                $this->_info_corso["idgruppo"]);
            return true;
        }
        catch (Exception $e) {
            if ($from_api)
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_response');

            return false;
        }

    }

    public function get_mail_sender($id_piattaforma, $from_api=false)
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
            if ($from_api)
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_response');

            DEBUGG::query($query);
            DEBUGG::log($e, __FUNCTION__, 1);

            return false;
        }


    }

    public function get_coupon_mail_recipients($id_piattaforma,
                                               $id_gruppo_societa,
                                               $email_coupon = '',
                                               $from_api=false)
    {

        try {
            // TO = tutor aziendale
            // CC = tutor di piattaforma

            // utente loggato
            $to = null;
            $cc = array();

            $user = new gglmsModelUsers();
            $tutor_piattaforma_id_list = $user->get_all_tutor_piattaforma($id_piattaforma, $from_api);
            // controllo integrità tutor piattaforma
            if (is_null($tutor_piattaforma_id_list)
                || !is_array($tutor_piattaforma_id_list))
                throw new RuntimeException("nessun tutor piattaforma trovato", E_USER_ERROR);

            foreach ($tutor_piattaforma_id_list as $tutor_id) {
                array_push($cc, $this->get_user_info($tutor_id, 'email'));
            }

            $tutor_az = $user->get_tutor_aziendale($id_gruppo_societa, $from_api);
            // controllo integrità tutor azienda
            if (is_null($tutor_az))
                throw new RuntimeException("nessun tutor aziendale trovato", E_USER_ERROR);


            $to->email = $email_coupon == '' ? $this->get_user_info($tutor_az, 'email') : $email_coupon;
            $to->name = $this->get_user_info($tutor_az, 'name');

            //        $to->email = $this->get_user_info($this->_userid, 'email');
            //        $to->name = $this->get_user_info($this->_userid, 'name');


            $result = array('to' => $to, 'cc' => $cc);

            return empty($to) || empty($cc) ? false : $result;
        }
        catch (Exception $e) {
            if ($from_api)
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_response');

            return false;
        }

    }

    public function get_user_info($user_id, $field)
    {
        $user = JFactory::getUser($user_id);
        $info = $user->get($field);

        return $info;
    }

    public function get_info_corso($id_gruppo_corso, $from_api=false)
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
            if ($from_api)
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_response');

            DEBUGG::error($e, __FUNCTION__);
            return null;
        }


    }

    public function get_info_piattaforma($id_piattaforma, $from_api=false)
    {


        try {

            $query = $this->_db->getQuery(true)
                ->select('ug.id as id , ug.title as name,
                        ud.dominio as dominio,
                        ud.alias as alias, ud.mail_from_default as mail_from_default')
                ->from('#__usergroups as ug')
                ->join('inner', '#__usergroups_details AS ud ON ug.id = ud.group_id')
                ->where('id=' . $id_piattaforma);

            $this->_db->setQuery($query);
            $info_piattaforma = $this->_db->loadAssoc();

            return $info_piattaforma;

        } catch (Exception $e) {
            if ($from_api)
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_response');

            DEBUGG::log(json_encode($e->getMessage()), 'api_genera_coupon_response_' . __FUNCTION__ . '_error', 0, 1, 0 );
            DEBUGG::error($e, 'get_info_piattaforma');
            return null;
        }


    }


    public function get_info_piattaforma_default($from_api=false, $db_option = array())
    {

        try {

            // gestione db esterno
            if (count($db_option) > 0)
                $this->_db = JDatabaseDriver::getInstance($db_option);

            $query = $this->_db->getQuery(true)
                ->select('ug.id as id , ug.title as name, ud.dominio as dominio, ud.alias as alias, ud.mail_from_default as mail_from_default')
                ->from('#__usergroups as ug')
                ->join('inner', '#__usergroups_details AS ud ON ug.id = ud.group_id')
                ->where('ud.is_default = 1');


            $this->_db->setQuery($query);
            $info_piattaforma = $this->_db->loadAssoc();

            return $info_piattaforma;

        } catch (Exception $e) {
            if ($from_api)
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_response');

            DEBUGG::error($e, 'get_info_piattaforma');
            return null;
        }


    }

    // MAIL REGISTRAZIONE NUOVA SOCIETA'

    public function send_new_company_user_mail($company_user,
                                               $nome_societa,
                                               $id_gruppo_societa,
                                               $id_piattaforma,
                                               $email_coupon = '',
                                               $from_api=false)
    {

        // se viene fornita una mail a cui inviare i coupon  non la mando al tutor aziendale ma alla mail fornita
//        $recipients = $email_coupon == '' ? $company_user["email"] : $email_coupon;
//        if (false == $recipients) {
//            DEBUGG::log($recipients, 'send_new_company_user_mail');
//        }

        try {
            // get recipients --> tutor piattaforma (cc) + tutor aziendale (to) --
            if (false == ($recipients = $this->get_coupon_mail_recipients($id_piattaforma,
                    $id_gruppo_societa,
                    $email_coupon,
                    $from_api))) {
                $_msg = 'Non ci sono tutor piattaforma configurati per questa piattaforma';
                if (!$from_api)
                    $this->_japp->redirect(JRoute::_('/home/genera-coupon'), $this->_japp->enqueueMessage($_msg, 'Error'));
                else
                    throw new Exception($_msg, 1);

            }


            // get sender
            if (false == ($sender = $this->get_mail_sender($id_piattaforma, $from_api))) {
                $_msg = 'Non è configurato un indirizzo mail di piattaforma';
                if (!$from_api)
                    $this->_japp->redirect(JRoute::_('/home/genera-coupon'), $this->_japp->enqueueMessage($_msg, 'Error'));
                else
                    throw new Exception($_msg, 1);

            }

            $info_piattaforma = $this->get_info_piattaforma($id_piattaforma, $from_api);
            if (is_null($info_piattaforma)
                || !is_array($info_piattaforma))
                throw new RuntimeException("nessun info piattaforma", E_USER_ERROR);

            $template = JPATH_COMPONENT . '/models/template/new_tutor_mail.tpl';

            //         modidifca custom per ausind,
            if ($info_piattaforma['mail_from_default'] == 1) {

                //            ricavo alias e name dalla piattaforma di default
                $piattaforma_default = $this->get_info_piattaforma_default($from_api);
                if (is_null($piattaforma_default)
                    || !is_array($piattaforma_default))
                    throw new RuntimeException("nessuna piattaforma di default trovata", E_USER_ERROR);

                $info_piattaforma["alias"] = $piattaforma_default['alias'];
                $info_piattaforma["name"] = $piattaforma_default['name'];
                $info_piattaforma["dominio"] = $piattaforma_default['dominio'];
            }

            // se viene fornita una mail a cui inviare i coupon  non la mando al tutor aziendale ma alla mail fornita
            $to = $email_coupon != '' ? $email_coupon : $recipients["to"]->email;


            $mailer = JFactory::getMailer();
            $mailer->setSender($sender);
            //        $mailer->addRecipient($recipients);
            $mailer->addRecipient($to);
            $mailer->addCc($recipients["cc"]);
            $mailer->setSubject('Registrazione  ' . $info_piattaforma["alias"]);


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
        catch (Exception $e) {
            if ($from_api)
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_response');

            return false;
        }
    }


    ////////////////////////////////////// FORUM ////////////////////

    public function _create_company_forum($company_group_id, $company_name, $id_piattaforma = null, $from_api=false)
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
            $query = $query . 'VALUES ( ' . $parent_id . ',
                                            \'' . $this->_db->escape($forum_name) . '\',
                                            \'' . $this->_db->escape($alias) . '\',
                                            \'' . $access_type . '\',
                                            ' . $access . ',
                                            ' . $pub_access . ',
                                            ' . $pub_recurse . ',
                                            ' . $admin_access . ',
                                            ' . $admin_recurse . ',
                                            ' . $published . ',
                                            \'' . $this->_db->escape($description) . '\',
                                            \'' . $this->_db->escape($headerdesc) . '\',
                                            \'' . $params . '\')';

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
                throw new RuntimeException('Cannot get forum ID from database', E_USER_ERROR);
            }

            // inserisco nella tabella alias altrimento il link al forum non è cliccabile
            $alias_type = 'catid';
            $query = 'INSERT INTO #__kunena_aliases (alias, type, item)';
            $query = $query . 'VALUES ( \'' . $this->_db->escape($alias) . '\', \'' . $alias_type . '\',' . $company_forum_id . ')';
            $this->_db->setQuery($query);
            if (false === ($results = $this->_db->query()))
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);


            // se va a buon fine
            //tutor aziendale diventa  il moderatore del forum
            $mu = new gglmsModelUsers();

            // controllo integrità tutor_id
            $tutor_id = $mu->get_tutor_aziendale($company_group_id, $from_api);
            if (is_null($tutor_id))
                throw new RuntimeException("nessun tutor_id trovato", E_USER_ERROR);

            // controllo impostazione moderatore
            $_set_moderator = $mu->set_user_forum_moderator($tutor_id, $company_forum_id, $from_api);
            if (!$_set_moderator)
                throw new RuntimeException("errore di impostazione moderatore forum", E_USER_ERROR);

            return true;

        } catch (Exception $e) {
            if ($from_api)
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_response');

            DEBUGG::error($e, __FUNCTION__);
            return false;

        }


    }

    public function _get_company_forum($company_group_id, $from_api=false)
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
            if ($from_api)
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_response');

            DEBUGG::error($e, '_get_company_forum');
            return false;
        }


    }

    public function _check_corso_forum($id_societa, $id_gruppo_corso, $from_api=false)
    {


        try {

            $company_forum = $this->_get_company_forum($id_societa, $from_api);
            if (null === $company_forum) {
                // se sono arrivato qui il company forum deve esistere
                throw new RuntimeException("nessun forum azienda trovato", E_USER_ERROR);
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
            if ($from_api)
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_response');

            DEBUGG::error($e, '_check_corso_forum');
            return null;
        }


    }

    public function _create_corso_forum($id_societa,
                                        $id_gruppo_corso,
                                        $nome_societa,
                                        $_info_corso = null,
                                        $from_api=false)
    {

        try {
            // il forum del corso è figlio del forum aziendale
            $parent_id = $this->_get_company_forum($id_societa, $from_api);
            if (null === $parent_id) {
                // se sono arrivato qui il company forum deve esistere
                throw new RuntimeException("nessun forum azienda trovato", E_USER_ERROR);
            }

            //        if (false == ($titolo_corso = $this->get_info_corso($id_gruppo_corso)["title"])) {
            //            throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            //
            //        }

            // se impostato $_info_corso significa che non arrivato da questo model ma da fuori
            $_check_titolo_corso = $this->_info_corso["titolo"];
            if (!is_null($_info_corso))
                $_check_titolo_corso = $_info_corso["titolo"];

            //if (false == ($titolo_corso = $this->_info_corso["titolo"])) {
            if (false == ($titolo_corso = $_check_titolo_corso)) {
                //throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
                throw new RuntimeException("Missing titolo corso id_societa:" . $id_societa . " id_gruppo_corso: " . $id_gruppo_corso, E_USER_ERROR);
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

            // inserimento titolo, alias, description, $headerdesc fix apici
            $query = 'INSERT INTO #__kunena_categories (parent_id,
                                                        name,
                                                        alias,
                                                        accesstype,
                                                        access,
                                                        pub_access,
                                                        pub_recurse,
                                                        admin_access ,
                                                        admin_recurse ,
                                                        published,
                                                        description,
                                                        headerdesc,
                                                        params)';
            $query = $query . 'VALUES (' . $parent_id . ',
                                       \'' . $this->_db->escape($titolo_corso) . '\',
                                       \'' . $this->_db->escape($alias) . '\',
                                       \'' . $access_type . '\',
                                       ' . $access . ',
                                       ' . $pub_access . ',
                                       ' . $pub_recurse . ',
                                       ' . $admin_access . ',
                                       ' . $admin_recurse . ',
                                       ' . $published . ',
                                       \'' . $this->_db->escape($description) . '\',
                                       \'' . $this->_db->escape($headerdesc) . '\',
                                       \'' . $params . '\'
                                       )';
            $this->_db->setQuery($query);
            if (false === ($results = $this->_db->query())) {
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            }


            // ID della categoria del forum appena creata
            $query = 'SELECT LAST_INSERT_ID() AS id';
            $this->_db->setQuery($query);
            if (false === ($results = $this->_db->loadAssoc())) {
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            }

            $corso_forum_id = filter_var($results['id'], FILTER_VALIDATE_INT);
            if (empty($corso_forum_id)) {
                throw new RuntimeException('Cannot get forum ID from database', E_USER_ERROR);
            }

            // inserisco nella tabella alias altrimento il link al forum non è cliccabile
            // fix preventivo alias per eventuali singolo apici di troppo
            $alias_type = 'catid';
            $query = 'INSERT INTO #__kunena_aliases (alias, type, item)';
            $query = $query . 'VALUES ( \'' . $this->_db->escape($alias) . '\', \'' . $alias_type . '\',' . $corso_forum_id . ')';
            $this->_db->setQuery($query);
            if (false === ($results = $this->_db->query())) {
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            }


            //tutor aziendale diventa  il moderatore del forum
            $mu = new gglmsModelUsers();
            $tutor_id = $mu->get_tutor_aziendale($id_societa, $from_api);
            // controllo integrità tutor_id
            if (is_null($tutor_id))
                throw new RuntimeException("nessun tutor_id trovato", E_USER_ERROR);

            // controllo impostazione moderatore
            $_set_moderator = $mu->set_user_forum_moderator($tutor_id, $corso_forum_id, $from_api);
            if (!$_set_moderator)
                throw new RuntimeException("errore di impostazione moderatore forum", E_USER_ERROR);

            return true;

        }
        catch (Exception $e) {
            if ($from_api)
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_response');

            return false;
        }
    }

    public function make_insert_coupon($prefisso_coupon, $nome_societa, $id_iscrizione, $durata_coupon, $id_gruppo_societa, $data_coupon, $id_utente = null, $data_utilizzo = null, $db_insert = false) {

        try {

            $coupon = $this->_generate_coupon($prefisso_coupon, $nome_societa);

            // se abilitato -> dataabilitazione = now
            $value = sprintf("('%s', '%s', %d, '%s', '%s', %d, %d , %d , %d , %d, %d , '%s', %d, '%s', '%s', %d, '%s')",
                $coupon,
                date('Y-m-d H:i:s', time()), //  time(), //creation_time
                $data_coupon['abilitato'],
                $id_iscrizione,
                $data_coupon['abilitato'] == 1 ? date('Y-m-d H:i:s', time()) : 'NULL',
                $durata_coupon,
                $data_coupon['attestato'],
                $id_gruppo_societa,
                $data_coupon['gruppo_corsi'],
                $data_coupon['stampatracciato'],
                $data_coupon['trial'],
                $data_coupon['venditore'],
                $data_coupon['id_piattaforma'],
                (isset($data_coupon['genera_coupon_tipi_coupon']) && $data_coupon['genera_coupon_tipi_coupon'] != "") ? $data_coupon['genera_coupon_tipi_coupon'] : 'NULL',
                (isset($data_coupon['ref_skill']) && $data_coupon['ref_skill'] != "") ? $data_coupon['ref_skill'] : 'NULL',
                !is_null($id_utente) ? $id_utente : 'NULL',
                !is_null($data_utilizzo) ? $data_utilizzo : 'NULL'
            );

            // inserisco il singolo valore a database
            if ($db_insert) {

                $query = 'INSERT INTO #__gg_coupon (coupon,
                                                    creation_time,
                                                    abilitato,
                                                    id_iscrizione,
                                                    data_abilitazione,
                                                    durata,
                                                    attestato,
                                                    id_societa,
                                                    id_gruppi,
                                                    stampatracciato,
                                                    trial,
                                                    venditore,
                                                    gruppo,
                                                    tipologia_coupon,
                                                    ref_skill,
                                                    id_utente,
                                                    data_utilizzo) VALUES ' . $value;

                $this->_db->setQuery($query);
                if (false === $this->_db->execute()) {
                    throw new Exception($this->_db->getErrorMsg(), E_USER_ERROR);
                }

                return 1;
            }

            // ritorno la stringa
            return $value;
        }
        catch (Exception $e) {
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return null;
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


}





