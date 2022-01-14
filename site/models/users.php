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

    private function get_user_joomla($id)
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
        // ritorna id e nome di tutte le piattaforme associate un utente

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

    public function get_tutor_aziendale_details($id_gruppo_societa, $from_api) {

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
                UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), 'api_genera_coupon_response');

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

    public function update_tipo_quota_iscrizione($id_pagamento, $tipo_quota) {

        try {

            $query = $this->_db->getQuery(true);
            $query->update("#__gg_quote_iscrizioni");
            $query->set("tipo_quota = " . $this->_db->quote($tipo_quota));
            $query->where("id = " . $this->_db->quote($id_pagamento));

            $this->_db->setQuery($query);
            $this->_db->execute();

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
            $this->_db->execute();

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
            $this->_db->transactionStart();

            $_extra_col = "";
            $_extra_insert = "";
            if (!is_null($unit_gruppo)) {
                $_extra_col = ', gruppo_corso';
                $_extra_insert = ", " . $this->_db->quote($unit_gruppo);
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
                $_params = UtilityHelper::get_params_from_module();
                $ug_group = ($_template == 'bb_buy_request') ? 'ug_conferma_acquisto' : '';

                UtilityHelper::processa_acquisto_evento($unit_id,
                                                        $user_id,
                                                        $totale,
                                                        $_template,
                                                        $ug_group,
                                                        $_params,
                                                        $unit_gruppo);

            }

            $this->_db->transactionCommit();



            $_ret['success'] = "tuttook";

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

            $this->_db->transactionStart();

            $query = "INSERT INTO #__gg_quote_iscrizioni (
                                                          user_id,
                                                          anno,
                                                          tipo_quota,
                                                          tipo_pagamento,
                                                          data_pagamento,
                                                          totale,
                                                          dettagli_transazione
                                                          )
                            VALUES ";

            $query .= "(
                               " . $this->_db->quote($user_id) . ",
                               " . $this->_db->quote($_anno_quota) . ",
                               " . $this->_db->quote($_quota) . ",
                               " . $this->_db->quote($_pagamento) . ",
                               " . $this->_db->quote($_data_creazione) . ",
                               " . $this->_db->quote($_totale) . ",
                               " . $this->_db->quote($_dettagli_transazione) . "
                            )";

            $this->_db->setQuery($query);
            $this->_db->execute();

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
                                           $send_email = true) {

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

            $query .= "(
                               '" . $user_id . "',
                               '" . $_anno_quota . "',
                               'quota',
                               'paypal',
                               '" . $_data_creazione . "',
                               '" . $totale_sinpe . "',
                               '" . addslashes($_order_details) . "'
                            )";

            if ($totale_espen)
                $query .= ", (
                               '" . $user_id . "',
                               '" . $_anno_quota . "',
                               'espen',
                               'paypal',
                               '" . $_data_creazione . "',
                               '" . $totale_espen . "',
                               NULL
                            )";

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
                    ->select('u.id AS user_id, u.username, u.email,
                                    cp.cb_nome AS nome, cp.cb_cognome AS cognome,
                                    cp.cb_codicefiscale AS codice_fiscale, cp.cb_ultimoannoinregola AS ultimo_anno,
                                    ug.title AS tipo_socio, ug.id AS id_group');

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
                                    OR cp.cb_ultimoannoinregola LIKE \'%' . $_search . '%\'
                                    OR ug.title LIKE \'%' . $_search . '%\')
                                    ');

                $count_query = $count_query->where('(u.username LIKE \'%' . $_search . '%\'
                                    OR u.username LIKE \'%' . $_search . '%\'
                                    OR cp.cb_nome LIKE \'%' . $_search . '%\'
                                    OR cp.cb_cognome LIKE \'%' . $_search . '%\'
                                    OR cp.cb_codicefiscale LIKE \'%' . $_search . '%\'
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
                                u.email,
                                COALESCE(cp.cb_indirizzodiresidenza, '') AS indirizzo,
                                COALESCE(cp.cb_citta, '') AS citta,
                                COALESCE(cp.cb_provdiresidenza, '') AS provincia,
                                COALESCE(cp.cb_cap, '') AS cap,
                                COALESCE(cp.cb_ragionesociale, '') AS ragione_sociale,
                                COALESCE(cp.cb_partitaiva, '') AS partita_iva,
                                COALESCE(cp.cb_codicedestinatario, '') AS codice_destinatario,
                                COALESCE(cp.cb_professionedisciplina, '') AS professione,
                                COALESCE(cp.cb_telefono, '') AS telefono
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

    // il numero di utenti per un gruppo
    public function get_users_per_gruppo($group_id) {

        try {
            $query = $this->_db->getQuery(true)
                ->select('user_id')
                ->from('#__user_usergroup_map')
                ->where('group_id = ' . $this->_db->quote($group_id))
                ->group('user_id');

            $this->_db->setQuery($query);
            return $this->_db->loadAssocList();
        }
        catch (Exception $e) {
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return null;
        }

    }

    // seleziono tutti i record dal master delle farmacie
    public function get_farmacie($hh_store_code = null, $db_option = array()) {

        try {

            // gestione db esterno
            if (count($db_option) > 0)
                $this->_db = JDatabaseDriver::getInstance($db_option);

            $query = $this->_db->getQuery(true)
                ->select('*')
                ->from('#__gg_master_farmacie');

            if (!is_null($hh_store_code))
                $query = $query->where('hh_store_code = ' . $this->_db->quote($hh_store_code));
            else
                $query = $query->order('id');

            $this->_db->setQuery($query);
            if (is_null($hh_store_code))
                $results = $this->_db->loadAssocList();
            else
                $results = $this->_db->loadAssoc();

            return $results;

        }
        catch (Exception $e) {
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return null;
        }

    }

    // prendo il riferimento dell'utente alla farmacia
    public function get_user_farmacia($user_id, $cb_codice_esterno_cdc_3 = null, $db_option = array()) {

        try {

            // gestione db esterno
            if (count($db_option) > 0)
                $this->_db = JDatabaseDriver::getInstance($db_option);

            $query = $this->_db->getQuery(true)
                    ->select('*')
                    ->from('#__gg_farmacie_dipendenti')
                    ->where('user_id = ' . $this->_db->quote($user_id));

            if (!is_null($cb_codice_esterno_cdc_3))
                $query = $query->where('codice_esterno_cdc_3 = ' . $this->_db->quote($cb_codice_esterno_cdc_3));

            $query = $query->order('id DESC');

            $this->_db->setQuery($query);
            $result = $this->_db->loadAssoc();

            return $result;

        }
        catch (Exception $e) {
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return null;
        }

    }

    // aggiornamento dei riferimenti utente nella tabella di associazione con le farmacie
    public function update_user_farmacia($user_id, $cb_codice_esterno_cdc_3, $cb_data_licenziamento, $db_option = array()) {

        try {

            // gestione db esterno
            if (count($db_option) > 0)
                $this->_db = JDatabaseDriver::getInstance($db_option);

            $this->_db->transactionStart();

            $query = $this->_db->getQuery(true)
                    ->update("#__gg_farmacie_dipendenti")
                    ->set("data_licenziamento = " . $this->_db->quote($cb_data_licenziamento))
                    ->where("user_id = " . $this->_db->quote($user_id))
                    ->where("codice_esterno_cdc_3 = " . $this->_db->quote($cb_codice_esterno_cdc_3));

            $this->_db->setQuery($query);
            if (!$this->_db->execute())
                throw new Exception("Query aggiornamento fallita", E_USER_ERROR);

            $this->_db->transactionCommit();

            return 1;
        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return null;
        }

    }

    // inserimento dell'utente nella tabella di associazione con le farmacie
    public function insert_user_farmacia($user_id,
                                         $id_gruppo,
                                         $cb_codice_esterno_cdc_3,
                                         $cb_data_inizio_rapporto,
                                         $cb_data_licenziamento,
                                         $db_option = array()) {

        try {

            // gestione db esterno
            if (count($db_option) > 0)
                $this->_db = JDatabaseDriver::getInstance($db_option);

            $this->_db->transactionStart();

            $query = "INSERT INTO #__gg_farmacie_dipendenti (
                                                          user_id,
                                                          id_gruppo,
                                                          codice_esterno_cdc_3,
                                                          data_assunzione,
                                                          data_licenziamento
                                                          )
                            VALUES ";

            $query .= "(
                               " . $this->_db->quote($user_id) . ",
                               " . $this->_db->quote($id_gruppo) . ",
                               " . $this->_db->quote($cb_codice_esterno_cdc_3) . ",
                               " . $this->_db->quote($cb_data_inizio_rapporto) . ",
                               " . $this->_db->quote($cb_data_licenziamento) . "
                            )";

            $this->_db->setQuery($query);
            if (!$this->_db->execute())
                throw new Exception("Query inserimento fallita", E_USER_ERROR);

            $this->_db->transactionCommit();

            return 1;

        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return null;
        }

    }

    // controllo se l'utente è già stato attivato
    public function check_activation_user_farmarcie($user_id, $codice_fiscale) {

        try {

            // come semaforo prendo la colonna block di users non più l'id di gg_attivazione_dipendenti_farmacie
            // se 1 ritorno false (utente bloccato) altrimenti ritorno true
            // ->select('df.id')
            $query = $this->_db->getQuery(true)
                ->select('u.block')
                ->from('#__gg_attivazione_dipendenti_farmacie df')
                ->join('inner', '#__users as u on u.id = df.user_id')
                ->where('df.user_id = ' . $this->_db->quote($user_id))
                ->where('df.codice_fiscale = ' . $this->_db->quote($codice_fiscale));

            $this->_db->setQuery($query);
            $result = $this->_db->loadResult();

            // return $result['id'] ? true : false;

            // non ritorna niente non è nella tabella
            if (is_null($result))
                return false;

            // se esiste ed è 1 (quindi bloccato) ritorno false perchè deve ancora completare il login
            return ((int) $result == 1) ? false : true;

        }
        catch (Exception $e) {
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return null;
        }

    }

    // cancello l'utente dalla tabella di attivazione
    public function delete_activation_user_farmarcie($user_id, $codice_fiscale) {

        try {

            $query = "DELETE FROM #__gg_attivazione_dipendenti_farmacie
                        WHERE user_id = " . $this->_db->quote($user_id) . "
                        AND codice_fiscale = " . $this->_db->quote($codice_fiscale);

            $this->_db->setQuery($query);
            if (!$this->_db->execute())
                throw new Exception("Query cancellazione fallita", E_USER_ERROR);

            return true;

        }
        catch (Exception $e) {
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return null;
        }

    }

    // inserisco l'utente nella tabella di attivazione
    public function insert_activation_user_farmarcie($user_id, $codice_fiscale) {

        try {

            $query = "INSERT INTO #__gg_attivazione_dipendenti_farmacie
                                    (
                                     user_id,
                                     codice_fiscale
                                     )
                                    VALUES (
                                        " . $this->_db->quote($user_id) . ",
                                        " . $this->_db->quote(strtoupper($codice_fiscale)) . "
                                    )";

            $this->_db->setQuery($query);
            if (!$this->_db->execute())
                throw new Exception("Query aggiornamento fallita", E_USER_ERROR);

            return true;

        }
        catch (Exception $e) {
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return null;
        }

    }

    // imposto la password del dipendente farmacia
    public function update_password_user_farmacia($codice_fiscale, $password) {

        try {

            $query = $this->_db->getQuery(true);
            $query->update("#__users");
            $query->set("password = ". $this->_db->quote(JUserHelper::hashPassword($password)));
            $query->where("username = ". $this->_db->quote($codice_fiscale));

            $this->_db->setQuery($query);
            $this->_db->execute();

            return true;
        }
        catch (Exception $e) {
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return null;
        }

    }

    // utenti attivi in base alle metriche della farmacia block = 0 e relativo gruppo farmacia
    public function get_activated_users_details()
    {

        try {

            $query_mode = "SET SQL_MODE='';";
            $this->_db->setQuery($query_mode);

            $query = "SELECT u.id AS id_utente,
                        u.name AS nominativo,
                        u.username AS codice_fiscale,
                        u.email AS email,
                        COALESCE(comp.cb_descrizionequalifica, '') AS qualifica,
                        jgmf.ragione_sociale
                        FROM jos_users u
                        JOIN jos_user_usergroup_map juum ON u.id = juum.user_id
                        JOIN jos_usergroups ju2 ON juum.group_id = ju2.id
                        JOIN jos_gg_master_farmacie jgmf ON ju2.id = jgmf.id_gruppo
                        JOIN jos_comprofiler comp ON u.id = comp.user_id
                        WHERE u.block = 0
                        GROUP BY u.username";

            $this->_db->setQuery($query);
            $results = $this->_db->loadAssocList();

            return $results;

        }
        catch(Exception $e) {
            UtilityHelper::make_debug_log(__FUNCTION__, $e->getMessage(), __FUNCTION__ . "_error");
            return null;
        }

    }



}

