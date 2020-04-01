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
class gglmsControllerCoupondispenser extends JControllerLegacy
{

    public $_params;
    public $_user;


    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();
        $this->_user = JFactory::getUser();

    }

    public function check()
    {
        $japp = JFactory::getApplication();
        $model = $this->getModel('coupondispenser');

        $email = JRequest::getVar('email');
        $id_iscrizione = JRequest::getVar('id_iscrizione');
        $id_dispenser = JRequest::getVar('id_dispenser');

        if ($model->checkAlreadyTaken($email, $id_dispenser)) {
            $results['report'] = "<p class='alert-danger alert'>Hai già riscosso un coupon</p>";
            $results['valido'] = 0;
            echo json_encode($results);
            $japp->close();
        }

        $coupon = $model->getCoupon($id_iscrizione);

        $model->setDispenserLog($email, $id_dispenser, $coupon);

        if($model->sendMail($email, $coupon)){
            $results['valido'] = 1;
            $results['report'] = "<p class='alert-success alert'> Coupon inviato alla tua mail. Controlla e inserisci il coupon su questa piattaforma</p>";
        };

        echo json_encode($results);
        $japp->close();
    }


    public function check_coupon_rinnovo()
    {

        $japp = JFactory::getApplication();

        $coupon = JRequest::getVar('coupon');
        $model = $this->getModel('coupon');

        $dettagli_coupon = $model->check_Coupon($coupon, true);

        $user = new gglmsModelUsers();
        if (!$user->is_tutor_piattaforma($this->_user->id)) {

            //CHECK USER
            // utente loggato che sta richiedendo un rinnovo non è tutori di piattaforma, non dovrebbe accedere a rinnova coupon
            $results['report'] = "<p class='alert-danger alert'>" . $this->_params->get('messaggio_rinnovo_notutor') . "</p>";
            $results['valido'] = 0;

        }

        if (empty($dettagli_coupon)) {
            // check esistaenza coupon
            $results['report'] = "<p class='alert-danger alert'> " . $this->_params->get('messaggio_inserimento_wrong') . "</p>";
            $results['valido'] = 0;
        } else {
            if (!$dettagli_coupon['abilitato']) {
                // se non è abilitato non te lo faccio rinnovare
                $results['report'] = "<p class='alert-danger alert'>" . $this->_params->get('messaggio_inserimento_pending') . "</p>";
                $results['valido'] = 0;
            } else {

                // check, deve essere associato ad un utente
                if (!$dettagli_coupon['id_utente'] || !$dettagli_coupon['data_utilizzo']) {

                    $results['report'] = "<p class='alert-danger alert'>" . $this->_params->get('messaggio_rinnovo_nouser') . "</p>";
                    $results['valido'] = 0;

                } else if (!$model->check_id_societa_match_user($dettagli_coupon['id_societa'], $this->_user->id)) {

                    // coupon id_societetà deve essere figlia di una delle piattaforme a cui appartiene l'utente
                    $results['report'] = "<p class='alert-danger alert'>" . $this->_params->get('messaggio_rinnovo_wrong_società') . "</p>";
                    $results['valido'] = 0;

                } else if (!$model->is_expired($coupon)) {

                    // check il coupon deve essere scaduto
                    $results['report'] = "<p class='alert-danger alert'>" . $this->_params->get('messaggio_rinnovo_not_expired') . "</p>";
                    $results['valido'] = 0;

                } else {


                    // tutti i controlli superati, rinnova coupon
                    if ($model->rinnova_coupon($coupon)) {
                        $results['valido'] = 1;
                        $results['report'] = "<p class='alert-success alert'>" . $this->_params->get('messaggio_rinnovo_success') . "</p>";
                    }

                }
            }


        }

        echo json_encode($results);
        $japp->close();
    }


}
