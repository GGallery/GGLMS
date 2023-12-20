<?php
/**
 * Created by IntelliJ IDEA.
 * User: Francesca
 * Date: 26/01/2021
 * Time: 09:10
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.model');
require_once JPATH_COMPONENT . '/models/contenuto.php';
require_once JPATH_COMPONENT . '/models/report.php';
require_once JPATH_COMPONENT . '/models/unita.php';
require_once JPATH_COMPONENT . '/models/config.php';
require_once JPATH_COMPONENT . '/models/generacoupon.php';
require_once JPATH_COMPONENT . '/models/syncdatareport.php';
require_once JPATH_COMPONENT . '/models/syncviewstatouser.php';
require_once JPATH_COMPONENT . '/models/users.php';
require_once JPATH_COMPONENT . '/controllers/zoom.php';
require_once JPATH_COMPONENT . '/controllers/api.php';

class gglmsControllerMt extends JControllerLegacy {

    private $_user;
    private $_japp;
    public $_params;
    public $_db;
    private $_config;
    private $_filterparam;
    public $mail_debug;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();

        $this->_user = JFactory::getUser();
        $this->_db = JFactory::getDbo();
        $this->_config = new gglmsModelConfig();

        $this->_filterparam->id_utente = JRequest::getVar('id_utente');
        $this->_filterparam->id_corso = JRequest::getVar('id_corso');
        $this->_filterparam->anno_ref = JRequest::getVar('anno_ref');
        $this->_filterparam->secret = JRequest::getVar('secret');
        $this->_filterparam->extraoff = JRequest::getVar('extraoff');
        $this->_filterparam->del_ug = JRequest::getVar('del_ug');
        $this->_filterparam->new_ug = JRequest::getVar('new_ug');
        $this->_filterparam->check_ug = JRequest::getVar('check_ug');

        $this->mail_debug = $this->_config->getConfigValue('mail_debug');
        $this->mail_debug = ($this->mail_debug == "" || is_null($this->mail_debug)) ? "luca.gallo@gallerygroup.it" : $this->mail_debug;


    }

    private function encrypt_decrypt($action, $string, $secret_key, $secret_iv) {
        //echo "entrato<br>";
        //echo $string;die;
        $output = null;
        // metodo di crypt
        $encrypt_method = "AES-256-CBC";
        // hash
        $key = hash('sha256', $secret_key);
        // AES-256-CBC si aspetta 16 bytes
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        // cripta la chiave
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } // decripta la chiave
        else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }

    public function test_() {
        try {

            $currentUrl = JUri::getInstance();
            if (!strpos($currentUrl, 'primaelearning.it')) 
                echo "yes";
            else
                echo "no";
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
        $this->_japp->close();

    }

    public function test_xml_import()
    {
        $api = new gglmsControllerApi();
        $ragione_sociale = "Utenti privati skillab";
        $piva = "00000000000";
        $email = "skillabfad@skillab.it";
        $get_corsi = [
            'Iscritti_20220214153157.xml',
            'Corsi_20220214153157.xml',
            'Corsi_20220214150901.xml',
            'Iscritti_20220214150901.xml'
        ];

        echo $api->load_corsi_from_xml(16, $ragione_sociale, $piva, $email, true, $get_corsi);
    }

    public function massive_password_update()
    {

        try {

            $secret = trim($this->_filterparam->secret);
            $local_secret = $this->encrypt_decrypt('encrypt', 'GGallery00!!!__', 'GGallery00__', 'GGallery00__');

            if ($secret != $local_secret)
                throw new Exception("Secret key error", E_USER_ERROR);

            $new_password = 'JCcW*3lY7W';

            $query = $this->_db->getQuery(true);
            $query->update("#__users");
            $query->set("password = " . $this->_db->quote(JUserHelper::hashPassword($new_password)));
            $query->where('id > 295');

            $this->_db->setQuery((string) $query);

            if (!$this->_db->execute())
                throw new Exception("update query ko -> " . $query, E_USER_ERROR);

            echo "FATTO!";

        }
        catch(Exception $e) {
            echo $e->getMessage();
        }

        $this->_japp->close();

    }

    public function sinpe_set_morosi()
    {
        try {


            if (!isset($this->_filterparam->anno_ref) || $this->_filterparam->anno_ref == "" || !is_numeric($this->_filterparam->anno_ref)) throw new Exception("Anno di riferimento non indicato", E_USER_ERROR);
            if (!isset($this->_filterparam->del_ug) || $this->_filterparam->del_ug == "" || !is_numeric($this->_filterparam->del_ug)) throw new Exception("Riferimento gruppo in eliminazione non specificato", E_USER_ERROR);
            if (!isset($this->_filterparam->new_ug) || $this->_filterparam->new_ug == "" || !is_numeric($this->_filterparam->new_ug)) throw new Exception("Riferimento gruppo in inserimento non specificato", E_USER_ERROR);

            /*
            $arr_ids = array(9,22,23,40,44,48,54,56,62,64,65,80,81,100,101,104,119,126,139,143,153,188,198,220,225,235,236,248,250,252,262,266,269,271,275,287,301,320,322,327,361,390,395,401,436,465,473,481,491,510,517,529,538,542,587,600,632,658,665,707,731,733,746,747,767,778,780,788,820,850,872,873,878,886,906,911,927,946,951,958,998,999,1008,1017,1027,1028,1041,1045,1049,1055,1061,1088,1094,1101,1108,1110,1111,1117,1119,1135,1136,1143,1176,1202,1230,1255,1260,1361,1402,1457,1482,1583,1587,1629,1674,1711,1719,1720,1722,1741,1785,1852,1854,1920,1921,1928,1942,1964,2017,2020,2027,3051,3116,3171,3237,3243,3275,3350,3405,3434,3440,3445,3486,3490,3502,3590,3603,3607,3610,3656,3658,3665,3668,3675,3680,3685,3686,3698,3708,3739,3777,3816,3858,3977,3984,3987,4140,4194,4222,4268,4327,4360,4387,4388,4413,4420,4422,4427,4435,4438,4439,4440,4444,4446,4470,4479,4481,4482,4495,4497,4502,4510,4514,4520,4554,4566,4578,4588,4627,4628,4664,4812,4854,4856);
            $del_ug = 23;
            $new_ug = 20;
            */

            // online
            $del_ug = $this->_filterparam->del_ug;
            // morosi
            $new_ug = $this->_filterparam->new_ug;

            $completed = 0;

            $arr_ids = $this->sinpe_get_online_anno($del_ug, $this->_filterparam->anno_ref, $this->_filterparam->extraoff ?? 0);
            if (!is_array($arr_ids) || !count($arr_ids)) throw new Exception("Non è stato trovato nessun utente appartenente ai criteri desiderati", E_USER_ERROR);

            $this->_db->transactionStart();

            foreach ($arr_ids as $key => $user_id) {

                $query_sel = "SELECT user_id
                                FROM #__user_usergroup_map
                                WHERE user_id = " . $this->_db->quote($user_id) . "
                                AND group_id = " . $this->_db->quote($new_ug);

                $this->_db->setQuery($query_sel);
                $result = $this->_db->loadResult();

                if (!is_null($result)) continue;

                // rimuovo user da online
                $query_del = "DELETE
                                FROM #__user_usergroup_map
                                WHERE user_id = " . $this->_db->quote($user_id) . "
                                AND group_id = " . $this->_db->quote($del_ug);

                $this->_db->setQuery($query_del);
                if (!$this->_db->execute()) throw new Exception("delete query ko -> " . $query_del, E_USER_ERROR);


                // aggiungo user in moroso
                $query_ins = "INSERT INTO #__user_usergroup_map (user_id, group_id)
                                VALUES (" . $this->_db->quote($user_id) . ", " . $this->_db->quote($new_ug) . ")";

                $this->_db->setQuery($query_ins);
                if (!$this->_db->execute()) throw new Exception("insert query ko -> " . $query_ins, E_USER_ERROR);


                $completed++;

            }

            $this->_db->transactionCommit();

            echo "TOTALI: " . count($arr_ids) . " | ELABORATI: " . $completed;

        }
        catch(Exception $e) {
            $this->_db->transactionRollback();
            echo "ERRORE: " . $e->getMessage();
        }

        $this->_japp->close();
    }

    /**
     * @check_ug: online
     * @anno_request: ultimo anno in regola
     * @extraoff_request: se impostato non considera i soci straordinari
     */
    public function sinpe_get_online_anno($check_ug = null, $anno_request = null, $extraoff_request = 0)
    {

        try {

            if (!isset($this->_filterparam->check_ug) && is_null($check_ug)) throw new Exception("Gruppo di selezione non indicato", E_USER_ERROR);
            if (!isset($this->_filterparam->anno_ref) && is_null($anno_request)) throw new Exception("Anno di riferimento non indicato", E_USER_ERROR);

            $anno_ref = isset($this->_filterparam->anno_ref)
                ? $this->_filterparam->anno_ref
                : $anno_request;
            $extra_ug = 0;

            if (isset($this->_filterparam->extraoff)) $extra_ug = $this->_filterparam->extraoff;
            else if ($extraoff_request) $extra_ug = $extraoff_request;

            $query = "SELECT user_id
                        FROM #__comprofiler
                        WHERE cb_ultimoannoinregola = " . $this->_db->quote($anno_ref);

            $this->_db->setQuery($query);
            $rows = $this->_db->loadAssocList();

            if (!count($rows)) throw new Exception("Nessun risultato per " . $anno_ref, E_USER_ERROR);

            //$check_ug = [23];
            //$extra_ug = [25];

            foreach ($rows as $key => $user) {

                $query_ug = "SELECT user_id
                                FROM #__user_usergroup_map
                                WHERE user_id = " . $this->_db->quote($user['user_id']) . "
                                AND group_id IN (" . $check_ug . ")";

                // se impostato straordinari lo esclude dalla query
                if ($extra_ug > 0) $query_ug .= " AND group_id NOT IN (" . $extra_ug . ")";

                $this->_db->setQuery($query_ug);
                $result = $this->_db->loadResult();

                if (is_null($result) || !$result) continue;

                $extra_arr[] = $user['user_id'];

            }

            //return implode(",", $extra_arr);
            return $extra_arr;

        }
        catch(Exception $e) {
            echo "ERRORE: " . $e->getMessage();
        }

        $this->_japp->close();

    }

    public function sinpe_new_extra() {

        try {

            $cf_s = array(
                        23,
                        44,
                        118,
                        206,
                        211,
                        227,
                        236,
                        334,
                        361,
                        436,
                        499,
                        529,
                        587,
                        668,
                        706,
                        733,
                        760,
                        767,
                        769,
                        850,
                        860,
                        879,
                        911,
                        915,
                        927,
                        951,
                        1041,
                        1069,
                        1071,
                        1110,
                        1135,
                        1143,
                        1230,
                        1246,
                        1289,
                        1341,
                        1462,
                        1464,
                        1629,
                        1647,
                        1719,
                        1724,
                        1812,
                        1852,
                        1902,
                        1921,
                        1928,
                        2007,
                        2019,
                        3051,
                        3060,
                        3171,
                        3237,
                        3415,
                        3509,
                        3524,
                        3656,
                        3671,
                        3779,
                        3928,
                        3950,
                        3977,
                        4045,
                        4222,
                        4327,
                        4360,
                        4376,
                        4391,
                        4392,
                        4406,
                        4427,
                        4470,
                        4471,
                        4490,
                        4496,
                        4498,
                        4499,
                        4500,
                        4504,
                        4505,
                        4507,
                        4509,
                        4512,
                        4513,
                        4516,
                        4574,
                        4576,
                        4577,
                        4579,
                        4584,
                        4586,
                        4590,
                        4593,
                        4594,
                        4595,
                        4596,
                        4597,
                        4598,
                        4599,
                        4600,
                        4601,
                        4602,
                        4603,
                        4604,
                        4605,
                        4606,
                        4607,
                        4608,
                        4609,
                        4610,
                        4611,
                        4612,
                        4613,
                        4614,
                        4615,
                        4616,
                        4617,
                        4618,
                        4619,
                        4621,
                        4622,
                        4623,
                        4624,
                        4625,
                        4626,
                        4629,
                        4630,
                        4631,
                        4633,
                        4634,
                        4635,
                        4636,
                        4637,
                        4638,
                        4639,
                        4640,
                        4641,
                        4642,
                        4643,
                        4644,
                        4645,
                        4646,
                        4647,
                        4648,
                        4649,
                        4650,
                        4651,
                        4652,
                        4653,
                        4654,
                        4655,
                        4656,
                        4657,
                        4658,
                        4659,
                        4660,
                        4661,
                        4663,
                        4665,
                        4667,
                        4670,
                        4671,
                        4672,
                        4673,
                        4676,
                        4680,
                        4681,
                        4683,
                        4684,
                        4685,
                        4686,
                        4687,
                        4688,
                        4689,
                        4690,
                        4691,
                        4692,
                        4693,
                        4694,
                        4695,
                        4697,
                        4698,
                        4699,
                        4700,
                        4701,
                        4702,
                        4703,
                        4704,
                        4705,
                        4707,
                        4708,
                        4709,
                        4710,
                        4711,
                        4712,
                        4713,
                        4714,
                        4715,
                        4717,
                        4718);
            $extra_arr = [];

            //$imp = "'" . implode( "','", $cf_s) . "'";
            $imp = implode(",", $cf_s);
            $check_ug = [20,21,23];

            /*WHERE comp.cb_codicefiscale IN (" . $imp . ")*/
            $query = "SELECT usr.id AS user_id, usr.username, comp.cb_codicefiscale
                        FROM #__comprofiler comp
                        JOIN #__users usr ON comp.user_id = usr.id
                        WHERE usr.id IN (" . $imp . ")";

            $this->_db->setQuery($query);
            $rows = $this->_db->loadAssocList();

            foreach ($rows as $key_row => $user) {


                $query_ug = "SELECT user_id
                                FROM #__user_usergroup_map
                                WHERE user_id = " . $this->_db->quote($user['user_id']) . "
                                AND group_id IN (" . implode(',', $check_ug) . ")"
                                ;
                $this->_db->setQuery($query_ug);
                $result = $this->_db->loadResult();

                if (is_null($result)
                    || !$result)
                    $extra_arr[] = $user['user_id'];

                /*
                if (($key = array_search($user['cb_codicefiscale'], $cf_s)) !== false) {
                    unset($cf_s[$key]);
                }
                */

            }

            echo implode(",", $extra_arr);

        }
        catch (Exception $e) {
            echo "ERRORE: " . $e->getMessage();
        }

        $this->_japp->close();

    }

    public function sinpe_insert_quote()
    {

        try {

            $arr_ids = [
                        755,
                        848,
                        1144,
                        493,
                        508,
                        4988,
                        435,
                        467,
                        136,
                        702,
                        1109,
                        358,
                        357,
                        1250
                    ];
            $completed = 0;

            $this->_db->transactionStart();
            foreach ($arr_ids as $key_user => $user_id) {

                // cb_ultimoannoinregola
                $query_update = "UPDATE #__comprofiler
                                SET cb_ultimoannoinregola = 2022
                                WHERE user_id = " . $this->_db->quote($user_id);

                $this->_db->setQuery($query_update);
                if (!$this->_db->execute())
                    throw new Exception("update query ko -> " . $query_update, E_USER_ERROR);

                $now = date('Y-m-d H:i:s');
                $query_quote = "INSERT INTO #__gg_quote_iscrizioni (
                                                user_id,
                                                anno,
                                                tipo_quota,
                                                tipo_pagamento,
                                                data_pagamento,
                                                dettagli_transazione
                                                )
                                            VALUES (
                                                " . $this->_db->quote($user_id) . ",
                                                2022,
                                                'quota',
                                                'bonifico',
                                                " . $this->_db->quote($now) . ",
                                                'Socio onorario 2022'
                                            )";
                $this->_db->setQuery($query_quote);
                if (!$this->_db->execute())
                    throw new Exception("insert quote query ko -> " . $query_quote, E_USER_ERROR);

                $completed++;

            }

            $this->_db->transactionCommit();

            echo "TOTALI: " . count($arr_ids) . " | ELABORATI: " . $completed;

        }
        catch(Exception $e) {
            $this->_db->transactionRollback();
            echo "ERRORE: " . $e->getMessage();
        }

        $this->_japp->close();

    }

    public function sinpe_ug_move() {

        try {

            $arr_ids = [
                4490,
                4496,
                4498,
                4499,
                4500,
                4505,
                4509,
                4512,
                4513,
                4516,
                4574,
                4576,
                4577,
                4579,
                4586,
                4590,
                4593,
                4594,
                4595,
                4596,
                4597,
                4598,
                4600,
                4601,
                4602,
                4603,
                4604,
                4606,
                4607,
                4608,
                4609,
                4610,
                4611,
                4612,
                4613,
                4615,
                4616,
                4617,
                4618,
                4619,
                4621,
                4622,
                4623,
                4624,
                4625,
                4626,
                4631,
                4633,
                4635,
                4636,
                4637,
                4638,
                4639,
                4640,
                4641,
                4642,
                4643,
                4644,
                4645,
                4647,
                4649,
                4651,
                4652,
                4653,
                4655,
                4656,
                4657,
                4658,
                4659,
                4661,
                4663,
                4667,
                4672,
                4676,
                4683,
                4684,
                4685,
                4686,
                4687,
                4688,
                4690,
                4691,
                4692,
                4694,
                4695,
                4697,
                4698,
                4699,
                4700,
                4701,
                4702,
                4703,
                4704,
                4705,
                4707,
                4708,
                4709,
                4710,
                4711,
                4712,
                4713,
                4714,
                4715,
                4717,
                4718];

            $del_ug = [28,30];
            $new_ug = [23,25];
            $completed = 0;

            $this->_db->transactionStart();
            foreach ($arr_ids as $key_user => $user_id) {

                // rimuovo utente da gruppi evento
                $query_del = "DELETE
                                FROM #__user_usergroup_map
                                WHERE user_id = " . $this->_db->quote($user_id) . "
                                AND group_id IN (" . implode(',', $del_ug) . ")";

                $this->_db->setQuery($query_del);
                if (!$this->_db->execute())
                    throw new Exception("delete query ko -> " . $query_del, E_USER_ERROR);

                // aggiungo utente a gruppi istituzionali
                $query_ins = "INSERT INTO #__user_usergroup_map
                                (user_id, group_id) VALUES ";
                foreach ($new_ug as $key_ug => $ug) {
                    $query_ins .= "(" . $this->_db->quote($user_id) . ", " . $this->_db->quote($ug) . "),";
                }

                $query_ins = rtrim(trim($query_ins), ",") . ";";
                $this->_db->setQuery($query_ins);
                if (!$this->_db->execute())
                    throw new Exception("insert query ko -> " . $query_ins, E_USER_ERROR);

                // cb_ultimoannoinregola
                $query_update = "UPDATE #__comprofiler
                                SET cb_ultimoannoinregola = 2022
                                WHERE user_id = " . $this->_db->quote($user_id);
                $this->_db->setQuery($query_update);
                if (!$this->_db->execute())
                    throw new Exception("update query ko -> " . $query_update, E_USER_ERROR);

                $now = date('Y-m-d H:i:s');
                $query_quote = "INSERT INTO #__gg_quote_iscrizioni (
                                                user_id,
                                                anno,
                                                tipo_quota,
                                                tipo_pagamento,
                                                data_pagamento,
                                                dettagli_transazione
                                                )
                                            VALUES (
                                                " . $this->_db->quote($user_id) . ",
                                                2022,
                                                'quota',
                                                'bonifico',
                                                " . $this->_db->quote($now) . ",
                                                'Socio straordinario da Congresso 2021'
                                            )";
                $this->_db->setQuery($query_quote);
                if (!$this->_db->execute())
                    throw new Exception("insert quote query ko -> " . $query_quote, E_USER_ERROR);

                $completed++;

            }

            $this->_db->transactionCommit();

            echo "TOTALI: " . count($arr_ids) . " | ELABORATI: " . $completed;

        }
        catch(Exception $e) {
            $this->_db->transactionRollback();
            echo "ERRORE: " . $e->getMessage();
        }

        $this->_japp->close();

    }



    public function get_tz() {

        $oggi = '2020-11-16T07:21:36Z';
        $dt = new DateTime($oggi, new DateTimeZone('UTC'));
        $dt->setTimezone(new DateTimeZone('Europe/Rome'));
        echo $dt->format('Y-m-d H:i:s');

        $this->_japp->close();

    }

    public function get_user_dt() {

        try {

            $modelUser = new gglmsModelUsers();
            $tmpuser = $modelUser->get_user($this->_filterparam->id_utente, 0, "cp");

            echo json_encode($tmpuser);

        }
        catch (Exception $e) {
            echo $e->getMessage();
        }

        $this->_japp->close();
    }

    public function get_last_insert_coupon() {

        try {

            $_ret = array();

            $query = $this->_db->getQuery(true)
                    ->select('messaggio')
                    ->from('#__gg_error_log')
                    ->where('messaggio LIKE ' . $this->_db->quote('%api_genera_coupon_response%'))
                    ->order('id DESC');

            $this->_db->setQuery($query);
            $result = $this->_db->loadAssoc();

            if (is_null($result)
                || !isset($result['messaggio'])
                || $result['messaggio'] == "")
                throw new Exception("Nessun riferimento trovato", 1);

            $_response = preg_replace('/\s/', '', $result['messaggio']);
            $_response = str_replace("api_genera_coupon_response:", "", $_response);

            $_decode = json_decode($_response);

            if (
                (is_object($_decode) && !isset($_decode->id_iscrizione))
                    || (is_array($_decode) && !isset($_decode['id_iscrizione']))
                )
                throw new Exception("Il riferimento ha un valore non valido", 1);


            $_ret['success'] = (is_object($_decode)) ? $_decode->id_iscrizione : $_decode['id_iscrizione'];

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        echo json_encode($_ret);
        $this->_japp->close();

    }

}
