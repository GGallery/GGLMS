<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
//require_once JPATH_COMPONENT . '/models/report.php';

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
            $alert_days_before=$this->_params->get('alert_days_before');
            $oggettomail=$this->_params->get('alert_mail_object');
            $testomail = $this->_params->get('alert_mail_text');
            $corsiarray=explode(',',$corsimail);
            foreach ($corsiarray as $id_corso) {

                $query = $this->_db->getQuery(true);
                $query->select('u.email , anagrafica.cognome as cognome, (select u.titolo from #__gg_unit as u where id='.$id_corso.') as titolo')
                    ->from('#__gg_view_stato_user_corso as uc')
                    ->join('inner', '#__gg_report_users as anagrafica on uc.id_anagrafica=anagrafica.id')
                    ->join('inner', '#__users as u on anagrafica.id_user=u.id')
                    ->where('id_corso=' . $id_corso . ' and IF(date(now())>DATE_ADD((select data_fine from #__gg_unit where id=' . $id_corso . '), INTERVAL -'.$alert_days_before.' DAY), IF(stato=0,1,0),0)=1')
                   ;

                $this->_db->setQuery($query);
                $rows = $this->_db->loadAssocList();


                if($rows!=null) {

                    foreach ($rows as $row) {

                        //$to = $row['email'];
                        $to = ['a.petruzzella71@gmail.com'];

                        $mailer = JFactory::getMailer();
                        $config = JFactory::getConfig();
                        $sender = array(
                            $config->get('mailfrom'),
                            $config->get('fromname')
                        );

                        $mailer->setSender($sender);

                        $mailer->addRecipient($to);
                        $mailer->setSubject($oggettomail . " " . $row['titolo']);
                        $mailer->setBody('Gentile ' . $row['cognome'] ." " . $testomail);

                        $send = $mailer->Send();  //ATTENZIONE IL VERO INVIO E' DISABILITATO IN PROVA

                        echo $send;


                    }
                }
            }
        $this->_japp->close();
        } catch (exceptions $ex) {

            DEBUGG::log($ex->getMessage(), 'ERRORE DA SENDMAILS', 1, 1,0);

        }

    }
}
