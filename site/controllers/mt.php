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
        try {

            // 727182924==999102115
            // 3Rdz7e5tpM+CU1mw6+1xIYKGK8GAIhe0IHS7N/VSbJg=
            $enc = $this->encrypt_decrypt('encrypt', '727182924==999102115', 'chiave-elearning', 'fvNN4F5y9sF6vtbv');
            echo $enc . '<br />';

            // bG13UW9XcHJrTjVsZEtJdnNYQjg1dktRZWh6Y1g4UTROeWJESTBkWVNaQT0=
            $dec = $this->encrypt_decrypt('decrypt', 'bG13UW9XcHJrTjVsZEtJdnNYQjg1dktRZWh6Y1g4UTROeWJESTBkWVNaQT0=', 'chiave-elearning', 'fvNN4F5y9sF6vtbv');
            echo $dec;
        }
        catch (Exception $e) {
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
