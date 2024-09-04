<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


require_once JPATH_COMPONENT . '/models/helpdesk.php';

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerHelpDesk extends JControllerLegacy
{

    private $_user;
    private $_japp;
    public $_params;
    public $_db;
    public $model;
    public $info_piattaforma;
//    public $request_recipients;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();

        $this->_user = JFactory::getUser();
        $this->_db = JFactory::getDbo();


        $this->model = new gglmsModelHelpDesk();
        $this->info_piattaforma = $this->model->getPiattaformaHelpDeskInfo();



    }

    public function sendMailRequest()
    {

        try {
            $input = $this->input;
            $data = $input->get($_POST);

            if (  $this->model->sendRequestMail($data) === false) {
                throw new RuntimeException("Errore".__FUNCTION__, E_USER_ERROR);
            }

            //$this->_japp->redirect(JRoute::_('/home/helpdesk'), $this->_japp->enqueueMessage(JText::_('COM_GGLMS_HELP_DESK_SUCCESS'), 'Success'));
            $this->_japp->redirect($data['current_url'], $this->_japp->enqueueMessage(JText::_('COM_GGLMS_HELP_DESK_SUCCESS'), 'Success'));

        } catch (Exception $e) {

            DEBUGG::log($e, 'Exception in sendMailRequest ', 1);
        }
        $this->_japp->close();
    }




}
