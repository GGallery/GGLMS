<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/models/users.php';

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerLibretto extends JControllerLegacy
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




//        $this->params = $this->_app->getParams();



       JHtml::_('stylesheet', 'components/com_gglms/libraries/css/debugg.css');


    }

    public function get_libretto(){



        try {
            if (!$this->_filterparam->user_id) {
                $user = JFactory::getUser();
                $data = $this->get_data($user->id);
            } else {
                $data = $this->get_data($this->_filterparam->user_id);
            }

            return $data;
            $this->_app->close();
        }catch (exceptions $ex){
            DEBUGG::log('ERRORE DA get_libretto','gglmsControllerLibretto',1,1);

        }

    }
    private function get_data($user_id){

        $model=$this->getModel('libretto');
        return $model->get_data($user_id);

     }
    public function get_user(){

         try {
             if (!$this->_filterparam->user_id) {
                 $user = JFactory::getUser();
                 $user_id=$user->id;
             }else{

                 $user_id=$this->_filterparam->user_id;
             }
             $model=$this->getModel('libretto');
             $user= $model->get_user($user_id);

             return $user;

         }catch (exceptions $ex){
             DEBUGG::log('ERRORE DA get_libretto','gglmsControllerLibretto',1,1);

         }
     }

}
