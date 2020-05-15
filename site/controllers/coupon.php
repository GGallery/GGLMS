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
class gglmsControllerCoupon extends JControllerLegacy
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

    public function check_coupon()
    {


        $japp = JFactory::getApplication();

        $coupon = JRequest::getVar('coupon');
        $model = $this->getModel('coupon');
        $dettagli_coupon = $model->check_Coupon($coupon);


        if (empty($dettagli_coupon)) {
            $results['report'] = "<p class='alert-danger alert'> " . JText::_('COM_GGLMS_COUPON_INSERT_WRONG') . "</p>";
            $results['valido'] = 0;
        } else {
            if (!$dettagli_coupon['abilitato']) {
                $results['report'] = "<p class='alert-danger alert'>" . JText::_('COM_GGLMS_COUPON_INSERT_PENDING')  . "</p>";
                $results['valido'] = 0;
            } else {

                if ($model->is_logged_user_tutor()) {

                    $results['report'] = "<p class='alert-danger alert'>" . JText::_('COM_GGLMS_COUPON_INSERT_TUTOR') . "</p>";
                    $results['valido'] = 0;

                } else {


                    if ($model->check_already_enrolled($dettagli_coupon)) {
                        // controllo che non esista già un coupon per lo stesso gruppo per lo stesso utente

                        $results['report'] = "<p class='alert-danger alert'>" . JText::_('COM_GGLMS_COUPON_INSERT_DUPLICATED') . "</p>";
                        $results['valido'] = 0;
                    } else {
                        $model->assegnaCoupon($coupon);

                        if ($dettagli_coupon['id_gruppi'])
                            $model->setUsergroupUserGroup($dettagli_coupon['id_gruppi']);
                        if ($dettagli_coupon['id_societa'])
                            $model->setUsergroupUserGroup($dettagli_coupon['id_societa']);

                        $results['valido'] = 1;
                        $results['report'] = "<p class='alert-success alert'> " . JText::_('COM_GGLMS_COUPON_INSERT_COUPON_VALID') ."</p>";

                        if ($dettagli_coupon['corsi_abilitati'])
                            $results['report'] .= $model->get_listaCorsiFast($dettagli_coupon['corsi_abilitati']);
                        else
                            $results['report'] =  JText::_('COM_GGLMS_COUPON_INSERT_SUCCESS') ;
                    }
                }

            }

            // manage TRIAL, se il coupon è trial sblocco tutti i contenuti

            if ($dettagli_coupon["trial"] == 1) {

                $unita_model = new gglmsModelUnita();
                $unita_model->set_corso_completed($dettagli_coupon["id_gruppi"]);

            }

        }

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
            $results['report'] = "<p class='alert-danger alert'>" . JText::_('COM_GGLMS_COUPON_RENEW_COUPON_NOTUTOR') . "</p>";
            $results['valido'] = 0;

        }

        if (empty($dettagli_coupon)) {
            // check esistaenza coupon
            $results['report'] = "<p class='alert-danger alert'> " . JText::_('COM_GGLMS_COUPON_INSERT_WRONG')  . "</p>";
            $results['valido'] = 0;
        } else {
            if (!$dettagli_coupon['abilitato']) {
                // se non è abilitato non te lo faccio rinnovare
                $results['report'] = "<p class='alert-danger alert'>" .  JText::_('COM_GGLMS_COUPON_INSERT_PENDING')  . "</p>";
                $results['valido'] = 0;
            } else {

                // check, deve essere associato ad un utente
                if (!$dettagli_coupon['id_utente'] || !$dettagli_coupon['data_utilizzo']) {

                    $results['report'] = "<p class='alert-danger alert'>" . JText::_('COM_GGLMS_COUPON_RENEW_COUPON_NOUSER')   . "</p>";
                    $results['valido'] = 0;

                } else if (!$model->check_id_societa_match_user($dettagli_coupon['id_societa'], $this->_user->id)) {

                    // coupon id_societetà deve essere figlia di una delle piattaforme a cui appartiene l'utente
                    $results['report'] = "<p class='alert-danger alert'>" . JText::_('COM_GGLMS_COUPON_RENEW_COUPON_WRONG_SOC'). "</p>";
                    $results['valido'] = 0;

                } else if (!$model->is_expired($coupon)) {

                    // check il coupon deve essere scaduto
                    $results['report'] = "<p class='alert-danger alert'>" . JText::_('COM_GGLMS_COUPON_RENEW_COUPON_WRONG_NOTEXPIRED'). "</p>";
                    $results['valido'] = 0;

                } else {


                    // tutti i controlli superati, rinnova coupon
                    if ($model->rinnova_coupon($coupon)) {
                        $results['valido'] = 1;
                        $results['report'] = "<p class='alert-success alert'>" .  JText::_('COM_GGLMS_COUPON_RENEW_COUPON_SUCCESS') . "</p>";
                    }

                }
            }


        }

        echo json_encode($results);
        $japp->close();
    }


}
