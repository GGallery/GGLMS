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
            if (false === ($new_user = $this->create_new_company_user($data))) {
                throw new RuntimeException('Error: cannot create user.', E_USER_ERROR);

            }

//            var_dump($new_user);

            $id_gruppo_societa = $this->_get_id_gruppo_societa($data['username'], $data["vendor"]);
            $id_iscrizione = $this->_generate_id_iscrizione($data['vendor']);

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
//            echo  $query;die;

            $this->_db->setQuery($query);
            if (false === $this->_db->execute())
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);


            if ($this->_send_mail($data["vendor"], $data['email'], $new_user, $coupons, $data['gruppo_corsi']) === false) {
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            }


            $this->_japp->redirect(JRoute::_('/home/genera-coupon'), $this->_japp->enqueueMessage('Coupon creato/i con successo!', 'Success'));

//            $app->enqueueMessage('Gruppo creato con successo!', 'Success')
        } catch (Exception $ex) {

//            echo 'error in insert_coupon';
            var_dump($ex);

        }

    }

    public function _get_id_gruppo_societa($piva, $id_piattaforma)
    {
        // prendo utente username= p.iva

        $query = $this->_db->getQuery(true)
            ->select('u.id')
            ->from('#__users as u')
            ->where('username="' . $piva . '"');


        $this->_db->setQuery($query);
        $user_societa = $this->_db->loadResult();

//        var_dump($user_societa);

        // prendo i gruppi a cui appartiene
        $gruppi_appartenenza_utente = JAccess::getGroupsByUser($user_societa, true);


        // filtro i gruppi a cui appartiene l'utente piva per quelli figli di piattaforma $id_piattaforma
        $query = $this->_db->getQuery(true)
            ->select('ug.id')
            ->from('#__usergroups as ug')
            ->where('parent_id="' . $id_piattaforma . '"')
            ->where('ug.id IN ' . ' (' . implode(',', $gruppi_appartenenza_utente) . ')');


        $this->_db->setQuery($query);
        $id_gruppo_societa = $this->_db->loadResult();

        return $id_gruppo_societa;
    }

    public function create_new_company_user($data)
    {
        try {
            // esiste gia' l'username?
            $user_id = $this->_check_username($data['username']);

            if (empty($user_id)) {

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

                $this->_set_user_tutor($user_id);


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
//                debug::msg('Nuovo utente aggiornata anagrafica');

//                // creo nuovo forum
//                /** @todo sostituire il 16 della riga sotto con un sistema che prelevi l'ID del gruppo tutor da DB */
//                if (false === $this->_create_company_forum($user_id, $company_group_id, $data['ragione_sociale'], $data['id_associazione'], 16))
//                    throw new Exception('Errore nella creazione del forum', E_USER_ERROR);
//
            }

            $res = array('user_id' => $user_id, 'password' => isset($password) ? $password : null, 'id_gruppo_societa' => $company_group_id);

            return $res;
        } catch (Exception $e) {


            var_dump($e);
            echo('eccezione in create_new_company_user');
            //debug::exception($e);
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

    private function _set_user_tutor($user_id)
    {

        $tutor_group_id = $this->_config->getConfigValue('id_gruppo_tutor_aziendale');

        $insertquery_map = 'INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (' . $user_id . ', ' . $tutor_group_id . ')';
        $this->_db->setQuery($insertquery_map);
        $this->_db->execute();


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

    private function _send_mail($id_gruppo_piattaforma, $email_ref_aziendale, $new_user, $coupons, $id_gruppo_corso)
    {

        //get email di riferimento
        $query = 'SELECT dominio, email_riferimento FROM #__usergroups_details WHERE group_id=' . $id_gruppo_piattaforma . ' LIMIT 1';
        $this->_db->setQuery($query);
        if (false === ($results = $this->_db->loadRow()))
            throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);

        $data['associazione_name'] = $results[0];
        $data['associazione_url'] = 'http://www.' . strtolower($results[0]) . '/';
        $data['email_riferimento'] = $results[1];

        //todo per evitare di mandare mail a caso, da cancellare
        $data['email_riferimento'] = 'francesca.bagni@ggallery.it';
        $email_ref_aziendale =  'francesca.bagni@ggallery.it';

        // get course info
        $query = 'SELECT * FROM #__usergroups WHERE id=' . $id_gruppo_corso . ' LIMIT 1';
        $this->_db->setQuery($query);
        if (false === ($course_info = $this->_db->loadRow()))
            throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);


        require_once(JPATH_COMPONENT . '/libraries/smarty/EasySmarty.class.php');
        $mailer = JFactory::getMailer();
        $mailer->setSender($data['email_riferimento']);
        $recipient = array($data['email_riferimento'], $email_ref_aziendale);//, 'martina@ggallery.it');
        $mailer->addRecipient($recipient);
        $mailer->setSubject('Coupon corso ' . $data['associazione_name']);

//
        $template = JPATH_COMPONENT . '/models/template/coupons_mail.tpl';

        $smarty = new EasySmarty();
        $data['password'] = $new_user['password'];
        $smarty->assign('ausind', $data);
        $smarty->assign('coupons', $coupons);
        $smarty->assign('coursename', $course_info['title']);
        $mailer->setBody($smarty->fetch_template($template, null, true, false, 0));
        $mailer->isHTML(true);

        if (!$mailer->Send())
            throw new RuntimeException('Error sending mail', E_USER_ERROR);


        return true;

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

}
