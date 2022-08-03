<?php
/**
 * Created by IntelliJ IDEA.
 * User: Luca
 * Date: 19/02/2021
 * Time: 14:59
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/libraries/zoom/vendor/autoload.php';
require_once JPATH_COMPONENT . '/models/zoom.php';

use \Firebase\JWT\JWT;
use GuzzleHttp\Client;

class gglmsControllerZoom extends JControllerLegacy
{

    protected $api_key;
    protected $api_secret;
    protected $api_endpoint;
    protected $api_version;
    protected $api_scadenza_token;
    protected $devel_mode;
    protected $config_client;
    protected $access_token;
    private $_filterparam;

    public function __construct($api_key, $api_secret, $api_endpoint, $api_version, $api_scadenza_token, $devel_mode, $config = array())
    {
        parent::__construct($config);

        $this->_japp = JFactory::getApplication();
        $this->_db = JFactory::getDbo();

        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
        $this->api_endpoint = $api_endpoint;
        $this->api_version = $api_version;
        $this->api_scadenza_token = $api_scadenza_token;
        $this->devel_mode = $devel_mode;
        $this->config_client = $this->get_client();

    }

    public function get_client() {

        $_config = array(
                'base_uri' => $this->api_endpoint,
                );

        if ($this->devel_mode) {
            $_config['curl'] = array(CURLOPT_SSL_VERIFYPEER => false);
            $_config['verify'] = false;
        }

        return $_config;

    }

    public function get_local_access_token() {

        $model = $this->getModel('zoom');
        $access_token = $model->get_valid_access_token($this->api_scadenza_token);

        return $access_token;

    }

    public function get_access_token() {

        $_ret = array();

        try {

            // verifico se ho salvato il token a database
            $local_token = null;
            $_check_token = $this->get_local_access_token();

            // nessun token faccio la chiamata
            if (!isset($_check_token['success'])
                || is_null($_check_token['success'])
                || !is_array($_check_token)) {

                $payload = array(
                    "iss" => $this->api_key,
                    'exp' => time() + 3600,
                );

                $local_token = JWT::encode($payload, $this->api_secret);
                // scrivo il token a database
                $model = $this->getModel('zoom');
                $insert_token = $model->store_access_token($local_token);

                if (!is_array($insert_token))
                    throw new Exception($insert_token, 1);


            }
            else
                $local_token = $_check_token['success'];

            $_ret['success'] = $local_token;

            return $_ret;

        }
        catch (Exception $e) {
            echo $e->getMessage();
        }

    }


    function get_users() {

        $client = new GuzzleHttp\Client($this->config_client);
        $get_token = $this->get_access_token();

        if (!is_array($get_token))
            throw new Exception($get_token, 1);

        $this->access_token = $get_token['success'];
        $_ret = array();

        try {

            $response = $client->request('GET', '/v' . $this->api_version . '/users', [
                "headers" => [
                    "Authorization" => "Bearer $this->access_token"
                ]
            ]);

            $data = json_encode(json_decode($response->getBody()),true);
            $_ret['success'] = $data;

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        return $_ret;

        $this->_japp->close();

    }

    function get_events($user_id = 'me', $type = 'meetings', $mese = '', $report_type = 'past', $page_size = 300) {

        $client = new GuzzleHttp\Client($this->config_client);
        $get_token = $this->get_access_token();
        $_json = array();

        /*
        if ($type == 'meetings')
          $_json = array(
                        'type' => 'past',
                        );
        */
        $dt_from = "";
        $dt_to = "";
        $report_type = 'past';
        $dt = new DateTime();
        if ($mese == '') {
            $dt_from = $dt->format('Y-m-') . '-01';
            $dt_to = $dt->format('Y-m-t');
        }
        else {
            $dt_from = $mese . '-01';
            $dt_to = date("Y-m-t", strtotime($dt_from));
        }

        if (!is_array($get_token))
            throw new Exception($get_token, 1);

        $_ret = array();

        $this->access_token = $get_token['success'];

        try {

            $response = $client->request('GET', '/v2/report/users/' . $user_id . '/' . $type
                                                                        . '?type=' . $report_type
                                                                        . '&page_size=' . $page_size
                                                                        . '&to=' . $dt_to
                                                                        . '&from=' . $dt_from,
                [
                "headers" => [
                    "Authorization" => "Bearer $this->access_token"
                ],
                $_json
            ]);

            $data = json_encode(json_decode($response->getBody()),true);
            $_ret['success'] = $data;

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        return $_ret;

        $this->_japp->close();
    }

    function get_event_participants($event_id, $type = 'meetings', $event_type = 0, $page_size = 300) {

        $client = new GuzzleHttp\Client($this->config_client);
        $get_token = $this->get_access_token();

        if (!is_array($get_token))
            throw new Exception($get_token, 1);

        $this->access_token = $get_token['success'];
        if (
            $event_type == 5
                || $event_type == 6
                || $event_type == 9
        )
            $type = 'webinars';

        $_ret = array();
        $result = array();
        try {

            $response = $client->request('GET', '/v2/report/' . $type . '/' . $event_id . '/participants?page_size=' . $page_size, [
                "headers" => [
                    "Authorization" => "Bearer $this->access_token"
                ]
            ]);

            $data = json_encode(json_decode($response->getBody()),true);
            $result['participants'] = json_decode($data)->participants;
            $resp = json_decode($data);

            //rifaccio la chiamata nel caso c fossero piu di 300 participants
            while(!empty($resp->next_page_token)){

                $next_token = $resp->next_page_token;

                $response = $client->request('GET', '/v2/report/' . $type . '/' . $event_id . '/participants?page_size=' . $page_size . '&next_page_token=' . $next_token, [
                    "headers" => [
                        "Authorization" => "Bearer $this->access_token"
                    ]
                ]);

                $data = json_encode(json_decode($response->getBody()),true);

                $resp = json_decode($data);

                $result['participants'] = array_merge($result['participants'],$resp->participants);

            }

            $_ret['success'] = json_encode($result);
        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        return $_ret;

        $this->_japp->close();
    }

    function get_event_details($event_id, $type = 'meetings') {

        $client = new GuzzleHttp\Client($this->config_client);
        $get_token = $this->get_access_token();

        if (!is_array($get_token))
            throw new Exception($get_token, 1);

        $this->access_token = $get_token['success'];
        $_ret = array();

        try {

            $response = $client->request('GET', '/v2/report/' . $type . '/' . $event_id, [
                "headers" => [
                    "Authorization" => "Bearer $this->access_token"
                ]
            ]);

            $data = json_encode(json_decode($response->getBody()),true);
            $_ret['success'] = $data;

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        return $_ret;

        $this->_japp->close();
    }

}
