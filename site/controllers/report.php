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
//require_once JPATH_COMPONENT . '/models/unita.php';
//require_once JPATH_COMPONENT . '/models/users.php';
require_once JPATH_COMPONENT . '/models/syncdatareport.php';
require_once JPATH_COMPONENT . '/models/report.php';

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerReport extends JControllerLegacy
{
    protected $_db;
    private $_app;
    private $params;
    private $_filterparam;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_app = JFactory::getApplication();
        $this->params = $this->_app->getParams();
        $this->_db = JFactory::getDbo();
        $this->_filterparam = new stdClass();
        $this->_filterparam->limit=JRequest::getVar('limit');
        $this->_filterparam->offset=JRequest::getVar('offset');

       JHtml::_('stylesheet', 'components/com_gglms/libraries/css/debugg.css');


    }

    //INGRESSO




    public function sync_report_users(){


        try {
            $syncdatareport = new gglmsModelSyncdatareport();
            $result=$syncdatareport->sync_report_users();
            if($result){
                echo json_encode('true');
            }else{
                echo json_encode('false');
            }
            $this->_app->close();
        }catch (exceptions $ex){

            DEBUGG::log($ex->getMessage(),'ERRORE DA REPORT.SYNC',1,1);

        }
    }

    public function sync_report_count(){


        try {
            $syncdatareport = new gglmsModelSyncdatareport();
            $result=$syncdatareport->sync_report_count();
            echo json_encode($result);

            $this->_app->close();
        }catch (exceptions $ex){

            DEBUGG::log($ex->getMessage(),'ERRORE DA REPORT.SYNC',1,1);

        }
    }

    public function sync_report(){

        $limit=$this->_filterparam->limit;
        $offset=$this->_filterparam->offset;
        try {
            $syncdatareport = new gglmsModelSyncdatareport();
            $result = $syncdatareport->sync_report($limit,$offset);
            //echo $result;
            if ($result==true) {
                echo json_encode('true');
            } else {
                echo json_encode('false');
            }
            $this->_app->close();
        }catch (exceptions $ex){

            DEBUGG::log($ex->getMessage(),'ERRORE DA REPORT.SYNC',1,1);

        }

    }

    public function updateconfig(){

        try {
            $syncdatareport = new gglmsModelSyncdatareport();
            $result = $syncdatareport->updateconfig();
            if ($result) {
                echo json_encode('true');
            } else {
                echo json_encode('false');
            }
            $this->_app->close();
        }catch (exceptions $ex){

            DEBUGG::log($ex->getMessage(),'ERRORE DA REPORT.SYNC',1,1);

        }

    }

    public function sync_report_complete(){

        try {
            $syncdatareport = new gglmsModelSyncdatareport();
            $result = $syncdatareport->sync_report_complete();
            if ($result) {
                echo json_encode('true');
            } else {
                echo json_encode('false');
            }
            $this->_app->close();
        }catch (exceptions $ex){

            DEBUGG::log($ex->getMessage(),'ERRORE DA REPORT.SYNC',1,1);

        }

    }
    public function checkSeconds(){

        echo json_encode('true');
        $this->_app->close();

    try {
        $ora = date('Y-m-d h:i:s', time());
        $lastsync = $this->params->get('data_sync');
        $lastsync = strtotime($lastsync);
        $ora = strtotime($ora);
        $secondi_ultima_syncro = $ora - $lastsync;
        $data_sync_seconds_limit=$this->params->get('data_sync_seconds_limit');
        if ($secondi_ultima_syncro >$data_sync_seconds_limit ) {

            echo json_encode('true');
        } else {

            echo json_encode('false');
        }
        $this->_app->close();
    }catch (Exception $ex){

        DEBUGG::log($ex->getMessage(),'ERRORE DA REPORT.CHECKSECONDS',1,1);
        }

    }

    public function insertUserLog(){

        try {

            $id_utente = $this->_filterparam->id_utente = JRequest::getVar('id_utente');
            $id_contenuto = $this->_filterparam->id_contenuto = JRequest::getVar('id_contenuto');
            $supporto = $this->_filterparam->supporto = JRequest::getVar('supporto');
            $uniqid = $this->_filterparam->uniqid = JRequest::getVar('uniqid');
            $ipaddress = JFactory::getApplication()->input->server->get('REMOTE_ADDR', '', '');
            $report = new gglmsModelReport();
            if ($report->insertUserLog($id_utente, $id_contenuto, $supporto, $ipaddress, $uniqid) == true) {

                echo json_encode('true');
            } else {

                echo json_encode('false');
            }
            $this->_app->close();

        }catch (exceptions $ex){
            DEBUGG::log($ex->getMessage(),'ERRORE DA REPORT.CHECKSECONDS',1,1);
        }
    }

    public function updateUserLog(){

        try {

            $uniquid = $this->_filterparam->uniqid = JRequest::getVar('uniqid');
            $report = new gglmsModelReport();
            if ($report->updateUserLog($uniquid) == true) {

                echo json_encode('true');
            } else {
                echo json_encode('false');
            }
            $this->_app->close();
        }catch (exceptions $ex){
            DEBUGG::log($ex->getMessage(),'ERRORE DA REPORT.CHECKSECONDS',1,1);
        }
    }


}
