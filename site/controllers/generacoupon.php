<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

//require_once JPATH_COMPONENT . '/models/contenuto.php';
require_once JPATH_COMPONENT . '/models/generacoupon.php';

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerGeneraCoupon extends JControllerLegacy
{

    private $_user;
    private $_japp;
    public $_params;
    public $_db;
    public $generaCoupon;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();

        $this->_user = JFactory::getUser();
        $this->_db = &JFactory::getDbo();


        $this->generaCoupon = new gglmsModelGeneraCoupon();
        $this->lista_corsi = $this->generaCoupon->lista_corsi;
        $this->societa_venditrici = $this->generaCoupon->societa_venditrici;

    }

    public function get_last_insert_coupon() {

        try {

            $_ret = array();
            $_fails = true;

            $query = $this->_db->getQuery(true)
                ->select('messaggio')
                ->from('#__gg_error_log')
                ->where('messaggio LIKE ' . $this->_db->quote('%api_genera_coupon_response%'))
                ->orWhere('messaggio LIKE ' . $this->_db->quote('%api_genera_coupon_exception%'))
                ->order('id DESC');

            $this->_db->setQuery($query);
            $result = $this->_db->loadAssoc();

            if (is_null($result)
                || !isset($result['messaggio'])
                || $result['messaggio'] == "")
                throw new Exception("Nessun riferimento trovato", 1);

            $_response = preg_replace('/\s/', '', $result['messaggio']);
            $_response = str_replace("api_genera_coupon_response:", "", $_response);
            $_response = str_replace("api_genera_coupon_exception:", "", $_response);

            if (strpos($result['messaggio'], "id_iscrizione") !== false)
                $_fails = false;

            // se il messaggio di log contiene id_iscrizione
            if (!$_fails) {
                $_decode = json_decode($_response);

                if (
                    (is_object($_decode) && !isset($_decode->id_iscrizione))
                    || (is_array($_decode) && !isset($_decode['id_iscrizione']))
                )
                    throw new Exception("Il riferimento ha un valore non valido", 1);

                $_ret['last_iscrizione'] = (is_object($_decode)) ? $_decode->id_iscrizione : $_decode['id_iscrizione'];
            }
            else {
                $_ret['last_error'] = trim($_response, '"');
            }

        }
        catch (Exception $e) {
            $_ret['call_error'] = $e->getMessage();
        }

        echo json_encode($_ret);
        $this->_japp->close();

    }

    public function generacoupon()
    {
        try {

            $data = JRequest::get($_POST);
            $this->generaCoupon->insert_coupon($data);

            $this->_japp->redirect(('index.php?option=com_gglms&view=genera'), $this->_japp->enqueueMessage(JText::_('COM_GGLMS_GENERA_COUPON_SUCCESS'), 'Success'));

        } catch (Exception $e) {

            DEBUGG::error($e, 'generaCoupon');
        }
        $this->_japp->close();
    }


    /*api genera coupon per sviluppatori e comerce*/
    public function api_genera_coupon()
    {
        try {

            $data = JRequest::get($_POST);

            /*
             * required
             * username: stringa
             * ragione_sociale: stringa
             * email: stringa / email
             * id_piattaforma: numerico
             * gruppo_corsi: numerico
             * qty: numerico
             * opzionale skillab
             * ref_skill: stringa -> integrazione esplicitamente realizzata per skillab
             * necessaria in fase di ritorno dati per metterli in grado di associare i coupon alla loro edizione corso
             * */

            // controllo username
            if (!isset($data['username'])
                || !preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
                $_msg = "username is not in a valid format";
                throw new Exception($_msg, E_USER_ERROR);
            }

            // controllo ragione sociale
            if (!isset($data['ragione_sociale'])
                || $data['ragione_sociale'] == "") {
                $_msg = "ragione_sociale is not in a valid format";
                throw new Exception($_msg, E_USER_ERROR);
            }

            // controllo email
            if (!isset($data['email'])
                || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $_msg = "email is not in a valid format";
                throw new Exception($_msg, E_USER_ERROR);
            }

            // controllo id_piattaforma
            if (!isset($data['id_piattaforma'])
                || !preg_match('/^[1-9][0-9]*$/', $data['id_piattaforma'])) {
                $_msg = "id_piattaforma is not in a valid format";
                throw new Exception($_msg, E_USER_ERROR);
            }

            // controllo gruppo_corsi
            if (!isset($data['gruppo_corsi'])
                || !preg_match('/^[1-9][0-9]*$/', $data['gruppo_corsi'])) {
                $_msg = "gruppo_corsi is not in a valid format";
                throw new Exception($_msg, E_USER_ERROR);
            }

            // controllo qty
            if (!isset($data['qty'])
                || !preg_match('/^[1-9][0-9]*$/', $data['qty'])) {
                $_msg  = "qty is not in a valid format";
                throw new Exception($_msg, E_USER_ERROR);
            }

            // log dei paramentri ricevuti
            DEBUGG::log(json_encode($data), 'api_genera_coupon', 0, 1, 0 );

            $id_iscrizione = $this->generaCoupon->insert_coupon($data, true);
            if (is_null($id_iscrizione)) throw new Exception("id_iscrizione missing", E_USER_ERROR);

            $result = new stdClass();
            $result->id_iscrizione = $id_iscrizione;
            // integrazione esclusiva per skillab
            if (isset($data['ref_skill'])
                && $data['ref_skill'] != "")
                $result->ref_skill = $data['ref_skill'];

            // log risposta api
            DEBUGG::log(json_encode($result), 'api_genera_coupon_response', 0, 1, 0 );

            echo json_encode($result);
            $this->_japp->close();

        } catch (Exception $e) {

            // Imposta header
            header('HTTP/1.1 500 Internal Server Error', true, 500);
            
            // Prepara il JSON di errore
            $error = new stdClass();
            $error->success = false;
            $error->message = $e->getMessage();
            
            // Invia risposta JSON
            echo json_encode($error);

            // loggo anche l'oggetto POST ricevuto per maggiori dettagli
            UtilityHelper::make_debug_log(__FUNCTION__, print_r(JRequest::get($_POST), true), 'api_genera_coupon_post_obj');
            // l'errore esclusivo della api_genera_coupon lo marco diversamente
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_exception');
            DEBUGG::error($e, __FUNCTION__, 1, true);

            // Termina l'esecuzione
            $this->_japp->close();
        }

    }

    /* usato in form genera coupon frontend*/
    public function check_username()
    {

        $japp = JFactory::getApplication();
        $piva = JRequest::getVar('username');

        $query = $this->_db->getQuery(true)
            ->select('u.id, u.username, u.email, c.cb_ateco, u.name')
            ->from('#__users as u')
            ->join('inner', '#__comprofiler AS c ON c.user_id = u.id')
            ->where("u.username= '" . $piva . "'");


        $this->_db->setQuery($query);
        $result = $this->_db->loadAssoc();

        if ($result) {
            // prendo anche la piattaforma
            $model_user = new gglmsModelUsers();
            $id_piattaforma = $model_user->get_user_piattaforme($result["id"]);

            $result["id_piattaforma"] = $id_piattaforma[0]->value;

        }

        echo isset($result) ? json_encode($result) : null;
        $japp->close();

    }

    /* usato in form genera coupon frontend*/
    public function load_matching_venditori_list()
    {

        $japp = JFactory::getApplication();
        $venditore = JRequest::getVar('txt_venditore');
        $id_piattaforma = JRequest::getVar('id_piattaforma');

        // filtro i venditori anche  per piattaforma
        $query = $this->_db->getQuery(true)
            ->select('DISTINCT c.venditore')
            ->from('#__gg_coupon as c')
            ->where("c.venditore like '%" . $venditore . "%'")
            ->where("LEFT(c.id_iscrizione, 2) = '" . $id_piattaforma . "'");

        $this->_db->setQuery($query);
        $list = $this->_db->loadAssocList();

        $result = [];
        foreach ($list as $v) {
            array_push($result, $v["venditore"]);
        }

        echo isset($result) ? json_encode($result) : null;
        $japp->close();


    }


    /* corsi per piattaforma per sviluppatori e comerce*/
    public function api_get_corsi()
    {

        $japp = JFactory::getApplication();
        $id_piattaforma = JRequest::getVar('id_piattaforma');
        $result = utilityHelper::getGruppiCorsi($id_piattaforma);

        echo isset($result) ? json_encode($result) : null;
        $japp->close();

    }


    public function get_lista_corsi_by_piattaforma()
    {

        $japp = JFactory::getApplication();
        $piva = JRequest::getVar('username');

        $query = $this->_db->getQuery(true)
            ->select('u.id, u.username, u.email, c.cb_ateco, u.name')
            ->from('#__users as u')
            ->join('inner', '#__comprofiler AS c ON c.user_id = u.id')
            ->where("u.username= '" . $piva . "'");

        $this->_db->setQuery($query);
        $result = $this->_db->loadAssoc();

        if ($result) {
            // prendo anche la piattaforma
            $model_user = new gglmsModelUsers();
            $id_piattaforma = $model_user->get_user_piattaforme($result["id"]);

            $result["id_piattaforma"] = $id_piattaforma[0]->value;

        }

        echo isset($result) ? json_encode($result) : null;
        $japp->close();

    }

    function get_lista_piva($ret_json = true)
    {

        try {
            $japp = JFactory::getApplication();
//            $txt_azienda = JRequest::getVar('txt_azienda');

            $user_id = $this->_user->id;
            $_config = new gglmsModelConfig();
            $id_gruppo_tutor_aziendale = $_config->getConfigValue('id_gruppo_tutor_aziendale');
            $_filtro_azienda = " = " . $id_gruppo_tutor_aziendale;

            $model_user = new gglmsModelUsers();
            $id_piattaforma = $model_user->get_user_piattaforme($user_id);

            $id_piattaforma_array = array();
            foreach ($id_piattaforma as $p) {
                array_push($id_piattaforma_array, $p->value);
            }

            // applico il filtro per il tutor aziendale, per il momento limitato soltanto allo scarica report
            $tutor_az = $model_user->is_tutor_aziendale($user_id);
            if ($tutor_az) {
                $lista_aziende = $model_user->get_user_societa($user_id, true);

                // applico filtro soltanto se ci sono società associate al tutor aziendale
                if (count($lista_aziende) > 0)
                    $_filtro_azienda = " in (" . implode(', ', utilityHelper::get_id_aziende($lista_aziende)) . ")";
            }

            $db = JFactory::getDbo();
            // estratto anche l'id dell'azienda
            $query = $db->getQuery(true)
                ->select(' distinct u.name as azienda , u.username as piva, u.id as id_azienda, piattaforme.id as id_gruppo ')
                ->from('#__users as u')
                ->join('inner', '#__user_usergroup_map as map on map.user_id = u.id')
                ->join('inner', '#__usergroups as ug on ug.id = map.group_id')
                ->join('inner', '#__usergroups as  piattaforme on piattaforme.title = u.name')
                //->where(" ug.id = " . $id_gruppo_tutor_aziendale)
                ->where(" ug.id " . $_filtro_azienda)
                ->where('piattaforme.parent_id IN (' . implode(", ", $id_piattaforma_array) . ')')
                ->order('u.name asc');

            $db->setQuery($query);

            // se richiamati da ajax restituisco un json
            if ($ret_json) {
                $piva_list = $db->loadObjectList();
                echo json_encode($piva_list);
                $japp->close();
            }
            // altrimenti restituisco un array in modo "silente"
            else {
                $piva_list = $db->loadAssocList();
                return $piva_list;
            }

        } catch (Exception $e) {
            DEBUGG::error($e, 'getListaPiva');
        }


    }


    public function get_corsi_custom($lista_corsi, $user_id){



        try{

            $model_user = new gglmsModelUsers();
            $tutor_az = $model_user->is_tutor_aziendale($user_id);
            $tutor_group = false;

            $res = array();

            if($tutor_az) {

                $usergroups = $model_user->get_user_societa($user_id, true);
                $i = 0;
                $result = array();

               foreach ($lista_corsi as $key_corso => $corso) {

                   $query = $this->_db->getQuery(true)
                       ->select('distinct u.id as id_unita, ug.idgruppo as value, u.titolo as text, u.id_gruppi_custom')
                       ->from('#__gg_unit as u')
                       ->join('inner', '#__gg_usergroup_map as ug on u.id = ug.idunita')
                       ->where("ug.idgruppo = '" . $corso->value . "'");

                   $this->_db->setQuery($query);
                   $result = $this->_db->loadAssoc();

                   $ids_custom = explode(",", $result['id_gruppi_custom']);


                   if (in_array($usergroups[0]->id, $ids_custom)) {


                       $res[$i]->value = $result['value'];
                       $res[$i]->text = $result['text'];
                       $i++;
                       $result = array();
                       $tutor_group = true;
                   }

                   if (isset($result) && count($result) > 0) {

                       $res[$i]->value = $result['value'];
                       $res[$i]->text = $result['text'];
                       $i++;
                       $result = array();
                   }
               }

            }

          return $tutor_group ? $res  : null;

       } catch (Exception $e) {
            DEBUGG::error($e, 'getListaCorsiCustom');
        }

    }

    public function get_corsi_not_custom($lista_corsi){



        try{


            $i = 0;
            $res = array();
            $result = array();

                foreach ($lista_corsi as $key_corso => $corso) {

                    $query = $this->_db->getQuery(true)
                        ->select('DISTINCT  ug.idgruppo as value, u.titolo as text')
                        ->from('#__gg_unit as u')
                        ->join('inner', '#__gg_usergroup_map as ug on u.id = ug.idunita')
                        ->where("u.id_gruppi_custom is null")
                        ->where("ug.idgruppo = '" . $corso->value . "'");

                    $this->_db->setQuery($query);
                    $result = $this->_db->loadAssoc();


                    if (count($result) > 0) {


                        $res[$i]->value = $result['value'];
                        $res[$i]->text = $result['text'];
                        $i++;
                    }


                }

            return $res;

        } catch (Exception $e) {
            DEBUGG::error($e, 'getListaCorsiNotCustom');
        }

        return $res;

    }



}
