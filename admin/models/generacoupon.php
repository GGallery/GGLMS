<?php

/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
require_once 'libs/getid3/getid3.php';

class gglmsModelGeneraCoupon extends JModelAdmin {

    public function getForm($data = array(), $loadData = true) {
        // Get the form.
//        $form = $this->loadForm('com_gglms.file', 'file', array('control' => 'jform', 'load_data' => $loadData));

//        return $form;
    }


    protected function loadFormData() {
    }

    /*
     * Verifico la durata del contenuto 
     */

    public function getTable($name = '', $prefix = 'gglmsTable', $options = array()) {
    }

    public function getItem($pk = null) {

        if ($item = parent::getItem($pk)) {
        }
        return $item;
    }


    private function _generate_coupon($prefisso) {
        return str_replace(' ', '_', substr($prefisso, 0, 5)) . str_replace('0', 'k', md5(uniqid('', true)));
    }

    public function generate() {
        try {

            if(is_array($_REQUEST['id_gruppi']))
                $_REQUEST['id_gruppi']="'".implode(",", $_REQUEST['id_gruppi'])."'";

            $config = JFactory::getConfig();
            $user = JFactory::getUser();
            $user_id = $user->get('id');
            $db= JFactory::getDBO();

            $return[] = array();

            $group_id=1; //a caso standard

            // creo i coupon
            $coupons = array();
            $values = array();
            if($_REQUEST['modocoupon']=='automatica') {

                for ($i = 0; $i < $_REQUEST['quantita']; $i++) {
                    $prefisso = $_REQUEST['prefisso'];
                    $coupons[$i] = $this->_generate_coupon($prefisso);
                    $values[] = sprintf("('%s', '%s', %d, '%s', %d, %d, %d, %d, %d, %s, %s)", $coupons[$i], $_REQUEST['course_id'], $group_id, $_REQUEST['id_iscrizione'], $_REQUEST['attestato'], $_REQUEST['id_societa'], "1", 0, $_REQUEST['durata'], 'now()', $_REQUEST['id_gruppi']);

                }
            }else{
                //var_dump($_REQUEST['coupon']);die;
                $coupons = explode("\n", str_replace("\r", "",$_REQUEST['coupon'][0]));
                foreach ($coupons as $coupon){
                    if(strlen($coupon)>0)
                        $values[] = sprintf("('%s', '%s', %d, '%s', %d, %d, %d, %d, %d, %s, %s)", $coupon, $_REQUEST['course_id'], $group_id, $_REQUEST['id_iscrizione'], $_REQUEST['attestato'], $_REQUEST['id_societa'], "1", 0, $_REQUEST['durata'], 'now()', $_REQUEST['id_gruppi']);

                }

            }

            // li inserisco nel DB
            $query = 'INSERT INTO #__gg_coupon (coupon, corsi_abilitati, gruppo, id_iscrizione, attestato, id_societa, abilitato, trial, durata, data_abilitazione, id_gruppi) VALUES ' . join(',', $values).' ON DUPLICATE KEY UPDATE data_utilizzo=NOW(), id_gruppi='.$_REQUEST['id_gruppi'];

            echo $query;
            $db->setQuery($query);

            echo "inserimento".$db->execute();


//            if (false === $db->execute())
//                throw new RuntimeException($db->getErrorMsg(), E_USER_ERROR);

            //// INVIO EMAIL ////
            $mailer = JFactory::getMailer();
            $mailer->setSender($config->get('mailfrom'));

            $recipient = array($_REQUEST['mail_destinatario']);
            $mailer->addRecipient($recipient);

            $mailer->setSubject('Generazione Coupon '.$config->get('sitename'));
            $mailer->isHTML(true);


            $body= '<h2>Elenco dei coupon generati</h2>';
            $body.= 'QUANTITA: '.count($coupons).'<br><br>';

            foreach ($coupons as $coupon) {
                $body .= $coupon . '<br>';
            }

            $mailer->setBody($body);

            if (!$mailer->Send())
                throw new RuntimeException('Error sending mail', E_USER_ERROR);

            $return = $coupons;
        } catch (Exception $e) {
            $return['errore'] = $e;
            debug::exception($e);
        }
        return $return;
    }

    public function getGruppiSocieta(){
        try {
            $db = JFactory::getDbo();
            $db->setQuery("SELECT  config_value FROM #__gg_configs WHERE config_key= 'id_gruppo_societa'");
            $gruppo_societa = $db->loadResult();

            $query = $this->_db->getQuery(true)
                ->select('*')
                ->from('#__usergroups AS ug')
                ->where('parent_id = '.$gruppo_societa);

            $db->setQuery($query);
            $data = $db->loadObjectList();

            return $data;

        } catch (Exception $e) {

        }

    }


    public function getGruppiCorsi(){
        try {
            $db = JFactory::getDbo();
            $db->setQuery("SELECT  config_value FROM #__gg_configs WHERE config_key= 'id_gruppo_corsi'");
            $gruppo_corsi = $db->loadResult();

            $query = $this->_db->getQuery(true)
                ->select('*')
                ->from('#__usergroups AS ug')
                ->where('parent_id = '.$gruppo_corsi);

            $db->setQuery($query);
            $data = $db->loadObjectList();

            return $data;

        } catch (Exception $e) {

        }

    }

}
