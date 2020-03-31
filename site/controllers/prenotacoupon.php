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
class gglmsControllerPrenotaCoupon extends JControllerLegacy
{

    private $_user;
    private $_japp;
    public $_params;
    public $_db;
    public $info_piattaforma;
    public $info_corso;

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

    public function _getPrezziByCorso($id_corso)
    {

        $query = $this->_db->getQuery(true)
            ->select('p.*,r.*')
            ->from('#__gg_prezzi as p ')
            ->join('inner', '#__gg_prezzi_range as r on r.id_corso = p.id_corso')
            ->join('inner', '#__gg_unit as u on u.id = p.id_corso')
            ->where('p.id_corso = "' . $id_corso . '"')
            ->setLimit(1);

        $this->_db->setQuery($query);
        $prezzi = $this->_db->loadAssoc();


        return $prezzi;
    }


    public function _getInfoCorso($id_corso)
    {

        $query = $this->_db->getQuery(true)
            ->select('u.titolo,  u.descrizione as descrizione, u.prefisso_coupon as codice_corso')
            ->from('#__gg_unit as u')
            ->where('u.id = "' . $id_corso . '"')
            ->setLimit(1);

        $this->_db->setQuery($query);
        $data = $this->_db->loadAssoc();

        $res["titolo_corso"] = $data["titolo"];
        $res["codice_corso"] = $data["codice_corso"];
        $res["descrizione_corso"] = $data["descrizione"];

//        $this->info_corso = $res;


        return $res;
    }

    public function get_info_piattaforma($id_piattaforma)
    {


        try {

            $query = $this->_db->getQuery(true)
                ->select('ug.id as id , ug.title as name, ud.email_riferimento as email, ud.alias as alias, ud.telefono , ud.dominio, ud.info_pagamento')
                ->from('#__usergroups as ug')
                ->join('inner', '#__usergroups_details AS ud ON ug.id = ud.group_id')
                ->where('id=' . $id_piattaforma)
                ->setLimit(1);


            $this->_db->setQuery($query);
            $info_piattaforma = $this->_db->loadAssoc();


            return $info_piattaforma;

        } catch (Exception $e) {
            DEBUGG::error($e, 'get_info_piattaforma');
        }


    }

    public function prenotacoupon()
    {

        try {

            $data = JRequest::get($_POST);

            if ($this->send_book_email($data) === false) {
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            }

            $this->_japp->redirect(('index.php?option=com_gglms&view=prenota&id_corso=' . $data["id_corso"] . '&id_piattaforma=' . $data["id_piattaforma"]), $this->_japp->enqueueMessage('Richiesta inviata con successo!', 'Success'));
        } catch (Exception $e) {

            DEBUGG::error($e, 'prenotaCoupon');
        }
        $this->_japp->close();


    }

    public function send_book_email($data)
    {


        $user = new gglmsModelUsers();
        $tutor_piattaforma_id_list = $user->get_all_tutor_piattaforma($data["id_piattaforma"]);

        $to = array();

        foreach ($tutor_piattaforma_id_list as $tutor_id) {

            array_push($to, $this->get_user_info($tutor_id, 'email'));
        }


        $info_corso = $this->_getInfoCorso($data["id_corso"]);
        $info_piattaforma = $this->get_info_piattaforma($data["id_piattaforma"]);

        $template = JPATH_COMPONENT . '/models/template/book_coupons_mail.tpl';

        $sender = $info_piattaforma["email"];
        $cc = $data["email"];

        $mailer = JFactory::getMailer();
        $mailer->setSender($sender); //FROM: email riferimento piattaforma
        $mailer->addRecipient($to); //TO: tutor Piattaforma
        $mailer->addCc($cc); // in copia chi ha compilato il form
        $mailer->setSubject('Prenotazione Coupon corso ' . $info_corso["titolo_corso"] . " - " . $info_corso["codice_corso"]);

//        var_dump($info_piattaforma["email"]);
//        var_dump($to);
//        var_dump($data["email"]);
//        die();

        $smarty = new EasySmarty();
        $smarty->assign('company_name', $data["ragione_sociale"]);
        $smarty->assign('piva', $data["piva"]);
        $smarty->assign('email', $data["email"]);
        $smarty->assign('ateco', $data["ateco"]);
        $smarty->assign('associato', $data["associato"]);
        $smarty->assign('qty', $data["qty"]);
        $smarty->assign('titolo_corso', $info_corso["titolo_corso"]);
        $smarty->assign('codice_corso', $info_corso["codice_corso"]);
        $smarty->assign('_prezzo', $data["_prezzo"]);
        $smarty->assign('piattaforma_name', $info_piattaforma["name"]);
        $smarty->assign('piattaforma_alias', $info_piattaforma["alias"]);
        $smarty->assign('recipient_name', 'Tutor');
//
//
        $mailer->setBody($smarty->fetch_template($template, null, true, false, 0));
        $mailer->isHTML(true);

        if (!$mailer->Send()) {
//            throw new RuntimeException('Error sending mail', E_USER_ERROR);
            utilityHelper::logMail('book_coupons_mail', $sender, $to, 0);
        }

        //log mail sent
        utilityHelper::logMail('book_coupons_mail', $sender, $to, 1);
        return true;


    }

    public function get_user_info($user_id, $field)
    {
        $user = JFactory::getUser($user_id);
        $info = $user->get($field);

        return $info;
    }


}
