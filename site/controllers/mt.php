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
require_once JPATH_COMPONENT . '/controllers/zoom.php';

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

        echo __FUNCTION__;

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
