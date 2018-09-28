<?php

/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');
jimport('joomla.user.helper');
require_once JPATH_COMPONENT . '/models/report.php';

class gglmsControllerIscrizioni extends JControllerForm
{

        public function addUserToGroup()
        {
            $app = JFactory::getApplication();

            $jinput = $app->input;
            $user_id = $jinput->get('user_id');
            $group_id = $jinput->get('group_id');

            JUserHelper::addUserToGroup($user_id, $group_id);


            $app->redirect(JRoute::_('index.php?option=com_gglms&view=iscrizioni', false));

        }

        public function sendEmail()
        {
            $mailer = JFactory::getMailer();
            $config = JFactory::getConfig();
            $app = JFactory::getApplication();
            $jinput = $app->input;
            $mailer->setSender($config->get('mailfrom'));

            $recipient = array($jinput->get('email'));
            $mailer->addRecipient($recipient);

            $mailer->setSubject('Iscrizione al corso' . $config->get('sitename'));
            $mailer->isHTML(true);

            $body = 'Gentile utente, <br> 
                la tua iscrizione al corso è stata completata correttamente.  <br>'.
                'Ora puoi identificarti sulla piattaforma con le credenziali scelte al momento della registrazione ed accedere al corso' .$jinput->get('course_name');

            $mailer->setBody($body);

            if (!$mailer->Send())
                throw new RuntimeException('Error sending mail', E_USER_ERROR);
        }

}
