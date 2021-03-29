<?php
/**
 * Created by IntelliJ IDEA.
 * User: Francesca
 * Date: 26/01/2021
 * Time: 09:10
 */
defined('_JEXEC') or die;

class gglmsControllerMt extends JControllerLegacy {

    private $_user;
    private $_japp;
    public $_params;
    public $_db;
    private $_config;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();

        $this->_user = JFactory::getUser();
        $this->_db = &JFactory::getDbo();
        $this->_config = new gglmsModelConfig();

    }

    public function get_tz() {

        $oggi = '2020-11-16T07:21:36Z';
        $dt = new DateTime($oggi, new DateTimeZone('UTC'));
        $dt->setTimezone(new DateTimeZone('Europe/Rome'));
        echo $dt->format('Y-m-d H:i:s');

        $this->_japp->close();

    }

    public function batch_responsabili() {

        $_ret = array();
        $_ending_msg = "";

        try {

            $dt = new DateTime();
            $_arr_resp = array();
            $_insert_arr = array();

            $target = $_SERVER['DOCUMENT_ROOT'] . '/batch/' . $dt->format('Ymd') . '_IMPORT_RESPONSABILI.txt';
            if (!file_exists($target))
                throw new Exception("File: " . $target . " non trovato", 1);

            $fp = @fopen($target, 'r');

            if (empty($fp))
                throw new Exception("File " . $target . " non leggibile", 1);

            if (!$fp)
                throw new Exception("File " . $target . " handler file non valido", 1);

            $ug_direttori = JRequest::getVar('ug_direttori');
            if (!isset($ug_direttori)
                || $ug_direttori == "")
                throw new Exception("Gruppo direttori non definito", 1);

            $insert_map = "INSERT INTO #__ggif_direttore_dipendenti_map
                                    (userid_direttore, userid_dipendente) VALUES ";
            $insert_group = "INSERT INTO #__user_usergroup_map 
                                    (user_id, group_id) VALUES ";
            $counter = 0;
            $rows = explode("\n", fread($fp, filesize($target)));
            foreach ($rows as $key => $row) {

                $_tmp = array();
                if ($counter == 0
                    || $row == "") {
                    $counter++;
                    continue;
                }

                $_tmp = explode(";", trim($row));
                // gli id responsabile che poi saranno usati per inserimento in usergroups
                if (!in_array($_tmp[0], $_arr_resp))
                    $_arr_resp[] = $_tmp[0];

                $_tmp_query = " (
                                       " . $this->_db->quote($_tmp[0]) . ",
                                       " . $this->_db->quote($_tmp[1]) . "
                                    ), ";
                $_insert_arr[] = $_tmp_query;

                $counter++;

            }

            if (count($_insert_arr) == 0)
                throw new Exception("Nessun valore da inserire a database", 1);

            $counter = 0;
            $_arr_query_chunked = array_chunk($_insert_arr, 500);

            $this->_db->transactionStart();

            // tronco tabella ggif_direttore_dipendenti_map
            $truncate_query = "TRUNCATE #__ggif_direttore_dipendenti_map";
            $this->_db->setQuery($truncate_query);
            if (!$this->_db->execute())
                throw new Exception("Query truncate errore -> " . $truncate_query, 1);

            foreach ($_arr_query_chunked as $key => $sub_query) {

                $_executed_query = "";

                foreach ($sub_query as $kk => $vv) {

                    $_executed_query .= $vv;

                }

                $_executed_query = (substr(trim($_executed_query), -1) == ",") ? substr(trim($_executed_query), 0, -1) : $_executed_query;
                $_row_query = $insert_map . $_executed_query;

                $this->_db->setQuery($_row_query);
                if (!$this->_db->execute())
                    throw new Exception("Query inserimento errore -> " . $_row_query, 1);

                $counter++;
            }

            // cancello tutti gli utenti appartenenti al gruppo direttori che viene passato come parametro
            $delete_ug = "DELETE FROM #__user_usergroup_map
                            WHERE group_id = " . $this->_db->quote($ug_direttori);
            $this->_db->setQuery($delete_ug);
            if (!$this->_db->execute())
                throw new Exception("Query inserimento errore -> " . $delete_ug, 1);

            // inserisco tutti gli utenti appartenenti al gruppo direttori
            $_insert_arr = array();
            foreach ($_arr_resp as $resp) {

                $_tmp_query = "(
                                " . $this->_db->quote($resp) . ",
                                " . $this->_db->quote($ug_direttori) . "
                            ), ";

                $_insert_arr[] = $_tmp_query;

            }

            if (count($_insert_arr) == 0)
                throw new Exception("Nessun gruppo da inserire a database", 1);

            $_arr_query_chunked = array_chunk($_insert_arr, 500);

            foreach ($_arr_query_chunked as $key => $sub_query) {

                $_executed_query = "";

                foreach ($sub_query as $kk => $vv) {

                    $_executed_query .= $vv;

                }

                $_executed_query = (substr(trim($_executed_query), -1) == ",") ? substr(trim($_executed_query), 0, -1) : $_executed_query;
                $_row_query = $insert_group . $_executed_query;

                $this->_db->setQuery($_row_query);
                if (!$this->_db->execute())
                    throw new Exception("Query inserimento errore -> " . $_row_query, 1);

                $counter++;
            }

            $this->_db->transactionCommit();
            $_ending_msg = " SUCCESS: " . $dt->format('d/m/Y H:i:s') . " operazione conclusa";
        }
        catch (Exception $e) {
            $this->_db->transactionRollback();
            $_ending_msg = " ERROR: " . $e->getMessage();
        }

        DEBUGG::log($_ending_msg,__FUNCTION__,0,1);
        $this->_japp->close();

    }

    public function _get_config() {

        $_check = $this->_config->getConfigValue('cassu');
        var_dump($_check);

        if ((int) $_check == 0
            && !is_null($_check))
            echo "ZERO";
        else
            echo "NON ZERO";

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
