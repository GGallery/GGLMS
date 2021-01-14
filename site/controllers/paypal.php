<?php

defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/libraries/paypal/vendor/autoload.php';
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;


class gglmsControllerPaypal extends JControllerLegacy {

    protected $client_id;
    protected $client_secret;
    protected $is_production;

    public function __construct($client_id, $client_secret, $is_production, $config = array())
    {
        parent::__construct($config);

        $this->_japp = JFactory::getApplication();
        //$this->_params = $this->_japp->getParams();
        $this->_db = JFactory::getDbo();

        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->is_production = $is_production;
    }

    public function quote_sinpe_store_payment($ordine_id, $user_id, $totale_sinpe, $totale_espen=0) {

        $_ret = array();

        try {

            // controllo parametri fondamentali
            if (!isset($ordine_id)
                || $ordine_id == "")
                throw new Exception("identificativo ordine mancante", 1);

            if (!isset($user_id)
                || $user_id == "")
                throw new Exception("identificativo utente mancante", 1);

            if (!isset($totale_sinpe)
                || $totale_sinpe == "")
                throw new Exception("totale transazione mancante", 1);

            $order_info_obj = $this->get_order($ordine_id,
                $this->client_id,
                $this->client_secret,
                $this->is_production);

            $order_info_arr = json_decode($order_info_obj, true);

            // dettaglio transazione non disponibile
            if (isset($order_info_arr['error']))
                throw new Exception($order_info_arr['error'], 1);

            $dt = new DateTime();
            // ricavo tutte le informazioni da memorizzare per l'utente corrente
            $_success_order = $order_info_arr['success'];

            $_order_details = "";
            $_order_details .= (isset($_success_order['id'])) ? 'ID: ' . $_success_order['id'] . "\n" : "";
            $_order_details .= (isset($_success_order['status'])) ? 'STATUS: ' . $_success_order['status'] . "\n" : "";
            $_order_details .= (isset($_success_order['purchase_units'][0]['description'])) ? 'DESCRIZIONE: ' . $_success_order['purchase_units'][0]['description'] . "\n" : "";
            //$_order_total = (isset($_success_order['purchase_units'][0]['amount']['value'])) ? $_success_order['purchase_units'][0]['amount']['value'] : 0;
            $_order_details .= (isset($_success_order['payer']['name']['given_name'])) ? 'PAYER NOME: ' . $_success_order['payer']['name']['given_name'] . "\n" : "";
            $_order_details .= (isset($_success_order['payer']['name']['surname'])) ? 'PAYER COGNOME: ' . $_success_order['payer']['name']['surname'] . "\n" : "";
            $_order_details .= (isset($_success_order['payer']['email_address'])) ? 'PAYER EMAIL: ' . $_success_order['payer']['email_address'] . "\n" : "";

            $_data_creazione = $dt->format('Y-m-d H:i:s');
            $_order_creation = (isset($_success_order['create_time']) && $_success_order['create_time'] != "") ? $_success_order['create_time'] : "";
            if ($_order_creation != "") {
                $dt_ = new DateTime($_order_creation);
                $_data_creazione = $dt_->format('Y-m-d H:i:s');
            }

            $_ret['success'] = "tuttook";
            $_ret['user_id'] = $user_id;
            $_ret['anno_quota'] = $dt->format('Y');
            $_ret['data_creazione'] = $_data_creazione;
            $_ret['order_details'] = $_order_details;
            $_ret['totale_sinpe'] = $totale_sinpe;
            $_ret['totale_espen'] = $totale_espen;

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
            DEBUGG::log(json_encode($e->getMessage()), 'ERRORE DA ' . __FUNCTION__, 0, 1, 0);
        }

        return json_encode($_ret, JSON_PRETTY_PRINT);
    }

    public function create_client($client_id, $client_secret, $is_production) {

        $environment = null;

        if (!$is_production)
            $environment = new SandboxEnvironment($client_id, $client_secret);
        else
            $environment = new ProductionEnvironment($client_id, $client_secret);

        $client = new PayPalHttpClient($environment);

        return $client;

    }

    public function get_order($order_id, $client_id, $client_secret, $is_production) {

        $_ret = array();

        try {

            $request = new OrdersGetRequest($order_id);
            $client = $this->create_client($client_id, $client_secret, $is_production);
            $response = $client->execute($request);

            $_ret['success'] = $response->result;

        }
        catch (Exception $e) {
            $_err = json_encode($e->getMessage());
            DEBUGG::log($_err, 'ERRORE DA ' . __FUNCTION__, 1, 1);
            $_ret['error'] = $e->getMessage();
        }

        return json_encode($_ret, JSON_PRETTY_PRINT);

    }

    public function captureOrder($order_id, $debug=false) {

        $_ret = array();

        try {

            $request = new OrdersCaptureRequest($order_id);
            $client = $this->create_client($this->client_id, $this->client_secret, $this->is_production);
            $request->prefer('return=representation');
            $response = $client->execute($request);

            if ($debug)
            {
                print "Status Code: {$response->statusCode}\n";
                print "Status: {$response->result->status}\n";
                print "Order ID: {$response->result->id}\n";
                print "Links:\n";
                foreach($response->result->links as $link)
                {
                    print "\t{$link->rel}: {$link->href}\tCall Type: {$link->method}\n";
                }
                print "Capture Ids:\n";
                foreach($response->result->purchase_units as $purchase_unit)
                {
                    foreach($purchase_unit->payments->captures as $capture)
                    {
                        print "\t{$capture->id}";
                    }
                }
                // To toggle printing the whole response body comment/uncomment below line
                $_ret['success'] = $response->result;
            }

        }
        catch (Exception $e) {
            $_err = json_encode($e->getMessage());
            DEBUGG::log($_err, 'ERRORE DA ' . __FUNCTION__, 1, 1);
            $_ret['error'] = $e->getMessage();
        }

        return json_encode($_ret, JSON_PRETTY_PRINT);

    }

}
