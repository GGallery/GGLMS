<?php

/**
 * WebTVContenuto Model
 *
 * @package    Joomla.Components
 * @subpackage WebTV
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * WebTVContenuto Model
 *
 * @package    Joomla.Components
 * @subpackage WebTV
 */

require_once JPATH_COMPONENT . '/models/config.php';

class gglmsModelHelpDesk extends JModelLegacy
{

    private $_japp;
    protected $_db;
    private $_userid;
    private $_user;
    public $_params;
    private $_config;


    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_dbg = JRequest::getBool('dbg', 0);
        $this->_japp = JFactory::getApplication();
        $this->_db = JFactory::getDbo();
        $this->_user = JFactory::getUser();
        $this->_userid = $this->_user->get('id');
        $this->_params = $this->_japp->getParams();
        $this->_config = new gglmsModelConfig();


    }

    public function __destruct()
    {

    }


    public function getPiattaformaHelpDeskInfo()
    {
        try {

            $query = $this->_db->getQuery(true)
                ->select('d.name, d.alias, d.telefono, d.email_riferimento as email_riferimento ,d.link_ecommerce, d.nomi_tutor, d.email_tutor as recipient_didattico')
                ->from('#__usergroups_details AS d')
                ->where("d.dominio= '" . DOMINIO . "'");

            $this->_db->setQuery($query);
            $data = $this->_db->loadObject();


            return $data;

        } catch (Exception $e) {
            DEBUGG::query($query);
            DEBUGG::log($e, 'getPiattaformaHelpDeskInfo', 1);

        }
    }

    public function getRequestRecipients()
    {

        try {


            $recipients = array("tecnica" => null, "didattica" => null);

            $mail_unificate = $this->_config->getConfigValue('mail_riferimento_specifica');
            if ($mail_unificate == 1) {

                $query = $this->_db->getQuery(true)
                    ->select('config_value')
                    ->from('#__gg_configs')
                    ->where("config_key IN ( 'mail_richiesta_tecnica' , 'mail_richiesta_didattica')");

                $this->_db->setQuery($query);
                $data = $this->_db->loadObjectList();

                $recipients["tecnica"] = $data[0]->config_value;
                $recipients["didattica"] = $data[1]->config_value;

            } else {

                $query = $this->_db->getQuery(true)
                    ->select('d.email_riferimento as recipient_tecnico , d.email_tutor as recipient_didattico')
                    ->from('#__usergroups_details AS d')
                    ->where("d.dominio= '" . DOMINIO . "'");

                $this->_db->setQuery($query);
                $data = $this->_db->loadObject();


                $recipients["tecnica"] = $data->recipient_tecnico;
                $recipients["didattica"] = $data->recipient_didattico;

            }

            return $recipients;
        } catch (Exception $e) {
            DEBUGG::query($query);
            DEBUGG::log($e, 'get Recipients', 1);

        }

    }

    public function sendRequestMail($data)
    {


        $Juser = JFactory::getUser();
        $user = new gglmsModelUsers();
        $user->get_user($Juser->id);
        $usergroups = $user->get_user_societa($Juser->id, true);

        require_once(JPATH_COMPONENT . '/libraries/smarty/EasySmarty.class.php');
        $recipient = $this->getRequestRecipients()[$data["request_type"]];

        $mailer = JFactory::getMailer();
        $mailer->setSender($data['email']);
        $recipient = array($recipient);//, 'martina@ggallery.it');
        $mailer->addRecipient($recipient);
        $mailer->setSubject('Richiesta di assistenza  ' . $data["request_type"] . ' su ' . $data['alias']);


        $template = JPATH_COMPONENT . '/models/template/help_desk_mail.tpl';


        $smarty = new EasySmarty();
        $smarty->assign('sender_name', $data['nominativo']);
        $smarty->assign('sender_email', $data['email']);
        $smarty->assign('alias', $data['alias']);
        $smarty->assign('request_type', $data["request_type"]);
        $smarty->assign('message', $data["question"]);
        $smarty->assign('id_utente', $this->_user->id);
        $smarty->assign('username', $this->_user->username);
        $smarty->assign('società', $usergroups[0]->titolo);


        $mailer->setBody($smarty->fetch_template($template, null, true, false, 0));
        $mailer->isHTML(true);

        if (!$mailer->Send())
            throw new RuntimeException('Error sending mail help_desk_mail ', E_USER_ERROR);


        return true;


    }

}
