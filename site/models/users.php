<?php

/**
 * Created by PhpStorm.
 * User: Tony
 * Date: 04/05/2017
 * Time: 17:03
 */
class gglmsModelUsers extends JModelLegacy
{

    protected $_db;
    private $_params;
    private $_app;
    public $_userid;
    public $nome;
    public $cognome;
    public $_config;


    public function __construct($config = array())
    {
        parent::__construct($config);

        $user = JFactory::getUser();
        $this->_userid = $user->get('id');
        $this->_db = $this->getDbo();
        $this->_app = JFactory::getApplication();
        $this->_params = $this->_app->getParams();
        $this->_config = new gglmsModelConfig();


    }


    public function get_user($id = null,
                             $integration_element_id = null,
                             $integrazione = null,
                             $campo_nome = null,
                             $campo_cognome = null)
    {
        $_integrazione_ref = (!is_null($integrazione)) ? $integrazione : $this->_params->get('integrazione');
        $_config = new gglmsModelConfig();


        if(!isset($_integrazione_ref))
            $_integrazione_ref = $_config->getConfigValue('integrazione');


        //switch ($this->_params->get('integrazione')) {
        switch ($_integrazione_ref) {
            case 'cb':
                $data = $this->get_user_cb($id, $campo_nome, $campo_cognome);
                break;

            case 'eb':
                $data = $this->get_user_eb($id, $integration_element_id, $campo_nome, $campo_cognome);
                break;

            default:
                $data = $this->get_user_joomla($id);
                break;
        }

        return $data;

    }

    public function get_user_joomla($id)
    {

        try {

            $query = $this->_db->getQuery(true)
                ->select('*, SUBSTRING_INDEX(name,\' \',1) as nome, SUBSTRING_INDEX(name,\' \',-1) as cognome ')
                ->from('#__users as u')
                ->where('u.id = ' . $id);

            $this->_db->setQuery($query);
            $registrants = $this->_db->loadObject();

            return $registrants;
        } catch (Exception $e) {
            DEBUGG::error($e, 'error get user cb', 1);
        }

    }

    private function get_user_cb($id, $campo_nome = null, $campo_cognome = null)
    {

        //$colonna_nome = $this->_app->getParams()->get('campo_community_builder_nome');
        //$colonna_cognome = $this->_app->getParams()->get('campo_community_builder_cognome');

        $colonna_nome = (!is_null($campo_nome)) ? $campo_nome : $this->_app->getParams()->get('campo_community_builder_nome');
        $colonna_cognome = (!is_null($campo_cognome)) ? $campo_cognome : $this->_app->getParams()->get('campo_community_builder_cognome');

        $_config = new gglmsModelConfig();

        if(!isset($colonna_cognome) || !isset($colonna_nome)){

            $colonna_cognome = $_config->getConfigValue('campo_community_builder_cognome');
            $colonna_nome = $_config->getConfigValue('campo_community_builder_nome');
        }

        try {

            $query = $this->_db->getQuery(true)
                ->select('*, ' . $colonna_nome . ' as nome, ' . $colonna_cognome . ' as cognome ')
                ->from('#__comprofiler as r')
                ->join('inner', '#__users as u on u.id = r.id')
                ->where('r.user_id = ' . $id);


            $this->_db->setQuery($query);
            $registrants = $this->_db->loadObject();

            return $registrants;
        } catch (Exception $e) {
            DEBUGG::error($e, 'error get user cb', 1);
        }

    }

    private function get_user_eb($id, $id_eb, $campo_nome=null, $campo_cognome=null)
    {

        //$colonna_nome = $this->_app->getParams()->get('campo_event_booking_nome');
        //$colonna_cognome = $this->_app->getParams()->get('campo_event_booking_cognome');

        $colonna_nome = (!is_null($campo_nome)) ? $campo_nome :  $this->_app->getParams()->get('campo_event_booking_nome');
        $colonna_cognome = (!is_null($campo_cognome)) ? $campo_cognome : $this->_app->getParams()->get('campo_event_booking_cognome');

        try {
            $query = $this->_db->getQuery(true)
                ->select('*')
                ->from('#__eb_registrants as r')
                ->where('r.user_id = ' . $id)
                ->where('r.event_id = ' . $id_eb);


            $this->_db->setQuery($query);
            $registrants = $this->_db->loadAssoc();

            if ($registrants['id']) {
                $registrants['nome'] = $registrants[$colonna_nome];
                $registrants['cognome'] = $registrants[$colonna_cognome];

                $extrafieldfields = $this->get_user_field_eb($registrants['id']);

                // per puntare l'id utente interno a gglms (report & c)
                $registrants['id'] = $id;

                if ($extrafieldfields)
                    $registrants = (object)array_merge($registrants, $extrafieldfields);
            }


            return $registrants;
        } catch (Exception $e) {
            DEBUGG::query($query, 'query error in get user eb');
            DEBUGG::error($e, 'error in get user eb', 1);

        }
    }

    private function get_user_field_eb($registrant_id)
    {

        $query = $this->_db->getQuery(true)
            ->select('f.`name`, v.field_value')
            ->from('#__eb_field_values AS v')
            ->join('inner', '#__eb_fields AS f ON f.id = v.field_id')
            ->where('v.registrant_id = ' . $registrant_id);

        $this->_db->setQuery($query);
        $fields = $this->_db->loadAssoclist('name', 'field_value');

        return $fields;
    }

    public function get_user_by_field($_field, $_valore, $_operatore = '=', $integrazione = 'cb') {

        try {
            $query = $this->_db->getQuery(true)
                ->select('u.*')
                ->from('#__users u');

            switch ($integrazione) {
                case 'cb':
                default:
                    $query = $query->join('inner', '#__comprofiler cb ON u.id = cb.user_id');
                    break;
            }

            $query = $query->where($_field . ' ' . $_operatore . ' ' . $this->_db->quote($_valore));
            $this->_db->setQuery($query);
            $result = $this->_db->loadAssoc();


            return $result;
        }
        catch (Exception $e) {
            return __FUNCTION__ . " errore: " . $e->getMessage();
        }

    }

    /////////////////////////////////////////////////////////////////////////////////////

    public function is_tutor_piattaforma($id)
    {
        $user_groups = JAccess::getGroupsByUser($id, false);
        $id_gruppo_tutor_piattaforma = $this->_config->getConfigValue('id_gruppo_tutor_piattaforma');

        return in_array($id_gruppo_tutor_piattaforma, $user_groups);

    }

    public function is_tutor_aziendale($id)
    {

        $user_groups = JAccess::getGroupsByUser($id, false);
        $id_gruppo_tutor_aziendale = $this->_config->getConfigValue('id_gruppo_tutor_aziendale');

        return in_array($id_gruppo_tutor_aziendale, $user_groups);
    }

    public function is_venditore($id)
    {

        $user_groups = JAccess::getGroupsByUser($id, false);
        $id_gruppo_venditori = $this->_config->getConfigValue('id_gruppo_venditori');

        return in_array($id_gruppo_venditori, $user_groups);

    }

    public function is_user_superadmin($id)
    {

        $id_gruppo_superadmin = $this->_config->getConfigValue('id_gruppo_super_admin');
        $user_groups = JAccess::getGroupsByUser($id, false);
        return in_array($id_gruppo_superadmin, $user_groups);


    }

    public function set_user_tutor($user_id, $tutor_type, $from_api=false)
    {
        /** TYPE = 'aziendale' oppure 'piattaforma' **/

        try {

            $tutor_group_id = null;

            switch ($tutor_type) {
                case "aziendale":
                    $tutor_group_id = $this->_config->getConfigValue('id_gruppo_tutor_aziendale');
                    break;

                case "piattaforma":
                    $tutor_group_id = $this->_config->getConfigValue('id_gruppo_tutor_piattaforma');
                    break;
                default:
                    // non faccio niente
                    $tutor_group_id = null;
                    break;

            }

            if (!$tutor_group_id)
                throw new RuntimeException("id_gruppo tutor non trovato", E_USER_ERROR);

            //if ($tutor_group_id) {
            $insertquery_map = 'INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (' . $user_id . ', ' . $tutor_group_id . ')';
            $this->_db->setQuery($insertquery_map);
            $this->_db->execute();
            //}


            return true;

        } catch (Exception $e) {
            if ($from_api)
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_response');

            DEBUGG::error($e, __FUNCTION__);
            return false;
        }


    }


    public function get_user_societa($id, $strict = true)
    {
        // $strict = true --> solo societa a cui l'utente appartiene, la ricavo dal dominio per essere sicura di avere un solo risultato (in caso di configurazioni sbagliate)
        // $strict = false --> tutte le società delle piattaforme a cui appartiene
        $res = array();

        try {


            $id_gruppo_piattaforme = $this->_config->getConfigValue('id_gruppo_piattaforme');
            $user_groups = JAccess::getGroupsByUser($id, false);
            $groupid_list = '(' . implode(',', $user_groups) . ')';


            if ($strict) {

                $subQuery_strict = $this->_db->getQuery(true)
                    ->select('group_id')
                    ->from('#__usergroups_details');
//                    ->where("dominio= '" . DOMINIO . "'");


                $query_strict = $this->_db->getQuery(true)
                    ->select('id, title')
                    ->from('#__usergroups')
                    ->where($this->_db->quoteName('parent_id') . ' IN (' . $subQuery_strict->__toString() . ')')
                    ->where('id IN ' . $groupid_list);


//          echo (string)$query_strict;

                $this->_db->setQuery($query_strict);
                $res = $this->_db->loadObjectList();

            } else {

                $subQuery = $this->_db->getQuery(true)
                    ->select('id')
                    ->from('#__usergroups')
                    ->where('id IN ' . $groupid_list)
                    ->where('parent_id= ' . $id_gruppo_piattaforme);


                $query_P = $this->_db->getQuery(true)
                    ->select('id, title')
                    ->from('#__usergroups')
                    ->where($this->_db->quoteName('parent_id') . ' IN (' . $subQuery->__toString() . ')');
                $this->_db->setQuery($query_P);
                $res = $this->_db->loadObjectList();
            }


            return $res;
        } catch (Exception $e) {
            DEBUGG::error($e, 'get_user_societa');
        }


    }

    public function get_numero_piattaforme() {

        // ritorna quante piattaforme sono definite nel sistema
        try {

            $query = $this->_db->getQuery(true);
            $query->select('COUNT(group_id) as tot_rows');
            $query->from('#__usergroups_details');

            $this->_db->setQuery($query);
            $data = $this->_db->loadAssoc();

            return $data;
        }
        catch (Exception $e) {
            DEBUGG::error($e, __FUNCTION__);
        }

    }

    public function get_user_piattaforme($id, $from_api=false)
    {
        // ritorna id e nonme di tutte le piattaforme associate un utente

        try {

            $id_gruppo_piattaforme = $this->_config->getConfigValue('id_gruppo_piattaforme');

            $user_groups = JAccess::getGroupsByUser($id, true);
            $groupid_list = '(' . implode(',', $user_groups) . ')';

            $query = $this->_db->getQuery(true)
                ->select('g.id as value, d.alias as text, dominio as dominio')
                ->from('#__usergroups as g')
                ->join('inner', '#__usergroups_details as d ON g.id = d.group_id')
                ->where("g.parent_id=" . $id_gruppo_piattaforme)
                ->where('g.id IN ' . $groupid_list);

            $this->_db->setQuery($query);
            $result = $this->_db->loadObjectList();

            return $result;

        } catch (Exception $e) {
            if ($from_api)
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_response');

            DEBUGG::error($e, 'get_user_piattaforme');
            return null;
        }


    }

    public function get_all_user_piattaforma($id_piattaforma, $ret_aziende = false, $_err_label = '') {

        try {

            $ids_aziende = array();
            $ids_users = array();
            $arr_aziende = array();
            $arr_dual = array();
            $_ret = array();

            // tutte le azienda per piattaforma
            $query_az = $this->_db->getQuery(true)
                        ->select('id AS id_azienda, title AS nome_azienda')
                        ->from('#__usergroups')
                        ->where('parent_id = ' . $this->_db->quote($id_piattaforma));
            $this->_db->setQuery($query_az);
            $ug_azienda = $this->_db->loadAssocList();

            if (count($ug_azienda) == 0)
                throw new Exception("Nessun gruppo aziende trovato per id_piattaforma: " . $id_piattaforma, E_USER_ERROR);

            foreach ($ug_azienda as $azienda_key => $azienda) {

                if (in_array($azienda['id_azienda'], $ids_aziende))
                    continue;

                $ids_aziende[] = $azienda['id_azienda'];
                $arr_aziende[$azienda['id_azienda']] = $azienda['nome_azienda'];
            }

            // tutti gli utenti per azienda
            $query_us =  $this->_db->getQuery(true)
                            ->select('user_id, group_id')
                            ->from('#__user_usergroup_map')
                            ->where('group_id IN (' . implode(",", $ids_aziende) . ')')
                            ->where('user_id > 0');
            $this->_db->setQuery($query_us);
            $ug_users = $this->_db->loadAssocList();

            if (count($ug_users) == 0)
                throw new Exception("Nessun utente trovato per id_piattaforma: " . $id_piattaforma, E_USER_ERROR);

            foreach ($ug_users as $user_key => $user) {

                // associazione azienda / utente
                $arr_dual[$user['group_id']][] = $user['user_id'];

                if (in_array($user['user_id'], $ids_users))
                    continue;

                $ids_users[] = $user['user_id'];
            }

            $_ret['users'] = $ids_users;

            if ($ret_aziende) {
                $_ret['aziende'] = $arr_aziende;
                $_ret['dual'] = $arr_dual;
            }

            return $_ret;
        }
        catch (Exception $e) {
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), (($_err_label != '') ? $_err_label : __FUNCTION__ ) . "_error");
            return null;
        }

    }

    public function get_all_tutor_piattaforma($id_piattaforma, $from_api=false)
    {

        // ritorna array di id di tutor di piattaforma

        try {
            $result = array();

            $id_gruppo_tutor_piattaforma = $this->_config->getConfigValue('id_gruppo_tutor_piattaforma');
            $all_tutor_piattaforma = JAccess::getUsersByGroup((int)$id_gruppo_tutor_piattaforma);

            foreach ($all_tutor_piattaforma as $tutor_id) {

                // per ognuno dei tutor piattaforma guardo se appartiene al gruppo piattaforma corrente
                $user_groups = array_column($this->get_user_piattaforme($tutor_id), 'value');


                if (in_array($id_piattaforma, $user_groups)) {
                    // l'utente è tutor per la piattaforma
                    array_push($result, $tutor_id);

                }

            }

            return $result;

        } catch (Exception $e) {
            if ($from_api)
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_response');

            DEBUGG::error($e, 'get_all_tutor_piattaforma');
            return null;
        }


    }

    public function get_tutor_aziendale_details($id_gruppo_societa, $from_api=false) {

        try {

            $id_tutor_az = $this->get_tutor_aziendale($id_gruppo_societa, $from_api);
            if (is_null($id_tutor_az))
                throw new Exception("Tutor non trovato per gruppo societa " . $id_gruppo_societa, E_USER_ERROR);

            return $this->get_user($id_tutor_az);

        }
        catch (Exception $e) {
            if ($from_api)
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_response');

            return null;
        }

    }

    public function get_tutor_aziendale($id_gruppo_societa, $from_api=false)
    {
        try {

            // se non impostato $id_gruppo_societa evito di eseguire una query che andrà in errore
            if (is_null($id_gruppo_societa)
                || $id_gruppo_societa == ""
                || !isset($id_gruppo_societa))
                return null;

            $id_gruppo_tutor_aziendale = $this->_config->getConfigValue('id_gruppo_tutor_aziendale');

            $query = $this->_db->getQuery(true)
                ->select('ug1.user_id')
                ->from('#__user_usergroup_map AS ug1')
                ->join('inner', '#__user_usergroup_map AS ug2 ON  ug1.user_id = ug2.user_id')
                ->where("ug1.group_id =" . $id_gruppo_societa)
                ->where("ug2.group_id =" . $id_gruppo_tutor_aziendale);

            $this->_db->setQuery($query);
            $result = $this->_db->loadResult();

            return $result;

        } catch (Exception $e) {
            if ($from_api)
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage() . " => " . $query, 'api_genera_coupon_response');

            DEBUGG::error($e, 'get_tutor_aziendale');
            return null;
        }
    }

    public function set_user_forum_moderator($user_id, $forum_id, $from_api=false)
    {

        try {

            $query = 'INSERT INTO #__kunena_user_categories (user_id, category_id, role) VALUES (' . $user_id . ', ' . $forum_id . ', 1)';
            $this->_db->setQuery($query);
            if (false === ($results = $this->_db->query())) {
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            }

            $query = 'INSERT INTO #__kunena_users (userid, moderator, rank) VALUES (' . $forum_id . ', 1, 8) ON DUPLICATE KEY UPDATE moderator=1, rank=8';
            $this->_db->setQuery($query);
            if (false === ($results = $this->_db->query())) {
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            }
            return true;

        }
        catch (Exception $e) {
            if ($from_api)
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_response');

            return false;
        }

    }

/////////////// LOGIN AS
    public function get_all_users()
    {
        try {
            $query = $this->_db->getQuery(true)
                ->select('*')
                ->from('#__users');


            $this->_db->setQuery($query);
            $res = $this->_db->loadObjectList();
            return $res;
        } catch (Exception $e) {
            DEBUGG::error($e, 'get_all_users');
        }

    }

    public static function getUserGroupName($user_id, $return_text = false)
    {


        $db = JFactory::getDBO();
        $groups = JAccess::getGroupsByUser($user_id);
        $groupid_list = '(' . implode(',', $groups) . ')';
        $query = $db->getQuery(true);
        $query->select('title');
        $query->from('#__usergroups');
        $query->where('id IN ' . $groupid_list);
        $db->setQuery($query);
        $rows = $db->loadColumn();

        if ($return_text) {
            return implode(', <br>', $rows);
        } else
            return $rows;

    }

    public function check_user($username, $password) {

        try {

            $_ret = array();

            // Get a database object
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('id, password')
                ->from('#__users')
                ->where('username = ' . $db->quote($username));

            $db->setQuery($query);
            $result = $db->loadObject();

            if (!$result)
                return "Utente non esistente!";

            $match = JUserHelper::verifyPassword($password, $result->password, $result->id);

            if (!$match)
                return "Password non corretta";

            $_ret['success'] = (int) $result->id;
            return $_ret;

        }
        catch (Exception $e) {
            return __FUNCTION__ . ' error: ' . $e->getMessage();
        }

    }

    public function get_user_quote($user_id, $anno=null, $tipo_quota=null) {

        try {

            $_ret = array();

            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('tipo_quota, anno')
                ->from('#__gg_quote_iscrizioni')
                ->where("user_id = '" . $user_id . "'");

            if (!is_null($anno)
                && $anno != "")
                $query = $query->where("anno = '" . $anno . "'");

            if (!is_null($tipo_quota)
                && $tipo_quota != "")
                $query = $query->where("tipo_quota = '" . $tipo_quota . "'");

            $query = $query->group($db->quoteName('tipo_quota'))
                ->group($db->quoteName('anno'))
                ->order('anno DESC');

            $db->setQuery($query);
            $result = $db->loadAssocList();

            // se nessun risultato restituisco un array vuoto
            if (!$result) {
                return $_ret;
            }

            return $result;

        }
        catch (Exception $e) {
            return __FUNCTION__ . ' error: ' . $e->getMessage();
        }

    }

    // tutte le colonne dell'utente community builder
    public function get_user_full_details_cb($user_id) {

        try {

            $_ret = array();

            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__comprofiler')
                ->where("user_id = '" . $user_id . "'");

            $db->setQuery($query);
            $result = $db->loadAssoc();

            // se nessun risultato restituisco un array vuoto
            if (!$result) {
                return $_ret;
            }

            return $result;

        }
        catch (Exception $e) {
            return __FUNCTION__ . ' error: ' . $e->getMessage();
        }

    }

    public function get_user_details_cb($user_id) {

        try {

            $_ret = array();

            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('cb_professionedisciplina as professione,
                                cb_laureain as tipo_laurea,
                                cb_laureanno as anno_laurea,
                                cb_datadinascita as data_nascita,
                                firstname as nome_utente,
                                lastname as cognome_utente,
                                cb_codicefiscale as codice_fiscale,
                                cb_ultimoannoinregola as ultimo_anno_pagato')
                ->from('#__comprofiler')
                ->where("user_id = '" . $user_id . "'");

            $db->setQuery($query);
            $result = $db->loadAssoc();

            // se nessun risultato restituisco un array vuoto
            if (!$result) {
                return $_ret;
            }

            return $result;

        }
        catch (Exception $e) {
            return __FUNCTION__ . ' error: ' . $e->getMessage();
        }

    }

    public function update_voucher_utilizzato($codiceVoucher, $userId, $dateTimeRef = null) {

        try {

            $dateTimeRef = is_null($dateTimeRef)
                ? date('Y-m-d H:i:s')
                : $dateTimeRef;

            $query = $this->_db->getQuery(true)
                        ->update("#__gg_quote_voucher")
                        ->set("user_id = " . $this->_db->quote($userId))
                        ->set("date = " . $this->_db->quote($dateTimeRef))
                        ->where("code = " . $this->_db->quote($codiceVoucher));

            $this->_db->setQuery($query);

            if (!$this->_db->execute()) throw new Exception("update voucher query ko -> " . $query, E_USER_ERROR);

            $_ret['success'] = 'tuttook';

            return $_ret;

        }
        catch(Exception $e) {
            return __FUNCTION__ . ' error: ' . $e->getMessage();
        }

    }

    public function update_tipo_quota_iscrizione($id_pagamento, $tipo_quota) {

        try {

            $query = $this->_db->getQuery(true);
            $query->update("#__gg_quote_iscrizioni");
            $query->set("tipo_quota = " . $this->_db->quote($tipo_quota));
            $query->where("id = " . $this->_db->quote($id_pagamento));

            $this->_db->setQuery($query);

            if (!$this->_db->execute()) throw new Exception("update tipo quota query ko -> " . $query, E_USER_ERROR);

            $_ret['success'] = 'tuttook';

            return $_ret;

        }
        catch (Exception $e) {
            return __FUNCTION__ . ' error: ' . $e->getMessage();
        }

    }

    public function update_ultimo_anno_pagato($user_id, $ultimo_anno_pagato) {

        try {

            $_ret = array();

            $query = $this->_db->getQuery(true);
            $query->update("#__comprofiler");
            $query->set("cb_ultimoannoinregola = " . $this->_db->quote($ultimo_anno_pagato));
            $query->where("user_id = " . $user_id);

            $this->_db->setQuery($query);

            if (!$this->_db->execute()) throw new Exception("update ultimo anno pagato query ko -> " . $query, E_USER_ERROR);

            $_ret['success'] = 'tuttook';

            return $_ret;

        }
        catch (Exception $e) {
            return __FUNCTION__ . ' error: ' . $e->getMessage();
        }

    }

    // inserisco pagamento servizi extra e acquisto eventi
    public function insert_user_servizi_extra($user_id,
                                              $_anno_quota,
                                              $_data_creazione,
                                              $_order_details,
                                              $totale,
                                              $_user_details = array(),
                                              $_template = 'servizi_extra',
                                              $send_email = true,
                                              $unit_id = null,
                                              $unit_gruppo = null) {

        try {

            $_ret = array();
            $insertQuotaLast = 0;
            $this->_db->transactionStart();

            $_extra_col = "";
            $_extra_insert = "";

            if (!is_null($unit_gruppo)) {
                $_extra_col = ', gruppo_corso';
                $_extra_insert = ", " . $this->_db->quote($unit_gruppo);
            }

            if ($_template == 'registrazioneasand' || $_template == 'voucher_buy_quota_asand') {
                $_extra_col .= ', stato';
                $_extra_insert .= ", " . $this->_db->quote(1);
            }

            // inserisco le righe riferite agli anni
            $query = "INSERT INTO #__gg_quote_iscrizioni (user_id,
                                                                anno,
                                                                tipo_quota,
                                                                tipo_pagamento,
                                                                data_pagamento,
                                                                totale,
                                                                dettagli_transazione
                                                                " . $_extra_col ."
                                                                )
                            VALUES ";

            $_tipo_quota = 'espen';
            $_tipo_pagamento = 'paypal';

            if ($_template == 'acquistaevento')
                $_tipo_quota = 'evento';
            else if ($_template == 'bb_buy_request') {
                $_tipo_quota = 'evento_nc';
                $_tipo_pagamento = 'bonifico';
            }
            else if ($_template == 'bb_buy_quota_asand') {
                $_tipo_quota = 'annuale';
                $_tipo_pagamento = 'bonifico';
            }
            else if ($_template == 'registrazioneasand') {
                $_tipo_quota = 'annuale';
            }
            else if ($_template == 'voucher_buy_quota_asand') {
                $_tipo_quota = 'annuale';
                $_tipo_pagamento = 'voucher';
            }

            $query .= "(
                        " . $this->_db->quote($user_id) . ",
                        " . $this->_db->quote($_anno_quota) . ",
                        " . $this->_db->quote($_tipo_quota) . ",
                        " . $this->_db->quote($_tipo_pagamento) . ",
                        " . $this->_db->quote($_data_creazione) . ",
                        " . $this->_db->quote($totale) . ",
                        " . $this->_db->quote(addslashes($_order_details)) . "
                        $_extra_insert
                       )";

            $this->_db->setQuery($query);
            $this->_db->execute();

            $insertQuotaLast = $this->_db->insertid();

            // invio email
            if ($_template == 'servizi_extra') {

                $_params = utilityHelper::get_params_from_plugin();
                $email_default = utilityHelper::get_params_from_object($_params, "email_default");

                if ($send_email)
                    utilityHelper::send_sinpe_email_pp($email_default,
                                                        $_data_creazione,
                                                        $_order_details,
                                                        $_anno_quota,
                                                        $_user_details,
                                                        0,
                                                        $totale,
                                                        $_template);

            }
            else if ($_template == 'acquistaevento'
                        || $_template == 'bb_buy_request') {

                // precarico i params del modulo
                $_params = utilityHelper::get_params_from_module();
                $ug_group = ($_template == 'bb_buy_request') ? 'ug_conferma_acquisto' : '';

                utilityHelper::processa_acquisto_evento($unit_id,
                                                        $user_id,
                                                        $totale,
                                                        $_template,
                                                        $ug_group,
                                                        $_params,
                                                        $unit_gruppo);

            }
            else if ($_template == 'bb_buy_quota_asand'
                || $_template == 'registrazioneasand'
                || $_template == 'voucher_buy_quota_asand') {

                $lastQuotaRef = ($_template == 'registrazioneasand' || $_template == 'voucher_buy_quota_asand')
                                    ? $insertQuotaLast
                                    : null;

                utilityHelper::send_acquisto_evento_email($_user_details['email'],
                                                            '',
                                                            $_user_details,
                                                            $totale,
                                                            $_data_creazione,
                                                            $_template,
                                                            $_user_details['mail_from'],
                                                            true,
                                                            $lastQuotaRef
                                                            );
            }

            $this->_db->transactionCommit();

            $_ret['success'] = "tuttook";
            $_ret['last_quota'] = $insertQuotaLast;

            return $_ret;
        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            return __FUNCTION__ . ' error: ' . $e->getMessage();
        }

    }

    // pagamento quota da bonifico (area riservata)
    public function insert_user_quote_anno_bonifico($user_id,
                                                    $_anno_quota,
                                                    $_totale,
                                                    $_dettagli_transazione = "",
                                                    $_data_pagamento = null,
                                                    $_modalita_pagamento = null,
                                                    $_tipo_quota = null,
                                                    $send_email=true) {

        try {

            $_ret = array();
            $dt = new DateTime();
            $_data_creazione = (is_null($_data_pagamento)) ? $dt->format('Y-m-d H:i:s') : $_data_pagamento;
            $_pagamento = (is_null($_modalita_pagamento)) ? 'bonifico' : $_modalita_pagamento;
            $_quota = is_null($_tipo_quota) ? 'quota' : $_tipo_quota;

            $verify_user = $this->verify_user_quote($user_id, $_anno_quota);

            if(is_array($verify_user) && count($verify_user) == 0) {

                $this->_db->transactionStart();

                $query = "INSERT INTO #__gg_quote_iscrizioni (
                                                          user_id,
                                                          anno,
                                                          tipo_quota,
                                                          tipo_pagamento,
                                                          data_pagamento,
                                                          totale,
                                                          dettagli_transazione,
                                                          stato
                                                          )
                            VALUES ";

                $query .= "(
                               " . $this->_db->quote($user_id) . ",
                               " . $this->_db->quote($_anno_quota) . ",
                               " . $this->_db->quote($_quota) . ",
                               " . $this->_db->quote($_pagamento) . ",
                               " . $this->_db->quote($_data_creazione) . ",
                               " . $this->_db->quote($_totale) . ",
                               " . $this->_db->quote($_dettagli_transazione) . ",
                               1
                            )";

                $this->_db->setQuery($query);
                $this->_db->execute();

            }elseif($verify_user['stato'] == 0){

                $this->_db->transactionStart();

                $query = "UPDATE #__gg_quote_iscrizioni
                      SET stato = 1
                      WHERE user_id = '" . $user_id . "'
                      AND anno = '" . $_anno_quota ."'";

                $this->_db->setQuery($query);
                $this->_db->execute();

            }

            // aggiorno ultimo anno pagato
            $_ultimo_anno = $this->update_ultimo_anno_pagato($user_id, $_anno_quota);
            if (!is_array($_ultimo_anno))
                throw new Exception($_ultimo_anno, 1);

            // inserisco le quote per l'utente selezionato
            $_user_details = $this->get_user_details_cb($user_id);

            // estrapolo i parametri dal plugin
            $_params = utilityHelper::get_params_from_plugin();
            $email_default = utilityHelper::get_params_from_object($_params, "email_default");
            $ug_categoria = utilityHelper::get_ug_from_object($_params, "ug_categoria");
            $ug_default = utilityHelper::get_ug_from_object($_params, "ug_default");
            $ug_extra = utilityHelper::get_ug_from_object($_params, "ug_extra");
            $gruppi_online = utilityHelper::get_ug_from_object($_params, "ug_online");
            $gruppi_moroso = utilityHelper::get_ug_from_object($_params, "ug_moroso");
            $gruppi_decaduto = utilityHelper::get_ug_from_object($_params, "ug_decaduto");

            // inserisco l'utente nel gruppo online
            $_ins_online = UtilityHelper::set_usergroup_online($user_id, $gruppi_online, $gruppi_moroso, $gruppi_decaduto);
            if (!is_array($_ins_online))
                throw new Exception($_ins_online, 1);

            // inserisco l'utente nel gruppo categoria corretto
            $_ins_categoria = utilityHelper::set_usergroup_categorie($user_id, $ug_categoria, $ug_default, $ug_extra, $_user_details);
            if (!is_array($_ins_categoria))
                throw new Exception($_ins_categoria, 1);


            $this->_db->transactionCommit();

            if ($send_email)
                utilityHelper::send_sinpe_email_pp($email_default,
                                                    $_data_creazione,
                                                    "Pagamento quota con bonifico",
                                                    $_anno_quota,
                                                    $_user_details,
                                                    $_totale,
                                                    0,
                                                    "bonifico");

            $_ret['success'] = "tuttook";

            return $_ret;

        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            return __FUNCTION__ . ' error: ' . $e->getMessage();
        }

    }

    // inserisco pagamento quote rinnovo
    public function insert_user_quote_anno($user_id,
                                           $_anno_quota,
                                           $_data_creazione,
                                           $_order_details,
                                           $totale_sinpe,
                                           $totale_espen=0,
                                           $_user_details = array(),
                                           $send_email = true,
                                           $tipo_quota = 'paypal') {

        try {

            $_ret = array();


            $this->_db->transactionStart();

            // inserisco le righe riferite agli anni
            $query = "INSERT INTO #__gg_quote_iscrizioni (user_id,
                                                                anno,
                                                                tipo_quota,
                                                                tipo_pagamento,
                                                                data_pagamento,
                                                                totale,
                                                                dettagli_transazione)
                            VALUES ";

            if($totale_sinpe && $tipo_quota == 'paypal') {
                $query .= "(
                               '" . $user_id . "',
                               '" . $_anno_quota . "',
                               'quota',
                               'paypal',
                               '" . $_data_creazione . "',
                               '" . $totale_sinpe . "',
                               '" . addslashes($_order_details) . "'
                            )";

            }elseif ($totale_espen && $tipo_quota == 'paypal') {
                $query .= ", (
                               '" . $user_id . "',
                               '" . $_anno_quota . "',
                               'espen',
                               'paypal',
                               '" . $_data_creazione . "',
                               '" . $totale_espen . "',
                               NULL
                            )";
            }elseif ($totale_espen && $tipo_quota = 'bonifico'){
                $query .= ", (
                               '" . $user_id . "',
                               '" . $_anno_quota . "',
                               'espen',
                               'bonifico',
                               '" . $_data_creazione . "',
                               '" . $totale_espen . "',
                               NULL
                            )";
            }

            $query .= ";";

            $this->_db->setQuery($query);
            $this->_db->execute();


            // aggiorno ultimo anno pagato
            $_ultimo_anno = $this->update_ultimo_anno_pagato($user_id, $_anno_quota);
            if (!is_array($_ultimo_anno))
                throw new Exception($_ultimo_anno, 1);

            // estrapolo i parametri dal plugin
            $_params = utilityHelper::get_params_from_plugin();
            $email_default = utilityHelper::get_params_from_object($_params, "email_default");
            $ug_categoria = utilityHelper::get_ug_from_object($_params, "ug_categoria");
            $ug_default = utilityHelper::get_ug_from_object($_params, "ug_default");
            $ug_extra = utilityHelper::get_ug_from_object($_params, "ug_extra");
            $gruppi_online = utilityHelper::get_ug_from_object($_params, "ug_online");
            $gruppi_moroso = utilityHelper::get_ug_from_object($_params, "ug_moroso");
            $gruppi_decaduto = utilityHelper::get_ug_from_object($_params, "ug_decaduto");

            // inserisco l'utente nel gruppo online
            $_ins_online = UtilityHelper::set_usergroup_online($user_id, $gruppi_online, $gruppi_moroso, $gruppi_decaduto);
            if (!is_array($_ins_online))
                throw new Exception($_ins_online, 1);

            // inserisco l'utente nel gruppo categoria corretto
            $_ins_categoria = utilityHelper::set_usergroup_categorie($user_id, $ug_categoria, $ug_default, $ug_extra, $_user_details);
            if (!is_array($_ins_categoria))
                throw new Exception($_ins_categoria, 1);

            $this->_db->transactionCommit();

            if ($send_email)
                utilityHelper::send_sinpe_email_pp($email_default,
                                                    $_data_creazione,
                                                    $_order_details,
                                                    $_anno_quota,
                                                    $_user_details,
                                                    $totale_sinpe,
                                                    $totale_espen);

            $_ret['success'] = "tuttook";

            return $_ret;

        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            return __FUNCTION__ . ' error: ' . $e->getMessage();
        }

    }

    // utenti iscritti a un corso
    public function get_utenti_iscritti_corso($id_corso, $users_id_arr) {

        try {

            $_ret = array();

            $colonna_nome = "";
            $colonna_cognome = "";
            $select_qry = "";

            $_integrazione = $this->_params->get('integrazione');
            switch ($_integrazione) {
                case 'eb':
                    $colonna_nome = $this->_app->getParams()->get('campo_event_booking_nome');
                    $colonna_cognome = $this->_app->getParams()->get('campo_event_booking_cognome');
                    break;

                case 'cb':
                default:
                    $colonna_nome = $this->_app->getParams()->get('campo_community_builder_nome');
                    $colonna_cognome = $this->_app->getParams()->get('campo_community_builder_cognome');
                    break;

            }

            $select_qry = 'user.user_id AS id_utente,
                            UPPER(CONCAT(
                                   COALESCE(user.' . $colonna_nome . ', ""),
                                   " ",
                                   COALESCE(user.' . $colonna_cognome . ', "")
                                   )) AS denominazione_utente';

            $query = $this->_db->getQuery(true)
                ->select($select_qry);

            if ($_integrazione == 'eb')
                $query = $query->from('#__eb_registrants AS user')
                            ->join('inner', '#__gg_unit unit ON user.id_event_booking = user.event_id AND unit.id = ' . $this->_db->quote($id_corso));
            else if ($_integrazione == 'cb')
                $query = $query->from('#__comprofiler AS user');

            if (count($users_id_arr) > 0)
                $query = $query->where('user.user_id IN (' . implode(",", $users_id_arr) . ')');

            $query = $query->order('user.' . $colonna_cognome . ', user.' . $colonna_cognome);

            $this->_db->setQuery($query);
            $results = $this->_db->loadAssocList();

            return $results;
        }
        catch (Exception $e) {
            return __FUNCTION__ . ' error: ' . $e->getMessage();
        }

    }

    // lista quote asand
    public function get_quote_asand($ug_list = null,
                                    $tipoPagamento = null,
                                    $statoPagamento = null,
                                    $_offset=0,
                                    $_limit=10,
                                    $_search=null,
                                    $_sort=null,
                                    $_order=null) {

        try {

            $_ret = array();

            $query = $this->_db->getQuery(true)
                    ->select("u.id AS user_id, u.username, u.email,
                                cp.cb_nome AS nome, cp.cb_cognome AS cognome,
                                cp.cb_codicefiscale AS codice_fiscale, cp.cb_telefono AS telefono,
                                quota.id AS id_quota, quota.anno AS anno_pagamento_quota, quota.data_pagamento, quota.totale AS totale_quota, quota.tipo_pagamento, quota.stato AS stato_pagamento, quota.stato AS stato_pagamento2
                            ");

            $count_query = $this->_db->getQuery(true)
                    ->select('COUNT(*)');

            $query = $query
                ->from('#__users u')
                ->join('inner', '#__comprofiler cp ON u.id = cp.user_id')
                ->join('inner', '#__gg_quote_iscrizioni quota ON quota.user_id = u.id');
                //->join('left', '#__user_usergroup_map gp ON u.id = gp.user_id');
                //->join('left', '#__usergroups ug ON gp.group_id = ug.id');


            $count_query = $count_query
                ->from('#__users u')
                ->join('inner', '#__comprofiler cp ON u.id = cp.user_id')
                ->join('inner', '#__gg_quote_iscrizioni quota ON quota.user_id = u.id');
                //->join('left', '#__user_usergroup_map gp ON u.id = gp.user_id');
                //->join('left', '#__usergroups ug ON gp.group_id = ug.id');


            if (!is_null($ug_list)) {
                $query = $query->where('gruppo_corso = ' . $this->_db->quote($ug_list));
                $count_query = $count_query->where('gruppo_corso = ' . $this->_db->quote($ug_list));
            }

            if (!is_null($tipoPagamento)) {
                $query = $query->where('tipo_pagamento = ' . $this->_db->quote($tipoPagamento));
                $count_query = $count_query->where('tipo_pagamento = ' . $this->_db->quote($tipoPagamento));
            }

            if (!is_null($statoPagamento)) {
                $query = $query->where('stato = ' . $this->_db->quote($statoPagamento));
                $count_query = $count_query->where('stato = ' . $this->_db->quote($statoPagamento));
            }

            // ricerca
            if (!is_null($_search)) {

                $query = $query->where('(cp.cb_nome LIKE \'%' . $_search . '%\'
                                    OR cp.cb_cognome LIKE \'%' . $_search . '%\'
                                    OR cp.cb_codicefiscale LIKE \'%' . $_search . '%\'
                                    OR quota.anno LIKE \'%' . $_search . '%\'
                                    OR quota.data_pagamento LIKE \'%' . $_search . '%\'
                                    OR quota.totale LIKE \'%' . $_search . '%\')
                                    ');

                $count_query = $count_query->where('(cp.cb_nome LIKE \'%' . $_search . '%\'
                                    OR cp.cb_cognome LIKE \'%' . $_search . '%\'
                                    OR cp.cb_codicefiscale LIKE \'%' . $_search . '%\'
                                    OR quota.anno LIKE \'%' . $_search . '%\'
                                    OR quota.data_pagamento LIKE \'%' . $_search . '%\'
                                    OR quota.totale LIKE \'%' . $_search . '%\')
                                    ');

            }

            // ordinamento per colonna - di default per id utente
            if (!is_null($_sort)
                && !is_null($_order)) {
                $query = $query->order($_sort . ' ' . $_order);
            }
            else
                $query = $query->order('u.id DESC');

            $this->_db->setQuery($query, $_offset, $_limit);
            $result = $this->_db->loadAssocList();

            $this->_db->setQuery($count_query);
            $result_count = $this->_db->loadResult();

            // se nessun risultato restituisco un array vuoto
            if (!$result) {
                return $_ret;
            }

            $_ret['rows'] = $result;
            $_ret['total_rows'] = $result_count;

            return $_ret;

        }
        catch (Exception $e) {
            return __FUNCTION__ . ' error: ' . $e->getMessage();
        }

    }

    // lista dei soci in un determinato gruppo
    public function get_soci_iscritti($ug_list=null, $_offset=0, $_limit=10, $_search=null, $_sort=null, $_order=null) {

        try {

            $_ret = array();
            $sub_q = null;

            if (!is_null($ug_list)
                ) {
                $sub_q = $this->_db->getQuery(true)
                    ->select('user_id')
                    ->from('#__user_usergroup_map')
                    ->where('group_id IN (' . $ug_list . ')');
            }

            $query = $this->_db->getQuery(true)
                    ->select("u.id AS user_id, u.username, u.email,
                                    cp.cb_nome AS nome, cp.cb_cognome AS cognome,
                                    cp.cb_codicefiscale AS codice_fiscale,
                                    COALESCE(cp.cb_datadinascita, '') AS data_nascita, cp.cb_ultimoannoinregola AS ultimo_anno,
                                    ug.title AS tipo_socio, ug.id AS id_group");

            $count_query = $this->_db->getQuery(true)
                    ->select('COUNT(*)');

            $query = $query
                    ->from('#__users u')
                    ->join('inner', '#__comprofiler cp ON u.id = cp.user_id')
                    ->join('left', '#__user_usergroup_map gp ON u.id = gp.user_id')
                    ->join('left', '#__usergroups ug ON gp.group_id = ug.id');

            $count_query = $count_query
                ->from('#__users u')
                ->join('inner', '#__comprofiler cp ON u.id = cp.user_id')
                ->join('left', '#__user_usergroup_map gp ON u.id = gp.user_id')
                ->join('left', '#__usergroups ug ON gp.group_id = ug.id');


            if (!is_null($sub_q)) {
                $query = $query->where($this->_db->quoteName('u.id') . ' IN (' . $sub_q->__toString() . ')')
                            ->where('ug.id IN (' . $ug_list . ')');
                $count_query = $count_query->where($this->_db->quoteName('u.id') . ' IN (' . $sub_q->__toString() . ')')
                    ->where('ug.id IN (' . $ug_list . ')');
            }

            // ricerca
            if (!is_null($_search)) {

                $query = $query->where('(u.username LIKE \'%' . $_search . '%\'
                                    OR u.username LIKE \'%' . $_search . '%\'
                                    OR cp.cb_nome LIKE \'%' . $_search . '%\'
                                    OR cp.cb_cognome LIKE \'%' . $_search . '%\'
                                    OR cp.cb_codicefiscale LIKE \'%' . $_search . '%\'
                                    OR cp.cb_datadinascita LIKE \'%' . $_search . '%\'
                                    OR cp.cb_ultimoannoinregola LIKE \'%' . $_search . '%\'
                                    OR ug.title LIKE \'%' . $_search . '%\')
                                    ');

                $count_query = $count_query->where('(u.username LIKE \'%' . $_search . '%\'
                                    OR u.username LIKE \'%' . $_search . '%\'
                                    OR cp.cb_nome LIKE \'%' . $_search . '%\'
                                    OR cp.cb_cognome LIKE \'%' . $_search . '%\'
                                    OR cp.cb_codicefiscale LIKE \'%' . $_search . '%\'
                                    OR cp.cb_datadinascita LIKE \'%' . $_search . '%\'
                                    OR cp.cb_ultimoannoinregola LIKE \'%' . $_search . '%\'
                                    OR ug.title LIKE \'%' . $_search . '%\')
                                    ');

            }

            // ordinamento per colonna - di default per id utente
            if (!is_null($_sort)
                && !is_null($_order)) {
                $query = $query->order($_sort . ' ' . $_order);
            }
            else
                $query = $query->order('u.id DESC');

            $this->_db->setQuery($query, $_offset, $_limit);
            $result = $this->_db->loadAssocList();

            $this->_db->setQuery($count_query);
            $result_count = $this->_db->loadResult();

            // se nessun risultato restituisco un array vuoto
            if (!$result) {
                return $_ret;
            }

            $_ret['rows'] = $result;
            $_ret['total_rows'] = $result_count;

            return $_ret;

        }
        catch (Exception $e) {
            return __FUNCTION__ . ' error: ' . $e->getMessage();
        }

    }

    // dettaglio pagamento quote per soci SINPE
    public function get_quote_iscrizione($user_id = null,
                                         $_offset=0,
                                         $_limit=10,
                                         $_search=null,
                                         $_sort=null,
                                         $_order=null,
                                         $ug_acquisto="") {

        try {

            $_ret = array();

            $_join_sel = "";
            $_extra_col = ($ug_acquisto != "") ? ", qi.gruppo_corso, un.titolo as titolo_corso" : "";

            // utente amministratore
            if (is_null($user_id)) {
                $_join_sel = ", u.username,
                                cp.cb_nome AS nome,
                                cp.cb_cognome  AS cognome,
                                UPPER(cp.cb_codicefiscale) AS codice_fiscale,
                                COALESCE(cp.cb_datadinascita, '') AS data_nascita,
                                u.email,
                                COALESCE(cp.cb_indirizzodiresidenza, '') AS indirizzo,
                                COALESCE(cp.cb_citta, '') AS citta,
                                COALESCE(cp.cb_provdiresidenza, '') AS provincia,
                                COALESCE(cp.cb_cap, '') AS cap,
                                COALESCE(cp.cb_ragionesociale, '') AS ragione_sociale,
                                COALESCE(cp.cb_partitaiva, '') AS partita_iva,
                                COALESCE(cp.cb_codicedestinatario, '') AS codice_destinatario,
                                COALESCE(cp.cb_professionedisciplina, '') AS professione,
                                COALESCE(cp.cb_telefono, '') AS telefono,
                                COALESCE(cp.cb_nazionalita, '') AS nazionalita
                                ";
            }

            $query = $this->_db->getQuery(true)
                    ->select('qi.user_id,
                                qi.id AS id_pagamento,
                                qi.anno,
                                qi.tipo_quota,
                                qi.tipo_pagamento,
                                COALESCE(DATE_FORMAT(qi.data_pagamento, "%d-%m-%Y %H:%i:%s"), "") AS data_pagamento,
                                TRUNCATE(qi.totale, 2) AS totale,
                                qi.dettagli_transazione
                            ' . $_extra_col . $_join_sel)
                    ->from('#__gg_quote_iscrizioni qi');

            $count_query = $this->_db->getQuery(true)
                    ->select('COUNT(*)')
                    ->from('#__gg_quote_iscrizioni qi');

            // utente amministratore
            if (is_null($user_id)) {
                $query = $query->join('inner', '#__users u ON qi.user_id = u.id')
                                ->join('inner', '#__comprofiler cp ON u.id = cp.user_id');
                $count_query = $count_query->join('inner', '#__users u ON qi.user_id = u.id')
                                            ->join('inner', '#__comprofiler cp ON u.id = cp.user_id');
            }

            // gruppo corso
            if ($_extra_col != "") {

                $query = $query->join('left', '#__gg_usergroup_map gm on qi.gruppo_corso = gm.idgruppo')
                                ->join('left', '#__gg_unit un on gm.idunita = un.id');

                $count_query = $count_query->join('left', '#__gg_usergroup_map gm on qi.gruppo_corso = gm.idgruppo')
                                            ->join('left', '#__gg_unit un on gm.idunita = un.id');
            }

            if (!is_null($user_id)) {
                $query = $query->where("qi.user_id = '" . $user_id . "'");
                $count_query = $count_query->where("qi.user_id = '" . $user_id . "'");
            }

            // ricerca
            if (!is_null($_search)) {

                $_admin_search = "";
                if (is_null($user_id)) {
                    $_admin_search = ' OR u.username LIKE \'%' . $_search . '%\'
                                            OR cp.cb_nome LIKE \'%' . $_search . '%\'
                                            OR cp.cb_cognome LIKE \'%' . $_search . '%\'
                                            OR cp.cb_codicefiscale LIKE \'%' . $_search . '%\'';
                }

                $query = $query->where('(qi.anno LIKE \'%' . $_search . '%\'
                                           OR qi.tipo_pagamento LIKE \'%' . $_search . '%\'
                                           OR qi.data_pagamento LIKE \'%' . $_search . '%\'
                                           OR qi.dettagli_transazione LIKE \'%' . $_search . '%\'
                                        ' . $_admin_search . ')');

                $count_query = $count_query->where('(qi.anno LIKE \'%' . $_search . '%\'
                                           OR qi.tipo_pagamento LIKE \'%' . $_search . '%\'
                                           OR qi.data_pagamento LIKE \'%' . $_search . '%\'
                                           OR qi.dettagli_transazione LIKE \'%' . $_search . '%\'
                                        ' . $_admin_search . ')');


            }

            // ordinamento per colonna - di default per id utente
            if (!is_null($_sort)
                && !is_null($_order)) {
                $query = $query->order($_sort . ' ' . $_order);
            }
            else
                $query = $query->order('qi.anno desc, qi.tipo_quota asc');

            $this->_db->setQuery($query, $_offset, $_limit);
            $result = $this->_db->loadAssocList();

            $this->_db->setQuery($count_query);
            $result_count = $this->_db->loadResult();

            // se nessun risultato restituisco un array vuoto
            if (!$result) {
                return $_ret;
            }

            $_ret['rows'] = $result;
            $_ret['total_rows'] = $result_count;

            return $_ret;

        }
        catch (Exception $e) {
            return __FUNCTION__ . ' error: ' . $e->getMessage();
        }
    }

    public function get_registration_request($user_id, $token = null) {

        try {

            $query = $this->_db->getQuery(true)
                ->select('token')
                ->from('#__gg_registration_request')
                ->where('user_id = ' . $this->_db->quote($user_id));

            if (!is_null($token))
                $query = $query->where('token = ' . $this->_db->quote($token));

            $query = $query->order('date DESC')
                        ->setLimit('1');

            $this->_db->setQuery($query);
            return $this->_db->loadResult();

        }
        catch(Exception $e) {
            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return null;
        }

    }

    public function get_quota_per_user_token($userId, $token, $anno) {

        try {

            $query = $this->_db->getQuery(true)
                ->select('subscr.*')
                ->from('#__gg_quote_iscrizioni subscr')
                ->join('inner', '#__gg_registration_request ref_token ON subscr.user_id = ref_token.user_id')
                ->where('ref_token.token = ' . $this->_db->quote($token))
                ->where('subscr.user_id = ' . $this->_db->quote($userId))
                ->where('subscr.anno = ' . $this->_db->quote($anno))
                ->order('subscr.id DESC');

            $this->_db->setQuery($query);
            return $this->_db->loadAssoc();

        }
        catch(Exception $e) {
            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return null;
        }
    }

    // quota ricercata per colonna e valore della colonna
    public function get_quota_per_id($idQuota, $targetCol = 'id', $anno = null) {

        try {

            $query = $this->_db->getQuery(true)
                ->select('*')
                ->from('#__gg_quote_iscrizioni')
                ->where($targetCol . ' = ' . $idQuota);

            if (!is_null($anno))
                $query = $query->where('anno = ' . $this->_db->quote($anno));

            $query = $query->order('id DESC');

            $this->_db->setQuery($query);
            return $this->_db->loadAssoc();

        }
        catch(Exception $e) {
            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return null;
        }
    }

    public function get_quota_user_anno($user_id, $anno = null) {

        try {

            $anno = is_null($anno)
                ? date('Y')
                : $anno;

            $query = $this->_db->getQuery(true)
                ->select('id')
                ->from('#__gg_quote_iscrizioni')
                ->where('user_id = ' . $user_id)
                ->where('anno = ' . $anno)
                ->order('id DESC');

            $this->_db->setQuery($query);
            return $this->_db->loadResult();

        }
        catch(Exception $e) {
            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return null;
        }
    }

    // inserimento di un utente in un gruppo specifico - sovrascrive la funzione nativa di joomla che per qualche motivo fallisce
    // nonostante restituisca TRUE...
    public function insert_user_into_usergroup($user_id, $group_id) {

        try {

            $this->_db->transactionStart();

            // associo utente al gruppo societa
            $insertquery_map = 'INSERT
                                    INTO #__user_usergroup_map (user_id, group_id) VALUES (' . $this->_db->quote($user_id) . ', ' . $this->_db->quote($group_id) . ')
                                            ON DUPLICATE KEY
                                            UPDATE user_id = ' . $this->_db->quote($user_id) . ',
                                            group_id = ' . $this->_db->quote($group_id);

            $this->_db->setQuery($insertquery_map);

            //if (false === $this->_db->execute())
            if (!$this->_db->execute())
                throw new Exception("Si è verificato un errore durante l'inserimento", E_USER_ERROR);

            $this->_db->transactionCommit();

            return 1;

        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return null;
        }

    }

    // dato id gruppo azienda seleziono le aziende - passo un array contenente gli id_gruppo
    public function get_utenti_per_azienda($arr_ids) {

        try {

            $_ret = [];

            $query = $this->_db->getQuery(true)
                    ->select('utenti.id AS id_utente, utenti.username, ugm.group_id AS gruppo_utente')
                    ->from('#__users AS utenti')
                    ->join('inner', '#__user_usergroup_map ugm ON utenti.id = ugm.user_id')
                    ->where('ugm.group_id IN (' . implode(",", $arr_ids) . ')');

            $this->_db->setQuery($query);
            $results = $this->_db->loadAssoclist();

            $query_count = $this->_db->getQuery(true)
                    ->select('COUNT(utenti.id) AS total_rows')
                    ->from('#__users AS utenti')
                    ->join('inner', '#__user_usergroup_map ugm ON utenti.id = ugm.user_id')
                    ->where('ugm.group_id IN (' . implode(",", $arr_ids) . ')');

            $this->_db->setQuery($query_count);
            $result_count = $this->_db->loadResult();

            $_ret['rows'] = $results;
            $_ret['total_rows'] = $result_count;

            return (is_array($_ret['rows']) && count($_ret['rows'])) ?  $_ret : null;

        }
        catch(Exception $e) {
            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return null;
        }

    }

    public function update_user_column($userId, $refColumn, $valueColumn, $refDb = "users") {

        try {

            $query = $this->_db->getQuery(true)
                    ->update("#__" . $refDb)
                    ->set($refColumn . " = " . $this->_db->quote($valueColumn))
                    ->where("id = " . $this->_db->quote($userId));

            $this->_db->setQuery((string) $query);

            if (!$this->_db->execute())
                throw new Exception("update query ko -> " . $query, E_USER_ERROR);

            return 1;

        }
        catch(Exception $e) {
            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return null;
        }
    }

    // aggiorna la password di un utente con tutti i crismi del caso
    public function update_users_password($user_id, $new_password) {

        try {

            $query = $this->_db->getQuery(true)
                    ->update("#__users")
                    ->set("password = " . $this->_db->quote(JUserHelper::hashPassword($new_password)))
                    ->where("id = " . $this->_db->quote($user_id));

            $this->_db->setQuery((string) $query);

            if (!$this->_db->execute())
                throw new Exception("update user password query ko -> " . $query, E_USER_ERROR);

            return [];

        }
        catch(Exception $e) {
            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return null;
        }

    }

    public function get_anagrafica_centri($_offset=0, $_limit=10, $_sort=null, $_order=null) {

        try {

            $_ret = array();

            $db = JFactory::getDbo();

            $count_query = $this->_db->getQuery(true)
                ->select('COUNT(*)')
                ->from('#__gg_anagrafica_centri');

            $this->_db->setQuery($count_query);
            $result_count = $this->_db->loadResult();


            $query = $db->getQuery(true)
                ->select(' id,
                             centro,
                             indirizzo,
                             telefono_responsabile,
                             telefono_servizio,
                             fax,
                             email,
                             responsabile,
                             ruolo')
                ->from('#__gg_anagrafica_centri');

            // ordinamento per colonna - di default per id centro
            if (!is_null($_sort)
                && !is_null($_order)) {
                $query = $query->order($_sort . ' ' . $_order);
            }
            else
                $query = $query->order('id ASC');

            $this->_db->setQuery($query, $_offset, $_limit);
            $result = $this->_db->loadAssocList();

            $_ret['rows'] = $result;
            $_ret['total_rows'] = $result_count;

            return (is_array($_ret['rows']) && count($_ret['rows'])) ?  $_ret : null;


        }
        catch (Exception $e) {
            return __FUNCTION__ . ' error: ' . $e->getMessage();
        }

    }

    public function get_utenti_dettagli_per_azienda($id_azienda, $_offset, $_limit, $_search, $_sort, $_order) {

        try {

            $_ret = [];


            $query = $this->_db->getQuery(true)
                ->select('u.id AS user_id, u.username, c.cb_nome as nome, c.cb_cognome as cognome, u.email, u.block as stato_user')
                ->from('#__users as u')
                ->join('inner', '#__user_usergroup_map as ugm ON u.id = ugm.user_id')
                ->join('inner', '#__comprofiler as c on ugm.user_id = c.user_id')
                ->where('ugm.group_id =' . $id_azienda );


            $query_count = $this->_db->getQuery(true)
                ->select('COUNT(u.id) AS total_rows')
                ->from('#__users AS u')
                ->join('inner', '#__user_usergroup_map ugm ON u.id = ugm.user_id')
                ->join('inner', '#__comprofiler as c on u.id = c.user_id')
                ->where('ugm.group_id =' . $id_azienda );

            // ordinamento per colonna - di default per id utente
            if (!is_null($_sort)
                && !is_null($_order)) {
                $query = $query->order($_sort . ' ' . $_order);
            }
            else
                $query = $query->order('u.id DESC');

            $this->_db->setQuery($query, $_offset, $_limit);
            $results = $this->_db->loadAssocList();

            $this->_db->setQuery($query_count);
            $result_count = $this->_db->loadResult();


            $_ret['rows'] = $results;
            $_ret['total_rows'] = $result_count;

            return (is_array($_ret['rows']) && count($_ret['rows'])) ?  $_ret : null;

        }
        catch(Exception $e) {
            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return null;
        }

    }


    public function update_accesso_utente($user_id, $stato_user) {

        try {

            $_ret = array();

            if((int)$stato_user === 0) {

                $query = $this->_db->getQuery(true)
                    ->update('#__users')
                    ->set('block = 1')
                    ->where('id = ' . $user_id);
                $this->_db->setQuery($query);
               $result =  $this->_db->execute();


            }else if((int)$stato_user === 1){

                $query = $this->_db->getQuery(true)
                    ->update('#__users')
                    ->set('block = 0')
                    ->where('id = ' . $user_id);
                $this->_db->setQuery($query);
                $result =  $this->_db->execute();
            }


            if (!$result) throw new Exception("update accesso utente ko -> " . $query, E_USER_ERROR);

            $_ret['success'] = 'tuttook';

            return $_ret;

        }
        catch (Exception $e) {
            return __FUNCTION__ . ' error: ' . $e->getMessage();
        }

    }

    public function get_dettagli_user_piattaforma($id_piattaforma, $_offset, $_limit, $_search, $_sort, $_order) {

        try {

            $ids_aziende = array();
            $_ret = array();

            // tutte le azienda per piattaforma
            $query_az = $this->_db->getQuery(true)
                ->select('id AS id_azienda, title AS nome_azienda')
                ->from('#__usergroups')
                ->where('parent_id = ' . $this->_db->quote($id_piattaforma));
            $this->_db->setQuery($query_az);
            $ug_piattaforma = $this->_db->loadAssocList();


            // tutte le azienda per piattaforma
            $query_az = $this->_db->getQuery(true)
                ->select('id AS id_azienda')
                ->from('#__usergroups')
                ->where('parent_id = ' . $this->_db->quote($ug_piattaforma[0]['id_azienda']));
            $this->_db->setQuery($query_az);
            $ug_azienda = $this->_db->loadAssocList();



            if (count($ug_azienda) == 0)
                throw new Exception("Nessun gruppo aziende trovato per id_piattaforma: " . $id_piattaforma, E_USER_ERROR);

            foreach ($ug_azienda as $azienda_key => $azienda) {

                if (in_array($azienda['id_azienda'], $ids_aziende))
                    continue;

                $ids_aziende[] = $azienda['id_azienda'];

            }


            $query_count = $this->_db->getQuery(true)
                ->select('u.id AS user_id, u.username, c.cb_nome as nome, c.cb_cognome as cognome, u.email, u.block as stato_user')
                ->from('#__users as u')
                ->join('inner', '#__user_usergroup_map as ugm ON u.id = ugm.user_id')
                ->join('inner', '#__comprofiler as c on ugm.user_id = c.user_id')
                ->where('ugm.group_id IN (' . implode(",", $ids_aziende) . ')')
                ->where('ugm.user_id > 0');
            $this->_db->setQuery($query_count);

            // tutti gli utenti per azienda
            $query_us =  $this->_db->getQuery(true)
                ->select('u.id AS user_id, u.username, c.cb_nome as nome, c.cb_cognome as cognome, u.email, u.block as stato_user')
                ->from('#__users as u')
                ->join('inner', '#__user_usergroup_map as ugm ON u.id = ugm.user_id')
                ->join('inner', '#__comprofiler as c on ugm.user_id = c.user_id')
                ->where('ugm.group_id IN (' . implode(",", $ids_aziende) . ')')
                ->where('ugm.user_id > 0');
            $this->_db->setQuery($query_us);



            // ordinamento per colonna - di default per id utente
            if (!is_null($_sort)
                && !is_null($_order)) {
                $query = $query_us->order($_sort . ' ' . $_order);
            }
            else
                $query = $query_us->order('u.id DESC');

            $this->_db->setQuery($query, $_offset, $_limit);
            $results = $this->_db->loadAssocList();

            $this->_db->setQuery($query_count);
            $result_count = $this->_db->loadResult();


            $_ret['rows'] = $results;
            $_ret['total_rows'] = $result_count;

            return (is_array($_ret['rows']) && count($_ret['rows'])) ?  $_ret : null;
        }
        catch (Exception $e) {

            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return null;
        }

    }

    public function insert_user_quote_stato_bonifico($user_id,
                                                    $_anno_quota,
                                                    $_data_pagamento = null,
                                                    $totale_sinpe,
                                                     $totale_espen = 0) {

        try {

            $_ret = array();
            $dt = new DateTime();
            $_data_creazione = (is_null($_data_pagamento)) ? $dt->format('Y-m-d H:i:s') : $_data_pagamento;


            $this->_db->transactionStart();

            $query = "INSERT INTO #__gg_quote_iscrizioni (
                                                          user_id,
                                                          anno,
                                                          tipo_quota,
                                                          tipo_pagamento,
                                                          data_pagamento,
                                                          totale,
                                                          dettagli_transazione,
                                                          gruppo_corso,
                                                          stato
                                                          )
                            VALUES ";

            if($totale_sinpe > 0 && $totale_espen == 0) {

                $query .= "(
                               '" . $user_id . "',
                               '" . $_anno_quota . "',
                               'quota',
                               'bonifico',
                               '" . $_data_creazione . "',
                               '" . $totale_sinpe . "',
                               NULL,
                               0,
                               0
                            )";

            }elseif ($totale_espen > 0 && $totale_sinpe == 0) {
                $query .= "(
                               '" . $user_id . "',
                               '" . $_anno_quota . "',
                               'espen',
                               'bonifico',
                               '" . $_data_creazione . "',
                               '" . $totale_espen . "',
                               NULL,
                               0,
                               0
                            )";
            }elseif ($totale_espen > 0 && $totale_sinpe > 0) {
                $query .= "(
                               '" . $user_id . "',
                               '" . $_anno_quota . "',
                               'espen + quota',
                               'bonifico',
                               '" . $_data_creazione . "',
                               '" . $totale_sinpe . "',
                               NULL,
                               0,
                               0
                            )";
            }


            $this->_db->setQuery($query);
            $this->_db->execute();


            $this->_db->transactionCommit();


            $_ret['success'] = "tuttook";

            return $_ret;

        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            return __FUNCTION__ . ' error: ' . $e->getMessage();
        }

    }



    public function verify_user_quote($user_id, $anno) {

        try {

            $_ret = array();

            $query = $this->_db->getQuery(true)
                ->select('anno ,tipo_quota ,stato')
                ->from('#__gg_quote_iscrizioni')
                ->where('user_id = ' . $user_id)
                ->where('anno = ' . $anno);


            $this->_db->setQuery($query);
            $result = $this->_db->loadAssocList();

            // se nessun risultato restituisco un array vuoto
            if (!$result) {
                return $_ret;
            }

            return $result;

        }
        catch (Exception $e) {
            return __FUNCTION__ . ' error: ' . $e->getMessage();
        }

    }



    public function get_societa_new_user($nome_societa)
    {

        $res = array();

        try {

            $id_gruppo_piattaforme = $this->_config->getConfigValue('id_gruppo_piattaforme');

            $subQuery = $this->_db->getQuery(true)
                    ->select('id')
                    ->from('#__usergroups')
                    ->where('parent_id= ' . $id_gruppo_piattaforme);


            $query_P = $this->_db->getQuery(true)
                ->select('id')
                ->from('#__usergroups')
                ->where($this->_db->quoteName('parent_id') . ' IN (' . $subQuery->__toString() . ')')
                ->where('title = "' . $nome_societa . '"');
            $this->_db->setQuery($query_P);
            $res = $this->_db->loadObject();

            return $res;
        } catch (Exception $e) {
            DEBUGG::error($e, 'get_societa_new_user');
        }


    }

    public static function get_user_by_id($id) {

        try {

            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('id, name, email')
                ->from('#__users')
                ->where("id = '" . $id . "'");

            $db->setQuery($query);

            if (false === ($results = $db->loadObjectList())) {
                throw new RuntimeException($db->getErrorMsg(), E_USER_ERROR);
            }

            return isset($results[0]) ? $results[0] : null;

        }
        catch (Exception $e) {
            DEBUGG::error($e, __FUNCTION__);
        }

    }

    public static function get_user_by_usergroup($id) {

        try {

            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('user_id')
                ->from('#__user_usergroup_map')
                ->where('group_id = ' . $db->quote($id))
                ->group('user_id');

            $db->setQuery($query);

            if (false === ($results = $db->loadAssocList())) {
                throw new RuntimeException($db->getErrorMsg(), E_USER_ERROR);
            }

            return isset($results) ? $results : null;

        }
        catch (Exception $e) {
            DEBUGG::error($e, __FUNCTION__);
        }

    }

    public function get_codice_votazione($user_id)
    {
        try {

            $id_user = UtilityHelper::encrypt_decrypt('encrypt', $user_id, 'Sinpe', '2023');

            $query = $this->_db->getQuery(true)
                ->select('*')
                ->from('#__gg_cod_votazioni_users as c')
                ->where('c.id_user="' . ($id_user) . '"');


            $this->_db->setQuery($query);
            if (false === ($results = $this->_db->loadAssoc()))
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);

            $_codice = empty($results) ? array() : $results;


        } catch (Exception $e) {
            utilityHelper::make_debug_log(__FUNCTION__, $e , __FUNCTION__);
        }
        return $_codice;
    }
}

