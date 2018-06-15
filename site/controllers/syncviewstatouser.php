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
require_once JPATH_COMPONENT . '/models/unita.php';
require_once JPATH_COMPONENT . '/models/syncviewstatouser.php';


/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerSyncViewStatoUser extends JControllerLegacy
{
    protected $_db;
    private $_app;
    private $params;
    private $_filterparam;
    private $syncviewstatouserModel;

    //private $unitas=array();
    //private $contenuti=array();

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_app = JFactory::getApplication();
        $this->params = $this->_app->getParams();
        $this->_db = JFactory::getDbo();
        $this->_filterparam = new stdClass();
        $this->_filterparam->typeofcall=JRequest::getVar('typeofcall');
        $this->_filterparam->limit=JRequest::getVar('limit');
        $this->_filterparam->offset=JRequest::getVar('offset');
        $this->_filterparam->maxts=JRequest::getVar('maxts');
        $this->syncviewstatouserModel=new gglmsModelSyncViewStatoUser();

       JHtml::_('stylesheet', 'components/com_gglms/libraries/css/debugg.css');


    }

    public  function GetMaxTimeStampforSession(){

        echo $this->syncviewstatouserModel->MaxTimeStampforSession();
        $this->_app->close();
    }


    public function syncViewStatoUser()
    {
        $typeofcall=$this->_filterparam->typeofcall;
        $offset=$this->_filterparam->offset;
        $limit=$this->_filterparam->limit;
        $maxts=$this->_filterparam->maxts;
        $maxts=substr($maxts,0,4)."-".substr($maxts,4,2)."-".substr($maxts,6,2)." ".substr($maxts,8,2).":".substr($maxts,10,4).":".substr($maxts,12,2);
        $result= $this->syncviewstatouserModel->syncViewStatoUser($offset,$limit,$maxts,$typeofcall);
        DEBUGG::log('FINE PROCEDURA ', ' ',0,1,0);
        switch ($result){

            case true:
                echo '1';
            break;
            case false:
                echo "0";
        }

        $this->_app->close();
    }

}
