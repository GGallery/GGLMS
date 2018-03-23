<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
require_once JPATH_COMPONENT . '/models/report.php';

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerSendMails extends JControllerLegacy
{
    private $_japp;
    public $_params;
    protected $_db;
    private $_filterparam;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();
        $this->_db = JFactory::getDbo();


    }

    public function sendMails(){

        try {
            $corsimail=$this->_params->get('alert_lista_corsi');
            $oggettomail=$this->_params->get('alert_mail_object');
            $testomail = $this->_params->get('alert_mail_text');
            $corsiarray=explode(',',$corsimail);
            foreach ($corsiarray as $id_corso) {

                $reportObj=new gglmsModelReport();
                $result=$reportObj->getUtentiInScadenzaCorso($id_corso);
                $i=0;
                if($result['rows']!=null) {


                    foreach ($result['rows'] as $row) {

                        //$to = $row->email;
                        $to = ['a.petruzzella71@gmail.com'];
                        $mailer = JFactory::getMailer();
                        $config = JFactory::getConfig();
                        $sender = array(
                            $config->get('mailfrom'),
                            $config->get('fromname')
                        );

                        $mailer->setSender($sender);
                        $mailer->addRecipient($to);
                        $mailer->setSubject($oggettomail . " " . $result['titolo']);
                        $mailer->setBody('Gentile ' . $row->cognome ." " . $testomail);
                        if ($i>5)
                            break;
                        $send = $mailer->Send();
                        DEBUGG::log('corso:'.$result['titolo'].' a:'.json_decode($row->fields)->email.' cognome:'.$row->cognome, 'INVIO MAIL', 0, 1,0);
                        $i++;
                    }

                }
                echo 'corso:'.$result['titolo'].' mail inviate: '.$i.'<BR>';
            }

        $this->_japp->close();
        } catch (exceptions $ex) {

            DEBUGG::log($ex->getMessage(), 'ERRORE DA SENDMAILS', 1, 1,0);

        }

    }
}
