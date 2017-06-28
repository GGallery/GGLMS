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

            $config = JFactory::getConfig();
            $user = JFactory::getUser();
            $user_id = $user->get('id');
            $db= JFactory::getDBO();

            $return[] = array();

            $group_id=1; //a caso standard

            // creo i coupon
            $coupons = array();
            $values = array();
            for ($i = 0; $i < $_REQUEST['quantita']; $i++) {
                $prefisso = $_REQUEST['prefisso'];
                $coupons[$i] = $this->_generate_coupon($prefisso);
                $values[] = sprintf("('%s', '%s', %d, '%s', %d, %d, %d, %d, %d, %s)", $coupons[$i], $_REQUEST['course_id'], $group_id, $_REQUEST['transition_id'], $_REQUEST['attestato'], $user_id, "1", 1, $_REQUEST['durata'], 'now()');
            }
            // li inserisco nel DB
            $query = 'INSERT INTO #__gg_coupon (coupon, corsi_abilitati, gruppo, id_iscrizione, attestato, id_societa, abilitato, trial, durata, data_abilitazione) VALUES ' . join(',', $values);

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
            $body.= 'QUANTITA: '.count($coupons).'</br></br>';

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




}
