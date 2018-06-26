<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/models/reporttempilearning.php';

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerReportTempiLearning extends JControllerLegacy
{
    protected $_db;
    private $_app;
    private $params;
    private $_filterparam;
    private $user;
    private $user_id;

    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->_app = JFactory::getApplication();
        $this->_db = JFactory::getDbo();
        $this->_filterparam = new stdClass();
        $this->_filterparam->user_id = JRequest::getVar('user_id');
       JHtml::_('stylesheet', 'components/com_gglms/libraries/css/debugg.css');
    }

    public function get_user_name(){

        try {
            if (!$this->_filterparam->user_id) {
                $user = JFactory::getUser();
                $user_id=$user->id;
            }else{

                $user_id=$this->_filterparam->user_id;
            }
            $model=$this->getModel('reporttempilearning');
            $user= $model->get_user_name($user_id);
            return $user;

        }catch (exceptions $ex){
            DEBUGG::log('ERRORE DA get_user_name','gglmsControllerTempiLearning',1,1);

        }
    }
    public function get_tempi(){


        try {

            if (!$this->_filterparam->user_id) {
                $user = JFactory::getUser();
                $user_id=$user->id;
            }else{

                $user_id=$this->_filterparam->user_id;
            }
            $model=$this->getModel('reporttempilearning');
            $tempi= $model->get_tempi($user_id);
            if(count($tempi)>0){

                foreach ($tempi as &$row){

                    switch ($row->mese){

                        case 1:
                            $row->mese=' gennaio ';
                            break;
                        case 2:
                            $row->mese=' febbraio ';
                            break;
                        case 3:
                            $row->mese=' marzo ';
                            break;
                        case 4:
                            $row->mese=' aprile ';
                            break;
                        case 5:
                            $row->mese=' maggio ';
                            break;
                        case 6:
                            $row->mese=' giugno ';
                            break;
                        case 7:
                            $row->mese=' luglio ';
                            break;
                        case 8:
                            $row->mese=' agosto ';
                            break;
                        case 9:
                            $row->mese=' settembre ';
                            break;
                        case 10:
                            $row->mese=' ottobre ';
                            break;
                        case 11:
                            $row->mese=' novembre ';
                            break;
                        case 12:
                            $row->mese=' dicembre ';
                            break;
                    }
                    $row->totale=$this->convertiDurata($row->totale);
                }
            }
            return $tempi;


        }catch (exceptions $ex){
            DEBUGG::log('ERRORE DA get_tempi','gglmsControllerTempiLearning',1,1);

        }


    }

    private function convertiDurata($durata)
    {
        $h = floor($durata/3600);
        $m = floor(($durata % 3600) / 60);
        $s = ($durata % 3600) % 60;
        $result = sprintf('h:%02d m:%02d s:%02d', $h,$m, $s);
        return $result;
    }

}
