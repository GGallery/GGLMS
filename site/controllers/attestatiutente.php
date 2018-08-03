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
class gglmsControllerAttestatiUtente extends JControllerLegacy
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

    public function get_user_name(){

        try {
            if (!$this->_filterparam->user_id) {
                $user = JFactory::getUser();
                $user_id=$user->id;
            }else{

                $user_id=$this->_filterparam->user_id;
            }
            $model=$this->getModel('attestatiutente');
            $user= $model->get_user_name($user_id);
            return $user;

        }catch (exceptions $ex){
            DEBUGG::log('ERRORE DA get_libretto','gglmsControllerLibretto',1,1);

        }
    }
    public function get_attestati(){

        $attestati_to_return=[];
        try {
            if (!$this->_filterparam->user_id) {
                $user = JFactory::getUser();
                $user_id=$user->id;
            }else{

                $user_id=$this->_filterparam->user_id;
            }
            $attestati=array_diff(scandir($_SERVER['DOCUMENT_ROOT'].'/mediagg/attestati/'),array('..', '.'));

            foreach ($attestati as $attestato){
                $id_utente=explode("_",$attestato)[0];
                if($id_utente==$user_id) {
                    $attestato_to_return=substr($attestato, strlen($id_utente) + 1);
                    $attestato_to_return_label=str_replace('_',' ',$attestato_to_return);
                    $attestato_to_return="<a href=../mediagg/attestati/".$attestato.">".$attestato_to_return_label."</a>";
                    array_push($attestati_to_return, $attestato_to_return);
                }
            }
            return $attestati_to_return;

        }catch (exceptions $ex){
            DEBUGG::log('ERRORE DA get_libretto','gglmsControllerLibretto',1,1);

        }


    }

    public function get_attestati_esma(){

        $attestati_to_return=[];
        try {
            if (!$this->_filterparam->user_id) {
                $user = JFactory::getUser();
                $user_id=$user->id;
            }else{

                $user_id=$this->_filterparam->user_id;
            }
            $attestati=array_diff(scandir($_SERVER['DOCUMENT_ROOT'].'/mediagg/attestati/'),array('..', '.'));

            foreach ($attestati as $attestato){
                $id_utente=explode("_",$attestato)[0];
                if($id_utente==$user_id) {
                    $attestato_to_return=substr($attestato, strlen($id_utente) + 1);
                    $attestato_to_return_label=str_replace('_',' ',$attestato_to_return);
                    $attestato_to_return="<a href=../mediagg/attestati/".$attestato.">".$attestato_to_return_label."</a>";
                    array_push($attestati_to_return, $attestato_to_return);
                }
            }
            return $attestati_to_return;

        }catch (exceptions $ex){
            DEBUGG::log('ERRORE DA get_libretto','gglmsControllerLibretto',1,1);

        }


    }

}
