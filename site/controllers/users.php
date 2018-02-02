<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerUsers extends JControllerLegacy
{

    public function login()
    {
        $app = JFactory::getApplication();
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('`id`, `username`, `password`');
        $query->from('`#__users`');
        $query->where('username=' . $db->Quote( $_REQUEST['username'])) ;
        $query->where('password=' . $db->Quote( $_REQUEST['password'])) ;

        $db->setQuery( $query );
        $result = $db->loadObject();

        if($result) {
            JPluginHelper::importPlugin('user');

            $options = array();
            $options['action'] = 'core.login.site';

            $response['username'] = $result->username;
            $logged= $app->triggerEvent('onUserLogin', array((array)$response, $options));
            if($logged)
                $app->enqueueMessage("Accesso effettuato correttamente come utente ". $_REQUEST['username'], 'success');
            else {
                $app->enqueueMessage("Problemi nell'effettuare l'accesso", 'danger');
            }
        }
        else
        {
            $app->enqueueMessage("Credenziali errate", 'danger');
        }

        $app->redirect(JRoute::_('index.php'));
    }

    public function reset(){
        $app = JFactory::getApplication();
        $appinput= $app->input;
        $config = JFactory::getConfig();
        $db= JFactory::getDbo();

        $user_id =  $appinput->get('id', 0, 'integer');
        $username =  $appinput->get('username', '', 'USERNAME');

        $parole=array('rosso','giallo','ambra','albicocca','amaranto','azzurro','bianco','bronzo','rosa','verde', 'turchese','magenta');
        $newpassword= $parole[rand(0, sizeof($parole))] . rand(10,99);

        $query = $db->getQuery(true);
        $query->update("#__users");
        $query->set("password='".JUserHelper::hashPassword($newpassword)."'");
        $query->where("id=".$user_id);

        $db->setQuery((string) $query);
        $db->execute();

        $ret['username']= $username;
        $ret['password']=$newpassword;

        echo "<h2>Nuove credenziali portale ".$config['sitename']."</h2>";
        echo "<b>Username</b>: ".$ret['username']."<br>";
        echo "<b>Password</b>: ".$ret['password'];

        $app->close();

    }

    public function resetsend(){
        $app = JFactory::getApplication();
        $appinput= $app->input;
        $config = JFactory::getConfig();
        $db= JFactory::getDbo();

        $user_id =  $appinput->get('id', 0, 'integer');
        $username =  $appinput->get('username', '', 'USERNAME');

        $parole=array('rosso','giallo','ambra','albicocca','amaranto','azzurro','bianco','bronzo','rosa','verde', 'turchese','magenta');
        $newpassword= $parole[rand(0, sizeof($parole)-1)] . rand(10,99);

        $query = $db->getQuery(true);
        $query->update("#__users");
        $query->set("password='".JUserHelper::hashPassword($newpassword)."'");
        $query->where("id=".$user_id);

        $db->setQuery((string) $query);
        $db->execute();

        $ret['username']= $username;
        $ret['password']=$newpassword;


        //mail di conferma
        $destinatari = array( $_REQUEST['email']);
        $oggetto ="Nuove credenziali portale ".$config['sitename'];
        $body   = 'Le tue credenziali sono: <br><br>'
            .'Username: <b>'.$username .'</b> <br> '
            .'Password: <b>'.$newpassword .'</b> <br><br> '

            . '<div>Lo staff di '.$config['sitename'].' </div> <br><br>';

        echo $this->sendMail($destinatari, $oggetto ,$body);

        echo "<h2>Nuove credenziali portale ".$config['sitename']."</h2>";
        echo "<b>Username</b>: ".$ret['username']."<br>";
        echo "<b>Password</b>: ".$ret['password'];

        $app->close();

    }

    public function sendMail($destinatari, $oggetto, $body ){

        $mailer = JFactory::getMailer();

        $config = JFactory::getConfig();
        $sender = array(
            $config->get( 'mailfrom' ),
            $config->get( 'fromname' )
        );
        $mailer->setSender($sender);


        $mailer->addRecipient($destinatari);
        $mailer->setSubject($oggetto);
        $mailer->isHtml(true);
        $mailer->Encoding = 'base64';
        $mailer->setBody($body);
        //optional
        $mailer->AddEmbeddedImage( JPATH_COMPONENT.'/images/logo.jpg', 'logo_id', 'logo.jpg', 'base64', 'image/jpeg' );


        $send = $mailer->Send();
        if ( $send !== true )
            return 'Errore invio mail: ';
        else
            return 'Mail inviata';

    }

    public function sso()
    {
        $app = JFactory::getApplication();
        $busta = $_REQUEST['busta'];

        echo "-----------------------------<br>";

        if ($busta) {
            echo "Busta : ". $busta . "<br>";
            echo "Busta BASE64DECODED: " . base64_decode($busta) . "<br>";
        }
        else{
            echo "Busta non fornita<br>";
        }

        echo "-----------------------------<br>";

        $app->close();
    }

}
